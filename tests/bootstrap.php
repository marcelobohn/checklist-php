<?php
// Bootstrap da suíte de testes funcionais.
//
// Os testes batem no app REAL (dockerizado) via HTTP. Antes de tudo,
// verifica se ele está acessível para dar uma mensagem clara caso não esteja.

require __DIR__ . '/../vendor/autoload.php';

$baseUrl = getenv('BASE_URL') ?: 'http://localhost/checklist';

$ch = curl_init($baseUrl . '/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_NOBODY         => true,
    CURLOPT_TIMEOUT        => 5,
]);
curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code === 0) {
    fwrite(STDERR, "\n[ERRO] App não acessível em {$baseUrl}\n");
    fwrite(STDERR, "Suba o ambiente antes de testar:  docker compose up -d --build\n\n");
    exit(1);
}
