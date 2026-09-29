<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="panel.css">
</head>
<body>
    <div class="contenedor">
        <!-- Sidebar -->
        <aside class="sideBar">
            <div class="logo">
                <img src="iconos/logo.jpg" alt="Logo">
            </div>
            <nav>
                <button onclick="mostrar('inicio')"><img src="iconos/inicio.png" alt="Inicio">Inicio</button>
                <button onclick="mostrar('sociosAlumnos')"><img src="iconos/usuario-azul.png" alt="Usuario">Socios alumnos</button>
                <button onclick="mostrar('sociosVoluntarios')"><img src="iconos/usuario-azul.png" alt="Usuario">Socios voluntarios</button>
                <button onclick="mostrar('carreras')"><img src="iconos/graduacion.png" alt="Carreras">Carreras</button>
                <button onclick="mostrar('cooperadora')"><img src="iconos/dinero.png" alt="Cooperadora">Cooperadora actual</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/dinero.png" alt="Pagos">Ver pagos</button>
                <button onclick="mostrar('graficos')"><img src="iconos/graficos.png" alt="Gráficos">Gráficos</button>
            </nav>
        </aside>

        <!-- Contenido -->
        <main class="contenido">
            <!-- Inicio -->
            <div id="inicio" class="formulario activo tarjeta">
                <div class="cards">
                    <div class="card-chica">
                        <div class="imgs">
                            <img src="iconos/usuario-verde.png" alt="Alumnos" class="card-icono">
                            <div id="cantAlumnos" class="card-numero"></div>
                        </div>
                        <span class="card-etiqueta">Socio alumnos</span>
                    </div>

                    <div class="card-chica">
                        <div class="imgs">
                            <img src="iconos/usuario-verde.png" alt="Voluntarios" class="card-icono">
                            <div id="cantVoluntarios" class="card-numero"></div>
                        </div>
                        <span class="card-etiqueta">Socio voluntarios</span>
                    </div>

                    <div class="card-chica">
                        <div class="imgs">
                            <img src="iconos/graduacion-verde.png" alt="Carreras" class="card-icono">
                            <div id="cantCarreras" class="card-numero"></div>
                        </div>
                        <span class="card-etiqueta">Carreras</span>
                    </div>

                    <div class="card-chica">
                        <div class="imgs">
                            <img src="iconos/dinero-verde.png" alt="Cooperadora" class="card-icono">
                            <div id="montoActual" class="card-numero"></div>
                        </div>
                        <span class="card-etiqueta">Cooperadora actual</span>
                    </div>
                </div>
                
                <div class="listados">
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio alumnos</h4>
                            <button class="ver-todos" onclick="mostrar('sociosAlumnos')">Ver todos</button>
                        </div>
                        <div class="listado-item">
                            <span class="item-email">nombre@gmail.com</span>
                        </div>
                    </div>
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio voluntario</h4>
                            <button class="ver-todos" onclick="mostrar('sociosVoluntarios')">Ver todos</button>
                        </div>
                        <div class="listado-item">
                            <span class="item-email">nombre@gmail.com</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secciones Secundarias -->
            <div id="sociosAlumnos" class="formulario tarjeta">
                <h3>Socios alumnos</h3>
            </div>

            <div id="sociosVoluntarios" class="formulario tarjeta">
                <h3>Socios voluntarios</h3>
            </div>

            <div id="carreras" class="formulario tarjeta">
                <h3>Carreras</h3>
            </div>

            <!-- Cooperadora (Oculto hasta activar) -->
            <div id="cooperadora" class="formulario cooperadora-card">
                <div class="cooperadora-header">
                    <button class="btn-editar" onclick="mostrar('editarCooperadora')">Editar</button>
                    <h2 class="titulo-cooperadora">Cooperadora actual</h2>
                </div>

                <table class="tabla-cooperadora">
                    <thead>
                        <tr>
                            <th>Importe</th>
                            <th>Importe anterior</th>
                            <th>Fecha de guardado</th>
                            <th>Fecha de efecto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$$$$</td>
                            <td>$$$$</td>
                            <td>XX-XX-XXXX</td>
                            <td>XX-XX-XXXX</td>
                        </tr>
                    </tbody>
                </table>
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
                            <td colspan="8" class="text-center">Cargando pagos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="graficos" class="formulario tarjeta">
                <h3>Gráficos</h3>
            </div>
        </main>
    </div>
    <script src="panel.js"></script>
</body>
</html>