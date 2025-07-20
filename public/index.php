<?php

use App\Controllers\EstoqueController;
use App\Controllers\VariacaoController;

session_start();

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Conexão com banco
require_once __DIR__ . '/../config/db.php';

use App\Services\JobRunner;
use App\Controllers\ProdutoController;
use App\Controllers\PedidoController;
use App\Controllers\CupomController;
use App\Controllers\WebhookController;

// Registra a conexão no JobRunner para todos os jobs
JobRunner::setPDO($pdo);

$page = $_GET['page'] ?? 'produtos';

$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'GET') {
    header('Content-Type: application/json');
}

$bodyHttpInput = json_decode(file_get_contents('php://input'), true);

switch ($page) {
    case 'produtos':
        $controller = new ProdutoController($pdo);
        if ($method === 'POST') {
            $controller->store($bodyHttpInput);
        } elseif ($method === 'PUT') {
            $controller->update($bodyHttpInput);
        } elseif ($method === 'DELETE') {
            $controller->delete($bodyHttpInput);
        } else {
            $controller->index();
        }
        break;

    case 'carrinho':
        $controller = new PedidoController($pdo);
        $action = $_GET['action'] ?? null;
        if ($action === 'add' && isset($_GET['id']) && isset($_GET['variacao'])) {
            $controller->add($_GET['id'], $_GET['variacao'], 1);
        } else if ($action === 'remove' && isset($_GET['id']) && isset($_GET['variacao'])) {
            $controller->remove($_GET['id'], $_GET['variacao']);
        } else if ($action === 'checkout') {
            $controller->checkout();
        } else {
            $controller->carrinho();
        }
        break;

    case 'aplicar-cupom':
        $controller = new CupomController($pdo);
        $controller->aplicar();
        break;

    case 'webhook':
        $controller = new WebhookController($pdo);
        $controller->handle();
        break;

    case 'variacao':
        $controller = new VariacaoController($pdo);
        $type = $bodyHttpInput['type'] ?? null;
        if ($method === 'POST') {
            if ($type === 'variacao') {
                $controller->storeVariacao();
            } elseif ($type === 'grupo') {
                $controller->storeGrupoVariacao();
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de requisição inválido.']);
            }
        } elseif ($method === 'PUT') {
            if ($type === 'variacao') {
                $controller->updateVariacao();
            } elseif ($type === 'grupo') {
                $controller->updateGrupoVariacao();
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de requisição inválido.']);
            }
        } elseif ($method === 'DELETE') {
            if ($type === 'variacao') {
                $controller->deleteVariacao();
            } elseif ($type === 'grupo') {
                $controller->deleteGrupoVariacao();
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de requisição inválido.']);
            }
        } else {
            $controller->index();
        }
        break;

    case 'estoque':
        $controller = new EstoqueController($pdo);
        $controller->create($bodyHttpInput);
        break;

    default:
        echo "Página não encontrada!";
}
