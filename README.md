# Dulceria El Pinguinito

Sistema web para la administracion de una dulceria. Incluye inicio de sesion, panel principal, inventario, punto de venta, gestion de empleados, detalle de ventas y analisis financiero.

## Tecnologias

- PHP
- MySQL / MariaDB
- HTML, CSS y JavaScript
- XAMPP como entorno local recomendado
- Google reCAPTCHA v3 opcional en el inicio de sesion

## Requisitos

- XAMPP instalado
- Apache activo
- MySQL activo
- Navegador web

## Instalacion local

1. Copia o clona este proyecto dentro de la carpeta `htdocs` de XAMPP:

```bash
C:\xampp\htdocs\dulceriav2
```

2. Inicia Apache y MySQL desde el panel de control de XAMPP.

3. Abre phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Crea una base de datos llamada:

```sql
dulceria_pinguinito
```

5. Importa el archivo SQL incluido en el proyecto:

```text
database/dulceria_pinguinito.sql
```

6. Revisa la conexion a la base de datos en:

```text
includes/db.php
```

Por defecto usa esta configuracion:

```php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dulceria_pinguinito";
```

7. Abre el sistema en el navegador:

```text
http://localhost/DULCERIAV2/
```

> Nota: Las rutas del proyecto usan `/DULCERIAV2/`. Si cambias el nombre de la carpeta, tambien debes actualizar esas rutas en los archivos PHP, CSS o JS donde aparezcan.

## Estructura del proyecto

```text
dulceriav2/
|-- actions/        # Acciones del sistema, como cerrar sesion o eliminar productos
|-- assets/         # Imagenes y recursos visuales
|-- css/            # Hojas de estilo
|-- database/       # Respaldo e importacion de la base de datos
|-- includes/       # Archivos reutilizables, como la conexion a MySQL
|-- js/             # Scripts del frontend
|-- pages/          # Modulos principales del sistema
`-- index.php       # Pantalla de inicio de sesion
```

## Modulos principales

- Inicio de sesion con validacion de usuario.
- Dashboard principal.
- Inventario de productos.
- Alta, edicion y eliminacion de productos.
- Punto de venta con registro de ventas.
- Gestion de empleados.
- Detalle de ventas.
- Analisis financiero.
- Control de acceso por rol de usuario.

## Base de datos

El archivo principal para importar la base de datos esta en:

```text
database/dulceria_pinguinito.sql
```

La base incluida contiene datos demo para probar el sistema sin usar informacion real.

## Usuarios demo

Administrador:

```text
Correo: admin@demo.com
Contrasena: admin123
```

Empleado:

```text
Correo: empleado@demo.com
Contrasena: empleado123
```

Tablas principales:

- `usuarios`
- `productos`
- `ventas`
- `detalles_venta`

## reCAPTCHA v3

El inicio de sesion funciona sin reCAPTCHA en la version demo. El codigo de reCAPTCHA esta comentado en `index.php`.

Para activarlo:

1. Entra a Google reCAPTCHA Admin Console.
2. Registra el dominio donde se ejecutara el sistema.
3. Selecciona reCAPTCHA v3.
4. Copia la clave del sitio y la clave secreta.
5. En `index.php`, busca `TU_SITE_KEY` y reemplazalo por la clave del sitio.
6. En `index.php`, busca `TU_SECRET_KEY` y reemplazalo por la clave secreta.
7. Descomenta estos tres bloques:

```text
script de Google reCAPTCHA
input hidden recaptcha_response
bloque PHP que valida el token con siteverify
```

Con reCAPTCHA activo, el formulario envia el token en `recaptcha_response` y PHP lo valida antes de iniciar sesion.

## Comandos basicos para subir a GitHub

Desde la raiz del proyecto:

```bash
git init
git add .
git commit -m "Primer commit"
git branch -M main
git remote add origin https://github.com/USUARIO/NOMBRE-DEL-REPOSITORIO.git
git push -u origin main
```

Reemplaza `USUARIO` y `NOMBRE-DEL-REPOSITORIO` por los datos de tu repositorio en GitHub.

## Autor

Proyecto desarrollado por Javier Mendoza Mondragón para la gestion de la Dulceria El Pinguinito.
