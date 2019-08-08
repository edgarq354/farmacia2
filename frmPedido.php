<?php
require 'clspedido.php';

switch ($_GET['opcion']) {
	case 'get_pedidos':
		get_pedidos();
		break;
	case 'pedido_en_curso':
		pedido_en_curso();
		break;
	case 'get_pedido_por_celular_usuario':
	    get_pedido_por_celular_usuario();
	    break;
	case 'get_pedido_por_id_usuario':
		get_pedido_por_id_usuario();
		break;

	case 'llego_el_taxi':
		llego_el_taxi();
	break;
	case 'cancelar_pedido_usuario':
		cancelar_pedido_usuario();
	break;
	case 'cancelar_pedido_conductor':
		cancelar_pedido_conductor();
	break;
	case 'cancelar_pedido_reserva_conductor':
		cancelar_pedido_reserva_conductor();
	break;
	case 'cancelar_pedido_delivery_conductor':
		cancelar_pedido_delivery_conductor();
	break;
	case 'confirmar_pedido_delivery_usuario';
	confirmar_pedido_delivery_usuario();
	break;
	case 'detalle_cancelar_pedido_usuario':
		detalle_cancelar_pedido_usuario();
	break;
	case 'detalle_cancelar_pedido_conductor':
		detalle_cancelar_pedido_conductor();
	break;
	
	case 'lista_delivery_por_id_usuario':
		lista_delivery_por_id_usuario();
	break;
	case 'lista_pedido_por_ci':
		lista_pedido_por_ci();
	break;
	case 'lista_pedido_por_ci_mes':
		lista_pedido_por_ci_mes();
	break;
	case 'lista_pedido_por_id_usuario':
		lista_pedido_por_id_usuario();
	break;
	case 'lista_pedido_por_id_usuario_mes':
		lista_pedido_por_id_usuario_mes();
	break;
	case 'lista_pedido_por_id_usuario_top50':
		lista_pedido_por_id_usuario_top50();
	break;

	case 'get_estado_pedido':
		get_estado_pedido();
		break;
	case 'terminar_todo_pedido':
	     terminar_todo_pedido();
	     break; 
	case 'pedir_taxi':
	 	pedir_taxi();
	 	break;
	case 'reservar_movil':
	 	reservar_movil(); 	
	 	break;
	case 'registrar_delivery':
	 	registrar_delivery(); 	
	 	break; 	
	case 'delivery_movil':
	 	delivery_movil(); 	
	 	break; 	
	case 'cancelar_pedido_reserva_usuario':
		cancelar_pedido_reserva_usuario();
	break; 	
	case 'cancelar_pedido_delivery_usuario':
		cancelar_pedido_delivery_usuario();
	break; 	
	case 'aceptar_pedido':
	 	aceptar_pedido();
	     break;
	case 'aceptar_reserva':
	 	aceptar_reserva();
	     break;   
	case 'aceptar_delivery':
	 	aceptar_delivery();
	     break; 
	case 'aceptar_delivery_conductor':
	 	aceptar_delivery_conductor();
	     break;      
	case 'iniciar_pedido_reserva':
	 	iniciar_pedido_reserva();
	     break;
	case 'iniciar_pedido_delivery':
	 	iniciar_pedido_delivery();
	     break;                  
	case 'get_pedido_por_id_pedido':
		get_pedido_por_id_pedido();
		break;
	case 'get_todos_reservas':
		get_todos_reservas();
		break;	
	case 'get_todos_delivery':
		get_todos_delivery();
		break;		
	case 'get_reservas':
		get_reservas();
		break;	
	case 'get_delivery':
		get_delivery();
		break;	
	case 'get_delivery_pendiente':
		get_delivery_pendiente();
		break;		
	case 'get_delivery_conductor_en_camino':
		get_delivery_conductor_en_camino();
		break;
	case 'get_delivery_conductor_en_proceso':
		get_delivery_conductor_en_proceso();
		break;
	case 'get_delivery_en_proceso':
		get_delivery_en_proceso();
		break;		
	case 'get_delivery_completados':
		get_delivery_completados();
		break;			
	case 'get_delivery_cancelado':
		get_delivery_cancelado();
		break;			
	case 'get_delivery_por_id_administrador_lugar':
		get_delivery_por_id_administrador_lugar();
		break;	
	case 'get_delivery_proceso_detalle_por_id':
		get_delivery_proceso_detalle_por_id();
		break;			
	case 'get_empresa_por_id':
		get_empresa_por_id();
		break;
	case 'set_estado':
		set_estado();
		break;
		//Estoy cerca es cuando el taxista esta a unos 200 metros
	case 'estoy_cerca':
		estoy_cerca();
		break;
		//llego el taxi es cuando el taxista esta a unos 50 metros
	case 'notificacion_llego_el_taxi':
		notificacion_llego_el_taxi();
		break;
	case 'cancelar_pedido':
		cancelar_pedido();
		break;
	case 'pedido_en_camino':
		pedido_en_camino();	
		break;
	case 'verificar_si_acepto_pedido':
		verificar_si_acepto_pedido();	
		break;
	case 'verificar_si_acepto_pedido_2':
		verificar_si_acepto_pedido_2();	
		break;	
	case 'verificar_si_acepto_pedido_sin_notificacion':
		verificar_si_acepto_pedido_sin_notificacion();	
		break;	
	case 'monto_total_por_id_pedido':
	    monto_total_por_id_pedido();
	    break;
	case 'cargar_puntuacion':
	    cargar_puntuacion();
	    break;    
	case 'enviar_notificacion_usuario':
	    enviar_notificacion_usuario();
	    break;  
	case 'enviar_notificacion_conductor':
	    enviar_notificacion_conductor();
	    break;      
	case 'cancelar_abordo_carrera':
		cancelar_abordo_carrera();
		break;
	case 'aceptar_abordo_carrera':
		aceptar_abordo_carrera();
		break;
	case 'get_carrera_por_id':
		get_carrera_por_id();
		break;	
	case 'get_conductor_numero_movil':
		get_conductor_numero_movil();
		break;
	case 'buscar_conductor_delivery':
		buscar_conductor_delivery();
		break;


	default:
		print json_encode(array('suceso' => '2','mensaje' => 'Intruso detectado.'));
		break;
}

function confirmar_pedido_delivery_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::confirmar_pedido_delivery_usuario($dato['id_pedido'],$dato['id_usuario'],$dato['id_lugar'],$dato['carrito'],$dato['nit'],$dato['nombre'],$dato['latitud'],$dato['longitud'],$dato['direccion'],$dato['referencia']);
	if($p===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Pedido enviado correctamente.";
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentarlo.'));
	}	
}
function estoy_cerca()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::estoy_cerca($dato['id_pedido']);
	if($p===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto ";
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Error al enviar la notificacion.'));
	}
}

function notificacion_llego_el_taxi()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Pedido::notificacion_llego_el_taxi($dato['id_pedido']);
	if($p===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto ";
		print json_encode($dato);
	    	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Error al enviar la notificacion.'));
	}
}

function get_pedidos()
{//ELIMINAR
$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_pedidos_por_id_motista($dato['id_taxi']);
	if($row)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["pedido"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."  ));
	}
		
}


function get_reservas()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_reservas_por_id_usuario($dato['id_usuario']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_todos_reservas()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_todos_reservas($dato['ci']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}


function get_todos_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_todos_delivery($dato['ci']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}



function get_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery($dato['id_usuario']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}



function get_delivery_pendiente()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_pendiente($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}


function get_delivery_en_proceso()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_en_proceso($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_delivery_conductor_en_camino()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_conductor_en_camino($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_delivery_conductor_en_proceso()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_conductor_en_proceso($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_delivery_completados()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_completados($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_delivery_cancelado()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_pendiente($dato['id_lugar']);
	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}



function get_delivery_por_id_administrador_lugar()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_delivery_por_id_administrador_lugar($dato['id_usuario']);

	if($row!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["lista"]=$row;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"Lista vacia."  ));
	}
		
}

function get_delivery_proceso_detalle_por_id()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$carrito=Pedido::get_carrito_por_id_pedido($dato['id_pedido']);
	$pedido=Pedido::get_pedido_proceso_id_pedido($dato['id_pedido']);

	if($pedido!=-1 && $carrito!=-1)
	  {
	  	 $dato["suceso"]= "1";
	  	 $dato["mensaje"]= "1";
	  	 $dato["pedido"]=$pedido;
	  	 $dato["carrito"]=$carrito;
		print json_encode($dato);
	}
	else
	{ 
		print json_encode(array("suceso"=>"2","mensaje"=>"No existe el pedido."  ));
	}
		
}




function get_ped()
{$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_pedidos_por_id_motista($dato['id_taxi']);
	if($row)
	{
		foreach ($row as $registro) {
		$dato[]=array("suceso"=>"1","id"=>$registro["id"],"id_usuario"=>$registro["id_usuario"],"id_taxi"=>$registro["id_taxi"],"calificacion"=>$registro["calificacion"],"tipo_pedido"=>$registro["tipo_pedido"],"mensaje"=>$registro["mensaje"],"fecha"=>$registro["fecha"],"fecha_llegado"=>$registro["fecha_llegado"],"estado"=>$registro["estado"],"latitud"=>$registro["latitud"],"longitud"=>$registro["longitud"],"nombre_usuario"=>$registro["nombre_usuario"]);
		}
	}
	else
	{ 
		$dato[]= array("suceso"=>"2","mensaje"=>"Error al obtener los datos del servidor."  );
	}
	print json_encode($dato);
}
function get_pedido_por_celular_usuario()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_celular_usuario($dato['celular']);
  if($row)
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);


   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}
function llego_el_taxi()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::llego_el_taxi($dato['id_pedido'],$dato['latitud'],$dato['longitud']);
	if($row===true)
	{	Pedido::notificacion_pedido_finalizado($dato['id_pedido']);
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Pedido finalizado correctamente.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function cancelar_pedido_usuario()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_usuario($dato['id_pedido'],$dato['id_usuario']);
	$estado=Pedido::get_estado_pedido($dato['id_pedido']);
	if($row===true && $estado=='4')
	{	Pedido::notificacion_pedido_cancelado_usuario($dato['id_pedido'],'');
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No puede finalizar el pedido.' ));
	}
}

function cancelar_pedido_reserva_usuario()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_reserva_usuario($dato['id_pedido'],$dato['id_usuario'],$deto['detalle']);
	$estado=Pedido::get_estado_pedido_reserva($dato['id_pedido']);
	if($row===true && $estado=='4')
	{	Pedido::notificacion_pedido_cancelado_usuario($dato['id_pedido'],$deto['detalle']);
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo su reserva.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No puede finalizar su reserva.' ));
	}
}

function cancelar_pedido_delivery_usuario()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_delivery_usuario($dato['id_pedido'],$dato['id_usuario']);
	$estado=Pedido::get_estado_pedido_delivery($dato['id_pedido']);

	if($row===true && $estado=='4')
	{	
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo su delivery.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No puede finalizar su delivery.' ));
	}
}

function cancelar_delivery_administrador()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_delivery_administrador($dato['id_pedido'],$dato['id_usuario']);
	$estado=Pedido::get_estado_pedido_delivery($dato['id_pedido']);

	if($row===true && $estado=='14')
	{	
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo su delivery.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No puede finalizar su delivery.' ));
	}
}

function cancelar_pedido_reserva_conductor()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_reserva_conductor($dato['id_pedido'],$dato['id_conductor'],$dato['placa'],$dato['detalle']);
	$estado=Pedido::get_estado_pedido_reserva($dato['id_pedido']);
	if($row===true && $estado=="0")
	{	Pedido::notificacion_pedido_cancelado_conductor($dato['id_pedido'],$dato['detalle']);
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No se pudo cancelar el pedido.' ));
	}
}


function cancelar_pedido_delivery_conductor()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_delivery_conductor($dato['id_pedido'],$dato['id_conductor'],$dato['placa'],$dato['detalle']);
	$estado=Pedido::get_estado_pedido_delivery($dato['id_pedido']);
	if($row===true && $estado=="0")
	{	Pedido::notificacion_pedido_cancelado_conductor($dato['id_pedido'],$dato['detalle']);
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No se pudo cancelar el pedido.' ));
	}
}
function cancelar_abordo_carrera()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_abordo_carrera($dato['id_pedido'],$dato['id_usuario']);
	if($row===true)
	{	Pedido::notificacion_pedido_cancelado_usuario($dato['id_pedido'],'');
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function aceptar_abordo_carrera()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::aceptar_abordo_carrera($dato['id_pedido'],$dato['id_usuario']);
	if($row===true)
	{	
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Abordo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: ' ));
	}
}

function cancelar_pedido_conductor()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido_conductor($dato['id_pedido'],$dato['id_conductor'],$dato['placa'],$dato['detalle']);
	$estado=Pedido::get_estado_pedido($dato['id_pedido']);
	if($row===true && $estado==5)
	{	Pedido::notificacion_pedido_cancelado_conductor($dato['id_pedido'],$dato['detalle']);
		print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No se pudo cancelar el pedido.' ));
	}
}
function detalle_cancelar_pedido_usuario()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::detalle_cancelar_pedido_usuario($dato['id_pedido'],$dato['id_usuario'],$dato['detalle']);
	if($row==true)
	{	print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function detalle_cancelar_pedido_conductor()
{ $dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::detalle_cancelar_pedido_conductor($dato['id_pedido'],$dato['id_conductor'],$dato['detalle']);
	if($row==true)
	{	print json_encode(array('suceso' =>'1' ,'mensaje'=>'Se cancelo el pedido.'));
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}
}
function lista_pedido_por_id_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_usuario($dato["id_usuario"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No tienes ningun pedido.' ));
	}
}

function lista_pedido_por_id_usuario_mes()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_usuario_mes($dato["id_usuario"],$dato["mes"],$dato["anio"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No tienes ningun pedido.' ));
	}
}

function lista_pedido_por_id_usuario_top50()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_id_usuario_top50($dato["id_usuario"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No tienes ningun pedido.' ));
	}
}



function lista_delivery_por_id_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_delivery_por_id_usuario($dato["id_usuario"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No tienes ningun pedido.' ));
	}
}

function lista_pedido_por_ci()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_ci($dato["ci"]);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No pudimos obtener el historial.' ));
	}
}
function lista_pedido_por_ci_mes()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::lista_pedido_por_ci_mes($dato["ci"],$dato['mes'],$dato['anio']);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["mes"]=$dato['mes'];
		$dato["anio"]=$dato['anio'];
		$dato["historial"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No pudimos obtener el historial.' ,'mes'=>$dato['mes'],'anio'=>$dato['anio']));
	}
}



function pedido_en_curso()
{
$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::pedido_en_curso($dato["ci"],$dato['placa']);

	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["pedido"]= array($row);
		$dato['id_carrera']=Pedido::get_id_carrera($row['id'],$dato["ci"],$dato['placa']);
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al obtener el ultimo pedido.' ));
	}	
}

function get_estado_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_estado_pedido($dato["id_pedido"]);

	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["estado"]= $row['estado'];
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al obtener el ultimo pedido.' ));
	}	
}
function terminar_todo_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::terminar_todo_pedido($dato["id_pedido"]);

	if ($row===true) {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Error: al finalizar el pedido.' ));
	}	
}
function pedido_en_camino()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$pedido=Pedido::pedido_en_camino($dato['id_usuario']);
	if($pedido!=-1)
	{
		print json_encode(array('suceso' => '1','mensaje' => 'ya tiene un pedido en camino.','id_pedido'=>$pedido));
	}

	else{
		print json_encode(array('suceso' => '2','mensaje' => 'No tiene pedidos.'));
	}	
}
function pedir_taxi()
{$dato=json_decode(file_get_contents("php://input"),true);
	$latitud=$dato['latitud'];
	$longitud=$dato['longitud'];
	$id_usuario=$dato['id_usuario'];
	$indicacion=$dato['indicacion'];
	$nombre=$dato['nombre'];
	$numero_casa=$dato['numero_casa'];
	$imei=$dato['imei'];
	$clase_vehiculo=$dato['clase_vehiculo'];
	$tipo_pedido_empresa=$dato['tipo_pedido_empresa'];
	$direccion=$dato['direccion'];


	$sw=false;
	
	$pedido=Pedido::pedido_en_camino($id_usuario);
	if($pedido!="-1")
	{
		print json_encode(array('suceso' => '1','mensaje' => 'Ya tiene un pedido en camino.','id_pedido'=>$pedido));
		$sw=true;
	}

	$sw_tipo_pedido_empresa=0;
	if($tipo_pedido_empresa==1){
		$sw_tipo_pedido_empresa=Pedido::get_id_empresa_por_id_usuario($id_usuario);	
	}


if($sw==false)
{	 
	$taxi=Pedido::taxi_disponible_por_clase($latitud,$longitud,$clase_vehiculo);

	if($taxi==true){
		try{
			$p="-1";
			if($tipo_pedido_empresa==0){
				//SOLICITUD NO CORPORATIVA
				$p=Pedido::pedir_taxi($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$direccion);
			}else if($tipo_pedido_empresa==1 && $sw_tipo_pedido_empresa !=0 ){
				//SOLICITUD CORPORATIVA.
				$p=Pedido::pedir_taxi_corporativo($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$sw_tipo_pedido_empresa,$direccion);
			}
				if($p!="-1")
				{
				    print json_encode(array('suceso' => '1','mensaje' => 'Pedir enviado correctamente.','id_pedido'=>$p));
				 }else if($tipo_pedido_empresa==1 && $sw_tipo_pedido_empresa ==0 ){
					print json_encode(array('suceso' => '2','mensaje' => 'No puede solicitar pedidos CORPORATIVOS por que no forma parte de ninguna de nuestras Empresas.'));
				 }
				else
				{
					print json_encode(array('suceso' => '2','mensaje' => 'Su pedido no a Podido realizarse.'));
				}

			}catch(Exception $e){

				print json_encode(array('suceso' => '2','mensaje' => 'Hay una nueva actualización'));
			}	


	}
	else{
		// CLASE DE VEHICULO:
		// 7: pide una moto...
		if( $clase_vehiculo==7)
		{
		print json_encode(array('suceso' => '2','mensaje' => 'No hay Motos disponible cerca de su solicitud.'));	
		}else{
		print json_encode(array('suceso' => '2','mensaje' => 'No hay Movil disponible cerca de su solicitud.'));	
		}
		
	}	
}
else
{
	print json_encode(array('suceso' => '2','mensaje' => 'Error al conectar con el Servidor.'));
}
}


function buscar_conductor_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_pedido=$dato['id_pedido'];


	
	$pedido=Pedido::buscar_conductor_delivery($id_pedido);
	if($pedido!="-1")
	{
		print json_encode(array(
			'suceso' => '1',
			'mensaje' => 'Se envio correctamente la solicitud de conductores.'));
	}else
	{
		print json_encode(array(
			'suceso' => '2',
			'mensaje' => 'No se pudo enviar la solicitud de conductor.'));
	}

	 
}
 

function aceptar_pedido()
{
	$suceso="2";
	$mensaje="El Pedido a sido aceptado por otro conductor";
	$pedido="";

	$dato=json_decode(file_get_contents("php://input"),true);
	$datos_asignacion=Pedido::get_datos_si_esta_disponible($dato['ci'],$dato['placa']);
	$tiene_pedido=Pedido::tiene_pedido($dato['ci'],$dato['placa']);

	if($datos_asignacion==-1){
		
	}else{
		if($datos_asignacion['estado_asignacion']==0){
			$mensaje="Su cuenta esta Suspendida.";
		}else if($datos_asignacion['estado_asignacion']==2){
			$mensaje="Su cuenta esta inactiva.";
		}else if($datos_asignacion['estado_asignacion']==6){
			$mensaje="Esta retirado de la Empresa";
		}else if($datos_asignacion['estado']!=1){
			$mensaje="Su cuenta de conductor no esta disponible.";
		}else if($datos_asignacion['bloqueo']==1){
			$mensaje="Su cuenta esta bloqueado.";
		}else if($datos_asignacion['panico']==1){
			$mensaje="Su cuenta esta con panico";
		}else if($datos_asignacion['estado_asignacion']==1 && $datos_asignacion['estado']==1 ){
				if($tiene_pedido==-1){
				$p=Pedido::aceptar_pedido($dato['id_pedido'],$dato['ci'],$dato['placa']);
				if($p===true)
				{  
					$get_pedido=Pedido::pedido_en_curso_por_id($dato['id_pedido']);
					if($get_pedido['id_conductor']==$dato['ci'] && $get_pedido['id_vehiculo']==$dato['placa']){	
						$suceso="1";
						$mensaje="Correcto";
						$pedido=array($get_pedido);
					}else {
						$retorno_c=Pedido::get_conductor_numero_movil($dato['id_pedido']);
						if($retorno_c==".")
						{
						$mensaje='El Pedido ha sido cancelado por el pasajero.';	
						}else
						{
						$mensaje='El Pedido ya a sido registrado por otro Taxista.'.$retorno_c;	
						}
						
					}
				    	
				}else
				{
					$mensaje="El pedido a sido aceptado por otro conductor :-(.".Pedido::get_conductor_numero_movil($dato['id_pedido']);
				}
			}else {
				if($tiene_pedido['panico']==1){
					$mensaje="Señor Conductor tiene un pedido que esta en panico.";
				}else if($tiene_pedido['estado']<=1){
					$mensaje="Señor Conductor tiene un pedido que esta en proceso.";
				}
			}
		}
	}

	$respuesta["suceso"]=$suceso;
	$respuesta["mensaje"]=$mensaje;
	$respuesta["pedido"]= $pedido;

	print json_encode($respuesta);
}

function aceptar_delivery_conductor()
{
	$suceso="2";
	$mensaje="El Delivery a sido aceptado por otro conductor";
	$pedido="";

	$dato=json_decode(file_get_contents("php://input"),true);
	$datos_asignacion=Pedido::get_datos_si_esta_disponible($dato['ci'],$dato['placa']);
	$tiene_pedido=Pedido::tiene_pedido($dato['ci'],$dato['placa']);

	if($datos_asignacion==-1){
		
	}else{
		if($datos_asignacion['estado_asignacion']==0){
			$mensaje="Su cuenta esta Suspendida.";
		}else if($datos_asignacion['estado_asignacion']==2){
			$mensaje="Su cuenta esta inactiva.";
		}else if($datos_asignacion['estado_asignacion']==6){
			$mensaje="Esta retirado de la Empresa";
		}else if($datos_asignacion['estado']!=1){
			$mensaje="Su cuenta de conductor no esta disponible.";
		}else if($datos_asignacion['bloqueo']==1){
			$mensaje="Su cuenta esta bloqueado.";
		}else if($datos_asignacion['panico']==1){
			$mensaje="Su cuenta esta con panico";
		}else if($datos_asignacion['estado_asignacion']==1 && $datos_asignacion['estado']==1 ){
				if($tiene_pedido==-1){
				$p=Pedido::asignar_delivery_conductor($dato['id_pedido'],$dato['ci'],$dato['placa']);
				if($p===true)
				{  
					$get_pedido=Pedido::delivery_en_curso_por_id($dato['id_pedido']);
					if($get_pedido['id_conductor']==$dato['ci'] && $get_pedido['id_vehiculo']==$dato['placa']){	
						$suceso="1";
						$mensaje="Correcto";
						$pedido=array($get_pedido);

						$jpedido=Pedido::get_pedido_por_id($dato['id_pedido']);
						Pedido::enviar_notificacion_lugar($jpedido['id_lugar'],"Delivery aceptado por el conductor.");
					}else {
						$retorno_c=Pedido::get_conductor_numero_movil($dato['id_pedido']);
						if($retorno_c==".")
						{
						$mensaje='El Delivery ha sido cancelado por el pasajero.';	
						}else
						{
						$mensaje='El Delivery ya a sido registrado por otro Taxista.'.$retorno_c;	
						}
						
					}
				    	
				}else
				{
					$mensaje="El Delivery a sido aceptado por otro conductor. :-(".Pedido::get_conductor_numero_movil($dato['id_pedido']);
				}
			}else {
				if($tiene_pedido['panico']==1){
					$mensaje="Señor Conductor tiene un pedido que esta en panico.";
				}else if($tiene_pedido['estado']<=1){
					$mensaje="Señor Conductor tiene un pedido que esta en proceso.";
				}
			}
		}
	}

	$respuesta["suceso"]=$suceso;
	$respuesta["mensaje"]=$mensaje;
	$respuesta["pedido"]= $pedido;

	print json_encode($respuesta);
}

function get_pedido_por_id_pedido()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_id_pedido($dato['id_pedido']);
  if($row)
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);


   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos habilitados."  ));
  }
}

function get_pedido_por_id_usuario()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $row=Pedido::get_pedido_por_id_usuario($dato['id_usuario']);
  if($row!=-1)
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['pedido']= array($row);


   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"No tiene pedidos en curso."  ));
  }
}

function get_empresa_por_id()
{

  $dato=json_decode(file_get_contents("php://input"),true);
  $empresa=Pedido::get_empresa_por_id($dato['id_empresa']);
  $row=Pedido::get_telefono_empresa_por_id($dato['id_empresa']);
  if($empresa!=-1)
  {
  	$resultado['suceso']="1";
    $resultado['mensaje']="Correcto.";
    $resultado['razon_social']= $empresa['razon_social'];
    $resultado['direccion']= $empresa['direccion'];
    $resultado['whatsapp']= $empresa['whatsapp'];
    if($row!=-1)
    {
    	$resultado['telefono']= $row;
	}else{
     $resultado['telefono']= "";
    }
   print json_encode($resultado);

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"Contactece con el TaxiCorp."  ));
  }
}

function set_estado()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::set_estado($dato['estado'],$dato['id_taxi']);
	if($row===true)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al cargar el estado.' ));	
	}
}
function cancelar_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cancelar_pedido($dato['id_pedido']);
	$estado=Pedido::get_estado_pedido($dato['id_pedido']);
	if($row===true && $estado=='3')
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Se cancelo correctamente.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No se pudo cancelar el pedido.' ));	
	}
}

function monto_total_por_id_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::monto_total_por_id_pedido($dato['id_pedido']);
	if($row!=-1)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Se finalizo el pedido.','monto_total'=>$row,'distancia'=>'0','monto'=>"0"));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No se pudo obtener el monto total.' ));	
	}
}

function verificar_si_acepto_pedido()
{
	 $dato=json_decode(file_get_contents("php://input"),true);
  $id_pedido=$dato['id_pedido'];
  $tiempo=0;
  $tiempo_notificacion=0;
  $diametro=2000;

  do{
	$row=Pedido::get_pedido_por_id_pedido($id_pedido);

	$tiempo++;
	if($tiempo%5==0 && $row==-1)
	{
		if($diametro==2000)
		{	
			$sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,300,600);
			$diametro+=3000;
		}else if($diametro==5000)
		{
			$sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,600,1500);
			$diametro+=1000;
		}
		else if($diametro==6000)
		{
			$sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,1500,2500);
			$diametro+=1000;
		}
	}
  }while($row==-1 && $tiempo<26);

  if($row!=-1)
  {
  	if($row['estado']>=3)
  	{
  	  	$resultado['suceso']="3";
        $resultado['mensaje']="Pedido cancelado.";
  	    print json_encode($resultado);
  	}
  	else
  	{
  	$resultado['suceso']="1";
    $resultado['mensaje']="Pedido aceptado.";
    $resultado['pedido']= array($row);
   	print json_encode($resultado);
  	}
  

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"Vuelva a Intentarlo."  ));
  }
}




function verificar_si_acepto_pedido_2()
{
	 $dato=json_decode(file_get_contents("php://input"),true);
  $id_pedido=$dato['id_pedido'];
  $tiempo=0;
  $tiempo_notificacion=0;
  $diametro_maximo=$dato['diametro_maximo'];
  $diametro_minimo=$dato['diametro_minimo'];
  $enviar_notificacion=$dato['enviar_notificacion'];

	$row=Pedido::get_pedido_por_id_pedido($id_pedido);
	if($row==-1)
	{
		if($enviar_notificacion==1){
		  if($diametro_maximo==2000)
			{
			 $sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,300,600);
			}else if($diametro_maximo==5000)
			{
			 $sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,600,1500);
			}else if($diametro_maximo==6000)
			{
			 $sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,1500,2500);
			}else
			{
			 $sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,1500,2500);		
			}
		}else
		{ 
			 $sw=Pedido::enviar_notificacion_pedido_taxi_rango($id_pedido,1500,2500);		
			 
		}
	}
  

  if($row!=-1)
  {
  	if($row['estado']>=3)
  	{
  	  	$resultado['suceso']="3";
        $resultado['mensaje']="Pedido cancelado.";
  	    print json_encode($resultado);
  	}
  	else
  	{
  	$resultado['suceso']="1";
    $resultado['mensaje']="Pedido aceptado.";
    $resultado['pedido']= array($row);
   	print json_encode($resultado);
  	}
  

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"Vuelva a Intentarlo."  ));
  }
}


function verificar_si_acepto_pedido_sin_notificacion()
{
	 $dato=json_decode(file_get_contents("php://input"),true);
  $id_pedido=$dato['id_pedido'];
  

	$row=Pedido::get_pedido_por_id_pedido($id_pedido);
	 
  if($row!=-1)
  {
  	if($row['estado']>=3)
  	{
  	  	$resultado['suceso']="3";
        $resultado['mensaje']="Pedido cancelado.";
  	    print json_encode($resultado);
  	}
  	else
  	{
  	$resultado['suceso']="1";
    $resultado['mensaje']="Pedido aceptado.";
    $resultado['pedido']= array($row);
   	print json_encode($resultado);
  	}
  

  }else
  {
   print json_encode(array("suceso"=>"2","mensaje"=>"Vuelva a Intentarlo."  ));
  }
}




function cargar_puntuacion()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::cargar_puntuacion($dato['id_pedido'],$dato['punto_conductor'],$dato['punto_vehiculo'],$dato['descripcion']);
	if($row===true)
	{
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";

		$pedido=Pedido::lista_pedido_por_id_max_4($dato['id_pedido']);
		if ($pedido!="-1") {
		$dato["historial"]=$pedido;
		}
		else{
			$dato['historial']="";
		}
		print json_encode($dato);
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No pudimos cargar la puntuacion.' ));	
	}
}

function  registrar_delivery()
{
$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];

	$sw_registro=Pedido::registrar_delivery($id_usuario);

				if($sw_registro>0)
				{
				    print json_encode(array('suceso' => '1','mensaje' => 'Pedir enviado correctamente.','id'=>$sw_registro));
				 }else {
					print json_encode(array('suceso' => '2','mensaje' => 'No se pudo realizar el registro.'));
				 }

	 
}

function reservar_movil()
{$dato=json_decode(file_get_contents("php://input"),true);
	$latitud=$dato['latitud'];
	$longitud=$dato['longitud'];
	$id_usuario=$dato['id_usuario'];
	$indicacion=$dato['indicacion'];
	$nombre=$dato['nombre'];
	$numero_casa=$dato['numero_casa'];
	$imei=$dato['imei'];
	$clase_vehiculo=$dato['clase_vehiculo'];
	$tipo_pedido_empresa=$dato['tipo_pedido_empresa'];
	$fecha_reserva=$dato['fecha_reserva'];
 
	$sw_tipo_pedido_empresa=0;
	if($tipo_pedido_empresa==1){
		$sw_tipo_pedido_empresa=Pedido::get_id_empresa_por_id_usuario($id_usuario);	
	}

		try{
			$p="-1";
			if($tipo_pedido_empresa==0){
				//SOLICITUD NO CORPORATIVA
				$p=Pedido::reservar_movil($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva);
			}else if($tipo_pedido_empresa==1 && $sw_tipo_pedido_empresa !=0 ){
				//SOLICITUD CORPORATIVA.
				$p=Pedido::reservar_movil_corporativo($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$sw_tipo_pedido_empresa,$fecha_reserva);
			}
				if($p!="-1")
				{
				    print json_encode(array('suceso' => '1','mensaje' => 'Pedir enviado correctamente.','id_pedido'=>$p));
				 }else if($tipo_pedido_empresa==1 && $sw_tipo_pedido_empresa !=0 ){
					print json_encode(array('suceso' => '2','mensaje' => 'No puede solicitar pedidos CORPORATIVOS por que no forma parte de ninguna de nuestras Empresas.'));
				 }
				else
				{
					print json_encode(array('suceso' => '2','mensaje' => 'Su pedido no a Podido realizarse.'));
				}

			}catch(Exception $e){

				print json_encode(array('suceso' => '2','mensaje' => 'Hay una nueva actualización'));
			}	

}

function aceptar_reserva()
{
	$suceso="2";
	$mensaje="La reserva a sido aceptado por otro conductor.";

	$dato=json_decode(file_get_contents("php://input"),true);
	$datos_asignacion=Pedido::get_datos_si_esta_disponible($dato['ci'],$dato['placa']);
	$tiene_pedido=Pedido::tiene_pedido($dato['ci'],$dato['placa']);

	if($datos_asignacion==-1){
		
	}else{
		if($datos_asignacion['estado_asignacion']==0){
			$mensaje="Su cuenta esta Suspendida.";
		}else if($datos_asignacion['estado_asignacion']==2){
			$mensaje="Su cuenta esta inactiva.";
		}else if($datos_asignacion['estado_asignacion']==6){
			$mensaje="Esta retirado de la Empresa";
		}else if($datos_asignacion['estado']!=1){
			$mensaje="Su cuenta de conductor no esta disponible.";
		}else if($datos_asignacion['bloqueo']==1){
			$mensaje="Su cuenta esta bloqueado.";
		}else if($datos_asignacion['panico']==1){
			$mensaje="Su cuenta esta con panico";
		}else if($datos_asignacion['estado_asignacion']==1 && $datos_asignacion['estado']==1 ){
				if($tiene_pedido==-1){
				$p=Pedido::aceptar_reserva($dato['id_pedido'],$dato['ci'],$dato['placa']);
				if($p===true)
				{  
					$suceso="1";
					$mensaje="Correcto";
				}else
				{
					$mensaje="La reserva a sido aceptado por otro conductor :-(.";
				}
			}else {
				if($tiene_pedido['panico']==1){
					$mensaje="Señor Conductor tiene un pedido que esta en panico.";
				}else if($tiene_pedido['estado']<=1){
					$mensaje="Señor Conductor tiene un pedido que esta en proceso.";
				}
			}
		}
	}

	$respuesta["suceso"]=$suceso;
	$respuesta["mensaje"]=$mensaje;

	print json_encode($respuesta);
}

function aceptar_delivery()
{
	$suceso="2";
	$mensaje="Vuelva a intentarlo";

	$dato=json_decode(file_get_contents("php://input"),true);
	$sw_delivery=Pedido::aceptar_delivery($dato['id_usuario'],$dato['id_pedido']);

	if($sw_delivery==true){
		$suceso="1";
		$mensaje="Pedido aceptado.";
	} 

	$respuesta["suceso"]=$suceso;
	$respuesta["mensaje"]=$mensaje;

	print json_encode($respuesta);
}


function iniciar_pedido_reserva()
{
	$suceso="2";
	$mensaje="La reserva a sido aceptado por otro conductor";
	$pedido="";

	$dato=json_decode(file_get_contents("php://input"),true);
	$datos_asignacion=Pedido::get_datos_si_esta_disponible($dato['ci'],$dato['placa']);
	$tiene_pedido=Pedido::tiene_pedido($dato['ci'],$dato['placa']);
	$pedido_usuario=Pedido::pedido_en_camino($dato['id_usuario']);

	if($pedido_usuario!="-1")
	{
		$mensaje="El pasajero esta en otro servicio. Vuelva a intentarlo mas tarde.";

	}else{

	if($datos_asignacion==-1){
		
	}else{
		if($datos_asignacion['estado_asignacion']==0){
			$mensaje="Su cuenta esta Suspendida.";
		}else if($datos_asignacion['estado_asignacion']==2){
			$mensaje="Su cuenta esta inactiva.";
		}else if($datos_asignacion['estado']!=1){
			$mensaje="Su cuenta de conductor no esta disponible.";
		}else if($datos_asignacion['bloqueo']==1){
			$mensaje="Su cuenta esta bloqueado.";
		} 
		else if($datos_asignacion['estado_asignacion']==1 && $datos_asignacion['estado']==1 ){
				if($tiene_pedido==-1){
				$p=Pedido::iniciar_pedido_reserva($dato['id_pedido'],$dato['ci'],$dato['placa']);
				if($p===true)
				{  
					$get_pedido=Pedido::pedido_en_curso_por_id($dato['id_pedido']);
					if($get_pedido['id_conductor']==$dato['ci'] && $get_pedido['id_vehiculo']==$dato['placa']){	
						$suceso="1";
						$mensaje="Correcto";
						$pedido=array($get_pedido);
					}else {
						$mensaje='El pedido ya a sido registrado por otro Taxista.';
					}
				    	
				}else
				{
					$mensaje="El pasajero esta en otro servicio. vuelva a intentarlo nuevamente";
				}
			}else {
				if($tiene_pedido['panico']==1){
					$mensaje="Señor Conductor tiene un pedido que esta en panico.";
				}else if($tiene_pedido['estado']<=1){
					$mensaje="Señor Conductor tiene un pedido que esta en proceso.";
				}
			}
		}
	}
}

	$respuesta["suceso"]=$suceso;
	$respuesta["mensaje"]=$mensaje;
	$respuesta["pedido"]= $pedido;

	print json_encode($respuesta);
}


function enviar_notificacion_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::enviar_notificacion_usuario($dato['id_pedido'],$dato['id_usuario'],$dato['detalle']);
	if($row===true)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No pudimos cargar la puntuacion.' ));	
	}
}

function enviar_notificacion_conductor()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::enviar_notificacion_conductor($dato['id_pedido'],$dato['id_usuario'],$dato['ci'],$dato['placa'],$dato['detalle']);
	if($row===true)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No pudimos cargar la puntuacion.' ));	
	}
}

function get_carrera_por_id()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Pedido::get_carrera_por_id($dato['id_pedido'],$dato['id_carrera'],$dato['ci'],$dato['placa']);
	if($row===true)
	{
	   print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	   print json_encode(array('suceso' => '2' ,'mensaje'=>'No pudimos cargar la puntuacion.' ));	
	}
}

function get_conductor_numero_movil()
{
	echo Pedido::get_conductor_numero_movil($_GET['id_pedido']);
}



?>