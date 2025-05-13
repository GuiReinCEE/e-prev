<?php
class nova_patrocinadora_model extends Model
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

    public function set_ordem($cd_nova_patrocinadora_estrutura, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_estrutura
               SET nr_nova_patrocinadora_estrutura = ".(trim($args['nr_nova_patrocinadora_estrutura']) != '' ? intval($args['nr_nova_patrocinadora_estrutura']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function get_atividades($cd_nova_patrocinadora_estrutura)
    {
        $qr_sql = "
            SELECT cd_nova_patrocinadora_estrutura AS value,
                   nr_nova_patrocinadora_estrutura|| ' - ' || ds_nova_patrocinadora_estrutura AS text
              FROM projetos.nova_patrocinadora_estrutura
             WHERE dt_exclusao IS NULL
               AND cd_nova_patrocinadora_estrutura != ".intval($cd_nova_patrocinadora_estrutura)."
             ORDER BY nr_nova_patrocinadora_estrutura ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_estrutura_dependencia($cd_nova_patrocinadora_estrutura)
    {
        $qr_sql = "
            SELECT nped.cd_nova_patrocinadora_estrutura_dep,
                   npe.nr_nova_patrocinadora_estrutura || ' - ' || npe.ds_nova_patrocinadora_estrutura AS ds_atividade_dependente
              FROM projetos.nova_patrocinadora_estrutura_dependencia nped
              JOIN projetos.nova_patrocinadora_estrutura npe
                ON npe.cd_nova_patrocinadora_estrutura = nped.cd_nova_patrocinadora_estrutura_dep
             WHERE nped.dt_exclusao IS NULL 
               AND nped.cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    	  	SELECT cd_nova_patrocinadora_estrutura,
    	  	       nr_nova_patrocinadora_estrutura,
    	  	       ds_nova_patrocinadora_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_substituto) AS ds_usuario_substituto,
                   cd_usuario_responsavel,
                   cd_usuario_substituto,
    	  	       nr_prazo,
                   TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       ds_observacao
    	  	  FROM projetos.nova_patrocinadora_estrutura
    	  	 WHERE dt_exclusao IS NULL
    	  	   ".(trim($args['fl_desativado']) == 'S' ? "AND dt_desativado IS NOT NULL" : "")."
    	  	   ".(trim($args['fl_desativado']) == 'N' ? "AND dt_desativado IS NULL" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_proximo_numero()
    {
    	$qr_sql = "
    		SELECT (nr_nova_patrocinadora_estrutura + 1) AS nr_nova_patrocinadora_estrutura
    		  FROM projetos.nova_patrocinadora_estrutura
    		 WHERE dt_exclusao   IS NULL
    		   AND dt_desativado IS NULL
    		 ORDER BY nr_nova_patrocinadora_estrutura DESC
    		 LIMIT 1";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_nova_patrocinadora_estrutura)
    {
    	$qr_sql = "
    	  	SELECT cd_nova_patrocinadora_estrutura,
    	  	       nr_nova_patrocinadora_estrutura,
    	  	       ds_nova_patrocinadora_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       cd_usuario_responsavel,
    	  	       cd_usuario_substituto,
    	  	       nr_prazo,
    	  	       ds_observacao,
    	  	       TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       funcoes.get_usuario_nome(cd_usuario_desativado) AS ds_usuario_desativado
    	  	  FROM projetos.nova_patrocinadora_estrutura
    	  	 WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_nova_patrocinadora_estrutura = intval($this->db->get_new_id(
            'projetos.nova_patrocinadora_estrutura', 
            'cd_nova_patrocinadora_estrutura'
        ));

        $qr_sql = "
            INSERT INTO projetos.nova_patrocinadora_estrutura
                 (
            		cd_nova_patrocinadora_estrutura, 
            		nr_nova_patrocinadora_estrutura, 
            		ds_nova_patrocinadora_estrutura, 
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
                    ".intval($cd_nova_patrocinadora_estrutura).",
                    ".(trim($args['nr_nova_patrocinadora_estrutura']) != '' ? intval($args['nr_nova_patrocinadora_estrutura']) : "DEFAULT").",
                    ".(trim($args['ds_nova_patrocinadora_estrutura']) != '' ? str_escape($args['ds_nova_patrocinadora_estrutura']) : "DEFAULT").",
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
                INSERT INTO projetos.nova_patrocinadora_estrutura_dependencia(cd_nova_patrocinadora_estrutura, cd_nova_patrocinadora_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_nova_patrocinadora_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_estrutura_dependencia']).")) x;";
        }
       
        $this->db->query($qr_sql);

        return $cd_nova_patrocinadora_estrutura;
    }

    public function atualizar($cd_nova_patrocinadora_estrutura, $args = array())
    {
    	$qr_sql = "
            UPDATE projetos.nova_patrocinadora_estrutura
               SET nr_nova_patrocinadora_estrutura = ".(trim($args['nr_nova_patrocinadora_estrutura']) != '' ? intval($args['nr_nova_patrocinadora_estrutura']) : "DEFAULT").",
            	   ds_nova_patrocinadora_estrutura = ".(trim($args['ds_nova_patrocinadora_estrutura']) != '' ? str_escape($args['ds_nova_patrocinadora_estrutura']) : "DEFAULT").",
            	   ds_atividade                  = ".(trim($args['ds_atividade']) != '' ? str_escape($args['ds_atividade']) : "DEFAULT").",
            	   cd_gerencia                   = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
            	   cd_usuario_responsavel        = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
            	   cd_usuario_substituto         = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
            	   nr_prazo                      = ".(trim($args['nr_prazo']) != '' ? intval($args['nr_prazo']) : "DEFAULT").",
            	   ds_observacao                 = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
                   dt_alteracao                  = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

        if(count($args['atividade_estrutura_dependencia']) > 0)
        {
             $qr_sql .= "
                UPDATE projetos.nova_patrocinadora_estrutura_dependencia
                   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
                       dt_exclusao                              = CURRENT_TIMESTAMP
                 WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura)."
                   AND dt_exclusao IS NULL
                   AND cd_nova_patrocinadora_estrutura_dep NOT IN (".implode(",", $args['atividade_estrutura_dependencia']).");
       
                INSERT INTO projetos.nova_patrocinadora_estrutura_dependencia(cd_nova_patrocinadora_estrutura, cd_nova_patrocinadora_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_nova_patrocinadora_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_estrutura_dependencia']).")) x
                 WHERE x.column1 NOT IN (SELECT a.cd_nova_patrocinadora_estrutura_dep
                                           FROM projetos.nova_patrocinadora_estrutura_dependencia a
                                          WHERE a.cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.nova_patrocinadora_estrutura_dependencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura)."
                   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
    }

    public function ativar($cd_nova_patrocinadora_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_estrutura
               SET cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP,
                   cd_usuario_desativado = NULL,
                   dt_desativado         = NULL
             WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function desativar($cd_nova_patrocinadora_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_estrutura
               SET cd_usuario_desativado = ".intval($cd_usuario).",
                   dt_desativado         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_estrutura = ".intval($cd_nova_patrocinadora_estrutura).";";

        $this->db->query($qr_sql);
    }
    public function get_planos()
    {
        $qr_sql = "
            SELECT cd_plano AS value,
                   descricao AS text
              FROM public.planos
             --WHERE cd_plano IN (2, 22, 6, 21, 23, 10)
             ORDER BY descricao ASC;";

        return $this->db->query($qr_sql)->result_array();     
    }

    public function get_patrocinadora()
    {
        $qr_sql = "
            SELECT cd_nova_patrocinadora AS value,
                   ds_nome_patrocinadora AS text
              FROM projetos.nova_patrocinadora
             WHERE dt_exclusao IS NULL
             ORDER BY ds_nome_patrocinadora ASC;";

        return $this->db->query($qr_sql)->result_array();     
    }

    public function patrocinadora_listar($args = array())
    {
        $qr_sql = "
            SELECT np.ds_nome_patrocinadora,
                   np.cd_nova_patrocinadora,
                   TO_CHAR(np.dt_limite_aprovacao,'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   TO_CHAR(np.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   p.descricao,
				   np.cd_plano,
				   np.cd_empresa,
				   pa.sigla AS ds_empresa,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade npa
                     WHERE npa.cd_nova_patrocinadora = np.cd_nova_patrocinadora
                       AND npa.dt_exclusao         IS NULL) AS qt_atividade,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade npae
                     WHERE npae.cd_nova_patrocinadora = np.cd_nova_patrocinadora
                       AND npae.dt_exclusao         IS NULL
                       AND npae.dt_encerramento     IS NOT NULL) AS qt_atividades_encerradas,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade npae
                     WHERE npae.cd_nova_patrocinadora = np.cd_nova_patrocinadora
                       AND npae.dt_exclusao         IS NULL
                       AND npae.dt_encerramento     IS NULL) AS qt_atividades_abertas
              FROM projetos.nova_patrocinadora np
              JOIN public.planos p
                ON np.cd_plano = p.cd_plano
			  LEFT JOIN public.patrocinadoras pa
			    ON pa.cd_empresa = np.cd_empresa
             WHERE np.dt_exclusao IS NULL
               ".(!gerencia_in(array('GP')) ? "AND np.dt_inicio IS NOT NULL" : "")."
               ".(trim($args['cd_plano']) != '' ? "AND np.cd_plano = ".intval($args['cd_plano']) : "")."
               ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', np.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function patrocinadora_carrega($cd_nova_patrocinadora)
    {
        $qr_sql = "
            SELECT np.ds_nome_patrocinadora,
                   np.cd_nova_patrocinadora,
                   p.descricao,
                   TO_CHAR(np.dt_limite_aprovacao,'DD/MM/YYYY') AS dt_limite_aprovacao,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   np.cd_plano,
                   np.cd_empresa,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade npa
                     WHERE npa.cd_nova_patrocinadora = np.cd_nova_patrocinadora
                       AND npa.dt_exclusao         IS NULL) AS qt_atividade,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade npae
                     WHERE npae.cd_nova_patrocinadora = np.cd_nova_patrocinadora
                       AND npae.dt_exclusao         IS NULL
                       AND npae.dt_encerramento     IS NOT NULL) AS qt_atividades_encerradas
              FROM projetos.nova_patrocinadora np
              JOIN public.planos p
                ON np.cd_plano = p.cd_plano 
             WHERE np.cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora)."
               AND np.dt_exclusao         IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function patrocinadora_salvar($args = array())
    {
        $cd_nova_patrocinadora = intval($this->db->get_new_id('projetos.nova_patrocinadora','cd_nova_patrocinadora'));

        $qr_sql = "
            INSERT INTO projetos.nova_patrocinadora
                 (
                    cd_nova_patrocinadora,
                    ds_nome_patrocinadora,
                    dt_limite_aprovacao,
                    cd_plano,
                    cd_empresa,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_nova_patrocinadora).",
                    ".(trim($args['ds_nome_patrocinadora']) != '' ? str_escape($args['ds_nome_patrocinadora']) : "DEFAULT").",
                    ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."','DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(intval($args['cd_plano']) > 0 ? intval($args['cd_plano']) : "DEFAULT").",
                    ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_nova_patrocinadora;
    }

    public function patrocinadora_atualizar($cd_nova_patrocinadora, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora
               SET ds_nome_patrocinadora  = ".(trim($args['ds_nome_patrocinadora']) != '' ? str_escape($args['ds_nome_patrocinadora']) : "DEFAULT").",
                   cd_plano             = ".(intval($args['cd_plano']) > 0 ? intval($args['cd_plano']) : "DEFAULT").",
                   cd_empresa           = ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                   dt_limite_aprovacao  = ".(trim($args['dt_limite_aprovacao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite_aprovacao'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora).";";

        $this->db->query($qr_sql);
    }

    public function cria_atividade_patrocinadora($cd_nova_patrocinadora, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   cd_usuario_inicio    = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   dt_inicio            = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora).";";

        $qr_sql .= "
            INSERT INTO projetos.nova_patrocinadora_atividade
                 (       
                    cd_nova_patrocinadora,
                    cd_nova_patrocinadora_estrutura,
                    nr_nova_patrocinadora_atividade,
                    ds_nova_patrocinadora_atividade,
                    ds_atividade,
                    cd_gerencia,
                    cd_usuario_responsavel,
                    cd_usuario_substituto,
                    nr_prazo,
                    ds_observacao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            SELECT ".intval($cd_nova_patrocinadora).",
                   cd_nova_patrocinadora_estrutura,  
                   nr_nova_patrocinadora_estrutura,
                   ds_nova_patrocinadora_estrutura,
                   ds_atividade,
                   cd_gerencia,
                   cd_usuario_responsavel,
                   cd_usuario_substituto,
                   nr_prazo,
                   ds_observacao,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM projetos.nova_patrocinadora_estrutura
             WHERE dt_exclusao   IS NULL
               AND dt_desativado IS NULL;";

        $this->db->query($qr_sql);

        $qr_sql = "
            INSERT INTO projetos.nova_patrocinadora_atividade_dependencia
                 (
                    cd_nova_patrocinadora_atividade, 
                    cd_nova_patrocinadora, 
                    cd_nova_patrocinadora_atividade_dep, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )

            SELECT (SELECT a2.cd_nova_patrocinadora_atividade
                      FROM projetos.nova_patrocinadora_atividade a2
                     WHERE a2.cd_nova_patrocinadora_estrutura = ed.cd_nova_patrocinadora_estrutura_dep
                       AND a2.cd_nova_patrocinadora           = ".intval($cd_nova_patrocinadora)."
                     LIMIT 1),
                   ".intval($cd_nova_patrocinadora).",
                   a.cd_nova_patrocinadora_atividade,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM projetos.nova_patrocinadora_estrutura_dependencia ed
              JOIN projetos.nova_patrocinadora_atividade a
                ON a.cd_nova_patrocinadora_estrutura = ed.cd_nova_patrocinadora_estrutura
             WHERE ed.dt_exclusao                  IS NULL 
               AND a.cd_nova_patrocinadora           = ".intval($cd_nova_patrocinadora).";";

        $this->db->query($qr_sql);
    }

    public function get_atividade_inicio($cd_nova_patrocinadora)
    {
        $qr_sql = "
            SELECT npa.cd_nova_patrocinadora_atividade,
                   funcoes.get_usuario(npa.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
                   funcoes.get_usuario(npa.cd_usuario_substituto) || '@eletroceee.com.br' AS ds_email_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, npa.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   npa.ds_nova_patrocinadora_atividade,
                   npa.ds_atividade
              FROM projetos.nova_patrocinadora_atividade npa
             WHERE npa.cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora)."
               AND npa.dt_exclusao         IS NULL
               AND (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade_dependencia npaep
                     WHERE npaep.dt_exclusao                       IS NULL    
                       AND npaep.cd_nova_patrocinadora               = npa.cd_nova_patrocinadora
                       AND npaep.cd_nova_patrocinadora_atividade_dep = npa.cd_nova_patrocinadora_atividade) = 0;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function iniciar_atividade($cd_nova_patrocinadora_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_atividade
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_envio_responsavel = CURRENT_TIMESTAMP,
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE dt_exclusao                    IS NULL
               AND cd_nova_patrocinadora_atividade  = ".intval($cd_nova_patrocinadora_atividade).";";

        $this->db->query($qr_sql);
    }

    public function listar_atividade($cd_nova_patrocinadora)
    {
        $qr_sql = "
            SELECT npa.cd_nova_patrocinadora_estrutura,
                   npa.cd_nova_patrocinadora_atividade,
                   npa.nr_nova_patrocinadora_atividade,
                   npa.ds_nova_patrocinadora_atividade,
                   npa.ds_atividade,
                   npa.cd_gerencia,
                   npa.dt_encerramento,
                   funcoes.get_usuario_nome(npa.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(npa.cd_usuario_substituto) AS ds_usuario_substituto,
                   funcoes.get_usuario_nome(npa.cd_usuario_encerramento) AS ds_usuario_encerramento,
                   TO_CHAR(npa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel_ini,
                   TO_CHAR(npa.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento_prazo,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   npa.nr_prazo,
                   npa.ds_observacao,
                   npa.dt_envio_responsavel,
                   (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade_dependencia npaep
                     WHERE npa.cd_nova_patrocinadora_estrutura = npaep.cd_nova_patrocinadora_atividade
                       AND npa.cd_nova_patrocinadora           = npaep.cd_nova_patrocinadora
                       AND npaep.dt_exclusao                 IS NULL
                       AND npa.dt_encerramento               IS NULL) AS qt_dependentes
              FROM projetos.nova_patrocinadora_atividade npa
              JOIN projetos.nova_patrocinadora np
                ON npa.cd_nova_patrocinadora = np.cd_nova_patrocinadora
             WHERE npa.dt_exclusao IS NULL
               AND npa.cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_dependente($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade)
    {
        $qr_sql = "
            SELECT npep.cd_nova_patrocinadora_atividade_dependencia,
                   npep.cd_nova_patrocinadora_atividade,  
                   npe.nr_nova_patrocinadora_atividade || ' - ' || npe.ds_nova_patrocinadora_atividade AS ds_atividades_dependentes,
                   npe.dt_encerramento
              FROM projetos.nova_patrocinadora_atividade_dependencia npep
              JOIN projetos.nova_patrocinadora_atividade npe
                ON npe.cd_nova_patrocinadora_atividade = npep.cd_nova_patrocinadora_atividade
               AND npe.cd_nova_patrocinadora           = npep.cd_nova_patrocinadora  
             WHERE npep.dt_exclusao IS NULL 
               AND npep.cd_nova_patrocinadora               = ".intval($cd_nova_patrocinadora)."
               AND npep.cd_nova_patrocinadora_atividade_dep = ".intval($cd_nova_patrocinadora_atividade)."
             ORDER BY npe.nr_nova_patrocinadora_atividade  ASC;"; 

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_minhas($cd_usuario, $args = array())
    {
        $qr_sql = "
            SELECT npa.cd_nova_patrocinadora_estrutura,
                   p.descricao,
                   npa.cd_nova_patrocinadora_atividade,
                   npa.nr_nova_patrocinadora_atividade,
                   npa.ds_nova_patrocinadora_atividade,
                   npa.cd_nova_patrocinadora,
                   npa.ds_atividade,
                   np.ds_nome_patrocinadora,
                   TO_CHAR(npa.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   TO_CHAR(npa.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento_prazo,
                   TO_CHAR(np.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   funcoes.get_usuario_nome(npa.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(npa.cd_usuario_substituto) AS ds_usuario_substituto,
                   funcoes.get_usuario_nome(npa.cd_usuario_encerramento) AS ds_usuario_encerramento,
                   TO_CHAR(funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(npa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_inpcio,
                   (CASE WHEN npa.dt_encerramento IS NOT NULL THEN 'success'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) < CURRENT_DATE THEN 'important'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) = CURRENT_DATE THEN 'warnpng'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) > CURRENT_DATE THEN 'info'
                         ELSE ''
                   END) AS ds_class_prazo
              FROM projetos.nova_patrocinadora_atividade npa
              JOIN projetos.nova_patrocinadora np
                ON npa.cd_nova_patrocinadora = np.cd_nova_patrocinadora
              JOIN public.planos p 
                ON np.cd_plano = p.cd_plano
             WHERE npa.dt_exclusao IS NULL
               AND npa.dt_envio_responsavel IS NOT NULL
               AND 
                 (
                    npa.cd_usuario_responsavel = ".intval($cd_usuario)."
                    OR 
                    npa.cd_usuario_substituto =  ".intval($cd_usuario)."
                 )
              ".(trim($args['fl_encerramento']) == 'S' ? "AND npa.dt_encerramento IS NOT NULL" : "")."
              ".(trim($args['fl_encerramento']) == 'N' ? "AND npa.dt_encerramento IS NULL": "")."
              ".(((trim($args['dt_prazo_ini']) != '') AND (trim($args['dt_prazo_fim']) != '')) ? " AND DATE_TRUNC('day', funcoes.dia_util('DEPOIS',date(npa.dt_envio_responsavel), npa.nr_prazo)) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')" : "")."
              ".(intval($args['cd_nova_patrocinadora']) != '' ? "AND np.cd_nova_patrocinadora = ".intval($args['cd_nova_patrocinadora'])."" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_atividade($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade)
    {
        $qr_sql = "
            SELECT npa.cd_nova_patrocinadora_estrutura,
                   npa.cd_nova_patrocinadora_atividade,
                   npa.nr_nova_patrocinadora_atividade,
                   npa.ds_nova_patrocinadora_atividade,
                   TO_CHAR(npa.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento_prazo,
                   npa.cd_nova_patrocinadora,
                   npa.ds_atividade,
                   npa.dt_encerramento,
                   funcoes.get_usuario_nome(npa.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(npa.cd_usuario_substituto) AS ds_usuario_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(npa.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_inpcio,
                   (CASE WHEN npa.dt_encerramento IS NOT NULL THEN 'success'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) < CURRENT_DATE THEN 'important'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) = CURRENT_DATE THEN 'warnpng'
                         WHEN funcoes.dia_util('DEPOIS', npa.dt_envio_responsavel::date, npa.nr_prazo) > CURRENT_DATE THEN 'info'
                         ELSE ''
                   END) AS ds_class_prazo
              FROM projetos.nova_patrocinadora_atividade npa
              JOIN projetos.nova_patrocinadora np
                ON npa.cd_nova_patrocinadora = np.cd_nova_patrocinadora
             WHERE npa.dt_exclusao IS NULL
               AND npa.dt_envio_responsavel IS NOT NULL
               AND npa.cd_nova_patrocinadora_atividade = ".intval($cd_nova_patrocinadora_atividade)." 
               AND npa.cd_nova_patrocinadora           = ".intval($cd_nova_patrocinadora).";";

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

    public function listar_acompanhamento($cd_nova_patrocinadora_atividade)
    {
        $qr_sql = "
            SELECT npaa.cd_nova_patrocinadora_atividade_acompanhamento,
                   npaa.cd_nova_patrocinadora_atividade,
                   npaa.ds_acompanhamento,
                   npaa.cd_atividade,
                   funcoes.get_usuario_nome(npaa.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(npaa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   (SELECT a.area
                      FROM projetos.atividades a
                     WHERE npaa.cd_atividade = a.numero) AS cd_gerencia
              FROM projetos.nova_patrocinadora_atividade_acompanhamento npaa
             WHERE npaa.dt_exclusao IS NULL
               AND npaa.cd_nova_patrocinadora_atividade = ".intval($cd_nova_patrocinadora_atividade).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_nova_patrocinadora_atividade_acompanhamento,
                   ds_acompanhamento,
                   cd_atividade
              FROM projetos.nova_patrocinadora_atividade_acompanhamento
             WHERE dt_exclusao IS NULL
               AND cd_nova_patrocinadora_atividade_acompanhamento = ".intval($cd_nova_patrocinadora_atividade_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.nova_patrocinadora_atividade_acompanhamento
                 (
                    cd_nova_patrocinadora_atividade,
                    ds_acompanhamento,
                    cd_atividade,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".(trim($args['cd_nova_patrocinadora_atividade']) != '' ? intval($args['cd_nova_patrocinadora_atividade']) : "DEFAULT").",
                    ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                    ".(intval($args['cd_atividade']) > 0 ? intval($args['cd_atividade']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";
            
        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_atividade_acompanhamento
               SET ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_atividade         = ".(intval($args['cd_atividade']) > 0 ? intval($args['cd_atividade']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_atividade_acompanhamento = ".intval($cd_nova_patrocinadora_atividade_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_atividade_acompanhamento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_atividade_acompanhamento = ".intval($cd_nova_patrocinadora_atividade_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function encerrar_atividade($cd_nova_patrocinadora_atividade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora_atividade
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora_atividade = ".intval($cd_nova_patrocinadora_atividade).";";

        $this->db->query($qr_sql);
    }

    public function get_atividade_dependente_inicio($cd_nova_patrocinadora, $cd_nova_patrocinadora_estrutura)
    {
        $qr_sql = "     
            SELECT npa.cd_nova_patrocinadora_atividade,
                   funcoes.get_usuario(npa.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
                   funcoes.get_usuario(npa.cd_usuario_substituto) || '@eletroceee.com.br' AS ds_email_substituto,
                   TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, npa.nr_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   npa.ds_nova_patrocinadora_atividade,
                   npa.ds_atividade
              FROM projetos.nova_patrocinadora_atividade npa
              JOIN projetos.nova_patrocinadora_atividade_dependencia npad
                ON npad.cd_nova_patrocinadora_atividade_dep = npa.cd_nova_patrocinadora_atividade
             WHERE npa.dt_exclusao                    IS NULL
               AND npa.dt_envio_responsavel           IS NULL
               AND npa.dt_encerramento                IS NULL
               AND npa.cd_nova_patrocinadora            = ".intval($cd_nova_patrocinadora)."
               AND npad.cd_nova_patrocinadora_atividade = ".intval($cd_nova_patrocinadora_estrutura)."
               AND (SELECT COUNT(*)
                      FROM projetos.nova_patrocinadora_atividade_dependencia npad2
                      JOIN projetos.nova_patrocinadora_atividade npa2
                        ON npa2.cd_nova_patrocinadora_atividade = npad2.cd_nova_patrocinadora_atividade
                     WHERE npad2.cd_nova_patrocinadora               = npa.cd_nova_patrocinadora  
                       AND npad2.cd_nova_patrocinadora_atividade_dep = npa.cd_nova_patrocinadora_atividade
                       AND npa2.dt_encerramento                    IS NULL) = 0;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function encerrar_patrocinadora($cd_nova_patrocinadora)
    {
        $qr_sql = "
            UPDATE projetos.nova_patrocinadora
               SET dt_encerramento = CURRENT_TIMESTAMP
             WHERE cd_nova_patrocinadora = ".intval($cd_nova_patrocinadora).";";

        $this->db->query($qr_sql);
    }
}

