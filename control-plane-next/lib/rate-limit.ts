import { Ratelimit } from "@upstash/ratelimit";
import { Redis } from "@upstash/redis";

let limiter: Ratelimit | null = null;

function buildLimiter() {
  if (!process.env.UPSTASH_REDIS_REST_URL || !process.env.UPSTASH_REDIS_REST_TOKEN) {
    return null;
  }

  const redis = new Redis({
    url: process.env.UPSTASH_REDIS_REST_URL,
    token: process.env.UPSTASH_REDIS_REST_TOKEN,
  });

  return new Ratelimit({
    redis,
    limiter: Ratelimit.slidingWindow(30, "1 m"),
    analytics: true,
    prefix: "cp-api",
  });
}

export async function checkRateLimit(key: string) {
  if (!limiter) {
    limiter = buildLimiter();
  }

  if (!limiter) {
    return { success: true, limit: -1, remaining: -1, reset: 0 };
  }

  return limiter.limit(key);
}
