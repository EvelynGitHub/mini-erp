<?php

function renderizarView(string $viewPath, ?string $layoutPath = null, array $dados = [])
{
    if (is_null($layoutPath))
        $layoutPath = __DIR__ . '/../app/Views/templates/layout.php';// '/views/layout.php';

    // $layoutPath = __DIR__ . '/../../public/views/layout.php';// '/views/layout.php';

    extract($dados); // torna cada chave do array uma variável
    ob_start();
    include $viewPath;
    $PAGE_CONTENT = ob_get_clean();

    if ($layoutPath) {
        // Define $PAGE_CONTENT como variável usada no layout
        ob_start();
        include $layoutPath;
        $finalOutput = ob_get_clean();
    } else {
        $finalOutput = $PAGE_CONTENT;
    }

    echo $finalOutput;

    // $response->getBody()->write($finalOutput);
    // return $response->withHeader('Content-Type', 'text/html');
}