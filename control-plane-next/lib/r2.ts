import { S3Client } from "@aws-sdk/client-s3";

export const r2Client =
  process.env.R2_ACCOUNT_ID && process.env.R2_ACCESS_KEY_ID && process.env.R2_SECRET_ACCESS_KEY
    ? new S3Client({
        region: "auto",
        endpoint: `https://${process.env.R2_ACCOUNT_ID}.r2.cloudflarestorage.com`,
        credentials: {
          accessKeyId: process.env.R2_ACCESS_KEY_ID,
          secretAccessKey: process.env.R2_SECRET_ACCESS_KEY,
        },
      })
    : null;

export function r2BucketName() {
  return process.env.R2_BUCKET || "";
}

export function r2PublicBaseUrl() {
  return process.env.R2_PUBLIC_BASE_URL || "";
}
