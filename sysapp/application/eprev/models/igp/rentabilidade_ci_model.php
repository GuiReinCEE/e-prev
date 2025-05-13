<?php
class Rentabilidade_ci_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_rentabilidade_ci,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
						   i.nr_rentabilidade,
						   i.nr_benchmark,
						   i.nr_peso_igp,

					       i.nr_diferenca,
						   i.nr_rentabilidade_fator,
						   i.nr_benchmark_fator,
						   i.nr_rentabilidade_indice,
						   i.nr_benchmark_indice,
						   i.nr_rentabilidade_variacao,
						   i.nr_benchmark_variacao,
						   i.nr_poder,
						   i.nr_igp_mes,
						   i.nr_igp_acumulado,
						   i.nr_igp_media,
						   i.nr_diferenca_acumulado,
						   i.nr_referencia_mes,
					       i.nr_referencia_acumulado,
						   i.nr_referencia_media,
						   
						   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
						   i.cd_usuario_inclusao,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_tabela,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM igp.rentabilidade_ci i1
									   WHERE (i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
						   END AS fl_editar
					  FROM igp.rentabilidade_ci i
					 WHERE i.dt_exclusao IS NULL
					 ORDER BY i.dt_referencia
				  ";
		$result = $this->db->query($qr_sql);
	}		
	
	function carregar($cd)
	{
		$qr_sql = " 
					SELECT cd_rentabilidade_ci,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   nr_rentabilidade,
						   nr_benchmark,
						   nr_peso_igp,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
						   cd_usuario_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   cd_usuario_exclusao, 
						   cd_indicador_tabela 
					  FROM igp.rentabilidade_ci  
					 WHERE cd_rentabilidade_ci = ".intval($cd)."
			      ";
		$ob_resul = $this->db->query($qr_sql);			   
		return $ob_resul->row_array();			
	}	

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_rentabilidade_ci']) == 0)
		{
			$qr_sql = "
						INSERT INTO igp.rentabilidade_ci 
						     ( 
							    dt_referencia,
								nr_rentabilidade,
								nr_benchmark, 
								nr_peso_igp,
								cd_indicador_tabela,
								cd_usuario_inclusao,
								cd_usuario_alteracao,
								dt_alteracao
						     ) 
					    VALUES 
						     ( 
								".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args["nr_rentabilidade"]) == "" ? "DEFAULT" : floatval($args["nr_rentabilidade"])).",
								".(trim($args["nr_benchmark"]) == "" ? "DEFAULT" : floatval($args["nr_benchmark"])).",
								".(trim($args["nr_peso_igp"]) == "" ? "DEFAULT" : floatval($args["nr_peso_igp"])).",
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
						UPDATE igp.rentabilidade_ci 
						   SET dt_referencia        = ".(trim($args["dt_referencia"]) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                               nr_rentabilidade     = ".(trim($args["nr_rentabilidade"]) == "" ? "DEFAULT" : floatval($args["nr_rentabilidade"])).",
                               nr_benchmark         = ".(trim($args["nr_benchmark"]) == "" ? "DEFAULT" : floatval($args["nr_benchmark"])).",
                               nr_peso_igp          = ".(trim($args["nr_peso_igp"]) == "" ? "DEFAULT" : floatval($args["nr_peso_igp"])).",
                               cd_indicador_tabela  = ".(intval($args["cd_indicador_tabela"]) == 0 ? "DEFAULT" : intval($args["cd_indicador_tabela"])).",
							   cd_usuario_alteracao = ".(intval($args["cd_usuario"]) == 0 ? "DEFAULT" : intval($args["cd_usuario"])).",
							   dt_alteracao         = CURRENT_TIMESTAMP
			             WHERE cd_rentabilidade_ci = ".intval($args['cd_rentabilidade_ci'])."
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
					UPDATE igp.rentabilidade_ci 
					   SET dt_exclusao          = CURRENT_TIMESTAMP, 
					       cd_usuario_exclusao  = ".intval(usuario_id()).",
					       dt_alteracao         = CURRENT_TIMESTAMP,
					       cd_usuario_alteracao = ".intval(usuario_id())."
		             WHERE MD5(cd_rentabilidade_ci::TEXT) = '".trim($id)."' 
		          "; 
		$this->db->query($qr_sql);
	}		
}
?>