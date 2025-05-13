<?php
class Fotos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					 SELECT f.cd_fotos,
							f.ds_titulo, 
							f.ds_caminho,
							TO_CHAR(f.dt_data, 'DD/MM/YYYY') AS dt_data,
							TO_CHAR(f.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
					   FROM acs.fotos f
					  ORDER BY f.dt_data DESC
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function getFoto( &$result, $args=array() )
	{
		$qr_sql = "
					 SELECT f.cd_fotos,
							f.ds_titulo, 
							f.ds_caminho,
							TO_CHAR(f.dt_data, 'DD/MM/YYYY') AS dt_data,
							TO_CHAR(f.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
					   FROM acs.fotos f
					  WHERE f.cd_fotos = ".intval($args['cd_fotos']);

		$result = $this->db->query($qr_sql);
	}

	function fotoSalvar(&$result, $args=array())
	{
		if(intval($args['cd_fotos']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE acs.fotos
						   SET dt_data    = ".(trim($args['dt_data']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_data']."','DD/MM/YYYY')").",
							   ds_titulo  = ".(trim($args['ds_titulo']) == "" ? "DEFAULT" : "'".$args['ds_titulo']."'").",
							   ds_caminho = ".(trim($args['ds_caminho']) == "" ? "DEFAULT" : "'".$args['ds_caminho']."'")."
						 WHERE cd_fotos = ".intval($args['cd_fotos'])."
					  ";	
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_fotos']);
		}
		else
		{
			$new_id = intval($this->db->get_new_id("acs.fotos", "cd_fotos"));
			$qr_sql = " 
						INSERT INTO acs.fotos
						     (
                               cd_fotos,
							   dt_data,
                               ds_titulo,
                               ds_caminho,
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['dt_data']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_data']."','DD/MM/YYYY')").",
							   ".(trim($args['ds_titulo']) == "" ? "DEFAULT" : "'".$args['ds_titulo']."'").",
							   ".(trim($args['ds_caminho']) == "" ? "DEFAULT" : "'".$args['ds_caminho']."'").",
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			$retorno = intval($new_id);
		}
		
		#echo "<pre>$qr_sql</pre>";exit;
		
		return $retorno;
	}	
}
?>