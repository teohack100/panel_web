import Link from "next/link";
import { CloudPanelTaskStatus, RechargeStatus, TenantStatus } from "@prisma/client";

import { SectionHeader } from "@/components/control/section-header";
import { StatCard } from "@/components/control/stat-card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { prisma } from "@/lib/db";
import { formatDateTime, formatUsd } from "@/lib/format";
import { requireControlAdmin } from "@/lib/auth";

export default async function ControlDashboardPage() {
  await requireControlAdmin();

  const [
    totalSystems,
    activeSystems,
    totalTenants,
    activeTenants,
    suspendedTenants,
    totalPlans,
    totalDomains,
    activeDomains,
    pendingRecharges,
    paidRechargeAgg,
    totalNodes,
    activeNodes,
    recentTenants,
    recentTasks,
  ] = await Promise.all([
    prisma.systemProduct.count(),
    prisma.systemProduct.count({ where: { status: "ACTIVE" } }),
    prisma.tenant.count(),
    prisma.tenant.count({ where: { status: TenantStatus.ACTIVE } }),
    prisma.tenant.count({ where: { status: TenantStatus.SUSPENDED } }),
    prisma.plan.count({ where: { isActive: true } }),
    prisma.tenantDomain.count(),
    prisma.tenantDomain.count({ where: { status: "ACTIVE" } }),
    prisma.recharge.count({ where: { status: RechargeStatus.PENDING } }),
    prisma.recharge.aggregate({ where: { status: RechargeStatus.PAID }, _sum: { amountUsd: true } }),
    prisma.cloudPanelNode.count(),
    prisma.cloudPanelNode.count({ where: { isEnabled: true } }),
    prisma.tenant.findMany({
      orderBy: { createdAt: "desc" },
      take: 6,
      include: { owner: true, plan: true, _count: { select: { domains: true, products: true } } },
    }),
    prisma.cloudPanelTask.findMany({
      where: { status: { in: [CloudPanelTaskStatus.PENDING, CloudPanelTaskStatus.RUNNING, CloudPanelTaskStatus.FAILED] } },
      orderBy: { createdAt: "desc" },
      take: 8,
      include: { node: true, tenant: true },
    }),
  ]);

  const paidUsd = paidRechargeAgg._sum.amountUsd;

  return (
    <div className="space-y-8">
      <SectionHeader
        eyebrow="Control Plane"
        title="Cerebro central SaaS White-Label"
        description="Administra productos, clientes, planes, pagos, dominios, suspensiones y orquestacion CloudPanel desde una sola consola."
      />

      <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <StatCard title="Sistemas activos" value={`${activeSystems} / ${totalSystems}`} hint="Productos habilitados" />
        <StatCard title="Tiendas activas" value={`${activeTenants} / ${totalTenants}`} hint={`${suspendedTenants} suspendidas`} />
        <StatCard title="Planes activos" value={totalPlans} hint="Catalogo comercial" />
        <StatCard title="Pagos cobrados" value={formatUsd(paidUsd)} hint={`${pendingRecharges} recargas pendientes`} />
        <StatCard title="Dominios activos" value={`${activeDomains} / ${totalDomains}`} hint="Estado DNS/SSL" />
        <StatCard title="Nodos CloudPanel" value={`${activeNodes} / ${totalNodes}`} hint="Infraestructura" />
      </section>

      <section className="grid gap-4 xl:grid-cols-2">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Tiendas recientes</CardTitle>
              <CardDescription>Nuevos clientes en el ecosistema</CardDescription>
            </div>
            <Link href="/control/tenants">
              <Button size="sm" variant="outline">Ver todo</Button>
            </Link>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <Table>
                <THead>
                  <TR>
                    <TH>Tienda</TH>
                    <TH>Owner</TH>
                    <TH>Plan</TH>
                    <TH>Estado</TH>
                    <TH>Creada</TH>
                  </TR>
                </THead>
                <TBody>
                  {recentTenants.map((tenant) => (
                    <TR key={tenant.id}>
                      <TD>
                        <p className="font-medium text-white">{tenant.name}</p>
                        <p className="text-xs text-slate-400">{tenant.slug}</p>
                      </TD>
                      <TD>{tenant.owner.email}</TD>
                      <TD>{tenant.plan?.name ?? "Sin plan"}</TD>
                      <TD>
                        <Badge>{tenant.status}</Badge>
                      </TD>
                      <TD>{formatDateTime(tenant.createdAt)}</TD>
                    </TR>
                  ))}
                </TBody>
              </Table>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Tareas CloudPanel</CardTitle>
              <CardDescription>Cola de ejecucion y automatizaciones</CardDescription>
            </div>
            <Link href="/control/cloudpanel">
              <Button size="sm" variant="outline">Abrir modulo</Button>
            </Link>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <Table>
                <THead>
                  <TR>
                    <TH>Tipo</TH>
                    <TH>Nodo</TH>
                    <TH>Tenant</TH>
                    <TH>Estado</TH>
                    <TH>Fecha</TH>
                  </TR>
                </THead>
                <TBody>
                  {recentTasks.map((task) => (
                    <TR key={task.id}>
                      <TD>{task.taskType}</TD>
                      <TD>{task.node.name}</TD>
                      <TD>{task.tenant?.slug ?? "-"}</TD>
                      <TD>
                        <Badge>{task.status}</Badge>
                      </TD>
                      <TD>{formatDateTime(task.createdAt)}</TD>
                    </TR>
                  ))}
                </TBody>
              </Table>
            </div>
          </CardContent>
        </Card>
      </section>
    </div>
  );
}
