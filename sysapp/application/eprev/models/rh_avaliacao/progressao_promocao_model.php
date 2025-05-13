<?php
class Progressao_promocao_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT uc.codigo AS cd_usuario,
				   uc.avatar,
				   uc.usuario,
			       uc.divisao AS ds_gerencia,
			       uc.nome AS ds_nome,
			       TO_CHAR(uc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
			       COALESCE((SELECT pp.cd_progressao_promocao
					           FROM rh_avaliacao.progressao_promocao pp
					          WHERE pp.dt_exclusao IS NULL
					            AND pp.cd_usuario = uc.codigo
					          ORDER BY pp.dt_progressao_promocao DESC 
					 		  LIMIT 1), 0) AS cd_progressao_promocao,
			       '' AS ds_cargo_area_atuacao,
			       '' AS ds_classe,
			       '' AS dt_progressao_promocao
			  FROM projetos.usuarios_controledi uc 
			  JOIN projetos.divisoes d
			    ON d.codigo = uc.divisao
			 WHERE uc.divisao NOT IN ('SNG', 'LM2') 
			   AND uc.tipo    NOT IN ('X','T', 'E', 'D', 'P', 'A')
			   ".(trim($args['cd_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_gerencia'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerencia_usuario()
	{
		$qr_sql = "
			SELECT uc.divisao AS value,
			       uc.divisao || ' - ' || d.nome AS text
			  FROM projetos.usuarios_controledi uc 
			  JOIN projetos.divisoes d
			    ON d.codigo = uc.divisao
			 WHERE uc.divisao NOT IN ('SNG', 'LM2') 
			   AND uc.tipo    NOT IN ('X','T', 'E', 'D', 'P', 'A') 
			 GROUP BY value, text
			 ORDER BY uc.divisao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_usuario)
	{
		$qr_sql = "
			SELECT 0 AS cd_progressao_promocao,
			       uc.codigo AS cd_usuario,
			       uc.avatar,
			       uc.usuario,
			       uc.divisao AS cd_gerencia,
			       uc.divisao || ' - ' || d.nome AS ds_gerencia,
			       uc.nome AS ds_nome,
			       TO_CHAR(uc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
			       pp.cd_cargo_area_atuacao,  
			       pp.cd_classe,	
			       pp.cd_classe_padrao,
			       '' AS dt_progressao_promocao
			  FROM projetos.usuarios_controledi uc
			  LEFT JOIN rh_avaliacao.progressao_promocao pp
			    ON pp.cd_usuario = uc.codigo
			   AND pp.dt_exclusao IS NULL
			  JOIN projetos.divisoes d
			    ON d.codigo = uc.divisao         
			 WHERE uc.codigo = ".intval($cd_usuario)."
			 ORDER BY pp.dt_progressao_promocao DESC 
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_area_atuacao($cd_gerencia)
	{
		$qr_sql = "
			SELECT caa.cd_cargo_area_atuacao AS value,
			       caa.cd_gerencia || ' - ' || cr.ds_cargo || (CASE WHEN aa.ds_area_atuacao IS NOT NULL THEN ' - ' || aa.ds_area_atuacao ELSE '' END) AS text
			  FROM rh_avaliacao.cargo_area_atuacao caa
			  JOIN rh_avaliacao.cargo cr
			    ON cr.cd_cargo = caa.cd_cargo
	          LEFT JOIN rh_avaliacao.area_atuacao aa
			    ON aa.cd_area_atuacao = caa.cd_area_atuacao
			 WHERE caa.dt_exclusao IS NULL
			   AND cr.dt_exclusao  IS NULL
			   AND aa.dt_exclusao  IS NULL
			   AND caa.cd_gerencia = '".trim($cd_gerencia)."'
			 ORDER BY caa.cd_gerencia, 
			          cr.ds_cargo, 
			          aa.ds_area_atuacao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_classe($cd_cargo_area_atuacao)
    {
        $qr_sql = "
            SELECT cl.cd_classe AS value,
                   TRIM(cg.ds_cargo || CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) AS text
              FROM rh_avaliacao.classe cl
              JOIN rh_avaliacao.cargo cg
                ON cg.cd_cargo = cl.cd_cargo
             WHERE cl.dt_exclusao IS NULL
               AND cg.dt_exclusao IS NULL
               AND cg.cd_cargo = (SELECT caa.cd_cargo
                                    FROM rh_avaliacao.cargo_area_atuacao caa
			                       WHERE caa.dt_exclusao IS NULL
			                         AND caa.cd_cargo_area_atuacao = ".intval($cd_cargo_area_atuacao).")
             ORDER BY text ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_classe_padrao($cd_classe)
    {
    	$qr_sql = "
            SELECT cd_classe_padrao AS value,
                   ds_padrao AS text
              FROM rh_avaliacao.classe_padrao
             WHERE dt_exclusao IS NULL
               AND cd_classe = ".intval($cd_classe)."
             ORDER BY ds_padrao;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_progressao_promocao($cd_progressao_promocao)
    {
    	$qr_sql = "
			SELECT pp.cd_progressao_promocao,
			       pp.cd_cargo_area_atuacao,  
			       pp.cd_classe,	
			       pp.cd_classe_padrao,
			       TO_CHAR(pp.dt_progressao_promocao, 'DD/MM/YYYY') AS dt_progressao_promocao,
			       uc.codigo AS cd_usuario,
			       uc.avatar,
			       uc.usuario,
			       uc.divisao AS cd_gerencia,
			       uc.divisao || ' - ' || d.nome AS ds_gerencia,
			       uc.nome AS ds_nome,
			       TO_CHAR(uc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
			       caa.cd_gerencia || ' - ' || cr.ds_cargo || (CASE WHEN aa.ds_area_atuacao IS NOT NULL THEN ' - ' || aa.ds_area_atuacao ELSE '' END) AS ds_cargo_area_atuacao,
				   TRIM(cr.ds_cargo || (CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) || (CASE WHEN ds_padrao IS NOT NULL THEN ' - ' || ds_padrao ELSE '' END)) AS ds_classe,
				   ds_padrao
			  FROM rh_avaliacao.progressao_promocao PP
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = pp.cd_usuario
			  JOIN projetos.divisoes d
			    ON d.codigo = uc.divisao  
			  JOIN rh_avaliacao.cargo_area_atuacao caa
			    ON caa.cd_cargo_area_atuacao = pp.cd_cargo_area_atuacao
			  JOIN rh_avaliacao.cargo cr
				ON cr.cd_cargo = caa.cd_cargo
			  JOIN rh_avaliacao.classe cl
				ON cl.cd_classe = pp.cd_classe
			  LEFT JOIN rh_avaliacao.area_atuacao aa
			    ON aa.cd_area_atuacao = caa.cd_area_atuacao
			  LEFT JOIN rh_avaliacao.classe_padrao cp
				ON cp.cd_classe_padrao = pp.cd_classe_padrao
			 WHERE pp.dt_exclusao IS NULL
			   AND cd_progressao_promocao = ".intval($cd_progressao_promocao).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function listar_progressao_promocao($cd_usuario)
    {
    	$qr_sql = "
				SELECT pp.cd_progressao_promocao,
				       TO_CHAR(pp.dt_progressao_promocao, 'DD/MM/YYYY') AS dt_progressao_promocao,
				       caa.cd_gerencia || ' - ' || cr.ds_cargo || (CASE WHEN aa.ds_area_atuacao IS NOT NULL THEN ' - ' || aa.ds_area_atuacao ELSE '' END) AS ds_cargo_area_atuacao,
				       TRIM(cr.ds_cargo || (CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END)) AS ds_classe,
				       ds_padrao
				  FROM rh_avaliacao.progressao_promocao pp
				  JOIN rh_avaliacao.cargo_area_atuacao caa
				    ON caa.cd_cargo_area_atuacao = pp.cd_cargo_area_atuacao
				  JOIN rh_avaliacao.cargo cr
				    ON cr.cd_cargo = caa.cd_cargo
				  LEFT JOIN rh_avaliacao.area_atuacao aa
				    ON aa.cd_area_atuacao = caa.cd_area_atuacao
				  JOIN rh_avaliacao.classe cl
				    ON cl.cd_classe = pp.cd_classe
				  LEFT JOIN rh_avaliacao.classe_padrao cp
				    ON cp.cd_classe_padrao = pp.cd_classe_padrao
				 WHERE pp.dt_exclusao  IS NULL
				   AND pp.cd_usuario   = ".intval($cd_usuario)."
				   AND caa.dt_exclusao IS NULL
				   AND cr.dt_exclusao  IS NULL
				   AND aa.dt_exclusao  IS NULL
				   AND cl.dt_exclusao  IS NULL
				   AND cp.dt_exclusao  IS NULL
				 ORDER BY pp.dt_progressao_promocao DESC;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($cd_usuario, $args = array())
    {
    	$qr_sql = "
    		INSERT INTO rh_avaliacao.progressao_promocao
    			(
    				cd_usuario,
    				cd_cargo_area_atuacao,
    				cd_classe,
    				cd_classe_padrao,
    				dt_progressao_promocao,
    				cd_usuario_inclusao,
    				cd_usuario_alteracao
    			)
    		VALUES
    			(
    				".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
    				".(intval($args['cd_cargo_area_atuacao']) > 0 ? intval($args['cd_cargo_area_atuacao']) : "DEFAULT").",
    				".(intval($args['cd_classe']) > 0 ? intval($args['cd_classe']) : "DEFAULT").",
    				".(intval($args['cd_classe_padrao']) > 0 ? intval($args['cd_classe_padrao']) : "DEFAULT").",
    				".(trim($args['dt_progressao_promocao']) != '' ? "TO_DATE('".trim($args['dt_progressao_promocao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    				".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
    			);";

    	$this->db->query($qr_sql);
    }

    public function atualizar($cd_progressao_promocao, $args = array())
    {
    	$qr_sql = "
    		UPDATE rh_avaliacao.progressao_promocao
    		   SET cd_cargo_area_atuacao  = ".(intval($args['cd_cargo_area_atuacao']) > 0 ? intval($args['cd_cargo_area_atuacao']) : "DEFAULT").",
    			   cd_classe 			  = ".(intval($args['cd_classe']) > 0 ? intval($args['cd_classe']) : "DEFAULT").",
    			   cd_classe_padrao 	  = ".(intval($args['cd_classe_padrao']) > 0 ? intval($args['cd_classe_padrao']) : "DEFAULT").",
    			   dt_progressao_promocao = ".(trim($args['dt_progressao_promocao']) != '' ? "TO_DATE('".trim($args['dt_progressao_promocao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
    			   cd_usuario_alteracao   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
    			   dt_alteracao 		  = CURRENT_TIMESTAMP
    		 WHERE cd_progressao_promocao = ".intval($cd_progressao_promocao).";";

    	$this->db->query($qr_sql);
    }
}