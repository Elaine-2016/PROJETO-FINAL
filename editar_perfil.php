<?php
session_start();
require 'conexao.php';

// Proteção de Rota
if (!isset($_SESSION['user_id'])) {
    header("Location: login_registo.php");
    exit();
}

// Buscar os dados atuais do utilizador
$stmt = $pdo->prepare("SELECT name, email, profile_pic FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Editar Perfil</title>
    <meta name="description" content="VivaTickets: A sua plataforma de eleição para comprar bilhetes online. Descubra os melhores concertos, festivais e eventos imperdíveis. Rápido, seguro e intuitivo. Garanta o seu lugar agora!">
    <meta name="keywords" content="comprar bilhetes, venda de ingressos, bilheteira online, VivaTickets, concertos, festivais, eventos, agenda de eventos, reservar lugares, entretenimento, bilhetes online">  
    <meta property="og:type" content="website">
    <meta property="og:title" content="VivaTickets | <?= htmlspecialchars($evento['title'] ?? 'Bilheteira Online'); ?>">
    <meta property="og:description" content="Garanta os seus bilhetes para os melhores eventos.">
    <meta property="og:image" content="https://teusite.com/uploads/<?= htmlspecialchars($evento['image'] ?? 'default_event.jpg'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white m-0" style="border-left: 4px solid #e0ff4f; padding-left: 15px;">Editar Perfil</h2>
                    <a href="perfil.php" class="btn btn-outline-secondary rounded-pill">Voltar</a>
                </div>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <?php 
                            if ($_GET['erro'] == 'passwords_diferentes') echo "As novas passwords não coincidem.";
                            else echo "Ocorreu um erro ao atualizar o perfil.";
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg border-0" data-bs-theme="dark" style="background-color: #161616; border-radius: 24px;">
                    <div class="card-body p-5">
                        
                        <form action="processar_edicao.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            
                            <div class="text-center mb-4">
                                <?php $foto = !empty($user['profile_pic']) ? $user['profile_pic'] : 'default_avatar.png'; ?>
                                <img src="uploads/<?= htmlspecialchars($foto); ?>" class="rounded-circle mb-3 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border-color: #333 !important;">
                                <input type="file" name="profile_pic" class="form-control form-control-sm bg-dark text-white border-secondary" accept="image/*">
                                <small class="text-muted mt-2 d-block">Carrega uma nova imagem para alterar (Opcional).</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Nome Completo</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                                <div class="invalid-feedback">
                                    Por favor, insere o teu nome completo.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted">Email <i class="fa-solid fa-lock ms-1" style="font-size: 12px;"></i></label>
                                <input type="email" class="form-control text-muted bg-dark border-secondary" value="<?= htmlspecialchars($user['email']); ?>" disabled>
                                <small class="text-muted">O email não pode ser alterado por motivos de segurança.</small>
                            </div>

                            <hr style="border-color: #333;" class="my-4">
                            <h5 class="text-lime mb-3">Alterar Password</h5>

                            <div class="mb-3">
                                <label class="form-label text-white">Nova Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Deixa em branco para manter a atual" minlength="6">
                                <div class="invalid-feedback">
                                    A nova password tem de ter pelo menos 6 caracteres.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Confirmar Nova Password</label>
                                <input type="password" name="confirm_new_password" class="form-control" placeholder="Repete a nova password" minlength="6">
                                <div class="invalid-feedback">
                                    A confirmação tem de ter pelo menos 6 caracteres.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-lime w-100 fw-bold">Guardar Alterações</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ativa a validação visual do Bootstrap
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