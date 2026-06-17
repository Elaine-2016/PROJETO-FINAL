<?php
session_start();
require 'conexao.php';

// Inicializar a variável de pesquisa
$pesquisa = $_GET['search'] ?? '';

// Construir a Query baseada na existência ou não de uma pesquisa
if (!empty($pesquisa)) {
    // Pesquisa por título OU localização (usando LIKE para encontrar partes da palavra)
    $sql = "SELECT * FROM events WHERE event_date >= CURDATE() AND (title LIKE :search OR location LIKE :search) ORDER BY event_date ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$pesquisa%"]);
} else {
    // Se não houver pesquisa, mostra todos os próximos eventos
    $sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
    $stmt = $pdo->query($sql);
}

$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Catálogo de Eventos</title>
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
        
        <div class="row mb-5 align-items-center">
            <div class="col-md-4">
                <h2 class="ps-3 text-white m-0" style="border-left: 4px solid #e0ff4f;">Todos os Eventos</h2>
                <p style="color: #a0a0a0;" class="mt-2 mb-0">Encontra o teu próximo espetáculo.</p>
            </div>
            <div class="col-md-8">
                <div class="row g-2" data-bs-theme="dark">
                    <div class="col-md-6">
                        <input type="text" id="searchQuery" class="form-control bg-dark border-secondary text-white" placeholder="Pesquisar por título ou local...">
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="searchDate" class="form-control bg-dark border-secondary text-white">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4" id="resultados">
            <div class="col-12 text-center text-white">A carregar eventos...</div>
        </div>
    </main>

    <script>
        const queryInput = document.getElementById('searchQuery');
        const dateInput = document.getElementById('searchDate');
        const resultadosDiv = document.getElementById('resultados');

        function buscarEventos() {
            const search = queryInput.value;
            const date = dateInput.value;
            
            // Chama o motor de busca que criámos no ficheiro buscar_eventos.php
            fetch(`buscar_eventos.php?search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`)
                .then(response => response.text())
                .then(html => {
                    resultadosDiv.innerHTML = html;
                });
        }

        // Escutar mudanças nos inputs
        queryInput.addEventListener('input', buscarEventos);
        dateInput.addEventListener('change', buscarEventos);

        // Carregar tudo ao abrir a página
        buscarEventos();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>