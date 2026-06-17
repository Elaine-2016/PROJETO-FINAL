<?php
require 'conexao.php';

$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';

// Construção da query dinâmica
$sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR location LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($date)) {
    $sql .= " AND event_date = :date";
    $params[':date'] = $date;
}

$sql .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($eventos) > 0) {
    foreach ($eventos as $evento) {
        $img = !empty($evento['image']) ? $evento['image'] : 'default_event.jpg';
        echo '<div class="col-md-4 col-lg-3">
                <div class="feature-card d-flex flex-column h-100">
                    <img src="uploads/'.htmlspecialchars($img).'" style="width: 100%; height: 200px; object-fit: cover;">
                    <div class="feature-card-content">
                        <h4 class="text-lime fs-5 mb-3">'.htmlspecialchars($evento['title']).'</h4>
                        <p class="mb-3" style="color: #a0a0a0; font-size: 14px;">'.htmlspecialchars(mb_strimwidth($evento['description'], 0, 80, "...")).'</p>
                        <div class="mb-4" style="color: #a0a0a0; font-size: 14px;">
                            <p class="mb-1"><i class="fa-solid fa-calendar-day me-2 text-white"></i>'.date('d/m/Y', strtotime($evento['event_date'])).'</p>
                            <p class="mb-1"><i class="fa-solid fa-location-dot me-2 text-white"></i>'.htmlspecialchars($evento['location']).'</p>
                        </div>
                        <div class="mt-auto pt-3 border-top" style="border-color: #333 !important;">
                            <span class="fs-4 fw-bold text-white">€ '.number_format($evento['ticket_price'], 2, ',', '.').'</span>
                            <a href="detalhes.php?id='.$evento['id'].'" class="btn btn-lime w-100 rounded-pill fw-bold mt-2">Comprar</a>
                        </div>
                    </div>
                </div>
              </div>';
    }
} else {
    echo '<div class="col-12 text-center py-5 text-white">Nenhum evento encontrado.</div>';
}
?>