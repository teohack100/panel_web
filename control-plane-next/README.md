# PROGRAMMIT Control Plane (Superadmin)

Control Plane central para ecosistema SaaS White-Label Multi-Tenant.

Este proyecto controla todo el cerebro del negocio:
- Sistemas / productos
- Clientes / tiendas (tenants)
- Planes
- Pagos / metodos / recargas
- Dominios
- Suspensiones
- Marca blanca
- Orquestacion CloudPanel (nodos + task queue + agente)

## Stack
- Next.js (App Router) + TypeScript
- Tailwind CSS + componentes UI propios (estilo shadcn)
- Clerk (auth)
- Prisma + PostgreSQL
- Stripe
- Upstash Redis (rate-limit)
- Supabase (opcional, para RLS/analitica)
- Cloudflare R2 (opcional)
- Resend (opcional)
- Sentry (opcional)

## Variables de entorno
Copia `.env.example` a `.env` y completa:

```bash
cp .env.example .env
```

Variables minimas obligatorias para correr:
- `DATABASE_URL`
- `NEXT_PUBLIC_CLERK_PUBLISHABLE_KEY`
- `CLERK_SECRET_KEY`
- `CONTROL_PLANE_SECRET`

Recomendadas para produccion:
- `CONTROL_ADMIN_USER_IDS`
- Stripe, Upstash, Sentry, Resend, R2

## Base de datos (PostgreSQL principal)

El proyecto ya viene listo para PostgreSQL con Prisma:
- `datasource db { provider = "postgresql" }`
- migracion inicial versionada en `prisma/migrations/20260222120000_init_control_plane`
- seed base en `prisma/seed.mjs`

Flujo recomendado:

0. Validar conexion:
```bash
npm run db:check
```

Si aun no existe la base/usuario en tu servidor PostgreSQL, usa la plantilla:
- `ops/postgres-bootstrap.sql`

1. Generar cliente Prisma:
```bash
npm run db:generate
```

2. Aplicar migraciones:
```bash
npm run db:migrate:deploy
```

Ver estado de migraciones:
```bash
npm run db:migrate:status
```

3. Poblar datos base (planes/sistemas/settings):
```bash
npm run db:seed
```

4. Setup completo en un solo comando:
```bash
npm run db:setup
```
`db:setup` ahora ejecuta validacion de conexion antes de migrar.

Si trabajas local y quieres crear nuevas migraciones:
```bash
npm run db:migrate:dev -- --name nombre_cambio
```

### URL recomendada en produccion (app en la misma VPS)
```env
DATABASE_URL="postgresql://control_plane_user:TU_PASSWORD@127.0.0.1:5432/control_plane_cp?schema=public&sslmode=disable"
```

Nota de seguridad:
- Mantener PostgreSQL escuchando solo en `127.0.0.1` (sin abrir `5432` a Internet).

Opcional (Supabase/RLS):
- ejecutar `supabase/rls.sql`
- adaptar `auth.uid()` al esquema de identidad que uses con Clerk.

## Ejecutar local

```bash
npm install
npm run dev
```

Abrir: `http://localhost:3000`

## Rutas principales

- ` / ` landing
- ` /sign-in ` login
- ` /control ` dashboard central
- ` /control/systems ` productos
- ` /control/tenants ` tenants/tiendas
- ` /control/plans ` planes
- ` /control/payments ` pagos y recargas
- ` /control/domains ` dominios
- ` /control/suspensions ` suspensiones
- ` /control/white-label ` marca blanca
- ` /control/cloudpanel ` nodos/tareas de CloudPanel

## APIs

### CloudPanel agent (sin Clerk, autenticacion por token hash)
- `POST /api/control/cloudpanel/agent/pull`
- `POST /api/control/cloudpanel/agent/ack`

Headers requeridos:
- `x-node-key: <node-key>`
- `Authorization: Bearer <agent-token>`

## Agente CloudPanel (instalacion rapida)

En este repo:
- `ops/cloudpanel-agent/programmit-cloudpanel-agent.sh`
- `ops/cloudpanel-agent/install-service.sh`

Instalacion en VPS CloudPanel:

```bash
cd /root/cloudpanel-agent
chmod +x programmit-cloudpanel-agent.sh install-service.sh
./install-service.sh https://control.programmit.com <NODE_KEY> <NODE_TOKEN> 0
```

Ver logs:

```bash
journalctl -u programmit-control-agent -f
```

### CloudPanel admin APIs (requiere cuenta admin Clerk)
- `GET/POST/PATCH /api/control/cloudpanel/nodes`
- `GET/POST/PATCH /api/control/cloudpanel/tasks`

### Core admin APIs (requiere cuenta admin Clerk)
- `GET/POST /api/control/systems`
- `GET/POST /api/control/tenants`
- `POST /api/control/provision` (onboarding 1-click: tenant + dominio + cola CloudPanel)
- `GET/POST/PATCH /api/control/plans`
- `GET/POST/PATCH /api/control/domains`
- `GET/POST/PATCH /api/control/payments/methods`
- `GET /api/control/payments/recharges`

### Webhook Stripe
- `POST /api/webhooks/stripe`

## Seguridad
- Guard de rol central (`SUPER_ADMIN`/`ADMIN`) en `/control/*` y APIs admin.
- Endpoints de agente protegidos por `agentTokenHash` + `timingSafeEqual`.
- Credenciales sensibles cifradas en reposo (`AES-256-GCM`) con `CONTROL_PLANE_SECRET`.
- Rate limit con Upstash.
- Audit log en tabla `audit_logs` para acciones criticas.

## Despliegue recomendado
- Vercel para frontend/API de Next
- PostgreSQL administrado (Supabase/Neon/RDS)
- Upstash Redis
- Sentry + Resend + R2 segun necesidad

