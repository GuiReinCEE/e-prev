<?php
class controle_cenario_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

    public function get_ano()
    {
        $qr_sql = "
            SELECT DISTINCT EXTRACT('year' FROM dt_legal) AS ano
              FROM projetos.cenario
			 WHERE dt_legal IS NOT NULL
             ORDER BY extract ('year' FROM dt_legal) DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function lista_registro($args = array())
    {
		$qr_sql = "
			SELECT c.cd_edicao, 
                   c.cd_cenario,
		           c.titulo, 
		           TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY') AS data_inc, 
		           TO_CHAR(c.dt_exclusao, 'DD/MM/YYYY') AS data_exc, 
		           TO_CHAR(c.dt_prevista, 'DD/MM/YYYY') AS dt_prev, 
		           TO_CHAR(c.dt_legal, 'DD/MM/YYYY') AS dt_leg, 
		           TO_CHAR(c.dt_implementacao, 'DD/MM/YYYY') AS dt_impl,
                   TO_CHAR(c.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento
	          FROM projetos.cenario c
              JOIN projetos.edicao_cenario ec
                ON ec.cd_edicao = c.cd_edicao
	         WHERE ec.dt_envio_email IS NOT NULL
               AND c.dt_exclusao IS NULL
                   ".($args['ano'] == '9999' 
						? "AND c.dt_legal IS NULL AND EXTRACT('year' FROM c.dt_inclusao) >= 2008" 
						: "AND EXTRACT('year' FROM c.dt_legal) = ".$args['ano']." AND dt_legal IS NOT NULL 
                          ".(trim($args['mes']) != '' ? "AND EXTRACT('month' FROM c.dt_legal) = ".trim($args['mes']) : ''))."
               ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', c.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
             ORDER BY c.cd_cenario DESC, 
			          c.pertinencia DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function lista_atividades($cd_cenario)
    {
        $qr_sql = "
            SELECT numero,
                   cod_atendente,
                   u.nome,
                   a.area,
				   CASE WHEN a.pertinencia = '2' AND CURRENT_DATE > a.dt_prevista_implementacao_norma_legal AND a.dt_implementacao_norma_legal IS NULL THEN 'S'
				        ELSE 'N'
			       END AS fl_fora_prazo,
                   a.pertinencia AS pertin,
			       TO_CHAR(a.dt_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_implementacao_norma_legal,
			       TO_CHAR(a.dt_prevista_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_prevista_implementacao_norma_legal,
    			   CASE WHEN (a.status_atual = 'CANC') THEN 'Cancelada'
                        WHEN (a.status_atual = 'CAGC') THEN
                        (
                           SELECT observacoes
                             FROM projetos.atividade_historico
                            WHERE cd_atividade = a.numero
                              AND status_atual = 'CAGC'
                            ORDER BY codigo DESC 
                            LIMIT(1)
                        )
                        WHEN (a.status_atual = 'RAGC') THEN
                        (
                           SELECT observacoes
                             FROM projetos.atividade_historico
                            WHERE cd_atividade = a.numero
                              AND status_atual = 'RAGC'
                            ORDER BY codigo DESC 
                            LIMIT(1)
                        )
                       WHEN a.pertinencia IS NOT NULL THEN cp.ds_pertinencia
    			       ELSE 'Não verificado'
    			   END AS pertinencia,
                   CASE WHEN (a.status_atual = 'CAGC') THEN 'gray'
                        WHEN (a.status_atual = 'RAGC') THEN 'gray'
                        WHEN (a.pertinencia = '0')     THEN 'black'
    			        WHEN (a.pertinencia = '1')     THEN 'green'
                        WHEN (a.pertinencia = '2')     THEN 'blue'
    			        ELSE 'orange'
    			   END AS cor,
                   CASE WHEN (a.status_atual = 'CAGC') THEN ''
                        WHEN (a.status_atual = 'RAGC') THEN ''
                        WHEN (a.pertinencia = '0')     THEN 'label-inverse'
    			        WHEN (a.pertinencia = '1')     THEN 'label-success'
                        WHEN (a.pertinencia = '2')     THEN 'label-info'
    			        ELSE 'label-warning'
    			   END AS cor_status						  
			  FROM projetos.atividades a
              JOIN projetos.usuarios_controledi u
                ON a.cod_atendente = u.codigo
              LEFT JOIN projetos.cenario_pertinencia cp
                ON cp.pertinencia = a.pertinencia
			 WHERE a.cd_cenario = ".intval($cd_cenario)." 
		     ORDER BY a.area, a.numero DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function lista_atrasada($args = array())
    {
        $qr_sql = "
            SELECT DISTINCT cd_edicao,
                   c.titulo,
                   TO_CHAR(a.dt_cad, 'DD/MM/YYYY') AS dt_inclusao,
                   TO_CHAR((a.dt_cad::date + '7 day'::interval)::date, 'DD/MM/YYYY') AS dt_limite,
                   a.area,
                   a.dt_cad::date
              FROM projetos.atividades a
              JOIN projetos.cenario c
                ON c.cd_cenario = a.cd_cenario
              JOIN listas l
                ON l.codigo = a.status_atual 
             WHERE a.tipo = 'L' 
               AND c.dt_inclusao::date >= '2017-01-01'::date
               AND a.status_atual NOT IN ('CAGC', 'RAGC')
               AND a.pertinencia IS NULL
               AND l.categoria = 'STAT'
               AND (a.dt_cad::date + '7 day'::interval)::date <= CURRENT_DATE
             ORDER BY a.dt_cad::date ASC;";

        return $this->db->query($qr_sql)->result_array();
    }
}
?>
