<?php
session_start();
require 'conexao.php';

// PROTEÇÃO DE ROTA MÁXIMA
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. DADOS PARA A VISÃO GERAL (Dashboard Overview)
$total_eventos = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_vendas = $pdo->query("SELECT SUM(total_price) FROM purchases WHERE status = 'concluida'")->fetchColumn() ?: 0;

// 2. BUSCAR TODOS OS EVENTOS
$eventos = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// 3. BUSCAR TODOS OS UTILIZADORES
$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// 4. BUSCAR TODAS AS COMPRAS (Com JOIN para saber o nome do utilizador e do evento)
$sql_compras = "SELECT p.id, p.quantity, p.total_price, p.status, p.purchase_date, u.name as user_name, e.title as event_title 
                FROM purchases p 
                JOIN users u ON p.user_id = u.id 
                JOIN events e ON p.event_id = e.id 
                ORDER BY p.purchase_date DESC";
$compras = $pdo->query($sql_compras)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Painel de Administração</title>
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

    <main class="container-fluid px-4 my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white m-0" style="border-left: 4px solid #e0ff4f; padding-left: 15px;">Dashboard do Administrador</h2>
        </div>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success bg-dark text-success border border-success rounded-3 shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i> Operação realizada com sucesso!
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="feature-card text-center p-4" style="border-color: #333;">
                    <i class="fa-solid fa-calendar-check text-lime mb-2" style="font-size: 2rem;"></i>
                    <h3 class="text-white"><?= $total_eventos; ?></h3>
                    <p class="color: #a0a0a0; mb-0">Eventos Ativos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4" style="border-color: #333;">
                    <i class="fa-solid fa-users text-lime mb-2" style="font-size: 2rem;"></i>
                    <h3 class="text-white"><?= $total_users; ?></h3>
                    <p class="color: #a0a0a0; mb-0">Utilizadores Registados</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center p-4" style="border-color: #333;">
                    <i class="fa-solid fa-euro-sign text-lime mb-2" style="font-size: 2rem;"></i>
                    <h3 class="text-white">€ <?= number_format($total_vendas, 2, ',', '.'); ?></h3>
                    <p class="color: #a0a0a0; mb-0">Total de Vendas</p>
                </div>
            </div>
        </div>

        <div class="card shadow-lg border-0 mb-5" data-bs-theme="dark" style="background-color: #161616; border-radius: 24px;">
            <div class="card-body p-4">
                
                <ul class="nav nav-tabs custom-tabs mb-4" id="adminTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-eventos">Gestão de Eventos</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-users">Utilizadores</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-compras">Compras e Bilhetes</button></li>
                </ul>

                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="tab-eventos">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <h4 class="text-lime mb-4"><i class="fa-solid fa-calendar-plus me-2"></i>Novo Evento</h4>
                                <form action="processar_evento.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label text-white">Título</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label text-white">Data</label><input type="date" name="event_date" class="form-control" required></div>
                                        <div class="col-6 mb-3"><label class="form-label text-white">Hora</label><input type="time" name="event_time" class="form-control" required></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label text-white">Local</label><input type="text" name="location" class="form-control" required></div>
                                    <div class="mb-3"><label class="form-label text-white">Preço (€)</label><input type="number" name="ticket_price" class="form-control" step="0.01" min="0" required></div>
                                    <div class="mb-3"><label class="form-label text-white">Imagem (Opcional)</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                                    <div class="mb-4"><label class="form-label text-white">Descrição</label><textarea name="description" class="form-control" rows="2" required></textarea></div>
                                    <button type="submit" name="criar_evento" class="btn btn-lime w-100 fw-bold">Publicar Evento</button>
                                </form>
                            </div>
                            <div class="col-lg-8">
                                <h4 class="text-white mb-4"><i class="fa-solid fa-list me-2"></i>Eventos Ativos</h4>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover align-middle">
                                        <thead style="background-color: #222;">
                                            <tr>
                                                <th class="text-lime">Evento</th><th class="text-lime">Data</th><th class="text-lime">Preço</th><th class="text-lime text-center">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($eventos as $evento): ?>
                                                <tr>
                                                    <td class="text-white fw-bold"><?= htmlspecialchars($evento['title']); ?></td>
                                                    <td class="text-muted"><?= date('d/m/Y', strtotime($evento['event_date'])); ?></td>
                                                    <td class="text-white">€ <?= number_format($evento['ticket_price'], 2, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <a href="editar_evento.php?id=<?= $evento['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-pen"></i></a>
                                                        <a href="apagar_evento.php?id=<?= $evento['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apagar este evento?');"><i class="fa-solid fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-users">
                        <h4 class="text-white mb-4"><i class="fa-solid fa-users me-2"></i>Gestão de Utilizadores</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle">
                                <thead style="background-color: #222;">
                                    <tr>
                                        <th class="text-lime">Nome</th><th class="text-lime">Email</th><th class="text-lime">Permissão</th><th class="text-lime">Registado em</th><th class="text-lime text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td class="text-white fw-bold"><?= htmlspecialchars($u['name']); ?></td>
                                            <td class="text-muted"><?= htmlspecialchars($u['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?= $u['role'] === 'admin' ? 'primary' : 'secondary'; ?>"><?= strtoupper($u['role']); ?></span>
                                            </td>
                                            <td class="text-muted"><?= date('d/m/Y', strtotime($u['created_at'])); ?></td>
                                            <td class="text-center">
                                                <a href="editar_utilizador.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="Editar">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                
                                                <?php if ($u['id'] !== $_SESSION['user_id']): // Impede que o admin apague a sua própria conta ?>
                                                    <a href="apagar_utilizador.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-danger" title="Apagar" onclick="return confirm('Tens a certeza? Isto apagará o utilizador e todas as suas compras.');">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-compras">
                        <h4 class="text-white mb-4"><i class="fa-solid fa-cart-shopping me-2"></i>Registo de Compras</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle">
                                <thead style="background-color: #222;">
                                    <tr>
                                        <th class="text-lime">Comprador</th><th class="text-lime">Evento</th><th class="text-lime">Qtd</th><th class="text-lime">Total</th><th class="text-lime text-center">Estado (Editar)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compras as $c): ?>
                                        <tr>
                                            <td class="text-white fw-bold"><?= htmlspecialchars($c['user_name']); ?></td>
                                            <td class="text-muted"><?= htmlspecialchars($c['event_title']); ?></td>
                                            <td class="text-white"><?= $c['quantity']; ?></td>
                                            <td class="text-white">€ <?= number_format($c['total_price'], 2, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <form action="atualizar_status.php" method="POST" class="d-flex justify-content-center">
                                                    <input type="hidden" name="compra_id" value="<?= $c['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary me-2" style="width: auto;">
                                                        <option value="pendente" <?= $c['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                        <option value="concluida" <?= $c['status'] == 'concluida' ? 'selected' : ''; ?>>Concluída</option>
                                                        <option value="cancelada" <?= $c['status'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-lime"><i class="fa-solid fa-check"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>