# PostgreSQL Production Checklist

## 1) Variables minimas
- `DATABASE_URL`
- `CONTROL_PLANE_SECRET` (>= 32 chars)
- `CONTROL_BOOTSTRAP_ADMIN_EMAIL`
- `CONTROL_BOOTSTRAP_ADMIN_NAME`
- `CONTROL_BOOTSTRAP_ADMIN_USER_ID` (opcional pero recomendado para seed superadmin)

Si la DB no existe aun:
- ejecutar `ops/postgres-bootstrap.sql` en PostgreSQL (ajustando usuario, pass y nombre de DB).

## 2) Migraciones
```bash
npm run db:check
npm run db:generate
npm run db:migrate:deploy
npm run db:migrate:status
```

## 3) Seed inicial
```bash
npm run db:seed
```

## 4) Verificacion rapida
```sql
SELECT count(*) FROM plans;
SELECT count(*) FROM system_products;
SELECT count(*) FROM control_settings;
SELECT id, email, role FROM users ORDER BY created_at DESC LIMIT 10;
```

## 5) Regla operativa
- Nuevas tablas/campos: siempre via Prisma migration.
- No hacer cambios manuales directos en produccion sin dejar migracion versionada.
- Mantener PostgreSQL privado (`listen_addresses=localhost` y sin abrir puerto 5432 en firewall publico).
