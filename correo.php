<?php

// Asunto
$nombre="edgar elio";
$asunto = "Negocio";
$correo=$_GET['correo'];
$telefono="75602869";
$mensaje = '
<html lang="es"><body>Bienvenido a Radio Movil clasico</body></html>
';

// Cabecera que especifica que es un HMTL
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Cabeceras adicionales
$cabeceras .= 'From: Cliente {{nombre}} <{{email}}>' . "\r\n";
$cabeceras .= 'Cc: {{email}}' . "\r\n";
$cabeceras .= 'Bcc: {{email}}' . "\r\n";
//apple@not-reply.com;
$cabeceras=str_replace('{{email}}', $correo, $cabeceras);
$cabeceras=str_replace('{{asunto}}', $asunto, $cabeceras);
$cabeceras=str_replace('{{nombre}}', $nombre, $cabeceras);
// enviamos el correo!
$titulo="Clasico";
mail('richarpizarrocampos@gmail.com',$titulo, $mensaje, $cabeceras);
//header('Location: enviado.html');
?>
