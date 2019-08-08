<?php

require 'clsusuario.php';


$re=Usuario::notificacion_usuario_cantidad_9();

	if($re==true)
	{
print json_encode(array('suceso' => '1' ,'mensaje'=>'Notificación enviada.' ));
	}else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Error ...' ));
	}

	?>