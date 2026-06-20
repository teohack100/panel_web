-- PostgreSQL bootstrap template for PROGRAMMIT Control Plane
-- Execute as a superuser (or managed-db admin) in psql.
--
-- 1) Replace these values before running:
--    APP_DB   -> control_plane
--    APP_USER -> control_plane_user
--    APP_PASS -> strong password

DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'control_plane_user') THEN
    CREATE ROLE control_plane_user LOGIN PASSWORD 'CHANGE_ME_STRONG_PASSWORD';
  END IF;
END
$$;

DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'control_plane') THEN
    CREATE DATABASE control_plane OWNER control_plane_user;
  END IF;
END
$$;

\connect control_plane;

GRANT CONNECT ON DATABASE control_plane TO control_plane_user;
GRANT USAGE, CREATE ON SCHEMA public TO control_plane_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO control_plane_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT USAGE, SELECT, UPDATE ON SEQUENCES TO control_plane_user;

-- Optional hardening:
-- REVOKE CREATE ON SCHEMA public FROM PUBLIC;
-- ALTER DATABASE control_plane SET timezone TO 'UTC';

