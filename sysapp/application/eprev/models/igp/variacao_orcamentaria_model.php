<?php
class Variacao_orcamentaria_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_variacao_orcamentaria,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
						   i.nr_orcado,
						   i.nr_realizado,
						   i.nr_meta,
						   i.nr_peso,

					       i.nr_realizado_percentual,
					       i.nr_orcado_acumulado,
					       i.nr_realizado_acumulado,
					       i.nr_realizado_percentual_acumulado,
					       i.nr_resultado_meta,
					       i.nr_referencia_mes,
					       i.nr_realizado_percentual_acumulado_meta, 
					       i.nr_referencia_mes_acumulado,
					       i.nr_media_movel,
					       i.nr_media_movel_percentual,					   
						   
						   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
						   i.cd_usuario_inclusao,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_tabela,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM igp.variacao_orcamentaria i1
									   WHERE (i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM igp.variacao_orcamentaria i
					 WHERE i.dt_exclusao IS NULL
					 ORDER BY i.dt_referencia
				  ";
		$result = $this->db->query($qr_sql);
	}	

	function carregar($cd)
	{
		$qr_sql = " 
					SELECT cd_variacao_orcamentaria,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   nr_orcado,
						   nr_realizado,
						   nr_meta,
						   nr_peso,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
						   cd_usuario_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   cd_usuario_exclusao, 
						   cd_indicador_tabela 
					  FROM igp.variacao_orcamentaria  
					 WHERE cd_variacao_orcamentaria = ".intval($cd)."
			      ";
		$ob_resul = $this->db->query($qr_sql);			   
		return $ob_resul->row_array();			
	}	

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_variacao_orcamentaria']) == 0)
		{
			$qr_sql = "
						INSERT INTO igp.variacao_orcamentaria 
						     ( 
							    dt_referencia,
								nr_orcado,
								nr_realizado, 
								nr_meta,
								nr_peso,
								cd_indicador_tabela,
								cd_usuario_inclusao,
								cd_usuario_alteracao,
								dt_alteracao
						     ) 
					    VALUES 
						     ( 
								".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args["nr_orcado"]) == "" ? "DEFAULT" : floatval($args["nr_orcado"])).",
								".(trim($args["nr_realizado"]) == "" ? "DEFAULT" : floatval($args["nr_realizado"])).",
								".(trim($args["nr_meta"]) == "" ? "DEFAULT" : floatval($args["nr_meta"])).",
								".(trim($args["nr_peso"]) == "" ? "DEFAULT" : floatval($args["nr_peso"])).",
								".(intval($args["cd_indicador_tabela"]) == 0 ? "DEFAULT" : intval($args["cd_indicador_tabela"])).",
								".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
								".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
								CURRENT_TIMESTAMP
						     );			
					  ";
		}
		else
		{
			$qr_sql = "
						UPDATE igp.variacao_orcamentaria 
						   SET dt_referencia        = ".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                               nr_orcado      = ".(trim($args["nr_orcado"]) == "" ? "DEFAULT" : floatval($args["nr_orcado"])).",
                               nr_realizado           = ".(trim($args["nr_realizado"]) == "" ? "DEFAULT" : floatval($args["nr_realizado"])).",
                               nr_meta              = ".(trim($args["nr_meta"]) == "" ? "DEFAULT" : floatval($args["nr_meta"])).",
                               nr_peso              = ".(trim($args["nr_peso"]) == "" ? "DEFAULT" : floatval($args["nr_peso"])).",
                               cd_indicador_tabela  = ".(intval($args["cd_indicador_tabela"]) == 0 ? "DEFAULT" : intval($args["cd_indicador_tabela"])).",
							   cd_usuario_alteracao = ".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
							   dt_alteracao         = CURRENT_TIMESTAMP
			             WHERE cd_variacao_orcamentaria = ".intval($args['cd_variacao_orcamentaria'])."
			          ";			
		}

		try
		{
			$this->db->query($qr_sql);
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
					UPDATE igp.variacao_orcamentaria 
					   SET dt_exclusao          = CURRENT_TIMESTAMP, 
					       cd_usuario_exclusao  = ".intval(usuario_id()).",
					       dt_alteracao         = CURRENT_TIMESTAMP,
					       cd_usuario_alteracao = ".intval(usuario_id())."
		             WHERE MD5(cd_variacao_orcamentaria::TEXT) = '".trim($id)."' 
		          "; 
		$this->db->query($qr_sql);
	}	
}
?>