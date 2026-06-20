import { CloudPanelTaskType, Prisma } from "@prisma/client";
import { revalidatePath } from "next/cache";

import { SectionHeader } from "@/components/control/section-header";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { Textarea } from "@/components/ui/textarea";
import { requireControlAdmin } from "@/lib/auth";
import { writeAuditLog } from "@/lib/audit";
import { encryptText, hashToken } from "@/lib/crypto";
import { prisma } from "@/lib/db";
import { formatDateTime } from "@/lib/format";

async function saveNodeAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const key = String(formData.get("key") ?? "").trim().toLowerCase();
  const name = String(formData.get("name") ?? "").trim();
  const baseUrl = String(formData.get("baseUrl") ?? "").trim();
  const username = String(formData.get("username") ?? "").trim() || null;
  const secret = String(formData.get("secret") ?? "").trim();
  const agentToken = String(formData.get("agentToken") ?? "").trim();

  if (!key || !name || !baseUrl || !agentToken) return;

  await prisma.cloudPanelNode.upsert({
    where: { key },
    create: {
      key,
      name,
      baseUrl,
      username,
      secretEncrypted: secret ? encryptText(secret) : null,
      agentTokenHash: hashToken(agentToken),
      isEnabled: true,
    },
    update: {
      name,
      baseUrl,
      username,
      ...(secret ? { secretEncrypted: encryptText(secret) } : {}),
      agentTokenHash: hashToken(agentToken),
      isEnabled: true,
    },
  });

  await writeAuditLog({
    action: "CLOUDPANEL_NODE_UPSERT",
    actorUserId: admin.id,
    targetType: "cloudpanel_node",
    targetId: key,
  });

  revalidatePath("/control/cloudpanel");
}

async function enqueueTaskAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();

  const nodeKey = String(formData.get("nodeKey") ?? "").trim().toLowerCase();
  const tenantSlug = String(formData.get("tenantSlug") ?? "").trim().toLowerCase();
  const taskType = String(formData.get("taskType") ?? "").trim() as CloudPanelTaskType;
  const payloadRaw = String(formData.get("payload") ?? "").trim() || "{}";
  const idempotencyKey = String(formData.get("idempotencyKey") ?? "").trim() || null;

  if (!nodeKey || !Object.values(CloudPanelTaskType).includes(taskType)) return;

  const node = await prisma.cloudPanelNode.findUnique({ where: { key: nodeKey } });
  if (!node) return;

  let payload: Record<string, unknown> = {};
  try {
    payload = JSON.parse(payloadRaw);
  } catch {
    payload = { raw: payloadRaw };
  }

  const tenant = tenantSlug ? await prisma.tenant.findUnique({ where: { slug: tenantSlug } }) : null;

  const task = await prisma.cloudPanelTask.create({
    data: {
      nodeId: node.id,
      tenantId: tenant?.id ?? null,
      createdByUserId: admin.id,
      taskType,
      payload: payload as Prisma.InputJsonValue,
      idempotencyKey,
    },
  });

  await writeAuditLog({
    action: "CLOUDPANEL_TASK_ENQUEUE",
    actorUserId: admin.id,
    tenantId: tenant?.id,
    targetType: "cloudpanel_task",
    targetId: task.id,
    metadata: { nodeKey, taskType },
  });

  revalidatePath("/control/cloudpanel");
}

export default async function CloudPanelPage() {
  await requireControlAdmin();

  const [nodes, tasks] = await Promise.all([
    prisma.cloudPanelNode.findMany({
      orderBy: [{ isEnabled: "desc" }, { updatedAt: "desc" }],
      include: { _count: { select: { sites: true, tasks: true } } },
    }),
    prisma.cloudPanelTask.findMany({
      orderBy: { createdAt: "desc" },
      take: 80,
      include: { node: true, tenant: true, createdBy: true },
    }),
  ]);

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="CloudPanel"
        title="Orquestacion de infraestructura"
        description="Registra nodos, define token de agente y administra la cola de tareas automatizadas."
      />

      <Card>
        <CardHeader>
          <CardTitle>Registrar nodo</CardTitle>
          <CardDescription>El token del agente se guarda con hash (no reversible).</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={saveNodeAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            <Input name="key" placeholder="vps-bo-01" required />
            <Input name="name" placeholder="Bolivia Node 01" required />
            <Input name="baseUrl" placeholder="https://212.69.5.172:8443" required />
            <Input name="username" placeholder="programmit" />
            <Input name="secret" placeholder="CloudPanel password / API secret" />
            <Input name="agentToken" placeholder="token-agente-largo-y-unico" required />
            <div className="lg:col-span-3">
              <Button type="submit">Guardar nodo</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Encolar tarea</CardTitle>
          <CardDescription>Task queue central para provisioning y operaciones remotas.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={enqueueTaskAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
            <Input name="nodeKey" placeholder="vps-bo-01" required />
            <Input name="tenantSlug" placeholder="cliente-demo (opcional)" />
            <select
              name="taskType"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue={CloudPanelTaskType.CREATE_SITE}
            >
              {Object.values(CloudPanelTaskType).map((taskType) => (
                <option key={taskType} value={taskType}>
                  {taskType}
                </option>
              ))}
            </select>
            <Input name="idempotencyKey" placeholder="idempotency-key-opcional" />
            <div className="lg:col-span-4">
              <Textarea name="payload" placeholder='{"domain":"panel.cliente.com","phpVersion":"8.2"}' required />
            </div>
            <div className="lg:col-span-4">
              <Button type="submit">Crear tarea</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Nodos registrados</CardTitle>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Key</TH>
                <TH>Nodo</TH>
                <TH>URL</TH>
                <TH>Sites</TH>
                <TH>Tareas</TH>
                <TH>Last Seen</TH>
                <TH>Estado</TH>
              </TR>
            </THead>
            <TBody>
              {nodes.map((node) => (
                <TR key={node.id}>
                  <TD className="font-mono text-xs">{node.key}</TD>
                  <TD>{node.name}</TD>
                  <TD className="max-w-[300px] truncate">{node.baseUrl}</TD>
                  <TD>{node._count.sites}</TD>
                  <TD>{node._count.tasks}</TD>
                  <TD>{formatDateTime(node.lastSeenAt)}</TD>
                  <TD>
                    <Badge>{node.isEnabled ? "ENABLED" : "DISABLED"}</Badge>
                  </TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Cola de tareas</CardTitle>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Fecha</TH>
                <TH>Nodo</TH>
                <TH>Tenant</TH>
                <TH>Tipo</TH>
                <TH>Estado</TH>
                <TH>Intentos</TH>
                <TH>Error</TH>
              </TR>
            </THead>
            <TBody>
              {tasks.map((task) => (
                <TR key={task.id}>
                  <TD>{formatDateTime(task.createdAt)}</TD>
                  <TD>{task.node.key}</TD>
                  <TD>{task.tenant?.slug ?? "-"}</TD>
                  <TD>{task.taskType}</TD>
                  <TD>
                    <Badge>{task.status}</Badge>
                  </TD>
                  <TD>
                    {task.attempts}/{task.maxAttempts}
                  </TD>
                  <TD className="max-w-[320px] truncate text-xs text-rose-300">{task.errorMessage ?? "-"}</TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
