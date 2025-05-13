<?php
class Treinamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_treinamento,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
						   i.nr_hora,
						   i.nr_homem,
						   i.nr_meta,
						   i.nr_meta_ano,
						   i.nr_peso,

					       i.nr_treinamento,
					       i.nr_hora_acumulado,
					       i.nr_homem_acumulado,
						   i.nr_treinamento_acumulado,
						   i.nr_resultado_meta,
						   i.nr_referencia_mes,
						   i.nr_treinamento_acumulado_meta,
						   i.nr_referencia_mes_acumulado,
						   i.nr_media_movel,
						   i.nr_media_movel_percentual,					   
						   
						   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
						   i.cd_usuario_inclusao,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_tabela,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM igp.treinamento i1
									   WHERE (i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM igp.treinamento i
					 WHERE i.dt_exclusao IS NULL
					 ORDER BY i.dt_referencia
				  ";
		$result = $this->db->query($qr_sql);
	}	
	
	function carregar($cd)
	{
		$qr_sql = " 
					SELECT cd_treinamento,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   nr_hora,
						   nr_homem,
						   nr_meta,
						   nr_meta_ano,
						   nr_peso,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
						   cd_usuario_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   cd_usuario_exclusao, 
						   cd_indicador_tabela 
					  FROM igp.treinamento  
					 WHERE cd_treinamento = ".intval($cd)."
			      ";
		$ob_resul = $this->db->query($qr_sql);			   
		return $ob_resul->row_array();			
	}
	
	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_treinamento']) == 0)
		{
			$qr_sql = "
						INSERT INTO igp.treinamento 
						     ( 
							    dt_referencia,
								nr_hora,
								nr_homem, 
								nr_meta,
								nr_meta_ano,
								nr_peso,
								cd_indicador_tabela,
								cd_usuario_inclusao,
								cd_usuario_alteracao,
								dt_alteracao
						     ) 
					    VALUES 
						     ( 
								".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args["nr_hora"]) == "" ? "DEFAULT" : floatval($args["nr_hora"])).",
								".(trim($args["nr_homem"]) == "" ? "DEFAULT" : floatval($args["nr_homem"])).",
								".(trim($args["nr_meta"]) == "" ? "DEFAULT" : floatval($args["nr_meta"])).",
								".(trim($args["nr_meta_ano"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano"])).",
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
						UPDATE igp.treinamento 
						   SET dt_referencia        = ".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                               nr_hora              = ".(trim($args["nr_hora"]) == "" ? "DEFAULT" : floatval($args["nr_hora"])).",
                               nr_homem             = ".(trim($args["nr_homem"]) == "" ? "DEFAULT" : floatval($args["nr_homem"])).",
                               nr_meta              = ".(trim($args["nr_meta"]) == "" ? "DEFAULT" : floatval($args["nr_meta"])).",
                               nr_meta_ano          = ".(trim($args["nr_meta_ano"]) == "" ? "DEFAULT" : floatval($args["nr_meta_ano"])).",
                               nr_peso              = ".(trim($args["nr_peso"]) == "" ? "DEFAULT" : floatval($args["nr_peso"])).",
                               cd_indicador_tabela  = ".(intval($args["cd_indicador_tabela"]) == 0 ? "DEFAULT" : intval($args["cd_indicador_tabela"])).",
							   cd_usuario_alteracao = ".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
							   dt_alteracao         = CURRENT_TIMESTAMP
			             WHERE cd_treinamento = ".intval($args['cd_treinamento'])."
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
					UPDATE igp.treinamento 
					   SET dt_exclusao          = CURRENT_TIMESTAMP, 
					       cd_usuario_exclusao  = ".intval(usuario_id()).",
					       dt_alteracao         = CURRENT_TIMESTAMP,
					       cd_usuario_alteracao = ".intval(usuario_id())."
		             WHERE MD5(cd_treinamento::TEXT) = '".trim($id)."' 
		          "; 
		$this->db->query($qr_sql);
	}		
}
?>