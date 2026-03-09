<?php
if (!isset($_GET['url'])) {
    die("URL no especificada");
}

$url = $_GET['url'];

// Validar URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    die("URL no válida");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($contentType) {
    header("Content-Type: " . $contentType);
}

// Si es HTML, reescribimos rutas
if (strpos($contentType, "text/html") !== false) {
    $base = parse_url($url);
    $base_url = $base["scheme"] . "://" . $base["host"];
    
    // Reemplazar rutas relativas en src y href
    $response = preg_replace(
        '/(src|href)=[\'"]\/([^\'"]+)[\'"]/',
        '$1="' . $base_url . '/$2"',
        $response
    );
}

echo $response;
