<?php
class Novo_instituidor_model extends Model
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

    public function set_ordem($cd_novo_instituidor_estrutura, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_estrutura
               SET nr_novo_instituidor_estrutura = ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function get_atividades($cd_novo_instituidor_estrutura)
    {
        $qr_sql = "
            SELECT cd_novo_instituidor_estrutura AS value,
                   nr_novo_instituidor_estrutura|| ' - ' || ds_novo_instituidor_estrutura AS text
              FROM projetos.novo_instituidor_estrutura
             WHERE dt_exclusao IS NULL
               AND cd_novo_instituidor_estrutura != ".intval($cd_novo_instituidor_estrutura)."
             ORDER BY nr_novo_instituidor_estrutura ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_estrutura_dependencia($cd_novo_instituidor_estrutura)
    {
        $qr_sql = "
            SELECT niep.cd_novo_instituidor_estrutura_dep,
                   nie.nr_novo_instituidor_estrutura || ' - ' || nie.ds_novo_instituidor_estrutura AS ds_atividade_dependente
              FROM projetos.novo_instituidor_estrutura_dependencia niep
              JOIN projetos.novo_instituidor_estrutura nie
                ON nie.cd_novo_instituidor_estrutura = niep.cd_novo_instituidor_estrutura_dep
             WHERE niep.dt_exclusao IS NULL 
               AND niep.cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    	  	SELECT cd_novo_instituidor_estrutura,
    	  	       nr_novo_instituidor_estrutura,
    	  	       ds_novo_instituidor_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_substituto) AS ds_usuario_substituto,
                   cd_usuario_responsavel,
                   cd_usuario_substituto,  
    	  	       nr_prazo,
                   TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       ds_observacao
    	  	  FROM projetos.novo_instituidor_estrutura
    	  	 WHERE dt_exclusao IS NULL
    	  	   ".(trim($args['fl_desativado']) == 'S' ? "AND dt_desativado IS NOT NULL" : "")."
    	  	   ".(trim($args['fl_desativado']) == 'N' ? "AND dt_desativado IS NULL" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_proximo_numero()
    {
    	$qr_sql = "
    		SELECT (nr_novo_instituidor_estrutura + 1) AS nr_novo_instituidor_estrutura
    		  FROM projetos.novo_instituidor_estrutura
    		 WHERE dt_exclusao   IS NULL
    		   AND dt_desativado IS NULL
    		 ORDER BY nr_novo_instituidor_estrutura DESC
    		 LIMIT 1";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_novo_instituidor_estrutura)
    {
    	$qr_sql = "
    	  	SELECT cd_novo_instituidor_estrutura,
    	  	       nr_novo_instituidor_estrutura,
    	  	       ds_novo_instituidor_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       cd_usuario_responsavel,
    	  	       cd_usuario_substituto,
    	  	       nr_prazo,
    	  	       ds_observacao,
    	  	       TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       funcoes.get_usuario_nome(cd_usuario_desativado) AS ds_usuario_desativado
    	  	  FROM projetos.novo_instituidor_estrutura
    	  	 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_novo_instituidor_estrutura = intval($this->db->get_new_id(
            'projetos.novo_instituidor_estrutura', 
            'cd_novo_instituidor_estrutura'
        ));

        $qr_sql = "
            INSERT INTO projetos.novo_instituidor_estrutura
                 (
            		cd_novo_instituidor_estrutura, 
            		nr_novo_instituidor_estrutura, 
            		ds_novo_instituidor_estrutura, 
            		ds_atividade, 
            		cd_gerencia, 
            		cd_usuario_responsavel, 
            		cd_usuario_substituto, 
            		nr_prazo, 
            		ds_observacao, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
                 )
    		VALUES 
                 (
                    ".intval($cd_novo_instituidor_estrutura).",
                    ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : "DEFAULT").",
                    ".(trim($args['ds_novo_instituidor_estrutura']) != '' ? str_escape($args['ds_novo_instituidor_estrutura']) : "DEFAULT").",
                    ".(trim($args['ds_atividade']) != '' ? str_escape($args['ds_atividade']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
                    ".(trim($args['nr_prazo']) != '' ? intval($args['nr_prazo']) : "DEFAULT").",
                    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        if(count($args['atividade_estrutura_dependencia']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.novo_instituidor_estrutura_dependencia(cd_novo_instituidor_estrutura, cd_novo_instituidor_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_novo_instituidor_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_estrutura_dependencia']).")) x;";
        }
       
        $this->db->query($qr_sql);

        return $cd_novo_instituidor_estrutura;
    }

    public function atualizar($cd_novo_instituidor_estrutura, $args = array())
    {
    	$qr_sql = "
            UPDATE projetos.novo_instituidor_estrutura
               SET nr_novo_instituidor_estrutura = ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : "DEFAULT").",
            	   ds_novo_instituidor_estrutura = ".(trim($args['ds_novo_instituidor_estrutura']) != '' ? str_escape($args['ds_novo_instituidor_estrutura']) : "DEFAULT").",
            	   ds_atividade                  = ".(trim($args['ds_atividade']) != '' ? str_escape($args['ds_atividade']) : "DEFAULT").",
            	   cd_gerencia                   = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
            	   cd_usuario_responsavel        = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
            	   cd_usuario_substituto         = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
            	   nr_prazo                      = ".(trim($args['nr_prazo']) != '' ? intval($args['nr_prazo']) : "DEFAULT").",
            	   ds_observacao                 = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
                   dt_alteracao                  = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        if(count($args['atividade_estrutura_dependencia']) > 0)
        {
             $qr_sql .= "
                UPDATE projetos.novo_instituidor_estrutura_dependencia
                   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
                       dt_exclusao                              = CURRENT_TIMESTAMP
                 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                   AND dt_exclusao IS NULL
                   AND cd_novo_instituidor_estrutura_dep NOT IN (".implode(",", $args['atividade_estrutura_dependencia']).");
       
                INSERT INTO projetos.novo_instituidor_estrutura_dependencia(cd_novo_instituidor_estrutura, cd_novo_instituidor_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_novo_instituidor_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_estrutura_dependencia']).")) x
                 WHERE x.column1 NOT IN (SELECT a.cd_novo_instituidor_estrutura_dep
                                           FROM projetos.novo_instituidor_estrutura_dependencia a
                                          WHERE a.cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.novo_instituidor_estrutura_dependencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
    }

    public function ativar($cd_novo_instituidor_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_estrutura
               SET cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP,
                   cd_usuario_desativado = NULL,
                   dt_desativado         = NULL
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function desativar($cd_novo_instituidor_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_estrutura
               SET cd_usuario_desativado = ".intval($cd_usuario).",
                   dt_desativado         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function get_planos()
    {
        $qr_sql = "
            SELECT cd_plano AS value,
                   descricao AS text
              FROM public.planos
             WHERE cd_plano IN (7, 8, 9)
             ORDER BY descricao ASC;";

        return $this->db->query($qr_sql)->result_array();     
    }

    public function get_instituidor()
    {
        $qr_sql = "
            SELECT cd_novo_instituidor AS value,
                   ds_nome_instituidor AS text
              FROM projetos.novo_instituidor
             WHERE dt_exclusao IS NULL
             ORDER BY ds_nome_instituidor ASC;";

        return $this->db->query($qr_sql)->result_array();     
    }

    public function instituidor_listar($args = array())
    {
        $qr_sql = "
            SELECT ni.ds_nome_instituidor,
                   ni.cd_novo_instituidor,
                   TO_CHAR(ni.dt_limite_aprovacao,'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(ni.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   TO_CHAR(ni.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   p.descricao,
				   ni.cd_plano,
				   ni.cd_empresa,
				   pa.sigla AS ds_empresa,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade nia
                     WHERE nia.cd_novo_instituidor = ni.cd_novo_instituidor
                       AND nia.dt_exclusao         IS NULL) AS qt_atividade,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade niae
                     WHERE niae.cd_novo_instituidor = ni.cd_novo_instituidor
                       AND niae.dt_exclusao         IS NULL
                       AND niae.dt_encerramento     IS NOT NULL) AS qt_atividades_encerradas,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade niae
                     WHERE niae.cd_novo_instituidor = ni.cd_novo_instituidor
                       AND niae.dt_exclusao         IS NULL
                       AND niae.dt_encerramento     IS NULL) AS qt_atividades_abertas
              FROM projetos.novo_instituidor ni
              JOIN public.planos p
                ON ni.cd_plano = p.cd_plano
			  LEFT JOIN public.patrocinadoras pa
			    ON pa.cd_empresa = ni.cd_empresa
             WHERE ni.dt_exclusao IS NULL
               ".(!gerencia_in(array('GP')) ? "AND ni.dt_inicio IS NOT NULL" : "")."
               ".(trim($args['cd_plano']) != '' ? "AND ni.cd_plano = ".intval($args['cd_plano']) : "")."
               ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', ni.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function instituidor_carrega($cd_novo_instituidor)
    {
        $qr_sql = "
            SELECT ni.ds_nome_instituidor,
                   ni.cd_novo_instituidor,
                   p.descricao,
                   TO_CHAR(ni.dt_limite_aprovacao,'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(ni.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   ni.cd_plano,
                   ni.cd_empresa,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade nia
                     WHERE nia.cd_novo_instituidor = ni.cd_novo_instituidor
                       AND nia.dt_exclusao         IS NULL) AS qt_atividade,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade niae
                     WHERE niae.cd_novo_instituidor = ni.cd_novo_instituidor
                       AND niae.dt_exclusao         IS NULL
                       AND niae.dt_encerramento     IS NOT NULL) AS qt_atividades_encerradas
              FROM projetos.novo_instituidor ni
              JOIN public.planos p
                ON ni.cd_plano = p.cd_plano 
             WHERE ni.cd_novo_instituidor = ".intval($cd_novo_instituidor)."
               AND ni.dt_exclusao         IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function instituidor_salvar($args = array())
    {
        $cd_novo_instituidor = intval($this->db->get_new_id('projetos.novo_instituidor','cd_novo_instituidor'));

        $qr_sql = "
            INSERT INTO projetos.novo_instituidor
                 (
                    cd_novo_instituidor,
                    ds_nome_instituidor,
                    dt_limite_aprovacao,
                    cd_plano,
                    cd_empresa,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_novo_instituidor).",
                    ".(trim($args['ds_nome_instituidor']) != '' ? str_escape($args['ds_nome_instituidor']) : "DEFAULT").",
                    ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."','DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(intval($args['cd_plano']) > 0 ? intval($args['cd_plano']) : "DEFAULT").",
                    ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_novo_instituidor;
    }

    public function instituidor_atualizar($cd_novo_instituidor, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor
               SET ds_nome_instituidor  = ".(trim($args['ds_nome_instituidor']) != '' ? str_escape($args['ds_nome_instituidor']) : "DEFAULT").",
                   cd_plano             = ".(intval($args['cd_plano']) > 0 ? intval($args['cd_plano']) : "DEFAULT").",
                   cd_empresa           = ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                   dt_limite_aprovacao  = ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor = ".intval($cd_novo_instituidor).";";

        $this->db->query($qr_sql);
    }

    public function cria_atividade_instituidor($cd_novo_instituidor, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   cd_usuario_inicio    = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   dt_inicio            = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor = ".intval($cd_novo_instituidor).";";

        $qr_sql .= "
            INSERT INTO projetos.novo_instituidor_atividade
                 (       
                    cd_novo_instituidor,
                    cd_novo_instituidor_estrutura,
                    nr_novo_instituidor_atividade,
                    ds_novo_instituidor_atividade,
                    ds_atividade,
                    cd_gerencia,
                    cd_usuario_responsavel,
                    cd_usuario_substituto,
                    nr_prazo,
                    ds_observacao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            SELECT ".intval($cd_novo_instituidor).",
                   cd_novo_instituidor_estrutura,  
                   nr_novo_instituidor_estrutura,
                   ds_novo_instituidor_estrutura,
                   ds_atividade,
                   cd_gerencia,
                   cd_usuario_responsavel,
                   cd_usuario_substituto,
                   nr_prazo,
                   ds_observacao,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM projetos.novo_instituidor_estrutura
             WHERE dt_exclusao   IS NULL
               AND dt_desativado IS NULL;";

        $this->db->query($qr_sql);

        $qr_sql = "
            INSERT INTO projetos.novo_instituidor_atividade_dependencia
                 (
                    cd_novo_instituidor_atividade, 
                    cd_novo_instituidor, 
                    cd_novo_instituidor_atividade_dep, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )

            SELECT (SELECT a2.cd_novo_instituidor_atividade
                      FROM projetos.novo_instituidor_atividade a2
                     WHERE a2.cd_novo_instituidor_estrutura = ed.cd_novo_instituidor_estrutura_dep
                       AND a2.cd_novo_instituidor           = ".intval($cd_novo_instituidor)."),
                   ".intval($cd_novo_instituidor).",
                   a.cd_novo_instituidor_atividade,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM projetos.novo_instituidor_estrutura_dependencia ed
              JOIN projetos.novo_instituidor_atividade a
                ON a.cd_novo_instituidor_estrutura = ed.cd_novo_instituidor_estrutura
             WHERE ed.dt_exclusao                  IS NULL 
               AND a.cd_novo_instituidor           = ".intval($cd_novo_instituidor).";";

        $this->db->query($qr_sql);
    }

    public function get_atividade_inicio($cd_novo_instituidor)
    {
        $qr_sql = "
            SELECT nia.cd_novo_instituidor_atividade,
                   funcoes.get_usuario(nia.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
                   funcoes.get_usuario(nia.cd_usuario_substituto) || '@eletroceee.com.br' AS ds_email_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, nia.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   nia.ds_novo_instituidor_atividade,
                   nia.ds_atividade
              FROM projetos.novo_instituidor_atividade nia
             WHERE nia.cd_novo_instituidor = ".intval($cd_novo_instituidor)."
               AND nia.dt_exclusao         IS NULL
               AND (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade_dependencia niaep
                     WHERE niaep.dt_exclusao                       IS NULL    
                       AND niaep.cd_novo_instituidor               = nia.cd_novo_instituidor
                       AND niaep.cd_novo_instituidor_atividade_dep = nia.cd_novo_instituidor_atividade) = 0;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function iniciar_atividade($cd_novo_instituidor_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_atividade
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_envio_responsavel = CURRENT_TIMESTAMP,
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE dt_exclusao                    IS NULL
               AND cd_novo_instituidor_atividade  = ".intval($cd_novo_instituidor_atividade).";";

        $this->db->query($qr_sql);
    }

    public function listar_atividade($cd_novo_instituidor)
    {
        $qr_sql = "
            SELECT nia.cd_novo_instituidor_estrutura,
                   nia.cd_novo_instituidor_atividade,
                   nia.nr_novo_instituidor_atividade,
                   nia.ds_novo_instituidor_atividade,
                   nia.ds_atividade,
                   nia.cd_gerencia,
                   nia.dt_encerramento,
                   funcoes.get_usuario_nome(nia.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(nia.cd_usuario_substituto) AS ds_usuario_substituto,
                   funcoes.get_usuario_nome(nia.cd_usuario_encerramento) AS ds_usuario_encerramento,
                   TO_CHAR(nia.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel_ini,
                   TO_CHAR(nia.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento_prazo,
                   TO_CHAR(ni.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   nia.nr_prazo,
                   nia.ds_observacao,
                   nia.dt_envio_responsavel,
                   (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade_dependencia niaep
                     WHERE nia.cd_novo_instituidor_estrutura = niaep.cd_novo_instituidor_atividade
                       AND nia.cd_novo_instituidor           = niaep.cd_novo_instituidor
                       AND niaep.dt_exclusao                 IS NULL
                       AND nia.dt_encerramento               IS NULL) AS qt_dependentes
              FROM projetos.novo_instituidor_atividade nia
              JOIN projetos.novo_instituidor ni
                ON nia.cd_novo_instituidor = ni.cd_novo_instituidor
             WHERE nia.dt_exclusao IS NULL
               AND nia.cd_novo_instituidor = ".intval($cd_novo_instituidor).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_dependente($cd_novo_instituidor, $cd_novo_instituidor_atividade)
    {
        $qr_sql = "
            SELECT niep.cd_novo_instituidor_atividade_dependencia,
                   niep.cd_novo_instituidor_atividade,  
                   nie.nr_novo_instituidor_atividade || ' - ' || nie.ds_novo_instituidor_atividade AS ds_atividades_dependentes,
                   nie.dt_encerramento
              FROM projetos.novo_instituidor_atividade_dependencia niep
              JOIN projetos.novo_instituidor_atividade nie
                ON nie.cd_novo_instituidor_atividade = niep.cd_novo_instituidor_atividade
               AND nie.cd_novo_instituidor           = niep.cd_novo_instituidor  
             WHERE niep.dt_exclusao IS NULL 
               AND niep.cd_novo_instituidor               = ".intval($cd_novo_instituidor)."
               AND niep.cd_novo_instituidor_atividade_dep = ".intval($cd_novo_instituidor_atividade)."
             ORDER BY nie.nr_novo_instituidor_atividade  ASC;"; 

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_minhas($cd_usuario, $args = array())
    {
        $qr_sql = "
            SELECT nia.cd_novo_instituidor_estrutura,
                   p.descricao,
                   nia.cd_novo_instituidor_atividade,
                   nia.nr_novo_instituidor_atividade,
                   nia.ds_novo_instituidor_atividade,
                   nia.cd_novo_instituidor,
                   nia.ds_atividade,
                   ni.ds_nome_instituidor,
                   TO_CHAR(nia.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   TO_CHAR(nia.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento_prazo,
                   funcoes.get_usuario_nome(nia.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(nia.cd_usuario_substituto) AS ds_usuario_substituto,
                   funcoes.get_usuario_nome(nia.cd_usuario_encerramento) AS ds_usuario_encerramento,
                   TO_CHAR(funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(nia.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   (CASE WHEN nia.dt_encerramento IS NOT NULL THEN 'success'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) < CURRENT_DATE THEN 'important'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) = CURRENT_DATE THEN 'warning'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) > CURRENT_DATE THEN 'info'
                         ELSE ''
                   END) AS ds_class_prazo
              FROM projetos.novo_instituidor_atividade nia
              JOIN projetos.novo_instituidor ni
                ON nia.cd_novo_instituidor = ni.cd_novo_instituidor
              JOIN public.planos p 
                ON ni.cd_plano = p.cd_plano
             WHERE nia.dt_exclusao IS NULL
               AND nia.dt_envio_responsavel IS NOT NULL
               AND 
                 (
                    nia.cd_usuario_responsavel = ".intval($cd_usuario)."
                    OR 
                    nia.cd_usuario_substituto =  ".intval($cd_usuario)."
                 )
              ".(trim($args['fl_encerramento']) == 'S' ? "AND nia.dt_encerramento IS NOT NULL" : "")."
              ".(trim($args['fl_encerramento']) == 'N' ? "AND nia.dt_encerramento IS NULL": "")."
              ".(((trim($args['dt_prazo_ini']) != '') AND (trim($args['dt_prazo_fim']) != '')) ? " AND DATE_TRUNC('day', funcoes.dia_util('DEPOIS',date(nia.dt_envio_responsavel), nia.nr_prazo)) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')" : "")."
              ".(intval($args['cd_novo_instituidor']) != '' ? "AND ni.cd_novo_instituidor = ".intval($args['cd_novo_instituidor'])."" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_atividade($cd_novo_instituidor, $cd_novo_instituidor_atividade)
    {
        $qr_sql = "
            SELECT nia.cd_novo_instituidor_estrutura,
                   nia.cd_novo_instituidor_atividade,
                   nia.nr_novo_instituidor_atividade,
                   nia.ds_novo_instituidor_atividade,
                   TO_CHAR(nia.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento_prazo,
                   nia.cd_novo_instituidor,
                   nia.ds_atividade,
                   nia.dt_encerramento,
                   funcoes.get_usuario_nome(nia.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(nia.cd_usuario_substituto) AS ds_usuario_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(nia.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   (CASE WHEN nia.dt_encerramento IS NOT NULL THEN 'success'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) < CURRENT_DATE THEN 'important'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) = CURRENT_DATE THEN 'warning'
                         WHEN funcoes.dia_util('DEPOIS', nia.dt_envio_responsavel::date, nia.nr_prazo) > CURRENT_DATE THEN 'info'
                         ELSE ''
                   END) AS ds_class_prazo
              FROM projetos.novo_instituidor_atividade nia
              JOIN projetos.novo_instituidor ni
                ON nia.cd_novo_instituidor = ni.cd_novo_instituidor
             WHERE nia.dt_exclusao IS NULL
               AND nia.dt_envio_responsavel IS NOT NULL
               AND nia.cd_novo_instituidor_atividade = ".intval($cd_novo_instituidor_atividade)." 
               AND nia.cd_novo_instituidor           = ".intval($cd_novo_instituidor).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function valida_atividade($cd_atividade)
    {
        $qr_sql = "
            SELECT COUNT(*) AS valida
              FROM projetos.atividades
             WHERE numero = ".intval($cd_atividade).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_acompanhamento($cd_novo_instituidor_atividade)
    {
        $qr_sql = "
            SELECT niaa.cd_novo_instituidor_atividade_acompanhamento,
                   niaa.cd_novo_instituidor_atividade,
                   niaa.ds_acompanhamento,
                   niaa.cd_atividade,
                   funcoes.get_usuario_nome(niaa.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(niaa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   (SELECT a.area
                      FROM projetos.atividades a
                     WHERE niaa.cd_atividade = a.numero) AS cd_gerencia
              FROM projetos.novo_instituidor_atividade_acompanhamento niaa
             WHERE niaa.dt_exclusao IS NULL
               AND niaa.cd_novo_instituidor_atividade = ".intval($cd_novo_instituidor_atividade).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_acompanhamento($cd_novo_instituidor_atividade_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_novo_instituidor_atividade_acompanhamento,
                   ds_acompanhamento,
                   cd_atividade
              FROM projetos.novo_instituidor_atividade_acompanhamento
             WHERE dt_exclusao IS NULL
               AND cd_novo_instituidor_atividade_acompanhamento = ".intval($cd_novo_instituidor_atividade_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.novo_instituidor_atividade_acompanhamento
                 (
                    cd_novo_instituidor_atividade,
                    ds_acompanhamento,
                    cd_atividade,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".(trim($args['cd_novo_instituidor_atividade']) != '' ? intval($args['cd_novo_instituidor_atividade']) : "DEFAULT").",
                    ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                    ".(intval($args['cd_atividade']) > 0 ? intval($args['cd_atividade']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
            
        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_novo_instituidor_atividade_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_atividade_acompanhamento
               SET ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_atividade         = ".(intval($args['cd_atividade']) > 0 ? intval($args['cd_atividade']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_atividade_acompanhamento = ".intval($cd_novo_instituidor_atividade_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_novo_instituidor_atividade_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_atividade_acompanhamento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_atividade_acompanhamento = ".intval($cd_novo_instituidor_atividade_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function encerrar_atividade($cd_novo_instituidor_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor_atividade
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_atividade = ".intval($cd_novo_instituidor_atividade).";";

        $this->db->query($qr_sql);
    }

    public function get_atividade_dependente_inicio($cd_novo_instituidor, $cd_novo_instituidor_estrutura)
    {
        $qr_sql = "     
            SELECT nia.cd_novo_instituidor_atividade,
                   funcoes.get_usuario(nia.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
                   funcoes.get_usuario(nia.cd_usuario_substituto) || '@eletroceee.com.br' AS ds_email_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, nia.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   nia.ds_novo_instituidor_atividade,
                   nia.ds_atividade
              FROM projetos.novo_instituidor_atividade nia
              JOIN projetos.novo_instituidor_atividade_dependencia niad
                ON niad.cd_novo_instituidor_atividade_dep = nia.cd_novo_instituidor_atividade
             WHERE nia.dt_exclusao                    IS NULL
               AND nia.dt_envio_responsavel           IS NULL
               AND nia.dt_encerramento                IS NULL
               AND nia.cd_novo_instituidor            = ".intval($cd_novo_instituidor)."
               AND niad.cd_novo_instituidor_atividade = ".intval($cd_novo_instituidor_estrutura)."
               AND (SELECT COUNT(*)
                      FROM projetos.novo_instituidor_atividade_dependencia niad2
                      JOIN projetos.novo_instituidor_atividade nia2
                        ON nia2.cd_novo_instituidor_atividade = niad2.cd_novo_instituidor_atividade
                     WHERE niad2.cd_novo_instituidor               = nia.cd_novo_instituidor  
                       AND niad2.cd_novo_instituidor_atividade_dep = nia.cd_novo_instituidor_atividade
                       AND nia2.dt_encerramento                    IS NULL) = 0;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function encerrar_instituidor($cd_novo_instituidor)
    {
        $qr_sql = "
            UPDATE projetos.novo_instituidor
               SET dt_encerramento = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor = ".intval($cd_novo_instituidor).";";

        $this->db->query($qr_sql);
    }
}

