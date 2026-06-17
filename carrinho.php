<?php
session_start();
require 'conexao.php';

$carrinho_items = [];
$total_carrinho = 0;

// 1. Verificar se o carrinho existe e tem itens
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    
    // Extrair os IDs dos eventos que estão na sessão e garantir que são números inteiros (Segurança)
    $ids = array_map('intval', array_keys($_SESSION['carrinho']));
    $ids_string = implode(',', $ids);
    
    // Buscar os dados apenas dos eventos que estão no carrinho
    $sql = "SELECT * FROM events WHERE id IN ($ids_string)";
    $stmt = $pdo->query($sql);
    $eventos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cruzar a informação da base de dados com as quantidades da Sessão
    foreach ($eventos_db as $evento) {
        $id = $evento['id'];
        $quantidade = $_SESSION['carrinho'][$id];
        $subtotal = $evento['ticket_price'] * $quantidade;
        
        $total_carrinho += $subtotal; // Vai somando ao total geral
        
        // Guardar no nosso array final para exibir no HTML
        $evento['quantidade'] = $quantidade;
        $evento['subtotal'] = $subtotal;
        $carrinho_items[] = $evento;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | O Teu Carrinho</title>
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

    <main class="container my-5 pt-4">
        
        <h2 class="text-white mb-5" style="border-left: 4px solid #e0ff4f; padding-left: 15px;">O Teu Carrinho</h2>

        <?php if (empty($carrinho_items)): ?>
            <div class="feature-card text-center py-5" style="border: 1px dashed #333;">
                <div class="icon mb-3"><i class="fa-solid fa-cart-arrow-down text-muted" style="font-size: 4rem;"></i></div>
                <h3 class="text-white">O teu carrinho está vazio</h3>
                <p class="mb-4" style="#a0a0a0">Ainda não adicionaste nenhum bilhete. Descobre os melhores espetáculos!</p>
                <a href="eventos.php" class="btn btn-lime px-4 py-2 rounded-pill fw-bold">Explorar Eventos</a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                
                <div class="col-lg-8">
                    <?php foreach ($carrinho_items as $item): ?>
                        <div class="card shadow-sm border-0 mb-4" style="background-color: #161616; border-radius: 16px;">
                            <div class="row g-0 align-items-center p-3">
                                
                                <div class="col-4 col-md-3">
                                    <?php $img = !empty($item['image']) ? $item['image'] : 'default_event.jpg'; ?>
                                    <img src="uploads/<?= htmlspecialchars($img); ?>" class="img-fluid rounded" style="object-fit: cover; height: 100px; width: 100%;">
                                </div>
                                
                                <div class="col-8 col-md-5 ps-3">
                                    <h5 class="text-lime mb-1 fw-bold"><?= htmlspecialchars($item['title']); ?></h5>
                                    <p class="text-muted mb-1 small"><i class="fa-regular fa-calendar me-2"></i><?= date('d/m/Y', strtotime($item['event_date'])); ?></p>
                                    <p class="text-white mb-0">Preço: € <?= number_format($item['ticket_price'], 2, ',', '.'); ?></p>
                                </div>
                                
                                <div class="col-6 col-md-3 mt-3 mt-md-0 d-flex flex-column align-items-center justify-content-center">
                                    <form action="atualizar_carrinho.php" method="POST" class="d-flex align-items-center mb-2">
                                        <input type="hidden" name="id" value="<?= $item['id']; ?>">
                                        
                                        <input type="number" name="quantidade" value="<?= $item['quantidade']; ?>" min="1" max="10" 
                                            class="form-control form-control-sm bg-dark text-white border-secondary text-center me-2 shadow-none" 
                                            style="width: 65px;">
                                            
                                        <button type="submit" class="btn btn-sm btn-outline-lime rounded-circle" title="Atualizar quantidade">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                        </button>
                                    </form>
                                    
                                    <p class="text-white fw-bold mt-1 mb-0 fs-5">€ <?= number_format($item['subtotal'], 2, ',', '.'); ?></p>
                                </div>
                                
                                <div class="col-6 col-md-1 mt-3 mt-md-0 text-end text-md-center">
                                    <a href="remover_carrinho.php?id=<?= $item['id']; ?>" class="btn btn-outline-danger btn-sm rounded-circle" title="Remover item">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="feature-card p-4 position-sticky" style="top: 20px;">
                        <h4 class="text-white mb-4 border-bottom border-secondary pb-3">Resumo da Compra</h4>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span style="color: #a0a0a0;">Subtotal</span>
                            <span class="text-white">€ <?= number_format($total_carrinho, 2, ',', '.'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="color: #a0a0a0;">Taxas</span>
                            <span class="text-success">Grátis</span>
                        </div>
                        
                        <hr style="border-color: #333;">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-white fw-bold fs-5">Total</span>
                            <span class="text-lime fw-bold fs-3">€ <?= number_format($total_carrinho, 2, ',', '.'); ?></span>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="processar_compra.php" class="btn btn-lime w-100 py-3 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-lock me-2"></i>Finalizar Pagamento
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning text-center small rounded-3 p-2">
                                Precisas de iniciar sessão para finalizar a compra.
                            </div>
                            <a href="login_registo.php" class="btn btn-outline-lime w-100 py-2 rounded-pill">
                                Fazer Login
                            </a>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <i class="fa-brands fa-cc-visa fs-2 mx-2" style="color: #ffffff;" title="Visa"></i>
                            <i class="fa-brands fa-cc-mastercard fs-2 mx-2" style="color: #ff5f00;" title="Mastercard"></i>
                            <i class="fa-brands fa-cc-paypal fs-2 mx-2" style="color: #009cde;" title="PayPal"></i>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>