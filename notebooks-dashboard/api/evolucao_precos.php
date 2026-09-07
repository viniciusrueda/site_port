<?php
require __DIR__ . '/db.php';

[$whereSql, $params] = montarFiltros($_GET);

$sql = "
    SELECT
        dia,
        AVG(preco) AS preco_medio,
        COUNT(*) AS total_anuncios
    FROM notebooks_geral
    {$whereSql}
    GROUP BY dia
    HAVING COUNT(*) >= 3
    ORDER BY dia ASC
";

try {
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao calcular evolução de preços']);
}