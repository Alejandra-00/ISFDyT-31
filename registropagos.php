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
        include("nav.php");
    ?>
    <div class="cont">
        <form action="pagarCooperadora.php" method="POST">
            <button class="registro">
                <table>
                    <tr>
                        <tbody id="tablapagos" onload="cargarPagos()">
                        
                        </tbody>
                    </tr>
                </table>
            </button>
         <input type="hidden" name="id_pago" id="id_pago" value="">
        </form>
    </div>
    <script src="registropagos.js"></script>
</body>
</html>