import { Resend } from "resend";

export const resend = process.env.RESEND_API_KEY
  ? new Resend(process.env.RESEND_API_KEY)
  : null;

export function defaultFromEmail() {
  return process.env.RESEND_FROM_EMAIL || "Control Plane <noreply@example.com>";
}
