<?php
session_start();
require 'conexao.php';

// Proteção máxima: Apenas administradores
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. PROCESSAR A ATUALIZAÇÃO (Quando o formulário é submetido)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_user'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    try {
        $sql = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);
        
        // Redireciona com sucesso
        header("Location: admin_dashboard.php?sucesso=1");
        exit();
    } catch (PDOException $e) {
        die("Erro ao atualizar utilizador: " . $e->getMessage());
    }
}

// 2. BUSCAR OS DADOS DO UTILIZADOR PARA PREENCHER O FORMULÁRIO
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT id, name, email, role, profile_pic FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user_edit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_edit) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Editar Utilizador</title>
    
    <meta name="description" content="Área reservada à administração da plataforma VivaTickets.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="VivaTickets | Administração">
    <meta property="og:description" content="Gestão de utilizadores da plataforma.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white m-0" style="border-left: 4px solid #e0ff4f; padding-left: 15px;">Editar Utilizador</h2>
                    <a href="admin_dashboard.php" class="btn btn-outline-secondary rounded-pill">Voltar</a>
                </div>

                <div class="card shadow-lg border-0" data-bs-theme="dark" style="background-color: #161616; border-radius: 24px;">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <?php $foto = !empty($user_edit['profile_pic']) ? $user_edit['profile_pic'] : 'default_avatar.png'; ?>
                            <img src="uploads/<?= htmlspecialchars($foto); ?>" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #333;">
                        </div>

                        <form action="editar_utilizador.php?id=<?= $user_edit['id']; ?>" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="id" value="<?= $user_edit['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Nome Completo</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user_edit['name']); ?>" required>
                                <div class="invalid-feedback">
                                    Por favor, insere o nome do utilizador.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_edit['email']); ?>" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required>
                                <div class="invalid-feedback">
                                    Por favor, insere um email válido (ex: nome@dominio.pt).
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Nível de Permissão</label>
                                <select name="role" class="form-select bg-dark text-white border-secondary">
                                    <option value="user" <?= $user_edit['role'] === 'user' ? 'selected' : ''; ?>>Utilizador Normal (User)</option>
                                    <option value="admin" <?= $user_edit['role'] === 'admin' ? 'selected' : ''; ?>>Administrador (Admin)</option>
                                </select>
                                <?php if ($user_edit['id'] === $_SESSION['user_id']): ?>
                                    <small class="text-warning mt-2 d-block"><i class="fa-solid fa-triangle-exclamation me-1"></i>Atenção: Estás a editar o teu próprio nível de permissão.</small>
                                <?php endif; ?>
                            </div>

                            <button type="submit" name="atualizar_user" class="btn btn-lime w-100 fw-bold">Guardar Alterações</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>