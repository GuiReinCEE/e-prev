<?php
class Relatorio_atividades_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
/*
       AVG(funcoes.dias_corridos(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_operacao,
       AVG(funcoes.dias_uteis(a.dt_cad::date,a.dt_fim_real::date))::INT AS qt_dias_uteis_operacao,
       AVG(funcoes.dias_corridos(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_atendimento,
       AVG(funcoes.dias_uteis(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS qt_dias_uteis_atendimento   
*/  
    
    
    public function tmaArea(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT AVG(EXTRACT(epoch FROM age(a.dt_fim_real, a.dt_cad)) / 86400)::INT AS tma 
                      FROM projetos.atividades a
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NOT NULL
                       AND funcoes.get_usuario_area(a.cod_solicitante::integer) = '".trim($args['cd_gerencia'])."'
                       AND TO_CHAR(a.dt_fim_real, 'YYYY') = '".intval($args['nr_ano'])."'
                  ";
        $result = $this->db->query($qr_sql);
    }

    public function tmaInformatica(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT AVG(EXTRACT(epoch FROM age(a.dt_fim_real, a.dt_cad)) / 86400)::INT AS tma 
                      FROM projetos.atividades a
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NOT NULL
                       AND TO_CHAR(a.dt_fim_real, 'YYYY') = '".intval($args['nr_ano'])."'
                  ";
        $result = $this->db->query($qr_sql);
    }   

    public function listarAbertasPeriodo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cod_atendente AS cd_atendente, 
                           funcoes.get_usuario_nome(a.cod_atendente::integer) AS ds_atendente, 
                           a.cod_solicitante AS cd_solicitante, 
                           funcoes.get_usuario_nome(a.cod_solicitante::integer) AS ds_solicitante, 
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                           TO_CHAR(a.dt_cad, 'YYYY'::text) AS ano, 
                           a.numero, 
                           funcoes.get_gerencia_atual(a.divisao) AS area_solicitante, 
                           a.descricao,
                           a.titulo AS assunto,
                           a.problema AS justificativa,
                           a.nr_prioridade, 
                           TO_CHAR(a.dt_prioridade, 'DD/MM/YYYY HH24:MI:SS') AS dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           funcoes.get_usuario_nome(a.cd_prioridade_usuario) AS ds_prioridade_usuario, 
                           a.status_atual AS cd_status, 
                           UPPER(l.descricao) AS ds_status
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND TO_CHAR(a.dt_cad, 'YYYY'::text) = '".intval($args['nr_ano'])."'
                       AND funcoes.get_gerencia_atual(a.divisao) = '".trim($args['cd_gerencia'])."'
                       ".(intval($args['cd_atendente']) > 0 ? "AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                     ORDER BY a.nr_prioridade
                  ";

        $result = $this->db->query($qr_sql);
    }	
	
    public function listarEncerradas(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cod_atendente AS cd_atendente, 
                           funcoes.get_usuario_nome(a.cod_atendente::integer) AS ds_atendente, 
                           a.cod_solicitante AS cd_solicitante, 
                           funcoes.get_usuario_nome(a.cod_solicitante::integer) AS ds_solicitante, 
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                           TO_CHAR(a.dt_cad, 'YYYY'::text) AS ano, 
                           a.numero, 
                           funcoes.get_gerencia_atual(a.divisao) AS area_solicitante, 
                           a.descricao,
                           a.titulo AS assunto,
                           a.problema AS justificativa,
                           a.nr_prioridade, 
                           TO_CHAR(a.dt_prioridade, 'DD/MM/YYYY HH24:MI:SS') AS dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           funcoes.get_usuario_nome(a.cd_prioridade_usuario) AS ds_prioridade_usuario, 
                           a.status_atual AS cd_status, 
                           UPPER(l.descricao) AS ds_status,
                           a.dt_fim_real,
                           TO_CHAR(a.dt_fim_real, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim,
                           TO_CHAR(a.dt_env_teste, 'DD/MM/YYYY HH24:MI:SS') AS dt_teste,
                           (funcoes.dias_uteis(a.dt_cad::date,a.dt_fim_real::date))::INT AS to,
                           (funcoes.dias_uteis(a.dt_cad::date,COALESCE(a.dt_env_teste,a.dt_fim_real)::date))::INT AS ta 
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NOT NULL
                       AND funcoes.get_gerencia_atual(a.divisao) = '".trim($args['cd_gerencia'])."'
                       AND TO_CHAR(a.dt_fim_real, 'YYYY') = '".intval($args['nr_ano'])."'
                       ".(intval($args['cd_atendente']) > 0 ? "AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                     ORDER BY a.dt_fim_real
                  ";

        $result = $this->db->query($qr_sql);
    }
    
    public function listarAbertas(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cod_atendente AS cd_atendente, 
                           funcoes.get_usuario_nome(a.cod_atendente::integer) AS ds_atendente, 
                           a.cod_solicitante AS cd_solicitante, 
                           funcoes.get_usuario_nome(a.cod_solicitante::integer) AS ds_solicitante, 
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                           TO_CHAR(a.dt_cad, 'YYYY'::text) AS ano, 
                           a.numero, 
                           funcoes.get_gerencia_atual(a.divisao) AS area_solicitante, 
                           a.descricao,
                           a.titulo AS assunto,
                           a.problema AS justificativa,
                           a.nr_prioridade, 
                           TO_CHAR(a.dt_prioridade, 'DD/MM/YYYY HH24:MI:SS') AS dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           funcoes.get_usuario_nome(a.cd_prioridade_usuario) AS ds_prioridade_usuario, 
                           a.status_atual AS cd_status, 
                           UPPER(l.descricao) AS ds_status
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
					   AND a.dt_cad < '".(intval($args['nr_ano']) + 1)."-01-01'
                       AND (a.dt_fim_real IS NULL OR a.dt_fim_real >= '".(intval($args['nr_ano']) + 1)."-01-01')
                       AND funcoes.get_gerencia_atual(a.divisao) = '".trim($args['cd_gerencia'])."'
                       ".(intval($args['cd_atendente']) > 0 ? "AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                       AND a.status_atual NOT IN ('AUSR','ETES')
                     ORDER BY a.nr_prioridade
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function listarEmTeste(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cod_atendente AS cd_atendente, 
                           funcoes.get_usuario_nome(a.cod_atendente::integer) AS ds_atendente, 
                           a.cod_solicitante AS cd_solicitante, 
                           funcoes.get_usuario_nome(a.cod_solicitante::integer) AS ds_solicitante, 
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                           TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_teste, 
                           TO_CHAR(a.dt_cad, 'YYYY'::text) AS ano, 
                           a.numero, 
                           funcoes.get_gerencia_atual(a.divisao) AS area_solicitante, 
                           a.descricao,
                           a.titulo AS assunto,
                           a.problema AS justificativa,
                           a.nr_prioridade, 
                           TO_CHAR(a.dt_prioridade, 'DD/MM/YYYY HH24:MI:SS') AS dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           funcoes.get_usuario_nome(a.cd_prioridade_usuario) AS ds_prioridade_usuario, 
                           a.status_atual AS cd_status, 
                           UPPER(l.descricao) AS ds_status,
                           TO_CHAR(a.dt_env_teste, 'DD/MM/YYYY HH24:MI:SS') AS dt_teste
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NULL
					   AND a.dt_cad < '".(intval($args['nr_ano']) + 1)."-01-01'
                       AND funcoes.get_gerencia_atual(a.divisao) = '".trim($args['cd_gerencia'])."'
                       ".(intval($args['cd_atendente']) > 0 ? "AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                       AND a.status_atual = 'ETES'
                     ORDER BY a.dt_cad
                  ";

        $result = $this->db->query($qr_sql);
    }   
    
    public function listarAguardaUsuario(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.cod_atendente AS cd_atendente, 
                           funcoes.get_usuario_nome(a.cod_atendente::integer) AS ds_atendente, 
                           a.cod_solicitante AS cd_solicitante, 
                           funcoes.get_usuario_nome(a.cod_solicitante::integer) AS ds_solicitante, 
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                           TO_CHAR(a.dt_aguardando_usuario_limite, 'DD/MM/YYYY') AS dt_aguardando_usuario_limite, 
                           TO_CHAR(a.dt_cad, 'YYYY'::text) AS ano, 
                           a.numero, 
                           funcoes.get_gerencia_atual(a.divisao) AS area_solicitante, 
                           a.descricao,
                           a.titulo AS assunto,
                           a.problema AS justificativa,
                           a.nr_prioridade, 
                           TO_CHAR(a.dt_prioridade, 'DD/MM/YYYY HH24:MI:SS') AS dt_prioridade, 
                           a.cd_prioridade_usuario, 
                           funcoes.get_usuario_nome(a.cd_prioridade_usuario) AS ds_prioridade_usuario, 
                           a.status_atual AS cd_status, 
                           UPPER(l.descricao) AS ds_status
                      FROM projetos.atividades a
                      LEFT JOIN listas l 
                        ON l.codigo    = a.status_atual
                       AND l.categoria = 'STAT'
                     WHERE a.tipo        <> 'L'
                       AND a.area        = 'GI'
                       AND a.dt_fim_real IS NULL
					   AND a.dt_cad < '".(intval($args['nr_ano']) + 1)."-01-01'
                       AND funcoes.get_gerencia_atual(a.divisao) = '".trim($args['cd_gerencia'])."'
                       AND a.status_atual = 'AUSR'
                       ".(intval($args['cd_atendente']) > 0 ? " AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                     ORDER BY a.dt_cad
                  ";

        $result = $this->db->query($qr_sql);
    }   
}
?>