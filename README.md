# UPTVal - Gestión Académica

Sistema de gestión académica para la **Universidad Politécnica Territorial de Valencia (UPTVal)**: control de asistencia, notas, cronograma académico y pénsum de los estudiantes.

> **Versión actual:** v1.0.3
> **Stack:** PHP 8.2 + MySQL 9.0 + Apache, contenedores Docker

---

## Tabla de Contenidos

1. [Funcionalidades](#funcionalidades)
2. [Stack Tecnológico](#stack-tecnológico)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Requisitos Previos](#requisitos-previos)
5. [Instalación en Desarrollo (Docker)](#instalación-en-desarrollo-docker)
6. [Configuración de Variables de Entorno](#configuración-de-variables-de-entorno)
7. [Base de Datos y Migraciones](#base-de-datos-y-migraciones)
8. [Acceso al Sistema](#acceso-al-sistema)
9. [Despliegue a Producción](#despliegue-a-producción)
10. [Procedimiento de Release (Git Tag)](#procedimiento-de-release-git-tag)
11. [Verificación Post-Deploy](#verificación-post-deploy)
12. [Solución de Problemas](#solución-de-problemas)
13. [Seguridad en Producción](#seguridad-en-producción)

---

## Funcionalidades

| Módulo | Descripción |
|--------|-------------|
| **Autenticación** | Login por cédula y contraseña, "Recordarme" (token 30 días), recuperación de contraseña vía correo (AWS SES / PHPMailer). |
| **Dashboard** | Métricas del sistema: total de personal, estudiantes, departamentos, estados estudiantil y reportes por rol. |
| **Gestión de Personal** | CRUD de docentes/administrativos, condiciones, tipos de contrato, múltiples roles y exportación de PDF. |
| **Permisos y Roles** | Asignación de roles a usuarios, activación/desactivación de roles, búsqueda por cédula y PDF. |
| **Asignación Académica** | Asignación de materias a docentes, activación/desactivación y exportación de PDF. |
| **Departamentos** | CRUD de departamentos, asignación de coordinadores y exportación de PDF. |
| **Aulas y Laboratorios** | CRUD de aulas, generación de códigos QR (SVG y PDF) y exportación. |
| **Materias** | CRUD de materias, trayectos y especialidades. |
| **Estudiantes** | **Módulo de solo lectura** (el registro se realiza en el sistema de inscripción externo). Listado, filtros (trayecto, especialidad, estatus, búsqueda), paginación y exportación de matrícula estudiantil en PDF. |

### Exportaciones PDF disponibles

| Ruta | Archivo generado |
|------|------------------|
| `/estudiantes/export-pdf` | `estudiantes_AAAAMMDD_HHMMSS.pdf` (matrícula estudiantil) |
| `/personal/export-pdf` | `personal_AAAAMMDD_HHMMSS.pdf` |
| `/personal/export-pdf-one` | `registro_personal_CEDULA_AAAAMMDD_HHMMSS.pdf` |
| `/personal/permisos-roles/export-pdf` | `roles_AAAAMMDD_HHMMSS.pdf` |
| `/personal/asignacion-academica/export-pdf` | `asignacion_academica_AAAAMMDD_HHMMSS.pdf` |
| `/departamentos/export-pdf` | `directorio_departamentos_AAAAMMDD_HHMMSS.pdf` |
| `/departamentos/export-pdf-coordinator` | `coordinador_NOMBRE_AAAAMMDD_HHMMSS.pdf` |
| `/aulas/export-qr-pdf` | `qr_CODIGO_AAAAMMDD_HHMMSS.pdf` |

> Los PDFs respetan los filtros activos de la vista (se pasan por query string). Generados con **Dompdf** (paginación de 50 registros, numeración de páginas y encabezado UPTVal).

---

## Stack Tecnológico

| Capa | Tecnología | Versión |
|------|------------|---------|
| Lenguaje | PHP (Apache) | ^8.2 |
| Base de datos | MySQL | 9.0 |
| Contenedores | Docker + Docker Compose | v2 |
| Dependencias PHP | Composer | latest |
| PDF | dompdf/dompdf | ^3.1 |
| Correo | phpmailer/phpmailer | ^7.1 |
| QR | chillerlan/php-qrcode | ^6.0 |
| Variables de entorno | vlucas/phpdotenv | ^5.6 |
| CI/CD | GitHub Actions + EC2 (AWS) | — |

---

## Estructura del Proyecto

```
uptval-gestion-academica/
├── app/
│   ├── Controllers/        # Controladores (Auth, Dashboard, Staff, Materia, Aula, Estudiante, etc.)
│   ├── Core/               # Router, Database (PDO singleton), Session
│   ├── Models/             # Acceso a datos (User, Staff, Estudiante, Aula, Materia, Rol, etc.)
│   └── View/               # Vistas PHP (auth, dashboard, staff, materias, aulas, estudiantes...)
├── database/               # Migraciones SQL (aula, especialidad, estudiante)
├── public/                 # DocumentRoot de Apache (index.php + .htaccess)
│   ├── .htaccess           # Reescribe todas las rutas hacia index.php
│   └── index.php           # Punto de entrada único (front controller)
├── .env                    # Variables de entorno (NO se versiona, ver .gitignore)
├── .github/workflows/      # Pipeline de deploy a EC2
├── composer.json           # Dependencias y autoload PSR-4
├── docker-compose.yml      # Servicios web + db
├── Dockerfile              # Imagen PHP 8.2 + Apache + extensiones
└── README.md
```

**Autoload (PSR-4):**

| Prefijo | Ruta |
|---------|------|
| `Core\` | `app/Core/` |
| `Controllers\` | `app/Controllers/` |
| `Models\` | `app/Models/` |

---

## Requisitos Previos

- **Docker** con **Docker Compose v2** (mínimo, para desarrollo y producción).
- **Git** para clonar el repositorio.
- Puertos libres: `80` (web), `3310` (MySQL externo).
- Para el deploy automático: cuenta de **GitHub** y servidor **EC2 (AWS)** con Docker instalado.

---

## Instalación en Desarrollo (Docker)

```bash
# 1. Clonar el repositorio
git clone https://github.com/<usuario>/uptval-gestion-academica.git
cd uptval-gestion-academica

# 2. Crear el archivo .env (ver sección siguiente)
# Copiar el template de la sección "Configuración de Variables de Entorno"

# 3. Levantar los contenedores
docker compose up -d --build

# 4. Instalar dependencias de Composer (solo si vendor/ no existe)
docker run --rm -w /var/www/html -v "$(pwd):/var/www/html" php:8.2-cli sh -c "curl -sS https://getcomposer.org/installer | php && php composer.phar install && rm composer.phar"

# 5. Ejecutar las migraciones (ver sección Base de Datos)
```

La aplicación queda disponible en **http://localhost** y MySQL en **localhost:3310**.

---

## Configuración de Variables de Entorno

Crear un archivo `.env` en la raíz del proyecto. **Este archivo NO se versiona** (está en `.gitignore`) y es respaldado automáticamente durante el deploy.

```ini
APP_NAME=UPTVal_Gestion_Academica
APP_ENV=local                  # Cambiar a production en el servidor
APP_KEY=
APP_DEBUG=true                 # Cambiar a false en producción
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_VE

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# ── Base de datos ─────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=uptval_db               # Nombre del servicio docker (no cambiar)
DB_PORT=3310
DB_DATABASE=uptval_db_gestion
DB_USERNAME=db_usr_admin
DB_PASSWORD=<PASSWORD_APP>

# ── Sesión ────────────────────────────────────────────────────
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# ── Correo (AWS SES) ──────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=<AWS_SES_ACCESS_KEY>
MAIL_PASSWORD=<AWS_SES_SECRET>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@<dominio>"
MAIL_FROM_NAME="${APP_NAME}"

# ── Credenciales del contenedor MySQL ─────────────────────────
DB_MYSQL_ROOT_PASSWORD=<ROOT_PASSWORD_DB>
```

> **IMPORTANTE:** En producción definir `APP_ENV=production` y `APP_DEBUG=false`. Las credenciales reales de AWS SES y MySQL se guardan en el servidor (respaldadas en `~/.env.backup`), nunca en el repositorio.

---

## Base de Datos y Migraciones

La base de datos se crea automáticamente al arrancar el contenedor `db` (según `MYSQL_DATABASE`). Las **tablas** deben cargarse manualmente ejecutando las migraciones en orden:

| Archivo | Crea |
|---------|------|
| `database/migration_aula.sql` | Tabla `aula` (+ FK a `department`) |
| `database/migration_especialidad.sql` | Tabla `especialidad` + columna `id_especialidad` en `materia` |
| `database/migration_estudiante.sql` | Tabla `estudiante` + **15 registros de ejemplo** (12 activos / 3 inactivos) |

### Ejecutar migraciones

```bash
# Copiar la migración al contenedor
docker cp database/migration_estudiante.sql uptval_db:/tmp/migracion.sql

# Ejecutarla
docker exec uptval_db sh -c "cat /tmp/migracion.sql | mysql -u db_usr_admin -p'<DB_PASSWORD>' uptval_db_gestion"
```

### Tablas del sistema

```
user, rol, rol_user, rol_modulo, modulo
staff, staff_materia, department, contract_type, type_condition
materia, trayecto, especialidad
aula, estudiante
```

---

## Acceso al Sistema

El acceso es por **cédula + contraseña**. El usuario principal actual (activo):

| Cédula | Estado |
|--------|--------|
| `24569437` | Activo (administrador) |

> Los nuevos usuarios se registran con estado `pendiente` y deben activarse antes de iniciar sesión. La contraseña de los usuarios nuevos se define en el flujo de registro/recuperación (o por hash `password_hash` directamente en BD).

---

## Despliegue a Producción

El proyecto incluye un pipeline de **GitHub Actions** (`.github/workflows/deploy.yml`) que despliega automáticamente a un servidor **EC2 (AWS)** cuando se publica un tag de versión (`v*.*.*`).

### 1. Preparar el servidor EC2 (una sola vez)

```bash
# Instalar Docker y Docker Compose
sudo yum install -y docker git
sudo service docker start
sudo usermod -aG docker ec2-user

# Instalar docker compose v2 (plugin)
sudo mkdir -p /usr/local/lib/docker/cli-plugins
sudo curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m) -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
```

### 2. Configurar secretos en GitHub

En **Settings → Secrets and variables → Actions** del repositorio:

| Secreto | Descripción |
|---------|-------------|
| `EC2_HOST` | IP pública o dominio del servidor EC2 |
| `EC2_USERNAME` | Usuario SSH (típicamente `ec2-user`) |
| `EC2_SSH_KEY` | Clave privada SSH (PEM) para conectarse |
| `GITHUB_TOKEN` | Generado automáticamente por GitHub (se refiere como `${{ secrets.GITHUB_TOKEN }}`) |

### 3. Primer despliegue manual (crear `.env` en el servidor)

El pipeline **respaldará el `.env` existente** en cada deploy. Para la primera vez, crear el archivo en el servidor:

```bash
cd /home/ec2-user/web-app    # después del primer clone manual, o directamente
nano .env                    # pegar el template de la sección de variables de entorno
```

> Si no existe `.env.backup`, el pipeline lo advertirá y el sistema usará los valores por defecto de `Database.php`. Es **obligatorio** crear el `.env` correctamente antes de la primera puesta en marcha.

### 4. Disparar el despliegue

El deploy se activa automáticamente al publicar un tag:

```bash
git tag v1.0.3
git push origin v1.0.3
```

### 5. Qué hace el pipeline

1. Respaldar `.env` y detener/eliminar contenedores previos.
2. Clonar el repositorio.
3. Restaurar `.env` desde el backup.
4. Crear directorios `storage/` y `bootstrap/cache/` con permisos.
5. Instalar dependencias con Composer (`--no-dev`).
6. Construir y levantar contenedores (`docker compose up -d --build`).
7. Ajustar ownership para `www-data`.

---

## Procedimiento de Release (Git Tag)

Seguir convención de versionado semántico:

```bash
# Ver cambios pendientes
git status
git log --oneline -10

# Crear tag con mensaje
git tag -a v1.0.4 -m "Descripción del release"

# Publicar tag (dispara el deploy)
git push origin v1.0.4

# Verificar tags
git tag --list
```

---

## Verificación Post-Deploy

```bash
# Estado de los contenedores
docker ps --filter name=uptval

# Verificar la tabla de estudiantes (debe existir tras la migración)
docker exec uptval_db mysql -u db_usr_admin -p"<DB_PASSWORD>" uptval_db_gestion -e "SELECT COUNT(*) AS total, SUM(status='activo') AS activos FROM estudiante;"

# Probar endpoints principales
curl -I http://localhost/
curl -I http://localhost/dashboard       # redirige al login si no hay sesión
curl -I http://localhost/estudiantes     # idem

# Verificar logs de PHP
docker logs uptval_web_app --tail 50
```

### Checklist de producción

- [ ] `APP_ENV=production` y `APP_DEBUG=false` en el `.env` del servidor
- [ ] Migraciones ejecutadas en orden (`aula` → `especialidad` → `estudiante`)
- [ ] `vendor/` instalado con `--no-dev`
- [ ] Usuario administrador activo con contraseña robusta
- [ ] HTTPS configurado (proxy/ALB frente al puerto 80)
- [ ] Puerto 3310 de MySQL **restringido** (solo acceso interno/VPC)
- [ ] Credenciales de AWS SES válidas (prueba de recuperación de contraseña)

---

## Solución de Problemas

| Problema | Causa | Solución |
|----------|-------|----------|
| `Error critico de conexion a la base de datos` | `.env` incorrecto o MySQL no inicializado | Verificar credenciales en `.env` y `docker logs uptval_db` |
| `Table 'uptval_db_gestion.estudiantes' doesn't exist` | Migración no ejecutada | Ejecutar `database/migration_estudiante.sql` |
| Página 404 al navegar | Ruta no registrada en `public/index.php` | Verificar que la ruta exista en el router |
| Código QR no se ve en el PDF | Extensión GD ausente | Reconstruir imagen: `docker compose up -d --build` (la imagen ya incluye GD) |
| No llega el correo de recuperación | Credenciales SES o dominio no verificado | Verificar `MAIL_USERNAME/PASSWORD` y que el remitente esté verificado en SES |
| Cambios PHP no se reflejan | Cache del navegador o de opcode | `docker compose restart web` y limpiar caché del navegador |
| `docker compose` no encontrado | Versión vieja de Docker | Usar `docker-compose` (v1) o instalar el plugin v2 |

---

## Seguridad en Producción

1. **Nunca** versionar `.env` (ya está en `.gitignore`).
2. Rotar las credenciales de AWS SES y de la BD antes del primer deploy si han estado expuestas.
3. Cambiar la contraseña del usuario administrador inicial.
4. Restringir el puerto `3310` de MySQL al tráfico interno (grupo de seguridad EC2 / VPC).
5. Habilitar HTTPS (certificado/ALB) para cifrar sesiones y credenciales en tránsito.
6. Mantener `SESSION_LIFETIME` razonable y revisar los logs (`docker logs`) periódicamente.
