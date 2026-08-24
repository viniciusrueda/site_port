<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configurações do SMTP
$SMTP_SERVER = "smtp.gmail.com";
$SMTP_PORT = 587;
$EMAIL_SENDER = "SEU_EMAIL_AQUI@gmail.com";  // valor real apenas no servidor, não commitado
$EMAIL_PASSWORD = "SUA_SENHA_DE_APP_AQUI"; // valor real apenas no servidor, não commitado

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "danger", "mensagem" => "Método inválido"]);
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($nome) || empty($email) || empty($mensagem)) {
    echo json_encode(["status" => "danger", "mensagem" => "Preencha todos os campos obrigatórios!"]);
    exit;
}

$corpo_email = "
    <h3>Nova mensagem recebida do site:</h3>
    <p><strong>Nome:</strong> $nome</p>
    <p><strong>E-mail:</strong> $email</p>
    <p><strong>Telefone:</strong> $telefone</p>
    <p><strong>Mensagem:</strong><br>" . nl2br($mensagem) . "</p>
";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $SMTP_SERVER;
    $mail->SMTPAuth = true;
    $mail->Username = $EMAIL_SENDER;
    $mail->Password = $EMAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $SMTP_PORT;

    $mail->setFrom($EMAIL_SENDER, 'Contato do Site');
    $mail->addAddress($EMAIL_SENDER);

    $mail->isHTML(true);
    $mail->Subject = "Nova mensagem do site";
    $mail->Body = $corpo_email;

    if ($mail->send()) {
        echo json_encode(["status" => "success", "mensagem" => "Mensagem enviada com sucesso!"]);
    } else {
        echo json_encode(["status" => "danger", "mensagem" => "Erro ao enviar mensagem: " . $mail->ErrorInfo]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "danger", "mensagem" => "Erro ao enviar mensagem: " . $e->getMessage()]);
}
?>
