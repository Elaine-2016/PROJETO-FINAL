<?php
// Iniciar a sessão para podermos salvar os dados do utilizador logado
session_start();
require 'conexao.php'; // Conexão com o banco de dados vivatickets

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Procurar o utilizador pelo email na tabela 'users'
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificar se o utilizador foi encontrado e se a password coincide com o hash
    if ($user && password_verify($password, $user['password'])) {
        
        // Sucesso! Guardamos as variáveis de sessão necessárias para a Navbar e permissões
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role']; // 'admin' ou 'user'
        // NOVA LINHA: Guardar o nome do ficheiro da foto na sessão
        $_SESSION['profile_pic'] = $user['profile_pic'];
        
        // Redireciona o utilizador para a página inicial já autenticado
        header("Location: index.php");
        exit();
    } else {
        // Mensagem simples de erro caso falhe
        echo "Email ou password incorretos. <a href='login_registo.php'>Tente novamente</a>";
    }
}
?>