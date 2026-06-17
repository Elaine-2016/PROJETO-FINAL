<?php
session_start();
require 'conexao.php';

// Proteção de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 1. PROCESSAR A ATUALIZAÇÃO (Quando clicas em Guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_evento'])) {
    $id = $_POST['id'];
    
    // Buscar imagem atual para não a perdermos se não enviarmos uma nova
    $stmt = $pdo->prepare("SELECT image FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $evento_atual = $stmt->fetch(PDO::FETCH_ASSOC);
    $image = $evento_atual['image'];

    // Lógica da nova imagem
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $upload_dir = __DIR__ . '/uploads/';
        $file_name = time() . '_' . basename($_FILES['event_image']['name']);
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_dir . $file_name)) {
            $image = $file_name;
        }
    }

    // Atualizar base de dados
    $sql = "UPDATE events SET title = :title, description = :description, image = :image, event_date = :event_date, 
            event_time = :event_time, location = :location, ticket_price = :ticket_price WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $_POST['title'], ':description' => $_POST['description'], ':image' => $image,
        ':event_date' => $_POST['event_date'], ':event_time' => $_POST['event_time'],
        ':location' => $_POST['location'], ':ticket_price' => $_POST['ticket_price'], ':id' => $id
    ]);
    header("Location: admin_dashboard.php?sucesso=atualizado");
    exit();
}

// =========================================================================
// A PARTE QUE TINHA DESAPARECIDO:
// 2. BUSCAR OS DADOS ATUAIS (Quando a página é aberta para edição)
// =========================================================================
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id' => $id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o evento não existir, volta para o painel
if (!$evento) {
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
    <title>VivaTickets | Editar Evento</title>
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
                    <h2 class="text-white m-0" style="border-left: 4px solid #e0ff4f; padding-left: 15px;">Editar Evento</h2>
                    <a href="admin_dashboard.php" class="btn btn-outline-secondary rounded-pill">Voltar ao Painel</a>
                </div>

                <div class="card shadow-lg border-0" data-bs-theme="dark" style="background-color: #161616; border-radius: 24px;">
                    <div class="card-body p-4 p-md-5">
                        
                        <form action="editar_evento.php?id=<?= $evento['id']; ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $evento['id']; ?>">
                            
                            <div class="mb-4 text-center p-3" style="background-color: #1a1a1a; border-radius: 12px; border: 1px dashed #333;">
                                <label class="form-label text-white d-block mb-3">Capa do Evento Atual</label>
                                <?php $img_atual = !empty($evento['image']) ? $evento['image'] : 'default_event.jpg'; ?>
                                <img src="uploads/<?= htmlspecialchars($img_atual); ?>" class="img-fluid rounded mb-3 shadow" style="max-height: 200px; width: 100%; object-fit: cover; border: 1px solid #222;">
                                
                                <input type="file" name="event_image" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                <small class="text-muted mt-2 d-block">Carregue uma nova imagem apenas se quiser substituir a atual.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-white">Título do Evento</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($evento['title']); ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Data</label>
                                    <input type="date" name="event_date" class="form-control" value="<?= $evento['event_date']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Hora</label>
                                    <input type="time" name="event_time" class="form-control" value="<?= $evento['event_time']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Localização</label>
                                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($evento['location']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Preço do Bilhete (€)</label>
                                <input type="number" name="ticket_price" class="form-control" step="0.01" min="0" value="<?= $evento['ticket_price']; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Descrição</label>
                                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($evento['description']); ?></textarea>
                            </div>

                            <button type="submit" name="atualizar_evento" class="btn btn-lime w-100 fw-bold">Guardar Alterações</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>