-- Supabase RLS baseline for Control Plane (PostgreSQL)
-- IMPORTANT:
-- 1) Ajusta el mapeo auth.uid() <-> users.id (Clerk) segun tu estrategia JWT.
-- 2) Ejecuta como rol con permisos de ALTER TABLE/POLICY.

create or replace function public.cp_is_super_admin()
returns boolean
language sql
stable
as $$
  select exists (
    select 1
    from public.users u
    where u.id = auth.uid()::text
      and u.role in ('SUPER_ADMIN', 'ADMIN')
  );
$$;

create or replace function public.cp_is_tenant_member(p_tenant_id text)
returns boolean
language sql
stable
as $$
  select exists (
    select 1
    from public.tenant_memberships tm
    where tm.tenant_id = p_tenant_id
      and tm.user_id = auth.uid()::text
  );
$$;

-- Users
alter table public.users enable row level security;
create policy if not exists users_self_or_admin_select on public.users
for select using (id = auth.uid()::text or public.cp_is_super_admin());

-- Tenants
alter table public.tenants enable row level security;
create policy if not exists tenants_member_or_admin_select on public.tenants
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(id));
create policy if not exists tenants_admin_all on public.tenants
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

-- Tenant memberships
alter table public.tenant_memberships enable row level security;
create policy if not exists tenant_memberships_member_or_admin on public.tenant_memberships
for select using (public.cp_is_super_admin() or user_id = auth.uid()::text or public.cp_is_tenant_member(tenant_id));

-- Tenant scoped tables
alter table public.tenant_domains enable row level security;
create policy if not exists tenant_domains_member_or_admin on public.tenant_domains
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));

alter table public.payment_methods enable row level security;
create policy if not exists payment_methods_member_or_admin on public.payment_methods
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));

alter table public.recharges enable row level security;
create policy if not exists recharges_member_or_admin on public.recharges
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));

alter table public.white_label_profiles enable row level security;
create policy if not exists white_label_member_or_admin on public.white_label_profiles
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));

alter table public.tenant_products enable row level security;
create policy if not exists tenant_products_member_or_admin on public.tenant_products
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));

-- CloudPanel tasks/sites are sensitive: only admin full access
alter table public.cloudpanel_nodes enable row level security;
create policy if not exists cloudpanel_nodes_admin_only on public.cloudpanel_nodes
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

alter table public.cloudpanel_sites enable row level security;
create policy if not exists cloudpanel_sites_admin_or_member on public.cloudpanel_sites
for select using (public.cp_is_super_admin() or public.cp_is_tenant_member(tenant_id));
create policy if not exists cloudpanel_sites_admin_all on public.cloudpanel_sites
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

alter table public.cloudpanel_tasks enable row level security;
create policy if not exists cloudpanel_tasks_admin_only on public.cloudpanel_tasks
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

-- Global control tables: admin only
alter table public.system_products enable row level security;
create policy if not exists system_products_admin_only on public.system_products
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

alter table public.plans enable row level security;
create policy if not exists plans_admin_only on public.plans
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

alter table public.control_settings enable row level security;
create policy if not exists control_settings_admin_only on public.control_settings
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());

alter table public.audit_logs enable row level security;
create policy if not exists audit_logs_admin_only on public.audit_logs
for all using (public.cp_is_super_admin()) with check (public.cp_is_super_admin());
