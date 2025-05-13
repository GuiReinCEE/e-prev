<?php
class Participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_participante,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   TO_CHAR(i.dt_referencia,'MM') AS nr_mes,
						   TO_CHAR(i.dt_referencia,'YYYY') AS nr_ano,
						   
						   i.nr_participante,
						   i.nr_patrocinado,
						   i.nr_instituidor,
						   i.nr_participante_semestre,
						   
						   i.nr_meta,
						   i.nr_meta_ano_anterior,
						   i.nr_meta_ano,
						   i.nr_peso,
						   
						   i.nr_resultado,
						   i.nr_referencia_mes,
						   i.nr_resultado_acumulado,
						   i.nr_referencia_acumulado,
						   i.nr_media_movel,
						   i.nr_media_referencia_mes,
						   
						   i.observacao,
						   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
						   i.cd_usuario_inclusao,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_tabela,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM igp.participante i1
									   WHERE (i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					FROM igp.participante i
				   WHERE i.dt_exclusao IS NULL
				   ORDER BY i.dt_referencia
				  ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$qr_sql = " 
					SELECT cd_participante,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   nr_participante,
						   nr_patrocinado,
						   nr_instituidor,
						   nr_participante_semestre,
						   
						   nr_meta,
						   nr_meta_ano_anterior,
						   nr_meta_ano,
						   nr_peso,
						   
						   nr_resultado,
						   nr_referencia_mes,
						   nr_resultado_acumulado,
						   nr_referencia_acumulado,
						   nr_media_movel,
						   nr_media_referencia_mes,
						   
						   observacao,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
						   cd_usuario_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   cd_usuario_exclusao, 
						   cd_indicador_tabela 
					  FROM igp.participante  
					 WHERE cd_participante = ".intval($cd)."
			      ";
		$ob_resul = $this->db->query($qr_sql);			   
		return $ob_resul->row_array();			
	}	
	
	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_participante']) == 0)
		{
			$qr_sql = "
						INSERT INTO igp.participante 
						     ( 
							    dt_referencia,
								nr_participante,
							    nr_meta_ano_anterior,
							    nr_meta_ano,
							    nr_peso,
								observacao,
								cd_indicador_tabela,
								cd_usuario_inclusao,
								cd_usuario_alteracao,
								dt_alteracao
						     ) 
					    VALUES 
						     ( 
								".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args["nr_participante"]) == "" ? "DEFAULT" : floatval($args["nr_participante"])).",
								".(trim($args["nr_meta_ano_anterior"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano_anterior"])).",
								".(trim($args["nr_meta_ano"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano"])).",
								".(trim($args["nr_peso"]) == "" ? "DEFAULT" : floatval($args["nr_peso"])).",
								".(trim($args["observacao"]) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
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
						UPDATE igp.participante 
						   SET dt_referencia        = ".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                               nr_participante      = ".(trim($args["nr_participante"]) == "" ? "DEFAULT" : floatval($args["nr_participante"])).",
                               nr_meta_ano_anterior = ".(trim($args["nr_meta_ano_anterior"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano_anterior"])).",
                               nr_meta_ano          = ".(trim($args["nr_meta_ano"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano"])).",
                               nr_peso              = ".(trim($args["nr_peso"]) == "" ? "DEFAULT" : floatval($args["nr_peso"])).",
							   observacao           = ".(trim($args["observacao"]) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
                               cd_indicador_tabela  = ".(intval($args["cd_indicador_tabela"]) == 0 ? "DEFAULT" : intval($args["cd_indicador_tabela"])).",
							   cd_usuario_alteracao = ".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
							   dt_alteracao         = CURRENT_TIMESTAMP
			             WHERE cd_participante = ".intval($args['cd_participante'])."
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
					UPDATE igp.participante 
					   SET dt_exclusao          = CURRENT_TIMESTAMP, 
					       cd_usuario_exclusao  = ".intval(usuario_id()).",
					       dt_alteracao         = CURRENT_TIMESTAMP,
					       cd_usuario_alteracao = ".intval(usuario_id())."
		             WHERE MD5(cd_participante::TEXT) = '".trim($id)."' 
		          "; 
		$this->db->query($qr_sql);
	}	
}
?>