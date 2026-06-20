import { GlobalRole, ProductStatus, PrismaClient } from "@prisma/client";

const prisma = new PrismaClient();

function toDecimalString(value, fallback) {
  const parsed = Number(value ?? "");
  if (!Number.isFinite(parsed)) return String(fallback);
  return String(parsed);
}

async function seedPlans() {
  const plans = [
    {
      code: "starter",
      name: "Starter",
      description: "Inicio para panelistas nuevos.",
      monthlyPriceUsd: "19.00",
      creditPriceUsd: "1.0000",
      includedCredits: 20,
      maxUsers: 100,
      maxDomains: 2,
      maxCloudPanelNodes: 1,
      isActive: true,
    },
    {
      code: "growth",
      name: "Growth",
      description: "Escala media para ventas continuas.",
      monthlyPriceUsd: "49.00",
      creditPriceUsd: "0.9000",
      includedCredits: 75,
      maxUsers: 500,
      maxDomains: 10,
      maxCloudPanelNodes: 2,
      isActive: true,
    },
    {
      code: "enterprise",
      name: "Enterprise",
      description: "Operacion high-volume multi-dominio.",
      monthlyPriceUsd: "149.00",
      creditPriceUsd: "0.8000",
      includedCredits: 250,
      maxUsers: 5000,
      maxDomains: 50,
      maxCloudPanelNodes: 10,
      isActive: true,
    },
  ];

  for (const plan of plans) {
    await prisma.plan.upsert({
      where: { code: plan.code },
      create: plan,
      update: plan,
    });
  }
}

async function seedSystemProducts() {
  const starterPlan = await prisma.plan.findUnique({ where: { code: "starter" } });
  const growthPlan = await prisma.plan.findUnique({ where: { code: "growth" } });

  const products = [
    {
      key: "vpn-panel",
      name: "VPN Panel",
      description: "Gestion de tokens, SSH y usuarios VPN.",
      status: ProductStatus.ACTIVE,
      defaultPlanId: starterPlan?.id ?? null,
    },
    {
      key: "ssh-manager",
      name: "SSH Manager",
      description: "Gestion avanzada SSH/HWID/token por tenant.",
      status: ProductStatus.ACTIVE,
      defaultPlanId: growthPlan?.id ?? starterPlan?.id ?? null,
    },
  ];

  for (const product of products) {
    await prisma.systemProduct.upsert({
      where: { key: product.key },
      create: product,
      update: product,
    });
  }
}

async function seedControlSettings() {
  const globalCreditUsd = toDecimalString(process.env.FINANCE_DEFAULT_CREDIT_USD, 1);
  const globalUsdToBob = toDecimalString(process.env.FINANCE_DEFAULT_USD_TO_BOB, 6.96);

  await prisma.controlSetting.upsert({
    where: { key: "finance.defaults" },
    create: {
      key: "finance.defaults",
      description: "Defaults globales para recargas.",
      value: {
        globalCreditUsd: Number(globalCreditUsd),
        globalUsdToBob: Number(globalUsdToBob),
      },
    },
    update: {
      value: {
        globalCreditUsd: Number(globalCreditUsd),
        globalUsdToBob: Number(globalUsdToBob),
      },
    },
  });

  await prisma.controlSetting.upsert({
    where: { key: "control.bootstrap" },
    create: {
      key: "control.bootstrap",
      description: "Metadata base de bootstrap para superadmin.",
      value: {
        bootstrapAdminEmail: (process.env.CONTROL_BOOTSTRAP_ADMIN_EMAIL ?? "").trim().toLowerCase(),
      },
    },
    update: {
      value: {
        bootstrapAdminEmail: (process.env.CONTROL_BOOTSTRAP_ADMIN_EMAIL ?? "").trim().toLowerCase(),
      },
    },
  });
}

async function seedBootstrapSuperAdmin() {
  const email = (process.env.CONTROL_BOOTSTRAP_ADMIN_EMAIL ?? "").trim().toLowerCase();
  const userId = (process.env.CONTROL_BOOTSTRAP_ADMIN_USER_ID ?? "").trim();
  const fullName = (process.env.CONTROL_BOOTSTRAP_ADMIN_NAME ?? "Programmit Superadmin").trim();

  if (!email || !userId) {
    return {
      seeded: false,
      reason: "Define CONTROL_BOOTSTRAP_ADMIN_EMAIL y CONTROL_BOOTSTRAP_ADMIN_USER_ID para crear superadmin en DB.",
    };
  }

  await prisma.user.upsert({
    where: { id: userId },
    create: {
      id: userId,
      email,
      fullName,
      role: GlobalRole.SUPER_ADMIN,
    },
    update: {
      email,
      fullName,
      role: GlobalRole.SUPER_ADMIN,
    },
  });

  return { seeded: true, reason: `Superadmin listo: ${email} (${userId})` };
}

async function main() {
  await seedPlans();
  await seedSystemProducts();
  await seedControlSettings();
  const bootstrap = await seedBootstrapSuperAdmin();

  const [planCount, productCount, settingCount, userCount] = await Promise.all([
    prisma.plan.count(),
    prisma.systemProduct.count(),
    prisma.controlSetting.count(),
    prisma.user.count(),
  ]);

  console.log("Seed OK");
  console.log(`- plans: ${planCount}`);
  console.log(`- system_products: ${productCount}`);
  console.log(`- control_settings: ${settingCount}`);
  console.log(`- users: ${userCount}`);
  console.log(`- bootstrap_admin: ${bootstrap.seeded ? "CREATED/UPDATED" : "SKIPPED"}`);
  if (!bootstrap.seeded) {
    console.log(`  reason: ${bootstrap.reason}`);
  }
}

main()
  .catch((error) => {
    console.error("Seed error:", error);
    process.exitCode = 1;
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
