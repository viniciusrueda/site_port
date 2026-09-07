<?php
require __DIR__ . '/db.php';

/**
 * As tabelas de mídia social guardam a marca como "Acer Brasil", "Asus Brasil"
 * etc, diferente de notebooks_geral que guarda só "Acer", "Asus". Normalizamos
 * pegando a primeira palavra, já que todas as marcas aqui são de uma palavra só.
 */
try {
    $sqlInstagram = "
        SELECT
            SUBSTRING_INDEX(marca, ' ', 1) AS marca,
            SUM(likes) AS total_likes,
            SUM(comments) AS total_comments,
            COUNT(*) AS total_posts,
            AVG(engagement) AS engajamento_medio
        FROM instagram_posts
        GROUP BY SUBSTRING_INDEX(marca, ' ', 1)
    ";
    $porMarca = [];
    foreach ($pdo->query($sqlInstagram) as $row) {
        $porMarca[$row['marca']] = [
            'marca'             => $row['marca'],
            'seguidores'        => null,
            'total_likes'       => (int) $row['total_likes'],
            'total_comments'    => (int) $row['total_comments'],
            'total_posts'       => (int) $row['total_posts'],
            'engajamento_medio' => round((float) $row['engajamento_medio'], 6),
        ];
    }

    $sqlSeguidores = "
        SELECT SUBSTRING_INDEX(s1.marca, ' ', 1) AS marca, s1.total_seguidores
        FROM seguidores s1
        INNER JOIN (
            SELECT marca, MAX(dia) AS max_dia FROM seguidores GROUP BY marca
        ) s2 ON s1.marca = s2.marca AND s1.dia = s2.max_dia
    ";
    foreach ($pdo->query($sqlSeguidores) as $row) {
        $marca = $row['marca'];
        if (!isset($porMarca[$marca])) {
            $porMarca[$marca] = [
                'marca' => $marca, 'seguidores' => null, 'total_likes' => 0,
                'total_comments' => 0, 'total_posts' => 0, 'engajamento_medio' => 0,
            ];
        }
        $porMarca[$marca]['seguidores'] = (int) $row['total_seguidores'];
    }

    echo json_encode(array_values($porMarca));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao calcular presença social']);
}
