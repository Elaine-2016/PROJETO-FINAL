<?php
require 'conexao.php';

// Variáveis para controlar o design da mensagem
$mensagem = "";
$tipo_alerta = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $mensagem = "As passwords que digitou não coincidem.";
        $tipo_alerta = "danger";
    } else {
        // Processar a Foto
        $profile_pic = 'default_avatar.png'; 
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $upload_dir = __DIR__ . '/uploads/'; 
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_pic']['name']);
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $file_name)) {
                $profile_pic = $file_name;
            }
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (name, email, password, profile_pic) VALUES (:name, :email, :password, :profile_pic)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password_hash,
                ':profile_pic' => $profile_pic
            ]);
            
            $mensagem = "A sua conta foi criada com sucesso!";
            $tipo_alerta = "success";
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem = "Este endereço de email já encontra-se registado no nosso sistema.";
                $tipo_alerta = "warning";
            } else {
                $mensagem = "Ocorreu um erro técnico: " . $e->getMessage();
                $tipo_alerta = "danger";
            }
        }
    }
} else {
    // Se tentarem aceder ao ficheiro diretamente sem preencher o formulário
    header("Location: login_registo.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Processar Registo</title>
    <meta name="description" content="VivaTickets: A sua plataforma de eleição para comprar bilhetes online. Descubra os melhores concertos, festivais e eventos imperdíveis. Rápido, seguro e intuitivo. Garanta o seu lugar agora!">
    <meta name="keywords" content="comprar bilhetes, venda de ingressos, bilheteira online, VivaTickets, concertos, festivais, eventos, agenda de eventos, reservar lugares, entretenimento, bilhetes online">  
    <meta property="og:type" content="website">
    <meta property="og:title" content="VivaTickets | <?= htmlspecialchars($evento['title'] ?? 'Bilheteira Online'); ?>">
    <meta property="og:description" content="Garanta os seus bilhetes para os melhores eventos.">
    <meta property="og:image" content="https://teusite.com/uploads/<?= htmlspecialchars($evento['image'] ?? 'default_event.jpg'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0 text-center mt-5">
                    <div class="card-body p-5">
                        
                        <?php if ($tipo_alerta === 'success'): ?>
                            <h1 class="text-success mb-3">🎉</h1>
                            <h3 class="mb-3">Bem-vindo(a) ao VivaTickets!</h3>
                        <?php elseif ($tipo_alerta === 'warning'): ?>
                            <h1 class="text-warning mb-3">⚠️</h1>
                            <h3 class="mb-3">Atenção</h3>
                        <?php else: ?>
                            <h1 class="text-danger mb-3">❌</h1>
                            <h3 class="mb-3">Erro no Registo</h3>
                        <?php endif; ?>

                        <p class="text-muted mb-4"><?= htmlspecialchars($mensagem); ?></p>
                        
                        <a href="login_registo.php" class="btn btn-<?= $tipo_alerta === 'success' ? 'primary' : 'secondary'; ?> w-100">
                            <?= $tipo_alerta === 'success' ? 'Ir para o Login' : 'Tentar Novamente'; ?>
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>