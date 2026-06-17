<?php
session_start();
require 'conexao.php';

// Ir buscar os 3 próximos eventos
$sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
$stmt = $pdo->query($sql);
$eventos_destaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Gestão de Eventos</title>
    
    <meta name="description" content="VivaTickets: A sua plataforma de eleição para comprar bilhetes online. Descubra os melhores concertos, festivais e eventos imperdíveis.">
    <meta name="keywords" content="comprar bilhetes, venda de ingressos, bilheteira online, VivaTickets, concertos, festivais">  
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="VivaTickets | A tua Bilheteira Online">
    <meta property="og:description" content="Garanta os seus bilhetes para os melhores eventos ao vivo.">
    <meta property="og:image" content="https://teusite.com/imagens/banner_geral.jpg"> 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>   
       
    <div class="text-center py-5 mt-4">
        <h1 class="display-4 fw-bold">Bem-vinda ao Viva<span class="text-lime">Tickets</span></h1>
        <p class="lead mt-3" style="color: #a0a0a0;">Os melhores eventos ao vivo, à distância de um clique.</p>
    </div>

    <header class="container mt-4 mb-5">
        <div id="eventosVideoCarousel" class="carousel slide shadow-lg" data-bs-ride="carousel" style="border-radius: 24px; overflow: hidden; border: 1px solid #333;">
            
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#eventosVideoCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#eventosVideoCarousel" data-bs-slide-to="1"></button>
            </div>

            <div class="carousel-inner">
                
                <div class="carousel-item active">
                    <video class="w-100 d-block" autoplay loop muted playsinline style="height: 60vh; object-fit: cover; filter: brightness(0.5);">
                        <source src="videos/videoplayback_djavan.mp4" type="video/mp4">
                    </video>
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100" style="bottom: 0;">
                        <h1 class="display-3 fw-bold text-white mb-3">Sente a Energia do <span class="text-lime">Ao Vivo</span></h1>
                        <p class="lead text-light mb-4 fs-4">Os maiores festivais de música já estão na VivaTickets.</p>
                        <div><a href="eventos.php" class="btn btn-lime px-5 py-3 rounded-pill fw-bold fs-5 shadow">Explorar Catálogo</a></div>
                    </div>
                </div>

                <div class="carousel-item">
                    <video class="w-100 d-block" autoplay loop muted playsinline style="height: 60vh; object-fit: cover; filter: brightness(0.5);">
                        <source src="videos/videoplayback_carmen.mp4" type="video/mp4">
                    </video>
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100" style="bottom: 0;">
                        <h1 class="display-3 fw-bold text-white mb-3">O Palco é <span class="text-lime">Tudo</span></h1>
                        <p class="lead text-light mb-4 fs-4">Descobre peças de teatro e espetáculos imperdíveis.</p>
                        <div><a href="#destaques" class="btn btn-outline-lime px-5 py-3 rounded-pill fw-bold fs-5">Ver Agenda</a></div>
                    </div>
                </div>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#eventosVideoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#eventosVideoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>
    </header>

    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ps-3 text-white m-0" style="border-left: 4px solid #e0ff4f;">Teasers em Destaque</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="ratio ratio-16x9 shadow-lg" style="border-radius: 16px; overflow: hidden; border: 1px solid #333;">
                    <iframe src="https://www.youtube.com/embed/il37Wz01YlA?si=Y3uBHLvm0JsMq7nt" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="ratio ratio-16x9 shadow-lg" style="border-radius: 16px; overflow: hidden; border: 1px solid #333;">
                    <iframe src="https://www.youtube.com/embed/ZSZyth38-zk?si=RDhH2fARySjjA0fW" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>
    
    <main id="destaques" class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ps-3 text-white m-0" style="border-left: 4px solid #e0ff4f;">Eventos em Destaque</h2>
            <a href="eventos.php" class="btn btn-outline-lime rounded-pill">Ver Todos</a>
        </div>
        
        <div class="row g-4">
            <?php if (count($eventos_destaque) > 0): ?>
                <?php foreach ($eventos_destaque as $evento): ?>
                    <div class="col-md-4">
                        <div class="feature-card d-flex flex-column h-100">
                            
                            <?php $img_evt = !empty($evento['image']) ? $evento['image'] : 'default_event.jpg'; ?>
                            <img src="uploads/<?= htmlspecialchars($img_evt); ?>" alt="<?= htmlspecialchars($evento['title']); ?>" style="width: 100%; height: 220px; object-fit: cover; border-bottom: 1px solid #333;">
                            
                            <div class="feature-card-content">
                                <h3 class="text-lime fs-4 mb-2"><?= htmlspecialchars($evento['title']); ?></h3>
                                
                                <p class="mb-3" style="color: #a0a0a0; font-size: 15px; line-height: 1.5;">
                                    <?= htmlspecialchars(mb_strimwidth($evento['description'], 0, 90, "...")); ?>
                                </p>
                                
                                <div class="mb-3 mt-auto" style="color: #a0a0a0; font-size: 14px;">
                                    <p class="mb-1"><i class="fa-solid fa-calendar-day me-2"></i><?= date('d/m/Y', strtotime($evento['event_date'])); ?> às <?= date('H:i', strtotime($evento['event_time'])); ?></p>
                                    <p class="mb-1"><i class="fa-solid fa-location-dot me-2"></i><?= htmlspecialchars($evento['location']); ?></p>
                                </div>
                                
                                <div class="pt-3 border-top" style="border-color: #333 !important;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-4 fw-bold text-white">€ <?= number_format($evento['ticket_price'], 2, ',', '.'); ?></span>
                                        <a href="detalhes.php?id=<?= $evento['id']; ?>" class="btn btn-sm btn-lime px-4 rounded-pill fw-bold">Comprar</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="feature-card text-center py-5" style="border: 1px dashed #333; padding: 50px !important;">
                        <p class="fs-5 mb-0 text-muted">Ainda não existem eventos agendados. Fica atenta!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>