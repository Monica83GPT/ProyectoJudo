# Instrucciones del proyecto

- Para cualquier tarea de este proyecto, usar como stack principal: HTML, CSS, JavaScript, PHP y MariaDB.
- Evitar proponer frameworks, lenguajes o bases de datos diferentes salvo que el usuario lo pida explicitamente.
- Mantener siempre HTML, CSS y JavaScript en archivos separados: no escribir estilos dentro de las etiquetas `<style>` del HTML ni scripts dentro de `<script>` salvo una necesidad técnica justificada.
- Consultar y actualizar `PENDIENTES.md` cuando el usuario pida apuntar, recordar o revisar tareas para hacer despues.

## Arquitectura obligatoria

- No salirse de la estructura MVC actual, basada en la carpeta de referencia `v04b`. No crear una arquitectura paralela, nuevas capas ni carpetas alternativas.
- Los controladores y únicos puntos de entrada web deben estar en `public/` y tener extensión `.php`.
- Las plantillas deben estar en `views/`, ser HTML sin PHP y recibir los datos mediante las marcas y bloques del renderizador `_render.php`.
- Mantener las cuatro capas existentes dentro de `models/`:
  - `mod001_kernel.php`: conexión PDO, sesiones e infraestructura básica.
  - `mod002_dataaccess.php`: ejecución de consultas, sentencias y transacciones.
  - `mod003_logic.php`: validación y lógica de negocio.
  - `mod004_presentation.php`: preparación de datos y renderizado de plantillas.
- Los ficheros auxiliares compartidos de `models/` deben comenzar por guion bajo, como `_render.php` y `_access.php`; no son capas nuevas.
- Todas las consultas SQL usadas por la aplicación deben declararse en `config/queries.php`. Los controladores y las vistas no pueden consultar directamente la base de datos.
- La configuración local debe permanecer en `config/database.php` y el modelo publicable en `config/database.example.php`.
- Los CSS, JavaScript, imágenes y demás recursos accesibles desde el navegador deben estar dentro de `public/`, en sus carpetas correspondientes.
- Los scripts de creación, migración y datos de MariaDB deben guardarse en `BBDD/`. El material anterior o sustituido debe conservarse en `BBDD/Antiguo/`.
- Las páginas nuevas deben reutilizar esta estructura y ampliar las capas existentes; no mover lógica al controlador, SQL al modelo lógico, PHP a las vistas ni estilos o scripts al HTML.
