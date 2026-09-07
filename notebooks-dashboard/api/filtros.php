<?php
require __DIR__ . '/db.php';

try {
    $marcas = $pdo->query(
        'SELECT DISTINCT marca FROM notebooks_geral ORDER BY marca ASC'
    )->fetchAll(PDO::FETCH_COLUMN);

    $periodo = $pdo->query(
        'SELECT MIN(dia) AS de, MAX(dia) AS ate FROM notebooks_geral'
    )->fetch();

    echo json_encode([
        'marcas'  => $marcas,
        'periodo' => $periodo,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar filtros']);
}
