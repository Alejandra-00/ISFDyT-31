<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="panel.css">
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
                <button onclick="mostrar('carreras')"><img src="iconos/graduacion.png" alt="Carreras" style= "width: 19%">Carreras</button>
                <button onclick="mostrar('cooperadora')"><img src="iconos/dinero.png" alt="Cooperadora" style= "width: 19%">Cooperadora actual</button>
                <button onclick="mostrar('verPagos')"><img src="iconos/dinero.png" alt="Pagos" style= "width: 19%">Ver pagos</button>
                <button onclick="mostrar('graficos')"><img src="iconos/graficos.png" alt="Gráficos" style= "width: 18%">Gráficos</button>
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
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/usuario-verde.png" alt="usuario">
                            <div id="cantVoluntarios" class="card-numero"></div>
                        </div>
                        Socio voluntarios
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/graduacion-verde.png" alt="carreras">
                            <div id="cantCarreras" class="card-numero"></div>
                        </div>
                        Carreras
                    </div>

                    <div class="card-chica">
                        <div class="valor-icono">
                            <img src="iconos/dinero-verde.png" alt="dinero">
                            <div id="montoActual" class="card-numero"></div>
                        </div>
                        Cooperadora actual
                    </div>
                </div>

                <div class="listados">
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio alumno</h4>
                            <button class="ver-todos" onclick="mostrar('alumno')">Ver todos</button>
                        </div>
                        <div class="listado-item" id="socAlumno">
                        </div>
                    </div>
                    <div class="card-listado">
                        <div class="listado-header">
                            <h4>Socio voluntario</h4>
                            <button class="ver-todos" onclick="mostrar('voluntario')">Ver todos</button>
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
            
            <!-- Sección Carreras -->
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
                        <p id="mensajeCooperadora" style="text-align:center; font-family:Tamrin; font-size:12px; color: red;"></p>
                    </tbody>
                </table>

                <div class="cooperadora-acciones-bottom">
                    <button class="btn-cancelar" id="cancelar" onclick="cancelarEdicionMonto()" style="display: none;">Cancelar</button>
                </div>
            </div>

            <!-- Ver pagos -->
            <div id="verPagos" class="formulario tarjeta pagos-card">
                <div class="pagos-header">
                    <h2 class="titulo-pagos">Pagos registrados</h2>
                </div>
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
                                <td colspan="8" style="text-align: center;">Cargando pagos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="visorComprobante" class="visor-comprobante" onclick="cerrarComprobante()">
                    <span class="cerrar-comprobante">&times;</span>
                    <img id="imagenComprobante" class="imagen-comprobante-grande" onclick="event.stopPropagation()">
                </div>

            <!-- Gráficos -->
            <div id="graficos" class="formulario tarjeta">
                <h3>Gráficos</h3>
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