<?php
require 'Basededatos.php';
class Tarifa extends Database
{
  public function Tarifa()
  {
    parent::Database();
  }
  function get_tarifa()
  {
   try{

		$consulta="SELECT * from tarifa";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		$comando->execute();
		$row=$comando->fetchAll();
 		return $row ;
		} catch (PDOException $e) {
		   return -1;
		}
  }
  function get_ultima_tarifa()
  {
  	try{
  		$consulta="SELECT * from tarifa order by id desc limit 1";
  		$comando=parent::getInstance()->getDb()->prepare($consulta);
  		$comando->execute();
  		$row=$comando->fetch(PDO::FETCH_ASSOC);
  		return $row;
  	}catch(PDOException $e)
  	{
  		return -1;
  	}
  }









  //datos de la empresa para confundir

  function get_datos_de_empresa_pagina($id_empresa){
           //OBTIENE LOS DATOS DE LA EMPRESAS PARA PODER MOSTRAR EN LA PAGINA WEB
    $consulta="SELECT nit, tipo,razon_social,direccion,latitud,longitud,correo,mision,vision,facebook,whatsapp from empresa where id='".$id_empresa."'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute();
     $empresa=$comando->fetchAll();

    $consulta="SELECT d.nombre,d.paterno,d.materno,d.celular,d.correo,d.direccion_imagen from duenio d, empresa e where e.ci_representante_legal=d.ci and  e.id='".$id_empresa."'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute();
    $propietario=$comando->fetchAll();


    $consulta="SELECT fo.direccion from foto fo, fotocopia f, empresa e where fo.id_fotocopia=f.id and e.id_imagen_corporativa=f.id and  e.id='".$id_empresa."'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute();
    $imagen_corporativa=$comando->fetchAll();


    $consulta="SELECT numero from telefono where id_empresa='".$id_empresa."'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute();
    $telefono=$comando->fetchAll();


        $row = array('empresa' => $empresa,'propietario'=>$propietario,'telefono'=>$telefono,'imagen_corporativa'=>$imagen_corporativa );       
        return $row;
    }
}
?>