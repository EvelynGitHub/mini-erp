<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

class EmailService
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Envia e-mail de confirmação de pedido.
     */
    public function enviarPedido(int $pedidoId, string $email, string $endereco, float $total): bool
    {
        sleep(30); // Simula atraso para testes


        file_put_contents('php://stderr', "[EmailService] Iniciado envio de email: {$email}" . PHP_EOL);

        // Busca os itens do pedido
        $sql = "SELECT p.nome, i.quantidade, i.preco_unitario
                FROM pedido_itens i
                JOIN produtos p ON p.id = i.produto_id
                WHERE i.pedido_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pedidoId]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Monta lista dos produtos
        $listaProdutos = "<ul>";
        foreach ($itens as $item) {
            $nome = htmlspecialchars($item['nome']);
            $qtd = (int) $item['quantidade'];
            $preco = number_format($item['preco_unitario'], 2, ',', '.');

            $listaProdutos .= "<li>{$nome} - {$qtd}x (R\${$preco} cada)</li>";
        }
        $listaProdutos .= "</ul>";

        // Envio com PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];// 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER']; //'evelynbrandao15@gmail.com';
            $mail->Password = $_ENV['MAIL_PASS']; //'eqvh ervo weeg bnfv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT']; // 587;

            $mail->setFrom('loja@teste.com', 'Mini ERP');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Confirmação do Pedido #$pedidoId";
            $mail->Body = "Olá! Seu pedido foi realizado com sucesso.<br><br>
                           <strong>Itens:</strong> {$listaProdutos}
                           <strong>Total:</strong> R$" . number_format($total, 2, ',', '.') . "<br>
                           <strong>Endereço:</strong> {$endereco}<br><br>
                           Obrigado pela compra!";

            $mail->send();
            return true;
        } catch (Exception $e) {
            file_put_contents('php://stderr', "[EmailService Error] Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . PHP_EOL);
            error_log("Erro ao enviar e-mail do pedido #$pedidoId: " . $mail->ErrorInfo);
            return false;
        }
    }
}
