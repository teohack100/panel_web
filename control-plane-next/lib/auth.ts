import { auth, currentUser } from "@clerk/nextjs/server";
import { GlobalRole } from "@prisma/client";
import { redirect } from "next/navigation";

import { prisma } from "@/lib/db";
import {
  bootstrapAdminEmail,
  bootstrapAdminName,
  getControlAdminEmails,
  getControlAdminIds,
  isAuthBypassed,
} from "@/lib/env";

export async function requireSessionUserId() {
  if (isAuthBypassed()) {
    return "local-admin";
  }

  const session = await auth();
  if (!session.userId) {
    redirect("/sign-in");
  }
  return session.userId;
}

export async function syncCurrentUser() {
  if (isAuthBypassed()) {
    return prisma.user.upsert({
      where: { id: "local-admin" },
      create: {
        id: "local-admin",
        email: bootstrapAdminEmail(),
        fullName: bootstrapAdminName(),
        role: GlobalRole.SUPER_ADMIN,
      },
      update: {
        email: bootstrapAdminEmail(),
        fullName: bootstrapAdminName(),
        role: GlobalRole.SUPER_ADMIN,
      },
    });
  }

  const userId = await requireSessionUserId();
  const user = await currentUser();

  const primaryEmail =
    user?.emailAddresses?.find((e) => e.id === user.primaryEmailAddressId)?.emailAddress ||
    user?.emailAddresses?.[0]?.emailAddress ||
    `${userId}@clerk.local`;
  const normalizedEmail = primaryEmail.toLowerCase();

  const fullName = [user?.firstName ?? "", user?.lastName ?? ""].join(" ").trim() || user?.username || null;
  const imageUrl = user?.imageUrl || null;

  const isBootstrapAdmin =
    getControlAdminIds().includes(userId) || getControlAdminEmails().includes(normalizedEmail);

  const dbUser = await prisma.user.upsert({
    where: { id: userId },
    create: {
      id: userId,
      email: normalizedEmail,
      fullName,
      imageUrl,
      role: isBootstrapAdmin ? GlobalRole.SUPER_ADMIN : GlobalRole.USER,
    },
    update: {
      email: normalizedEmail,
      fullName,
      imageUrl,
      ...(isBootstrapAdmin ? { role: GlobalRole.SUPER_ADMIN } : {}),
    },
  });

  return dbUser;
}

export async function requireControlAdmin() {
  const dbUser = await syncCurrentUser();
  const isBootstrapAdmin = getControlAdminIds().includes(dbUser.id);
  const allowed = isBootstrapAdmin || dbUser.role === GlobalRole.SUPER_ADMIN || dbUser.role === GlobalRole.ADMIN;

  if (!allowed) {
    redirect("/no-access");
  }

  return dbUser;
}

export async function isControlAdminApi() {
  if (isAuthBypassed()) {
    return true;
  }

  const session = await auth();
  if (!session.userId) {
    return false;
  }

  const user = await prisma.user.findUnique({ where: { id: session.userId } });
  if (!user) {
    return getControlAdminIds().includes(session.userId);
  }

  return (
    getControlAdminIds().includes(session.userId) ||
    user.role === GlobalRole.SUPER_ADMIN ||
    user.role === GlobalRole.ADMIN
  );
}
