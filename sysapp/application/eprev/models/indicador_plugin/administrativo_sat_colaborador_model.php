<?php
class administrativo_sat_colaborador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_administrativo_sat_colaborador,
		                   i.periodo_ini,
		                   i.periodo_fim,
		                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
		                   i.cd_usuario_inclusao,
		                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
		                   i.cd_usuario_exclusao,
		                   i.cd_indicador_tabela,
		                   i.fl_media,
		                   i.nr_valor_1,
		                   i.nr_valor_2,
		                   i.nr_percentual_f,
		                   i.nr_meta,
                           i.observacao,
						   CASE WHEN (SELECT MAX(i1.cd_administrativo_sat_colaborador)
						                FROM indicador_plugin.administrativo_sat_colaborador i1
						               WHERE i1.dt_exclusao IS NULL) = i.cd_administrativo_sat_colaborador
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM indicador_plugin.administrativo_sat_colaborador i
					 WHERE i.dt_exclusao IS NULL
		             ORDER BY i.periodo_ini ASC
		          ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$row = Array();
		$qr_sql = " 
					SELECT cd_administrativo_sat_colaborador,
		                   periodo_ini,
		                   periodo_fim,
					       TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
					       cd_usuario_inclusao,
					       TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
					       cd_usuario_exclusao,
					       cd_indicador_tabela,
					       fl_media,
					       nr_valor_1,
					       nr_valor_2,
					       nr_percentual_f,
					       nr_meta,
                           observacao
		              FROM indicador_plugin.administrativo_sat_colaborador 
					  ".(intval($cd) > 0 ? " WHERE cd_administrativo_sat_colaborador = ".intval($cd) : " LIMIT 1 ")."
				  ";
		$ob_resul = $this->db->query($qr_sql);
		
		if(intval($cd) > 0)
		{
			$row = $ob_resul->row_array();
		}
		else
		{
			$ar_campo = $ob_resul->field_data();
			foreach($ar_campo as $campo)
			{
				$row[$campo->name] = '';
			}		
		}
		
		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(floatval($args["nr_valor_2"]) > 0)
		{
			$nr_percentual_f = (floatval($args["nr_valor_2"])/floatval($args["nr_valor_1"])) * 100;
		}
		else
		{
			$nr_percentual_f = 0;
		}		
		
		if(intval($args['cd_administrativo_sat_colaborador']) == 0)
		{
			
			
			$qr_sql = "
						INSERT INTO indicador_plugin.administrativo_sat_colaborador 
						     ( 
							   periodo_ini,
							   periodo_fim,
							   dt_inclusao,
                               cd_usuario_inclusao,
                               cd_indicador_tabela,
                               fl_media,
                               nr_valor_1,
                               nr_valor_2,
                               nr_meta,
							   nr_percentual_f,
                               observacao
			                 ) 
					    VALUES 
						     ( 
                               ".intval($args["periodo_ini"]).",
                               ".intval($args["periodo_fim"]).",
			                   CURRENT_TIMESTAMP,
			                   ".intval($args["cd_usuario_inclusao"]).",
			                   ".intval($args["cd_indicador_tabela"]).",
			                   'N',
							   ".($args["nr_valor_1"]).",
							   ".($args["nr_valor_2"]).",
							   ".($args["nr_meta"]).",
							   ".$nr_percentual_f.",
                               '".trim($args["observacao"])."'
							 )
			          ";
		}
		else
		{
			$qr_sql = "
						UPDATE indicador_plugin.administrativo_sat_colaborador 
						   SET periodo_ini     = ".intval($args["periodo_ini"]).",
						       periodo_fim     = ".intval($args["periodo_fim"]).",
							   nr_valor_1      = ".($args["nr_valor_1"]).",
							   nr_valor_2      = ".($args["nr_valor_2"]).",
							   nr_meta         = ".($args["nr_meta"]).",
							   nr_percentual_f = ".$nr_percentual_f.",
                               observacao      = '".$args["observacao"]."'
						 WHERE cd_administrativo_sat_colaborador = ".intval($args['cd_administrativo_sat_colaborador'])."
			          ";
		}

		try
		{
			$query = $this->db->query($qr_sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}

	function excluir($id)
	{
		$qr_sql = " 
					UPDATE indicador_plugin.administrativo_sat_colaborador 
					   SET dt_exclusao         = CURRENT_TIMESTAMP, 
					       cd_usuario_exclusao = ".usuario_id()." 
		             WHERE md5(cd_administrativo_sat_colaborador::text) = '".$id."' 
		          "; 
		$query=$this->db->query($sql); 
	}
}
?>