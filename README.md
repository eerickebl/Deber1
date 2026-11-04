# Mundo Fútbol: sitio web con formulario de contacto

Sitio web sobre fútbol hecho con **HTML5 y CSS**, con un **formulario de contacto** que guarda los mensajes en una base de datos **MySQL** mediante **PHP**, y una página que los consulta.

- **Autor:** Erick Bermeo ([ebermeol1@est.ups.edu.ec](mailto:ebermeol1@est.ups.edu.ec))
- **Asignatura:** Programación y Plataformas Web, Universidad Politécnica Salesiana (sede Cuenca)

## Descripción

El sitio tiene cinco páginas informativas (Inicio, Historia, Reglas, Posiciones y Mundiales) y dos funciones dinámicas:

1. **Contacto** (`paginas/contacto.html`): el usuario escribe su nombre completo, su correo, un asunto y un mensaje. Los datos se envían por **POST**.
2. **Guardar mensaje** (`paginas/guardar_mensaje.php`): valida los datos en el servidor y los guarda con una **consulta preparada de MySQLi**. La confirmación aparece solo si el registro se guardó.
3. **Mensajes** (`paginas/mensajes.php`): consulta la base de datos y muestra todos los mensajes en una tabla HTML. Si no hay registros, lo informa.

Relación con MVC:

| Componente | Archivo |
|---|---|
| Vista | `paginas/contacto.html` y las páginas HTML |
| Controlador | `paginas/guardar_mensaje.php` y `paginas/mensajes.php` |
| Modelo | `php/conexion.php` y la tabla `mensajes` en MySQL |

### Diseño

El sitio tiene el aspecto de una página de tutoriales: una cabecera blanca con el logo y un botón para escribir, una franja oscura con todas las secciones, un menú lateral gris con la página actual marcada en verde, el contenido en recuadros y botones verdes de **Anterior** y **Siguiente** para recorrer las páginas en orden. En el celular el menú lateral se oculta y queda solo la franja oscura.

## Requisitos

- [XAMPP](https://www.apachefriends.org/) con **Apache**, **MySQL/MariaDB** y **PHP 8** (las extensiones `mysqli` y `mbstring` ya vienen activadas en XAMPP).
- Un navegador web actualizado (Chrome, Edge o Firefox).

## Estructura del proyecto

```
Deber1/
├── index.html                  Página principal
├── README.md
├── css/
│   └── estilos.css             Hoja de estilos externa (todas las páginas)
├── images/                     Las 5 ilustraciones del sitio
├── paginas/
│   ├── historia.html
│   ├── reglas.html
│   ├── posiciones.html
│   ├── mundiales.html          Tabla, listas y video (iframe)
│   ├── contacto.html           Formulario de contacto (Vista)
│   ├── guardar_mensaje.php     Validación y almacenamiento (Controlador)
│   └── mensajes.php            Consulta de mensajes
├── php/
│   └── conexion.php            Datos y función de conexión (Modelo)
└── database/
    └── mundo_futbol.sql        Script para crear la base de datos
```

## Base de datos

Base de datos `mundo_futbol` (utf8mb4) con la tabla `mensajes`:

| Campo | Tipo | Restricciones |
|---|---|---|
| `id` | `INT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` |
| `nombre_completo` | `VARCHAR(100)` | `NOT NULL`, no vacío (`CHECK`) |
| `correo` | `VARCHAR(150)` | `NOT NULL`, debe contener `@` y un punto (`CHECK`) |
| `asunto` | `VARCHAR(150)` | `NOT NULL`, no vacío (`CHECK`) |
| `mensaje` | `VARCHAR(2000)` | `NOT NULL`, no vacío (`CHECK`) |
| `fecha_registro` | `DATETIME` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`, índice |

## Instalación y ejecución en XAMPP

1. **Copia el proyecto** a la carpeta `htdocs` de XAMPP:
   ```
   C:\xampp\htdocs\Deber1\
   ```
2. **Inicia los servicios:** abre el *XAMPP Control Panel* y presiona **Start** en **Apache** y en **MySQL**.
3. **Crea la base de datos:**
   1. Abre `http://localhost/phpmyadmin`.
   2. Entra a la pestaña **Importar**.
   3. Selecciona el archivo `database/mundo_futbol.sql` y presiona **Importar** (o **Continuar**).
   4. Verifica que aparezca la base de datos `mundo_futbol` con la tabla `mensajes`.
4. **Abre el sitio:**
   ```
   http://localhost/Deber1/
   ```
5. **Prueba el formulario:** entra a **Contacto**, envía un mensaje y revísalo en **Mensajes**.

### Configuración de la conexión

Los datos de conexión están en `php/conexion.php` y usan los valores por defecto de XAMPP:

```php
const DB_SERVIDOR = 'localhost';
const DB_USUARIO  = 'root';
const DB_CLAVE    = '';
const DB_NOMBRE   = 'mundo_futbol';
const DB_PUERTO   = 3306;
```

Si tu MySQL tiene otra contraseña o usa otro puerto (por ejemplo `3307`), cámbialo ahí. No subas al repositorio contraseñas personales.

## Seguridad aplicada

- **Consultas preparadas** (`prepare` y `bind_param`): los datos nunca se pegan dentro del SQL, lo que evita la inyección SQL.
- **`htmlspecialchars`** al imprimir: si alguien escribe etiquetas HTML, se muestran como texto y no se ejecutan.
- **Correo con dominio real:** además del formato, PHP consulta en internet (DNS, con `checkdnsrr`) que el dominio del correo exista y reciba correos. Así se rechazan correos inventados como `ana@asdfqwer.com`. Por eso la PC necesita conexión a internet para guardar mensajes.
- **Validación doble:** en el navegador (`required`, `type="email"` y `maxlength`) y en el servidor con PHP, que es la que realmente protege. Además, la base de datos tiene restricciones `CHECK`.
- **Errores controlados:** si MySQL falla, el usuario ve un mensaje comprensible y nunca una confirmación falsa.
