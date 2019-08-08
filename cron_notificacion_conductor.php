<?php

require 'clsconductor.php';


$re=conductor::notificacion_conductor();

	if($re==true)
	{
print json_encode(array('suceso' => '1' ,'mensaje'=>'Notificación enviada.' ));
	}else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Error ...' ));
	}

	?>