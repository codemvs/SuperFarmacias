<?php

//Configo database
define('HOST', 'localhost');
define('DB', 'superfarmacia');
define('USER', 'root');
define('PASSWORD', "");
define('CHARSET', 'utf8');

//Config correo ADMIN Super Farmacias
define('CORREO_SF_ADMIN',  'superfarmacia.test@gmail.com');
define('NOMBRE_SF_ADMIN',  'Super Farmacias');

// Config envio de correo SMTP
define('CORREO_SMTP',  'superfarmacia.contact@gmail.com');
define('SECRET_CORREO_SMTP', 'superfarmacia.contact?51');
define('NOMBRE_CORREO_SMTP', 'Super Farmacias Contact');

// Este frgmento de código desactiva la opciono de mostraar errores
// Para debuggear, solo se debe comentarlo
error_reporting(0);
?>