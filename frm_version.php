<?php
require('clsusuario.php');



switch ($_GET['opcion']) {
		case 'valle_grande':
			  valle_grande();
			break;
		case 'gougo':
			  gougo();
			break;	
		case 'valle_grande_conductor':
			   valle_grande_conductor();
			break;
		case 'get_datos_de_empresa_pagina':
			   get_datos_de_empresa_pagina();
			break;	
		
		default:			
			break;
	}

function gougo()
{
  
  $dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::verificar_usuario_actual($dato['id_usuario'],$dato['token']);
	if($retorno) { 
			if($retorno['estado']==0)
			{
			 print json_encode(array('suceso'=>'2',
				'mensaje'=>'Cuenta bloqueada. -> '.$retorno['descripcion_estado'],
				'version'=>"1"));
			}else{
			print json_encode(array('suceso'=>'1',
				'mensaje'=>'Version actual',
				'version'=>"1"));
			}			
		} 
		else {
		 print json_encode(array(
			'suceso' => '2' ,
			'mensaje'=>'Su cuenta a sido iniciada en otro dispositivo.',
			'version'=>"1"
			 ));	
		}
}

function valle_grande()
{
  
  $dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::verificar_usuario_actual($dato['id_usuario'],$dato['token']);
	if($retorno) { 
			if($retorno['estado']==0)
			{
			 print json_encode(array('suceso'=>'2',
				'mensaje'=>'Cuenta bloqueada. -> '.$retorno['descripcion_estado'],
				'version'=>"41"));
			}else{
			print json_encode(array('suceso'=>'1',
				'mensaje'=>'Version actual',
				'version'=>"41"));
			}			
		} 
		else {
		 print json_encode(array(
			'suceso' => '2' ,
			'mensaje'=>'Su cuenta a sido iniciada en otro dispositivo.',
			'version'=>"41"
			 ));	
		}
}

function valle_grande_conductor()
{
   $dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::verificar_conductor_actual($dato['id_conductor'],$dato['token']);
	if ($retorno==true){	

				$vehiculo=Usuario::verificar_conductor_vehiculo_actual($dato['id_conductor'],$dato['id_vehiculo']);
			if ($vehiculo==true){	
				print json_encode(array('suceso'=>'1',
				'mensaje'=>'Version actual',
				'version'=>"40"));
								
				} 
				else {
				 print json_encode(array(
					'suceso' => '2' ,
					'mensaje'=>'No tiene vehiculo asignado.',
					'version'=>"40"
					 ));	
				}
		
						
		} 
		else {
		 print json_encode(array(
			'suceso' => '2' ,
			'mensaje'=>'Su cuenta a sido iniciada en otro dispositivo.',
			'version'=>"40"
			 ));	
		}
}

function get_datos_de_empresa_pagina()
{
	header("Access-Control-Allow-Origin: *");
  $dato=Tarifa::get_datos_de_empresa_pagina($_GET['id_empresa']);
  print json_encode($dato);	
}




?>
