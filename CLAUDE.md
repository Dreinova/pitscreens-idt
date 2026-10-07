# IDT App — pitScreens

Sistema de pantallas interactivas de los Puntos de Información Turística de
Bogotá. Dos módulos PHP independientes (`app/` = kiosco, `admin/` = panel),
ver `pitScreens-docs/` para la documentación completa (arquitectura, base de
datos, flujos, despliegue).

## Mantener la documentación al día

Después de cualquier cambio que agregue, modifique o quite una
funcionalidad, tabla/columna de base de datos, página del admin, flujo del
kiosco o variable de configuración, invoca el subagente **`docs-updater`**
(`.claude/agents/docs-updater.md`) para que sincronice `pitScreens-docs/`
con el cambio — no lo dejes para después ni asumas que alguien más lo hará.
No hace falta para fixes puramente visuales/CSS sin impacto funcional.

## Despliegue a producción

Ver la memoria del proyecto (`deploy-pantallas.md`) para el procedimiento
completo. En resumen: `git push` a `origin` (`github.com/Dreinova/pitscreens-idt`),
luego `git archive HEAD | ssh useridt@10.216.153.78 'tar -x -C
/data/visitbogota.co_proxy/pantallas.visitbogota.co'`, restaurar
`Conexion_DB.php` desde `pantallas.visitbogota.co.secrets/` (no están en
git), y `docker compose build && up` solo del servicio
`pantallas_visitbogota_co`. El acceso SSH es por contraseña, no por llave.
