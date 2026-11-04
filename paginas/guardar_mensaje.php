<?php

require_once __DIR__ . '/../php/conexion.php';

const MAX_NOMBRE  = 100;
const MAX_CORREO  = 150;
const MAX_ASUNTO  = 150;
const MAX_MENSAJE = 2000;

function obtenerCampo(string $campo): string
{
    $valor = $_POST[$campo] ?? '';

    if (!is_string($valor)) {
        return '';
    }

    return trim($valor);
}

function contarCaracteres(string $texto): int
{
    return mb_strlen($texto, 'UTF-8');
}

function validarTexto(string $valor, string $etiqueta, int $maximo, array &$errores): void
{
    if ($valor === '') {
        $errores[] = "El campo \"$etiqueta\" es obligatorio y no puede estar vacío ni contener solo espacios.";
    } elseif (contarCaracteres($valor) > $maximo) {
        $errores[] = "El campo \"$etiqueta\" admite máximo $maximo caracteres y escribiste "
                   . contarCaracteres($valor) . '.';
    }
}

function dominioRecibeCorreos(string $correo): bool
{
    $dominio = substr(strrchr($correo, '@'), 1);
    if (function_exists('idn_to_ascii')) {
        $dominio = idn_to_ascii($dominio) ?: $dominio;
    }
    return checkdnsrr($dominio, 'MX') || checkdnsrr($dominio, 'A');
}

$errores  = [];
$guardado = false;
$falloBD  = false;
$idNuevo  = 0;
$nombre   = '';
$asunto   = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errores[] = 'Esta página solo recibe mensajes enviados desde el formulario de contacto.';
} else {
    $nombre  = obtenerCampo('nombre');
    $correo  = obtenerCampo('correo');
    $asunto  = obtenerCampo('asunto');
    $mensaje = obtenerCampo('mensaje');

    validarTexto($nombre, 'Nombre completo', MAX_NOMBRE, $errores);

    validarTexto($correo, 'Correo electrónico', MAX_CORREO, $errores);
        if ($correo !== '' && contarCaracteres($correo) <= MAX_CORREO) {
        if (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            $errores[] = 'El correo electrónico no tiene un formato válido. Ejemplo: nombre@dominio.com';
        } elseif (!dominioRecibeCorreos($correo)) {
            $errores[] = 'El dominio del correo (lo que va después de la @) no existe o no recibe correos.';
        }
    }

    validarTexto($asunto, 'Asunto', MAX_ASUNTO, $errores);
    validarTexto($mensaje, 'Mensaje', MAX_MENSAJE, $errores);

    if (count($errores) === 0) {
        try {
            $conexion = conectarBD();

            $sql = 'INSERT INTO mensajes (nombre_completo, correo, asunto, mensaje) VALUES (?, ?, ?, ?)';
            $consulta = $conexion->prepare($sql);
            $consulta->bind_param('ssss', $nombre, $correo, $asunto, $mensaje);
            $consulta->execute();

            if ($consulta->affected_rows === 1) {
                $guardado = true;
                $idNuevo  = $conexion->insert_id;
            } else {
                $falloBD = true;
            }

            $consulta->close();
            $conexion->close();
        } catch (mysqli_sql_exception $e) {
            $falloBD = true;
            error_log('Error al guardar el mensaje: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Fútbol - Resultado del envío</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedor">

        <header class="cabecera">
            <h1><a href="../index.html">Mundo Fútbol</a></h1>
            <p>Resultado del envío</p>
            <a class="boton" href="contacto.html">Escríbenos</a>
        </header>

        <nav class="barra">
            <ul>
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="historia.html">Historia</a></li>
                <li><a href="reglas.html">Reglas</a></li>
                <li><a href="posiciones.html">Posiciones</a></li>
                <li><a href="mundiales.html">Mundiales</a></li>
                <li><a href="contacto.html" class="actual">Contacto</a></li>
                <li><a href="mensajes.php">Mensajes</a></li>
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
                    <li><a href="contacto.html" class="actual">Contacto</a></li>
                    <li><a href="mensajes.php">Mensajes</a></li>
                </ul>
            </nav>

            <main class="contenido">
                <section>
                    <h2>Resultado del envío</h2>

                    <?php if ($guardado): ?>
                        <article class="aviso exito">
                            <h3>¡Gol! Tu mensaje quedó guardado</h3>
                            <p>
                                Gracias, <strong><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></strong>.
                                Tu mensaje con el asunto
                                "<em><?php echo htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8'); ?></em>"
                                quedó registrado con el número <strong><?php echo $idNuevo; ?></strong>.
                            </p>
                        </article>

                    <?php elseif ($falloBD): ?>
                        <article class="aviso error">
                            <h3>No se pudo guardar el mensaje</h3>
                            <p>
                                Tus datos están bien, pero la base de datos no respondió y el mensaje
                                <strong>no</strong> se registró. Vuelve a intentarlo en unos minutos.
                            </p>
                        </article>

                    <?php else: ?>
                        <article class="aviso error">
                            <h3>El mensaje no se guardó</h3>
                            <p>Corrige estos datos y vuelve a enviarlo:</p>
                            <ul>
                                <?php foreach ($errores as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endif; ?>

                    <p>
                        <a class="boton" href="contacto.html">Volver al formulario</a>
                        <a class="boton" href="mensajes.php">Ver los mensajes</a>
                    </p>
                </section>

                <aside>
                    <h2>Recuerda</h2>
                    <p>
                        Cada campo tiene un límite: nombre <strong>100</strong>, correo <strong>150</strong>,
                        asunto <strong>150</strong> y mensaje <strong>2000</strong> caracteres.
                    </p>
                </aside>


            </main>
        </div>

        <footer class="pie">
            <p>Sitio desarrollado por <strong>Erick Bermeo</strong> en Cuenca, Ecuador.</p>
            <p>Correo: <a href="mailto:ebermeol1@est.ups.edu.ec">ebermeol1@est.ups.edu.ec</a></p>
            <p>&copy; 2026 Mundo Fútbol. Todos los derechos reservados.</p>
        </footer>

    </div>
</body>
</html>
