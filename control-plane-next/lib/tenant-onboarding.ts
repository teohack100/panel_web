import { createHash } from "node:crypto";
import { Prisma, TenantProductStatus, TenantRole, TenantStatus } from "@prisma/client";

import { prisma } from "@/lib/db";

export type CreateTenantOnboardingInput = {
  slug: string;
  name: string;
  ownerId?: string | null;
  ownerEmail?: string | null;
  planId?: string | null;
  productIds?: string[];
  status?: TenantStatus;
  initialCreditBalance?: number | string | null;
  primaryDomain?: string | null;
  nodeKey?: string | null;
  phpVersion?: string | null;
  autoProvision?: boolean;
  failOnDomainExists?: boolean;
  createdByUserId?: string | null;
};

export type CreateTenantOnboardingOutput = {
  tenantId: string;
  tenantSlug: string;
  domainId: string | null;
  cloudTaskId: string | null;
  notes: string[];
};

export function normalizeSlug(raw: string) {
  return raw
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9-]/g, "-")
    .replace(/-+/g, "-")
    .replace(/^-|-$/g, "");
}

export function normalizeHostname(raw: string) {
  return raw
    .toLowerCase()
    .trim()
    .replace(/^https?:\/\//, "")
    .replace(/\/$/, "");
}

export function isValidHostname(raw: string) {
  const host = normalizeHostname(raw);
  if (!host) return false;
  if (host.length > 253) return false;
  if (!host.includes(".")) return false;
  const labels = host.split(".");
  if (labels.some((label) => label.length < 1 || label.length > 63)) return false;
  if (labels.some((label) => !/^[a-z0-9-]+$/.test(label))) return false;
  if (labels.some((label) => label.startsWith("-") || label.endsWith("-"))) return false;
  return true;
}

function normalizeEmail(raw?: string | null) {
  return (raw ?? "").trim().toLowerCase();
}

function buildSyntheticOwnerId(email: string) {
  const digest = createHash("sha256").update(email).digest("hex").slice(0, 20);
  return `email_${digest}`;
}

function parseNonNegativeNumber(raw: number | string | null | undefined) {
  if (raw === null || raw === undefined || raw === "") return 0;
  const value = Number(raw);
  if (!Number.isFinite(value) || value < 0) return 0;
  return value;
}

export async function createTenantWithProvisioning(input: CreateTenantOnboardingInput): Promise<CreateTenantOnboardingOutput> {
  const slug = normalizeSlug(input.slug);
  if (!slug) {
    throw new Error("invalid_slug");
  }

  const requestedOwnerId = input.ownerId?.trim() || "";
  const requestedOwnerEmail = normalizeEmail(input.ownerEmail);
  if (!requestedOwnerId && !requestedOwnerEmail) {
    throw new Error("missing_owner_identity");
  }

  const planId = input.planId?.trim() || null;
  const primaryDomain = input.primaryDomain ? normalizeHostname(input.primaryDomain) : "";
  const nodeKey = input.nodeKey?.trim().toLowerCase() || "";
  const productIds = Array.from(new Set((input.productIds ?? []).filter(Boolean)));
  const initialCreditBalance = parseNonNegativeNumber(input.initialCreditBalance);
  const notes: string[] = [];

  return prisma.$transaction(async (tx) => {
    const userByEmail = requestedOwnerEmail
      ? await tx.user.findUnique({ where: { email: requestedOwnerEmail } })
      : null;
    const userById = requestedOwnerId
      ? await tx.user.findUnique({ where: { id: requestedOwnerId } })
      : null;

    let ownerId = requestedOwnerId;
    let ownerEmail = requestedOwnerEmail;

    if (userByEmail) {
      ownerId = userByEmail.id;
      ownerEmail = userByEmail.email;
      if (requestedOwnerId && requestedOwnerId !== userByEmail.id) {
        notes.push(`owner_id_replaced_by_email:${requestedOwnerId}->${userByEmail.id}`);
      }
    } else {
      if (!ownerId && ownerEmail) {
        ownerId = buildSyntheticOwnerId(ownerEmail);
      }
      if (!ownerEmail && userById) {
        ownerEmail = userById.email;
      }
      if (!ownerEmail && ownerId) {
        ownerEmail = `${ownerId}@control.local`;
      }
    }

    if (!ownerId) {
      throw new Error("missing_owner_id_resolved");
    }
    if (!ownerEmail) {
      throw new Error("missing_owner_email_resolved");
    }

    await tx.user.upsert({
      where: { id: ownerId },
      create: {
        id: ownerId,
        email: ownerEmail,
        fullName: ownerId,
      },
      update: {
        email: ownerEmail,
      },
    });

    const tenant = await tx.tenant.create({
      data: {
        slug,
        name: input.name.trim(),
        ownerId,
        planId,
        status: input.status ?? TenantStatus.TRIAL,
        creditBalance: initialCreditBalance,
        memberships: {
          create: {
            userId: ownerId,
            role: TenantRole.OWNER,
          },
        },
      },
    });

    if (productIds.length > 0) {
      await tx.tenantProduct.createMany({
        data: productIds.map((productId) => ({
          tenantId: tenant.id,
          productId,
          status: input.autoProvision ? TenantProductStatus.PROVISIONING : TenantProductStatus.ACTIVE,
        })),
        skipDuplicates: true,
      });
    }

    let domainId: string | null = null;

    if (primaryDomain) {
      const existingDomain = await tx.tenantDomain.findUnique({ where: { hostname: primaryDomain } });
      if (existingDomain) {
        if (input.failOnDomainExists) {
          throw new Error("domain_already_exists");
        }
        notes.push(`domain_already_exists:${primaryDomain}`);
      } else {
        await tx.tenantDomain.updateMany({
          where: { tenantId: tenant.id, isPrimary: true },
          data: { isPrimary: false },
        });

        const domain = await tx.tenantDomain.create({
          data: {
            tenantId: tenant.id,
            hostname: primaryDomain,
            isPrimary: true,
          },
        });

        domainId = domain.id;
      }
    }

    let cloudTaskId: string | null = null;

    if (input.autoProvision) {
      if (!primaryDomain) {
        notes.push("provision_skipped_missing_domain");
      } else if (!nodeKey) {
        notes.push("provision_skipped_missing_node");
      } else {
        const node = await tx.cloudPanelNode.findUnique({ where: { key: nodeKey } });
        if (!node || !node.isEnabled) {
          notes.push(`provision_skipped_node_unavailable:${nodeKey}`);
        } else {
          const payload = {
            tenantId: tenant.id,
            tenantSlug: tenant.slug,
            domain: primaryDomain,
            phpVersion: input.phpVersion?.trim() || "8.2",
            planId,
            productIds,
          } satisfies Prisma.InputJsonValue;

          const task = await tx.cloudPanelTask.create({
            data: {
              nodeId: node.id,
              tenantId: tenant.id,
              createdByUserId: input.createdByUserId ?? null,
              taskType: "CREATE_SITE",
              payload,
              idempotencyKey: `tenant:${tenant.id}:domain:${primaryDomain}:create_site`,
            },
          });

          cloudTaskId = task.id;
        }
      }
    }

    return {
      tenantId: tenant.id,
      tenantSlug: tenant.slug,
      domainId,
      cloudTaskId,
      notes,
    };
  });
}
