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
