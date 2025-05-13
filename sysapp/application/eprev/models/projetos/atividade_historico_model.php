<?php
class Atividade_historico_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
				SELECT t.codigo AS codigo, 
					   u.nome AS responsavel, 
					   l.descricao AS status, 
					   t.observacoes AS complemento, 
					   TO_CHAR(dt_inicio_prev, 'DD/MM/YYYY - HH24:MI:SS') AS data,
					   CASE WHEN l.valor = 1 THEN 'blue'		
							WHEN l.valor = 2 THEN '#8B7D7B'
							WHEN l.valor = 3 THEN 'red'
							ELSE 'green'
					   END AS status_cor,
					   CASE WHEN l.valor = 1 THEN 'label label-info'
							WHEN l.valor = 2 THEN 'label'
							WHEN l.valor = 3 THEN 'label label-important'
							WHEN l.valor = 4 THEN 'label label-warning'
							WHEN l.valor = 5 THEN 'label label-info'
							ELSE 'label label-success'
					   END AS class_status					   
				  FROM projetos.atividade_historico t 
				 INNER JOIN projetos.usuarios_controledi u
					ON t.cd_recurso = u.codigo 
				  LEFT OUTER JOIN listas l
					ON l.codigo = t.status_atual
				   AND l.categoria = 'STAT'
				 WHERE t.cd_atividade = ".intval($args['cd_atividade'])."
			  ORDER BY t.dt_inicio_prev, 
					   t.codigo
				";
		$result = $this->db->query($qr_sql);
	}
	
	public function prioridade_historico(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aph.r_tp_prioridade,
						   aph.r_tp_operacao,
					       aph.r_cd_atividade_prioridade, 
						   TO_CHAR(aph.r_dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS r_dt_inclusao, 
						   aph.r_status_atual,
						   l.descricao AS ds_status_atual,
						   aph.r_nr_prioridade, 
						   aph.r_nr_posicao, 
						   aph.r_nr_total, 
						   aph.r_cd_atividade_origem,
						   aph.r_status_atual_origem,
						   l1.descricao AS ds_status_atual_origem
					  FROM funcoes.atividade_prioridade_historico(".intval($args['cd_atividade']).") aph
					  LEFT JOIN listas l
					    ON l.codigo = aph.r_status_atual
				       AND l.categoria = 'STAT'
					  LEFT JOIN listas l1
					    ON l1.codigo = aph.r_status_atual_origem
				       AND l1.categoria = 'STAT'					   
					  
				  ";

		$result = $this->db->query($qr_sql);
	}	
}
?>