<?php
session_start();
if (!isset($_SESSION['inscription_temp'])) {
    header('Location: ../landing.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP - MarkEdit</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/landing.css">

    <style>
        .otp-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .otp-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 12px;
            padding: 10px;
        }
        .timer {
            text-align: center;
            margin: 10px 0;
            color: var(--text-primary);
        }
        .resend {
            text-align: center;
            margin-top: 10px;
        }
        .error-message {
            color: #ff4444;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2>Vérification de votre email</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <p>Un code de vérification a été envoyé à <?php echo htmlspecialchars($_SESSION['inscription_temp']['email']); ?></p>
        
        <form action="verifierOTP.php" method="post" class="otp-form">
            <input type="text" name="otp" maxlength="6" pattern="\d{6}" 
                   class="otp-input" placeholder="------" required
                   inputmode="numeric">
            <div class="timer" id="timer">Expire dans : 10:00</div>
            <button type="submit" class="primary-button">Vérifier</button>
        </form>

        <div class="resend">
            <p>Vous n'avez pas reçu le code ?</p>
            <a href="renvoyerOTP.php">Renvoyer le code</a>
        </div>
    </div>

    <script>
        // Timer pour l'expiration du code
        function startTimer(duration, display) {
            let timer = duration, minutes, seconds;
            let countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = "Expire dans : " + minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "Code expiré";
                    display.style.color = "#ff4444";
                }
            }, 1000);
        }

        window.onload = function () {
            let tenMinutes = 60 * 10,
                display = document.querySelector('#timer');
            startTimer(tenMinutes, display);
        };
    </script>
</body>
</html>
