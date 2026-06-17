<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criar_evento'])) {
    $title = trim($_POST['title']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $ticket_price = floatval($_POST['ticket_price']);
    $description = trim($_POST['description']);
    
    $image = 'default_event.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
            $image = $file_name;
        }
    }

    try {
        $sql = "INSERT INTO events (title, description, image, event_date, event_time, location, ticket_price) 
                VALUES (:title, :description, :image, :event_date, :event_time, :location, :ticket_price)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title, ':description' => $description, ':image' => $image,
            ':event_date' => $event_date, ':event_time' => $event_time,
            ':location' => $location, ':ticket_price' => $ticket_price
        ]);
        header("Location: admin_dashboard.php?sucesso=1");
        exit();
    } catch (PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
}
?>