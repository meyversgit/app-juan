GAMEZONE STORE - PHP + MYSQL + VERIFICACION REAL DE CORREO

1. Copia la carpeta gamezone_real como:
   C:\xampp\htdocs\gamezone_store

2. Inicia Apache y MySQL en XAMPP.

3. En phpMyAdmin importa:
   database/gamezone_store.sql

4. Abre:
   http://localhost/gamezone_store/

CUENTAS DE PRUEBA
-----------------
Superadmin:
  superadmin@gamezone.com
  superadmin123

Admin:
  admin@gamezone.com
  admin123

Usuario:
  usuario@gamezone.com
  usuario123

CORREO REAL
-----------
Para registrar cuentas nuevas el sistema comprueba el dominio del correo y trata de enviar el código de 6 dígitos por SMTP ANTES de crear la cuenta.
Si el correo no puede recibir el mensaje o SMTP no está configurado, la cuenta NO se crea.

Configura:
  config/mail.php

Con Gmail:
  SMTP_HOST = smtp.gmail.com
  SMTP_PORT = 587
  SMTP_USER = tu_correo@gmail.com
  SMTP_PASSWORD = contraseña de aplicación de Google

No uses la contraseña normal de Gmail. Debes crear una contraseña de aplicación en tu cuenta de Google con la verificación en dos pasos activa.

VERIFICACION
------------
- El código dura 15 minutos.
- Después del registro, el usuario permanece en login.php.
- El código se introduce en esa misma pantalla.
- Una cuenta no verificada no puede entrar al catálogo, carrito, perfil, contacto ni administración.
- Al cerrar sesión vuelve al login normal.

ROLES
-----
Superadmin > Admin > Usuario.
Solo el superadmin puede retirar el rol de un admin.
Los admins pueden promover usuarios a admin, pero no pueden quitar el admin a otro admin.

IMAGENES
--------
El administrador puede subir JPG, PNG o WebP desde el panel. PHP las convierte a WebP y las guarda en assets/uploads/.

DISEÑO
------
Se mantiene la identidad visual del proyecto original, con mejoras en centrado, header, hover, portada, catálogo, detalle de producto y footer.

ACTUALIZACION PERFIL
--------------------
Importa database/gamezone_store.sql para una instalacion limpia. Si ya tienes la base creada y no quieres borrarla, ejecuta database/migration_perfil.sql.
La foto de perfil se guarda como WebP en assets/uploads.


RAILWAY
-------
El proyecto ya está preparado para usar las variables de entorno de Railway.

En el servicio app-juan, configura estas referencias a tu servicio MySQL:

  MYSQLHOST=${{MySQL.MYSQLHOST}}
  MYSQLPORT=${{MySQL.MYSQLPORT}}
  MYSQLUSER=${{MySQL.MYSQLUSER}}
  MYSQLPASSWORD=${{MySQL.MYSQLPASSWORD}}
  MYSQLDATABASE=${{MySQL.MYSQLDATABASE}}

El archivo config/db.php toma esas variables automáticamente.
No escribas la contraseña de MySQL dentro del código.

La base de datos creada por Railway normalmente se llama "railway".
El archivo database/gamezone_store.sql fue ajustado para NO borrar ni crear
la base de datos de Railway. Ejecútalo sobre la base "railway" para crear
las tablas y datos iniciales.

