<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '../../config/config.php';
require_once __DIR__ . '../../src/Database/Connection.php';
require_once __DIR__ . '../../src/Models/User.php';
require_once __DIR__ . '../../src/Validation/UserValidator.php';
require_once __DIR__ . '../../src/Repositories/UserRepository.php';
require_once __DIR__ . '../../src/Controllers/UserController.php';

use Src\Controllers\UserController;

try {
    $controller = new UserController();

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $action = $_GET['action'] ?? '';
        if ($action === 'list') {
            echo json_encode($controller->list(), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(['message' => ['error' => true, 'msgError' => 'Ação GET inválida.']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'POST') {
        $action = $_POST['action'] ?? '';
        switch ($action) {
            case 'create':
                echo json_encode($controller->create($_POST), JSON_UNESCAPED_UNICODE);
                break;
            case 'update':
                echo json_encode($controller->update($_POST), JSON_UNESCAPED_UNICODE);
                break;
            case 'delete':
                echo json_encode($controller->delete($_POST), JSON_UNESCAPED_UNICODE);
                break;
            default:
                echo json_encode(['message' => ['error' => true, 'msgError' => 'Ação POST inválida.']], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    echo json_encode(['message' => ['error' => true, 'msgError' => 'Método não suportado.']], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'message' => [
            'error' => true,
            'msgError' => 'Erro no servidor: ' . $e->getMessage()
        ]
    ], JSON_UNESCAPED_UNICODE);
}
