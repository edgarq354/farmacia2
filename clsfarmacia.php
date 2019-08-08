<?php
include_once 'Basededatos.php';
include_once 'Push.php';
include_once 'Firebase.php';

class Farmacia extends Database
{
    
	public function Farmacia()
	{
 		parent::Database();
 		
	}


	public static function enviar_usuario_desaparecido($id_usuario,$id_desaparecido,$mensaje,$titulo,$yo,$latitud,$longitud)
	{
		try{
		$consulta="INSERT INTO chat (id_usuario,id_desaparecido,mensaje,titulo,yo,latitud,longitud) values(?,?,?,?,?,?,?)";
		
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_usuario,$id_desaparecido,$mensaje,$titulo,$yo,$latitud,$longitud));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
			echo $e;
		   return -1;
		}
	}

		
	public static function enviar_mensaje($id_usuario,$id_conductor,$mensaje,$titulo,$yo)
	{
		try{
		$consulta="INSERT INTO chat (id_usuario,id_conductor,mensaje,titulo,yo) values(?,?,?,?,?)";
		
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_usuario,$id_conductor,$mensaje,$titulo,$yo));
 		$lastId = parent::getInstance()->getDb()->lastInsertId();
 		return $lastId;
		} catch (PDOException $e) {
			echo $e;
		   return -1;
		}
	}

	public static function notificacion_chat_enviado_usuario($id_usuario,$id_conductor,$mensaje,$titulo ,$estado,$id_chat,$yo)
	   {$resultado=false;
	   	try{
			$consulta="SELECT token FROM usuario WHERE id=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_usuario));
	 		
	 			 $tokens = array(); 
	        while($fila=$comando->fetch(PDO::FETCH_OBJ)) {
				 array_push($tokens, $fila->token);
			    }
			   // $detalle="Señor usuario le faltan 1 Carreras para que tenga su primera carrera gratis.";

			     $push = new Push($titulo,$mensaje,null,"usuario","","","0","0","100");
			     $push->setId_usuario($id_usuario);
			     $push->setId_conductor($id_conductor);
			     $push->setEstado($estado);
			     $push->setId_chat($id_chat);
			     $push->setYo($yo);
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
			  echo $e;
			}
			return $resultado;
	   }


	   public static function notificacion_chat_enviado_conductor($id_usuario,$id_conductor,$mensaje,$titulo,$estado,$id_chat,$yo)
	   {$resultado=false;
	   	try{
			$consulta="SELECT token FROM conductor WHERE ci=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_conductor));
	 		
	 			 $tokens = array(); 
	        while($fila=$comando->fetch(PDO::FETCH_OBJ)) {
				 array_push($tokens, $fila->token);
			    }
			   // $detalle="Señor usuario le faltan 1 Carreras para que tenga su primera carrera gratis.";

			     $push = new Push($titulo,$mensaje,null,"usuario","","","0","0","100");
			     $push->setId_usuario($id_usuario);
			     $push->setId_conductor($id_conductor);
			     $push->setEstado($estado);
			     $push->setId_chat($id_chat);
			     $push->setYo($yo);
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

  
	public static function lista_farmacia($id_usuario,$texto,$latitud,$longitud)
   {$resultado=-1;
   	try{
			$consulta="SELECT * from tbfarmacia";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_usuario,$texto,$latitud,$longitud));
	 		$row=$comando->fetchAll();
			if($row)
			{
				$resultado=$row;
			}
			else
				{
					$resultado=-1;
				}

	   		}catch (PDOException $e) {
				  $resultado=-1;
				}
	return $resultado; 
	}
	


}


	 