<?php
require __DIR__ . '/db.php';

[$whereSql, $params] = montarFiltros($_GET);

$sql = "
    SELECT
        marca,
        MIN(preco) AS preco_minimo,
        MAX(preco) AS preco_maximo,
        AVG(preco) AS preco_medio,
        AVG(desconto) AS desconto_medio,
        COUNT(*) AS total_anuncios
    FROM notebooks_geral
    {$whereSql}
    GROUP BY marca
    ORDER BY preco_medio DESC
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
    echo json_encode(['error' => 'Erro ao calcular ranking']);
}
