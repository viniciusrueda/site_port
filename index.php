<?php
session_start();

// Autoload do Composer para PHPMailer e outras dependências
require __DIR__ . '/vendor/autoload.php';

// Roteamento básico
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($request_uri) {
    case '/':
        include 'index.html';
        break;
    case '/automacao':
        include 'automacao.html';
        break;
    case '/dashboard':
        include 'dashboard.html';
        break;
    case '/contato':
        include 'contato.html';
        break;
    case '/download_curriculo':
        downloadCurriculo();
        break;
    default:
        http_response_code(404);
        echo "Página não encontrada.";
        break;
}

// Função para baixar o currículo
function downloadCurriculo() {
    $file = 'static/CV - Vinicius Rueda Lopes.pdf';
    if (file_exists($file)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="CV - Vinicius Rueda Lopes.pdf"');
        readfile($file);
    } else {
        http_response_code(404);
        echo "Arquivo não encontrado.";
    }
}
?>
