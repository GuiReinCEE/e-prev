<?php
class Videos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT v.cd_video, 
                           v.titulo,
						   v.ds_local,
                           TO_CHAR(v.dt_evento,'DD/MM/YYYY') AS dt_evento, 
	                       TO_CHAR(v.dt_atualizacao,'DD/MM/YYYY') AS dt_atualizacao 
                      FROM acs.videos v
					 WHERE v.dt_exclusao IS NULL
                     ORDER BY v.dt_evento DESC
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function getVideo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT v.cd_video,
					       v.titulo,
						   v.ds_local,
					       v.arquivo, 
						   v.arquivo_original,
						   v.diretorio,
						   TO_CHAR(v.dt_evento,'DD/MM/YYYY') AS dt_evento,
						   TO_CHAR(v.dt_atualizacao,'DD/MM/YYYY') AS dt_atualizacao,
						   ('http://srvimagem:1111/' || COALESCE(v.diretorio,'') || COALESCE(v.arquivo,'')) AS video_link,
						   ('http://srvimagem:1111/down.php?arq=' || COALESCE(v.diretorio,'') || COALESCE(v.arquivo_original,'')) AS video_down
					  FROM acs.videos v
					 WHERE v.cd_video = ".intval($args['cd_video']);
		$result = $this->db->query($qr_sql);
	}


	function videoSalvar(&$result, $args=array())
	{
		if(intval($args['cd_video']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE acs.videos
						   SET dt_atualizacao         = CURRENT_TIMESTAMP,
						       cd_usuario_atualizacao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario']).",
						       dt_evento              = ".(trim($args['dt_evento']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_evento']."','DD/MM/YYYY')").",
							   titulo                 = ".(trim($args['titulo']) == "" ? "DEFAULT" : "'".$args['titulo']."'").",
							   ds_local               = ".(trim($args['ds_local']) == "" ? "DEFAULT" : "'".$args['ds_local']."'").",
							   diretorio              = ".(trim($args['diretorio']) == "" ? "DEFAULT" : "'".$args['diretorio']."'").",
							   arquivo                = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
							   arquivo_original       = ".(trim($args['arquivo_original']) == "" ? "DEFAULT" : "'".$args['arquivo_original']."'")."
						 WHERE cd_video = ".intval($args['cd_video'])."
					  ";	
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_video']);
		}
		else
		{
			$new_id = intval($this->db->get_new_id("acs.videos", "cd_video"));
			$qr_sql = " 
						INSERT INTO acs.videos
						     (
                               cd_video,
							   dt_evento,
                               titulo,
                               ds_local,
                               diretorio,
                               arquivo,
                               arquivo_original,
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['dt_evento']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_evento']."','DD/MM/YYYY')").",
							   ".(trim($args['titulo']) == "" ? "DEFAULT" : "'".$args['titulo']."'").",
							   ".(trim($args['ds_local']) == "" ? "DEFAULT" : "'".$args['ds_local']."'").",
							   ".(trim($args['diretorio']) == "" ? "DEFAULT" : "'".$args['diretorio']."'").",
							   ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
							   ".(trim($args['arquivo_original']) == "" ? "DEFAULT" : "'".$args['arquivo_original']."'").",
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
