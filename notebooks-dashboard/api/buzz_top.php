<?php
require __DIR__ . '/db.php';

/**
 * Estas duas tabelas foram coletadas por termo de busca ("notebook barato",
 * "notebook") e não por marca, então aqui mostramos o buzz geral do mercado,
 * não segmentado por marca.
 */
try {
    $tiktok = $pdo->query("
        SELECT nome_usuario, MAX(total_visualizacoes) AS total_visualizacoes, link_post,
               MAX(descricao_post) AS descricao_post, MAX(data_postagem) AS data_postagem
        FROM tiktok_posts
        GROUP BY link_post
        ORDER BY total_visualizacoes DESC
        LIMIT 5
    ")->fetchAll();

    $youtube = $pdo->query("
        SELECT nome_usuario, MAX(total_visualizacoes) AS total_visualizacoes, link_post,
               MAX(titulo_post) AS titulo_post, MAX(data_postagem) AS data_postagem
        FROM youtube_posts
        GROUP BY link_post
        ORDER BY total_visualizacoes DESC
        LIMIT 5
    ")->fetchAll();

    echo json_encode(['tiktok' => $tiktok, 'youtube' => $youtube]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar posts em destaque']);
}
