<?php
class Avaliacao_model extends Model
{
	public function listar($args = array())
    {
        $qr_sql = "
            SELECT a.cd_avaliacao,
                   a.nr_ano_avaliacao,
                   TO_CHAR(a.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(a.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario,
                   (SELECT COUNT(*)
			          FROM rh_avaliacao.avaliacao_usuario au
			         WHERE au.dt_exclusao  IS NULL
			           AND au.cd_avaliacao = a.cd_avaliacao) AS qt_avaliacao,
                   (SELECT COUNT(*)
                      FROM rh_avaliacao.avaliacao_usuario au
                     WHERE au.dt_exclusao  IS NULL
                       AND au.cd_avaliacao = a.cd_avaliacao
                       AND au.dt_encerramento IS NOT NULL) AS qt_avaliacao_encerrada,
                   (SELECT AVG(nr_resultado)
                      FROM rh_avaliacao.avaliacao_usuario au
                      JOIN rh_avaliacao.avaliacao_usuario_avaliacao aua
                        ON aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                      JOIN rh_avaliacao.avaliacao_usuario_avaliacao_resultado auar
                        ON auar.cd_avaliacao_usuario_avaliacao = aua.cd_avaliacao_usuario_avaliacao
                     WHERE au.dt_exclusao     IS NULL   
                       AND au.cd_avaliacao    = a.cd_avaliacao
                       AND au.dt_encerramento IS NOT NULL
                       AND aua.tp_avaliacao   = 'QUA') AS nr_media_resultado,
                   TO_CHAR(dt_envio_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
                   funcoes.get_usuario_nome(cd_usuario_envio_email) AS ds_usuario_envio_email
              FROM rh_avaliacao.avaliacao a
             WHERE a.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_avaliacao)
    {
        $qr_sql = "
            SELECT a.cd_avaliacao,
                   a.nr_ano_avaliacao,
                   TO_CHAR(a.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(a.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento,
                   (CASE WHEN CURRENT_DATE <= a.dt_encerramento THEN 1
                         ELSE 0
                    END) AS fl_permissao,
                   a.ds_instrucao_preenchimento,
                   TO_CHAR(dt_envio_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
                   funcoes.get_usuario_nome(a.cd_usuario_envio_email) AS ds_usuario_envio_email,
                   (SELECT COUNT(*)
                      FROM rh_avaliacao.avaliacao_usuario au
                     WHERE au.dt_exclusao  IS NULL
                       AND au.cd_avaliacao = a.cd_avaliacao) AS qt_avaliacao,
                   (SELECT COUNT(*)
                      FROM rh_avaliacao.avaliacao_usuario au
                     WHERE au.dt_exclusao     IS NULL
                       AND au.dt_encerramento IS NOT NULL
                       AND au.cd_avaliacao    = a.cd_avaliacao) AS qt_avaliacao_encerrada,
                   CASE WHEN a.dt_inicio::date <= CURRENT_DATE THEN 'S' ELSE 'N' END AS fl_inicio
              FROM rh_avaliacao.avaliacao a
             WHERE a.dt_exclusao IS NULL
               AND a.cd_avaliacao = ".intval($cd_avaliacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_envio_email($cd_avaliacao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao
               SET dt_envio_email         = CURRENT_TIMESTAMP,
                   cd_usuario_envio_email = ".intval($cd_usuario)."
             WHERE cd_avaliacao = ".intval($cd_avaliacao).";";

        $this->db->query($qr_sql);
    }

    public function salvar($args = array())
    {
    	$cd_avaliacao = intval($this->db->get_new_id('rh_avaliacao.avaliacao', 'cd_avaliacao'));

        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao
                (
                	cd_avaliacao,
                    nr_ano_avaliacao,
                    dt_inicio,
                    dt_encerramento,
                    ds_instrucao_preenchimento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                	".intval($cd_avaliacao).",
                    ".(trim($args['nr_ano_avaliacao']) != '' ? intval($args['nr_ano_avaliacao']) : "DEFAULT").",
                    ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['dt_encerramento']) != '' ? "TO_DATE('".trim($args['dt_encerramento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['ds_instrucao_preenchimento']) != '' ? str_escape($args['ds_instrucao_preenchimento']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);

        return $cd_avaliacao;
    }

    public function atualizar($cd_avaliacao, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao
               SET dt_inicio                  = ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   dt_encerramento            = ".(trim($args['dt_encerramento']) != '' ? "TO_DATE('".trim($args['dt_encerramento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ds_instrucao_preenchimento = ".(trim($args['ds_instrucao_preenchimento']) != '' ? str_escape($args['ds_instrucao_preenchimento']) : "DEFAULT").",
                   cd_usuario_alteracao       = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao               = CURRENT_TIMESTAMP
             WHERE cd_avaliacao = ".intval($cd_avaliacao).";";

        $this->db->query($qr_sql);
    }

    public function get_usuario_avaliacao($cd_avaliacao)
    {
    	$qr_sql = "
    		SELECT uc.codigo AS cd_usuario,
                   (CASE WHEN uc.tipo = 'G' OR uc.divisao = 'AI'
                         THEN funcoes.get_usuario_diretor(uc.codigo)
                         ELSE funcoes.get_gerente_gerencia(uc.divisao)
                   END) AS cd_avaliador
			  FROM projetos.usuarios_controledi uc 
			 WHERE uc.divisao NOT IN ('SNG', 'LM2') 
			   AND uc.tipo NOT IN ('X','T', 'E', 'D', 'P', 'A') 
			   AND (SELECT COUNT(*)
			          FROM rh_avaliacao.avaliacao_usuario au
			         WHERE au.dt_exclusao  IS NULL
			           AND au.cd_usuario   = uc.codigo
			           AND au.cd_avaliacao = ".intval($cd_avaliacao).") = 0
			 ORDER BY uc.nome;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_progresso_promocao($cd_usuario)
    {
        $qr_sql = "
            SELECT TO_CHAR(pp.dt_progressao_promocao, 'DD/MM/YYYY') AS dt_progressao_promocao,
                   cr.ds_cargo,
                   caa.cd_gerencia || ' - ' || cr.ds_cargo || (CASE WHEN aa.ds_area_atuacao IS NOT NULL THEN ' - ' || aa.ds_area_atuacao ELSE '' END) AS ds_cargo_area_atuacao,
                   TRIM(cr.ds_cargo || (CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END)) AS ds_classe,
                   ds_padrao,
                   cr.ds_conhecimento_generico,
                   caa.ds_conhecimento_especifico,
                   f.ds_formacao,
                   e.nome_escolaridade
              FROM rh_avaliacao.progressao_promocao pp
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pp.cd_usuario
              JOIN projetos.escolaridade e
                ON e.cd_escolaridade = uc.cd_escolaridade
              JOIN projetos.divisoes d
                ON d.codigo = uc.divisao  
              JOIN rh_avaliacao.cargo_area_atuacao caa
                ON caa.cd_cargo_area_atuacao = pp.cd_cargo_area_atuacao
              JOIN rh_avaliacao.cargo cr
                ON cr.cd_cargo = caa.cd_cargo
              JOIN rh_avaliacao.formacao f
                ON f.cd_formacao = cr.cd_formacao
              JOIN rh_avaliacao.classe cl
                ON cl.cd_classe = pp.cd_classe
              LEFT JOIN rh_avaliacao.area_atuacao aa
                ON aa.cd_area_atuacao = caa.cd_area_atuacao
              LEFT JOIN rh_avaliacao.classe_padrao cp
                ON cp.cd_classe_padrao = pp.cd_classe_padrao
             WHERE pp.dt_exclusao IS NULL
               AND pp.cd_usuario = ".intval($cd_usuario)."
             ORDER BY pp.dt_progressao_promocao DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_avaliacao_usuario($args = array())
    {
        $cd_avaliacao_usuario = intval($this->db->get_new_id('rh_avaliacao.avaliacao_usuario', 'cd_avaliacao_usuario'));

    	$qr_sql = "
    		INSERT INTO rh_avaliacao.avaliacao_usuario
    		     (
                    cd_avaliacao_usuario,
            		cd_avaliacao, 
            		ds_cargo, 
            		ds_cargo_area_atuacao, 
            		ds_classe, 
            		ds_padrao, 
                    cd_usuario, 
            		cd_usuario_avaliador, 
                    ds_conhecimento_generico,
                    ds_conhecimento_especifico,
                    ds_escolaridade_cargo,
                    ds_escolaridade_avaliado,
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
            	 )
    		VALUES 
    		     (
                    ".intval($cd_avaliacao_usuario).",
    		     	".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
    		     	".(trim($args['ds_cargo']) != '' ? str_escape($args['ds_cargo']) : "DEFAULT").",
    		     	".(trim($args['ds_cargo_area_atuacao']) != '' ? str_escape($args['ds_cargo_area_atuacao']) : "DEFAULT").",
    		     	".(trim($args['ds_classe']) != '' ? str_escape($args['ds_classe']) : "DEFAULT").",
    		     	".(trim($args['ds_padrao']) != '' ? str_escape($args['ds_padrao']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    		     	".(intval($args['cd_usuario_avaliador']) > 0 ? intval($args['cd_usuario_avaliador']) : "DEFAULT").",
                    ".(trim($args['ds_conhecimento_generico']) != '' ? str_escape($args['ds_conhecimento_generico']) : "DEFAULT").",
                    ".(trim($args['ds_conhecimento_especifico']) != '' ? str_escape($args['ds_conhecimento_especifico']) : "DEFAULT").",
                    ".(trim($args['ds_escolaridade_cargo']) != '' ? str_escape($args['ds_escolaridade_cargo']) : "DEFAULT").",
                    ".(trim($args['ds_escolaridade_avaliado']) != '' ? str_escape($args['ds_escolaridade_avaliado']) : "DEFAULT").",
    		     	".intval($args['cd_usuario_inclusao']).",
    		     	".intval($args['cd_usuario_inclusao'])."
    		     );";

    	$this->db->query($qr_sql);

        return $cd_avaliacao_usuario;
    }

    public function salvar_avaliacao_usuario_avaliacao($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_usuario_avaliacao
                 (
                    cd_avaliacao, 
                    cd_avaliacao_usuario, 
                    cd_usuario, 
                    tp_avaliacao,  
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(trim($args['tp_avaliacao']) != '' ? "'".trim($args['tp_avaliacao'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario_inclusao']).",
                    ".intval($args['cd_usuario_inclusao'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function carrega_anterior()
    {
    	$qr_sql = "
    		SELECT (nr_ano_avaliacao + 1) AS nr_ano_avaliacao,
                   ds_instrucao_preenchimento
              FROM rh_avaliacao.avaliacao
             WHERE dt_exclusao IS NULL
             ORDER BY dt_inclusao DESC;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_avaliacao_performance($cd_avaliacao, $cd_usuario)
    {
    	$qr_sql = "
    		INSERT INTO rh_avaliacao.avaliacao_peformance
    			 (
            		cd_avaliacao, 
            		ds_grupo, 
            		tp_grupo, 
            		ds_performance, 
            		tp_performance, 
            		ds_performance_descricao, 
            		nr_ponto,  
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao 
            	 )
            SELECT ".intval($cd_avaliacao).",
                   g.ds_grupo ||' - '|| g.ds_grupo_sigla,
			       g.ds_grupo_sigla,
			       p.ds_performance_sigla ||' - '|| p.ds_performance,
			       p.ds_performance_sigla,
			       p.ds_performance_descricao,
			       p.nr_ponto,
			       ".intval($cd_usuario).",
			       ".intval($cd_usuario)."
			  FROM rh_avaliacao.performance p
			  JOIN rh_avaliacao.grupo g
			    ON g.cd_grupo = p.cd_grupo
			   AND g.dt_exclusao IS NULL
			 WHERE p.dt_exclusao IS NULL
			 ORDER BY p.cd_grupo;";

		$this->db->query($qr_sql);
    }

    public function salvar_matriz_conceito($cd_avaliacao, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_matriz_conceito
                 (
                    cd_matriz_conceito, 
                    cd_avaliacao, 
                    tp_grupo, 
                    nr_matriz_conceito, 
                    nr_nota_min, 
                    nr_nota_max, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
            SELECT rc.cd_matriz_conceito,
                   ".intval($cd_avaliacao).",
                   g.ds_grupo_sigla,
                   rc.nr_matriz_conceito, 
                   rc.nr_nota_min, 
                   rc.nr_nota_max,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM rh_avaliacao.matriz_conceito rc
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = rc.cd_grupo
               AND g.dt_exclusao IS NULL
             WHERE rc.dt_exclusao IS NULL;";

        $this->db->query($qr_sql);
    }

    public function salvar_matriz_acao($cd_avaliacao, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_matriz_acao
                 (
                    cd_matriz_acao, 
                    cd_avaliacao, 
                    ds_matriz_acao, 
                    fl_progressao, 
                    fl_promocao, 
                    cor_fundo, 
                    cor_texto, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            SELECT cd_matriz_acao,
                   ".intval($cd_avaliacao).", 
                   ds_matriz_acao,
                   fl_progressao, 
                   fl_promocao, 
                   cor_fundo, 
                   cor_texto,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM rh_avaliacao.matriz_acao
             WHERE dt_exclusao IS NULL;";

        $this->db->query($qr_sql);
    }

    public function salvar_matriz_quadro($cd_avaliacao, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_matriz_quadro
                 (
                    cd_avaliacao, 
                    cd_avaliacao_matriz_conceito_a, 
                    cd_avaliacao_matriz_conceito_b, 
                    cd_avaliacao_matriz_acao,
                    nr_ranking,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            SELECT ".intval($cd_avaliacao).",
                   (SELECT amc.cd_avaliacao_matriz_conceito
                      FROM rh_avaliacao.avaliacao_matriz_conceito amc
                     WHERE amc.cd_matriz_conceito = mq.cd_matriz_conceito_a
                       AND amc.cd_avaliacao       = ".intval($cd_avaliacao)."),
                   (SELECT amc.cd_avaliacao_matriz_conceito
                      FROM rh_avaliacao.avaliacao_matriz_conceito amc
                     WHERE amc.cd_matriz_conceito = mq.cd_matriz_conceito_b
                       AND amc.cd_avaliacao       = ".intval($cd_avaliacao)."),
                   (SELECT ama.cd_avaliacao_matriz_acao
                      FROM rh_avaliacao.avaliacao_matriz_acao ama
                     WHERE ama.cd_matriz_acao = mq.cd_matriz_acao
                       AND ama.cd_avaliacao       = ".intval($cd_avaliacao)."),
                   mq.nr_ranking,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM rh_avaliacao.matriz_quadro mq
             WHERE mq.dt_exclusao IS NULL;";

        $this->db->query($qr_sql);
    }

    public function get_avaliacao_usuario($cd_avaliacao, $cd_usuario = 0)
    {
    	$qr_sql = "
    		SELECT cd_avaliacao_usuario,
    		       cd_usuario,
			       ds_classe
			  FROM rh_avaliacao.avaliacao_usuario
			 WHERE dt_exclusao IS NULL
			   AND cd_avaliacao = ".intval($cd_avaliacao)."
			   ".(intval($cd_usuario) > 0 ? "AND cd_usuario = ".intval($cd_usuario) : "").";";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_bloco_classe($ds_classe)
    {
    	$qr_sql = "
    		SELECT g.ds_grupo_sigla AS tp_grupo,
    		       b.cd_bloco,
                   b.ds_bloco,
                   b.ds_bloco_descricao,
                   b.fl_conhecimento
              FROM rh_avaliacao.pergunta p
              JOIN rh_avaliacao.bloco b
                ON b.cd_bloco = p.cd_bloco
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = b.cd_grupo
             WHERE p.dt_exclusao IS NULL
               AND '".trim($ds_classe)."' IN (SELECT TRIM(cg.ds_cargo || CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) AS ds_classe
				                                FROM rh_avaliacao.pergunta_classe pc
				                                JOIN rh_avaliacao.classe cl
					                              ON cl.cd_classe = pc.cd_classe
				                                JOIN rh_avaliacao.cargo cg
	        				                      ON cg.cd_cargo = cl.cd_cargo
								               WHERE cl.dt_exclusao IS NULL
										         AND b.dt_exclusao IS NULL
										         AND cg.dt_exclusao IS NULL
										         AND pc.dt_exclusao IS NULL
										         AND pc.cd_pergunta = p.cd_pergunta)
			 GROUP BY tp_grupo,
			       b.cd_bloco,
                   b.ds_bloco,
                   b.ds_bloco_descricao
				       	
			UNION 

			SELECT g.ds_grupo_sigla AS tp_grupo,
			       b.cd_bloco,
                   b.ds_bloco,
                   b.ds_bloco_descricao,
                   b.fl_conhecimento
              FROM rh_avaliacao.pergunta p
              JOIN rh_avaliacao.bloco b
                ON b.cd_bloco = p.cd_bloco
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = b.cd_grupo
             WHERE p.dt_exclusao IS NULL
               AND (SELECT COUNT(*) 
                      FROM rh_avaliacao.pergunta_classe pc 
                     WHERE pc.dt_exclusao IS NULL 
                       AND pc.cd_pergunta = p.cd_pergunta) = 0

             GROUP BY tp_grupo,
             	   b.cd_bloco,
                   b.ds_bloco,
                   b.ds_bloco_descricao

             ORDER BY tp_grupo,
                   ds_bloco;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function gera_avaliacao_bloco($args = array())
    {
    	$cd_avaliacao_bloco = intval($this->db->get_new_id('rh_avaliacao.avaliacao_bloco', 'cd_avaliacao_bloco'));

    	$qr_sql = "
    		INSERT INTO rh_avaliacao.avaliacao_bloco
    		     (
    		     	cd_avaliacao_bloco,
            		cd_avaliacao, 
            		cd_avaliacao_usuario, 
            		tp_grupo, 
            		ds_bloco, 
            		ds_bloco_descricao,
                    fl_conhecimento, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
            	 )
    		VALUES 
    		     (
    		     	".intval($cd_avaliacao_bloco).",
    		     	".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
    		     	".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
    		     	".(trim($args['tp_grupo']) != '' ? str_escape($args['tp_grupo']) : "DEFAULT").",
    		     	".(trim($args['ds_bloco']) != '' ? str_escape($args['ds_bloco']) : "DEFAULT").",
                    ".(trim($args['ds_bloco_descricao']) != '' ? str_escape($args['ds_bloco_descricao']) : "DEFAULT").",
    		     	".(trim($args['fl_conhecimento']) != '' ? "'".trim($args['fl_conhecimento'])."'" : "DEFAULT").",
    		     	".intval($args['cd_usuario']).",
    		     	".intval($args['cd_usuario'])."
    		     );";

    	$this->db->query($qr_sql);

    	return $cd_avaliacao_bloco;
    }

    public function get_pergunta_bloco($cd_bloco, $ds_classe)
    {
    	$qr_sql = "
            SELECT ds_pergunta
              FROM rh_avaliacao.pergunta p
              JOIN rh_avaliacao.bloco b
                ON b.cd_bloco = p.cd_bloco
             WHERE p.dt_exclusao IS NULL
               AND p.cd_bloco            = ".intval($cd_bloco)."
               AND '".trim($ds_classe)."' IN (SELECT TRIM(cg.ds_cargo || CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) AS ds_classe
                                                FROM rh_avaliacao.pergunta_classe pc
                                                JOIN rh_avaliacao.classe cl
                                                  ON cl.cd_classe = pc.cd_classe
                                                JOIN rh_avaliacao.cargo cg
                                                  ON cg.cd_cargo = cl.cd_cargo
                                               WHERE cl.dt_exclusao IS NULL
                                                 AND b.dt_exclusao IS NULL
                                                 AND cg.dt_exclusao IS NULL
                                                 AND pc.dt_exclusao IS NULL
                                                 AND pc.cd_pergunta = p.cd_pergunta)
             ORDER BY ds_pergunta;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_pergunta_bloco_fator_desempenho($cd_bloco)
    {
        $qr_sql = "
            SELECT ds_pergunta
              FROM rh_avaliacao.pergunta p
              JOIN rh_avaliacao.bloco b
                ON b.cd_bloco = p.cd_bloco
             WHERE p.dt_exclusao IS NULL
               AND p.cd_bloco    = ".intval($cd_bloco)."
             ORDER BY ds_pergunta;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function gera_avaliacao_pergunta($args = array())
    {
    	$qr_sql = "
			INSERT INTO rh_avaliacao.avaliacao_bloco_pergunta
			     (
        			cd_avaliacao_bloco, 
        			cd_avaliacao, 
        			cd_avaliacao_usuario, 
        			ds_pergunta, 
        			cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
			VALUES 
			     (
			     	".(intval($args['cd_avaliacao_bloco']) > 0 ? intval($args['cd_avaliacao_bloco']) : "DEFAULT").",
			     	".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
			     	".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
			     	".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
    		     	".intval($args['cd_usuario']).",
    		     	".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);
    }

    public function listar_avaliacao($cd_avaliacao, $args = array())
    {
        $qr_sql = "
            SELECT uc.divisao AS ds_gerencia,
                   uc.avatar,
                   TO_CHAR(uc.dt_admissao, 'DD/MM/YYYY') AS dt_admissao,
                   uc.nome AS ds_nome,
                   uc.usuario AS ds_usuario,
                   au.cd_avaliacao_usuario,
                   au.cd_usuario,
                   au.ds_cargo_area_atuacao,
                   au.ds_classe,
                   au.ds_padrao,
                   '' AS dt_progressao_promocao,
                   funcoes.get_usuario_nome(au.cd_usuario_avaliador) AS ds_avaliador,
                   funcoes.get_usuario(au.cd_usuario_avaliador) AS ds_usuario_avaliador,
                   au.dt_envio_email,
                   COALESCE((SELECT (CASE WHEN aua.tp_avaliacao = 'PRI'
                                          THEN 'Autoavaliação'
                                          WHEN aua.tp_avaliacao = 'SEG'
                                          THEN 'Avaliação do Gestor'
                                          WHEN aua.tp_avaliacao = 'TER'
                                          THEN 'Revisão com o comitê de calibragem'
                                          WHEN aua.tp_avaliacao = 'QUA' AND aua.dt_encerramento IS NULL
                                          THEN 'Reunião de Consenso'
                                          WHEN aua.tp_avaliacao = 'QUA' AND aua.dt_encerramento IS NOT NULL AND au.dt_encerramento IS NULL
                                          THEN 'Aguardando PDI'
                                          ELSE 'Finalizado'
                                    END)
                               FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                              WHERE aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                                AND aua.tp_avaliacao    != 'PRI'
                              ORDER BY aua.dt_inclusao DESC
                              LIMIT 1), '') AS ds_status_avaliador,
                   COALESCE((SELECT (CASE WHEN aua.tp_avaliacao = 'PRI'
                                          THEN 'label label-important'
                                          WHEN aua.tp_avaliacao = 'SEG'
                                          THEN 'label label-important'
                                          WHEN aua.tp_avaliacao = 'TER'
                                          THEN 'label label-warning'
                                          WHEN aua.tp_avaliacao = 'QUA' AND aua.dt_encerramento IS NULL
                                          THEN 'label label-inverse'
                                          WHEN aua.tp_avaliacao = 'QUA' AND aua.dt_encerramento IS NOT NULL AND au.dt_encerramento IS NULL
                                          THEN 'label'
                                          ELSE 'label label-sucess'
                                    END)
                               FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                              WHERE aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                                AND aua.tp_avaliacao    != 'PRI'
                              ORDER BY aua.dt_inclusao DESC
                              LIMIT 1), 'label label-sucess') AS ds_class_status_avaliador,
                   COALESCE((SELECT (CASE WHEN aua.dt_encerramento IS NULL
                                          THEN 'Autoavaliação'
                                          ELSE 'Finalizado'
                                    END)
                               FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                              WHERE aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                                AND aua.tp_avaliacao    = 'PRI'), 'Finalizado') AS ds_status_avaliado,
                   COALESCE((SELECT (CASE WHEN aua.dt_encerramento IS NULL
                                          THEN 'label label-important'
                                          ELSE 'label label-success'
                                    END)
                               FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                              WHERE aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                                AND aua.tp_avaliacao    = 'PRI'), 'label label-success') AS ds_class_status_avaliado,
                   (SELECT SUM(nr_pontuacao)
                      FROM rh_avaliacao.avaliacao_usuario_capacitacao auc
                     WHERE auc.dt_exclusao          IS NULL
                       AND auc.cd_avaliacao         = au.cd_avaliacao
                       AND auc.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS nr_pontuacao_treinamento
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = au.cd_usuario
             WHERE au.dt_exclusao IS NULL
               AND au.cd_avaliacao = ".intval($cd_avaliacao)."
               ".(trim($args['ds_cargo']) != '' ? "AND au.ds_cargo = '".trim($args['ds_cargo'])."'": "")."
               ".(trim($args['cd_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_gerencia']."'"): "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_envio_email_avaliacao_usuario($cd_avaliacao_usuario, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE rh_avaliacao.avaliacao_usuario
    		   SET dt_envio_email         = CURRENT_TIMESTAMP,
    		       cd_usuario_envio_email = ".intval($cd_usuario)."
    		 WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

    	$this->db->query($qr_sql);
    }

    public function valida_permissao_usuario($cd_avaliacao_usuario, $cd_usuario)
    {
        $qr_sql = "
            SELECT COUNT(*) AS fl_permissao     
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN rh_avaliacao.avaliacao a
                ON a.cd_avaliacao = au.cd_avaliacao
              JOIN rh_avaliacao.avaliacao_usuario_avaliacao aua
                ON aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = au.cd_usuario
             WHERE au.dt_exclusao          IS NULL
               AND a.dt_exclusao           IS NULL
               AND a.dt_inicio             <= CURRENT_DATE
               AND au.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
               AND aua.cd_usuario          = ".intval($cd_usuario).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_formulario_usuario($cd_avaliacao_usuario)
    {
        $qr_sql = "
            SELECT a.cd_avaliacao,
                   a.nr_ano_avaliacao,
                   au.cd_usuario,
                   au.cd_avaliacao_usuario,
                   funcoes.get_usuario_nome(au.cd_usuario_avaliador) AS ds_avaliador,
                   uc.nome AS ds_avaliado,
                   TO_CHAR(uc.dt_admissao, 'DD/MM/YYYY') AS dt_admissao,
                   au.ds_cargo_area_atuacao,
                   a.ds_instrucao_preenchimento,
                   a.cd_avaliacao,
                   au.ds_conhecimento_generico,
                   au.ds_conhecimento_especifico,
                   au.cd_usuario_avaliador,
                   au.ds_classe,
                   au.ds_padrao,
                   (SELECT aua.dt_encerramento
                      FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                     WHERE aua.dt_exclusao          IS NULL
                       AND aua.tp_avaliacao         = 'PRI'
                       AND aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS dt_encerramento_autoavaliacao,
                   (SELECT aua.cd_avaliacao_usuario_avaliacao
                      FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                     WHERE aua.dt_exclusao          IS NULL
                       AND aua.tp_avaliacao         = 'PRI'
                       AND aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS cd_avaliacao_usuario_avaliacao_autoavaliacao,
                   (SELECT aua.dt_encerramento
                      FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                     WHERE aua.dt_exclusao          IS NULL
                       AND aua.tp_avaliacao         = 'QUA'
                       AND aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS dt_encerramento_reuniao_avaliacao,
                   (SELECT aua.cd_avaliacao_usuario_avaliacao
                      FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                     WHERE aua.dt_exclusao          IS NULL
                       AND aua.tp_avaliacao         = 'QUA'
                       AND aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS cd_avaliacao_usuario_avaliacao_reuniao_avaliacao,
                   au.dt_encerramento,
                   au.ds_pontos_fortes,
                   au.ds_pontos_melhorias,
                   au.ds_observacao,
                   au.ds_escolaridade_cargo,
                   au.ds_escolaridade_avaliado,
                   (SELECT COUNT(*)
                      FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento pdi
                     WHERE pdi.dt_exclusao IS NULL
                       AND pdi.cd_avaliacao_usuario = au.cd_avaliacao_usuario) AS tl_dpi,
                   (SELECT COUNT(*)
                      FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento pdi
                     WHERE pdi.dt_exclusao IS NULL
                       AND pdi.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                       AND 
                         (
                            ds_plano_melhoria IS NULL
                            OR 
                            ds_resultado IS NULL
                            OR 
                            ds_responsavel IS NULL
                            OR 
                            ds_quando IS NULL
                         )) AS tl_dpi_preenchido,
                   COALESCE((SELECT (CASE WHEN aua.dt_encerramento IS NULL
                                          THEN 'label label-important'
                              			  ELSE 'label label-success'
                                     END)
                               FROM rh_avaliacao.avaliacao_usuario_avaliacao aua
                              WHERE aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
                                AND aua.tp_avaliacao    = 'PRI'), 'label label-success') AS ds_class_avaliacao
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN rh_avaliacao.avaliacao a
                ON a.cd_avaliacao = au.cd_avaliacao
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = au.cd_usuario
             WHERE au.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_formulario_grupo($cd_avaliacao)
    {
        $qr_sql = "
            SELECT tp_grupo, 
                   ds_grupo
              FROM rh_avaliacao.avaliacao_peformance
             WHERE cd_avaliacao = ".intval($cd_avaliacao)."
             GROUP BY tp_grupo, ds_grupo
             ORDER BY tp_grupo, ds_grupo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_formulario_peformance($cd_avaliacao, $tp_grupo)
    {
        $qr_sql = "
            SELECT ds_performance,
                   ds_performance_descricao,
                   tp_performance,
                   nr_ponto
              FROM rh_avaliacao.avaliacao_peformance
             WHERE cd_avaliacao = ".intval($cd_avaliacao)."
               AND tp_grupo     = '".trim($tp_grupo)."'
               AND dt_exclusao  IS NULL
            ORDER BY nr_ponto;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_formulario_bloco_usuario($cd_avaliacao_usuario, $tp_grupo)
    {
        $qr_sql = "
            SELECT cd_avaliacao_bloco,
                   ds_bloco,
                   ds_bloco_descricao,
                   fl_conhecimento
              FROM rh_avaliacao.avaliacao_bloco
             WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
               AND tp_grupo             = '".trim($tp_grupo)."'
               AND dt_exclusao  IS NULL
             ORDER BY ds_bloco;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_formulario_pergunta_usuario($cd_avaliacao_usuario, $cd_avaliacao_bloco)
    {
        $qr_sql = "
            SELECT cd_avaliacao_bloco_pergunta,
                   ds_pergunta
              FROM rh_avaliacao.avaliacao_bloco_pergunta
             WHERE dt_exclusao IS NULL
               AND cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
               AND cd_avaliacao_bloco   = ".intval($cd_avaliacao_bloco)."
               AND dt_exclusao  IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliacao_usuario_avaliacao_resposta($cd_avaliacao_bloco_pergunta, $cd_avaliacao_usuario_avaliacao)
    {
        $qr_sql = "
            SELECT auar.cd_avaliacao_usuario_avaliacao_resposta,
                   auar.tp_resposta,
                   ap.ds_performance
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resposta auar
              JOIN rh_avaliacao.avaliacao_bloco_pergunta abp
                ON abp.cd_avaliacao_bloco_pergunta = auar.cd_avaliacao_bloco_pergunta
              JOIN rh_avaliacao.avaliacao_bloco ab
                ON ab.cd_avaliacao_bloco = abp.cd_avaliacao_bloco
              LEFT JOIN rh_avaliacao.avaliacao_peformance ap
                ON ap.tp_grupo = ab.tp_grupo
               AND ap.tp_performance = auar.tp_resposta
             WHERE auar.dt_exclusao                 IS NULL
               AND auar.cd_avaliacao_bloco_pergunta    = ".intval($cd_avaliacao_bloco_pergunta)."
               AND auar.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_avaliacao_usuario_avaliacao_resposta($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_usuario_avaliacao_resposta
                 (
                    cd_avaliacao, 
                    cd_avaliacao_usuario, 
                    cd_avaliacao_usuario_avaliacao, 
                    cd_avaliacao_bloco_pergunta, 
                    tp_resposta, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario_avaliacao']) > 0 ? intval($args['cd_avaliacao_usuario_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_bloco_pergunta']) > 0 ? intval($args['cd_avaliacao_bloco_pergunta']) : "DEFAULT").",
                    ".(trim($args['tp_resposta']) != '' ? "'".trim($args['tp_resposta'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario_inclusao']).",
                    ".intval($args['cd_usuario_inclusao'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_avaliacao_usuario_avaliacao_resposta($cd_avaliacao_usuario_avaliacao_resposta, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario_avaliacao_resposta
               SET tp_resposta          = ".(trim($args['tp_resposta']) != '' ? "'".trim($args['tp_resposta'])."'" : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($args['cd_usuario_inclusao'])."
             WHERE cd_avaliacao_usuario_avaliacao_resposta = ".intval($cd_avaliacao_usuario_avaliacao_resposta).";";

        $this->db->query($qr_sql);
    }

    public function encerrar_avaliacao($cd_avaliacao_usuario_avaliacao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario_avaliacao
               SET dt_encerramento         = CURRENT_TIMESTAMP, 
                   cd_usuario_encerramento = ".intval($cd_usuario)."
             WHERE cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao).";    

            INSERT INTO rh_avaliacao.avaliacao_usuario_avaliacao_resultado
                 (
                    cd_avaliacao, 
                    cd_avaliacao_usuario_avaliacao, 
                    tp_grupo, 
                    nr_resultado, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            SELECT ua.cd_avaliacao,
                   ".intval($cd_avaliacao_usuario_avaliacao).",
                   b.tp_grupo,
                   ROUND(SUM(af.nr_ponto) / COUNT(ar.*), 2) AS nr_resultado,
                   ".intval($cd_usuario).",
                   ".intval($cd_usuario)."
              FROM rh_avaliacao.avaliacao_usuario_avaliacao ua 
              JOIN rh_avaliacao.avaliacao_usuario_avaliacao_resposta ar
                ON ar.cd_avaliacao_usuario_avaliacao = ua.cd_avaliacao_usuario_avaliacao
              JOIN rh_avaliacao.avaliacao_bloco_pergunta bp
                ON bp.cd_avaliacao_bloco_pergunta = ar.cd_avaliacao_bloco_pergunta
               AND bp.dt_exclusao IS NULL
              JOIN rh_avaliacao.avaliacao_bloco b
                ON b.cd_avaliacao_bloco = bp.cd_avaliacao_bloco
              JOIN rh_avaliacao.avaliacao_peformance af
                ON af.tp_grupo       = b.tp_grupo
               AND af.tp_performance = ar.tp_resposta 
               AND af.cd_avaliacao   = ar.cd_avaliacao
             WHERE ua.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao)."
             GROUP BY ua.cd_avaliacao,
                      ua.tp_avaliacao,
                      b.tp_grupo
             ORDER BY ua.tp_avaliacao,
                      b.tp_grupo;";

        $this->db->query($qr_sql);
    }

    public function get_avaliacao_usuario_avaliacao_justificativa($cd_avaliacao_bloco, $cd_avaliacao_usuario_avaliacao)
    {
        $qr_sql = "
            SELECT auaj.cd_avaliacao_usuario_avaliacao_justificativa,
                   auaj.ds_justificativa
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_justificativa auaj
             WHERE auaj.dt_exclusao                    IS NULL
               AND auaj.cd_avaliacao_bloco             = ".intval($cd_avaliacao_bloco)."
               AND auaj.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_avaliacao_usuario_avaliacao_justificativa($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_usuario_avaliacao_justificativa
                 (
                    cd_avaliacao, 
                    cd_avaliacao_usuario, 
                    cd_avaliacao_usuario_avaliacao, 
                    cd_avaliacao_bloco, 
                    ds_justificativa, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario_avaliacao']) > 0 ? intval($args['cd_avaliacao_usuario_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_bloco']) > 0 ? intval($args['cd_avaliacao_bloco']) : "DEFAULT").",
                    ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                    ".intval($args['cd_usuario_inclusao']).",
                    ".intval($args['cd_usuario_inclusao'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_avaliacao_usuario_avaliacao_justificativa($cd_avaliacao_usuario_avaliacao_justificativa, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario_avaliacao_justificativa
               SET ds_justificativa     = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($args['cd_usuario_inclusao'])."
             WHERE cd_avaliacao_usuario_avaliacao_justificativa = ".intval($cd_avaliacao_usuario_avaliacao_justificativa).";";

        $this->db->query($qr_sql);
    }

    public function listar_minhas_avalicoes($cd_usuario, $args = array())
    {
        $qr_sql = "
            SELECT a.nr_ano_avaliacao,
                   au.cd_avaliacao_usuario,
                   funcoes.get_usuario_nome(au.cd_usuario)           AS ds_usuario_avaliado,
                   funcoes.get_usuario_nome(au.cd_usuario_avaliador) AS ds_usuario_avaliador
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN rh_avaliacao.avaliacao a
                ON a.cd_avaliacao = au.cd_avaliacao
             WHERE au.dt_exclusao IS NULL
               AND a.dt_inicio <= CURRENT_DATE
               AND (
					".intval($cd_usuario)." IN (au.cd_usuario, au.cd_usuario_avaliador) 
					OR
					".intval($cd_usuario)." = funcoes.get_usuario_gerente(funcoes.get_usuario_area(au.cd_usuario))
			   )
               ".(intval($args['nr_ano_avaliacao']) > 0 ? "AND a.nr_ano_avaliacao = ".intval($args['nr_ano_avaliacao']) : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_minhas_avalicoes_status($cd_avaliacao_usuario, $cd_usuario)
    {
        $qr_sql = "
            SELECT ua1.tp_avaliacao,
                   ua1.dt_encerramento, 
                   (CASE WHEN ua1.tp_avaliacao = 'PRI' AND ua1.dt_encerramento IS NULL
                         THEN 'Autoavaliação'
                         WHEN ua1.tp_avaliacao = 'SEG' AND ua1.dt_encerramento IS NULL
                         THEN 'Avaliação do Gestor'
                         WHEN ua1.tp_avaliacao = 'TER' AND ua1.dt_encerramento IS NULL
                         THEN 'Revisão com o comitê de calibragem'
                         WHEN ua1.tp_avaliacao = 'QUA' AND ua1.dt_encerramento IS NULL
                         THEN 'Reunião de Consenso'
                         WHEN ua1.tp_avaliacao = 'QUA' AND ua1.dt_encerramento IS NOT NULL AND u.dt_encerramento IS NULL
                         THEN 'Aguardando PDI'
                         WHEN u.dt_encerramento IS NOT NULL
                         THEN 'Finalizado'
                         ELSE ''
                   END) AS ds_status,
                   (CASE WHEN ua1.tp_avaliacao = 'PRI' AND ua1.dt_encerramento IS NULL
                         THEN 'label label-important'
                         WHEN ua1.tp_avaliacao = 'SEG' AND ua1.dt_encerramento IS NULL
                         THEN 'label label-important'
                         WHEN ua1.tp_avaliacao = 'TER' AND ua1.dt_encerramento IS NULL
                         THEN 'label label-warning'
                         WHEN ua1.tp_avaliacao = 'QUA' AND ua1.dt_encerramento IS NULL
                         THEN 'label label-inverse'
                         WHEN ua1.tp_avaliacao = 'QUA' AND ua1.dt_encerramento IS NOT NULL AND u.dt_encerramento IS NULL
                         THEN 'label'
                         WHEN u.dt_encerramento IS NOT NULL
                         THEN 'label labe-success'
                        ELSE ''
                   END) AS ds_class_status
              FROM rh_avaliacao.avaliacao_usuario_avaliacao ua1
              JOIN rh_avaliacao.avaliacao_usuario u
                ON u.cd_avaliacao_usuario = ua1.cd_avaliacao_usuario
             WHERE ua1.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
               AND (ua1.dt_encerramento IS NULL OR ua1.tp_avaliacao = 'QUA')
               AND (
                    ua1.cd_usuario = ".intval($cd_usuario)." 
                    OR 
                    (SELECT COUNT(*) 
                       FROM rh_avaliacao.avaliacao_usuario_avaliacao ua2 
                      WHERE ua2.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)." 
                        AND ua2.dt_encerramento IS NOT NULL) > 0
                    )
             ORDER BY (
                    CASE WHEN ua1.tp_avaliacao = 'PRI'
                         THEN 1
                         WHEN ua1.tp_avaliacao = 'SEG'
                         THEN 2
                         WHEN ua1.tp_avaliacao = 'TER'
                         THEN 3
                         WHEN ua1.tp_avaliacao = 'QUA'
                         THEN 4
                         ELSE 4
                    END) ASC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function formulario_status($cd_avaliacao_usuario, $cd_usuario)
    {
        $qr_sql = "
            SELECT ua.cd_avaliacao_usuario_avaliacao,
                   ua.tp_avaliacao,
                   ua.dt_encerramento, 
                   (CASE WHEN ua.tp_avaliacao = 'PRI' 
                         THEN 'Autoavaliação'
                         WHEN ua.tp_avaliacao = 'SEG'
                         THEN 'Avaliação do Gestor'
                         WHEN ua.tp_avaliacao = 'TER'
                         THEN 'Revisão com o comitê de calibragem'
                         WHEN ua.tp_avaliacao = 'QUA' AND ua.dt_encerramento IS NULL
                         THEN 'Reunião de Consenso'
                         WHEN ua.tp_avaliacao = 'QUA' AND ua.dt_encerramento IS NOT NULL AND u.dt_encerramento IS NULL
                         THEN 'Aguardando PDI'
                         ELSE ''
                   END) AS ds_status
              FROM rh_avaliacao.avaliacao_usuario_avaliacao ua
              JOIN rh_avaliacao.avaliacao_usuario u
                ON u.cd_avaliacao_usuario = ua.cd_avaliacao_usuario
             WHERE ua.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
               AND ua.cd_usuario           = ".intval($cd_usuario)."
             ORDER BY ua.dt_inclusao DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_avaliacao_usuario_avaliacao_justificativa_anterior($cd_avaliacao_bloco, $cd_avaliacao_usuario, $tp_avaliacao)
    {
        $qr_sql = "
            SELECT auaj.cd_avaliacao_usuario_avaliacao_justificativa,
                   auaj.ds_justificativa
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_justificativa auaj
             WHERE auaj.dt_exclusao                    IS NULL
               AND auaj.cd_avaliacao_bloco             = ".intval($cd_avaliacao_bloco)."
               AND auaj.cd_avaliacao_usuario_avaliacao = (SELECT cd_avaliacao_usuario_avaliacao
                                                            FROM rh_avaliacao.avaliacao_usuario_avaliacao
                                                           WHERE dt_exclusao          IS NULL
                                                             AND cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
                                                             AND tp_avaliacao         = '".trim($tp_avaliacao)."');";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_avaliacao_usuario_avaliacao_resposta_anterior($cd_avaliacao_bloco_pergunta, $cd_avaliacao_usuario, $tp_avaliacao)
    {
        $qr_sql = "
            SELECT auar.cd_avaliacao_usuario_avaliacao_resposta,
                   auar.tp_resposta,
                   ap.ds_performance
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resposta auar
              JOIN rh_avaliacao.avaliacao_bloco_pergunta abp
                ON abp.cd_avaliacao_bloco_pergunta = auar.cd_avaliacao_bloco_pergunta
              JOIN rh_avaliacao.avaliacao_bloco ab
                ON ab.cd_avaliacao_bloco = abp.cd_avaliacao_bloco
              LEFT JOIN rh_avaliacao.avaliacao_peformance ap
                ON ap.tp_grupo = ab.tp_grupo
               AND ap.tp_performance = auar.tp_resposta
             WHERE auar.dt_exclusao                    IS NULL
               AND auar.cd_avaliacao_bloco_pergunta    = ".intval($cd_avaliacao_bloco_pergunta)."
               AND auar.cd_avaliacao_usuario_avaliacao = (SELECT cd_avaliacao_usuario_avaliacao
                                                            FROM rh_avaliacao.avaliacao_usuario_avaliacao
                                                           WHERE dt_exclusao          IS NULL
                                                             AND cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
                                                             AND tp_avaliacao         = '".trim($tp_avaliacao)."');";

        return $this->db->query($qr_sql)->row_array();
    }

    public function matriz($cd_avaliacao)
    {
        $qr_sql = "
            SELECT mq.cd_avaliacao_matriz_quadro,
                   mq.cd_avaliacao_matriz_conceito_a,
                   mq.cd_avaliacao_matriz_conceito_b, 
                   mq.cd_avaliacao_matriz_acao, 
                   mc1.tp_grupo || mc1.nr_matriz_conceito AS ds_matriz_conceito_a,
                   mc2.tp_grupo || mc2.nr_matriz_conceito AS ds_matriz_conceito_b,
                   ma.ds_matriz_acao,
                   ma.cor_fundo,
                   ma.cor_texto,
                   mq.nr_ranking
              FROM rh_avaliacao.avaliacao_matriz_quadro mq
              JOIN rh_avaliacao.avaliacao_matriz_conceito mc1
                ON mc1.cd_avaliacao_matriz_conceito = mq.cd_avaliacao_matriz_conceito_a
               AND mc1.dt_exclusao IS NULL
              JOIN rh_avaliacao.avaliacao_matriz_conceito mc2
                ON mc2.cd_avaliacao_matriz_conceito = mq.cd_avaliacao_matriz_conceito_b
               AND mc2.dt_exclusao IS NULL   
              JOIN rh_avaliacao.avaliacao_matriz_acao ma
                ON ma.cd_avaliacao_matriz_acao = mq.cd_avaliacao_matriz_acao
               AND ma.dt_exclusao IS NULL
             WHERE mq.dt_exclusao IS NULL
               AND mq.cd_avaliacao = ".intval($cd_avaliacao)."
             ORDER BY mc1.tp_grupo, 
                      mc1.nr_matriz_conceito DESC, 
                      mc2.tp_grupo, 
                      mc2.nr_matriz_conceito;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function resultado($cd_avaliacao_usuario_avaliacao)
    {
        $qr_sql = "
            SELECT (SELECT c.tp_grupo || c.nr_matriz_conceito
                      FROM rh_avaliacao.avaliacao_matriz_conceito c
                     WHERE c.dt_exclusao  IS NULL
                       AND c.cd_avaliacao = r.cd_avaliacao
                       AND c.tp_grupo     = r.tp_grupo 
                       AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max) cd_matriz,
                   r.nr_resultado
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
             WHERE r.dt_exclusao                    IS NULL 
               AND r.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao)."
             ORDER BY r.tp_grupo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function resultado_tipo($cd_avaliacao_usuario_avaliacao)
    {
        $qr_sql = "       
            SELECT fl_progressao,
                   fl_promocao
              FROM rh_avaliacao.avaliacao_matriz_acao
             WHERE dt_exclusao IS NULL
               AND cd_avaliacao_matriz_acao = (

                    SELECT cd_avaliacao_matriz_acao
                      FROM rh_avaliacao.avaliacao_matriz_quadro
                     WHERE dt_exclusao IS NULL
                       AND cd_avaliacao_matriz_conceito_a = (

                        SELECT (
                                SELECT c.cd_avaliacao_matriz_conceito
                                  FROM rh_avaliacao.avaliacao_matriz_conceito c
                                 WHERE c.dt_exclusao  IS NULL
                                   AND c.cd_avaliacao = r.cd_avaliacao
                                   AND c.tp_grupo     = r.tp_grupo 
                                   AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max)
                         FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
                        WHERE r.dt_exclusao                    IS NULL 
                          AND r.tp_grupo                       = 'C'
                          AND r.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao)."
               )
               AND cd_avaliacao_matriz_conceito_b = (
                        SELECT (
                                SELECT c.cd_avaliacao_matriz_conceito
                                  FROM rh_avaliacao.avaliacao_matriz_conceito c
                                 WHERE c.dt_exclusao  IS NULL
                                   AND c.cd_avaliacao = r.cd_avaliacao
                                   AND c.tp_grupo     = r.tp_grupo 
                                   AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max)
                         FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
                        WHERE r.dt_exclusao                    IS NULL 
                          AND r.tp_grupo                       = 'FD'
                          AND r.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao)."

               ));";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_plano_desenvolvimento($cd_avaliacao_usuario)
    {
        $qr_sql = "
            SELECT cd_avaliacao_usuario_plando_desenvolvimento, 
                   cd_avaliacao, 
                   cd_avaliacao_usuario, 
                   ds_avaliacao_usuario_plando_desenvolvimento,
                   ds_plano_melhoria, 
                   ds_resultado, 
                   ds_responsavel, 
                   ds_quando, 
                   fl_formulario
              FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
             WHERE dt_exclusao                    IS NULL
               AND cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_plano_desenvolvimeto_individual($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
                 (
                    cd_avaliacao, 
                    cd_avaliacao_usuario, 
                    ds_avaliacao_usuario_plando_desenvolvimento, 
                    ds_plano_melhoria, 
                    ds_resultado, 
                    ds_responsavel, 
                    ds_quando, 
                    fl_formulario,  
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
                    ".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
                    ".(trim($args['ds_avaliacao_usuario_plando_desenvolvimento']) != '' ? str_escape($args['ds_avaliacao_usuario_plando_desenvolvimento']) : "DEFAULT").",
                    ".(trim($args['ds_plano_melhoria']) != '' ? str_escape($args['ds_plano_melhoria']) : "DEFAULT").",
                    ".(trim($args['ds_resultado']) != '' ? str_escape($args['ds_resultado']) : "DEFAULT").",
                    ".(trim($args['ds_responsavel']) != '' ? str_escape($args['ds_responsavel']) : "DEFAULT").",
                    ".(trim($args['ds_quando']) != '' ? str_escape($args['ds_quando']) : "DEFAULT").",
                    ".(trim($args['fl_formulario']) != '' ? "'".trim($args['fl_formulario'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
               SET ds_avaliacao_usuario_plando_desenvolvimento = (CASE WHEN fl_formulario = 'S' THEN ds_avaliacao_usuario_plando_desenvolvimento ELSE ".(trim($args['ds_avaliacao_usuario_plando_desenvolvimento']) != '' ? str_escape($args['ds_avaliacao_usuario_plando_desenvolvimento']) : "NULL")." END), 
                   ds_plano_melhoria                           = ".(trim($args['ds_plano_melhoria']) != '' ? str_escape($args['ds_plano_melhoria']) : "DEFAULT").", 
                   ds_resultado                                = ".(trim($args['ds_resultado']) != '' ? str_escape($args['ds_resultado']) : "DEFAULT").", 
                   ds_responsavel                              = ".(trim($args['ds_responsavel']) != '' ? str_escape($args['ds_responsavel']) : "DEFAULT").",
                   ds_quando                                   = ".(trim($args['ds_quando']) != '' ? str_escape($args['ds_quando']) : "DEFAULT").", 
                   dt_alteracao                                = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao                        = ".intval($args['cd_usuario'])."
             WHERE cd_avaliacao_usuario_plando_desenvolvimento = ".intval($cd_avaliacao_usuario_plando_desenvolvimento).";";

        $this->db->query($qr_sql);
    }

    public function carrega_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento)
    {
        $qr_sql = "
            SELECT cd_avaliacao_usuario_plando_desenvolvimento, 
                   cd_avaliacao, 
                   cd_avaliacao_usuario, 
                   ds_avaliacao_usuario_plando_desenvolvimento, 
                   ds_plano_melhoria, 
                   ds_resultado, 
                   ds_responsavel, 
                   ds_quando, 
                   fl_formulario
              FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
             WHERE cd_avaliacao_usuario_plando_desenvolvimento = ".intval($cd_avaliacao_usuario_plando_desenvolvimento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function excluir_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
               SET dt_exclusao         = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao = ".intval($cd_usuario)."
             WHERE cd_avaliacao_usuario_plando_desenvolvimento = ".intval($cd_avaliacao_usuario_plando_desenvolvimento).";";

        $this->db->query($qr_sql);
    }

    public function salvar_pontos_fortes($cd_avaliacao_usuario, $ds_pontos_fortes, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario
               SET ds_pontos_fortes     = ".(trim($ds_pontos_fortes) != '' ? str_escape($ds_pontos_fortes) : "DEFAULT").", 
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($cd_usuario)."
             WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        $this->db->query($qr_sql);
    }

    public function salvar_pontos_melhorias($cd_avaliacao_usuario, $ds_pontos_melhorias, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario
               SET ds_pontos_melhorias  = ".(trim($ds_pontos_melhorias) != '' ? str_escape($ds_pontos_melhorias) : "DEFAULT").", 
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($cd_usuario)."
             WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        $this->db->query($qr_sql);
    }

    public function salvar_observacao($cd_avaliacao_usuario, $ds_observacao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario
               SET ds_observacao        = ".(trim($ds_observacao) != '' ? str_escape($ds_observacao) : "DEFAULT").", 
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($cd_usuario)."
             WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        $this->db->query($qr_sql);
    }

    public function encerrar_avaliacao_usuario($cd_avaliacao_usuario, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.avaliacao_usuario
               SET ds_observacao           = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").", 
                   ds_pontos_melhorias     = ".(trim($args['ds_pontos_melhorias']) != '' ? str_escape($args['ds_pontos_melhorias']) : "DEFAULT").", 
                   ds_pontos_fortes        = ".(trim($args['ds_pontos_fortes']) != '' ? str_escape($args['ds_pontos_fortes']) : "DEFAULT").",
                   dt_encerramento         = CURRENT_TIMESTAMP, 
                   cd_usuario_encerramento = ".intval($args['cd_usuario'])."
             WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

        $this->db->query($qr_sql);
    }

    public function get_bloco($cd_avaliacao_bloco)
    {
        $qr_sql = "
            SELECT ds_bloco
              FROM rh_avaliacao.avaliacao_bloco
             WHERE cd_avaliacao_bloco = ".intval($cd_avaliacao_bloco).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_treinamento_avaliado($nr_ano_avaliacao, $cd_usuario)
    {
        if(intval($nr_ano_avaliacao) == 2021)
        {
            $periodo = " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('01/07/2020', 'DD/MM/YYYY') AND TO_DATE('30/06/2021', 'DD/MM/YYYY')";
        }
        else if(intval($nr_ano_avaliacao) == 2022)
        {
            $periodo = " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('01/07/2021', 'DD/MM/YYYY') AND TO_DATE('30/06/2022', 'DD/MM/YYYY')";
        }
        else
        {
            $periodo = " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('01/07/".intval($nr_ano_avaliacao - 1)."', 'DD/MM/YYYY') AND TO_DATE('30/06/".intval($nr_ano_avaliacao)."', 'DD/MM/YYYY')";
        }

        $qr_sql = "
            SELECT funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
                   tc.nome,
                   tc.promotor,
                   tc.cidade,
                   tc.uf,
                   TO_CHAR(tc.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(tc.dt_final,'DD/MM/YYYY') AS dt_final,
                   tcp.ds_treinamento_colaborador_tipo,
                   tc.carga_horaria
              FROM projetos.treinamento_colaborador tc
              JOIN projetos.treinamento_colaborador_tipo tcp
                ON tcp.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
              JOIN projetos.treinamento_colaborador_item tct
                ON tct.numero      = tc.numero
               AND tct.ano         = tc.ano
              JOIN projetos.usuarios_controledi uc
                ON uc.cd_patrocinadora      = tct.cd_empresa
               AND uc.cd_registro_empregado = tct.cd_registro_empregado
             WHERE uc.codigo       = ".intval($cd_usuario)."
               AND tc.dt_exclusao  IS NULL
               AND tct.dt_exclusao IS NULL
	           ".$periodo."
             ORDER BY numero DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_usuario_avaliacao($cd_usuario_avaliacao)
    {
    	$qr_sql = "
    		SELECT uc.divisao AS ds_gerencia,
                   uc.avatar,
                   TO_CHAR(uc.dt_admissao, 'DD/MM/YYYY') AS dt_admissao,
                   uc.nome AS ds_nome,
                   uc.usuario AS ds_usuario,
                   au.cd_avaliacao_usuario,
                   au.cd_avaliacao,
                   au.cd_usuario,
                   au.ds_cargo_area_atuacao,
                   au.ds_classe,
                   funcoes.get_usuario_area(au.cd_usuario_avaliador) AS ds_area_avaliador,
                   au.cd_usuario_avaliador
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = au.cd_usuario
             WHERE au.dt_exclusao IS NULL
               AND au.cd_avaliacao_usuario = ".intval($cd_usuario_avaliacao).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function get_gerencia($diretoria = '')
    {
    	$qr_sql = "
    		SELECT codigo AS value,
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente('DIV')
             WHERE 1 = 1 
              ".(trim($diretoria) != '' ? "AND diretoria = '".trim($diretoria)."'" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

  	public function get_usuario($cd_gerencia)
  	{
  		$qr_sql = "
  			SELECT codigo AS value,
  			       nome AS text
  			  FROM projetos.usuarios_controledi
  			 WHERE divisao = '".trim($cd_gerencia)."';";

  		return $this->db->query($qr_sql)->result_array();
  	}

  	public function salvar_avaliador($cd_avaliacao_usuario, $cd_usuario_avaliador, $cd_usuario)
  	{
  		$qr_sql = "
  			UPDATE rh_avaliacao.avaliacao_usuario
  			   SET cd_usuario_avaliador = ".intval($cd_usuario_avaliador).",
  			       cd_usuario_alteracao = ".intval($cd_usuario).",
  			       dt_alteracao 		= CURRENT_TIMESTAMP
  			 WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

  		$this->db->query($qr_sql);
  	}

  	public function salvar_avaliador_avaliacao($cd_avaliacao_usuario, $cd_usuario_avaliador, $cd_usuario)
  	{
  		$qr_sql = "
  			UPDATE rh_avaliacao.avaliacao_usuario_avaliacao
  			   SET cd_usuario 			= ".intval($cd_usuario_avaliador).",
  			       cd_usuario_alteracao = ".intval($cd_usuario).",
  			       dt_alteracao 		= CURRENT_TIMESTAMP
  			 WHERE cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
  			   AND tp_avaliacao         != 'PRI';";

  		$this->db->query($qr_sql);
  	}

  	public function listar_ocorrencia_ponto($cd_usuario, $nr_ano_avaliacao)
  	{
		$qr_sql = "
			SELECT op.cd_ocorrencia_ponto,
				   TO_CHAR(op.dt_referencia, 'MM/YYYY') AS dt_referencia,
				   op.cd_ocorrencia_ponto_tipo,
				   op.nr_quantidade,
				   opt.ds_ocorrencia_ponto_tipo
			  FROM rh_avaliacao.ocorrencia_ponto op
			  JOIN rh_avaliacao.ocorrencia_ponto_tipo opt
			    ON opt.cd_ocorrencia_ponto_tipo = op.cd_ocorrencia_ponto_tipo
			 WHERE op.dt_exclusao  IS NULL
			   AND op.cd_usuario                             = ".intval($cd_usuario)."
			   AND extract(year FROM dt_referencia)::integer = ".intval($nr_ano_avaliacao)."
			 ORDER BY dt_referencia ASC;";

  		return $this->db->query($qr_sql)->result_array();
  	}

    public function get_matriz_conceito($cd_avaliacao_usuario_avaliacao, $tp_grupo)
    {
        $qr_sql = "
            SELECT (SELECT c.cd_avaliacao_matriz_conceito
                      FROM rh_avaliacao.avaliacao_matriz_conceito c
                     WHERE c.dt_exclusao  IS NULL
                       AND c.cd_avaliacao = r.cd_avaliacao
                       AND c.tp_grupo     = r.tp_grupo 
                       AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max) cd_avaliacao_matriz_conceito
              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
             WHERE r.dt_exclusao                    IS NULL 
               AND r.cd_avaliacao_usuario_avaliacao = ".intval($cd_avaliacao_usuario_avaliacao)."
               AND r.tp_grupo                       = '".trim($tp_grupo)."'
             ORDER BY r.tp_grupo;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_promocao_progressao($cd_avaliacao_matriz_conceito_a, $cd_avaliacao_matriz_conceito_b)
    {
        $qr_sql = "
            SELECT fl_progressao,
                   fl_promocao,
                   nr_ranking
              FROM rh_avaliacao.avaliacao_matriz_quadro mq
              JOIN rh_avaliacao.avaliacao_matriz_acao ma
                ON ma.cd_avaliacao_matriz_acao = mq.cd_avaliacao_matriz_acao
             WHERE mq.dt_exclusao IS NULL
               AND cd_avaliacao_matriz_conceito_a = ".intval($cd_avaliacao_matriz_conceito_a)."
               AND cd_avaliacao_matriz_conceito_b = ".intval($cd_avaliacao_matriz_conceito_b).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_resultado_somado($cd_avaliacao)
    {
        $qr_sql = "
            SELECT (SELECT ap.ds_grupo FROM rh_avaliacao.avaliacao_peformance ap WHERE ap.dt_exclusao IS NULL AND ap.tp_grupo = auar.tp_grupo LIMIT 1) AS ds_grupo,
                   SUM(auar.nr_resultado) AS nr_totalizador,
                   AVG(auar.nr_resultado) AS nr_media
              FROM rh_avaliacao.avaliacao_usuario au 
              JOIN rh_avaliacao.avaliacao_usuario_avaliacao aua
                ON aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario
               AND aua.tp_avaliacao = 'QUA' 
              JOIN rh_avaliacao.avaliacao_usuario_avaliacao_resultado auar
                ON auar.cd_avaliacao_usuario_avaliacao = aua.cd_avaliacao_usuario_avaliacao
             WHERE au.dt_exclusao     IS NULL
               AND au.dt_encerramento IS NOT NULL
               AND au.cd_avaliacao    = ".intval($cd_avaliacao)."
             GROUP BY auar.tp_grupo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliacao_usuario_cargo($cd_avaliacao)
    {
        $qr_sql = "
            SELECT ds_cargo AS value,
                   ds_cargo AS text
              FROM rh_avaliacao.avaliacao_usuario 
             WHERE dt_exclusao     IS NULL 
               AND dt_encerramento IS NOT NULL 
               AND cd_avaliacao    = ".intval($cd_avaliacao)."
             GROUP BY ds_cargo 
             ORDER BY ds_cargo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliacao_usuario_gerencia($cd_avaliacao)
    {
        $qr_sql = "
            SELECT uc.divisao AS value, 
                   uc.divisao || ' - ' || d.nome AS text
              FROM rh_avaliacao.avaliacao_usuario au
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = au.cd_usuario
              JOIN projetos.divisoes d
                ON d.codigo = uc.divisao
             WHERE au.dt_exclusao     IS NULL 
               AND au.dt_encerramento IS NOT NULL 
               AND au.cd_avaliacao    = ".intval($cd_avaliacao)."
             GROUP BY uc.divisao, d.nome
             ORDER BY d.nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliacao_matriz($cd_avaliacao, $cd_matriz_a, $cd_matriz_b, $args = array())
    {
        $qr_sql = "
            SELECT x.cd_usuario,
                   x.ds_cargo,
                   uc.nome AS ds_nome,
                   uc.usuario AS ds_usuario,
                   uc.divisao AS ds_gerencia,
                   uc.avatar,
                   x.ds_cargo_area_atuacao,
                   TO_CHAR(uc.dt_admissao, 'DD/MM/YYYY') AS dt_admissao,
                   x.ds_classe,
                   x.ds_padrao,
                   funcoes.get_usuario_nome(x.cd_usuario_avaliador) AS ds_avaliador,
                   x.cd_avaliacao_usuario
             FROM (

                SELECT (SELECT (SELECT c.tp_grupo || c.nr_matriz_conceito
                                  FROM rh_avaliacao.avaliacao_matriz_conceito c
                                 WHERE c.dt_exclusao  IS NULL
                                   AND c.cd_avaliacao = r.cd_avaliacao
                                   AND c.tp_grupo     = r.tp_grupo 
                                   AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max)
                              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
                             WHERE r.cd_avaliacao_usuario_avaliacao = aua.cd_avaliacao_usuario_avaliacao
                               AND r.dt_exclusao      IS NULL
                               AND r.tp_grupo       = 'C') AS cd_matriz_a,
                       (SELECT (SELECT c.tp_grupo || c.nr_matriz_conceito
                                  FROM rh_avaliacao.avaliacao_matriz_conceito c
                                 WHERE c.dt_exclusao  IS NULL
                                   AND c.cd_avaliacao = r.cd_avaliacao
                                   AND c.tp_grupo     = r.tp_grupo 
                                   AND r.nr_resultado BETWEEN c.nr_nota_min AND c.nr_nota_max)
                              FROM rh_avaliacao.avaliacao_usuario_avaliacao_resultado r
                             WHERE r.cd_avaliacao_usuario_avaliacao = aua.cd_avaliacao_usuario_avaliacao
                               AND r.dt_exclusao      IS NULL
                               AND r.tp_grupo       = 'FD') AS cd_matriz_b,
                               au.cd_usuario,
                               au.ds_cargo,
                               au.ds_cargo_area_atuacao,
                               au.ds_classe,
                               au.ds_padrao,
                               au.cd_usuario_avaliador,
                               au.cd_avaliacao_usuario
                          FROM rh_avaliacao.avaliacao_usuario au
                          JOIN rh_avaliacao.avaliacao_usuario_avaliacao aua
                            ON aua.cd_avaliacao_usuario = au.cd_avaliacao_usuario 
                         WHERE au.dt_exclusao      IS NULL 
                           AND au.dt_encerramento  IS NOT NULL 
                           AND au.cd_avaliacao     = ".intval($cd_avaliacao)."
                           AND aua.tp_avaliacao    = 'QUA'
                           AND aua.dt_encerramento IS NOT NULL

             ) x
             JOIN projetos.usuarios_controledi uc
               ON uc.codigo = x.cd_usuario
             JOIN projetos.divisoes d
               ON d.codigo = uc.divisao
            WHERE x.cd_matriz_a = '".trim($cd_matriz_a)."'
              AND cd_matriz_b = '".trim($cd_matriz_b)."'
              ".(trim($args['ds_cargo']) != '' ? "AND x.ds_cargo = '".trim($args['ds_cargo'])."'": "")."
              ".(trim($args['cd_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_gerencia']."'") : (trim($args['diretoria']) != '' ? "AND d.area = '".trim($args['diretoria'])."'" : "")).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_avaliacao($cd_avaliacao_usuario)
  	{
  		$qr_sql = "
  			SELECT cd_avaliacao_usuario_avaliacao,
  				   tp_avaliacao,
  				   (CASE WHEN tp_avaliacao = 'PRI'
                       		THEN 1
							WHEN tp_avaliacao = 'SEG'
							THEN 2
							WHEN tp_avaliacao = 'TER'
							THEN 3
							WHEN tp_avaliacao = 'QUA'
							THEN 4
                            ELSE 4
                       END) AS nr_ordem,
				   (CASE WHEN tp_avaliacao = 'PRI'
                       		THEN 'Autoavaliação'
							WHEN tp_avaliacao = 'SEG'
							THEN 'Superior'
							WHEN tp_avaliacao = 'TER'
							THEN 'Comitê de Calibragem'
							WHEN tp_avaliacao = 'QUA'
							THEN 'Reunião de Consenso'
                            ELSE 'Reunião de Consenso'
                       END) AS ds_avaliacao_usuario,
                       dt_encerramento
  			  FROM rh_avaliacao.avaliacao_usuario_avaliacao
  			 WHERE dt_exclusao IS NULL
  			   AND cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario)."
  			 ORDER BY nr_ordem ASC;";

  		return $this->db->query($qr_sql)->result_array();
  	}

  	public function listar_capacitacao($cd_avaliacao_usuario)
  	{
  		$qr_sql = "
  			SELECT auc.cd_avaliacao_usuario_capacitacao,
  				   auc.nr_pontuacao,
  				   auct.ds_avaliacao_usuario_capacitacao_tipo
  			  FROM rh_avaliacao.avaliacao_usuario_capacitacao auc
  			  JOIN rh_avaliacao.avaliacao_usuario_capacitacao_tipo auct
  			    ON auct.cd_avaliacao_usuario_capacitacao_tipo = auc.cd_avaliacao_usuario_capacitacao_tipo
  			 WHERE auc.dt_exclusao  IS NULL
  			   AND auct.dt_exclusao IS NULL
  			   AND auc.cd_avaliacao_usuario = ".intval($cd_avaliacao_usuario).";";

  		return $this->db->query($qr_sql)->result_array();
  	}

  	public function carrega_capacitacao($cd_avaliacao_usuario_capacitacao)
  	{
  		$qr_sql = "
  			SELECT cd_avaliacao_usuario_capacitacao,
  				   nr_pontuacao,
  				   cd_avaliacao_usuario_capacitacao_tipo
  			  FROM rh_avaliacao.avaliacao_usuario_capacitacao
  			 WHERE dt_exclusao  IS NULL
  			   AND cd_avaliacao_usuario_capacitacao = ".intval($cd_avaliacao_usuario_capacitacao).";";

  		return $this->db->query($qr_sql)->row_array();
  	}

  	public function salvar_capacitacao($args = array())
  	{
  		$qr_sql = "
  			INSERT INTO rh_avaliacao.avaliacao_usuario_capacitacao
  				(
					cd_avaliacao,
					cd_avaliacao_usuario,
					cd_avaliacao_usuario_capacitacao_tipo,
					nr_pontuacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
  				)
  			VALUES
  				(
  					".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
  					".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
  					".(intval($args['cd_avaliacao_usuario_capacitacao_tipo']) > 0 ? intval($args['cd_avaliacao_usuario_capacitacao_tipo']) : "DEFAULT").",
  					".(intval($args['nr_pontuacao']) > 0 ? floatval($args['nr_pontuacao']) : "DEFAULT").",
  					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
  					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
  				);";

  		$this->db->query($qr_sql);
  	}

  	public function atualizar_capacitacao($cd_avaliacao_usuario_capacitacao, $args = array())
  	{
  		$qr_sql = "
  			UPDATE rh_avaliacao.avaliacao_usuario_capacitacao
  			   SET cd_avaliacao 						 = ".(intval($args['cd_avaliacao']) > 0 ? intval($args['cd_avaliacao']) : "DEFAULT").",
				   cd_avaliacao_usuario 				 = ".(intval($args['cd_avaliacao_usuario']) > 0 ? intval($args['cd_avaliacao_usuario']) : "DEFAULT").",
				   cd_avaliacao_usuario_capacitacao_tipo = ".(intval($args['cd_avaliacao_usuario_capacitacao_tipo']) > 0 ? intval($args['cd_avaliacao_usuario_capacitacao_tipo']) : "DEFAULT").",
				   nr_pontuacao 						 = ".(intval($args['nr_pontuacao']) > 0 ? floatval($args['nr_pontuacao']) : "DEFAULT").",
				   cd_usuario_alteracao 				 = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
  			 WHERE cd_avaliacao_usuario_capacitacao = ".intval($cd_avaliacao_usuario_capacitacao).";";

  		$this->db->query($qr_sql);
  	}

    public function get_avaliacao_ano()
    {
        $qr_sql = "
            SELECT a.cd_avaliacao AS value,
                   a.nr_ano_avaliacao AS text
              FROM rh_avaliacao.avaliacao a
             WHERE a.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_ultima_avaliacao()
    {
        $qr_sql = "
            SELECT a.cd_avaliacao
              FROM rh_avaliacao.avaliacao a
             WHERE a.dt_exclusao IS NULL
             ORDER BY a.nr_ano_avaliacao DESC
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

  	public function listar_relatorio_pdi($cd_avaliacao, $args = array())
  	{
  		$qr_sql = "
			SELECT au.cd_usuario, 
			       uc.divisao AS cd_gerencia, 
			       uc.nome AS ds_colaborador,
			       aupd.ds_avaliacao_usuario_plando_desenvolvimento,
			       aupd.ds_plano_melhoria,
			       aupd.ds_resultado,
			       aupd.ds_responsavel,
			       aupd.ds_quando 
			  FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento aupd
			  JOIN rh_avaliacao.avaliacao_usuario au
			    ON au.cd_avaliacao_usuario = aupd.cd_avaliacao_usuario
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = au.cd_usuario
			 WHERE aupd.dt_exclusao IS NULL 
			   AND aupd. cd_avaliacao = ".intval($cd_avaliacao)."
			   ".(trim($args['cd_gerencia']) != '' ? 'AND uc.divisao = '.str_escape($args['cd_gerencia']) : '')."
			   ".(intval($args['cd_usuario']) > 0 ? 'AND au.cd_usuario = '.intval($args['cd_usuario']) : '')."
			   ".(trim($args['ds_cargo']) != '' ? 'AND au.ds_cargo = '.str_escape($args['ds_cargo']) : '')."
			   ".(trim($args['ds_avaliacao_usuario_plando_desenvolvimento']) != '' ? 'AND aupd.ds_avaliacao_usuario_plando_desenvolvimento = '.str_escape($args['ds_avaliacao_usuario_plando_desenvolvimento']) : '').";";

		return $this->db->query($qr_sql)->result_array();
  	}

  	public function get_usuarios_avaliacao($cd_avaliacao)
  	{
  		$qr_sql = "
  			SELECT cd_usuario AS value,
			       funcoes.get_usuario_nome(cd_usuario) AS text
			  FROM rh_avaliacao.avaliacao_usuario
			 WHERE dt_exclusao IS NULL
			   AND cd_avaliacao = ".intval($cd_avaliacao)."
			 ORDER BY text ASC;";

  		return $this->db->query($qr_sql)->result_array();
  	}

  	public function get_competencia($cd_avaliacao)
  	{
  		$qr_sql = "
  			SELECT ds_avaliacao_usuario_plando_desenvolvimento AS value,
			       ds_avaliacao_usuario_plando_desenvolvimento AS text
			  FROM rh_avaliacao.avaliacao_usuario_plando_desenvolvimento
			 WHERE dt_exclusao IS NULL
               AND cd_avaliacao = ".intval($cd_avaliacao)."
			 GROUP BY ds_avaliacao_usuario_plando_desenvolvimento
			 ORDER BY ds_avaliacao_usuario_plando_desenvolvimento ASC;";

  		return $this->db->query($qr_sql)->result_array();
  	}
} 