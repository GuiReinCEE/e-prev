<?php

class contrato_avaliacao_resposta_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function verificacoes_contrato_avaliacao(&$result, $args=array())
    {
		$qr_sql = "
			SELECT COUNT(*) tl,
				   (SELECT COUNT(*) 
					  FROM projetos.contrato_avaliacao ca
					 WHERE ca.dt_limite_avaliacao   >= CURRENT_DATE
					   AND ca.dt_exclusao           IS NULL
					   AND ca.cd_contrato_avaliacao = cai.cd_contrato_avaliacao
					   AND 0 < (SELECT COUNT(*) 
								  FROM projetos.contrato_avaliacao_item cai2
								 WHERE cai2.cd_contrato_avaliacao =  ca.cd_contrato_avaliacao
								   AND cai2.dt_exclusao           IS NULL
								   AND cai2.cd_usuario_avaliador  = cai.cd_usuario_avaliador)) AS fl_limite,
				   (SELECT COUNT(*) AS fl_avaliou
					  FROM projetos.contrato_avaliacao_resposta car
					 WHERE car.cd_contrato_avaliacao_item IN(SELECT cai2.cd_contrato_avaliacao_item
															   FROM projetos.contrato_avaliacao_item cai2
															  WHERE cai2.cd_contrato_avaliacao = cai.cd_contrato_avaliacao
																AND cai2.dt_exclusao           IS NULL
																AND cai2.cd_usuario_avaliador  = cai.cd_usuario_avaliador)) AS fl_avaliou
			  FROM projetos.contrato_avaliacao_item cai
			 WHERE cai.dt_exclusao           IS NULL
			   AND cai.cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao'])."
			   AND cai.cd_usuario_avaliador  = ".intval($args['cd_usuario'])."
			 GROUP BY cai.cd_contrato_avaliacao, cai.cd_usuario_avaliador;";

		$result = $this->db->query($qr_sql);
	}
	
	function contrato_avaliacao(&$result, $args=array())
    {
		$qr_sql = "
			SELECT c.ds_empresa,
				   c.ds_servico,
				   TO_CHAR(ca.dt_inicio_avaliacao,'MM/YYYY') AS dt_ini,
				   TO_CHAR(ca.dt_fim_avaliacao,'MM/YYYY') AS dt_fim,
				   TO_CHAR(ca.dt_limite_avaliacao,'DD/MM/YYYY') AS dt_limite_avaliacao
			  FROM projetos.contrato_avaliacao ca
			  JOIN projetos.contrato c
				ON c.cd_contrato = ca.cd_contrato
			 WHERE ca.dt_limite_avaliacao   >= CURRENT_DATE
			   AND ca.dt_exclusao           IS NULL
			   AND ca.cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao'])."
			   AND 0 < (SELECT COUNT(*) 
						  FROM projetos.contrato_avaliacao_item cai
					     WHERE cai.cd_contrato_avaliacao =  ca.cd_contrato_avaliacao
						   AND cai.dt_exclusao           IS NULL
						   AND cai.cd_usuario_avaliador  = ".intval($args['cd_usuario']).");";
	
		$result = $this->db->query($qr_sql);
	}	
	
	function grupos_perguntas(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cai.cd_contrato_avaliacao_item,
				   cai.cd_divisao,
				   cai.cd_usuario_avaliador,
				   cfg.cd_contrato_formulario_grupo,
				   cfg.ds_contrato_formulario_grupo
			  FROM projetos.contrato_avaliacao_item cai
			  JOIN projetos.contrato_avaliacao ca
				ON ca.cd_contrato_avaliacao = cai.cd_contrato_avaliacao
			  JOIN projetos.contrato_formulario cf
				ON cf.cd_contrato_formulario = ca.cd_contrato_formulario
			   AND cf.dt_exclusao IS NULL
			  JOIN projetos.contrato_formulario_grupo cfg
				ON cfg.cd_contrato_formulario       = ca.cd_contrato_formulario
			   AND cfg.cd_contrato_formulario_grupo = cai.cd_contrato_formulario_grupo
			 WHERE ca.dt_limite_avaliacao   >= CURRENT_DATE
			   AND ca.dt_exclusao           IS NULL
			   AND cai.dt_exclusao          IS NULL
			   AND cf.dt_exclusao           IS NULL
			   AND cfg.dt_exclusao          IS NULL
			   AND ca.cd_contrato_avaliacao = ".intval($args['cd_contrato_avaliacao'])."
			   AND cai.cd_usuario_avaliador =".intval($args['cd_usuario'])."
			 ORDER BY cfg.nr_ordem ASC;";
	
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_contrato_formulario_pergunta,
				   ds_contrato_formulario_pergunta
			  FROM projetos.contrato_formulario_pergunta
			 WHERE dt_exclusao                  IS NULL
			   AND cd_contrato_formulario_grupo = ".intval($args['cd_contrato_formulario_grupo'])."
			 ORDER BY nr_ordem ASC;";

		$result = $this->db->query($qr_sql);
	}
	
	function respostas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_contrato_formulario_resposta,  
				   ds_resposta
			  FROM projetos.contrato_formulario_resposta
			 WHERE dt_exclusao                  IS NULL
			   AND cd_contrato_formulario_pergunta = ".intval($args['cd_contrato_formulario_pergunta'])."
			 ORDER BY nr_ordem ASC;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.contrato_avaliacao_resposta
				 (
				   cd_contrato_avaliacao_item, 
				   cd_contrato_formulario_resposta,
				   cd_usuario_inclusao
				 )
			VALUES 
				 (
				   ".intval($args['cd_contrato_formulario_pergunta']).",
				   ".intval($args['cd_contrato_formulario_resposta']).",
				   ".intval($args['cd_usuario'])."
				 );";
	
		$result = $this->db->query($qr_sql);
	}
}

?>