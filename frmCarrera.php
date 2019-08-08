<?php
require('clscarrera.php');




switch ($_GET['opcion']) {
		case 'comenzar_carrera':
			   comenzar_carrera();
			break;
		case 'nueva_carrera':
			   nueva_carrera();
			break;
		case 'finalizar_pedido':
			   finalizar_pedido();
			break;
		case 'lista_de_carrera_por_pedido_conductor':
			lista_de_carrera_por_pedido_conductor();
			break;	
		case 'lista_de_carrera_por_pedido_usuario':
			lista_de_carrera_por_pedido_usuario();
			break;
		case 'get_carrera_por_id':
			get_carrera_por_id();
		break;	



		//PRUEBA PARA REGISTRAR LAS ENCONTRAR LAS TARIFAS.
		case 'iniciar_carrera_prueba':
			   iniciar_carrera_prueba();
			break;
		case 'finalizar_carrera_prueba':
			   finalizar_carrera_prueba();
			break;
		case 'get_carrera_prueba_por_id':
			get_carrera_prueba_por_id();
		break;		
        case 'lista_de_carrera_casual_conductor':
			lista_de_carrera_casual_conductor();
		break;
		case 'get_ruta':
			get_ruta();
		break;

		case 'calcular_tarifa':
			calcular_tarifa();
		break;

		case 'get_ruta_por_id_carrera':
			get_ruta_por_id_carrera();
		break;
		case 'distancia_prueba':
			distancia_prueba();
		break;
		case 'get_ruta_por_id':
			get_ruta_por_id();
		break;
		case 'get_carrera_por_id_carrera':
			get_carrera_por_id_carrera();
		break;
		
		default:
		print json_encode(array('suceso' => '2','mensaje' => 'Intruso detectado.'));			
			break;

	}
function comenzar_carrera()
{

$dato=json_decode(file_get_contents("php://input"),true);
$id_pedido=$dato['id_pedido'];
$dato=Carrera::comenzar_carrera($dato['id_pedido'],$dato['latitud'],$dato['longitud'],$dato['altura'],$dato['ci'],$dato['placa'],$dato['id_usuario'],$dato['direccion']);
if($dato==1)
{
	$token=Carrera::get_token_id_pedido($id_pedido);
  
   $pedido=Carrera::get_pedido_todo_por_id($id_pedido);
   if($pedido['clase_vehiculo']=="5")
   {
	$sw_entragado=Carrera::delivery_enviado_en_camino($id_pedido);
	$sw_enviado=Carrera::enviar_notificacion_usuario($token,$id_pedido,"Su carrito de pedido esta en camino.","Delivery");
   }else
   {
   	 $enviado=Carrera::enviar_notificacion_iniciar_carrera($token,$id_pedido);
   }

	print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.','id_carrera'=>1 ));
   
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al iniciar carrera.' ));
}

}
#no recuerdo donde esta esta function,,,,,,,......
function nueva_carrera()
{
	$dato=json_decode(file_get_contents("php://input"),true);
$dato=Carrera::nueva_carrera($dato['id_pedido'],$dato['latitud'],$dato['longitud'],$dato['altura'],$dato['ci'],$dato['placa'],$dato['id_usuario'],$dato['monto'],$dato['distancia'],$dato['direccion']);
if($dato!=-1)
{
	
	print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.','id_carrera'=>$dato ));}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al cargar.' ));}
}


#termino la carrera,,..............
function finalizar_pedido()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
$sw_carrera=Carrera::finalizar_pedido(
	$dato['id_carrera'],
	$dato['id_pedido'],
	$dato['latitud'],
	$dato['longitud'],
	$dato['altura'],
	$dato['ci'],
	$dato['placa'],
	$dato['id_usuario'],
	$dato['monto_total'],
	$dato['distancia'],
	$dato['direccion'],
	$dato['comentario']);
if($sw_carrera==true)
{
	$sw_entragado=Carrera::delivery_entregado($dato['id_pedido']);
	print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.' ));
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al cargar.' ));}

}

function lista_de_carrera_por_pedido_conductor()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carrera_por_pedido_conductor($dato['ci'],$dato['placa'],$dato['id_pedido']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
 
  
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al obtener la lista.' ));
}
}

function lista_de_carrera_por_pedido_usuario()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carrera_por_pedido_usuario($dato['id_usuario'],$dato['id_pedido']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
 
  
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al obtener la lista.' ));
}
}

function get_carrera_por_id()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_pedido=$dato['id_pedido'];
	$id_carrera=$dato['id_carrera'];
	$ci=$dato['ci'];
	$placa=$dato['placa'];

	$detalle="Correcto.";
	
	$monto_aumentar=0;

	$row=Carrera::get_carrera_por_id($id_pedido,$id_carrera,$ci,$placa);

	if($row!=-1)
	{
	 // $sumatoria_distancia=Carrera::get_distancia_por_carrera($id_pedido,$id_carrera);
	  $tiempo=Carrera::get_tiempo_por_carrera($id_pedido,$id_carrera);
	  $minuto=Carrera::get_minuto_por_carrera($id_pedido,$id_carrera);
	  $metros=Carrera::get_distancia_metros_por_carrera_2($id_pedido,$id_carrera);
	  $clase_vehiculo=Carrera::get_clase_vehicu_por_id_pedido($id_pedido);
	  
	  if($clase_vehiculo==2){

	  }else
	  if($id_carrera==1){
		  $monto_aumentar=Carrera::get_monto_aumentar_por_id_pedido($id_pedido);
		}

	  $monto=Carrera::get_monto_tarifa_2($metros,$minuto,$clase_vehiculo);
	  $monto=$monto+$monto_aumentar;

	  $cantidad_solicitud=Carrera::get_cantidad_solicitud_usuario($id_pedido);
	 
	  if($cantidad_solicitud%10==0)
	  {
	  	$monto=0;
	  }

// Implementacion de verificacion de Huzo de Billetera
$estado_billetera=1;
$monto_efectivo=$monto;
	  if($estado_billetera==1)
	  {
	    $monto_billetera=Carrera::get_monto_billetera($id_pedido);

	  	if($monto<$monto_billetera)
	  	{
	  		$monto_efectivo=0;
	  	}else{
	  		$monto_efectivo=$monto-$monto_billetera;	
	  	}
	  	
	  	$detalle="El total del servicio es ".$monto." Bs menos el monto de Billetera es ".$monto_efectivo." Bs";
	  }
//final de huzo de billetera.


	  $mensaje['suceso']="1";
	  $mensaje['mensaje']="Correcto.";
	  $mensaje['carrera']=$row;
	  $mensaje['distancia']=$metros;
	  $mensaje['tiempo']=$tiempo;
	  $mensaje['altura']="0";
	  $mensaje['monto']=$monto;
	  $mensaje['total']=$monto_efectivo;
	  $mensaje['metros']=$metros;
	  $mensaje['minutos']=$minuto;
	  $mensaje['carrera']= array($row);
	  $mensaje['detalle']= $detalle;
	  print json_encode($mensaje);	
	}
	else
	{
	   $mensaje['suceso']="2";
	  $mensaje['mensaje']="Error al obtener loa carrera.";
	  $mensaje['carrera']= "";
	  $mensaje['distnacia']= "0";
	  $mensaje['tiempo']= "0";
	  $mensaje['altura']= "0";
	  $mensaje['monto']= "0";
	  $mensaje['total']= "0";
	  $mensaje['detalle']= "";
	  print json_encode($mensaje);	
	}
}

function calcular_tarifa()
{
 	$dato=json_decode(file_get_contents("php://input"),true);

 	$latitud_inicio=$dato['latitud_inicio'];
 	$longitud_inicio=$dato['longitud_inicio'];
 	$latitud_fin=$dato['latitud_fin'];
 	$longitud_fin=$dato['longitud_fin'];

 	$distancia_tiempo=Carrera::calcular_distancia_minuto_con_google($latitud_inicio,$longitud_inicio,$latitud_fin,$longitud_fin);
 	$metros=round($distancia_tiempo['distancia'],0);
 	$minuto=round($distancia_tiempo['tiempo'],0);
 	 


 	if($metros==0 && $minuto==0)
 	{
  	  $mensaje['suceso']="2";
	  $mensaje['mensaje']="Vuelve a intentarlo en otro momento"; 
	  print json_encode($mensaje);
 	}else{
 	
 	  $mensaje['suceso']="1";
	  $mensaje['mensaje']="Correcto.";
	  $mensaje['altura']="0";
	  $mensaje['metros']=$metros;
	  $mensaje['minutos']=$minuto;
	  $mensaje['normal']=Carrera::get_monto_tarifa_2($metros,$minuto,1);
	  $mensaje['de_lujo']=Carrera::get_monto_tarifa_2($metros,$minuto,2);
	  $mensaje['con_aire']=Carrera::get_monto_tarifa_2($metros,$minuto,3);
	  $mensaje['maletero']=Carrera::get_monto_tarifa_2($metros,$minuto,4);
	  $mensaje['pedido']=Carrera::get_monto_tarifa_2($metros,$minuto,5);
	  $mensaje['reserva']=Carrera::get_monto_tarifa_2($metros,$minuto,6);
	  $mensaje['moto']=Carrera::get_monto_tarifa_2($metros,$minuto,7);
	  $mensaje['moto_pedido']=Carrera::get_monto_tarifa_2($metros,$minuto,8);
	  
	  print json_encode($mensaje);	
	  }


}










//REGISTRO DE LAS TARIFA,...7
function iniciar_carrera_prueba()
{

$dato=json_decode(file_get_contents("php://input"),true);
$id_pedido=$dato['id_pedido'];
$id_carrera=Carrera::iniciar_carrera_prueba($dato['latitud'],$dato['longitud'],$dato['altura'],$dato['ci'],$dato['placa']);
if($dato!=-1)
{
	print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.','id_carrera'=>$id_carrera ));
   
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al iniciar carrera.' ));
}

}

#termino la carrera,,..............
function finalizar_carrera_prueba()
{
$dato=json_decode(file_get_contents("php://input"),true);
$carrera=Carrera::finalizar_carrera_prueba($dato['id_carrera'],$dato['latitud'],$dato['longitud'],$dato['altura'],$dato['altura_fin'],$dato['ci'],$dato['monto_total'],$dato['distancia'],$dato['tiempo']);
if($carrera==true)
{print json_encode(array('suceso' =>'1','mensaje'=>'Se cargo correctamente.' ));}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al cargar.' ));}
}

function get_carrera_prueba_por_id()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Carrera::get_carrera_prueba_por_id($dato['id_carrera'],$dato['ci']);
	if($row!=-1)
	{$sumatoria_distancia=Carrera::get_distancia_por_carrera_prueba($dato['id_carrera']);
	  $mensaje['suceso']="1";
	  $mensaje['mensaje']="Correcto.";
	  $mensaje['distancia']=$sumatoria_distancia;
	  $mensaje['monto']="0";
	  $mensaje['prueba']= array($row);
	  print json_encode($mensaje);	
	}
	else
	{
	   $mensaje['suceso']="2";
	  $mensaje['mensaje']="Error al obtener loa carrera.";
	  $mensaje['prueba']= "";
	  $mensaje['distancia']= "0";
	  $mensaje['monto']= "0";
	  print json_encode($mensaje);	
	}
}

function lista_de_carrera_casual_conductor()
{
$dato=json_decode(file_get_contents("php://input"),true);
$row=Carrera::lista_de_carrera_casual_conductor($dato['id_conductor']);
if($row!="-1")
{
  $dato['suceso']="1";
  $dato['mensaje']="Correcto.";
  $dato['carrera']= $row;
 
  
  print json_encode($dato);	
}
else
{
	print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error al obtener la lista.' ));
}
}

function get_ruta()
{
	$ruta=Carrera::get_ruta($_GET['id_carrera'],$_GET['id_pedido']);
	echo $ruta;
	echo "<img src='".$ruta."' />";

}


function get_ruta_por_id_carrera()
{
 
$ruta=Carrera::get_ruta_por_id_carrera_2($_GET['id_carrera'],$_GET['id_pedido'] );
echo  "<a href='".$ruta."'>ruta</a>";
 

}
function distancia_prueba()
{
	echo "distancia=".Carrera::get_distancia_metros_por_carrera_2($_GET['id_pedido'],$_GET['id_carrera']);

	/*
$point1 = array("lat" => "-17.325362", "long" => "-63.248539"); // París (Francia)
$point2 = array("lat" => "-17.342888", "long" => "-63.248765"); // Ciudad de México (México)
$km = Carrera::calcular_distancia($point1['lat'], $point1['long'], $point2['lat'], $point2['long']); // Calcular la distancia en kilómetros (por defecto)
$mi = Carrera::calcular_distancia($point1['lat'], $point1['long'], $point2['lat'], $point2['long'], 'mi'); // Calcular la distancia en millas
$nmi = Carrera::calcular_distancia($point1['lat'], $point1['long'], $point2['lat'], $point2['long'], 'nmi'); // Calcular la distancia en millas naúticas
echo "La distancia entre París (Francia) y la Ciudad de México (México) es de $km km (= $mi millas = $nmi millas naúticas)";
*/

}

function get_ruta_por_id()
{
	$ruta=Carrera::get_ruta_por_id($_GET['id_pedido'],$_GET['id_carrera']);
	$carrera=Carrera::get_carrera_por_id_carrera($_GET['id_pedido'],$_GET['id_carrera']);
 

        $row = array('ruta' => $ruta,'carrera'=>$carrera );       

	  print json_encode($row);	
	} 




















?>