<?php
include_once 'Basededatos.php';
include_once 'Push.php';
include_once 'Firebase.php';

class Usuario extends Database
{
    
	public function Usuario()
	{
 		parent::Database();
 		
	}

	
	
/**
	* Obtiene el nro y contraseña para loguearse
	* a la base de datos
	* @param $nro    nro del usuario
	* @param $password    contraseña del usuario
	*/
	
 public static function set_ubicacion_punto_panico($latitud,$longitud,$id_usuario,$placa,$id_panico,$numero,$distancia,$rotacion)
  {
    if(self::existe_ruta_por_id_panico_numero($id_panico,$numero)==false){
     		 try{
		    $consulta="INSERT ruta_panico (latitud,longitud,id_panico,numero,distancia,rotacion)values(?,?,?,?,?,?)";
		    $comando=parent::getInstance()->getDb()->prepare($consulta);
		    $comando->execute(array($latitud,$longitud,$id_panico,$numero,$distancia,$rotacion));
		    
		    return true;
		    }catch(PDOException $e)
		    {
		      return false;
		    }
   		
    }else 
    {
      return false;
    }

  }

  public static function existe_ruta_por_id_panico_numero($id_panico,$numero)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT numero from ruta_panico WHERE  id_panico= ? and numero=? limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_panico,$numero));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $resultado=true;
            }   
            else{
              $resultado=false;
            }   
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }
    
	public static function insertar_perfil($nombre,$apellido,$celular,$email,$contrasenia,$token)
	{
		try{
		$consulta="INSERT INTO tbusuario (nombre,apellido,celular,correo,contrasenia,token) values(?,?,?,?,?,?)";
		
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$celular,$email,$contrasenia,$token));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		   return false;
		}
	}

		public static function notificacion_usuario_cantidad_9()
   {$resultado=false;
   	try{
		$consulta="SELECT cantidad_solicitud,token FROM tbusuario WHERE cantidad_solicitud%10=0 ";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute();
 		
 			 $tokens = array(); 
        while($fila=$comando->fetch(PDO::FETCH_OBJ)) {
			 array_push($tokens, $fila->token);
		    }
		    $detalle="Señor usuario su siguiente Servicio por aplicación sera gratuita.";
		   // $detalle="Señor usuario le faltan 1 Carreras para que tenga su primera carrera gratis.";

		     $push = new Push('Taxi-Valle',$detalle,null,"taxi","","","0","0","17");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos		 

			// creación de objeto de clase firebase
			 $firebase = new Firebase(); 
			 // envío de notificación push y visualización de resultados
			 $sw_notificacion=$firebase->send($tokens, $mPushNotification);
			 if($sw_notificacion===false)
			 {
			 	$resultado=false;
			 }
			 else
			 {
				 $resultado=true;	
			 }

		} catch (PDOException $e) {
		  $resultado=false;
		}
		return $resultado;
   }

	public static function get_perfil($id_usuario)
   {$resultado=-1;
   	try{
		$consulta="SELECT * from tbusuario where id=? ";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_usuario));
 		$row=$comando->fetch(PDO::FETCH_ASSOC);
 		if($row)
 		{
 			$resultado=$row;
 		}
		} catch (PDOException $e) {
		  $resultado=-1;
		}
		return $resultado;
   }

	public static function cambiar_numero_celular($id_usuario,$celular,$id_facebook,$id_google,$token,$imei,$correo)
	{
		$resultado=-1;

		$resultado_existe=-1; 
		$usuario="";



		   	try{
				$consulta="SELECT * from tbusuario where celular=?";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
		 		$comando->execute(array($celular));
		 		$row=$comando->fetch(PDO::FETCH_ASSOC);
		 		if($row)
		 		{
		 			$resultado_existe=$row['id'];
		 			$usuario=$row;
		 		 
		 		}
				} catch (PDOException $e) {
				 
				  $resultado_existe=-1;
				}

	  
	    if($resultado_existe!=-1)
	    {
	    	if(strlen($id_facebook)<5) {
	    		$id_facebook=$usuario['id_facebook'];
	    	}

	    	if(strlen($id_google)<5) {
	    		$id_google=$usuario['id_google'];
	    	}
	    	if(strlen($correo)<8) {
	    		$correo=$usuario['correo'];
	    	}


	    	try{
			$consulta="UPDATE usuario SET celular=?,id_facebook=?,id_google=?,token=?,imei=?,correo=? where id=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($celular,$id_facebook,$id_google,$token,$imei,$correo,$resultado_existe));
	 		 
	 		$resultado= $resultado_existe;
			} catch (PDOException $e) {
			 
			  $resultado= -1;
			}

			try{
			$consulta="DELETE FROM usuario where id=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(  array($id_usuario));
			} catch (PDOException $e) {
				 
			}

	    }else{
 			try{
			$consulta="UPDATE usuario SET celular=?  where id=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($celular, $id_usuario));
	 		$resultado= $id_usuario;
			} catch (PDOException $e) {
			 
			  $resultado= -1;
			}
	    }
	    return $resultado;
	}


	public static function actualizar_usuario($nombre,$apellido,$email,$token,$id)
	{
	try{
		$consulta="UPDATE usuario SET nombre=?,apellido=?,token=? where correo=? and id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute($nombre,$apellido,$telefono,$email,$token,$id);
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
	}
	public static function actualizar_dato($nombre,$apellido,$email,$celular,$id)
	{

	try{
		$consulta="UPDATE usuario SET nombre=?,apellido=? ,correo=? where  id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido, $email,$id));
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
	}

	public static  function actualizar_token($id_usuario,$token)
   {
   	try{
		$consulta="UPDATE usuario SET token=?  where id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($token,$id_usuario));
 	return true;
		} catch (PDOException $e) {
		 return false;
		}
   }
  

   public static function iniciar_sesion($codigo,$contrasenia,$token,$imei)
   {$resultado=-1;
   	try{
		$consulta="SELECT * from usuario where codigo=? and contrasenia=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($codigo,$contrasenia));
 		$row=$comando->fetch(PDO::FETCH_ASSOC);
 		if($row)
 		{
 			$resultado=$row;
 			self::actualizar_token($row['id'],$token,$imei);
 		}
		} catch (PDOException $e) {
		  $resultado=-1;
		}
		return $resultado;
   }

   public static function iniciar_sesion_con_celular($celular,$token)
   {$resultado=-1;
   	try{
		$consulta="SELECT * from tbusuario where celular=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($celular));
 		$row=$comando->fetch(PDO::FETCH_ASSOC);
 		if($row)
 		{
 			$resultado=$row;
 			self::actualizar_token($row['id'],$token);
 		}
		} catch (PDOException $e) {
		  $resultado=-1;
		}
		return $resultado;
   }


public static  function get_token_moto()
   {   $query = parent::getInstance()->getDb()->prepare("SELECT token FROM usuario");
        $query->execute(); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 array_push($tokens, $row->token);
    }
        return $tokens; 
   }
public static function pedir_moto($id_usuario,$latitud,$longitud)
  {try{
	   $push = new Push('Pedir Taxi','',null);
     // obteniendo el empuje del objeto push
		 $mPushNotification = $push->getPush(); 
		 
		 // obtener el token del objeto de base de datos

		 $devicetoken = self::get_token_moto();		 

		// creación de objeto de clase firebase
		 $firebase = new Firebase(); 
		 
		 // envío de notificación push y visualización de resultados
		  $firebase->send($devicetoken, $mPushNotification);
		return true;
		}
	catch (Exception $e){
return false;
	}
  }

 	public static  function actualizar_contrasenia($id_usuario,$antigua,$nueva)
   {
   	try{
		$consulta="UPDATE usuario SET contrasenia=? where id=? and contrasenia=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nueva,$id_usuario,$antigua));
 	return true;
		} catch (PDOException $e) {
		 return false;
		}
   }

    public static function get_estado_contrasenia($id_usuario,$contrasenia)
  {
    try{
      $consulta="SELECT id from usuario where id=? and contrasenia=?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_usuario,$contrasenia));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return true;
      }else
      {
        return false;
      }    
    }catch(PDOException $e)
    {
      return false;
    }
  }

  
  public static function verificar_usuario_actual($id_usuario,$token)
  {
    try{
      $consulta="SELECT id,estado,descripcion_estado from usuario where id=? and token=?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_usuario,$token));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
       return $row;  
    }catch(PDOException $e)
    {
      return -1;
    }
  }

public static function verificar_conductor_actual($id_conductor,$token)
  {
    try{
      $consulta="SELECT ci from conductor where ci=? and token=?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_conductor,$token));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return true;
      }else
      {
        return false;
      }    
    }catch(PDOException $e)
    {
      return false;
    }
  }

  public static function verificar_conductor_vehiculo_actual($id_conductor,$id_vehiculo)
  {
    try{
      $consulta="SELECT ci from conductor c,vehiculo v where c.id_vehiculo=v.placa and c.ci=? and v.placa =? ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_conductor,$id_vehiculo));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return true;
      }else
      {
        return false;
      }    
    }catch(PDOException $e)
    {
      return false;
    }
  }

public static function registrar_usuario_facebook($nombre,$apellido,$celular,$correo,$contrasenia,$token,$codigo,$id_facebook,$imei)
   {
   	try{
		$consulta="INSERT INTO usuario (nombre,apellido,celular,correo,contrasenia,token,codigo,id_facebook,imei) values(?,?,?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$celular,$correo,$contrasenia,$token,$codigo,$id_facebook,$imei));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		 return "-1";
		}
	
   }

public static function registrar_usuario_autenticar($nombre,$apellido,$celular,$correo,$contrasenia,$token,$codigo,$aplicacion)
   {
   	try{
		$consulta="INSERT INTO tbusuario (nombre,apellido,celular,correo,contrasenia,token,codigo,aplicacion) values(?,?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$celular,$correo,$contrasenia,$token,$codigo,$aplicacion));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
			echo $e;
		 return "-1";
		}
	
   }


   public static  function actualizar_cuenta($nombre,$apellido,$celular,$contrasenia,$token,$email,$id,$codigo,$id_facebook)
   {
   	try{
		$consulta="UPDATE usuario SET nombre=?,apellido=?,celular=?,contrasenia=?,token=?,correo=?  where codigo=? and id=? and id_facebook=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$celular,$contrasenia,$token,$email,$codigo,$id,$id_facebook));
 	return true;
		} catch (PDOException $e) {
		 return false;
		}
   }

   public static function registrar_usuario_facebook_directo($nombre,$apellido,$email,$token,$id_facebook,$imei,$aplicacion)
   {
   	try{
		$consulta="INSERT INTO usuario (nombre,apellido,correo,token,id_facebook,imei,aplicacion) values(?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$email,$token,$id_facebook,$imei,$aplicacion));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		
		 return "-1";
		}
	
   }

   public static function registrar_usuario_google_directo($nombre,$apellido,$email,$token,$id_google,$imei,$aplicacion)
   {
   	try{
		$consulta="INSERT INTO usuario (nombre,apellido,correo,token,id_google,imei,aplicacion) values(?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$email,$token,$id_google,$imei,$aplicacion));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
		
		 return "-1";
		}
	
   }

   public static  function actualizar_cuenta_directo($nombre,$apellido,$token,$correo,$id,$aplicacion)
   {
   	try{
		$consulta="UPDATE tbusuario SET nombre=?,apellido=?,token=?,correo=?,aplicacion=?  where  id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($nombre,$apellido,$token,$correo,$aplicacion,$id));
 		 
 		  return true;
		} catch (PDOException $e) {
			
		  return false;
		}
   }

    public static function existe_cuenta($codigo)
   {$resultado="-1";
   	try{
		$consulta="SELECT * from usuario where codigo=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($codigo));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado= $row['id'];}
		 	else
		 	{
		   $resultado="-1";
		 	}
		} catch (PDOException $e) {
		  $resultado="-1";
		}
		return $resultado;
   }
     public static function existe_celular($celular,$id_usuario)
   {$resultado=false;
   	try{
		$consulta="SELECT celular from usuario where celular=? and id<>?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($celular,$id_usuario));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado= true;}
		 	else
		 	{
		   $resultado=false;
		 	}
		} catch (PDOException $e) {
		  $resultado=false;
		}
		return $resultado;
   }

     public static function existe_celular_2($celular )
   {$resultado="-1";
   	try{
		$consulta="SELECT id from tbusuario where celular=?  limit 1";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($celular));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado=$row['id'];}
		 	else
		 	{
		 $resultado="-1";
		 	}
		} catch (PDOException $e) {
		  $resultado="-1";
		  echo $e;
		}
		return $resultado;
   }


       public static function existe_cuenta_facebook($id_facebook)
   {$resultado="-1";
   	try{
		$consulta="SELECT id,celular,correo from usuario where id_facebook=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_facebook));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado= $row;}
		 	else
		 	{
		   $resultado="-1";
		 	}
		} catch (PDOException $e) {
		  $resultado="-1";
		}
		return $resultado;
   }


   public static function get_cargar_codigo($id_usuario,$codigo)
   {
   	/*
   	0: error al registrar.
	1: carga de codigo correctamente.
	2: codigo con fecha caducada.
	3: codigo ya usado anteriormente.
	4: codigo ya usado anteriormente.
	5: codigo incorrecto.
	6: codigo de compartir ya agregado anteriormente.
   	*/

   	$resultado="0";


   		if($codigo>=10001)
   		{


   			$row="";
   			 	try{
					$consulta="SELECT *,date(now()) as 'hoy' from codigo where  id=?";
					$comando=parent::getInstance()->getDb()->prepare($consulta);
			 		$comando->execute(array($codigo));
			 		$row=$comando->fetch(PDO::FETCH_ASSOC);
			 		} catch (PDOException $e) {
					  
					}
			 	 if($row)
				 	{
				 			$fecha_inicio = strtotime($row['fecha_inicio']);
     						$fecha_fin = strtotime($row['fecha_fin']);
     						$fecha = strtotime($row['hoy']);
     						$monto=$row['monto'];
     						$unico=$row['unico'];
     						$usuario=$row['usuario'];
     						$cantidad=$row['cantidad'];
     						/*
								USUARIO:
								0:todos
								1:pasajero
								2:conductor
     						*/

		     				if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin) && $usuario<=1) {
		     					$sw=false;

		     					if($unico==1&&$cantidad==0){
		     						try{
			     						$comando=parent::getInstance()->getDb()->prepare("INSERT into usuario_codigo  (id_usuario,id_codigo) values(?,?) ");
										$comando->execute(array($id_usuario,$codigo));
										$sw=true;
									} catch (PDOException $eee) {
										$sw=false;
									}
		     					}else if($unico==0)
		     					{
		     						try{
			     						$comando=parent::getInstance()->getDb()->prepare("INSERT into usuario_codigo  (id_usuario,id_codigo) values(?,?) ");
										$comando->execute(array($id_usuario,$codigo));
										$sw=true;
									} catch (PDOException $eee) {
										$sw=false;
									}	
		     					}
		     					


								if($sw==true)
								{
			 						try{
				     					 $consulta = "UPDATE usuario set billetera=billetera+? where id=?";
										 $comando = parent::getInstance()->getDb()->prepare($consulta);
										 $comando->execute(array($monto,$id_usuario));
										 $resultado="1";
									} catch (PDOException $ee) {
									  $resultado="3";
									}

									 //Aumentar la cantidad de codigos utlizados
									try{
				     					 $consultaCodigo = "UPDATE codigo set cantidad=cantidad+1 where id=?";
										 $comandoCodigo = parent::getInstance()->getDb()->prepare($consultaCodigo);
										 $comandoCodigo->execute(array($codigo));
									} catch (PDOException $eee) {
									}

								}else
								{
									$resultado="4";

								}

		     				 


						 	}else{
						 			$resultado="2";
						 	}


				 	}
					else{
					   $resultado="5";
					}
					
   		}else
   		{


   			$row="";
   			$usuario="";
   			 	try{
					$consulta="SELECT * from ajuste where anio=YEAR(now())";
					$comando=parent::getInstance()->getDb()->prepare($consulta);
			 		$comando->execute();
			 		$row=$comando->fetch(PDO::FETCH_ASSOC);
			 		 
			 		} catch (PDOException $e) {
					 
					}

					try{
					$consulta_usu="SELECT * from usuario u,usuario p where u.id_usuario_codigo=p.id and u.id=?";
					$comando_usu=parent::getInstance()->getDb()->prepare($consulta_usu);
			 		$comando_usu->execute(array($id_usuario));
			 		$usuario=$comando_usu->fetch(PDO::FETCH_ASSOC);
			 		} catch (PDOException $usu) {
			 			$usuario="";
			 		 
					}
 
					if($row && $usuario==""){
						$monto_compartir=$row['monto_compartir'];
						$monto_agregar_codigo=$row['monto_agregar_codigo'];

						try{
     					 $consulta = "UPDATE usuario set billetera=billetera+? ,id_usuario_codigo=? where id=?";
						 $comando = parent::getInstance()->getDb()->prepare($consulta);
						 $comando->execute(array($monto_agregar_codigo,$codigo,$id_usuario));
						 $resultado="1";
						} catch (PDOException $ee) {
						
						  $resultado="0";
						   
						}

						try{
     					 $consulta = "UPDATE usuario set billetera=billetera+? where id=?";
						 $comando = parent::getInstance()->getDb()->prepare($consulta);
						 $comando->execute(array($monto_compartir,$codigo));
						} catch (PDOException $ee) {
							 
						}
					}else{
						$resultado="6";
					}

   		 

   		}

   	return $resultado;
   }


   
 public static function get_monto_billetera($id_usuario)
   {$resultado="0";
   	try{
		$consulta="SELECT billetera from usuario where id=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_usuario));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado= $row['billetera'];}
		 	else
		 	{
		   $resultado="0";
		 	}
		} catch (PDOException $e) {
		  $resultado="0";
		}
		return $resultado;
   }


    public static function existe_cuenta_google($id_google)
   {$resultado="-1";
   	try{
		$consulta="SELECT id,celular,correo from usuario where id_google=?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_google));
 			$row=$comando->fetch(PDO::FETCH_ASSOC);
 			if($row)
 		{$resultado= $row;}
		 	else
		 	{
		   $resultado="-1";
		 	}
		} catch (PDOException $e) {
		  $resultado="-1";
		}
		return $resultado;
   }

     public static function insertar_imagen($id_usuario,$imagen)
	{
	
		try {
		
		 
		$direccion_imagen_png="usuario/imagen/perfil/".$id_usuario."_perfil.png";
		 
		self::guardar_imagen_png($imagen,$direccion_imagen_png);
		
 		$consulta = "UPDATE usuario set direccion_imagen= ? where id=?";

			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($direccion_imagen_png,$id_usuario));
			//Capturar primera fila del resultado
			return true;
			
		} catch (PDOException $e) {
			return false;
		}
	} 



  public static function insertar_imagen_perfil($id_usuario,$imagen)
  {

        $nuevo_nombre=$id_usuario."_perfil.png";
        $rutadelaimagen="usuario/imagen/perfil/".$nuevo_nombre;
        $direccion_imagen_png= $rutadelaimagen;
    
      $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)) {
               try{
                 $consulta = "UPDATE usuario set direccion_imagen= ? where id=?";
                  $comando=parent::getInstance()->getDb()->prepare($consulta);
                  $comando->execute(array($rutadelaimagen,$id_usuario));
                    $resultado=true;
                  } catch (PDOException $e) {
                    echo $e;
                  }
         }

      } 
      return $resultado;
  }


 public  function lista_video()
   {  
   	$resultado=-1;
   	$query = parent::getInstance()->getDb()->prepare("SELECT id,id_empresa,nombre,descripcion,url as 'url' from video order by nombre asc");
        $query->execute(); 
		$row=$query->fetchAll();
		if($row)
		{
			$resultado=$row;
		}
		else
			{
				$resultado=-1;
			}
	return $resultado;

   }


   public static function insertar_imagen_taxi($id_taxi,$imagen)
	{
		
		try {

		$direccion_png="Imagen_Conductor/Perfil-".$id_taxi.".png";
		$direccion_imagen_png="../taxivalle/public/".$direccion_png;
		
		self::guardar_imagen_png($imagen,$direccion_imagen_png);

		//BUSCAR IMAGENES GUARDADOS EN LA BASE DE DATOS..
			 
            $cantidad=0;
            $id_fotocopia=0;

			$comando=parent::getInstance()->getDb()->prepare("SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_perfil and c.ci='".$id_taxi."'");
	 		$comando->execute();
            
            while($row=$comando->fetch(PDO::FETCH_OBJ)) {
			  $cantidad=$row->cantidad;
              $id_fotocopia=$row->id_fotocopia;
		    }

            $cantidad+=1;


			$nuevo_nombre="Imagen_PERFIL-".$id_taxi."-".$cantidad.".png";
	        $rutade_la_imagen_local="../taxivalle/storage/Imagen_Conductor/".$nuevo_nombre;
	        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;
			self::guardar_imagen_png($imagen, $rutade_la_imagen_local);

           

                            

                    if($cantidad==1){
                        $comando=parent::getInstance()->getDb()->prepare("INSERT into fotocopia (nombre) values('PERFIL') ");
						$comando->execute();
					    $id_fotocopia= parent::getInstance()->getDb()->lastInsertId();
                     }
						$comando=parent::getInstance()->getDb()->prepare("INSERT into foto  (id,direccion,detalle,id_fotocopia) values(?,?,'Subido por el conductor',?) ");
						$comando->execute(array($cantidad,$rutadelaimagen,$id_fotocopia));

 			$consulta = "UPDATE conductor set direccion_imagen= ?,id_fotocopia_perfil=? where ci=?";
			//preparar sentencia
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			//Ejecutar sentencia preparada
			$comando->execute(array($direccion_png,$id_fotocopia,$id_taxi));
			//Capturar primera fila del resultado
			return true;
			
		} catch (PDOException $e) {
			return false;
		}
	}

 

public static function solicitar_registro($nombre,$apellido,$celular,$email,$contrasenia)
{try{
	$consulta="SELECT * from pagina where tipo='solicitar_registro'";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute();
 		$row=$comando->fetch(PDO::FETCH_ASSOC);

	$mensaje=$row['body'];
	$mensaje=str_replace('{{nombre}}',$nombre, $mensaje);
	$mensaje=str_replace('{{apellido}}',$apellido, $mensaje);
	$mensaje=str_replace('{{celular}}',$celular, $mensaje);
	$mensaje=str_replace('{{email}}',$email, $mensaje);
	$mensaje=str_replace('{{contrasenia}}',$contrasenia, $mensaje);

	// Cabecera que especifica que es un HMTL
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Cabeceras adicionales
	$cabeceras .= 'From: Easytaxi <edgarq354@gmail.com>' . "\r\n";
	$cabeceras .= 'Cc: edgarq354@gmail.com' . "\r\n";
	$cabeceras .= 'Bcc: edgarq354@gmail.com' . "\r\n";

$para=$email;
	// enviamos el correo!
	mail($para, "Solicitud de registro", $mensaje, $cabeceras);
	   	return true;
	   }
	   catch(PDOException $e)
	   {
	   	return false;
	   }
   
}
  public static  function correro_de_modificacion_usuario($id_usuario,$nombre,$apellido,$celular,$email)
   {try{
   	$consulta="SELECT * from pagina where tipo='modificar_perfil'";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute();
 		$row=$comando->fetch(PDO::FETCH_ASSOC);

	$mensaje=$row['body'];
   	$mensaje=str_replace('{{id_usuario}}',$id_usuario, $mensaje);
	$mensaje=str_replace('{{nombre}}',$nombre, $mensaje);
	$mensaje=str_replace('{{apellido}}',$apellido, $mensaje);
	$mensaje=str_replace('{{celular}}',$celular, $mensaje);
	$mensaje=str_replace('{{email}}',$email, $mensaje);

	// Cabecera que especifica que es un HMTL
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Cabeceras adicionales
	$cabeceras .= 'From: Easytaxi <edgarq354@gmail.com>' . "\r\n";
	$cabeceras .= 'Cc: edgarq354@gmail.com' . "\r\n";
	$cabeceras .= 'Bcc: edgarq354@gmail.com' . "\r\n";


	$consulta="SELECT * from usuario where id= ?";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_usuario));
 		$row=$comando->fetch(PDO::FETCH_ASSOC);
	$para=$row['email'];

	// enviamos el correo!
	mail($para, "Modificacion de Contraseña", $mensaje, $cabeceras);
	   	return true;
    	}
	   catch(PDOException $e)
	   {
	   	return false;
	   }
}



	public function guardar_imagen($dato,$file)
	{
		if($dato!="")
		{
		$success=file_put_contents($file, $dato);
		}
	}

	public function guardar_imagen_png($dato,$file)
	{
		if($dato!="")
		{

    // Imagen png codificada en base64.
    $v_imagen_Base64 = "data:image/png;base64,".$dato;

    // Eliminamos data:image/png; y base64, de la cadena que tenemos
    // Hay otras formas de hacerlo
    list(, $v_imagen_Base64) = explode(';', $v_imagen_Base64);
    list(, $v_imagen_Base64) = explode(',', $v_imagen_Base64);

    // Decodificamos $Base64Img codificada en base64.
    $v_imagen_Base64 = base64_decode($v_imagen_Base64);
    
    // Escribimos la información obtenida en un archivo para que se cree la imagen correctamente
    $file=str_replace(".txt", ".png", $file);
    file_put_contents($file, $v_imagen_Base64);
		}
	}

	public static function lista_desaparecido()
		{
			$consulta="SELECT * from desaparecido ORDER by id desc limit 100";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute();
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

	public static function desaparecido_por_id($id)
		{
			$consulta="SELECT *,(year(now())-year(fecha_nacimiento)) as 'edad' from desaparecido where id=? ORDER by id desc limit 100";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}
	


  



}


		

 ?>