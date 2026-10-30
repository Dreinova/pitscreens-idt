# Despliegue: protector general solo con contenido general

**Cambio:** `app/index.php`. El protector general ya no aparece en las pantallas
que tienen contenido específico. Antes se mostraba en las pantallas con fila
propia en `configuracion`, porque `admin/editar_modulo.php` copia ahí el
protector general cuando solo se guarda la URL de toque o los tiempos. Ahora
el protector solo cuenta como propio si el archivo es distinto del general.

**Cambio 2:** `app/index.php` y `app/assets/css/styles.css`. El overlay de
toque queda solo con el círculo, la mano y un texto pequeño "Toca aquí" dentro
del círculo. Se quita el título "Toca la pantalla para comenzar" y se mantiene
la pastilla con el nombre de la pantalla.

- No hay migración SQL.
- No cambian archivos de la galería ni `Conexion_DB.php`.

## Pasos

Servidor: `useridt@10.216.153.78`. El acceso SSH es por contraseña y la
contraseña está en `PASS=` de `D:\NAS_DATA\Claude\NewCandelaria-test\.env`.

### 1. Commit y push (local)

```bash
cd /b/repos/pitScreens
git add app/index.php app/assets/css/styles.css
git commit -m "Protector propio solo si el archivo difiere del general; overlay de toque simplificado"
git push origin main
```

### 2. Sincronizar el código al servidor

```bash
git archive HEAD | ssh useridt@10.216.153.78 \
  'tar -x -C /data/visitbogota.co_proxy/pantallas.visitbogota.co'
```

> ⚠️ No agregues `< /dev/null` al final: rompe el pipe y tar falla con
> "does not look like a tar archive".

### 3. Restaurar la conexión a BD de producción (obligatorio)

`git archive` sobrescribe los `Conexion_DB.php` con las credenciales locales.

```bash
ssh useridt@10.216.153.78 '
  cp /data/visitbogota.co_proxy/pantallas.visitbogota.co.secrets/Conexion_DB.admin.php \
     /data/visitbogota.co_proxy/pantallas.visitbogota.co/admin/assets/php/Conexion_DB.php &&
  cp /data/visitbogota.co_proxy/pantallas.visitbogota.co.secrets/Conexion_DB.app.php \
     /data/visitbogota.co_proxy/pantallas.visitbogota.co/app/assets/php/Conexion_DB.php'
```

### 4. Rebuild solo de este servicio

```bash
ssh useridt@10.216.153.78 '
  cd /data/visitbogota.co_proxy &&
  sudo docker compose build pantallas_visitbogota_co &&
  sudo docker compose up -d pantallas_visitbogota_co'
```

Este paso solo afecta a `pantallas_visitbogota_co`. Los demás sitios del
compose no se tocan. La galería es un volumen persistente y no se pierde.

### 5. Verificar

```bash
ssh useridt@10.216.153.78 \
  'curl -sk -o /dev/null -w "%{http_code}\n" -H "Host: pantallas.visitbogota.co" https://localhost/'
```

## Después del deploy

- **Recarga las pantallas a mano.** El polling de `tiempo.php` solo detecta
  cambios de datos, no de código, así que los kioscos siguen con la versión
  vieja hasta que se recargan. También se recargan solos si cambia algo en
  el admin.
- **Comprueba:**
  - Una pantalla con contenido específico vigente reproduce solo ese contenido.
  - Una pantalla sin contenido específico sigue mostrando el protector general
    intercalado con el contenido general.
  - Una pantalla con un protector propio distinto del general lo sigue
    mostrando.
  - El overlay muestra el círculo con la mano y "Toca aquí", sin el título de
    abajo, y con la pastilla del nombre de la pantalla.
