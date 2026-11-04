<?php
require_once __DIR__ . '/../php/conexion.php';

$mensajes = [];
$falloBD  = false;

try {
    $conexion = conectarBD();

    $sql = 'SELECT id, nombre_completo, correo, asunto, mensaje, fecha_registro
            FROM mensajes
            ORDER BY fecha_registro DESC, id DESC';
    $resultado = $conexion->query($sql);

    while ($fila = $resultado->fetch_assoc()) {
        $mensajes[] = $fila;
    }

    $resultado->free();
    $conexion->close();
} catch (mysqli_sql_exception $e) {
    $falloBD = true;
    error_log('Error al consultar los mensajes: ' . $e->getMessage());
}

$total = count($mensajes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Fútbol - Mensajes</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedor">

        <header class="cabecera">
            <h1><a href="../index.html">Mundo Fútbol</a></h1>
            <p>Buzón de mensajes</p>
            <a class="boton" href="contacto.html">Escríbenos</a>
        </header>

        <nav class="barra">
            <ul>
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="historia.html">Historia</a></li>
                <li><a href="reglas.html">Reglas</a></li>
                <li><a href="posiciones.html">Posiciones</a></li>
                <li><a href="mundiales.html">Mundiales</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="mensajes.php" class="actual">Mensajes</a></li>
            </ul>
        </nav>

        <div class="cuerpo">

            <nav class="menu">
                <p>Temas del sitio</p>
                <ul>
                    <li><a href="../index.html">Inicio</a></li>
                    <li><a href="historia.html">Historia</a></li>
                    <li><a href="reglas.html">Reglas</a></li>
                    <li><a href="posiciones.html">Posiciones</a></li>
                    <li><a href="mundiales.html">Mundiales</a></li>
                    <li><a href="contacto.html">Contacto</a></li>
                    <li><a href="mensajes.php" class="actual">Mensajes</a></li>
                </ul>
            </nav>

            <main class="contenido">
                <p class="pasos"><a class="boton secundario" href="contacto.html">Anterior: Contacto</a></p>
                <section>
                    <h2>Mensajes recibidos</h2>

                    <?php if ($falloBD): ?>
                        <article class="aviso error">
                            <h3>No se pudo consultar la base de datos</h3>
                            <p>
                                La base de datos no respondió, por eso no se pueden mostrar los mensajes.
                                Vuelve a intentarlo en unos minutos.
                            </p>
                        </article>

                    <?php else: ?>
                        <p id="total-mensajes">Total de mensajes registrados: <strong><?php echo $total; ?></strong></p>

                        <?php if ($total === 0): ?>
                            <article class="aviso informacion">
                                <h3>Todavía no existen mensajes</h3>
                                <p>
                                    Este espacio espera el primer mensaje. Envíalo desde la página de
                                    <a href="contacto.html">Contacto</a>.
                                </p>
                            </article>

                        <?php else: ?>
                            <div class="tabla-desplazable">
                                <table id="tabla-mensajes">
                                    <thead>
                                        <tr>
                                            <th>N.º</th>
                                            <th>Nombre completo</th>
                                            <th>Correo electrónico</th>
                                            <th>Asunto</th>
                                            <th>Mensaje</th>
                                            <th>Fecha de registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mensajes as $fila): ?>
                                            <tr>
                                                <td><?php echo (int) $fila['id']; ?></td>
                                                <td><?php echo htmlspecialchars($fila['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($fila['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($fila['asunto'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo nl2br(htmlspecialchars($fila['mensaje'], ENT_QUOTES, 'UTF-8')); ?></td>
                                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($fila['fecha_registro'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <p><a class="boton" href="contacto.html">Enviar un mensaje</a></p>
                </section>

                <aside>
                    <h2>Sobre esta consulta</h2>
                    <p>
                        Los mensajes se muestran del más reciente al más antiguo. Todo lo que escribieron
                        los usuarios se muestra <strong>como texto</strong>, aunque contenga etiquetas HTML.
                    </p>
                </aside>

                <p class="pasos"><a class="boton secundario" href="contacto.html">Anterior: Contacto</a></p>

            </main>
        </div>

        <footer class="pie">
            <p>Sitio desarrollado por <strong>Erick Bermeo</strong> en Azogues, Ecuador.</p>
            <p>Correo: <a href="mailto:ebermeol1@est.ups.edu.ec">ebermeol1@est.ups.edu.ec</a></p>
            <p>&copy; 2026 Mundo Fútbol. Todos los derechos reservados.</p>
        </footer>

    </div>
</body>
</html>
