<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function envoyerCodeOTP($email, $nom, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'korobaramahamane311@gmail.com'; 
        $mail->Password = 'pwflelxvpihvugdy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('korobaramahamane311@gmail.com', 'MarkEdit');
        $mail->addAddress($email, $nom);
        
        $mail->isHTML(true);
        $mail->Subject = 'Verification de votre compte MarkEdit';
        $mail->Body = "
            <h2>Bienvenue sur MarkEdit !</h2>
            <p>Bonjour $nom,</p>
            <p>Voici votre code de vérification :</p>
            <h1 style='color: #4CAF50; font-size: 32px; letter-spacing: 5px;'>$code</h1>
            <p>Ce code est valable pendant 10 minutes.</p>
            <p>Si vous n'avez pas créé de compte sur MarkEdit, ignorez cet email.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        throw new Exception("Erreur d'envoi d'email : " . $mail->ErrorInfo);
    }
}
?>
