<?php
require __DIR__ . '/db.php';

[$whereSql, $params] = montarFiltros($_GET);

$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 50;
$offset = ($pagina - 1) * $porPagina;

$sql = "
    SELECT
        nome_computador,
        marca,
        preco,
        desconto,
        dia
    FROM notebooks_geral
    {$whereSql}
    ORDER BY dia DESC
    LIMIT :limite OFFSET :offset
";

try {
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar produtos']);
}
