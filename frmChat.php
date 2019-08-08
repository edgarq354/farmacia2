<?php
require('clschat.php');



switch ($_GET['opcion']) {
		case 'enviar_pasajero':
			   enviar_pasajero();
			break;
		case 'enviar_conductor':
			   enviar_conductor();
			break;
		case 'get_chat_usuario_conductor':
			   get_chat_usuario_conductor();
			break;
		case 'enviar_usuario_desaparecido':
			   enviar_usuario_desaparecido();
			break;	

		default:	
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Actualice la aplicación.' ));		
			break;

	}

 function enviar_pasajero()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$id_conductor=$dato['id_conductor'];
	$mensaje=$dato['mensaje'];
	$titulo=$dato['titulo'];

	$retorno=Chat::enviar_mensaje($id_usuario,$id_conductor,$mensaje,$titulo,"1");

	if($retorno!=-1){
	  $sw_notificacion=Chat::notificacion_chat_enviado_conductor($id_usuario,$id_conductor,$mensaje,$titulo,1,$retorno,"1");
	}



	if($retorno!=-1)
	{
		$m['suceso']="1";
		$m['mensaje']="Se envio correctamente";
		$m['id']=$retorno;
		$m['fecha']=date("Y-m-d");
		$m['hora']=date("H:i:s");
	}else{
		$m['suceso']="2";
		$m['mensaje']="No envio correctamente";	
	}
	
	print json_encode($m);
}


 function enviar_usuario_desaparecido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$id_desaparecido=$dato['id_desaparecido'];
	$mensaje=$dato['mensaje'];
	$titulo=$dato['titulo'];
	$latitud=$dato['latitud'];
	$longitud=$dato['longitud']; 

	$retorno=Chat::enviar_usuario_desaparecido($id_usuario,$id_desaparecido,$mensaje,$titulo,"1",$latitud,$longitud);

	 


	if($retorno!=-1)
	{
		$m['suceso']="1";
		$m['mensaje']="Se envio correctamente";
	 
	}else{
		$m['suceso']="2";
		$m['mensaje']="No envio correctamente";	
	}
	
	print json_encode($m);
}


 function enviar_conductor()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$id_conductor=$dato['id_conductor'];
	$mensaje=$dato['mensaje'];
	$titulo=$dato['titulo'];
	$retorno=Chat::enviar_mensaje($id_usuario,$id_conductor,$mensaje,$titulo,"0");
	if($retorno!=-1){
		$sw_notificacion=Chat::notificacion_chat_enviado_usuario($id_usuario,$id_conductor,$mensaje,$titulo,1,$retorno,"0");
	}



	if($retorno!=-1)
	{
		$m['suceso']="1";
		$m['mensaje']="Se envio correctamente";
		$m['id']=$retorno;
		$m['fecha']=date("Y-m-d");
		$m['hora']=date("H:i:s");
	}else{
		$m['suceso']="2";
		$m['mensaje']="No envio correctamente";	
	}
	
	print json_encode($m);
}

 function get_chat_usuario_conductor()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$id_conductor=$dato['id_conductor']; 
	$retorno=Chat::get_chat_usuario_conductor($id_usuario,$id_conductor);
	 $m['suceso']="1";
	 $m['mensaje']="Se envio correctamente";
	 $m['lista']=array($retorno);
 	print json_encode($m);

}


	?>