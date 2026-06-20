import "dotenv/config";
import { Client } from "pg";

function maskDbUrl(input) {
  try {
    const url = new URL(input);
    if (url.password) url.password = "********";
    return url.toString();
  } catch {
    return "<invalid DATABASE_URL format>";
  }
}

function looksLikePlaceholder(url) {
  return /johndoe|randompassword|mydb/i.test(url);
}

async function main() {
  const connectionString = process.env.DATABASE_URL;
  if (!connectionString) {
    console.error("DATABASE_URL is not set.");
    console.error("Set it in .env or environment before running db commands.");
    process.exit(1);
  }

  console.log("Checking PostgreSQL connection...");
  console.log(`- DATABASE_URL: ${maskDbUrl(connectionString)}`);

  if (looksLikePlaceholder(connectionString)) {
    console.error("");
    console.error("Detected placeholder DATABASE_URL.");
    console.error("Replace it with your real PostgreSQL URL before migrations.");
    process.exit(1);
  }

  const client = new Client({
    connectionString,
    connectionTimeoutMillis: 7000,
  });

  try {
    await client.connect();
    const result = await client.query(
      "select current_database() as db, current_user as usr, inet_server_addr() as host, version() as ver"
    );
    const row = result.rows[0];

    console.log("");
    console.log("PostgreSQL connection OK");
    console.log(`- db: ${row.db}`);
    console.log(`- user: ${row.usr}`);
    console.log(`- host: ${row.host ?? "local/socket"}`);
    console.log(`- version: ${String(row.ver).split(",")[0]}`);
  } catch (error) {
    const code = error?.code ?? "unknown";
    const detail = error?.detail ?? "";
    const hint = error?.hint ?? "";
    const message = error?.message ?? String(error);

    console.error("");
    console.error("PostgreSQL connection FAILED");
    console.error(`- code: ${code}`);
    console.error(`- message: ${message}`);
    if (detail) console.error(`- detail: ${detail}`);
    if (hint) console.error(`- hint: ${hint}`);
    console.error("");
    console.error("Quick checks:");
    console.error("1) PostgreSQL is running and reachable from this host.");
    console.error("2) User/password in DATABASE_URL are correct.");
    console.error("3) DB exists and user has privileges.");
    console.error("4) SSL mode is compatible (add ?sslmode=require if needed).");
    process.exitCode = 1;
  } finally {
    await client.end().catch(() => undefined);
  }
}

main();
