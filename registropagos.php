<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="registropagos.css">
</head>
<body>
    <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include("nav.php");
    ?>
    <div class="cont">
        <form id="formPago" action="pagarCooperadora.php" method="POST">
            <input type="hidden" name="id_pago" id="id_pago" value="">
            
            <table class="registro">
                <tbody id="tablapagos">
                    
                </tbody>
            </table>
        </form>
    </div>
    <script src="registropagos.js"></script>
</body>
</html>