<?php
session_start();
require 'conexao.php';

// Verifica se veio por POST e se está logado
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name']);
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_new_password'];

// Validação de segurança: Nome não pode ser apenas espaços em branco
if (empty($name)) {
    header("Location: editar_perfil.php?erro=nome_invalido");
    exit();
}

// 1. Resgatar a foto antiga caso o utilizador não envie uma nova
$stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_pic = $user['profile_pic'];

// 2. Processar a Nova Foto de Perfil (se foi enviada)
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $upload_dir = __DIR__ . '/uploads/';
    $file_name = time() . '_' . basename($_FILES['profile_pic']['name']);
    
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $file_name)) {
        $profile_pic = $file_name;
    }
}

// 3. Preparar a atualização da base de dados
$update_sql = "UPDATE users SET name = :name, profile_pic = :profile_pic";
$params = [
    ':name' => $name,
    ':profile_pic' => $profile_pic,
    ':id' => $user_id
];

// 4. Validação e encriptação da Nova Password
if (!empty($new_password)) {
    
    // Dupla validação do Back-end
    if (strlen($new_password) < 6) {
        header("Location: editar_perfil.php?erro=password_curta");
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        header("Location: editar_perfil.php?erro=passwords_diferentes");
        exit();
    }
    
    // Encripta a nova password
    $update_sql .= ", password = :password";
    $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
}

// Fechar a Query com a cláusula WHERE
$update_sql .= " WHERE id = :id";

// 5. Executar a atualização
try {
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute($params);

    // Atualizar as Sessões para a Navbar mudar imediatamente
    $_SESSION['user_name'] = $name;
    $_SESSION['profile_pic'] = $profile_pic;

    // Redirecionar para o perfil com mensagem de sucesso
    header("Location: perfil.php?sucesso=1");
    exit();

} catch (PDOException $e) {
    header("Location: editar_perfil.php?erro=db_error");
    exit();
}
?>