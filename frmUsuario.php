<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
//header('content-type: application/json; charset=utf-8');

 
/*
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers:  Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
*/
/**
* Loguear al usuario 
*/
require_once 'clsusuario.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Decodificando formato Json
	switch ($_GET['opcion']) {
		case 'insertar_perfil':
			   insertar_perfil();
			break;
		case 'actualizar_usuario':
			   actualizar_usuario();
			break;
		case 'iniciar_sesion':
			   iniciar_sesion();
			break;
		case 'iniciar_sesion_con_celular':
			   iniciar_sesion_con_celular();
			break;	
	 			
		case 'cambiar_numero_celular':
			cambiar_numero_celular();
			break;
		case 'correro_de_modificacion_usuario':
		    correro_de_modificacion_usuario();
			break;
		case 'actualizar_contrasenia':
			actualizar_contrasenia();
			break;
		
		case 'registrar_usuario_facebook':
			registrar_usuario_facebook();
			break;
		case 'registrar_usuario_autenticar':
			registrar_usuario_autenticar();
			break;
		case 'registrar_usuario_facebook_directo':
			registrar_usuario_facebook_directo();
			break;
		case 'registrar_usuario_google_directo':
			registrar_usuario_google_directo();
			break;	
		case 'insertar_imagen':
			insertar_imagen();
			break;

		case 'insertar_imagen_perfil':
			insertar_imagen_perfil();
			break;
case 'insertar_imagen_prueba':
                        insertar_imagen_prueba();
                        break;
		
		case 'insertar_imagen_taxi':
			insertar_imagen_taxi();
			break;


case 'get_cargar_codigo':
			get_cargar_codigo();
		break;	

		case 'get_monto_billetera':
			get_monto_billetera();
			break;	


		

			
		case 'actualizar_dato':
			actualizar_dato();
			break;
		case 'lista_video':
			lista_video();
			break;	

		case 'enviar_sms':
			enviar_sms();
			break;	
		case 'verificar_codigo_sms':
			verificar_codigo_sms();
			break;		
		case 'lista_desaparecido':
			lista_desaparecido();
			break;	
		case 'desaparecido_por_id':
			desaparecido_por_id();
			break;		
		case 'set_ubicacion_punto_panico':
				set_ubicacion_punto_panico();
				break;	
		default:
			print json_encode(array('suceso' => '2' ,'mensaje'=>'Actualice la aplicación.' ));
			break;
	}
}else
{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'No esta enviando datos Post' ));
}


function set_ubicacion_punto_panico()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Usuario::set_ubicacion_punto_panico($dato['latitud'],$dato['longitud'],$dato['ci'],$dato['placa'],$dato['id_panico'],$dato['numero'],$dato['distancia'],$dato['rotacion']);
	if($row===true)
	{// di tiene id_carrera=0 quiere decir q el pedido esta en camino... porque el estado en 0--
		// si el id_carrera=-1 quiere decir que se inicio una carrera y se finalizo.. ey esta en espera a una nueva carrera o finalizar todo el pedido.....
	//	$cargar=conductor::set_puntos($dato['latitud'],$dato['longitud'],$dato['id_pedido']);
	
	print json_encode(array('suceso' => '1' ,'mensaje'=>'Correcto.'));
	}
	else
	{
	print json_encode(array('suceso' => '2' ,'mensaje'=>'Error: Al ingresar mi ubicacion.' ));
			}	
}
function lista_desaparecido()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Usuario::lista_desaparecido();
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["desaparecido"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No hay ningun desaparecido.' ));
	}
}


function desaparecido_por_id()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Usuario::desaparecido_por_id($dato['id_desaparecido']);
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Correcto";
		$dato["desaparecido"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'No hay ningun desaparecido.' ));
	}
}




 function get_monto_billetera()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::get_monto_billetera($dato['id_usuario']);
 	
	print json_encode(array('suceso'=>'1','mensaje'=>'Monto retornado.' ,'monto'=>$retorno));
						
		 
}


function get_cargar_codigo()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$codigo=$dato['codigo'];
	$num=base_convert($codigo, 16, 10);

	$decimal=substr("".$num, 4,5);
	/*
echo "codigo:".$codigo;
echo "numero:".$num;
echo "decimal:".$decimal;
	*/
	$retorno=Usuario::get_cargar_codigo($id_usuario,$decimal);
		/*
   	0: error al registrar.
	1: carga de codigo correctamente.
	2: codigo con fecha caducada.
	3: codigo ya usado anteriormente.
	4: codigo ya usado anteriormente.
	5: codigo incorrecto.
	6: codigo de compartir ya agregado anteriormente.
   	*/
 
   	$mensaje="Error al registrar.";
   	$suceso="2";

	switch ($retorno) {
			case '0':
			 	$mensaje="Error al registrar.";
				break;
			case '1':
			 	$mensaje="Codigo agregado correctamente.";
			 	$suceso="1";

				break;
			case '2':
			 $mensaje="Codigo esta fuera de la fecha de promoción.";
				break;
			case '3':
			 $mensaje="El codigo ya fue usado anteriormente.";
				break;			
			case '4':
			 $mensaje="El codigo ya fue usado anteriormente";
				break;
			case '5':
			 $mensaje="El codigo no es valido.";
				break;
			case '6':
			 $mensaje="EL codigo de INVITACIÓN fue agregado anteriormente.";
				break;
					
			default:
			 
				break;
		}	
		print json_encode(array('suceso'=>$suceso,
			'mensaje'=>$mensaje));

}

function insertar_imagen_perfil()
{
	$uploadfile_temporal=$_FILES['imagen']['tmp_name']; 

	$retorno=Usuario::insertar_imagen_perfil($_POST['id_usuario'],$uploadfile_temporal);
		if($retorno==false)
		{print	json_encode(array('suceso' => '2' ,'mensaje'=>'Tenemos problemas al subir la imagen.' ));
		}
		else{
			print json_encode(array('suceso' => '1' ,'mensaje'=>'Insertado correctamente'));
		}
}

function  insertar_imagen_prueba()
{
$uploadfile_temporal=$_FILES['imagen']['tmp_name'];
   $nuevo_nombre=$id_usuario."_perfil.png";
        $rutadelaimagen="prueba/".$nuevo_nombre;
        $direccion_imagen_png= $rutadelaimagen;
    
      $resultado=false;
      if (is_uploaded_file($uploadfile_temporal)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)) {
               $resultado=true;
print json_encode(array('suceso'=>'1','mensjae'=>'Insertador correctamente'));
          }else
{print json_encode(array('suceso'=>'2','mensjae'=>'NO se pudo mover la imagen'));
}
      } else
{
print json_encode(array('suceso'=>'2','mensjae'=>'No existe la imagen'));
}
}

 function insertar_perfil()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::insertar_perfil($dato['nombre'],$dato['apellido'],$dato['celular'],$dato['email'],$dato['contrasenia'],$dato['token']);
	if ($retorno!=false){	
		print json_encode(array('suceso'=>'1','mensaje'=>'Registrado correctamente.','id'=>$retorno,'nombre'=>$dato['nombre'],'apellido'=>$dato['apellido'],'celular'=>$dato['celular'],'email'=>$dato['email'],'token'=>$dato['token']));
						
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
}

 function actualizar_usuario()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::actualizar_usuario($dato['nombre'],$dato['apellido'],$dato['email'],$dato['token'],$dato['id']);
	if ($retorno===true){	
		print json_encode(array('suceso'=>'1','mensaje'=>'Se actualizo correctamente.'));
						
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
}

 function actualizar_dato()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	if(Usuario::existe_celular($dato['celular'],$dato['id'])==false)
	{
	$retorno=Usuario::actualizar_dato($dato['nombre'],$dato['apellido'],$dato['email'],$dato['celular'],$dato['id']);
	if ($retorno===true){	
		print json_encode(array('suceso'=>'1','mensaje'=>'Se actualizo correctamente.'));
						
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
	}
	else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Ya existe otra cuenta con el mismo Numero telefonico.' ));
	}
}
function iniciar_sesion()
{
	try{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::iniciar_sesion($dato['codigo'],$dato['contrasenia'],$dato['token'],$dato['imei']);
	if ($retorno!='-1'){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se inicio correctamente.";
	  	 $dato["perfil"]=array($retorno);
		print json_encode($dato);				
		} else {
		 print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al Iniciar Sesion.' ));	
		}
	}catch(Exception $e){
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Hay una nueva actualización.' ));
	}
}
function iniciar_sesion_con_celular()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$celular=$dato['celular'];
	$evaluar = verificar_codigo($celular,$dato['codigo']);
	 $validar = json_decode($evaluar,true);
			 		
	// if ($validar['success']==true) 
	 if (true) 
	 {
		$retorno=Usuario::iniciar_sesion_con_celular($celular,$dato['token'],$dato['imei']);
		if ($retorno!='-1'){	
			 $dato["suceso"]= "1";
			 $dato["mensaje"]= "Se inicio correctamente.";
		  	 $dato["perfil"]=array($retorno);
			print json_encode($dato);				
			} else { print json_encode(array('suceso' => '3' ,'mensaje'=>'ingresa tus datos' ));	
			}
	  }else
		 {
		 print json_encode( array( 'suceso' => '2', 'mensaje' => 'El codigo no es valido' ));
		 }
}

function cambiar_numero_celular()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	$celular=$dato['celular'];
	$evaluar = verificar_codigo($celular,$dato['codigo']);
	 $validar = json_decode($evaluar,true);
			 		
	// if ($validar['success']==true) 
	 if (true) 
	 {
		$retorno=Usuario::cambiar_numero_celular($dato['id_usuario'],$celular,$dato['id_facebook'],$dato['id_google'],$dato['token'],$dato['imei'],$dato['correo']);
		if ($retorno!=-1){	
			 $mensaje["suceso"]= "1";
			 $mensaje["mensaje"]= "Se actualizo sus datos."; 
			 $mensaje["perfil"]=array(Usuario::get_perfil($retorno)); 
			print json_encode($mensaje);				
			} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'El codigo no es valido'));	
			}
	  }else
		 {
		 print json_encode( array( 'suceso' => '2', 'mensaje' => 'El codigo no es valido' ));
		 }
}


function registrar_usuario_facebook()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
$id=Usuario::existe_cuenta($dato['codigo']);
    if($id=="-1")
    {
    	$retorno=Usuario::registrar_usuario_facebook($dato['nombre'],$dato['apellido'],$dato['celular'],$dato['email'],$dato['contrasenia'],$dato['token'],$dato['codigo'],$dato['id_facebook'],$dato['imei']);
	  if ($retorno!="-1"){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se registro correctamente.";
	  	 $dato["id_usuario"]=$retorno;
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
	}



	/*
	else if($id)
	{
		$retorno=Usuario::actualizar_cuenta($dato['nombre'],$dato['apellido'],$dato['celular'],$dato['contrasenia'],$dato['token'],$dato['email'],$id,$dato['codigo'],$dato['id_facebook']);
	  if ($retorno!=false){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se actualizo correctamente.";
	  	 $dato["id_usuario"]=$id;
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al actualizar Usuario.' ));	
		}
	}
	*/
	else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Ya existe una cuenta con el mismo USUARIO.' ));	
	}
	
}


function registrar_usuario_autenticar()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
	

    	$id=Usuario::existe_celular_2($dato['celular']);
    	if($id=='-1'){

    	  $retorno=Usuario::registrar_usuario_autenticar(
    	  	$dato['nombre'],
    	  	$dato['apellido'],
    	  	$dato['celular'],
    	  	$dato['correo'],
    	  	$dato['contrasenia'],
    	  	$dato['token'],
    	  	$dato['codigo'],
    	  	$dato['aplicacion']);

		  if ($retorno!="-1"){	
			 $dato["suceso"]= "1";
			 $dato["mensaje"]= "Se registro correctamente.";
		  	 $dato["id_usuario"]=$retorno;
			print json_encode($dato);				
			} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
			}
 		}else if($id)
		{
		 $retorno=Usuario::actualizar_cuenta_directo(
		 	$dato['nombre'],
		 	$dato['apellido'],
		 	$dato['token'],
		 	$dato['correo'],
		 	$id,
		 	$dato['aplicacion']);

		 if ($retorno==true){	
			 $resultado["suceso"]= "1";
			 $resultado["mensaje"]= "Se registro correctamente.";
		  	 $resultado["id_usuario"]=$id;
			print json_encode($resultado);				
			} else { 
				print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al actualizar sus datos.' ));	
			}
 		}


 
	
}

function registrar_usuario_facebook_directo()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
$id=Usuario::existe_cuenta_facebook($dato['id_facebook']);
    if($id=="-1")
    {
    	$retorno=Usuario::registrar_usuario_facebook_directo($dato['nombre'],$dato['apellido'],$dato['email'],$dato['token'],$dato['id_facebook'],$dato['imei'],$dato['aplicacion']);
	  if ($retorno!="-1"){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se registro correctamente.";
	  	 $dato["id_usuario"]=$retorno;
	  	 $dato["celular"]="";
	  	 $dato["correo"]=$dato['email'];
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
	}
	else if($id)
	{
		$retorno=Usuario::actualizar_cuenta_directo($dato['nombre'],$dato['apellido'],$dato['token'],$dato['email'],$id['id'],$dato['imei']);
	  if ($retorno!=false){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se actualizo correctamente.";
	  	 $dato["id_usuario"]=$id['id'];
	  	 $dato["celular"]=$id['celular'];
	  	 $dato["correo"]=$id['correo'];
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al actualizar Usuario.' ));	
		}
	}
	else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario ..' ));	
	}
	
}

function registrar_usuario_google_directo()
{
	
	$dato=json_decode(file_get_contents("php://input"),true);
$id=Usuario::existe_cuenta_google($dato['id_google']);
    if($id=="-1")
    {
    	$retorno=Usuario::registrar_usuario_google_directo($dato['nombre'],$dato['apellido'],$dato['email'],$dato['token'],$dato['id_google'],$dato['imei'],$dato['aplicacion']);
	  if ($retorno!="-1"){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se registro correctamente.";
	  	 $dato["id_usuario"]=$retorno;
	  	 $dato["celular"]="";
	  	 $dato["correo"]=$dato['email'];
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.' ));	
		}
	}
	else if($id)
	{
		$retorno=Usuario::actualizar_cuenta_directo($dato['nombre'],$dato['apellido'],$dato['token'],$dato['email'],$id['id'],$dato['imei']);
	  if ($retorno!=false){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Se actualizo correctamente.";
	  	 $dato["id_usuario"]=$id['id'];
	  	 $dato["celular"]=$id['celular'];
	  	 $dato["correo"]=$id['correo'];
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al actualizar Usuario.' ));	
		}
	}
	else
	{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario ..' ));	
	}
	
}



function solicitar_registro()
{	

$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::solicitar_registro($dato['nombre'],$dato['apellido'],$dato['celular'],$dato['email'],$dato['contrasenia']);
	if($retorno===true)
	{print json_encode(array('suceso' => '1' ,'mensaje'=>'Por favor confirme su registro en su correo electronico.' ));
	}
	else{
	print	json_encode(array('suceso' => '2' ,'mensaje'=>'Tenemos problemas al enviar el correo.' ));
	}
}


function actualizar_contrasenia()
{	
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::actualizar_contrasenia($dato['id_usuario'],$dato['antigua'],$dato['nueva']);
	$row=Usuario::get_estado_contrasenia($dato['id_usuario'],$dato['nueva']);
	if ($retorno===true && $row==true){	
		 $dato["suceso"]= "1";
		 $dato["mensaje"]= "Modificacion Correcta.";
		print json_encode($dato);				
		} else { print json_encode(array('suceso' => '2' ,'mensaje'=>'Los datos que ingreso no son validos.' ));	
		}
}

function correro_de_modificacion_usuario()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::correro_de_modificacion_usuario($dato['id_usuario'],$dato['nombre'],$dato['apellido'],$dato['celular'],$dato['email']);
	if($retorno===true)
	{print json_encode(array('suceso' => '1' ,'mensaje'=>'Por favor confirme la modificacion en su correo electronico.' ));
	}
	else{
	 print 	json_encode(array('suceso' => '2' ,'mensaje'=>'Tenemos problemas al enviar el correo.' ));
	}
}

function insertar_imagen()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::insertar_imagen($dato['id_usuario'],$dato['imagen']);

	if($retorno==true)
	{print json_encode(array('suceso' => '1' ,'mensaje'=>'Insertado correctamente.' ));
	}
	else{
	 print	json_encode(array('suceso' => '2' ,'mensaje'=>'Tenemos problemas al subir la imagen.' ));
	}
}

function insertar_imagen_taxi()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$retorno=Usuario::insertar_imagen_taxi($dato['ci'],$dato['imagen']);

	if($retorno==true)
	{print json_encode(array('suceso' => '1' ,'mensaje'=>'Insertado correctamente.' ));
	}
	else{
	 print	json_encode(array('suceso' => '2' ,'mensaje'=>'Tenemos problemas al subir la imagen.' ));
	}
}

function lista_video()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$row=Usuario::lista_video();
	if ($row!="-1") {
		$dato["suceso"]="1";
		$dato["mensaje"]="Lista descargada correctamente.";
		$dato["video"]=$row;
		print json_encode($dato);
	}
	else
	{
		print json_encode(array('suceso' =>'2' ,'mensaje'=>'Lista.' ));
	}
}


function enviar_sms()
{
		$dato=json_decode(file_get_contents("php://input"),true);
		$validar=enviar_solicitud_de_codigo($dato['celular']);
		$validar = json_decode($validar,true);
			 		
		if ($validar['success']==true) 
		{print json_encode( array('suceso' => '1','mensaje' => 'Mensaje enviado.' ));
		}else{
			print json_encode( array('suceso' => '2','mensaje' => 'CODIGO: 2019' ));
			//print json_encode( array('suceso' => '2','mensaje' => 'Vuelva a intentarlo nuevamente.' ));
		}

		
	
}

function verificar_codigo_sms(){
	
	$dato=json_decode(file_get_contents("php://input"),true);

	$evaluar = verificar_codigo($_GET['celular'],$_GET['codigo']);
	$validar = json_decode($evaluar,true);
			 		
	if ($validar['success']==true) 
	{
		print json_encode( array('suceso' => '1','mensaje' => 'Codigo de identificación Correcta.' ));
	}else{
		print json_encode( array('suceso' => '2','mensaje' => 'Codigo de verificación Incorrecta. Vuelva a intentarlo.' ));
	}
}














function enviar_solicitud_de_codigo($criterio)
{
	$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.authy.com/protected/json/phones/verification/start?via=sms&country_code=591&phone_number='".$criterio."'&locale=es",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER => array(
				    "cache-control: no-cache",
				    "x-authy-api-key: 1DkdkrHQ0rtLq16iiAXSGsJOp1qS2HKn"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) 
				{
				  return $err;
				} 
				else 
				{
				 return $response;
				}
}


function verificar_codigo($nro,$cod)
{
				$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.authy.com/protected/json/phones/verification/check?country_code=591&phone_number='".$nro."'&verification_code=".$cod,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "x-authy-api-key: 1DkdkrHQ0rtLq16iiAXSGsJOp1qS2HKn"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  //fecho "cURL Error #:" . $err;
} else {

  return $response;
}
}


 ?>
