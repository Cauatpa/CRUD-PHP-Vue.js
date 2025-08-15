<?php
header('Content-Type: application/json');
require_once __DIR__ . '/conexao.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$response = [
    "message" => [
        "error" => true,
        "msgError" => "Ação inválida"
    ]
];

try {
    if ($action === "list") {
        $sql = "SELECT id, name, email, phone FROM crud ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();

        $response = [
            "message" => ["error" => false],
            "users"   => $users
        ];
    } elseif ($action === "create") {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '' || $phone === '') {
            $response["message"]["msgError"] = "Preencha todos os campos.";
        } else {
            $sql = "INSERT INTO crud (name, email, phone) VALUES (:name, :email, :phone)";
            $stmt = $conn->prepare($sql);
            $ok = $stmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);

            if ($ok) {
                $response = [
                    "message" => [
                        "error" => false,
                        "msgSucces" => "Usuário salvo com sucesso."
                    ]
                ];
            } else {
                $response["message"]["msgError"] = "Erro ao salvar usuário.";
            }
        }
    } elseif ($action === "update") {
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($id <= 0 || $name === '' || $email === '' || $phone === '') {
            $response["message"]["msgError"] = "Preencha todos os campos.";
        } else {
            $sql = "UPDATE crud SET name = :name, email = :email, phone = :phone WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $ok = $stmt->execute([
                ':id'    => $id,
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);

            if ($ok) {
                $response = [
                    "message" => [
                        "error" => false,
                        "msgSucces" => "Usuário atualizado com sucesso."
                    ]
                ];
            } else {
                $response["message"]["msgError"] = "Erro ao atualizar usuário.";
            }
        }
    } elseif ($action === "delete") {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $response["message"]["msgError"] = "ID inválido para exclusão.";
        } else {
            $sql = "DELETE FROM crud WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $ok = $stmt->execute([':id' => $id]);

            if ($ok) {
                $response = [
                    "message" => [
                        "error" => false,
                        "msgSucces" => "Usuário excluído com sucesso."
                    ]
                ];
            } else {
                $response["message"]["msgError"] = "Erro ao excluir usuário.";
            }
        }
    }
} catch (PDOException $e) {
    $response["message"]["msgError"] = "Erro no servidor: " . $e->getMessage();
}

echo json_encode($response);
