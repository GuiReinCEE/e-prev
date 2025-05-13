<?php
class Projeto_model extends Model 
{
  function __construct()
  {
    parent::Model();
  }

  public function get_projetos()
  {
    $qr_sql = "
      SELECT ds_projeto AS text,
             cd_projeto AS value
        FROM gestao.projeto
       WHERE dt_exclusao IS NULL;";

    return $this->db->query($qr_sql)->result_array();
  }

  public function get_divisao()
  {
    $qr_sql = "
        SELECT codigo AS value,
               codigo || ' - ' || nome AS text
          FROM projetos.divisoes 
         WHERE tipo IN ('DIV', 'ASS')
           AND dt_vigencia_fim is null
            OR dt_vigencia_fim > CURRENT_TIMESTAMP 
         ORDER BY nome";
               
    return $this->db->query($qr_sql)->result_array();
  }

  public function get_gerencia()
  {
    $qr_sql = "
      SELECT codigo AS value,
             codigo || ' - ' || nome AS text
        FROM projetos.divisoes
       WHERE tipo IN ('DIV', 'ASS')
         AND dt_vigencia_ini <= CURRENT_TIMESTAMP
         AND (dt_vigencia_fim >= CURRENT_TIMESTAMP OR dt_vigencia_fim IS NULL)
       ORDER BY nome;";
  
    return $this->db->query($qr_sql)->result_array();
  }

  public function alterar_ordem($cd_projeto_cronograma, $nr_ordem, $cd_usuario)
  {
    $qr_sql = "
        UPDATE gestao.projeto_cronograma
         SET nr_ordem       = ".(trim($nr_ordem) != '' ? intval($nr_ordem) : "DEFAULT").",
             cd_usuario_alteracao = ".intval($cd_usuario).",
           dt_alteracao         = CURRENT_TIMESTAMP
         WHERE dt_exclusao IS NULL
         AND cd_projeto_cronograma = ".intval($cd_projeto_cronograma).";";    

    $this->db->query($qr_sql);  
  }
  
  public function get_ordem($cd_projeto)
  {
    $qr_sql= "
        SELECT COALESCE(nr_ordem + 1, 0) AS nr_ordem
          FROM gestao.projeto_cronograma
         WHERE dt_exclusao IS NULL
           AND cd_projeto = ".intval($cd_projeto)."
           AND cd_projeto_cronograma_pai IS NULL
         ORDER BY nr_ordem DESC
         LIMIT 1;";
    
    return $this->db->query($qr_sql)->row_array();
  }
    
  public function get_ordem_sub_cronograma($cd_projeto, $cd_projeto_cronograma_pai)
  {
    $qr_sql= "
        SELECT COALESCE(nr_ordem + 1, 0) AS nr_ordem
          FROM gestao.projeto_cronograma
         WHERE dt_exclusao IS NULL
           AND cd_projeto = ".intval($cd_projeto)."
           AND cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma_pai)."
         ORDER BY nr_ordem DESC
         LIMIT 1;";
    
    return $this->db->query($qr_sql)->row_array();
  }

  public function gerencia_responsavel($cd_projeto_cronograma)
  {
    $qr_sql = "
      SELECT pge.cd_gerencia,
             d.nome AS gerencia
        FROM gestao.projeto_cronograma_gerencia pge
        JOIN projetos.divisoes d
          ON d.codigo = pge.cd_gerencia
       WHERE pge.dt_exclusao IS NULL
         AND pge.cd_projeto_cronograma = ".intval($cd_projeto_cronograma)."
       ORDER BY pge.cd_gerencia;";
       
    return $this->db->query($qr_sql)->result_array();
  }

  public function listar($args = array())
  {
    $qr_sql = "
        SELECT p.cd_projeto,
               p.ds_projeto,
               TO_CHAR(p.dt_inclusao, 'DD/MM/YYY hh24:MI:SS') AS dt_inclusao,
               p.objetivo,
               p.justificativa,
               p.cd_gerencia_resposanvel
          FROM gestao.projeto p
         WHERE p.dt_exclusao IS NULL
               ".(trim($args['cd_projeto']) != '' ? "AND p.cd_projeto = ".intval($args['cd_projeto'])."" : "")."
               ".(trim($args['cd_gerencia_resposanvel']) != '' ? "AND cd_gerencia_resposanvel = '".trim($args['cd_gerencia_resposanvel'])."'" : "")."
               ".(((trim($args['dt_inclusao_ini'])) AND (trim($args['dt_inclusao_fim']))) ? "AND DATE_TRUNC('day', p.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : '').";";

    return $this->db->query($qr_sql)->result_array();
  }
  
  public function gerencia_envolvida($cd_projeto)
  {
    $qr_sql = "
        SELECT pge.cd_gerencia_envolvida,
               d.nome AS gerencia_envolvida
          FROM gestao.projeto_gerencia_envolvida pge
          JOIN projetos.divisoes d
            ON d.codigo = pge.cd_gerencia_envolvida
  	     WHERE pge.dt_exclusao IS NULL
           AND pge.cd_projeto = ".intval($cd_projeto)."
         ORDER BY pge.cd_gerencia_envolvida;";

    return $this->db->query($qr_sql)->result_array();
  }

  public function carrega($cd_projeto)
  {
    $qr_sql = "
        SELECT p.cd_projeto,
               p.ds_projeto,
               p.objetivo,
               p.justificativa,
               p.cd_gerencia_resposanvel,
               p.ds_indicador,
               d.nome AS gerencia_resposanvel
          FROM gestao.projeto p
          JOIN projetos.divisoes d
            ON d.codigo = p.cd_gerencia_resposanvel
         WHERE p.cd_projeto = ".intval($cd_projeto).";";

    return $this->db->query($qr_sql)->row_array();
  }

  public function salvar($args)
  {
    $cd_projeto = intval($this->db->get_new_id('gestao.projeto', 'cd_projeto'));

    $qr_sql = "
        INSERT INTO gestao.projeto
             (
                cd_projeto,
                ds_projeto,
                objetivo,
                justificativa,
                cd_gerencia_resposanvel,
                cd_usuario_inclusao,
                cd_usuario_alteracao
             )
        VALUES
             (
                ".intval($cd_projeto).",
                ".(trim($args['ds_projeto']) != '' ? str_escape($args['ds_projeto']) : "DEFAULT").",
                ".(trim($args['objetivo']) != '' ? str_escape($args['objetivo']) : "DEFAULT").",
                ".(trim($args['justificativa']) != '' ? str_escape($args['justificativa']) : "DEFAULT").",
                ".(trim($args['cd_gerencia_resposanvel']) != '' ? str_escape($args['cd_gerencia_resposanvel']) : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );

        INSERT INTO gestao.projeto_gerencia_envolvida(cd_projeto, cd_gerencia_envolvida, cd_usuario_inclusao, 
                    cd_usuario_alteracao)
             SELECT ".intval($cd_projeto).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
               FROM (VALUES ('".implode("'),('", $args['gerencia_envolvida'])."')) x;";

    $this->db->query($qr_sql);

    return $cd_projeto;
  }

  public function atualizar($cd_projeto, $args)
  {
    $qr_sql = "
        UPDATE gestao.projeto
           SET ds_projeto              = ".(trim($args['ds_projeto']) != '' ? str_escape($args['ds_projeto']):"DEFAULT").",
               objetivo                = ".(trim($args['objetivo']) != '' ? str_escape($args['objetivo']) : "DEFAULT").",
               justificativa           = ".(trim($args['justificativa']) != '' ? str_escape($args['justificativa']):"DEFAULT").",
               cd_gerencia_resposanvel = ".(trim($args['cd_gerencia_resposanvel']) != '' ? str_escape($args['cd_gerencia_resposanvel']) : "DEFAULT").",
               cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
               dt_alteracao            = CURRENT_TIMESTAMP
         WHERE cd_projeto = ".intval($cd_projeto).";

        UPDATE gestao.projeto_gerencia_envolvida
           SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
               dt_exclusao         = CURRENT_TIMESTAMP
         WHERE cd_projeto  = ".intval($cd_projeto)."
           AND dt_exclusao IS NULL
           AND cd_gerencia_envolvida NOT IN ('".implode("','", $args['gerencia_envolvida'])."');

        INSERT INTO gestao.projeto_gerencia_envolvida(cd_projeto, cd_gerencia_envolvida, cd_usuario_inclusao, cd_usuario_alteracao)
        SELECT ".intval($cd_projeto).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
          FROM (VALUES ('".implode("'),('", $args['gerencia_envolvida'])."')) x
         WHERE x.column1 NOT IN (SELECT a.cd_gerencia_envolvida
                                   FROM gestao.projeto_gerencia_envolvida a
                                  WHERE a.cd_projeto = ".intval($cd_projeto)."
                                    AND a.dt_exclusao IS NULL);";

    $this->db->query($qr_sql);
  }
  
  public function listar_relatorio()
  {
    $qr_sql = "
        SELECT p.cd_projeto,
  			       p.ds_projeto,
  			       (SELECT COUNT(*)
  				        FROM gestao.projeto_cronograma pc
  			         WHERE pc.cd_projeto = p.cd_projeto
               ) AS etapas_previstas,
  			       (SELECT COUNT(*)
  				        FROM gestao.projeto_cronograma pc
  			         WHERE pc.cd_projeto = p.cd_projeto
  			           AND CURRENT_DATE >= pc.dt_projeto_cronograma_realizado_fim
               ) as etapas_realizadas,
  			       (SELECT COUNT(*)
  				        FROM gestao.projeto_cronograma pc
  			         WHERE (pc.cd_projeto = p.cd_projeto
  			           AND CURRENT_DATE < pc.dt_projeto_cronograma_realizado_fim) 
  				          OR (pc.dt_projeto_cronograma_realizado_fim IS NULL
  				         AND pc.cd_projeto = p.cd_projeto)
               ) as etapas_pendentes
          FROM gestao.projeto p
  		    LEFT JOIN gestao.projeto_cronograma pc
  		      ON pc.cd_projeto = p.cd_projeto
         WHERE p.dt_exclusao IS NULL
  	     GROUP BY p.cd_projeto;";

    return $this->db->query($qr_sql)->result_array();
  }
  
  public function indicador()
  {
    $qr_sql = "
        SELECT i.cd_indicador As value,
               p.procedimento || ' - ' || i.ds_indicador AS text
          FROM indicador.indicador i
          JOIN projetos.processos p
            ON p.cd_processo = i.cd_processo
         WHERE i.dt_exclusao IS NULL
         ORDER BY p.procedimento, i.ds_indicador;";

    return $this->db->query($qr_sql)->result_array();
  }

  public function projeto_indicador($cd_projeto)
  {
    $qr_sql = "
        SELECT pi.cd_indicador,
               p.procedimento || ' - ' || i.ds_indicador AS text
          FROM gestao.projeto_indicador pi
          JOIN indicador.indicador i
            ON i.cd_indicador = pi.cd_indicador
          JOIN projetos.processos p
            ON p.cd_processo = i.cd_processo
         WHERE pi.dt_exclusao IS NULL
           AND pi.cd_projeto = ".intval($cd_projeto)."
         ORDER BY p.procedimento, i.ds_indicador;";

    return $this->db->query($qr_sql)->result_array();
  }

  public function salvar_indicador($cd_projeto, $args)
  {
    $qr_sql = "
        UPDATE gestao.projeto
           SET ds_indicador         = ".(trim($args['ds_indicador']) != '' ? str_escape($args['ds_indicador']) : "DEFAULT").",
               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
               dt_alteracao         = CURRENT_TIMESTAMP
         WHERE cd_projeto = ".intval($cd_projeto).";

        UPDATE gestao.projeto_indicador
           SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
               dt_exclusao         = CURRENT_TIMESTAMP
         WHERE cd_projeto   = ".intval($cd_projeto)."
           AND dt_exclusao  IS NULL
           AND cd_indicador NOT IN (".implode(',', $args['indicador']).");
     
        INSERT INTO gestao.projeto_indicador(cd_projeto, cd_indicador, cd_usuario_inclusao, cd_usuario_alteracao)
        SELECT ".intval($cd_projeto).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
          FROM (VALUES (".implode("),(", $args['indicador']).")) x
         WHERE x.column1 NOT IN (SELECT a.cd_indicador
                                   FROM gestao.projeto_indicador a
                                  WHERE a.cd_projeto = ".intval($cd_projeto)."
                                    AND a.dt_exclusao IS NULL);";

    $this->db->query($qr_sql);
  }

  public function listar_custo($cd_projeto)
  {
    $qr_sql = "
        SELECT cd_projeto_custo,
               ds_projeto_custo,
               nr_valor,
               nr_valor_aprovado
          FROM gestao.projeto_custo
         WHERE cd_projeto = ".intval($cd_projeto).";";

    return $this->db->query($qr_sql)->result_array();
  }

  public function carrega_custo($cd_projeto_custo)
  {
    $qr_sql = "
        SELECT cd_projeto_custo,
               ds_projeto_custo,
               nr_valor,
               nr_valor_aprovado
          FROM gestao.projeto_custo
         WHERE cd_projeto_custo = ".intval($cd_projeto_custo).";";

      return $this->db->query($qr_sql)->row_array();
  }

  public function salvar_custo($args)
  {
    $qr_sql = "
        INSERT INTO gestao.projeto_custo
             (
                cd_projeto,
                ds_projeto_custo,
                nr_valor,
                nr_valor_aprovado,
                cd_usuario_inclusao,
                cd_usuario_alteracao
             )
        VALUES
             (
                ".intval($args['cd_projeto']).",
                ".(trim($args['ds_projeto_custo']) != '' ? str_escape($args['ds_projeto_custo']) : "DEFAULT").",
                ".(trim($args['nr_valor']) != '' ? floatval($args['nr_valor']) : "DEFAULT").",
                ".(trim($args['nr_valor_aprovado']) != "" ? floatval($args['nr_valor_aprovado']) : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );";

    $this->db->query($qr_sql);
  }

  public function atualizar_custo($cd_projeto_custo, $args)
  {
    $qr_sql = "
        UPDATE gestao.projeto_custo
           SET ds_projeto_custo     = ".(trim($args['ds_projeto_custo']) != '' ? str_escape($args['ds_projeto_custo']) : "DEFAULT").",
               nr_valor             = ".(trim($args['nr_valor']) != '' ? floatval($args['nr_valor']) : "DEFAULT").",
               nr_valor_aprovado    = ".(trim($args['nr_valor_aprovado']) != '' ? floatval($args['nr_valor_aprovado']) : "DEFAULT").",
               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
               dt_alteracao         = CURRENT_TIMESTAMP
         WHERE cd_projeto_custo = ".intval($cd_projeto_custo).";";

    $this->db->query($qr_sql);
  }
  
  public function listar_sub_cronogramas($cd_projeto, $cd_projeto_cronograma)
  {
  	$qr_sql = "
    	  SELECT pc.ds_projeto_cronograma AS sub_cronograma,
    			     pc.cd_projeto,
    			     pc.nr_ordem,
    			     pc.cd_projeto_cronograma,
    			     pc.cd_projeto_cronograma_pai,
               TO_CHAR(pc.dt_projeto_cronograma_ini, 'MM') AS mes_planejado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_fim, 'MM') AS mes_planejado_fim,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_ini, 'MM') AS mes_realizado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_fim, 'MM') AS mes_realizado_fim,
               (SELECT cd_gerencia
                  FROM gestao.projeto_cronograma_gerencia pcg
                 WHERE pc.cd_projeto_cronograma = pcg.cd_projeto_cronograma
                 LIMIT 1) AS ds_envolvidos, 
    			     (SELECT pc2.nr_ordem 
    			        FROM gestao.projeto_cronograma pc2
    			       WHERE pc2.cd_projeto_cronograma = pc.cd_projeto_cronograma_pai
    			         AND pc2.cd_projeto = pc.cd_projeto
    			         AND pc2.dt_exclusao IS NULL) AS nr_ordem_pai
         FROM gestao.projeto_cronograma pc
    	  WHERE pc.cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma)."
    		  AND pc.cd_projeto = ".intval($cd_projeto)."
    		  AND pc.dt_exclusao IS NULL
        ORDER BY pc.nr_ordem;";
  	
  	return $this->db->query($qr_sql)->result_array();
  }
  
  public function listar_sub_cronogramas_ordem($cd_projeto_cronograma)
  {
  	$qr_sql = "
    	  SELECT pc.nr_ordem,
    			     pc.cd_projeto_cronograma_pai
    		  FROM gestao.projeto_cronograma pc
    	   WHERE pc.cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma)."
    		 AND pc.dt_exclusao IS NULL;";
  	  
  	return $this->db->query($qr_sql)->result_array();
  }
  
  public function listar_cronograma($cd_projeto, $cd_projeto_cronograma_pai = 0)
  {
    $qr_sql = "
        SELECT pc.cd_projeto,
  			       pc.nr_ordem,
  			       pc.cd_projeto_cronograma,
               pc.ds_projeto_cronograma,
  			       pc.cd_projeto_cronograma_pai,
               TO_CHAR(pc.dt_projeto_cronograma_ini, 'DD/MM/YYYY') AS dt_projeto_cronograma_ini,
               TO_CHAR(pc.dt_projeto_cronograma_fim, 'DD/MM/YYYY') AS dt_projeto_cronograma_fim,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_ini, 'DD/MM/YYYY') AS dt_projeto_cronograma_realizado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_fim, 'DD/MM/YYYY') AS dt_projeto_cronograma_realizado_fim,
               TO_CHAR(pc.dt_projeto_cronograma_ini, 'YYYY') AS ano_planejado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_ini, 'MM') AS mes_planejado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_ini, 'DD') AS dia_planejado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_fim, 'YYYY') AS ano_planejado_fim,
               TO_CHAR(pc.dt_projeto_cronograma_fim, 'MM') AS mes_planejado_fim,
               TO_CHAR(pc.dt_projeto_cronograma_fim, 'DD') AS dia_planejado_fim,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_ini, 'MM') AS mes_realizado_ini,
               TO_CHAR(pc.dt_projeto_cronograma_realizado_fim, 'MM') AS mes_realizado_fim,
               ((SELECT SUM(DATE_PART('month', age( pc0.dt_projeto_cronograma_realizado_fim ,
                                                   pc0.dt_projeto_cronograma_realizado_ini
                       )))
                  FROM gestao.projeto_cronograma pc0
                 WHERE pc0.cd_projeto_cronograma_pai IS NOT NULL
                   AND pc0.cd_projeto = pc.cd_projeto
                   AND pc0.dt_exclusao IS NULL 
                 LIMIT 1
               ) /
               (SELECT SUM(DATE_PART('month', age(pc1.dt_projeto_cronograma_fim ,pc1.dt_projeto_cronograma_ini)))
                  FROM gestao.projeto_cronograma pc1
                 WHERE ".(intval($cd_projeto_cronograma_pai) > 0 ? 
                          "pc1.cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma_pai) : 
                          "pc1.cd_projeto_cronograma_pai IS NULL"
                         )."
                  AND  pc1.cd_projeto = pc.cd_projeto
                  AND pc1.dt_exclusao IS NULL 
                 LIMIT 1
               )*100) AS andamento , 
               (SELECT cd_gerencia
                  FROM gestao.projeto_cronograma_gerencia pcg
                 WHERE pc.cd_projeto_cronograma = pcg.cd_projeto_cronograma
                 LIMIT 1) AS ds_envolvidos
  		    FROM gestao.projeto_cronograma pc
  	     WHERE ".(intval($cd_projeto_cronograma_pai) > 0 ? 
                  "pc.cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma_pai) : 
                  "pc.cd_projeto_cronograma_pai IS NULL"
               )."
  		     AND pc.cd_projeto = ".intval($cd_projeto)."
  		     AND dt_exclusao IS NULL
         ORDER BY pc.nr_ordem;";
	  
    return $this->db->query($qr_sql)->result_array();
  }

  public function andamento($cd_projeto, $cd_projeto_cronograma)
  {
    $qr_sql = "
        SELECT(SELECT count(*)
                 FROM gestao.projeto_cronograma pc0
                WHERE pc0.cd_projeto = pc.cd_projeto
                  AND pc0.dt_projeto_cronograma_realizado_fim = pc0.dt_projeto_cronograma_realizado_fim
                  AND pc.cd_projeto_cronograma_pai = pc0.cd_projeto_cronograma_pai
                  AND pc0.dt_exclusao IS NULL 
                LIMIT 1
               ) concluido,
               (SELECT count(*)
                  FROM gestao.projeto_cronograma pc1
                 WHERE pc1.cd_projeto = pc.cd_projeto
                   AND pc1.dt_projeto_cronograma_fim = pc1.dt_projeto_cronograma_fim
                   AND pc.cd_projeto_cronograma_pai = pc1.cd_projeto_cronograma_pai
                   AND pc1.dt_exclusao IS NULL 
                 LIMIT 1
               )AS previsto  
          FROM gestao.projeto_cronograma pc
         WHERE pc.cd_projeto_cronograma_pai = ".intval($cd_projeto_cronograma)."
           AND pc.cd_projeto = ".intval($cd_projeto)."
           AND dt_exclusao IS NULL
         ORDER BY pc.nr_ordem  limit 1;";

         return $this->db->query($qr_sql)->result_array();
  }

  public function carrega_cronograma($cd_projeto_cronograma)
  {
    $qr_sql = "
        SELECT cd_projeto_cronograma,
  			 nr_ordem,
               ds_projeto_cronograma,
  			 cd_projeto_cronograma_pai,
               TO_CHAR(dt_projeto_cronograma_ini, 'DD/MM/YYYY') AS dt_projeto_cronograma_ini,
               TO_CHAR(dt_projeto_cronograma_fim, 'DD/MM/YYYY') AS dt_projeto_cronograma_fim,
  			 TO_CHAR(dt_projeto_cronograma_realizado_ini, 'DD/MM/YYYY') AS dt_projeto_cronograma_realizado_ini,
               TO_CHAR(dt_projeto_cronograma_realizado_fim, 'DD/MM/YYYY') AS dt_projeto_cronograma_realizado_fim
          FROM gestao.projeto_cronograma
         WHERE cd_projeto_cronograma = ".intval($cd_projeto_cronograma).";";

    return $this->db->query($qr_sql)->row_array();
  }

  public function salvar_cronograma($args)
  {
    $cd_projeto_cronograma = intval($this->db->get_new_id('gestao.projeto_cronograma', 'cd_projeto_cronograma'));

    $qr_sql = "
        INSERT INTO gestao.projeto_cronograma
             (
    		        nr_ordem,
                cd_projeto_cronograma,
                cd_projeto,
    			      cd_projeto_cronograma_pai,
                ds_projeto_cronograma, 
                dt_projeto_cronograma_ini,
                dt_projeto_cronograma_fim,
                cd_usuario_inclusao,
                cd_usuario_alteracao
             )
        VALUES
             (
    		        ".intval($args['nr_ordem']).",
                ".intval($cd_projeto_cronograma).",
                ".intval($args['cd_projeto']).",
    			      ".(intval($args['cd_projeto_cronograma_pai']) > 0 ? intval($args['cd_projeto_cronograma_pai']) : "DEFAULT").",
                ".(trim($args['ds_projeto_cronograma']) != '' ? str_escape($args['ds_projeto_cronograma']) : "DEFAULT").",
    			      ".(trim($args['dt_projeto_cronograma_ini']) != '' ? "TO_DATE('".$args['dt_projeto_cronograma_ini']."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['dt_projeto_cronograma_fim']) != '' ? "TO_DATE('".$args['dt_projeto_cronograma_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );";
  		   if(count($args['gerencia']) > 0)
  		   {
      			$qr_sql.= "
      					INSERT INTO gestao.projeto_cronograma_gerencia
                    (
                      cd_projeto_cronograma, 
                      cd_gerencia, 
                      cd_usuario_inclusao,
                      cd_usuario_alteracao
                    )
      					SELECT ".intval($cd_projeto_cronograma).", x.column1, ".intval($args['cd_usuario']).",
                       ".intval($args['cd_usuario'])."
      					   FROM (VALUES ('".implode("'),('", $args['gerencia'])."')) x;";
  		   }
		
    $this->db->query($qr_sql);
  }

  public function atualizar_cronograma($cd_projeto_cronograma, $args)
  {
    $qr_sql = "
    		UPDATE gestao.projeto_cronograma 
    		   SET nr_ordem 				         = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
    			     cd_projeto_cronograma_pai = ".(trim($args['cd_projeto_cronograma_pai']) != '' ? intval($args['cd_projeto_cronograma_pai']) : "DEFAULT").",
    			     ds_projeto_cronograma 	   = ".(trim($args['ds_projeto_cronograma']) != '' ? str_escape($args['ds_projeto_cronograma']) : "DEFAULT").", 
    		       dt_projeto_cronograma_ini = ".(trim($args['dt_projeto_cronograma_ini']) != '' ? "TO_DATE('".$args['dt_projeto_cronograma_ini']."', 'DD/MM/YYYY')" : "DEFAULT").", 
    			     dt_projeto_cronograma_fim = ".(trim($args['dt_projeto_cronograma_fim']) != '' ? "TO_DATE('".$args['dt_projeto_cronograma_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
    			     cd_usuario_alteracao 	   = ".intval($args['cd_usuario']).", 
    			     dt_alteracao              = CURRENT_TIMESTAMP 
    		 WHERE cd_projeto_cronograma = ".intval($cd_projeto_cronograma).";
    		 
    	  UPDATE gestao.projeto_cronograma_gerencia 
    	     SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
               dt_exclusao         = CURRENT_TIMESTAMP
           WHERE cd_projeto_cronograma = ".intval($cd_projeto_cronograma)."
             AND dt_exclusao IS NULL
             AND cd_gerencia NOT IN ('".implode("','", $args['gerencia'])."');
    		 ";
    		if(count($args['gerencia']) > 0)
    		{
    			$qr_sql.= "
      				INSERT INTO gestao.projeto_cronograma_gerencia
                  (
                    cd_projeto_cronograma, 
                    cd_gerencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
      				SELECT ".intval($cd_projeto_cronograma).", x.column1, ".intval($args['cd_usuario']).", 
                     ".intval($args['cd_usuario'])."
      				  FROM (VALUES ('".implode("'),('", $args['gerencia'])."')) x
      				WHERE x.column1 NOT IN (
                                        SELECT a.cd_gerencia
        										              FROM gestao.projeto_cronograma_gerencia a
        										             WHERE a.cd_projeto_cronograma = ".intval($cd_projeto_cronograma)."
        										            	 AND a.dt_exclusao IS NULL
                                     );";
    		}
        
	  $this->db->query($qr_sql);
  }
  
  public function excluir_cronograma($cd_projeto, $cd_projeto_cronograma, $cd_usuario)
  {
    $qr_sql = "
		UPDATE gestao.projeto_cronograma 
		   SET dt_exclusao         = CURRENT_TIMESTAMP,
			     cd_usuario_exclusao = ".intval($cd_usuario)."
		 WHERE cd_projeto_cronograma = ".intval($cd_projeto_cronograma)."
		   AND cd_projeto = ".intval($cd_projeto).";
		 
	  UPDATE gestao.projeto_cronograma_gerencia 
	     SET cd_usuario_exclusao = ".intval($cd_usuario).",
             dt_exclusao         = CURRENT_TIMESTAMP
       WHERE cd_projeto_cronograma  = ".intval($cd_projeto_cronograma)."
         AND dt_exclusao IS NULL;";
    
	 $this->db->query($qr_sql);
  }

  public function atualizar_cronograma_realizado($cd_projeto_cronograma, $args)
  {
    $qr_sql = "
		UPDATE gestao.projeto_cronograma 
		   SET dt_projeto_cronograma_realizado_ini = ".(trim($args['dt_projeto_cronograma_realizado_ini']) != '' ? 
              "TO_DATE('".$args['dt_projeto_cronograma_realizado_ini']."', 'DD/MM/YYYY')" : "DEFAULT").", 
			     dt_projeto_cronograma_realizado_fim = ".(trim($args['dt_projeto_cronograma_realizado_fim']) != '' ? "TO_DATE('".$args['dt_projeto_cronograma_realizado_fim']."', 'DD/MM/YYYY')" : "DEFAULT").",
			     cd_usuario_alteracao 	             = ".intval($args['cd_usuario']).", 
			     dt_alteracao                        = CURRENT_TIMESTAMP 
		 WHERE cd_projeto_cronograma 	= ".intval($cd_projeto_cronograma).";";

    $this->db->query($qr_sql);
  }
}
?>