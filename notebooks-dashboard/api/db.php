<?php
/**
 * Conexão com o MySQL da Hostinger.
 * Credenciais via variáveis de ambiente (getenv) — nunca hardcoded aqui.
 *
 * Como configurar na Hostinger:
 *   copie notebooks-dashboard/.htaccess.example para .htaccess no servidor
 *   e preencha DB_HOST, DB_NAME, DB_USER, DB_PASSWORD (o .htaccess real
 *   fica fora do git).
 *
 * Pra testar local, exporte as variáveis antes de subir o PHP embutido:
 *   export DB_HOST=localhost DB_NAME=notebooks DB_USER=root DB_PASSWORD=...
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: same-origin');

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'SEU_BANCO';
$user = getenv('DB_USER') ?: 'SEU_USUARIO';
$pass = getenv('DB_PASSWORD') ?: 'SUA_SENHA';
$port = getenv('DB_PORT') ?: '3306';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão com o banco de dados']);
    exit;
}

/**
 * Monta a cláusula WHERE comum a todos os endpoints a partir da querystring:
 *   ?marca=Acer&termo=vivobook&de=2024-01-01&ate=2024-06-01
 * Retorna [sql, params] pra usar com bindValue/execute.
 */
function montarFiltros(array $get): array
{
    $where  = [];
    $params = [];

    if (!empty($get['marca']) && $get['marca'] !== 'todas') {
        $where[] = 'marca = :marca';
        $params[':marca'] = $get['marca'];
    }
    if (!empty($get['termo'])) {
        $where[] = 'nome_computador LIKE :termo';
        $params[':termo'] = '%' . $get['termo'] . '%';
    }
    if (!empty($get['de'])) {
        $where[] = 'dia >= :de';
        $params[':de'] = $get['de'];
    }
    if (!empty($get['ate'])) {
        $where[] = 'dia <= :ate';
        $params[':ate'] = $get['ate'];
    }

    $sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    return [$sql, $params];
}
