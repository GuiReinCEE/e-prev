<?php
class Relatorio_atividade_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
    public function listar($cd_usuario, $args = array())
    {
        $qr_sql = "
			SELECT a.numero,
				   funcoes.get_usuario_nome(a.cod_solicitante) AS solicitante,
				   l.descricao AS status,
				   CASE WHEN l.valor = 1 THEN 'label label-info'
						WHEN l.valor = 2 THEN 'label'
						WHEN l.valor = 3 THEN 'label label-important'
						WHEN l.valor = 4 THEN 'label label-warning'
						WHEN l.valor = 5 THEN 'label label-info'
						ELSE 'label label-success'
				   END AS status_label,
				   a.descricao,
				   pp.nome AS projeto_nome,
				   TO_CHAR(a.dt_limite, 'dd/mm/yyyy') AS data_limite,
				   a.dt_limite,
				   funcoes.get_usuario_area(a.cod_solicitante::integer) AS divisao,
				   a.area,
				   d.nome
			  FROM projetos.atividades a
			  JOIN listas l
				ON l.codigo    = a.status_atual
			   AND l.categoria = 'STAT'
			  LEFT JOIN projetos.projetos pp
				ON pp.codigo = a.sistema
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = funcoes.get_usuario_area(a.cod_solicitante::integer)
			 WHERE a.dt_fim_real IS NULL
			 ".(is_array(($args['gerencia'])) ? "AND a.divisao      IN ('".implode($args['gerencia'],"','")."')" : '')."
			 ".(is_array(($args['status']))   ? "AND a.status_atual IN ('".implode($args['status'],  "','")."')" : '')."	   
			 ".((intval($cd_usuario) != 0) ? "AND a.cod_atendente = ".intval($cd_usuario) : '')."
			 ORDER BY funcoes.get_usuario_area(a.cod_solicitante::integer) ASC, a.numero DESC;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function gerencia()
    {
        $qr_sql = "
			SELECT codigo AS value,
			       codigo || ' - ' || nome AS text
			  FROM projetos.divisoes
			 WHERE dt_exclusao IS NULL
			   AND tipo = 'DIV'
			   AND dt_vigencia_ini <= CURRENT_DATE
			   AND COALESCE(dt_vigencia_fim, CURRENT_DATE) >= CURRENT_DATE
			 ORDER BY text;";

		return $this->db->query($qr_sql)->result_array();
    }
}