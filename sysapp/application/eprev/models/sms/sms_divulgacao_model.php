<?php
class Sms_divulgacao_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function carrega($cd_sms_divulgacao)
    {
    	$qr_sql = "
    		SELECT cd_sms_divulgacao,
    			   ds_assunto,
				   ds_texto,
				   ds_url_link,
				   ds_avulso,
				   arquivo,
				   arquivo_nome
			  FROM sms.sms_divulgacao
			 WHERE dt_exclusao IS NULL
			   AND cd_sms_divulgacao = ".intval($cd_sms_divulgacao).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function listar_participante($cd_sms_divulgacao)
    {
    	$qr_sql = "
    		SELECT sdp.cd_sms_divulgacao_participante,
    			   sdp.cd_empresa||'/'||sdp.cd_registro_empregado||'/'||sdp.seq_dependencia AS nr_re_participante,
    			   p.nome AS ds_nome,
    			   funcoes.get_participante_celular(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS ds_celular,
    			   (CASE WHEN p.nome IS NOT NULL AND char_length(funcoes.get_participante_celular(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)) = 11 
                         THEN 'OK'
                         WHEN p.nome IS NOT NULL AND p.celular IS NULL 
                         THEN 'SEM CELULAR'
                         WHEN p.nome IS NOT NULL 
			             THEN 'CELULAR INCORRETO'
			             ELSE 'PARTICIPANTE NÃO ENCONTRADO'
			       END) AS ds_status,
				   (CASE WHEN p.nome IS NOT NULL AND char_length(funcoes.get_participante_celular(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)) = 11 
                         THEN 'label label-success'
                         WHEN p.nome IS NOT NULL AND p.celular IS NULL 
                         THEN 'label label-inverse'
                         WHEN p.nome IS NOT NULL 
			             THEN 'label label-warning'
			             ELSE 'label label-info'
			       END) AS ds_status_class
    		  FROM sms.sms_divulgacao_participante sdp
    		  LEFT JOIN public.participantes p
    		    ON p.cd_empresa 		   = sdp.cd_empresa
    		   AND p.cd_registro_empregado = sdp.cd_registro_empregado
    		   AND p.seq_dependencia 	   = sdp.seq_dependencia
    		 WHERE sdp.dt_exclusao IS NULL
    		   AND sdp.cd_sms_divulgacao = ".intval($cd_sms_divulgacao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($args = array())
    {
    	$cd_sms_divulgacao = intval($this->db->get_new_id('sms.sms_divulgacao', 'cd_sms_divulgacao'));

    	$qr_sql = "
    		INSERT INTO sms.sms_divulgacao
    			(
					cd_sms_divulgacao,
					ds_assunto,
					ds_url_link,
					ds_texto,
					ds_avulso,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao,
					cd_usuario_alteracao
    			)
    		VALUES
    			(
    				".intval($cd_sms_divulgacao).",
    				".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
    				".(trim($args['ds_url_link']) != '' ? str_escape($args['ds_url_link']) : "DEFAULT").",
    				".(trim($args['ds_texto']) != '' ? str_escape($args['ds_texto']) : "DEFAULT").",
    				".(trim($args['ds_avulso']) != '' ? str_escape($args['ds_avulso']) : "DEFAULT").",
    				".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
    				".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    			);";

    	$this->db->query($qr_sql);
    
    	return $cd_sms_divulgacao;
    }

    public function atualizar($cd_sms_divulgacao, $args = array())
    {
    	$qr_sql = "
    		UPDATE sms.sms_divulgacao
    		   SET ds_assunto 		 	= ".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
    		       ds_url_link 		 	= ".(trim($args['ds_url_link']) != '' ? str_escape($args['ds_url_link']) : "DEFAULT").",
    		       ds_texto 		 	= ".(trim($args['ds_texto']) != '' ? str_escape($args['ds_texto']) : "DEFAULT").",
    		       ds_avulso 		 	= ".(trim($args['ds_avulso']) != '' ? str_escape($args['ds_avulso']) : "DEFAULT").",
				   arquivo 			 	= ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
				   arquivo_nome 		= ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."    		 
			 WHERE cd_sms_divulgacao = ".intval($cd_sms_divulgacao).";";

    	$this->db->query($qr_sql);
    }

    public function limpar_tabela($cd_sms_divulgacao, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE sms.sms_divulgacao_participante
    		   SET dt_exclusao 		   = CURRENT_TIMESTAMP,
    		       cd_usuario_exclusao = ".intval($cd_usuario)."
    		 WHERE cd_sms_divulgacao = ".intval($cd_sms_divulgacao).";";

    	$this->db->query($qr_sql);
    }


    public function salvar_participante($args = array())
    {
    	$qr_sql = "
    		INSERT INTO sms.sms_divulgacao_participante
    			(
    				cd_sms_divulgacao,
					cd_empresa,
					cd_registro_empregado,
					seq_dependencia,
					cd_usuario_inclusao,
					cd_usuario_alteracao
    			)
    		VALUES
    			(
    				".(intval($args['cd_sms_divulgacao']) > 0 ? intval($args['cd_sms_divulgacao']) : "DEFAULT").",
    				".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
    				".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
    				".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    			);";

    	$this->db->query($qr_sql);
    }

    public function publico_listar($cd_sms_divulgacao)
    {
    	$qr_sql = "
    		SELECT cd_sms_divulgacao_grupo,
    			   nr_participantes,
    			   nr_participantes_contato
    		  FROM sms.sms_divulgacao_grupo
    		 WHERE dt_exclusao IS NULL
    		 AND cd_sms_divulgacao = ".intval($cd_sms_divulgacao).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function publico_carrega($cd_sms_divulgacao_grupo)
    {
    	$qr_sql = "
    		SELECT cd_sms_divulgacao_grupo,
    			   cd_sms_divulgacao
    		  FROM sms.sms_divulgacao_grupo
    		 WHERE dt_exclusao IS NULL
    		   AND cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_empresa($cd_sms_divulgacao_grupo)
    {
    	$qr_sql = "
    		SELECT smge.cd_empresa,
    		       p.sigla 
    		  FROM sms.sms_divulgacao_grupo_empresa smge
    		  JOIN public.patrocinadoras p
    		    ON p.cd_empresa = smge.cd_empresa
    		 WHERE smge.dt_exclusao IS NULL
    		   AND smge.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_plano($cd_sms_divulgacao_grupo)
    {
    	$qr_sql = "
    		SELECT smgp.cd_plano,
    			   p.descricao
    		  FROM sms.sms_divulgacao_grupo_plano smgp
    		  JOIN public.planos p
    		    ON p.cd_plano = smgp.cd_plano
    		 WHERE smgp.dt_exclusao IS NULL
    		   AND smgp.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_tipo($cd_sms_divulgacao_grupo)
    {
    	$qr_sql = "
    		SELECT ds_tipo,
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
    		  FROM sms.sms_divulgacao_grupo_tipo
    		 WHERE dt_exclusao IS NULL
    		   AND cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_cidade($cd_sms_divulgacao_grupo)
    {
    	$qr_sql = "
    		SELECT cd_sms_divulgacao_grupo_cidade,
    			   ds_cidade,
    			   nr_participantes_cidade,
    			   nr_participantes_cidade_contato
    		  FROM sms.sms_divulgacao_grupo_cidade
    		 WHERE dt_exclusao IS NULL
    		   AND cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_participantes($empresa, $plano, $tipo, $cidade)
    {
    	$qr_sql = "
    		SELECT COUNT(*)
    		  FROM public.participantes 
    		 WHERE 1 = 1
    		   ".(count($empresa) > 0 ? "AND cd_empresa IN (".implode(",", $empresa).")" : "")."
    		   ".(count($plano) > 0 ? "AND cd_plano IN (".implode(",", $plano).")" : "")."
    		   ".(count($tipo) > 0 ? "AND projetos.participante_tipo(cd_empresa, cd_registro_empregado, seq_dependencia) IN ('".implode("'),('", $tipo)."')" : "")."
    		   ".(count($cidade) > 0 ? "
    		   		AND UPPER(COALESCE(cidade, '')) IN 
				   		(
				   			UPPER(funcoes.remove_acento(TRIM('".implode("'))),UPPER(funcoes.remove_acento(TRIM('", $cidade)."')))
				   		)" : "")."
    		   ;";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_participantes_contato($empresa, $plano, $tipo, $cidade)
    {
    	$qr_sql = "
    		SELECT COUNT(*)
    		  FROM public.participantes 
    		 WHERE 1 = 1
    		   ".(count($empresa) > 0 ? "AND cd_empresa IN (".implode(",", $empresa).")" : "")."
    		   ".(count($plano) > 0 ? "AND cd_plano IN (".implode(",", $plano).")" : "")."
    		   ".(count($tipo) > 0 ? "AND projetos.participante_tipo(cd_empresa, cd_registro_empregado, seq_dependencia) IN ('".implode("'),('", $tipo)."')" : "")."
    		   ".(count($cidade) > 0 ? "
    		   		AND UPPER(COALESCE(cidade, '')) IN 
				   		(
				   			UPPER(funcoes.remove_acento(TRIM('".implode("'))),UPPER(funcoes.remove_acento(TRIM('", $cidade)."')))
				   		)" : "")."
    		   AND char_length(funcoes.get_participante_celular(cd_empresa, cd_registro_empregado, seq_dependencia)) = 11
    		   ;";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function get_empresa()
    {
    	$qr_sql = "
    		SELECT cd_empresa AS value,
    			   sigla AS text
    		  FROM public.patrocinadoras
    		 WHERE cd_empresa NOT IN (4, 5)
    		 ORDER BY text ASC;";

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

    public function publico_salvar($args = array())
    {
    	$cd_sms_divulgacao_grupo = intval($this->db->get_new_id('sms.sms_divulgacao_grupo', 'cd_sms_divulgacao_grupo'));

    	$qr_sql = "
    		INSERT INTO sms.sms_divulgacao_grupo
    			(
    				cd_sms_divulgacao_grupo,
    				cd_sms_divulgacao,
    				nr_participantes,
    				nr_participantes_contato,
    				cd_usuario_inclusao,
    				cd_usuario_alteracao
    			)
    		VALUES
    			(
    				".intval($cd_sms_divulgacao_grupo).",
    				".(intval($args['cd_sms_divulgacao']) > 0 ? intval($args['cd_sms_divulgacao']) : "DEFAULT").",
    				".(intval($args['nr_participantes']) > 0 ? intval($args['nr_participantes']) : "DEFAULT").",
    				".(intval($args['nr_participantes_contato']) > 0 ? intval($args['nr_participantes_contato']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    			);";

    	if(count($args['cd_empresa']) > 0)
    	{
    		$qr_sql .= "
                INSERT INTO sms.sms_divulgacao_grupo_empresa
                	(	
                		cd_sms_divulgacao_grupo, 
                		cd_empresa, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1::integer, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['cd_empresa'])."')) x;";
    	}

    	if(count($args['cd_plano']) > 0)
    	{
    		$qr_sql .= "
                INSERT INTO sms.sms_divulgacao_grupo_plano
                	(	
                		cd_sms_divulgacao_grupo, 
                		cd_plano, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1::integer, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['cd_plano'])."')) x;";
    	}

    	if(count($args['ds_tipo']) > 0)
    	{
    		$qr_sql .= "
                INSERT INTO sms.sms_divulgacao_grupo_tipo
                	(	
                		cd_sms_divulgacao_grupo, 
                		ds_tipo, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['ds_tipo'])."')) x;";
    	}

    	if(count($args['ds_cidade']) > 0)
    	{
    		$qr_sql .= "
                INSERT INTO sms.sms_divulgacao_grupo_cidade
                	(	
                		cd_sms_divulgacao_grupo, 
                		ds_cidade, 
                		nr_participantes_cidade,
                		nr_participantes_cidade_contato,
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   UPPER(funcoes.remove_acento(TRIM(x.c1))), 
                	   x.c2,
                	   x.c3,
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cidades']).")) x (c1, c2, c3);";
    	}

    	$this->db->query($qr_sql);
    }

    public function publico_atualizar($cd_sms_divulgacao_grupo, $args = array())
    {
    	$qr_sql = "
    		UPDATE sms.sms_divulgacao_grupo
    		   SET nr_participantes         = ".(intval($args['nr_participantes']) > 0 ? intval($args['nr_participantes']) : "DEFAULT").",
    		       nr_participantes_contato = ".(intval($args['nr_participantes_contato']) > 0 ? intval($args['nr_participantes_contato']) : "DEFAULT").",
				   cd_usuario_inclusao      = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    			   cd_usuario_alteracao     = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    		 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";

        if(count($args['cd_empresa']) > 0)
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_empresa
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND cd_empresa NOT IN (".implode(",", $args['cd_empresa']).");
       
                INSERT INTO sms.sms_divulgacao_grupo_empresa
                	(
                		cd_sms_divulgacao_grupo, 
                		cd_empresa, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1::integer, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cd_empresa']).")) x
                 WHERE x.column1::integer NOT IN (SELECT a.cd_empresa
                                           FROM sms.sms_divulgacao_grupo_empresa a
                                          WHERE a.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_empresa
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['cd_plano']) > 0)
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_plano
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND cd_plano NOT IN ('".implode("','", $args['cd_plano'])."');
       
                INSERT INTO sms.sms_divulgacao_grupo_plano
                	(
                		cd_sms_divulgacao_grupo, 
                		cd_plano, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1::integer, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['cd_plano'])."')) x
                 WHERE x.column1::integer NOT IN (SELECT a.cd_plano
                                           FROM sms.sms_divulgacao_grupo_plano a
                                          WHERE a.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_plano
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['ds_tipo']) > 0)
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_tipo
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND ds_tipo NOT IN ('".implode("','", $args['ds_tipo'])."');
       
                INSERT INTO sms.sms_divulgacao_grupo_tipo
                	(
                		cd_sms_divulgacao_grupo, 
                		ds_tipo, 
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   x.column1, 
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['ds_tipo'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.ds_tipo
                                           FROM sms.sms_divulgacao_grupo_tipo a
                                          WHERE a.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_tipo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['ds_cidade']) > 0)
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_cidade
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL
                   AND ds_cidade NOT IN ('".implode("','", $args['ds_cidade'])."');
       
                INSERT INTO sms.sms_divulgacao_grupo_cidade
                	(
                		cd_sms_divulgacao_grupo, 
                		ds_cidade, 
                		nr_participantes_cidade,
                		nr_participantes_cidade_contato,
                		cd_usuario_inclusao, 
                		cd_usuario_alteracao
                	)
                SELECT ".intval($cd_sms_divulgacao_grupo).", 
                	   UPPER(funcoes.remove_acento(TRIM(x.c1))),
                	   x.c2,
                	   x.c3,
                	   ".intval($args['cd_usuario']).", 
                	   ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['cidades']).")) x (c1, c2, c3)
                 WHERE x.c1 NOT IN (SELECT a.ds_cidade
                                           FROM sms.sms_divulgacao_grupo_cidade a
                                          WHERE a.cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE sms.sms_divulgacao_grupo_cidade
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo)."
                   AND dt_exclusao IS NULL;";
        }

    	$this->db->query($qr_sql);
    }

    public function atualizar_participantes($cd_sms_divulgacao_grupo, $nr_participantes, $nr_participantes_contato, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE sms.sms_divulgacao_grupo
    		   SET nr_participantes     	= ".(intval($nr_participantes) > 0 ? intval($nr_participantes) : "DEFAULT").",
    		       nr_participantes_contato = ".(intval($nr_participantes_contato) > 0 ? intval($nr_participantes_contato) : "DEFAULT").",
				   dt_alteracao  			= CURRENT_TIMESTAMP	,
    			   cd_usuario_alteracao 	= ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT")."
    		 WHERE cd_sms_divulgacao_grupo = ".intval($cd_sms_divulgacao_grupo).";";
    }

    public function atualizar_participantes_cidade($cd_sms_divulgacao_grupo_cidade, $nr_participantes_cidade, $nr_participantes_cidade_contato, $cd_usuario)
    {
    	$qr_sql	= "
    		UPDATE sms.sms_divulgacao_grupo_cidade
    		   SET nr_participantes_cidade 		   = ".(trim($nr_participantes_cidade) != '' ? intval($nr_participantes_cidade) : "DEFAULT").",
    		       nr_participantes_cidade_contato = ".(trim($nr_participantes_cidade_contato) != '' ? intval($nr_participantes_cidade_contato) : "DEFAULT").",
    		   	   dt_alteracao 		   		   = CURRENT_TIMESTAMP,
    		   	   cd_usuario_alteracao    		   = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT")."
    		 WHERE cd_sms_divulgacao_grupo_cidade = ".intval($cd_sms_divulgacao_grupo_cidade).";";

    	$this->db->query($qr_sql);
    }
}
?>
