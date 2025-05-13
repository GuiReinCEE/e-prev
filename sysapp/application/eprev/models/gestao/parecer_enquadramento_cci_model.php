<?php
class parecer_enquadramento_cci_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT cd_parecer_enquadramento_cci,
			       gestao.nr_parecer_enquadramento_cci(nr_ano, nr_numero) AS nr_ano_numero,
			       descricao,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(COALESCE(dt_limite_prorrogacao, dt_limite), 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(dt_encerrado, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrado,
				   CASE WHEN dt_envio IS NULL THEN 'Não Enviado'
				        WHEN dt_envio IS NOT NULL AND dt_encerrado IS NULL THEN 'Enviado'
					    ELSE 'Encerrado'
				   END AS situacao,
				   CASE WHEN dt_envio IS NULL THEN 'label label-important'
				        WHEN dt_envio IS NOT NULL AND dt_encerrado IS NULL THEN 'label label-success'
					    ELSE 'label'
				   END AS cor_situacao,
				   TO_CHAR(dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento
			  FROM gestao.parecer_enquadramento_cci
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['nr_ano']) != '' ? "AND nr_ano = ".intval($args['nr_ano']) : "")."
			 ".(trim($args['nr_numero']) != '' ? "AND nr_numero = ".intval($args['nr_numero']) : "")."
			 ".(trim($args['cd_usuario_inclusao']) != '' ? "AND cd_usuario_inclusao = ".intval($args['cd_usuario_inclusao']) : "")."
			 ".(((trim($args['dt_inclusao_ini']) != "") AND  (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_envio_ini']) != "") AND  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_limite_ini']) != "") AND  (trim($args['dt_limite_fim']) != "")) ? " AND DATE_TRUNC('day', dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_encerrado_ini']) != "") AND  (trim($args['dt_encerrado_fim']) != "")) ? " AND DATE_TRUNC('day', dt_encerrado) BETWEEN TO_DATE('".$args['dt_encerrado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerrado_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args['fl_situacao']) == 'N' ? "AND dt_envio IS NULL" : "")."
			 ".(trim($args['fl_situacao']) == 'E' ? "AND dt_envio IS NOT NULL AND dt_encerrado IS NULL" : "")."
			 ".(trim($args['fl_situacao']) == 'R' ? "AND dt_encerrado IS NOT NULL" : "")."
			 ".(trim($args['descricao']) != '' ? "AND UPPER(funcoes.remove_acento(descricao)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['descricao'])."%')))" : "")."
			 ".(trim($args['fl_cancelar']) == 'N' ? "AND dt_cancelamento IS NULL" : "")."
			 ".(trim($args['fl_cancelar']) == 'S' ? "AND dt_cancelamento IS NOT NULL" : "").";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_cadastro(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.nome AS text,
			       uc.codigo AS value
			  FROM projetos.usuarios_controledi uc
			  JOIN gestao.parecer_enquadramento_cci pec
			    ON pec.cd_usuario_inclusao = uc.codigo
			 WHERE pec.dt_exclusao IS NULL";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pec.cd_parecer_enquadramento_cci,
			       pec.descricao,
				   gestao.nr_parecer_enquadramento_cci(pec.nr_ano, pec.nr_numero) AS nr_ano_numero,
				   TO_CHAR(pec.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(pec.dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(pec.dt_limite_prorrogacao, 'DD/MM/YYYY') AS dt_limite_prorrogacao,
				   TO_CHAR(pec.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(pec.dt_encerrado, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrado,
				   uc1.nome AS usuario_cadastro,
				   uc2.nome AS usuario_envio,
				   uc3.nome AS usuario_encerrado,
				   pec.dt_cancelamento AS dt_cancelamento_fl,
				   TO_CHAR(dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(pec.cd_usuario_cancelamento) AS ds_usuario_cancelamento,
				   pec.ds_justificativa_cancelamento
			  FROM gestao.parecer_enquadramento_cci pec
			  JOIN projetos.usuarios_controledi uc1
			    ON uc1.codigo = pec.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = pec.cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi uc3
			    ON uc3.codigo = pec.cd_usuario_encerrado
			 WHERE pec.cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_parecer_enquadramento_cci']) == 0)
		{
			$cd_parecer_enquadramento_cci = intval($this->db->get_new_id("gestao.parecer_enquadramento_cci", "cd_parecer_enquadramento_cci"));
			
			$qr_sql = "
				INSERT INTO gestao.parecer_enquadramento_cci
				     (
					    cd_parecer_enquadramento_cci,
						descricao,
						dt_limite,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
					    ".intval($cd_parecer_enquadramento_cci).",
						".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
						".(trim($args['dt_limite']) != '' ? "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_parecer_enquadramento_cci = intval($args['cd_parecer_enquadramento_cci']);
			
			$qr_sql = "
				UPDATE gestao.parecer_enquadramento_cci
				   SET descricao            = ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
				       dt_limite            = ".(trim($args['dt_limite']) != '' ? "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
				       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci']).";";
		}
		
		$this->db->query($qr_sql);
		
		return $cd_parecer_enquadramento_cci;
	}

	public function salvar_prorrogacao($cd_parecer_enquadramento_cci, $dt_limite_prorrogacao, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.parecer_enquadramento_cci
			   SET dt_limite_prorrogacao = ".(trim($dt_limite_prorrogacao) != '' ? "TO_DATE('".trim($dt_limite_prorrogacao)."','DD/MM/YYYY')" : "DEFAULT").",
			       cd_usuario_alteracao  = ".intval($cd_usuario).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_parecer_enquadramento_cci = ".intval($cd_parecer_enquadramento_cci).";";

		$this->db->query($qr_sql);
	}
	
	function enviar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.parecer_enquadramento_cci
			   SET cd_usuario_envio = ".intval($args['cd_usuario']).",
				   dt_envio         = CURRENT_TIMESTAMP
			 WHERE cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function encerrar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.parecer_enquadramento_cci
			   SET cd_usuario_encerrado = ".intval($args['cd_usuario']).",
				   dt_encerrado         = CURRENT_TIMESTAMP
			 WHERE cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT an.cd_parecer_enquadramento_cci_anexo,
			       an.arquivo,
				   an.arquivo_nome,
				   uc.nome,
				   TO_CHAR(an.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.parecer_enquadramento_cci_anexo an
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = an.cd_usuario_inclusao
			 WHERE an.dt_exclusao IS NULL
			   AND an.cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci'])."";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.parecer_enquadramento_cci_anexo
			     (
					cd_parecer_enquadramento_cci,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_parecer_enquadramento_cci']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.parecer_enquadramento_cci_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_parecer_enquadramento_cci_anexo = ".intval($args['cd_parecer_enquadramento_cci_anexo']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	public function salva_cancelamento($args=array())
	{
		$qr_sql = "
			UPDATE gestao.parecer_enquadramento_cci
			   SET ds_justificativa_cancelamento = ".(trim($args['ds_justificativa_cancelamento']) != '' ? str_escape($args['ds_justificativa_cancelamento']) : "DEFAULT").",
			   	   cd_usuario_cancelamento       = ".intval($args['cd_usuario']).",
				   dt_cancelamento               = CURRENT_TIMESTAMP
			 WHERE cd_parecer_enquadramento_cci = ".intval($args['cd_parecer_enquadramento_cci']).";";

		$this->db->query($qr_sql);
	}
}
?>