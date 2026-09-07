<?php
if (!isset($_GET['tipo']) || empty($_GET['tipo'])) {
    echo json_encode(["erro" => "Tipo de código não informado!"]);
    exit;
}

$tipo = htmlspecialchars($_GET['tipo']); // Sanitiza a entrada
$api_url = "https://web-production-f2068.up.railway.app/executar_codigo?tipo=" . urlencode($tipo); // Nova URL da API no Railway

// Redireciona para o link do arquivo gerado pela API
header("Location: " . $api_url);
exit;
?>


