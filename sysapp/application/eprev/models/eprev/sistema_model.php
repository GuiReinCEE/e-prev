<?php
class Sistema_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_usuarios($cd_divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_gerencia()
    {
        $qr_sql = "
            SELECT nome AS text,
                   codigo AS value
              FROM funcoes.get_gerencias_vigente()";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_responsavel()
    {
        $qr_sql = "
            SELECT DISTINCT cd_usuario_responsavel AS value,
                   funcoes.get_usuario_nome(cd_usuario_responsavel) AS text
              FROM eprev.sistema
             WHERE dt_exclusao IS NULL
             ORDER BY funcoes.get_usuario_nome(cd_usuario_responsavel) ASC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_solicitante()
    {
        $qr_sql = "
            SELECT DISTINCT cd_usuario_solicitante AS value,
                   funcoes.get_usuario_nome(cd_usuario_solicitante) AS text
              FROM eprev.sistema
             WHERE dt_exclusao IS NULL
             ORDER BY funcoes.get_usuario_nome(cd_usuario_solicitante) ASC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_pendencia_minha()
    {
        $qr_sql = "
            SELECT pmq.cd_pendencia_minha_query AS value,
                   pmq.cd_pendencia_minha ||' - '|| COALESCE (pmq.ds_descricao ,pm.ds_pendencia_minha) AS text
              FROM gestao.pendencia_minha_query pmq
              JOIN gestao.pendencia_minha pm
                ON pm.cd_pendencia_minha = pmq.cd_pendencia_minha
             WHERE pmq.cd_pendencia_minha_query NOT IN (SELECT a.cd_pendencia_minha_query 
                                                          FROM eprev.sistema_pendencia a
                                                         WHERE dt_exclusao IS NULL)
               AND pmq.dt_exclusao IS NULL                         
             ORDER BY value ASC;";

      return $this->db->query($qr_sql)->result_array();
    }
    
    public function listar($args=array())
    {
        $qr_sql = "
            SELECT s.cd_sistema,
                   s.ds_sistema,
                   funcoes.get_usuario_nome(s.cd_usuario_responsavel) AS ds_responsavel,
                   funcoes.get_usuario_nome(s.cd_usuario_solicitante) As ds_solicitante,
                   s.ds_controller,
                   TO_CHAR(s.dt_publicacao, 'DD/MM/YYYY') AS dt_publicacao,
                   s.ds_descricao,
                   s.cd_gerencia_responsavel,
                   (SELECT TO_CHAR(sa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' : ' || sa.ds_acompanhamento
                      FROM eprev.sistema_acompanhamento sa
                     WHERE sa.dt_exclusao IS NULL
                       AND sa.cd_sistema = s.cd_sistema
                     ORDER BY dt_inclusao DESC
                     LIMIT 1) AS ds_acompanhamento
              FROM eprev.sistema s
             WHERE s.dt_exclusao IS NULL 
               ".(trim($args['fl_publicado']) == 'S' ? "AND s.dt_publicacao IS NOT NULL" : "")."
               ".(trim($args['fl_publicado']) == 'N' ? "AND s.dt_publicacao IS NULL": "")."
               ".(trim($args['ds_sistema'])!= '' ? "AND UPPER(funcoes.remove_acento(s.ds_sistema)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_sistema'])."%'))" : "")."
               ".(trim($args['cd_usuario_responsavel']) != '' ? "AND s.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : "")."
               ".(trim($args['cd_usuario_solicitante']) != '' ? "AND s.cd_usuario_solicitante = ".intval($args['cd_usuario_solicitante']) : "")."
               ".(trim($args['cd_gerencia_responsavel']) != '' ? "AND s.cd_gerencia_responsavel = ".str_escape($args['cd_gerencia_responsavel']) : "").";";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function salvar($args = array())
    {
        $cd_sistema = intval($this->db->get_new_id('eprev.sistema', 'cd_sistema'));

        $qr_sql = "
            INSERT INTO eprev.sistema 
                 (
                    cd_sistema,
                    ds_sistema,
                    cd_usuario_responsavel,
                    cd_usuario_solicitante,
                    ds_controller,
                    dt_publicacao,
                    ds_descricao,
                    cd_gerencia_responsavel,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_sistema).",
                    ".(trim($args['ds_sistema']) != '' ? str_escape($args['ds_sistema']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_solicitante']) != '' ? intval($args['cd_usuario_solicitante']) : "DEFAULT").",
                    ".(trim($args['ds_controller']) != '' ? str_escape($args['ds_controller']) : "DEFAULT").",
                    ".(trim($args['dt_publicacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_publicacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia_responsavel']) != '' ? str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );"; 
       
        $this->db->query($qr_sql);
    }

    public function carrega($cd_sistema)
    {
        $qr_sql = "
            SELECT cd_sistema,
                   ds_sistema,
                   cd_gerencia_responsavel,
                   cd_usuario_responsavel,
                   cd_usuario_solicitante,
                   ds_controller,
                   TO_CHAR(dt_publicacao, 'DD/MM/YYYY') AS dt_publicacao,
                   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_solicitante) AS ds_solicitante,
                   ds_descricao
              FROM eprev.sistema 
             WHERE dt_exclusao IS NULL
               AND cd_sistema = ".intval($cd_sistema).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function atualizar($cd_sistema, $args = array())
    {
        $qr_sql = "
            UPDATE eprev.sistema
               SET ds_sistema              = ".(trim($args['ds_sistema']) != '' ? str_escape($args['ds_sistema']) : "DEFAULT").",
                   cd_gerencia_responsavel = ".(trim($args['cd_gerencia_responsavel']) != '' ? str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").",
                   cd_usuario_responsavel  = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                   cd_usuario_solicitante  = ".(trim($args['cd_usuario_solicitante']) != '' ? intval($args['cd_usuario_solicitante']) : "DEFAULT").",
                   ds_controller           = ".(trim($args['ds_controller']) != '' ? str_escape($args['ds_controller']) : "DEFAULT").",
                   dt_publicacao           = ".(trim($args['dt_publicacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_publicacao'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   ds_descricao            = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_sistema = ".intval($cd_sistema).";";

        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento($cd_sistema)
    {
        $qr_sql = "
            SELECT cd_sistema,
                   ds_acompanhamento,
                   cd_usuario_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                   cd_sistema_acompanhamento,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') As dt_inclusao
              FROM eprev.sistema_acompanhamento 
             WHERE dt_exclusao IS NULL
               AND cd_sistema = ".intval($cd_sistema).";";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function carrega_acompanhamento($cd_sistema_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_sistema,
                   ds_acompanhamento,
                   cd_sistema_acompanhamento
              FROM eprev.sistema_acompanhamento 
             WHERE dt_exclusao IS NULL
               AND cd_sistema_acompanhamento = ".intval($cd_sistema_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $cd_sistema_acompanhamento = intval($this->db->get_new_id('eprev.sistema_acompanhamento', 'cd_sistema_acompanhamento'));

        $qr_sql = "
            INSERT INTO eprev.sistema_acompanhamento 
                 (
                    cd_sistema_acompanhamento,
                    cd_sistema,
                    ds_acompanhamento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_sistema_acompanhamento).",
                    ".(trim($args['cd_sistema']) != '' ? intval($args['cd_sistema']) : "DEFAULT").",
                    ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );"; 
       
        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_sistema_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE eprev.sistema_acompanhamento
               SET ds_acompanhamento      = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_sistema_acompanhamento = ".intval($cd_sistema_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_sistema_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE eprev.sistema_acompanhamento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_acompanhamento = ".intval($cd_sistema_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function valida_atividade($cd_atividade)
    {
        $qr_sql = "
            SELECT COUNT(*) AS valida
              FROM projetos.atividades
             WHERE numero = ".intval($cd_atividade).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function valida_evento($cd_evento)
    {
        $qr_sql = "
            SELECT COUNT(*) AS valida
              FROM projetos.eventos
             WHERE cd_evento = ".intval($cd_evento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_atividade($cd_sistema)
    {
        $qr_sql = "
            SELECT sa.cd_sistema_atividade,  
                   sa.cd_sistema,
                   sa.cd_usuario_inclusao,
                   sa.cd_atividade,
                   TO_CHAR(dt_cad, 'DD/MM/YYYY') AS dt_cad,  
                   funcoes.get_usuario_nome(a.cod_solicitante) AS ds_solicitante,
                   funcoes.get_usuario_nome(a.cod_atendente) AS ds_atendente,
                   a.descricao,
                   a.area,
                   CASE WHEN l.valor = 1 THEN 'label label-info'
                        WHEN l.valor = 2 THEN 'label'
                        WHEN l.valor = 3 THEN 'label label-important'
                        WHEN l.valor = 4 THEN 'label label-warning'
                        WHEN l.valor = 5 THEN 'label label-info'
                        ELSE 'label label-success'
                    END AS status_label,
                   l.descricao AS ds_status,
                   TO_CHAR(a.dt_fim_real, 'DD/MM/YYYY') AS dt_conclusao
              FROM eprev.sistema_atividade sa
              JOIN projetos.atividades a
                ON sa.cd_atividade = a.numero
              JOIN listas l 
                ON l.codigo = a.status_atual
             WHERE sa.dt_exclusao IS NULL
               AND sa.cd_sistema = ".intval($cd_sistema).";";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function carrega_atividade($cd_sistema_atividade)
    {
        $qr_sql = "
            SELECT cd_sistema,
                   cd_atividade,
                   cd_sistema_atividade
              FROM eprev.sistema_atividade 
             WHERE dt_exclusao IS NULL
               AND cd_sistema_atividade = ".intval($cd_sistema_atividade).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_atividade($args = array())
    {
        $cd_sistema_atividade = intval($this->db->get_new_id('eprev.sistema_atividade', 'cd_sistema_atividade'));

        $qr_sql = "
            INSERT INTO eprev.sistema_atividade 
                 (
                    cd_sistema_atividade,
                    cd_sistema,
                    cd_atividade,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_sistema_atividade).",
                    ".(trim($args['cd_sistema']) != '' ? intval($args['cd_sistema']) : "DEFAULT").",
                    ".(trim($args['cd_atividade']) != '' ? intval($args['cd_atividade']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );"; 
       
        $this->db->query($qr_sql);
    }

    public function excluir_atividade($cd_sistema_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE eprev.sistema_atividade
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_atividade = ".intval($cd_sistema_atividade).";";

        $this->db->query($qr_sql);
    }

    public function anexo_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO eprev.sistema_arquivo
                 (
                    cd_sistema,
                    arquivo, 
                    arquivo_nome,
                    ds_sistema_arquivo,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($args['cd_sistema']).",
                    ".str_escape($args['arquivo']).",
                    ".str_escape($args['arquivo_nome']).",
                    ".(trim($args['ds_sistema_arquivo']) != '' ? str_escape($args['ds_sistema_arquivo']) : 'DEFAULT').",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
 
        $this->db->query($qr_sql);
    }

    public function anexo_listar($cd_sistema)
    {
        $qr_sql = "
            SELECT a.cd_sistema_arquivo,
                   a.ds_sistema_arquivo,    
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS usuario_inclusao
              FROM eprev.sistema_arquivo  a
             WHERE a.dt_exclusao IS NULL
               AND a.cd_sistema = ".intval($cd_sistema)."
             ORDER BY a.dt_inclusao DESC ";

        return $this->db->query($qr_sql)->result_array();
    }

    public function anexo_carrega($cd_sistema)
    {
        $qr_sql = "
            SELECT d.cd_sistema as cd_sistema,
                   da.cd_sistema_arquivo as cd_sistema_arquivo,
                   da.arquivo as arquivo,
                   da.arquivo_nome as arquivo_nome
              FROM eprev.sistema d
              JOIN eprev.sistema_arquivo da
                ON d.cd_sistema = da.cd_sistema 
             WHERE d.cd_sistema = ".intval($cd_sistema).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function anexo_excluir($cd_sistema, $cd_sistema_arquivo , $cd_usuario)
    {
        $qr_sql = "
            UPDATE eprev.sistema_arquivo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_sistema          = ".intval($cd_sistema)."
               AND cd_sistema_arquivo  = ".intval($cd_sistema_arquivo).";";

        $this->db->query($qr_sql);
    }

    public function rotina_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO eprev.sistema_rotina
                 (
                    cd_sistema,
                    cd_evento,
                    ds_sistema_rotina,
                    ds_descricao,
                    ds_job,
                    ds_execucao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                     ".intval($args['cd_sistema']).",
                     ".(trim($args['cd_evento']) != '' ? intval($args['cd_evento']) : 'DEFAULT').",
                     ".(trim($args['ds_sistema_rotina']) != '' ? str_escape($args['ds_sistema_rotina']) : 'DEFAULT').",
                     ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : 'DEFAULT').",
                     ".(trim($args['ds_job']) != '' ? str_escape($args['ds_job']) : 'DEFAULT').",
                     ".(trim($args['ds_execucao']) != '' ? str_escape($args['ds_execucao']) : 'DEFAULT').",
                     ".intval($args['cd_usuario']).",
                     ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_rotina($cd_sistema_rotina, $args = array())
    {
        $qr_sql = "
            UPDATE eprev.sistema_rotina
               SET ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : 'DEFAULT').",
                   ds_sistema_rotina    = ".(trim($args['ds_sistema_rotina']) != '' ? str_escape($args['ds_sistema_rotina']) : 'DEFAULT').",
                   cd_evento            = ".(trim($args['cd_evento']) != '' ? intval($args['cd_evento']) : 'DEFAULT').",
                   ds_job               = ".(trim($args['ds_job']) != '' ? str_escape($args['ds_job']) : 'DEFAULT').",
                   ds_execucao          = ".(trim($args['ds_execucao']) != '' ? str_escape($args['ds_execucao']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_rotina = ".intval($cd_sistema_rotina).";";

        $this->db->query($qr_sql);
    }

    public function rotina_listar($cd_sistema)
    {
        $qr_sql = "
            SELECT a.cd_sistema_rotina,
                   a.ds_sistema_rotina,    
                   a.ds_descricao,
                   a.cd_evento,
                   a.ds_job,
                   a.ds_execucao,
                   a.cd_sistema,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   a.cd_evento ||' - '||e.nome AS ds_evento
              FROM eprev.sistema_rotina  a
              JOIN projetos.eventos e
                ON e.cd_evento = a.cd_evento
             WHERE a.dt_exclusao IS NULL
               AND a.cd_sistema = ".intval($cd_sistema)."
             ORDER BY a.dt_inclusao DESC ";
  
      return $this->db->query($qr_sql)->result_array();
    }

    public function rotina_carrega($cd_sistema_rotina)
    {
        $qr_sql = "
            SELECT d.cd_sistema as cd_sistema,
                   da.cd_sistema_rotina as cd_sistema_rotina,
                   da.cd_evento,
                   da.ds_job,
                   da.ds_execucao,
                   da.ds_sistema_rotina as ds_sistema_rotina,
                   da.ds_descricao as ds_descricao
              FROM eprev.sistema d
              JOIN eprev.sistema_rotina da
                ON d.cd_sistema = da.cd_sistema 
             WHERE da.cd_sistema_rotina = ".intval($cd_sistema_rotina).";";
  
        return $this->db->query($qr_sql)->row_array();
    }

    public function metodo_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO eprev.sistema_metodo
                 (
                    cd_sistema,
                    cd_evento,
                    ds_sistema_metodo,
                    ds_descricao,
                    nr_ordem,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($args['cd_sistema']).",
                    ".(trim($args['cd_evento']) != '' ? intval($args['cd_evento']) : 'DEFAULT').",
                    ".(trim($args['ds_sistema_metodo']) != '' ? str_escape($args['ds_sistema_metodo']) : 'DEFAULT').",
                    ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : 'DEFAULT').",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_metodo($cd_sistema_metodo, $args = array())
    {
        $qr_sql = "
            UPDATE eprev.sistema_metodo
               SET ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : 'DEFAULT').",
                   ds_sistema_metodo    = ".(trim($args['ds_sistema_metodo']) != '' ? str_escape($args['ds_sistema_metodo']) : 'DEFAULT').",
                   cd_evento            = ".(trim($args['cd_evento']) != '' ? intval($args['cd_evento']) : 'DEFAULT').",
                   nr_ordem             = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_metodo = ".intval($cd_sistema_metodo).";";

        $this->db->query($qr_sql);
    }

    public function metodo_listar($cd_sistema)
    {
        $qr_sql = "
            SELECT a.cd_sistema_metodo,
                   a.ds_sistema_metodo,    
                   a.ds_descricao,
                   a.cd_evento,
                   a.nr_ordem,
                   a.cd_sistema,
                   a.cd_usuario_inclusao,
                   a.dt_inclusao,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   a.cd_evento ||' - '||e.nome AS ds_evento
              FROM eprev.sistema_metodo a
              LEFT JOIN projetos.eventos e
                ON e.cd_evento = a.cd_evento
             WHERE a.dt_exclusao IS NULL
               AND a.cd_sistema = ".intval($cd_sistema)."
             ORDER BY a.cd_sistema_metodo ASC ";

        return $this->db->query($qr_sql)->result_array();
    }

    public function metodo_carrega($cd_sistema_metodo)
    {
        $qr_sql = "
            SELECT d.cd_sistema as cd_sistema,
                   da.cd_sistema_metodo as cd_sistema_metodo,
                   da.cd_evento,
                   da.nr_ordem,
                   da.ds_sistema_metodo as ds_sistema_metodo,
                   da.ds_descricao as ds_descricao
              FROM eprev.sistema d
              JOIN eprev.sistema_metodo da
                ON d.cd_sistema = da.cd_sistema 
             WHERE da.cd_sistema_metodo = ".intval($cd_sistema_metodo).";";
  
        return $this->db->query($qr_sql)->row_array();
    }

    public function metodo_excluir($cd_sistema_metodo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE eprev.sistema_metodo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_metodo  = ".intval($cd_sistema_metodo).";";

        $this->db->query($qr_sql);
    }

    public function pendencia_listar($cd_sistema)
    {
        $qr_sql = "
            SELECT a.cd_sistema_pendencia,
                   a.cd_pendencia_minha_query||' - '||pmq.cd_pendencia_minha as cd_pendencia_minha_query,
                   a.nr_ordem,
                   a.cd_sistema,
                   pm.ds_pendencia_minha,
                   pm.cd_pendencia_minha,
                   a.cd_usuario_inclusao
              FROM eprev.sistema_pendencia  a
              JOIN gestao.pendencia_minha_query pmq
                ON pmq.cd_pendencia_minha_query = a.cd_pendencia_minha_query
              JOIN gestao.pendencia_minha pm
                ON pm.cd_pendencia_minha = pmq.cd_pendencia_minha  
             WHERE a.dt_exclusao IS NULL
               AND a.cd_sistema = ".intval($cd_sistema)." 
             ORDER BY a.cd_sistema_pendencia ASC  ";

        return $this->db->query($qr_sql)->result_array();
    }

    public function pendencia_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO eprev.sistema_pendencia
                 (
                    cd_sistema,
                    cd_pendencia_minha_query,
                    nr_ordem,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($args['cd_sistema']).",
                    ".(trim($args['cd_pendencia_minha_query']) != '' ? intval($args['cd_pendencia_minha_query']) : 'DEFAULT').",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function pendencia_atualizar($cd_sistema_pendencia, $args = array())
    {
        $qr_sql = "
            UPDATE eprev.sistema_pendencia
               SET nr_ordem                 = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                   cd_pendencia_minha_query = ".(trim($args['cd_pendencia_minha_query']) != '' ? intval($args['cd_pendencia_minha_query']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_pendencia = ".intval($sistema_pendencia).";";

        $this->db->query($qr_sql);
    }   

    public function pendencia_carrega($cd_sistema_pendencia)
    {
        $qr_sql = "
            SELECT cd_sistema,
                   cd_atividade,
                   cd_sistema_atividade
              FROM eprev.sistema_pendencia 
             WHERE dt_exclusao IS NULL
               AND cd_sistema_pendencia = ".intval($cd_sistema_pendencia).";";

        return $this->db->query($qr_sql)->row_array();
    } 

    public function pendencia_excluir($cd_sistema_pendencia, $cd_usuario)
    {
        $qr_sql = "
            UPDATE eprev.sistema_pendencia
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_sistema_pendencia  = ".intval($cd_sistema_pendencia).";";

        $this->db->query($qr_sql);
    }    
}
?>