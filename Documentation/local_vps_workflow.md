# PANEL WEB: flujo profesional local -> git -> VPS

## 1. Regla base

Nunca mezclar configuracion local con produccion dentro del codigo.

- Local: usar `.env.local`
- Produccion: usar variables reales del VPS o un archivo fuera del repo
- Git: subir solo `.env.example`, nunca secretos reales

## 2. Entorno local recomendado

Mientras el proyecto siga en XAMPP, la forma estable de trabajo es:

- URL temporal funcional: `http://127.0.0.1/`
- Login general: `http://127.0.0.1/index.php?p=login`
- Login admin: `http://127.0.0.1/admin-login.php`
- Base local: `programm_panel`
- Motor local actual: `MySQL/MariaDB`

Cuando Apache ya quede reiniciado con el vhost correcto, la meta es trabajar con un solo dominio local canonico, por ejemplo:

- `http://panel.programmit.test/`

## 3. Carpetas que deben ser escribibles

Estas rutas no deben tratarse como codigo estatico:

- `templates_c/`
- `profile/`
- `serverside/_uploads/`
- `logo/branding/`
- `logo/metodos/`

En VPS, estas carpetas deben existir y tener permisos de escritura para el usuario del servidor web.

Comandos base en Ubuntu/Apache:

```bash
sudo chown -R www-data:www-data /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos
sudo find /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos -type d -exec chmod 775 {} \;
sudo find /var/www/panel_web/templates_c /var/www/panel_web/profile /var/www/panel_web/serverside/_uploads /var/www/panel_web/logo/branding /var/www/panel_web/logo/metodos -type f -exec chmod 664 {} \;
sudo find /var/www/panel_web/templates_c -mindepth 1 ! -name '.gitkeep' -delete
sudo -u www-data php /var/www/panel_web/tools/doctor.php
```

Nota importante:

- No validar permisos con `sudo php tools/doctor.php`, porque `root` puede dar un falso positivo.
- En produccion, el chequeo correcto es con `sudo -u www-data php /var/www/panel_web/tools/doctor.php`.

## 4. Flujo de trabajo recomendado

1. Trabajar y probar en local.
2. Ejecutar el bootstrap del owner si la instalacion es nueva o si vas a entregar el sistema a un cliente.
3. Confirmar login, panel y base de datos antes de tocar produccion.
4. Hacer commit limpio en Git.
5. Subir al remoto.
6. En VPS: hacer backup de archivos y base antes de actualizar.
7. Hacer `git pull` o deploy controlado.
8. Ejecutar cambios de base si corresponde.
9. Verificar dominio, login, panel admin y logs.

## 5. Orden profesional de configuracion

Separar siempre estas capas:

- Codigo del proyecto
- Configuracion de entorno
- Base de datos
- Archivos subidos por usuarios
- Backups

Estructura mental recomendada:

- Repo Git: solo codigo y archivos necesarios para reproducir el proyecto
- VPS: codigo desplegado + `.env` real + uploads + backups + logs
- DNS/dominio: configurado aparte, nunca hardcodeado como unica ruta del sistema

## 6. Siguiente objetivo recomendado

Antes de agregar mas modulos o limpiar mas vistas, conviene cerrar estos puntos:

1. Dejar un admin local dedicado para desarrollo.
2. Normalizar un solo dominio local.
3. Revisar permisos y rutas de escritura.
4. Preparar checklist de deploy a VPS.

Con eso, cualquier mejora futura del panel ya se hace sobre una base estable.

## 7. Bootstrap profesional del owner

Este panel historicamente dependia mucho de `user_id=1`. Por compatibilidad, la forma profesional de entregarlo no es dejar una clave escondida, sino definir el owner al instalar.

Flujo recomendado:

1. Configurar en `.env.local` o en variables del VPS:
   `BOOTSTRAP_ADMIN_USER`, `BOOTSTRAP_ADMIN_PASS`, `BOOTSTRAP_ADMIN_EMAIL`, `BOOTSTRAP_ADMIN_NAME`
2. Ejecutar el comando de bootstrap:
   `C:\xampp\php\php.exe tools\bootstrap_admin.php`
3. Entrar por `admin-login.php`
4. Guardar esas credenciales fuera del repo
5. Si hace falta cambiar el owner para otro cliente, volver a ejecutar el mismo comando con nuevos datos

Ejemplo en VPS Linux:

- `php tools/bootstrap_admin.php`

Regla importante:

- No entregar dumps con usuarios reales o contrasenas historicas.
- Entregar codigo + base limpia o controlada + bootstrap del owner.

## 8. Mini checklist de entrega

1. Configurar `.env.local` o `.env` del VPS con base de datos real.
2. Definir `BOOTSTRAP_ADMIN_USER`, `BOOTSTRAP_ADMIN_PASS`, `BOOTSTRAP_ADMIN_EMAIL` y `BOOTSTRAP_ADMIN_NAME`.
3. Ejecutar `php tools/bootstrap_admin.php`.
4. Probar acceso en `admin-login.php`.
5. Confirmar que el owner entra como `superadmin`.
6. Revisar permisos de `templates_c/`, `profile/`, `serverside/_uploads/`, `logo/branding/` y `logo/metodos/`.
7. Guardar las credenciales finales fuera del repo.
8. Si el proyecto pasa a cliente final, cambiar la clave inicial apenas se valide el acceso.
9. En VPS Linux, correr `sudo -u www-data php /var/www/panel_web/tools/doctor.php` antes de dar el deploy por cerrado.

## 9. Aislar monitoreo legacy de servidores

Este panel heredado tiene vistas y cron legacy que hacen sondeos directos a servidores registrados en `server_list`.

Casos tipicos:

- `includes/cronjob/cronjob_servers.php` prueba puerto `22`
- `server-status` prueba puerto `80`
- `online-users-*` y `privateusers` leen `server_parser`

Si estas migrando a un VPS nuevo o sospechas que un servidor interno se esta viendo afectado, puedes apagar temporalmente esos probes sin borrar registros ni usuarios.

En `.env` del VPS:

```env
LEGACY_SERVER_PROBES_ENABLED=0
```

Luego reiniciar Apache o PHP-FPM segun el stack.

Uso recomendado:

1. Activar `LEGACY_SERVER_PROBES_ENABLED=0`
2. Reiniciar el servicio web
3. Vigilar el servidor interno afectado
4. Si el problema desaparece, revisar cron, vistas legacy y `server_parser` antes de volver a activarlo

## 10. Checklist maestro Programmit

Usar esta secuencia como flujo fijo para cualquier panel, web o sistema nuevo.

Para deploy operativo paso a paso en VPS, usar tambien `Documentation/DEPLOY_CHECKLIST.md`.

### A. Local

1. Confirmar ruta real del proyecto.
2. Verificar `.env.local`.
3. Confirmar base local activa.
4. Probar login, panel y rutas principales.
5. Revisar que no haya secretos reales dentro del codigo.

### B. Git

1. Ejecutar `git status`.
2. Revisar que `.env`, backups, uploads y secretos no entren al commit.
3. Hacer commit con mensaje limpio y claro.
4. Subir cambios al remoto correcto.

### C. VPS

1. Confirmar ruta productiva, por ejemplo `/var/www/panel_web`.
2. Hacer backup de archivos antes del deploy.
3. Hacer backup de base de datos antes del deploy.
4. Actualizar codigo con `git pull` o deploy controlado.
5. Revisar owner, grupo y permisos del proyecto.
6. Ejecutar validaciones del sistema como `tools/doctor.php`.

### D. Dominio y DNS

1. Confirmar que el dominio o subdominio apunta al VPS correcto.
2. Evitar dependencias a IP fija dentro del codigo.
3. Preferir dominio canonico para panel, APIs y nodos.
4. Validar propagacion DNS antes de cerrar el deploy.

### E. SSL

1. Confirmar que el certificado cubre el dominio correcto.
2. Verificar renovacion futura del certificado.
3. Forzar HTTPS solo cuando el proxy o vhost ya responda bien.
4. Probar `https://dominio` y login real despues del cambio.

### F. Base de datos

1. Confirmar motor correcto por entorno.
2. Validar credenciales en `.env`.
3. Ejecutar schema o migraciones pendientes.
4. Confirmar tablas clave del sistema.
5. Verificar acceso de login, owner y modulos criticos.

### G. Panel y permisos

1. Verificar `templates_c/`, `profile/`, `serverside/_uploads/`, `logo/branding/` y `logo/metodos/`.
2. Confirmar que Smarty, uploads y branding escriben correctamente.
3. Revisar que el owner entra como `superadmin`.
4. Confirmar que no queden credenciales historicas o hardcodeadas.

### H. Nodos o servidores internos

1. Confirmar si los nodos usan dominio o IP fija.
2. Revisar si existe sync real o configuracion local independiente.
3. Antes de una migracion, comprobar cron, probes y servicios de tunel.
4. Si hay riesgo operativo, desactivar temporalmente probes legacy.

### I. Verificacion final

1. Abrir home, login, admin login y dashboard.
2. Revisar logs web y logs PHP.
3. Confirmar que el sistema responde por dominio productivo.
4. Confirmar que SSL, DB y permisos quedaron estables.
5. Documentar que se cambio y que quedo pendiente.

### J. Cierre profesional

1. Guardar accesos finales fuera del repo.
2. Anotar IP, dominio, ruta VPS y nombre de base real.
3. Dejar un mini changelog del deploy.
4. Si el proyecto pasa a cliente, cambiar claves iniciales y cerrar accesos temporales.
