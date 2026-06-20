#!/usr/bin/env python3
import argparse
import select
import socket
import socketserver
import sys
import threading
import time

import paramiko


class TunnelServer(socketserver.ThreadingTCPServer):
    allow_reuse_address = True
    daemon_threads = True


class Handler(socketserver.BaseRequestHandler):
    ssh_transport = None
    remote_host = "127.0.0.1"
    remote_port = 5432

    def handle(self):
        try:
            chan = self.ssh_transport.open_channel(
                kind="direct-tcpip",
                dest_addr=(self.remote_host, self.remote_port),
                src_addr=self.request.getpeername(),
            )
        except Exception as exc:
            sys.stderr.write(f"open_channel failed: {exc}\n")
            return

        if chan is None:
            sys.stderr.write("open_channel returned None\n")
            return

        sockets = [self.request, chan]
        try:
            while True:
                readable, _, _ = select.select(sockets, [], [], 1.0)
                if self.request in readable:
                    data = self.request.recv(16384)
                    if not data:
                        break
                    chan.sendall(data)
                if chan in readable:
                    data = chan.recv(16384)
                    if not data:
                        break
                    self.request.sendall(data)
        finally:
            try:
                chan.close()
            except Exception:
                pass
            try:
                self.request.close()
            except Exception:
                pass


def parse_args():
    parser = argparse.ArgumentParser(description="Forward local TCP port to remote PostgreSQL over SSH.")
    parser.add_argument("--ssh-host", required=True)
    parser.add_argument("--ssh-port", type=int, default=22)
    parser.add_argument("--ssh-user", required=True)
    parser.add_argument("--ssh-password", required=True)
    parser.add_argument("--local-host", default="127.0.0.1")
    parser.add_argument("--local-port", type=int, default=5432)
    parser.add_argument("--remote-host", default="127.0.0.1")
    parser.add_argument("--remote-port", type=int, default=5432)
    return parser.parse_args()


def main():
    args = parse_args()

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        hostname=args.ssh_host,
        port=args.ssh_port,
        username=args.ssh_user,
        password=args.ssh_password,
        timeout=15,
        auth_timeout=15,
        banner_timeout=15,
        look_for_keys=False,
        allow_agent=False,
    )

    transport = ssh.get_transport()
    if transport is None or not transport.is_active():
        raise SystemExit("SSH transport not active")

    Handler.ssh_transport = transport
    Handler.remote_host = args.remote_host
    Handler.remote_port = args.remote_port

    server = TunnelServer((args.local_host, args.local_port), Handler)
    sys.stdout.write(
        f"Tunnel listening on {args.local_host}:{args.local_port} -> {args.remote_host}:{args.remote_port} via {args.ssh_user}@{args.ssh_host}:{args.ssh_port}\n"
    )
    sys.stdout.flush()

    keep_running = True

    def keepalive():
        while keep_running:
            try:
                transport.send_ignore()
            except Exception:
                break
            time.sleep(30)

    thread = threading.Thread(target=keepalive, daemon=True)
    thread.start()

    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        keep_running = False
        server.server_close()
        ssh.close()


if __name__ == "__main__":
    main()
