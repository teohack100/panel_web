# PANEL WEB deploy checklist

Usar esta guia cada vez que el proyecto pase de local a VPS o cuando migres de un servidor a otro.

## 1. Antes del deploy

1. Confirmar que el trabajo local esta en `C:\xampp\htdocs\projects\panel_web`.
2. Probar home, login, admin login y dashboard en local.
3. Revisar `git status` y confirmar que no vas a subir secretos, dumps ni backups.
4. Confirmar que `.env`, `.env.local` y archivos sensibles siguen ignorados por git.

## 2. Preparar Git

1. Hacer commit limpio con mensaje claro.
2. Hacer push al remoto correcto.
3. Confirmar rama y commit que vas a desplegar.

## 3. Backup en VPS

1. Entrar al VPS correcto.
2. Hacer backup de archivos del proyecto.
3. Hacer backup de la base de datos.
4. Confirmar que tienes una ruta de rollback antes de tocar produccion.

Ejemplo base:

```bash
cd /var/www
sudo tar -czf /root/panel_web_backup_$(date +%F_%H%M).tar.gz panel_web
sudo mariadb-dump programm_panel > /root/programm_panel_$(date +%F_%H%M).sql
```

## 4. Actualizar codigo

1. Confirmar que la ruta productiva es `/var/www/panel_web`.
2. Hacer `git pull` o deploy controlado.
3. Revisar que no haya conflictos ni archivos faltantes.

```bash
cd /var/www/panel_web
sudo git pull origin master
```

## 5. Entorno y configuracion

1. Confirmar que existe `.env` real en VPS.
2. Validar DB host, DB user, DB pass y DB name.
3. Confirmar `BOOTSTRAP_ADMIN_*` si la instalacion es nueva.
4. Durante migraciones delicadas, considerar `LEGACY_SERVER_PROBES_ENABLED=0`.

Ejemplo minimo:

```env
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=change_me
DB_PASS=change_me
DB_NAME=programm_panel
DB_SCHEMA=
LEGACY_SERVER_PROBES_ENABLED=1
BOOTSTRAP_ADMIN_USER=owner
BOOTSTRAP_ADMIN_PASS=ChangeMe123!
BOOTSTRAP_ADMIN_EMAIL=owner@example.com
BOOTSTRAP_ADMIN_NAME=Project Owner
```

## 6. Base de datos

1. Ejecutar schema o migraciones si aplica.
2. Verificar que existan tablas criticas como `users`, `server_list` y `vpn_servers`.
3. Si es una instalacion nueva, ejecutar bootstrap del owner.

```bash
cd /var/www/panel_web
sudo php tools/bootstrap_admin.php
```

## 7. Permisos

1. Confirmar que el servidor web puede escribir en las rutas runtime.
2. Revisar `templates_c/`.
3. Revisar `profile/`.
4. Revisar `serverside/_uploads/`.
5. Revisar `logo/branding/`.
6. Revisar `logo/metodos/`.

```bash
sudo chown -R www-data:www-data /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos
sudo find /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos -type d -exec chmod 775 {} \;
sudo find /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos -type f -exec chmod 664 {} \;
```

## 8. Dominio y SSL

1. Confirmar que `panel.programmit.com` apunta al VPS correcto.
2. Confirmar que el proxy o vhost responde por HTTPS.
3. Evitar apuntar nodos o panel a IP fija cuando el dominio ya existe.
4. Si usas proxy manager, revisar host, certificado y forward port.

## 9. Verificacion tecnica

1. Correr el doctor del proyecto con el usuario real del servidor web.
2. Revisar resolucion DNS del host del panel.
3. Revisar login admin.
4. Revisar dashboard.
5. Revisar logs de Apache o Nginx.

```bash
cd /var/www/panel_web
sudo -u www-data php tools/doctor.php
sudo systemctl restart apache2
```

## 10. Smoke test funcional

1. Abrir `https://panel.programmit.com/`
2. Abrir `https://panel.programmit.com/admin-login.php`
3. Iniciar sesion
4. Abrir dashboard
5. Confirmar que no hay errores de DB, Smarty o permisos
6. Confirmar que assets y estilos cargan correctamente

## 11. Nodos internos

1. Si el panel sincroniza nodos o servidores internos, revisar si esos nodos usan dominio o IP fija.
2. Confirmar si los nodos dependen de configuracion local y no solo de la base central.
3. Si un nodo se vuelve inestable durante la migracion, apagar temporalmente probes legacy:

```env
LEGACY_SERVER_PROBES_ENABLED=0
```

4. Reiniciar el servicio web y vigilar el nodo.

## 12. Cierre

1. Guardar commit desplegado, fecha, VPS y dominio.
2. Guardar backups creados.
3. Anotar cualquier pendiente post-deploy.
4. Cambiar credenciales iniciales si el proyecto queda para cliente final.
