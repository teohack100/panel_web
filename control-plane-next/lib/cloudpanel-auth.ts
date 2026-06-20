import { prisma } from "@/lib/db";
import { safeEqualHash } from "@/lib/crypto";

export async function authenticateCloudPanelNode(nodeKey: string, bearerToken: string) {
  if (!nodeKey || !bearerToken) {
    return null;
  }

  const node = await prisma.cloudPanelNode.findUnique({
    where: { key: nodeKey },
  });

  if (!node || !node.isEnabled) {
    return null;
  }

  if (!safeEqualHash(bearerToken, node.agentTokenHash)) {
    return null;
  }

  return node;
}
