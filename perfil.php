<?php
session_start();
require 'conexao.php';

// Proteção de Rota: Se não estiver logado, expulsa para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login_registo.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Buscar os dados atualizados do utilizador
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Buscar o histórico de compras cruzando com os dados do evento
$sql_compras = "SELECT p.quantity, p.total_price, p.status, p.purchase_date, e.title, e.event_date 
                FROM purchases p 
                JOIN events e ON p.event_id = e.id 
                WHERE p.user_id = :user_id 
                ORDER BY p.purchase_date DESC";
$stmt_compras = $pdo->prepare($sql_compras);
$stmt_compras->execute([':user_id' => $user_id]);
$compras = $stmt_compras->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | O Meu Perfil</title>
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

    <main class="container my-5">
        
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'compra'): ?>
            <div class="alert alert-success bg-dark text-success border border-success rounded-3 shadow-sm mb-4">
                <i class="fa-solid fa-party-horn me-2"></i> <strong>Parabéns!</strong> A tua compra foi registada com sucesso. Vê os detalhes abaixo.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div class="alert alert-success bg-dark text-success border border-success rounded-3 shadow-sm mb-4">
                <i class="fa-solid fa-circle-check me-2"></i> Perfil atualizado com sucesso!
            </div>
        <?php endif; ?>

        <div class="row g-5">
            
            <div class="col-lg-4">
                <div class="feature-card text-center text-white p-4">
                    <?php 
                        $foto = !empty($user['profile_pic']) ? $user['profile_pic'] : 'default_avatar.png';
                    ?>
                    <img src="uploads/<?= htmlspecialchars($foto); ?>" alt="Foto de Perfil" class="rounded-circle mb-4 shadow" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #e0ff4f;">
                    
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($user['name']); ?></h3>
                    <p style="color: #a0a0a0;" class="mb-4"><i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($user['email']); ?></p>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="editar_perfil.php" class="btn btn-lime w-100"><i class="fa-solid fa-pen me-2"></i>Editar Perfil</a>
                    </div>
                    
                    <hr style="border-color: #333;" class="my-4">
                    
                    <div class="text-start">
                        <p class="mb-1"><strong class="text-lime">Membro desde:</strong></p>
                        <p style="color: #a0a0a0;"><?= date('d/m/Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <h2 class="mb-4 ps-3 text-white" style="border-left: 4px solid #e0ff4f;">Histórico de Compras</h2>
                
                <?php if (count($compras) > 0): ?>
                    <div class="table-responsive feature-card p-0 overflow-hidden">
                        <table class="table table-dark table-hover mb-0 align-middle" style="background-color: transparent;">
                            <thead style="background-color: #222;">
                                <tr>
                                    <th class="py-3 px-4 text-lime border-bottom border-dark">Evento</th>
                                    <th class="py-3 px-4 text-lime border-bottom border-dark">Data</th>
                                    <th class="py-3 px-4 text-lime border-bottom border-dark text-center">Bilhetes</th>
                                    <th class="py-3 px-4 text-lime border-bottom border-dark text-center">Total</th>
                                    <th class="py-3 px-4 text-lime border-bottom border-dark text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($compras as $compra): ?>
                                    <tr>
                                        <td class="py-3 px-4 fw-bold text-white"><?= htmlspecialchars($compra['title']); ?></td>
                                        <td class="py-3 px-4 text-muted"><?= date('d/m/Y', strtotime($compra['event_date'])); ?></td>
                                        <td class="py-3 px-4 text-center text-white"><?= $compra['quantity']; ?></td>
                                        <td class="py-3 px-4 text-center text-white">€ <?= number_format($compra['total_price'], 2, ',', '.'); ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <?php if ($compra['status'] === 'concluida'): ?>
                                                <span class="badge bg-success rounded-pill px-3">Concluída</span>
                                            <?php elseif ($compra['status'] === 'pendente'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3">Pendente</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill px-3">Cancelada</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="feature-card text-center py-5" style="border: 1px dashed #333;">
                        <div class="icon mb-3"><i class="fa-solid fa-ticket-simple text-muted" style="font-size: 3rem;"></i></div>
                        <h4 class="text-white">Ainda não tens bilhetes</h4>
                        <p style="color: #a0a0a0;" class="mb-4">O teu histórico de compras está vazio. Explora os nossos eventos e garante o teu lugar!</p>
                        <a href="eventos.php" class="btn btn-outline-lime">Ver Eventos</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>