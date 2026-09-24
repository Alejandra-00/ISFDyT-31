<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Cooperadora</title>
    <link rel="stylesheet" href="panel.css">
</head>
<body>
    <div class="contenedor">
        <!-- sidebar -->
        <aside class="sideBar">
            <h1>ISFDyT N°31</h1>
            <div class="logo">
                <img src="iconos/logo.jpg" alt="Logo">
            </div>
            <nav>
                <button onclick="mostrar('inicio')">Inicio</button>
                <button onclick="mostrar('sociosAlumnos')"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 19%">Socios alumnos</button>
                <button onclick="mostrar('sociosVoluntarios')"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 19%">Socios voluntarios</button>
                <button onclick="mostrar('carreras')"><img src="iconos/graduacion.png" alt="Usuario" style= "width: 18%">Carreras</button>
                <button onclick="mostrar('cooperadora')"><img src="iconos/dinero.png" alt="Usuario" style= "width: 18%">Cooperadora actual</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/dinero.png" alt="Usuario" style= "width: 18%">Ver pagos</button>
                <button onclick="mostrar('graficos')"><img src="iconos/graficos.png" alt="Usuario" style= "width: 18%">Gráficos</button>
            </nav>
        </aside>

        <!-- CONTENIDO -->
        <main class="contenido">
            <!-- inicio -->
            <div id="inicio" class="formulario activo tarjeta">
                <div class="cards">
                    <div class="card-chica">
                        <div class="card-numero">130</div> Socio alumnos
                    </div>

                    <div class="card-chica">
                        <div class="card-numero">234</div> Socio voluntarios
                    </div>

                    <div class="card-chica">
                        <div class="card-numero">35</div> Carreras
                    </div>

                    <div class="card-chica">
                        <div class="card-numero">$14.000</div> Cooperadora actual
                    </div>
                </div> 
                
                <div class="listados">
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio alumnos</h4>
                            <button  class="ver-todos" onclick="mostrar('sociosAlumnos')">Ver todos</button>
                        </div>
                        <div class="listado-item">
                            <span class="item-email">nombre@gmail.com</span>
                        </div>
                    </div>
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio voluntario</h4>
                            <button  class="ver-todos" onclick="mostrar('sociosVoluntarios')">Ver todos</button>
                        </div>
                        <div class="listado-item">
                            <span class="item-email">nombre@gmail.com</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Socio alumnos -->
            <div id="sociosAlumnos" class="formulario tarjeta">
                <h3>Socios alumnos</h3>
            </div>

            <!-- Socio voluntarios -->
            <div id="sociosVoluntarios" class="formulario tarjeta">
                <h3>Socios voluntarios</h3>
            </div>

            <!-- Carreras-->
            <div id="carreras" class="formulario tarjeta">
                <h3>Carreras</h3>
            </div>

            <!-- Cooperadora -->
            <div id="cooperadora" class="formulario tarjeta">
                <h3>Cooperadora actual</h3>
            </div>
            
            <!-- Ver pagos -->
            <div id="verPagos" class="formulario tarjeta">
                <h3>Ver pagos</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo de socio</th>
                                <th>Carrera</th>
                                <th>Mes</th>
                                <th>Fecha</th>
                                <th>Importe</th>
                                <th>Estado</th>
                                <th>Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!$resultadoPagos) {
                                echo "
                                    <tr>
                                        <td colspan='8' class='text-center'>
                                            Error al consultar los pagos.
                                        </td>
                                    </tr>
                                ";
                            } elseif ($resultadoPagos->num_rows == 0) {
                                echo "
                                    <tr>
                                        <td colspan='8' class='text-center'>
                                            No hay pagos registrados.
                                        </td>
                                    </tr>
                                ";
                            } else {
                                while ($row = $resultadoPagos->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($row['tipo_socio']) ?></td>
                                    <td><?= htmlspecialchars($row['carrera']) ?></td>
                                    <td><?= htmlspecialchars($row['mes']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                                    <td>$<?= htmlspecialchars($row['importe']) ?></td>
                                    <td><?= htmlspecialchars($row['estado']) ?></td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                            <a href="<?= htmlspecialchars($row['foto']) ?>" target="_blank">Ver</a>
                                        <?php else: ?>
                                            Sin archivo
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Graficos -->
            <div id="graficos" class="formulario tarjeta">
                <h3>Gráficos</h3>
            </div>
        </main>
    </div>
    <script src="panel.js"></script>
</body>
</html>