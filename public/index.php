<?php
session_start();

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Conexão com banco
require_once __DIR__ . '/../config/db.php';

use App\Controllers\ProdutoController;
use App\Controllers\PedidoController;
use App\Controllers\CupomController;
use App\Controllers\WebhookController;


$page = $_GET['page'] ?? 'produtos';

switch ($page) {
    case 'produtos':
        $controller = new ProdutoController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->index();
        }
        break;

    case 'carrinho':
        $controller = new PedidoController($pdo);
        $action = $_GET['action'] ?? null;
        if ($action === 'add' && isset($_GET['id'])) {
            $controller->add($_GET['id'], 1);
        } else {
            $controller->carrinho();
        }
        break;

    case 'checkout':
        $controller = new PedidoController($pdo);
        $controller->checkout();
        break;

    case 'aplicar-cupom':
        $controller = new CupomController($pdo);
        $controller->aplicar();
        break;

    case 'webhook':
        $controller = new WebhookController($pdo);
        $controller->handle();
        break;

    default:
        echo "Página não encontrada!";
}
