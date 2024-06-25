# chat
Una aplicación para gestionar los chats en asignaturas


# Ejecucion

-Configurar archivo db.php.
-Descomprimir carpeta vendor.
-Subir archivo chat.sql al gestor de base de datos.
-Configurar en vendor vendor/ezyang/htmlpurifier/library/HTMLPurifier/Encoder.php y remplazar $in = ord($str{$i}) por  $in = ord($str[$i]); Linea 162. Eso corrige un error de los profesores cuando quieren ver los alumnos. Probar en caso de que no funcione actualizar librerias y composer.