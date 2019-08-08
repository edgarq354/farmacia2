<?php
include_once 'Basededatos.php';
include_once 'Push.php';
include_once 'Firebase.php';

class Corporativo extends Database
{
    
	public function Corporativo()
	{
 		parent::Database();
 		
	}

	public static function verificar_administrador_empresa($id_usuario)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from empresa where tipo like '%CLIENTE%' and id_administrador=? ";

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

	public static function verificar_administrador_lugar($id_usuario)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from lugar where  id_administrador=? ";

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


	public static function monto_deuda_empresa($id_empresa)
	{
		$resultado=0;
   		try{
			$consulta="SELECT sum(pp.monto_empresa) as 'monto_total' from pedido pp where  pp.estado_pago_empresa=0 and pp.id_empresa_cliente=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_empresa));
	 		$row=$comando->fetch(PDO::FETCH_ASSOC);
	 		if($row)
	 		{
	 			$resultado=$row['monto_total'];
	 		}
		} catch (PDOException $e) {
		  $resultado=0;
		}
		return $resultado;
	}

	public  function lista_de_usuarios_por_id_empresa($id_empresa)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from usuario where id_empresa=?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_empresa));
	 		$row=$comando->fetchAll();
	 		if($row)
	 		{
	 			$resultado=$row;
	 		}

		} catch (PDOException $e) {
		  $resultado=-1;

		}
		return $resultado;
	}

	public static function lista_de_usuarios_sin_empresa($celular)
	{

		$resultado='-1';
   		try{
			$consulta="SELECT * from usuario where id_empresa=0 and celular like '%".$celular."%' and id not in(select id_administrador from empresa where tipo like '%CLIENTE%')";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute();
	 		$row=$comando->fetchAll();

	 		if($row)
	 		{
	 			$resultado=$row;
	 		}
		} catch (PDOException $e) {
		  $resultado='-1';

		}
		return $resultado;
	}

  public static function get_token_id_usuario($id_usuario)
   { $query = parent::getInstance()->getDb()->prepare("SELECT  token from usuario where  id=?");
        $query->execute(array($id_usuario)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }


   public static  function agregar_usuario_empresa($id_usuario,$id_administrador,$id_empresa)
   {
   	try{
		$consulta="UPDATE usuario SET id_empresa=?,id_usuario_registro=? where id=? and id_empresa=0";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_empresa,$id_administrador,$id_usuario));
 		
	 		try{
				$consulta="SELECT id_empresa from usuario where id=?";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
		 		$comando->execute(array($id_usuario));
		 		$row=$comando->fetch(PDO::FETCH_ASSOC);
		 		if($row['id_empresa']==$id_empresa)
		 		{
		 			//enviar notificacion.
		 			$token=self::get_token_id_usuario($id_usuario);
					$push = new Push('Corporativo','Señor Usuario: Ahora ya puedes solicitar tu Movil Corporativo.',null,"usuario","","","","","7");
					 $mPushNotification = $push->getPush(); 
					 $firebase = new Firebase(); 
					 //fin de enviar notificacion
					 
					 // envío de notificación push y visualización de resultados
					  $firebase->send($token, $mPushNotification);
		 			return 1;

		 		}else {
		 			return 0;
		 		}
			} catch (PDOException $e) {
				
			  	return 0;
			}

		} catch (PDOException $e) {
		 return 0;
		}
   }

   public static  function eliminar_usuario_empresa($id_usuario,$id_administrador,$id_empresa)
   {
   	try{
		$consulta="UPDATE usuario SET id_empresa=0,id_usuario_registro=? where id=? ";
		$comando=parent::getInstance()->getDb()->prepare($consulta);
 		$comando->execute(array($id_administrador,$id_usuario));
 		
	 		try{
				$consulta="SELECT id_empresa from usuario where id=?";
				$comando=parent::getInstance()->getDb()->prepare($consulta);
		 		$comando->execute(array($id_usuario));
		 		$row=$comando->fetch(PDO::FETCH_ASSOC);
		 		if($row['id_empresa']==0)
		 		{
		 			//enviar notificacion.
		 			$token=self::get_token_id_usuario($id_usuario);
					$push = new Push('Corporativo','Señor Usuario: Su cuenta ya no puede solicitar Moviles Corporativos.',null,"usuario","","","","","7");
					$mPushNotification = $push->getPush(); 
					 $firebase = new Firebase(); 
					 //fin de enviar notificacion
					 
					 // envío de notificación push y visualización de resultados
					  $firebase->send($token, $mPushNotification);
		 			return 1;
		 		}else {
		 			return 0;
		 		}
			} catch (PDOException $e) {
			  	return 0;
			}

		} catch (PDOException $e) {
		 return 0;
		}
   }

}


		

 ?>