<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="panel.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<body>
    <div class="contenedor">
        <!-- Sidebar -->
        <aside class="sideBar">
            <div class="logo">
                <a href="inicio.php">
                    <img src="iconos/logo.jpg" alt="Logo">
                </a>
            </div>
            <nav>
                <button onclick="mostrar('inicio')"><img src="iconos/proximo.png" alt="" style = "width: 20%">Inicio</button>
                <button onclick="mostrar('alumno'); cargarAlumnos()"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 17%">Socios alumnos</button>
                <button onclick="mostrar('voluntario'); cargarVoluntarios()"><img src="iconos/usuario-azul.png" alt="Usuario" style= "width: 17%">Socios voluntarios</button>
                <button onclick="mostrar('carreras'); cargarCarreras()"><img src="iconos/graduacion.png" alt="Carreras" style= "width: 19%">Carreras</button>
                <button onclick="mostrar('cooperadora')"><img src="iconos/dinero.png" alt="Cooperadora" style= "width: 19%">Cooperadora actual</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/dinero.png" alt="Pagos" style= "width: 19%">Ver pagos</button>
                <button onclick="mostrar('graficos'); cargarGraficos()"><img src="iconos/graficos.png" alt="Gráficos" style= "width: 18%">Gráficos</button>
                <button onclick="mostrar('descargarDatos')"><img src="iconos/descargarDatos.png" alt="Descargar" style= "width: 17%">Descargar datos</button>
            </nav>
        </aside>

        <!-- APARTADOS -->
        <main class="contenido">
            <!-- Inicio -->
            <div id="inicio" class="formulario activo tarjeta">
                <div class="cards">
                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/usuario-verde.png" alt="usuario">
                            <div id="cantAlumnos" class="card-numero"></div>
                        </div>
                        Socio alumnos
                        <p id="mensajeAlumnos" style="text-align:center; font-family:Tamrin; font-size:6px; color: red;"></p>
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/usuario-verde.png" alt="usuario">
                            <div id="cantVoluntarios" class="card-numero"></div>
                        </div>
                        Socio voluntarios
                        <p id="mensajeVoluntarios" style="text-align:center; font-family:Tamrin; font-size:6px; color: red;"></p>
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/graduacion-verde.png" alt="carreras">
                            <div id="cantCarreras" class="card-numero"></div>
                        </div>
                        Carreras
                        <p id="mensajeCarrera" style="text-align:center; font-family:Tamrin; font-size:6px; color: red;"></p>
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/dinero-verde.png" alt="dinero">
                            <div id="montoActual" class="card-numero"></div>
                        </div>
                        Cooperadora actual
                        <p id="mensajeMonto" style="text-align:center; font-family:Tamrin; font-size:6px; color: red;"></p>
                    </div>
                </div>

                <div class="listados">
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio alumno</h4>
                            <button class="btn-editar" onclick="mostrar('alumno')">Ver todos</button>
                        </div>
                        <div class="listado-item" id="socAlumno">
                        </div>
                    </div>
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio voluntario</h4>
                            <button class="btn-editar" onclick="mostrar('voluntario')">Ver todos</button>
                        </div>
                        <div class="listado-item" id="socVoluntario">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Socios -->
            <div id="alumno" class="formulario">
                <input type="text" id="barraBusquedaAlumnos" placeholder="Buscar por DNI o nombre completo..." onkeydown="if(event.key === 'Enter') buscarUsuariosAlumnos()">

                <div id="sociosAlumnos">
                    <div class="socios-header">
                        <h3>Socios alumnos</h3>
                    </div>
                    <p id="mensajeSociosAlumnos" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                    <table>
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Marcar como inactivo</th>
                            </tr>
                        </thead>
                        <tbody id="tablasocios-Alumno"></tbody>
                    </table>
                </div>
            </div>

            <div id="voluntario" class="formulario">
                 <input type="text" id="barraBusquedaVoluntarios" placeholder="Buscar por DNI o nombre completo..." onkeydown="if(event.key === 'Enter') buscarUsuariosVoluntarios()">
                
                 <div id="sociosVoluntarios">
                
                    <div class="socios-header">
                        <h3>Socios voluntarios</h3>
                    </div>
                    <p id="mensajeSociosVoluntarios" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                    <table>
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Marcar como inactivo</th>
                            </tr>
                        </thead>
                        <tbody id="tablasocios-Voluntario"></tbody>
                    </table>
                </div>
            </div>

            <div id="editarDni" class="formulario">
                <h3>Editar DNI</h3>
                <div class="editar-dni-form">
                    <label for="nuevoDni">DNI</label>
                    <input type="text" id="nuevoDni" placeholder="Nuevo DNI">
                    <button class="btn-editar" onclick="guardarDni()">Guardar cambios</button>
                    <p id="mensajeEditarDni"></p>
                </div>
                
            </div>
            
            <!-- Carreras -->
            <div id="carreras" class="formulario">

                <!-- Vista 1: Lista / Tabla de Carreras -->
                <div id="vistaTablaCarreras">
                    <div class="cooperadora-header">
                        <h2 class="titulo-cooperadora">Carreras Actuales</h2>
                        <button type="button" class="btn-editar" onclick="mostrarFormularioCarrera()">Agregar</button>
                    </div>

                    <table class="tabla-carreras">
                        <thead>
                            <tr>
                                <th>Carreras</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCarreras"></tbody>
                    </table>
                    <p id="mensajeCarreras" style="text-align:center; font-family:Tamrin; font-size:12px; color: red; margin-top: 10px;"></p>
                </div>

                <!-- Vista 2: Formulario de Agregar / Editar (Oculto por defecto) -->
                <div id="formularioCarrera" style="display: none; flex-direction: column; gap: 15px; width: 100%;">
                    <h2 id="tituloFormularioCarrera" class="titulo-cooperadora" style="text-align: center;">Editar carrera</h2>
                    
                    <!-- Contenedor del campo de texto a ancho completo -->
                    <div style="width: 100%;">
                        <input type="text" id="nombreCarrera" placeholder="Nombre de la carrera" aria-label="Nombre de la carrera" style="width: 100%; box-sizing: border-box; padding: 10px; font-size: 16px;">
                    </div>

                    <!-- Botones de Acción -->
                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                        <button type="button" class="btn-editar" onclick="guardarCarrera()">Guardar cambios</button>
                        <button type="button" class="btn-cancelar" onclick="cancelarFormularioCarrera()" style="background: #e74c3c; color: white;">Cancelar</button>
                    </div>
                </div>

            </div>

            <!-- Cooperadora -->
            <div id="cooperadora" class="formulario tarjeta cooperadora-card">
                <div class="cooperadora-header">
                    <button class="btn-editar" id="editar" onclick="editarMonto()">Editar</button>
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
                    <tbody id="tablaMonto">
                        </tbody>
                    </table>
                    <p id="mensajeCooperadora" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>

                <div class="cooperadora-acciones-bottom">
                    <button class="btn-cancelar" id="cancelar" onclick="cancelarEdicionMonto()" style="display: none;">Cancelar</button>
                </div>
            </div>

            <!-- Ver pagos -->
            <div id="verPagos" class="formulario tarjeta pagos-card">
                <div class="pagos-header">
                    <h2 class="titulo-pagos">Pagos registrados</h2>
                </div>
                <p id="mensajePagos" class="msj-error"></p>
                <!-- Filtros de pagos -->
                <div class="filtros-pagos">
                    <div class="filtro-grupo">
                        <label for="filtroMes">Mes:</label>
                        <select id="filtroMes">
                            <option value="">Todos los meses</option>
                            <option value="Marzo">Marzo</option>
                            <option value="Abril">Abril</option>
                            <option value="Mayo">Mayo</option>
                            <option value="Junio">Junio</option>
                            <option value="Julio">Julio</option>
                            <option value="Agosto">Agosto</option>
                            <option value="Septiembre">Septiembre</option>
                            <option value="Octubre">Octubre</option>
                            <option value="Noviembre">Noviembre</option>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="filtroEstado">Estado:</label>
                        <select id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="1">Pagado</option>
                            <option value="2">Impago</option>
                            <option value="3">Pendiente</option>
                        </select>
                    </div>

                    <button type="button" class="filtrar" onclick="filtrarPagos()">
                        <img id="iconoFiltro" src="iconos/filtro-vacio.png" alt="Filtro vacío" width="24" height="24"><br>
                        Filtrar 
                    </button>

                    <button type="button" class="limpiar-filtros" onclick="limpiarFiltrosPagos()"><br>
                        Limpiar <br>
                        filtros 
                    </button>
                </div>
                <p id="mensajeFiltrosPagos"></p>

                <div class="tabla-pagos-contenedor">
                    <table class="tabla-pagos">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Monto</th>
                                <th>Comprobante</th>
                                <th>Estado</th>
                                <th>Tipo Socio</th>
                                <th>Carrera</th>
                            </tr>
                        </thead>
                        <tbody id="tablapagos">
                            <tr>
                                <td colspan="8" style="text-align: center;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gráficos -->
            <div id="graficos" class="formulario">
                <div class="contenedor-graficos">
                    <div class="card-grafico">
                        <h3>Usuarios Registrados que Pagan</h3>
                        <div class="canvas-box">
                            <canvas id="chartUsuariosPagan"></canvas>
                            <p id="mensajeGrafico1" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                        </div>
                    </div>

                    <div class="card-grafico">
                        <h3>Usuarios por Carrera que Pagan</h3>
                        <div class="canvas-box">
                            <canvas id="chartCarrerasPagan"></canvas>
                            <p id="mensajeGrafico2" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                        </div>
                    </div>

                    <div class="card-grafico card-grafico-centrado">
                        <h3>Socios Voluntarios que Pagan</h3>
                        <div class="canvas-box">
                            <canvas id="chartVoluntariosPagan"></canvas>
                            <p id="mensajeGrafico3" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descargar datos -->
            <div id="descargarDatos" class="formulario tarjeta descargar-card">
                <div class="descargar-header">
                    <h2 class="titulo-descargar">Descargar datos</h2>
                </div>
                
                <div class="descargar-body">
                    <div class="grupo-descargar">
                        <label for="inicio" class="descargar-label">Desde:</label>
                        <input type="date" class="input-descargar" id="fecha_inicio">
                    </div>

                    <div class="grupo-descargar">
                        <label for="final" class="descargar-label">Hasta:</label>
                        <input type="date" class="input-descargar" id="fecha_final">
                    </div>
                </div>
                
                <div class="descargar-acciones-button">
                    <button type="button" onclick="exportarExcelPagos()" class="btn-exportar">Exportar pagos</button>
                    <button type="button" onclick="exportarExcelUsuarios()" class="btn-exportar">Exportar usuarios</button>
                </div>

                <p id="mensajeDescargar" style="text-align:center; font-family:Tamrin; font-size:12px; color: yellow; margin-top: 10px;"></p>
            </div>
        </main>
    </div>
    <script src="panel.js"></script>
</body>
</html>