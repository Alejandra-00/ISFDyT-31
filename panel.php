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
            <div class="logo">
                <img src="iconos/logo.jpg" alt="Logo">
            </div>
            <nav>
                <button onclick="mostrar('inicio')"><img src="iconos/inicio.png" alt="Inicio" style = "width: 17.5%">Inicio</button>
                <button onclick="mostrar('sociosAlumnos')"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 19%">Socios alumnos</button>
                <button onclick="mostrar('sociosVoluntarios')"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 19%">Socios voluntarios</button>
                <button onclick="mostrar('carreras')"><img src="iconos/graduacion.png" alt="Sombrero" style= "width: 18%">Carreras</button>
                <button onclick="mostrar('cooperadora')"><img src="iconos/dinero.png" alt="Pago" style= "width: 18%">Cooperadora actual</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/dinero.png" alt="Pago" style= "width: 18%">Ver pagos</button>
                <button onclick="mostrar('graficos')"><img src="iconos/graficos.png" alt="Grafico" style= "width: 18%">Gráficos</button>
            </nav>
        </aside>

        <!-- CONTENIDO -->
        <main class="contenido">
            <!-- inicio -->
            <div id="inicio" class="formulario activo tarjeta">
                <div class="cards">
                    <div class="card-chica">
                        <div id = "cantAlumnos" class="card-numero"></div> Socio alumnos
                    </div>

                    <div class="card-chica">
                        <div id = "cantVoluntarios" class="card-numero"></div> Socio voluntarios
                    </div>

                    <div class="card-chica">
                        <div id = "cantCarreras" class="card-numero"></div> Carreras
                    </div>

                    <div class="card-chica">
                        <div id = "montoActual" class="card-numero"></div> Cooperadora actual
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
            
            <div id="verPagos" class="formulario tarjeta">
                <table class="tabla-pagos">
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
                    <tbody id="tablapagos">
                        <tr>
                            <td colspan="8" class="text-center"></td>
                        </tr>
                    </tbody>
                </table>
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