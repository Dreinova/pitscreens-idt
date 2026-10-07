# IDT App — Pantallas Interactivas de Puntos de Información Turística

Sistema de gestión y visualización de contenido multimedia para las pantallas táctiles interactivas de los Puntos de Información Turística (PIT) de la ciudad de Bogotá.

---

## Tabla de Contenidos

- [Descripción general](#descripción-general)
- [Arquitectura del sistema](#arquitectura-del-sistema)
- [Estructura de carpetas](#estructura-de-carpetas)
- [Módulo App (Pantalla)](#módulo-app-pantalla)
- [Módulo Admin (Panel Administrativo)](#módulo-admin-panel-administrativo)
- [Base de datos](#base-de-datos)
- [Flujos de usuario](#flujos-de-usuario)
- [Requisitos del servidor](#requisitos-del-servidor)
- [Instalación](#instalación)
- [Roles de usuario](#roles-de-usuario)
- [Notas de seguridad](#notas-de-seguridad)

---

## Descripción general

IDT App es una aplicación web en PHP que gestiona el contenido mostrado en pantallas táctiles interactivas ubicadas en puntos de información turística de la ciudad. El sistema está compuesto por dos módulos:

| Módulo | Ruta | Descripción |
|--------|------|-------------|
| **App (Pantalla)** | `/app/` | Interfaz que se ejecuta en cada kiosco táctil. Muestra galerías de imágenes/videos y permite el acceso interactivo al contenido web del IDT. |
| **Admin** | `/admin/` | Panel de administración web para gestionar contenido, programación, módulos y usuarios. |

---

## Arquitectura del sistema

```
┌─────────────────────────────────────────────────────────┐
│                    SERVIDOR WEB (Apache/Nginx + PHP)     │
│                                                         │
│  ┌─────────────────┐        ┌───────────────────────┐  │
│  │   Módulo APP    │        │    Módulo ADMIN        │  │
│  │   /app/         │        │    /admin/             │  │
│  │                 │        │                        │  │
│  │ Pantallas táct. │        │ Panel administración   │  │
│  │ Kioscos PIT     │        │ Gestión de contenido   │  │
│  └────────┬────────┘        └──────────┬─────────────┘  │
│           │                            │                 │
│           └────────────┬───────────────┘                 │
│                        │                                 │
│              ┌─────────▼──────────┐                      │
│              │   Base de Datos    │                      │
│              │   MySQL: idt_app   │                      │
│              └────────────────────┘                      │
└─────────────────────────────────────────────────────────┘
```

**Stack tecnológico:**
- **Backend:** PHP (sin framework)
- **Base de datos:** MySQL — extensión MySQLi
- **Frontend Admin:** Bootstrap 4, jQuery, Font Awesome
- **Frontend App:** CSS personalizado, jQuery
- **Autenticación:** Sesiones PHP + hash MD5
- **Proxy:** cURL (para cargar URLs externas en iframe)

---

## Estructura de carpetas

```
pitScreens/
├── app/                          # Módulo pantalla / kiosco
│   ├── index.php                 # Pantalla principal con slideshow
│   ├── log_index.php             # Selección de módulo/kiosco
│   ├── login.php                 # Ingreso del número de documento
│   ├── comprobacion.php          # Verifica si el visitante está registrado
│   ├── registro.php              # Formulario de registro de nuevo visitante
│   ├── datos_visitantes.php      # Guarda los datos del visitante en BD
│   ├── frame.php                 # Muestra URL externa en iframe
│   ├── proxy.php                 # Proxy HTTP para URLs externas (cURL)
│   ├── tiempo.php                # Polling AJAX para cambio de programación
│   ├── activacion.php            # Activa/desactiva listas de reproducción
│   └── assets/
│       ├── css/styles.css        # Estilos de la interfaz del kiosco
│       ├── js/
│       │   ├── IDT_app.js        # Lógica principal de la app
│       │   ├── Inactividad1.js   # Detector de inactividad (login/registro)
│       │   └── Inactividad2.js   # Detector de inactividad (frame)
│       ├── img/                  # Imágenes de la interfaz del kiosco
│       └── media/IDT.mp4         # Video por defecto (sin programación)
│
├── admin/                        # Módulo panel administrativo
│   ├── index.php                 # Login del administrador
│   ├── inicio.php                # Dashboard principal
│   ├── contenido.php             # Gestión de imágenes y videos
│   ├── editar_contenido.php      # Editar elemento de contenido
│   ├── programacion.php          # Gestión de listas de reproducción
│   ├── editar_programacion.php   # Editar programación
│   ├── frame.php                 # Ver URL del iframe configurada
│   ├── editar_frame.php          # Editar URL del iframe
│   ├── modulos.php               # Gestión de módulos/kioscos
│   ├── editar_modulo.php         # Editar módulo registrado
│   ├── usuarios.php              # Gestión de usuarios del sistema
│   ├── editar_usuario.php        # Editar perfil de usuario
│   ├── perfil_usuario.php        # Perfil del usuario autenticado
│   ├── reporte_tabla.php         # Reporte general de uso
│   ├── reporte_visitantes.php    # Reporte de visitantes registrados
│   ├── reporte_charts_genero.php # Estadísticas por género
│   ├── reporte_charts_edad.php   # Estadísticas por edad
│   ├── reporte_charts_tiempo.php # Estadísticas por tiempo de uso
│   └── assets/
│       ├── php/
│       │   ├── Conexion_DB.php       # Configuración de conexión a MySQL
│       │   ├── Conexion_Reco.php     # Conexión a BD auxiliar (electronika)
│       │   ├── menu_lateral.php      # Menú lateral (sidebar)
│       │   ├── menu_superior.php     # Barra superior de navegación
│       │   ├── logout.php            # Cerrar sesión
│       │   ├── eliminar_img.php      # Eliminar archivo de galería
│       │   ├── eliminar_modulo.php   # Eliminar módulo
│       │   ├── eliminar_programacion.php # Eliminar programación
│       │   └── eliminar_usuario.php  # Eliminar usuario
│       ├── galeria/              # Archivos multimedia subidos (imágenes/videos)
│       ├── img_users/            # Fotos de perfil de usuarios
│       ├── ajax/                 # Endpoints AJAX
│       │   ├── upload2.php       # Subida alternativa de archivos
│       │   ├── upload_banner.php # Subida de banners
│       │   ├── banner_ajax.php   # Gestión AJAX de banners
│       │   ├── editar_banner.php # Edición de banners
│       │   └── pagination.php    # Paginación AJAX
│       ├── css/                  # Hojas de estilo del admin
│       ├── js/                   # Scripts del admin
│       └── images/               # Imágenes estáticas del admin
```

---

## Módulo App (Pantalla)

### Flujo de la pantalla interactiva

```
log_index.php → index.php → login.php → comprobacion.php
                                           ↓              ↓
                                      registro.php    frame.php
                                           ↓
                                   datos_visitantes.php → frame.php
```

### Descripción de cada archivo

| Archivo | Función |
|---------|---------|
| `log_index.php` | **Inicio del kiosco.** El operador selecciona cuál módulo/kiosco es esta pantalla. Establece `$_SESSION['log-modulo']` y `$_SESSION['modulo']`. |
| `index.php` | **Pantalla principal.** Reproduce en slideshow automático las imágenes y videos de la lista activa. Verifica y activa la programación correspondiente según la fecha/hora. |
| `login.php` | **Acceso de visitantes.** Solicita el número de documento. Compatible con lectores de códigos de barras. |
| `comprobacion.php` | **Verificación.** Consulta la tabla `visitantes` con el número de documento. Redirige a `registro.php` si es nuevo o a `frame.php` si ya existe. |
| `registro.php` | **Formulario de registro.** Captura tipo de documento, número, nombres, apellidos y aceptación de política de datos. |
| `datos_visitantes.php` | **Guardado de datos.** Inserta el nuevo visitante en la tabla `visitantes` y redirige a `frame.php`. |
| `frame.php` | **Contenido principal.** Muestra la URL configurada en un iframe usando `proxy.php` como intermediario. |
| `proxy.php` | **Proxy HTTP.** Descarga el contenido de la URL remota con cURL y lo sirve directamente para evadir restricciones de X-Frame-Options. |
| `tiempo.php` | **Polling de tiempo.** Se carga vía AJAX cada segundo desde `index.php`. Si detecta que la hora coincide con la Fecha-Inicio de una programación, redirige a `activacion.php`. |
| `activacion.php` | **Activación de playlist.** Cambia el campo `Estado` en `lista-reproduccion`: activa la programación correspondiente y desactiva todas las demás. |

### Slideshow multimedia

El slideshow en `index.php` funciona con JavaScript puro:

- **Imágenes:** Se muestran durante **10 segundos** (`setTimeout 10000ms`).
- **Videos MP4:** Se reproducen completos; al terminar el evento `ended` avanza al siguiente elemento.
- **Sin contenido:** Si no hay elementos en la lista activa, reproduce el video por defecto `assets/media/IDT.mp4`.
- **Loop:** Al llegar al último elemento, vuelve al primero automáticamente.

### Detección de inactividad

- `Inactividad1.js` — Monitorea la inactividad en las pantallas de login y registro.
- `Inactividad2.js` — Monitorea la inactividad en la pantalla de contenido (frame).
- Al detectar inactividad, redirigen automáticamente a `index.php` (pantalla de espera).

---

## Módulo Admin (Panel Administrativo)

### Autenticación

- **URL de acceso:** `/admin/index.php`
- El login acepta **nombre de usuario o correo electrónico**.
- Las contraseñas se almacenan con hash **MD5**.
- La sesión dura **2 horas** (`cookie_lifetime: 7200`).
- Opción "Mantener sesión iniciada" guarda las credenciales en cookie por 1 hora.

### Páginas del panel

| Página | Acceso | Descripción |
|--------|--------|-------------|
| `inicio.php` | Todos | Dashboard con accesos rápidos a los módulos según el rol |
| `contenido.php` | Todos | Subir, ver, editar y eliminar imágenes y videos |
| `programacion.php` | Todos | Crear y gestionar listas de reproducción con fecha de activación |
| `frame.php` | Todos | Ver la URL configurada para el iframe |
| `editar_frame.php` | Todos | Actualizar la URL del iframe |
| `modulos.php` | Todos | Registrar y gestionar módulos/kioscos |
| `usuarios.php` | **Solo Administrador** | Crear, editar y eliminar usuarios del sistema |
| `reporte_tabla.php` | **Solo Administrador** | Reporte general de interacciones (módulo, edad, género, tiempo) |
| `reporte_visitantes.php` | **Solo Administrador** | Listado de visitantes registrados |
| `reporte_charts_genero.php` | **Solo Administrador** | Gráficas de uso por género |
| `reporte_charts_edad.php` | **Solo Administrador** | Gráficas de uso por rango de edad |
| `reporte_charts_tiempo.php` | **Solo Administrador** | Gráficas de tiempo promedio de uso |

### Gestión de contenido

1. Ir a **Contenido** en el menú lateral.
2. Seleccionar el archivo (PNG, JPEG o MP4, máximo 80 MB).
3. Elegir el estado (Activo/Inactivo) y el número de orden.
4. Seleccionar a qué lista de reproducción pertenece.
5. Guardar — el archivo se copia a `assets/galeria/` y se registra en BD.

### Gestión de programación

1. Ir a **Programación** en el menú lateral.
2. Crear una nueva programación con nombre y fecha/hora de inicio.
3. El sistema activará automáticamente esa lista cuando llegue la fecha/hora programada.
4. Solo una programación puede estar activa a la vez.

---

## Base de datos

**Nombre:** `idt_app`

### Diagrama de tablas

```
usuarios ──────────────────────────────────────────────────────
│ ID | Nombre | Correo | Foto_Usuario | Funcion (FK) | Password │
└─────────────────────────────────────────────────────────────┘
                               │
                    funciones_usuario
                    │ ID | Funcion │
                    └─────────────┘

lista-reproduccion ─────────────────────────────────────────────
│ ID | Nombre | Fecha-Inicio | Estado | Fecha-Modificacion | Usuario │
└───────────────────────────────────────────────────────────────┘
         │ (1)
         │ (N)
contenido ──────────────────────────────────────────────────────
│ ID | URL | Tipo | Estado | Orden | Lista-Reproduccion | Fecha_Modificación | Usuario │
└───────────────────────────────────────────────────────────────┘

frame ───────────────────────────────
│ ID | URL | Fecha-Modificacion | Usuario │
└─────────────────────────────────────┘

modulos ─────────────────────────────────────────
│ ID | nombre_modulo | ubicacion | fecha_modificacion | Usuario │
└─────────────────────────────────────────────────┘

visitantes ─────────────────────────────────────────────────────
│ ID | Tipo_Documento | No_Documento | Nombres | Apellidos | modulo | Fecha_Ingreso │
└───────────────────────────────────────────────────────────────┘

reco (estadísticas de uso) ─────────────────────
│ ID | modulo | Edad | Genero | Hora | Tiempo │
└────────────────────────────────────────────┘
```

### Descripción de tablas

| Tabla | Descripción |
|-------|-------------|
| `usuarios` | Usuarios del panel administrativo con su rol y foto de perfil |
| `funciones_usuario` | Catálogo de roles: `Administrador` (ID=1) y `Colaborador` (ID=2) |
| `lista-reproduccion` | Programaciones con fecha de activación automática |
| `contenido` | Archivos multimedia asignados a listas de reproducción |
| `frame` | URL única configurada para mostrar en iframe en los kioscos |
| `modulos` | Registro de los kioscos/pantallas táctiles del sistema |
| `visitantes` | Historial de visitantes que han usado los kioscos |
| `reco` | Estadísticas de uso capturadas por reconocimiento facial u otro sistema externo (edad, género, tiempo) |

---

## Flujos de usuario

### Visitante en el kiosco

```
1. PANTALLA DE ESPERA (index.php)
   └── Slideshow de imágenes/videos de la programación activa
   └── Mensaje: "Toque la pantalla para comenzar"
   └── [Toca la pantalla]
          ↓
2. INGRESO DE DOCUMENTO (login.php)
   └── Ingresa número de documento (manual o lector de código)
   └── [Continuar]
          ↓
3. VERIFICACIÓN (comprobacion.php)
   ├── ¿Ya está registrado? → SÍ → frame.php
   └── ¿Nuevo visitante?   → NO → registro.php
          ↓ (si es nuevo)
4. REGISTRO (registro.php)
   └── Tipo de documento, número, nombres, apellidos
   └── Acepta política de datos
   └── [Continuar] → datos_visitantes.php → frame.php
          ↓
5. CONTENIDO (frame.php)
   └── URL externa cargada mediante proxy.php
   └── [Inactividad] → index.php (pantalla de espera)
```

### Administrador en el panel

```
1. LOGIN (admin/index.php)
   └── Usuario/Correo + Contraseña
          ↓
2. DASHBOARD (inicio.php)
   └── Accesos según rol (Administrador / Colaborador)
          ↓
3. GESTIÓN
   ├── Contenido    → Subir/editar/eliminar imágenes y videos
   ├── Programación → Crear/editar listas de reproducción con fecha
   ├── Frame        → Configurar URL del iframe
   ├── Módulos      → Registrar/editar kioscos
   ├── Usuarios     → (Solo Administrador) CRUD de usuarios
   └── Reportes     → (Solo Administrador) Estadísticas de uso
```

---

## Requisitos del servidor

| Componente | Versión mínima recomendada |
|------------|---------------------------|
| PHP | 7.2 o superior |
| MySQL | 5.7 o superior |
| Apache / Nginx | Cualquier versión actual |
| Extensión PHP cURL | Habilitada (para proxy.php) |
| Extensión PHP MySQLi | Habilitada |
| Extensión PHP GD | Recomendada (para procesamiento de imágenes) |

**Permisos de escritura requeridos:**
- `admin/assets/galeria/` — Para subida de imágenes y videos
- `admin/assets/img_users/` — Para fotos de perfil de usuarios

---

## Instalación

1. **Clonar o copiar el proyecto** en el directorio raíz del servidor web:
   ```bash
   # Ejemplo con Apache en Linux
   cp -r pitScreens/ /var/www/html/IDT_app/
   ```

2. **Crear la base de datos** MySQL:
   ```sql
   CREATE DATABASE idt_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importar el esquema** de tablas desde `idt_app.sql`, incluido en la raíz del repositorio.

4. **Configurar la conexión** a la base de datos en:
   - `admin/assets/php/Conexion_DB.php`
   ```php
   $host_db    = "localhost";  // Host del servidor MySQL
   $usuario_db = "root";       // Usuario de MySQL
   $clave_db   = "";           // Contraseña de MySQL
   $nombre_db  = "idt_app";    // Nombre de la base de datos
   ```

5. **Dar permisos de escritura** a las carpetas de subida:
   ```bash
   chmod 755 admin/assets/galeria/
   chmod 755 admin/assets/img_users/
   ```

6. **Verificar que cURL esté habilitado** en `php.ini`:
   ```ini
   extension=curl
   ```

7. **Acceder al panel administrativo:**
   ```
   http://localhost/IDT_app/admin/
   ```

8. **Configurar cada kiosco** accediendo a:
   ```
   http://localhost/IDT_app/app/
   ```
   Y seleccionando el módulo correspondiente en `log_index.php`.

---

## Roles de usuario

| Rol | Permisos |
|-----|----------|
| **Administrador** | Acceso total: contenido, programación, frame, módulos, usuarios, reportes |
| **Colaborador** | Acceso a: contenido, programación, frame, módulos (sin usuarios ni reportes) |

Los roles se asignan al crear o editar usuarios desde `admin/usuarios.php`.

---

## Notas de seguridad

> Las siguientes observaciones son recomendaciones para futuros desarrollos o ambientes de producción.

- **Contraseñas MD5:** El hash MD5 se considera inseguro para contraseñas en la actualidad. Se recomienda migrar a `password_hash()` / `password_verify()` de PHP (bcrypt).
- **Consultas SQL:** Las consultas usan interpolación de variables con `mysqli_escape_string`. Para mayor seguridad se recomienda migrar a **consultas preparadas** (`mysqli_prepare` / PDO).
- **Credenciales en código:** El archivo `Conexion_DB.php` contiene las credenciales de la base de datos en texto plano. En producción se recomienda usar variables de entorno o un archivo `.env` excluido del repositorio.
- **proxy.php:** El proxy no tiene lista blanca de URLs permitidas. Se recomienda validar que la URL pertenezca a dominios autorizados antes de hacer la petición cURL.
- **Eliminación de archivos:** Los scripts `eliminar_img.php` y `eliminar_usuario.php` no verifican sesión activa. Se recomienda agregar validación de sesión y CSRF token.
- **SSL en cURL:** `CURLOPT_SSL_VERIFYPEER` está desactivado en `proxy.php`. Activarlo en producción para evitar ataques MITM.

---

## Información adicional

- **Zona horaria configurada:** `America/Bogota` (UTC-5)
- **Duración de sesión Admin:** 2 horas
- **Duración de sesión Kiosco:** 1 año (pantallas 24/7)
- **Formatos de contenido soportados:** PNG, JPEG, MP4
- **Tamaño máximo de archivo:** 80 MB
- **Caché en kioscos:** Deshabilitada mediante meta tags HTTP para garantizar contenido actualizado

---

*Instituto Distrital de Turismo — Alcaldía Mayor de Bogotá D.C.*
