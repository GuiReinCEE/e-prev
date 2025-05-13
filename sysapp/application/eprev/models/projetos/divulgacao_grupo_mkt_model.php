<?php
class Divulgacao_grupo_mkt_model extends Model
{
    function __construct()
    {
        parent::Model();

        CheckLogin();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT dg.cd_divulgacao_grupo, 
                   dg.ds_divulgacao_grupo,
                   COALESCE(dgt.qt_registro,0) AS qt_registro,
                   dg.tp_grupo,
                   funcoes.get_usuario_nome(dg.cd_usuario_inclusao) AS cd_usuario_inclusao,
                   TO_CHAR(dg.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(dg.cd_usuario_alteracao) AS cd_usuario_alteracao,
                   TO_CHAR(dg.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                   (CASE WHEN dg.tp_grupo = 'I' THEN 'Importação Email'
                         WHEN dg.tp_grupo = 'P' THEN 'Importação RE'
                         WHEN dg.tp_grupo = 'C' THEN 'Configuração'
                         ELSE ''
                   END) AS ds_grupo
              FROM projetos.divulgacao_grupo dg
              LEFT JOIN projetos.divulgacao_grupo_total dgt
                ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo  
             WHERE dg.dt_exclusao IS NULL
               ".(trim($args['grupo']) == '' ? "AND dg.tp_grupo IS NOT NULL" : "AND dg.tp_grupo = '".trim($data['grupo'])."'")." ;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT dg.cd_divulgacao_grupo, 
                   dg.ds_cidade,
                   dg.ds_divulgacao_grupo,
                   COALESCE(dgt.qt_registro,0) AS qt_registro,
                   dg.tp_grupo,
                   (CASE WHEN dg.tp_grupo = 'I' THEN 'Importação Email'
                         WHEN dg.tp_grupo = 'P' THEN 'Importação RE'
                         WHEN dg.tp_grupo = 'C' THEN 'Configuração'
                         ELSE ''
                   END) AS ds_grupo,
                   dg.qr_sql
              FROM projetos.divulgacao_grupo dg
              LEFT JOIN projetos.divulgacao_grupo_total dgt
                ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo   
             WHERE dg.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_divulgacao_grupo = intval($this->db->get_new_id('projetos.divulgacao_grupo', 'cd_divulgacao_grupo'));

        $args['sql'] = str_replace('{CD_DIVULGACAO_GRUPO}', $cd_divulgacao_grupo, $args['sql']);

        $qr_sql = "
            INSERT INTO projetos.divulgacao_grupo
                 (
                    cd_divulgacao_grupo, 
                    ds_divulgacao_grupo,
                    qr_sql,
                    tp_grupo,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_divulgacao_grupo).",
                    ".(trim($args['ds_divulgacao_grupo']) != '' ? str_escape($args['ds_divulgacao_grupo']) : "DEFAULT").",
                    ".(trim($args['sql']) != '' ? str_escape($args['sql']) : "DEFAULT").",
                    '".trim($args['tp_grupo'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_divulgacao_grupo;
    }   

    public function atualizar($cd_divulgacao_grupo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET ds_divulgacao_grupo  = ".(trim($args['ds_divulgacao_grupo']) != "" ? str_escape($args['ds_divulgacao_grupo']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_divulgacao_grupo,$cd_usuario_exclusao)
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET cd_usuario_exclusao = ".intval($cd_usuario_exclusao).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";"; 

        $this->db->query($qr_sql); 
    }

    public function get_tipo()
    {
        $qr_sql = "
            SELECT tp_grupo,
                   (CASE WHEN tp_grupo = 'I' THEN 'Importação'
                         WHEN tp_grupo = 'C' THEN 'Configuração'
                         ELSE ''
                   END) AS ds_grupo
              FROM projetos.divulgacao_grupo      
             WHERE dt_exclusao IS NULL
             ORDER BY ds_grupo;";    

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_email($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT cd_divulgacao_grupo_email,
                   ds_divulgacao_grupo_email,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM projetos.divulgacao_grupo_email 
             WHERE dt_exclusao IS NULL
               AND cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_res($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT g.cd_divulgacao_grupo_participante,
                   p.cd_plano,
                   p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.nome,
                   p.email,
                   p.email_profissional,
                   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
                   TO_CHAR(g.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM projetos.divulgacao_grupo_participante g
              JOIN participantes p 
                ON p.cd_empresa            = g.cd_empresa
               AND p.cd_registro_empregado = g.cd_registro_empregado
               AND p.seq_dependencia       = g.seq_dependencia
             WHERE g.dt_exclusao         IS NULL
               AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
               AND g.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_participantes($qr_sql)
    {
        return $this->db->query(trim($qr_sql).";")->result_array();
    }

    public function anexo_salvar($cd_divulgacao_grupo, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.divulgacao_grupo_email 
                 (
                    cd_divulgacao_grupo,
                    ds_divulgacao_grupo_email,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_divulgacao_grupo).",
                    ".(trim($args['ds_divulgacao_grupo_email']) != '' ? 'funcoes.remove_acento('.str_escape($args['ds_divulgacao_grupo_email']).')' : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function participante_salvar($cd_divulgacao_grupo, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.divulgacao_grupo_participante
                 (
                    cd_divulgacao_grupo, 
                    cd_empresa, 
                    cd_registro_empregado, 
                    seq_dependencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_divulgacao_grupo).",
                    ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                    ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                    ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function excluir_email($cd_divulgacao_grupo_email, $cd_usuario_exclusao)
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo_email
               SET cd_usuario_exclusao = ".intval($cd_usuario_exclusao).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo_email = ".intval($cd_divulgacao_grupo_email).";"; 

        $this->db->query($qr_sql); 
    }

    public function excluir_participante($cd_divulgacao_grupo_participante, $cd_usuario_exclusao)
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo_participante
               SET cd_usuario_exclusao = ".intval($cd_usuario_exclusao).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo_participante = ".intval($cd_divulgacao_grupo_participante).";"; 

        $this->db->query($qr_sql); 
    }

    public function atualizar_grupo($cd_divulgacao_grupo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        $this->db->query($qr_sql);
    }

    public function atualiza_registro($cd_divulgacao_grupo) 
    {
        $qr_sql = "SELECT rotinas.divulgacao_grupo_qt_registro(".intval($cd_divulgacao_grupo).");";

        $this->db->query($qr_sql);
    }

    public function get_empresa()
    {
        $qr_sql = "
            SELECT cd_empresa AS value,
                   cd_empresa || ' - ' || sigla AS text
              FROM public.patrocinadoras
             WHERE cd_empresa NOT IN (4, 5)
             ORDER BY sigla ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_plano()
    {
        $qr_sql = "
            SELECT cd_plano AS value,
                   descricao AS text
              FROM public.planos
             WHERE cd_plano > 0
             ORDER BY text ASC;";

        return $this->db->query($qr_sql)->result_array();
    }   

    public function carrega_tipo($cd_divulgacao_grupo)
    {
        $qr_sql =   "
            SELECT  ds_tipo,
                    (CASE WHEN ds_tipo = 'ATIV'
                          THEN 'Ativo'
                          WHEN ds_tipo = 'APOS'
                          THEN 'Aposentado'
                          WHEN ds_tipo = 'PENS'
                          THEN 'Pensionista'
                          WHEN ds_tipo = 'EXAU'
                          THEN 'Ex-Autárquico'
                          WHEN ds_tipo = 'AUXD'
                          THEN 'Auxilio Doença'
                    END) AS tipo
              FROM  projetos.divulgacao_grupo_tipo
             WHERE  dt_exclusao IS NULL
               AND  cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_empresa($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT dge.cd_empresa,
                   p.sigla 
              FROM projetos.divulgacao_grupo_empresa dge
              JOIN public.patrocinadoras p
                ON p.cd_empresa = dge.cd_empresa
             WHERE dge.dt_exclusao IS NULL
               AND dge.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_cidade($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT cd_divulgacao_grupo_cidade,
                   ds_cidade
              FROM projetos.divulgacao_grupo_cidade
             WHERE dt_exclusao IS NULL
               AND cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_plano($cd_divulgacao_grupo)
    {
        $qr_sql = "
            SELECT dgp.cd_plano,
                   p.descricao 
              FROM projetos.divulgacao_grupo_plano dgp
              JOIN public.planos p
                ON p.cd_plano = dgp.cd_plano
             WHERE dgp.dt_exclusao IS NULL
               AND dgp.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function configuracao_salvar($cd_divulgacao_grupo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        if(count($args['cd_empresa']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_empresa
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND cd_empresa NOT IN (".implode(",", $args['cd_empresa']).");

                INSERT INTO projetos.divulgacao_grupo_empresa
                    (
                        cd_divulgacao_grupo, 
                        cd_empresa,
                        cd_usuario_inclusao,
                        cd_usuario_alteracao
                    )
                SELECT ".intval($cd_divulgacao_grupo).", 
                       x.column1::integer, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cd_empresa']).")) x
                 WHERE x.column1::integer NOT IN (SELECT a.cd_empresa
                                                    FROM projetos.divulgacao_grupo_empresa a
                                                   WHERE a.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                                                     AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .=  "
                UPDATE projetos.divulgacao_grupo_empresa
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao         IS NULL;";
        }

        if(count($args['cd_plano']) > 0)
        {
            $qr_sql .=  "
                UPDATE projetos.divulgacao_grupo_plano
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND cd_plano NOT IN ('".implode("','", $args['cd_plano'])."');

                INSERT INTO projetos.divulgacao_grupo_plano
                    (
                        cd_divulgacao_grupo, 
                        cd_plano, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                    )
                SELECT ".intval($cd_divulgacao_grupo).", 
                       x.column1::integer, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['cd_plano'])."')) x
                 WHERE x.column1::integer NOT IN (SELECT a.cd_plano
                                                    FROM projetos.divulgacao_grupo_plano a
                                                   WHERE a.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                                                     AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_plano
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['ds_tipo']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_tipo
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND ds_tipo NOT IN ('".implode("','", $args['ds_tipo'])."');

                INSERT INTO projetos.divulgacao_grupo_tipo
                    (
                        cd_divulgacao_grupo, 
                        ds_tipo, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                    )
                SELECT ".intval($cd_divulgacao_grupo).", 
                       x.column1, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['ds_tipo'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.ds_tipo
                                            FROM projetos.divulgacao_grupo_tipo a
                                           WHERE a.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                                             AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_tipo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['ds_cidade']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_cidade
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND ds_cidade NOT IN ('".implode("','", $args['ds_cidade'])."');

                INSERT INTO projetos.divulgacao_grupo_cidade
                    (
                        cd_divulgacao_grupo, 
                        ds_cidade, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                    )
                SELECT ".intval($cd_divulgacao_grupo).", 
                       UPPER(funcoes.remove_acento(TRIM(x.column1))),
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['ds_cidade'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.ds_cidade
                                            FROM projetos.divulgacao_grupo_cidade a
                                           WHERE a.cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                                             AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.divulgacao_grupo_cidade
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function excluir_grupo($cd_divulgacao_grupo,$cd_usuario_exclusao)
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET cd_usuario_exclusao = ".intval($cd_usuario_exclusao).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";"; 

        $this->db->query($qr_sql); 
    }
}
?>