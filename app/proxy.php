<?php
/**
 * proxy.php — Módulo App (Pantalla)
 *
 * Proxy HTTP para cargar URLs externas dentro de un iframe sin restricciones
 * de política de mismo origen (CORS / X-Frame-Options).
 *
 * Muchas páginas web modernas bloquean ser incrustadas en iframes mediante
 * la cabecera X-Frame-Options o Content-Security-Policy. Este proxy actúa
 * como intermediario: el servidor PHP descarga el contenido de la URL remota
 * usando cURL y lo sirve directamente al navegador del kiosco.
 *
 * Parámetros GET:
 *   url (string) — URL externa codificada con urlencode() que se desea cargar
 *
 * Proceso:
 *   1. Valida que la URL sea válida usando FILTER_VALIDATE_URL.
 *   2. Descarga el contenido remoto con cURL siguiendo redirecciones.
 *   3. Si la respuesta es HTML, reescribe rutas relativas a absolutas
 *      para que los recursos (imágenes, CSS, JS) carguen correctamente.
 *   4. Reenvía el Content-Type original y muestra la respuesta.
 *
 * Nota de seguridad: CURLOPT_SSL_VERIFYPEER está desactivado para
 * permitir sitios con certificados autofirmados en entorno local/intranet.
 */

if (!isset($_GET['url'])) {
    die("URL no especificada");
}

$url = $_GET['url'];

// Validar que la URL tenga un formato válido antes de hacer la petición
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    die("URL no válida");
}

// Inicializar sesión cURL para hacer la petición HTTP al sitio externo
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,            $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retornar respuesta como string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Seguir redirecciones automáticamente
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // No verificar certificado SSL (entorno local)
$response    = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE); // Obtener tipo de contenido real
curl_close($ch);

// Reenviar el Content-Type original de la respuesta remota
if ($contentType) {
    header("Content-Type: " . $contentType);
}

// Si la respuesta es HTML, reescribir rutas relativas a absolutas
// para que recursos como imágenes, CSS y JS carguen correctamente
if (strpos($contentType, "text/html") !== false) {
    $base     = parse_url($url);
    $base_url = $base["scheme"] . "://" . $base["host"];

    // Reemplaza src="/ruta" y href="/ruta" por sus equivalentes absolutos
    $response = preg_replace(
        '/(src|href)=[\'"]\/([^\'"]+)[\'"]/',
        '$1="' . $base_url . '/$2"',
        $response
    );
}

// Enviar el contenido remoto al navegador del kiosco
echo $response;
