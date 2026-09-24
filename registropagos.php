<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="registropago.css">
</head>
<body>
    <?php
        include("nav.php");
    ?>
    <div class="cont">
        <button class="registro">
            <table>
                <tr>
                    <tbody id="tablapagos" onload="cargarPagos()">
                     
                    </tbody>
                </tr>
            </table>
        </button>
        
    </div>
    <script src="registropagos.js"></script>
</body>
</html>