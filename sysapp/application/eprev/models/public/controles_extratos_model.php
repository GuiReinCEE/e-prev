<?php
class controles_extratos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT TRIM(TO_CHAR(ce.nro_extrato,'000')) AS nr_extrato,
						   ce.ano AS nr_ano,
						   TO_CHAR(ce.data_base,'YYYY-MM-DD') AS data_base,
						   TO_CHAR(ce.data_base,'DD/MM/YYYY') AS dt_extrato
					  FROM public.controles_extratos ce
					 WHERE ce.cd_empresa = ".intval($args["cd_empresa"])."
					   AND ce.cd_plano   = ".intval($args["cd_plano"])."
					   AND ce.dt_liberacao IS NOT NULL
					   AND ce.nro_extrato IN (SELECT DISTINCT(ep.nro_extrato)
											    FROM extrato_participantes ep
											   WHERE ep.cd_empresa = ".intval($args["cd_empresa"])."
												 AND ep.cd_registro_empregado = ".intval($args["cd_registro_empregado"])."
												 AND ep.seq_dependencia = 0
												 AND ep.cd_plano = ".intval($args["cd_plano"]).");
				   ";

		$result = $this->db->query($qr_sql);
	}
	
	function participante(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_plano, 
			       cd_empresa, 
				   cd_registro_empregado
		      FROM public.participantes
		     WHERE cd_empresa            = 9
		       AND cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
		       AND seq_dependencia       = 0";
		
		$result = $this->db->query($qr_sql);
	}
	
	function planos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_indexador
		      FROM public.planos_patrocinadoras
		     WHERE cd_empresa = ".intval($args['cd_empresa'])."
		       AND cd_plano   = ".intval($args['cd_plano']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function patrocinadora(&$result, $args=array())
	{
		$qr_sql = "
			SELECT tipo_cliente
		      FROM public.patrocinadoras
		     WHERE cd_empresa = 9;";
		
		$result = $this->db->query($qr_sql);
	}
}
?>