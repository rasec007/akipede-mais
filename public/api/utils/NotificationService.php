<?php
// public/api/utils/NotificationService.php

class NotificationService {
    private static $wa_url = "https://evolutionapi.c2net.com.br/message/sendText/cpu";
    private static $wa_key = "D5B618562D06-46B4-9977-EC1190CA4D0A";

    private static $smtp_host = "smtp.gmail.com";
    private static $smtp_port = 587;
    private static $smtp_user = "coopergenbubble@gmail.com";
    private static $smtp_pass = "zmzzcongcuheoxvu";
    private static $smtp_from = "coopergenbubble@gmail.com";

    public static function sendWelcome($data, $type = 'user') {
        $name = $data['nome'] ?? 'Usuário';
        $email = $data['email'] ?? '';
        $phone = $data['fone'] ?? $data['celular'] ?? '';
        $password = $data['raw_password'] ?? $data['senha'] ?? ''; // Senha não criptografada para envio inicial
        $perfil = empty($data['perfil']) ? 'Cliente' : $data['perfil'];

        $subject = "Bem-vindo ao Akipede Mais!";
        
        $message = "Olá Sr.(a) $name,\n\n";
        if ($type === 'user' || $type === 'parceiro' || $type === 'lojista') {
            $message .= "Sua conta no Akipede Mais foi criada com sucesso!\n";
            $message .= "Aqui estão suas credenciais de acesso:\n";
            $message .= "E-mail: $email\n";
            if ($password) $message .= "Senha: $password\n";
            $message .= "\nAcesse em: http://localhost:8000/login.php\n";
        } else {
            $message .= "Seu cadastro como {$perfil} no Akipede Mais foi realizado.\n";
            $message .= "Para acessar o sistema use as credenciais abaixo:\n";
            $message .= "Link: http://localhost:8000/login.php\n";
            $message .= "Email: $email\n";
            $message .= "Senha: $password\n";
        }
        $message .= "\nEquipe Akipede Mais";

        // Enviar WhatsApp
        if ($phone) {
            self::sendWhatsApp($phone, $message);
        }

        // Enviar Email
        if ($email) {
            self::sendEmail($email, $subject, $message);
        }
    }

    public static function sendWhatsApp($number, $text) {
        $number = preg_replace('/\D/', '', $number);
        // Adiciona 55 se o número tiver 10 ou 11 dígitos e não começar com 55
        if ((strlen($number) == 10 || strlen($number) == 11) && substr($number, 0, 2) !== '55') {
            $number = "55" . $number;
        }

        $payload = [
            "number" => $number,
            "text" => $text
        ];

        $ch = curl_init(self::$wa_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "apikey: " . self::$wa_key
        ]);

        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public static function sendEmail($to, $subject, $message) {
        // Minimal SMTP implementation for Gmail (STARTTLS)
        try {
            $socket = fsockopen(self::$smtp_host, self::$smtp_port, $errno, $errstr, 10);
            if (!$socket) throw new Exception("Could not connect: $errstr");

            $response = fgets($socket, 512);
            
            fwrite($socket, "EHLO " . self::$smtp_host . "\r\n");
            while($line = fgets($socket, 512)) if(substr($line,3,1) == ' ') break;

            fwrite($socket, "STARTTLS\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '220') throw new Exception("STARTTLS failed: $response");

            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            fwrite($socket, "EHLO " . self::$smtp_host . "\r\n");
            while($line = fgets($socket, 512)) if(substr($line,3,1) == ' ') break;

            fwrite($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 512);
            
            fwrite($socket, base64_encode(self::$smtp_user) . "\r\n");
            $response = fgets($socket, 512);
            
            fwrite($socket, base64_encode(self::$smtp_pass) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '235') throw new Exception("Login failed: $response");

            fwrite($socket, "MAIL FROM: <" . self::$smtp_from . ">\r\n");
            $response = fgets($socket, 512);

            fwrite($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 512);

            fwrite($socket, "DATA\r\n");
            $response = fgets($socket, 512);

            $headers = "From: Akipede Mais <" . self::$smtp_from . ">\r\n";
            $headers .= "To: <$to>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "\r\n";

            fwrite($socket, $headers . $message . "\r\n.\r\n");
            $response = fgets($socket, 512);

            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            return true;
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return false;
        }
    }
}
