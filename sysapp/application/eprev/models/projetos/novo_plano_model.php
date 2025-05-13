<?php
class Novo_plano_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT npe.cd_novo_plano_estrutura,
                   npe.nr_ordem,
                   npe.ds_novo_plano_estrutura,
                   nps.ds_novo_plano_subprocesso,
                   nps.cd_novo_plano_subprocesso,
                   TO_CHAR(npe.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento
              FROM projetos.novo_plano_estrutura npe
              JOIN projetos.novo_plano_subprocesso nps
                ON nps.cd_novo_plano_subprocesso = npe.cd_novo_plano_subprocesso
             WHERE npe.dt_exclusao IS NULL
                ".(intval($args['cd_novo_plano_subprocesso']) > 0 ? "AND npe.cd_novo_plano_subprocesso = ".intval($args['cd_novo_plano_subprocesso']) : "")."
                ".(trim($args['fl_encerramento']) == 'S' ? "AND dt_encerramento IS NOT NULL" : "")."
    	  	    ".(trim($args['fl_encerramento']) == 'N' ? "AND dt_encerramento IS NULL" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_novo_plano_estrutura)
    {
        $qr_sql = "
            SELECT npe.cd_novo_plano_estrutura,
                   npe.nr_ordem,
                   npe.ds_novo_plano_estrutura,
                   npe.cd_novo_plano_subprocesso,
                   npe.dt_encerramento
              FROM projetos.novo_plano_estrutura npe
             WHERE npe.cd_novo_plano_estrutura = ".intval($cd_novo_plano_estrutura).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_subprocesso()
    {
        $qr_sql = "
            SELECT cd_novo_plano_subprocesso AS value,
                   ds_novo_plano_subprocesso AS text
              FROM projetos.novo_plano_subprocesso
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function set_ordem_subprocesso($cd_novo_plano_subprocesso)
    {
        $qr_sql = "
            SELECT (COALESCE(MAX(nr_ordem), 0) + 1) AS nr_ordem
              FROM projetos.novo_plano_estrutura
             WHERE cd_novo_plano_subprocesso = ".intval($cd_novo_plano_subprocesso)."
               AND dt_exclusao               IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function set_ordem($cd_novo_plano_subprocesso, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_estrutura
               SET nr_ordem = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_plano_subprocesso = ".intval($cd_novo_plano_subprocesso)."
               AND cd_novo_plano_estrutura = ".$args['cd_novo_plano_estrutura'].";";

        $this->db->query($qr_sql);
    }

    public function salvar($args = array())
    {
        $cd_novo_plano_estrutura = intval($this->db->get_new_id('projetos.novo_plano_estrutura', 'cd_novo_plano_estrutura'));

        $qr_sql = "
            INSERT INTO projetos.novo_plano_estrutura
                 (
                    cd_novo_plano_estrutura, 
                    cd_novo_plano_subprocesso,
            		nr_ordem, 
                    ds_novo_plano_estrutura,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
    		VALUES 
                 (
                    ".intval($cd_novo_plano_estrutura).",
                    ".(trim($args['cd_novo_plano_subprocesso']) != '' ? intval($args['cd_novo_plano_subprocesso']) : "DEFAULT").",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".(trim($args['ds_novo_plano_estrutura']) != '' ? str_escape($args['ds_novo_plano_estrutura']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
       
        $this->db->query($qr_sql);

        return $cd_novo_plano_estrutura;
    }

    public function atualizar($cd_novo_plano_estrutura, $args = array())
    {
    	$qr_sql = "
            UPDATE projetos.novo_plano_estrutura
               SET nr_ordem                = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
            	   ds_novo_plano_estrutura = ".(trim($args['ds_novo_plano_estrutura']) != '' ? str_escape($args['ds_novo_plano_estrutura']) : "DEFAULT").",
                   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_novo_plano_estrutura = ".intval($cd_novo_plano_estrutura).";";  

        $this->db->query($qr_sql);
    }

    public function ativar($cd_novo_plano_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_estrutura
               SET cd_usuario_alteracao    = ".intval($cd_usuario).",
                   dt_alteracao            = CURRENT_TIMESTAMP,
                   cd_usuario_encerramento = NULL,
                   dt_encerramento         = NULL
             WHERE cd_novo_plano_estrutura = ".intval($cd_novo_plano_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function desativar($cd_novo_plano_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_estrutura
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_novo_plano_estrutura = ".intval($cd_novo_plano_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function listar_plano()
    {
        $qr_sql = "
            SELECT np.cd_novo_plano,
                   np.ds_nome_plano,
                   TO_CHAR(np.dt_limite_aprovacao, 'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   TO_CHAR(np.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   (SELECT COUNT(*)
                      FROM projetos.novo_plano_atividade npa
                     WHERE npa.cd_novo_plano = np.cd_novo_plano
                       AND npa.dt_exclusao         IS NULL) AS qt_atividade,
                   (SELECT COUNT(*)
                      FROM projetos.novo_plano_atividade npae
                     WHERE npae.cd_novo_plano = np.cd_novo_plano
                       AND npae.dt_exclusao         IS NULL
                       AND npae.dt_encerramento     IS NOT NULL) AS qt_atividades_encerradas,
                   (SELECT COUNT(*)
                      FROM projetos.novo_plano_atividade npae
                     WHERE npae.cd_novo_plano = np.cd_novo_plano
                       AND npae.dt_exclusao         IS NULL
                       AND npae.dt_encerramento     IS NULL) AS qt_atividades_abertas
              FROM projetos.novo_plano np
             WHERE np.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_plano($cd_novo_plano)
    {
        $qr_sql = "
            SELECT np.cd_novo_plano,
                   np.ds_nome_plano,
                   TO_CHAR(np.dt_limite_aprovacao, 'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio
              FROM projetos.novo_plano np
             WHERE np.cd_novo_plano = ".intval($cd_novo_plano)."
               AND np.dt_exclusao   IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_plano($args = array())
    {
        $cd_novo_plano = intval($this->db->get_new_id('projetos.novo_plano', 'cd_novo_plano'));

        $qr_sql = "
            INSERT INTO projetos.novo_plano
                 (
                    cd_novo_plano,
                    ds_nome_plano, 
                    dt_limite_aprovacao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
    		VALUES 
                 (
                    ".intval($cd_novo_plano).",
                    ".(trim($args['ds_nome_plano']) != '' ? str_escape($args['ds_nome_plano']) : "DEFAULT").",
                    ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."','DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
       
        $this->db->query($qr_sql);

        return $cd_novo_plano;
    }

    public function atualizar_plano($cd_novo_plano, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_plano
               SET ds_nome_plano        = '".trim($args['ds_nome_plano'])."',
                   dt_limite_aprovacao  = ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_plano = ".intval($cd_novo_plano).";";

        $this->db->query($qr_sql);
    }

    public function cria_atividade_plano($cd_novo_plano, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_plano
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   cd_usuario_inicio    = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   dt_inicio            = CURRENT_TIMESTAMP
             WHERE cd_novo_plano = ".intval($cd_novo_plano).";";

        $qr_sql .= "
            INSERT INTO projetos.novo_plano_atividade
                 (       
                    cd_novo_plano,
                    cd_novo_plano_estrutura,
                    nr_ordem,
                    ds_novo_plano_atividade,
                    cd_novo_plano_subprocesso,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            SELECT ".intval($cd_novo_plano).",
                   cd_novo_plano_estrutura,  
                   nr_ordem,
                   ds_novo_plano_estrutura,
                   cd_novo_plano_subprocesso,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM projetos.novo_plano_estrutura
             WHERE dt_exclusao     IS NULL
               AND dt_encerramento IS NULL;";

        $this->db->query($qr_sql);
    }

    public function iniciar_atividade($cd_novo_plano_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_atividade
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_envio_responsavel = CURRENT_TIMESTAMP,
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE dt_exclusao                    IS NULL
               AND cd_novo_plano_atividade  = ".intval($cd_novo_plano_atividade).";";

        $this->db->query($qr_sql);
    }

    public function listar_atividade($cd_novo_plano, $cd_novo_plano_subprocesso)
    {
        $qr_sql = "
            SELECT npa.cd_novo_plano_estrutura,
                   npa.cd_novo_plano_atividade,
                   npa.nr_ordem,
                   npa.ds_novo_plano_atividade,
                   npa.dt_encerramento,
                   funcoes.get_usuario_nome(npa.cd_usuario_encerramento) AS ds_usuario_encerramento,
                   TO_CHAR(npa.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento_prazo,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   nps.ds_novo_plano_subprocesso,
                   (SELECT COUNT(*)
                      FROM projetos.novo_plano_atividade_acompanhamento npaa
                     WHERE npa.cd_novo_plano_estrutura = npaa.cd_novo_plano_atividade
                       AND npa.cd_novo_plano           = npaa.cd_novo_plano_atividade
                       AND npaa.dt_exclusao                 IS NULL
                       AND npa.dt_encerramento               IS NULL) AS qt_dependentes,
                   (SELECT TO_CHAR(npaa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' - ' || npaa.ds_acompanhamento
                      FROM projetos.novo_plano_atividade_acompanhamento npaa
                     WHERE npaa.cd_novo_plano_atividade = npa.cd_novo_plano_atividade
                       AND npaa.dt_exclusao IS NULL
                     ORDER BY npaa.dt_inclusao DESC
                     LIMIT 1) AS ds_acompanhamento
              FROM projetos.novo_plano_atividade npa
              JOIN projetos.novo_plano np
                ON npa.cd_novo_plano = np.cd_novo_plano
              JOIN projetos.novo_plano_subprocesso nps
                ON nps.cd_novo_plano_subprocesso = npa.cd_novo_plano_subprocesso
             WHERE npa.dt_exclusao IS NULL
               AND npa.cd_novo_plano             = ".intval($cd_novo_plano)."
               AND npa.cd_novo_plano_subprocesso = ".intval($cd_novo_plano_subprocesso)."
             ORDER BY npa.nr_ordem;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_atividade($cd_novo_plano_atividade)
    {
        $qr_sql = "
            SELECT npa.cd_novo_plano_atividade,
                   npa.ds_novo_plano_atividade, 
                   npa.dt_encerramento,
                   nps.ds_novo_plano_subprocesso
              FROM projetos.novo_plano_atividade npa
              JOIN projetos.novo_plano_subprocesso nps
                ON nps.cd_novo_plano_subprocesso = npa.cd_novo_plano_subprocesso
             WHERE npa.cd_novo_plano_atividade = ".intval($cd_novo_plano_atividade).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_acompanhamento($cd_novo_plano_atividade)
    {
        $qr_sql = "
            SELECT cd_novo_plano_atividade_acompanhamento,
                   ds_acompanhamento,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario
              FROM projetos.novo_plano_atividade_acompanhamento
             WHERE cd_novo_plano_atividade = ".intval($cd_novo_plano_atividade)."
               AND dt_exclusao IS NULL
             ORDER BY dt_inclusao DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_acompanhamento($cd_novo_plano_atividade_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_novo_plano_atividade_acompanhamento,
                   ds_acompanhamento 
              FROM projetos.novo_plano_atividade_acompanhamento
             WHERE cd_novo_plano_atividade_acompanhamento = ".intval($cd_novo_plano_atividade_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_acompanhamento($cd_novo_plano_atividade, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.novo_plano_atividade_acompanhamento
                 (
                    cd_novo_plano_atividade,
                    ds_acompanhamento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
    		VALUES 
                 (
                    ".(trim($cd_novo_plano_atividade) != '' ? intval($cd_novo_plano_atividade) : "DEFAULT").",
                    ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
       
        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_novo_plano_atividade_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_atividade_acompanhamento
               SET ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_plano_atividade_acompanhamento  = ".intval($cd_novo_plano_atividade_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function concluir_atividade($cd_novo_plano_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_plano_atividade
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_novo_plano_atividade  = ".intval($cd_novo_plano_atividade).";";

        $this->db->query($qr_sql);
    }
}