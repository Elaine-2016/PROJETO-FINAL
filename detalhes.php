<?php
session_start();
require 'conexao.php';

// 1. Verificar se o ID do evento foi passado no URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se tentarem aceder a detalhes.php sem um ID, volta para os eventos
    header("Location: eventos.php");
    exit();
}

$id = $_GET['id'];

// 2. Ir à base de dados buscar TODOS os dados deste evento específico
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id' => $id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Se o utilizador escrever um ID que não existe (ex: detalhes.php?id=999)
if (!$evento) {
    header("Location: eventos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">    
    <title>VivaTickets | <?= htmlspecialchars($evento['title']); ?></title>
    <meta name="description" content="Compre bilhetes para <?= htmlspecialchars($evento['title']); ?> em <?= htmlspecialchars($evento['location']); ?>. Reserve já o seu lugar na VivaTickets!">
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

    <main class="container my-5 pt-4">
        
        <a href="eventos.php" class="btn btn-outline-secondary rounded-pill mb-4">
            <i class="fa-solid fa-arrow-left me-2"></i>Voltar aos Eventos
        </a>

        <div class="card shadow-lg border-0 overflow-hidden" style="background-color: #161616; border-radius: 24px;">
            <div class="row g-0">
                
                <div class="col-lg-5 col-xl-6">
                    <?php $img_evt = !empty($evento['image']) ? $evento['image'] : 'default_event.jpg'; ?>
                    <img src="uploads/<?= htmlspecialchars($img_evt); ?>" alt="<?= htmlspecialchars($evento['title']); ?>" 
                         class="img-fluid w-100 h-100" style="object-fit: cover; min-height: 400px; border-right: 1px solid #333;">
                </div>

                <div class="col-lg-7 col-xl-6 d-flex flex-column">
                    <div class="card-body p-4 p-md-5 d-flex flex-column h-100">
                        
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h1 class="text-white fw-bold mb-0" style="font-size: 2.5rem;"><?= htmlspecialchars($evento['title']); ?></h1>
                            <span class="badge bg-dark text-lime border border-secondary fs-4 px-3 py-2 rounded-pill shadow-sm">
                                € <?= number_format($evento['ticket_price'], 2, ',', '.'); ?>
                            </span>
                        </div>

                        <div class="d-flex flex-wrap gap-4 mb-4 pb-4 border-bottom" style="border-color: #333 !important;">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; border: 1px solid #333;">
                                    <i class="fa-regular fa-calendar text-lime fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small text-uppercase fw-bold">Data e Hora</p>
                                    <p class="text-white mb-0 fs-5"><?= date('d/m/Y', strtotime($evento['event_date'])); ?> às <?= date('H:i', strtotime($evento['event_time'])); ?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; border: 1px solid #333;">
                                    <i class="fa-solid fa-location-dot text-lime fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small text-uppercase fw-bold">Localização</p>
                                    <p class="text-white mb-0 fs-5"><?= htmlspecialchars($evento['location']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 flex-grow-1">
                            <h4 class="text-white mb-3">Sobre o Evento</h4>
                            <p style="color: #a0a0a0; line-height: 1.8; font-size: 1.1rem;">
                                <?= nl2br(htmlspecialchars($evento['description'])); ?>
                            </p>
                        </div>

                        <div class="bg-dark p-4 rounded-4 border" style="border-color: #444 !important;">
                            <form action="adicionar_carrinho.php" method="POST" class="row align-items-center g-3">
                                
                                <input type="hidden" name="event_id" value="<?= $evento['id']; ?>">
                                
                                <div class="col-sm-4 col-md-3">
                                    <label class="form-label text-white small fw-bold">Quantidade</label>
                                    <input type="number" name="quantidade" class="form-control bg-dark text-white border-secondary text-center" value="1" min="1" max="10" required>
                                </div>
                                
                                <div class="col-sm-8 col-md-9 d-flex align-items-end">
                                    <button type="submit" class="btn btn-lime w-100 py-2 fs-5 fw-bold rounded-pill">
                                        <i class="fa-solid fa-cart-plus me-2"></i>Adicionar ao Carrinho
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>