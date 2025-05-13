<?php
class atividade_dashboard_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
/*
AGUARDANDO
"ADIR"
"AINI"
"SUSP"

EM ANDAMENTO
"EANA"
"EMAN"

EM TESTE
"ETES"

AGUARDA USUARIO
"AUSR"

ENCERRADAS
"AGDF"
"CANC"
"CONC"
*/


    
    public function listarBacklog(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.ds_atendente_avatar, 
                           a.cd_solicitante, 
                           a.ds_solicitante,
                           a.ds_solicitante_avatar,                        
                           a.dt_cadastro, 
                           a.dt_cadastro_min, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status
                      FROM informatica.dashboard_backlog a
                     ORDER BY a.nr_prioridade
                  ";

        $result = $this->db->query($qr_sql);
    }
    
    public function resumoBacklogArea(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.area_solicitante, 
                           COUNT(*) AS quantidade
                      FROM informatica.dashboard_backlog a
                     GROUP BY a.area_solicitante
                     ORDER BY quantidade DESC
                  ";

        $result = $this->db->query($qr_sql);
    }   
    
    public function listarAndamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.ds_atendente_avatar, 
                           a.cd_solicitante, 
                           a.ds_solicitante, 
                           a.ds_solicitante_avatar,                        
                           a.dt_cadastro, 
                           a.dt_cadastro_min, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status
                      FROM informatica.dashboard_em_andamento a
                     ORDER BY a.nr_prioridade
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function resumoAndamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.area_solicitante, 
                           COUNT(*) AS quantidade
                      FROM informatica.dashboard_em_andamento a
                     GROUP BY a.area_solicitante
                     ORDER BY quantidade DESC
                  ";

        $result = $this->db->query($qr_sql);
    }   

    public function listarEmTeste(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.ds_atendente_avatar,
                           a.cd_solicitante, 
                           a.ds_solicitante,
                           a.ds_solicitante_avatar,                        
                           a.dt_cadastro, 
                           a.dt_cadastro_min, 
                           a.dt_limite_teste, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status
                      FROM informatica.dashboard_em_teste a
                  ";

        $result = $this->db->query($qr_sql);
    }   
	
    public function resumoEmTesteMes(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.ano_mes_limite,
                           COUNT(*) AS qt_atividade
                      FROM informatica.dashboard_em_teste a
                     GROUP BY a.ano_mes_limite
                     ORDER BY a.ano_mes_limite
                  ";

        $result = $this->db->query($qr_sql);
    }	
    
    public function resumoEmTeste(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.area_solicitante, 
                           COUNT(*) AS quantidade
                      FROM informatica.dashboard_em_teste a
                     GROUP BY a.area_solicitante
                     ORDER BY quantidade DESC
                  ";

        $result = $this->db->query($qr_sql);
    }   
    
    public function listarAguardaUsuario(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.ds_atendente_avatar,
                           a.cd_solicitante, 
                           a.ds_solicitante, 
                           a.ds_solicitante_avatar,
                           a.dt_cadastro, 
                           a.dt_cadastro_min, 
                           a.dt_aguardando_usuario_limite, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status
                      FROM informatica.dashboard_aguarda_usuario a
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function resumoAguardaUsuario(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.area_solicitante, 
                           COUNT(*) AS quantidade
                      FROM informatica.dashboard_aguarda_usuario a
                     GROUP BY a.area_solicitante
                     ORDER BY quantidade DESC
                  ";

        $result = $this->db->query($qr_sql);
    }
    
    public function tempoMedio(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT COUNT(*) AS qt_atividade,
                           AVG(funcoes.dias_corridos(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_tmo,
                           AVG(funcoes.dias_uteis(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_uteis_tmo,
                           AVG(funcoes.dias_corridos(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_tma,
                           AVG(funcoes.dias_uteis(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_uteis_tma      
                      FROM projetos.atividades a
                     WHERE a.tipo <> 'L'
                       AND a.area = 'GI'
                       AND a.dt_fim_real IS NOT NULL 
                       ".(trim($args['cd_tipo']) == 1 ? "AND a.dt_cad >= DATE_TRUNC('YEAR', (CURRENT_DATE - '5 YEARS'::INTERVAL))" : "")."
                       ".(trim($args['cd_tipo']) == 2 ? "AND a.dt_cad >= DATE_TRUNC('YEAR', (CURRENT_DATE - '3 YEARS'::INTERVAL))" : "")."
                       ".(trim($args['cd_tipo']) == 3 ? "AND a.dt_cad >= DATE_TRUNC('YEAR', CURRENT_DATE)" : "")."
                  ";
        $result = $this->db->query($qr_sql);
    }   
    
    public function tempoMedioArea(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT funcoes.get_gerencia_atual(a.divisao) AS gerencia, 
                           COUNT(*) AS qt_os,
                           AVG(funcoes.dias_corridos(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_tmo,
                           AVG(funcoes.dias_uteis(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_uteis_tmo,
                           AVG(funcoes.dias_corridos(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_tma,
                           AVG(funcoes.dias_uteis(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_uteis_tma      
                      FROM projetos.atividades a
                     WHERE a.tipo <> 'L'
                       AND a.area = 'GI'
                       AND a.dt_fim_real IS NOT NULL 
                       ".(trim($args['dt_referencia']) != "" ? "AND a.dt_cad >= TO_DATE('".trim($args['dt_referencia'])."','DD/MM/YYYY')" : "")."
                     GROUP BY gerencia
                     ORDER BY gerencia      
                  ";
    }
    
    public function resumoAno(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT CASE WHEN a.dt_cad >= DATE_TRUNC('YEAR', CURRENT_DATE) THEN '0 anos'
                                WHEN a.dt_cad >= DATE_TRUNC('YEAR', (CURRENT_DATE - '3 YEAR'::INTERVAL)) THEN '1 a 3 anos'
                                ELSE '3 anos ou +'
                           END AS ano,
                           COUNT(*) AS qt_atividade,
						   (((COUNT(*) * 1.0) / SUM(COUNT(*)) over()) * 100) AS pr_atividade
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NULL
                     GROUP BY ano
                     ORDER BY ano                  
                  ";

        $result = $this->db->query($qr_sql);
    }   
    
    public function resumoMes(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT COUNT(*) AS qt_atividade
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       ".(trim($args['cd_tipo']) == 10 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', CURRENT_DATE)" : "")."
                       ".(trim($args['cd_tipo']) == 11 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', (CURRENT_DATE - '3 MONTH'::INTERVAL))" : "")."
                       ".(trim($args['cd_tipo']) == 12 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', (CURRENT_DATE - '12 MONTH'::INTERVAL))" : "")."
                       
                       ".(trim($args['cd_tipo']) == 20 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', CURRENT_DATE)" : "")."
                       ".(trim($args['cd_tipo']) == 21 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', (CURRENT_DATE - '3 MONTH'::INTERVAL))" : "")."
                       ".(trim($args['cd_tipo']) == 22 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', (CURRENT_DATE - '12 MONTH'::INTERVAL))" : "")."                     
                  ";

        $result = $this->db->query($qr_sql);
    }   
    
	public function resumoArea(&$result, $args=array())
    {
        $qr_sql = "
					SELECT funcoes.get_gerencia_atual(a.divisao) AS gerencia, 
						   COUNT(*) AS qt_atividade,
						   (((COUNT(*) * 1.0) / SUM(COUNT(*)) over()) * 100) AS pr_atividade
					  FROM projetos.atividades a
					 WHERE a.tipo <> 'L'
					   AND a.area = 'GI'
					   AND a.dt_fim_real IS NULL 
					   AND a.status_atual NOT IN ('ETES','AUSR')
					 GROUP BY gerencia
					 ORDER BY qt_atividade
                  ";

        $result = $this->db->query($qr_sql);
    } 
	
	public function resumoMesAtendente(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT x.ds_atendente, 
                           SUM(x.qt_atividade_aberta) AS qt_atividade_aberta, 
                           SUM(x.qt_atividade_encerrada) AS qt_atividade_encerrada
                      FROM (
                            SELECT funcoes.get_gerencia_atual(a.divisao) AS ds_atendente,
                                   COUNT(*) AS qt_atividade_aberta,
                                   0 AS qt_atividade_encerrada
                              FROM projetos.atividades a
                              LEFT JOIN listas l 
                                ON l.codigo    = a.status_atual
                               AND l.categoria = 'STAT'
                             WHERE a.tipo        <> 'L'
                               AND a.area        = 'GI'
                               ".(trim($args['cd_tipo']) == 10 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', CURRENT_DATE)" : "")."
                               ".(trim($args['cd_tipo']) == 11 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', (CURRENT_DATE - '3 MONTH'::INTERVAL))" : "")."
                               ".(trim($args['cd_tipo']) == 12 ? "AND a.dt_cad >= DATE_TRUNC('MONTH', (CURRENT_DATE - '12 MONTH'::INTERVAL))" : "")."
                             GROUP BY ds_atendente
                             
                             UNION

                            SELECT funcoes.get_gerencia_atual(a.divisao) AS ds_atendente,
                                   0 AS qt_atividade_aberta,
                                   COUNT(*) AS qt_atividade_encerrada
                              FROM projetos.atividades a
                              LEFT JOIN listas l 
                                ON l.codigo    = a.status_atual
                               AND l.categoria = 'STAT'
                             WHERE a.tipo        <> 'L'
                               AND a.area        = 'GI'
                               ".(trim($args['cd_tipo']) == 10 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', CURRENT_DATE)" : "")."
                               ".(trim($args['cd_tipo']) == 11 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', (CURRENT_DATE - '3 MONTH'::INTERVAL))" : "")."
                               ".(trim($args['cd_tipo']) == 12 ? "AND a.dt_fim_real >= DATE_TRUNC('MONTH', (CURRENT_DATE - '12 MONTH'::INTERVAL))" : "")."
                             GROUP BY ds_atendente
                           ) x
                     GROUP BY x.ds_atendente
                     ORDER BY x.ds_atendente
                  ";

        $result = $this->db->query($qr_sql);
    } 

    public function listarEncerradas(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.cd_solicitante, 
                           a.ds_solicitante, 
                           a.dt_cadastro, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status,
                           a.dt_fim_real,
                           a.dt_fim,
                           a.dt_teste,
                           a.to,
                           a.ta 
                      FROM informatica.dashboard_encerrada a
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function listarAbertas(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cd_atendente, 
                           a.ds_atendente, 
                           a.cd_solicitante, 
                           a.ds_solicitante, 
                           a.dt_cadastro, 
                           a.ano, 
                           a.numero, 
                           a.area_solicitante, 
                           a.descricao,
                           a.assunto,
                           a.justificativa,
                           a.nr_prioridade, 
                           a.dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           a.ds_prioridade_usuario, 
                           a.cd_status, 
                           a.ds_status
                      FROM informatica.dashboard_aberta a
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function resumoCategoria(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cd_atividade_classificacao, 
					       ds_atividade_classificacao, 
						   COUNT(*) AS qt_atividade,
						   (((COUNT(*) * 1.0) / SUM(COUNT(*)) over()) * 100) AS pr_atividade
					  FROM informatica.dashboard_classificacao
					 WHERE 1 = 1
					       ".(trim($args['fl_desenv']) == "S" ? "AND cd_atividade_classificacao NOT IN (9,10)" : "")."
					       ".(trim($args['cd_gerencia']) != "" ? "AND area_solicitante = '".trim($args['cd_gerencia'])."'" : "")."
					       ".(intval($args['nr_ano']) > 0 ? "AND ano = '".intval($args['nr_ano'])."'" : "")."
					 GROUP BY cd_atividade_classificacao, ds_atividade_classificacao
					 ORDER BY qt_atividade
                  ";

        $result = $this->db->query($qr_sql);
    }	
	
    public function monitorAtendente(&$result, $args=array())
    {
        $qr_sql = "
					SELECT 'BACKLOG' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_backlog a
					 WHERE LOWER(funcoes.get_usuario(a.cd_atendente)) = LOWER('".trim($args['usuario'])."')
					 UNION
					SELECT 'ANDAMENTO' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_em_andamento b
					 WHERE LOWER(funcoes.get_usuario(b.cd_atendente)) = LOWER('".trim($args['usuario'])."')
					 UNION
					SELECT 'TESTE' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_em_teste c
					 WHERE LOWER(funcoes.get_usuario(c.cd_atendente)) = LOWER('".trim($args['usuario'])."')
					 UNION
					SELECT 'AGDUSER' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_aguarda_usuario d
					 WHERE LOWER(funcoes.get_usuario(d.cd_atendente)) = LOWER('".trim($args['usuario'])."')
					 UNION
					SELECT 'ABERTA_ANO' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_aberta_ano e
					 WHERE LOWER(funcoes.get_usuario(e.cd_atendente)) = LOWER('".trim($args['usuario'])."')		
					 UNION
					SELECT 'CONCLUIDA_ANO' AS tipo, COUNT(*) AS quantidade
					  FROM informatica.dashboard_encerrada_ano e
					 WHERE LOWER(funcoes.get_usuario(e.cd_atendente)) = LOWER('".trim($args['usuario'])."')						 
                  ";

        $result = $this->db->query($qr_sql);
    }	
}
?>