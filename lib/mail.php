<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Certifique-se de que o autoload do Composer está no caminho correto.

function enviarEmail($destinatario, $assunto, $mensagem, $nomeDestinatario = '', $anexos = null) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP do Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thomasschanoel@gmail.com'; // Seu endereço de e-mail
        $mail->Password   = 'joww vbgo zfgh smjw'; // Senha ou senha de aplicativo (não use sua senha principal do Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Criptografia SSL/TLS
        $mail->Port       = 465; // Porta SMTP para SSL

        $mail->CharSet = 'UTF-8';

        // Remetente e destinatário
        $mail->setFrom('thomasschanoel@gmail.com', 'Thomas Pontes'); // Remetente
        $mail->addAddress($destinatario, $nomeDestinatario); // Destinatário

        // Verificar se há anexos
        if (!empty($anexos)) {
            foreach ($anexos as $anexo) {
                $mail->addAttachment($anexo);
            }
        }

        // Conteúdo do e-mail
        $mail->isHTML(true); // Define o e-mail como HTML
        $mail->Subject = $assunto;
        $mail->Body    = $mensagem; // Corpo do e-mail em HTML
        $mail->AltBody = strip_tags($mensagem); // Texto alternativo para leitores que não suportam HTML

        $mail->send();
        return "E-mail enviado com sucesso para $destinatario!";
    } catch (Exception $e) {
        return "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
    }
}
