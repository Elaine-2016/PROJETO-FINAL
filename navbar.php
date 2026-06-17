<?php
// Garantir que a sessão está iniciada para podermos verificar o utilizador
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #161616; border-bottom: 1px solid #222;">
  <div class="container">
    
    <a class="navbar-brand fw-bold fs-4" href="index.php">Viva<span class="text-lime">Tickets</span></a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
        <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
        <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
                
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    <?php 
                        // Verifica se tem foto na sessão, senão usa a imagem padrão
                        $foto = !empty($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default_avatar.png';
                    ?>
                    <img src="uploads/<?= htmlspecialchars($foto); ?>" alt="Foto de Perfil" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #e0ff4f;">
                    
                    Olá, <?= htmlspecialchars($_SESSION['user_name']); ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" style="background-color: #1a1a1a; border: 1px solid #333;">
                    <li><a class="dropdown-item" href="perfil.php">Meu Perfil</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a class="dropdown-item text-lime fw-bold" href="admin_dashboard.php">Painel Admin</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider" style="border-color: #333;"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link btn btn-outline-lime ms-lg-3 px-4 py-2" href="login_registo.php">Login / Registo</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>