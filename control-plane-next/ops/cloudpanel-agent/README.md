# Programmit CloudPanel Agent

Este agente conecta un nodo CloudPanel con el Control Plane central.

## Requisitos
- VPS con CloudPanel
- binarios instalados: `curl`, `jq`, `clpctl`, `openssl`

## Archivos
- `programmit-cloudpanel-agent.sh`: worker que hace pull/ack de tareas
- `install-service.sh`: instala el worker como servicio systemd

## Instalacion
```bash
cd /root
# copia esta carpeta al servidor (scp/rsync)
cd cloudpanel-agent
chmod +x programmit-cloudpanel-agent.sh install-service.sh
./install-service.sh https://control.programmit.com vps-bo-01 TU_TOKEN_LARGO 0
```

## Verificar
```bash
systemctl status programmit-control-agent
journalctl -u programmit-control-agent -f
```

## Task types soportados
- `CREATE_SITE`
  - usa: `clpctl site:add:php --domainName ... --phpVersion ... --vhostTemplate ... --siteUser ...`
- `DELETE_SITE`
  - usa: `clpctl site:delete --domainName ... --force`
- `RUN_COMMAND` (solo si `ALLOW_RUN_COMMAND=1` en env)

## Payload esperado para CREATE_SITE
```json
{
  "domain": "panel.cliente.com",
  "phpVersion": "8.2",
  "tenantSlug": "cliente-demo",
  "installLetsEncrypt": true
}
```

## Notas
- Si el dominio ya existe, se marca `SUCCESS` con `alreadyExists=true`.
- Si el tipo de tarea no esta soportado, se responde `FAILED` sin reintento.
