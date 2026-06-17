<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA: Verificar se o utilizador tem sessão iniciada
if (!isset($_SESSION['user_id'])) {
    header("Location: login_registo.php");
    exit();
}

// 2. Verificar se o carrinho existe e tem itens
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // 3. Iniciar Transação (Protege os dados caso algo falhe a meio)
    $pdo->beginTransaction();

    // Preparamos as queries fora do ciclo para maior performance
    $stmt_evento = $pdo->prepare("SELECT ticket_price FROM events WHERE id = :id");
    
    // Assumimos que o status inicial da compra é 'pendente' (aguarda pagamento real, se existisse Stripe/MBWay)
    // Se a tua tabela não tiver a coluna purchase_date (e usar um DEFAULT CURRENT_TIMESTAMP), podes apagar o NOW() e a coluna da query.
    $sql_compra = "INSERT INTO purchases (user_id, event_id, quantity, total_price, status, purchase_date) 
                   VALUES (:user_id, :event_id, :quantity, :total_price, 'pendente', NOW())";
    $stmt_compra = $pdo->prepare($sql_compra);

    // 4. Percorrer cada item do carrinho
    foreach ($_SESSION['carrinho'] as $event_id => $quantidade) {
        
        // Vamos buscar o preço atual do bilhete diretamente à base de dados (nunca confiar no preço que vem do frontend)
        $stmt_evento->execute([':id' => $event_id]);
        $evento = $stmt_evento->fetch(PDO::FETCH_ASSOC);

        if ($evento) {
            $total_price = $evento['ticket_price'] * $quantidade;

            // Inserir o registo da compra para este evento
            $stmt_compra->execute([
                ':user_id' => $user_id,
                ':event_id' => $event_id,
                ':quantity' => $quantidade,
                ':total_price' => $total_price
            ]);
        }
    }

    // 5. Confirmar e guardar tudo na base de dados
    $pdo->commit();

    // 6. Esvaziar o carrinho, pois a compra foi um sucesso!
    unset($_SESSION['carrinho']);

    // 7. Redirecionar o utilizador para o seu perfil para ver os bilhetes recém-comprados
    header("Location: perfil.php?sucesso=compra");
    exit();

} catch (PDOException $e) {
    // Se houver algum erro, cancela a transação inteira
    $pdo->rollBack();
    die("Ocorreu um erro ao processar a tua compra. Por favor, tenta novamente. Erro: " . $e->getMessage());
}
?>