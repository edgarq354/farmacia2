<?php
include_once 'Basededatos.php';

class Guia_turistica extends Database
{
    
	public function Guia_turistica()
	{
 		parent::Database();	
	}

	public static function get_lugar_por_id_pedido($id)
	{
		$consulta="SELECT l.*  from pedido p,lugar l  where l.id=p.id_lugar and p.tipo_pedido=1 and p.id=? order by id desc";
		try{ 
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
			return $row;
			}
			else
			{
			return -1;
			}
			
		}catch(PDOException $e)
		{
			return -1;
		}
	}


	public static function lista_de_categoria()
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from categoria where estado=1 ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute();
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

public static function lista_de_lugar($id_categoria,$latitud,$longitud)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT *,distancia_entre_dos_puntos('".$latitud."','".$longitud."',latitud,longitud) as distancia from lugar where id_categoria=? ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_categoria));
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


	public static function lista_de_producto($id_lugar)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from producto where id_lugar=? ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_lugar));
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

	public static function lista_de_producto_todo($id_lugar)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from producto where id_lugar=? ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_lugar));
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



		public static function lista_de_categoria_delivery()
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from categoria where estado=1 and estado_pedido=1";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute();
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
		public static function lista_de_tipo_producto_delivery($id_lugar)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from tipo_producto where id in (select id_tipo_producto from producto where id_lugar=".$id_lugar." group by id_tipo_producto)";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute();
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

public static function lista_de_lugar_delivery($id_categoria,$latitud,$longitud)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT *,distancia_entre_dos_puntos('".$latitud."','".$longitud."',latitud,longitud) as distancia from lugar where id_categoria=? and estado_pedido=1 ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_categoria));
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


	public static function lista_de_producto_delivery($id_lugar)
	{
		$resultado=-1;
   		try{
			$consulta="SELECT * from producto where id_lugar=? and estado_pedido=1 ";

			$comando=parent::getInstance()->getDb()->prepare($consulta);
	 		$comando->execute(array($id_lugar));
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



  public static function actualizar_producto($id_producto,$precio)
   {
    try{
    $consulta="UPDATE producto SET precio=? where id=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($precio,$id_producto));
  return true;
    } catch (PDOException $e) {
     return false;
    }
   }


  public static function actualizar_producto_estado($id_producto,$estado)
   {
    try{
    $consulta="UPDATE producto SET estado_pedido=? where id=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($estado,$id_producto));
  return true;
    } catch (PDOException $e) {
     return false;
    }
   }


/*

	public function visitar_parque_samasa($edgar,$hilda)
	{
		$hora_salida=8;
		$hora=$hora_salida;
		if(self::sw_permiso_de_padre($hilda)==true)
		{
			if(self::reemplazo_de_tienda($edgar,$mariela)==true)
			{
				$hora_regreso=10;
				while($hora<=$hora_regreso)
				{
					switch ($hora) {
						case 8,15:
						//en los labios.
						self::dar_beso($edgar,$hilda);
							break;
						case 8,40:
						self::dar_abrazo($edgar,$hilda);
							break;
						case 9:
						self::panchito($edgar,$hilda);
							break;
						case 9,30:
						self::tomar_fotos($edgar,$hilda);
							break;				
						case 9,50:
						self::regresa_a_todo_motor($edgar,$hilda);
							break;
						default:
							# code...
							break;
					}
					$hora+=0.1;
				}
				echo "♥...Bonita noche de 28 de Diciembre de 2018 junto a mi Novia Hilda Flores Ruiz.....♥";
				echo "TE AMO MI AMOR ♥";
			}else
			{
				echo "Cerrar tienda y camino a la casa de ".$hilda;
			}
		}else
		{
			echo "Hablemos en la puertita";
		}

	}

*/



 
}


		

 ?>