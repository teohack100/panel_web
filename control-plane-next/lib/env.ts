const required = [
  "DATABASE_URL",
  "CONTROL_PLANE_SECRET",
] as const;

export function assertServerEnv() {
  const missing = required.filter((k) => !process.env[k] || process.env[k]?.trim() === "");
  if (missing.length > 0) {
    throw new Error(`Missing required env vars: ${missing.join(", ")}`);
  }
}

export function getControlAdminIds() {
  return (process.env.CONTROL_ADMIN_USER_IDS ?? "")
    .split(",")
    .map((v) => v.trim())
    .filter(Boolean);
}

export function getControlAdminEmails() {
  return (process.env.CONTROL_ADMIN_EMAILS ?? "")
    .split(",")
    .map((v) => v.trim().toLowerCase())
    .filter(Boolean);
}

export function appUrl() {
  return process.env.NEXT_PUBLIC_APP_URL?.trim() || "http://localhost:3000";
}

export function isAuthBypassed() {
  return (process.env.CONTROL_PLANE_BYPASS_AUTH ?? "").trim() === "1";
}

export function bootstrapAdminEmail() {
  return (process.env.CONTROL_BOOTSTRAP_ADMIN_EMAIL ?? "admin@programmit.com").trim().toLowerCase();
}

export function bootstrapAdminName() {
  return (process.env.CONTROL_BOOTSTRAP_ADMIN_NAME ?? "Programmit Superadmin").trim();
}
