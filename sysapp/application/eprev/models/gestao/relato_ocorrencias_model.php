<?php
class Relato_ocorrencias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_permissao($cd_relato_ocorrencias, $cd_usuario)
	{
		$qr_sql = "
			SELECT COUNT(*) AS qt_relato
			  FROM gestao.relato_ocorrencias
			 WHERE dt_exclusao IS NULL
			   AND cd_relato_ocorrencias = ".intval($cd_relato_ocorrencias)."
			   AND cd_usuario_inclusao   = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT cd_relato_ocorrencias,
				   gestao.nr_relato_ocorrencias(nr_numero_relato_ocorrencias, nr_ano_relato_ocorrencias) AS nr_ano_numero_relato_ocorrencia,
				   ds_relato_ocorrencias,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
				   TO_CHAR(dt_verificacao, 'DD/MM/YYYY') AS dt_verificacao,
				   funcoes.get_usuario_nome(cd_usuario_verificacao) AS ds_usuario_verificacao,
				   ds_verificacao
			  FROM gestao.relato_ocorrencias
			 WHERE dt_exclusao IS NULL
			   AND (cd_usuario_inclusao = ".intval($cd_usuario)." OR (SELECT COUNT(*)
			 														    FROM projetos.usuarios_controledi
			 														   WHERE indic_12 = '*'
			 														     AND codigo   = ".intval($cd_usuario).") > 0)
 			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
 			   ".(((trim($args['dt_verificacao_ini']) != '') AND (trim($args['dt_verificacao_fim']) != '')) ? "AND DATE_TRUNC('day', dt_verificacao) BETWEEN TO_DATE('".$args['dt_verificacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_verificacao_fim']."', 'DD/MM/YYYY')" : "")."
 			   ".(trim($args['fl_verificado']) == 'S' ? "AND dt_verificacao IS NOT NULL" : "")."
 			   ".(trim($args['fl_verificado']) == 'N' ? "AND dt_verificacao IS NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_relato_ocorrencias)
	{
		$qr_sql = "
			SELECT cd_relato_ocorrencias,
				   gestao.nr_relato_ocorrencias(nr_numero_relato_ocorrencias, nr_ano_relato_ocorrencias) AS nr_ano_numero_relato_ocorrencia,
				   ds_relato_ocorrencias,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
				   cd_usuario_inclusao,
				   TO_CHAR(dt_verificacao, 'DD/MM/YYYY') AS dt_verificacao,
				   funcoes.get_usuario_nome(cd_usuario_verificacao) AS ds_usuario_verificacao,
				   ds_verificacao
			  FROM gestao.relato_ocorrencias
			 WHERE cd_relato_ocorrencias = ".intval($cd_relato_ocorrencias).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_relato_ocorrencias = intval($this->db->get_new_id('gestao.relato_ocorrencias', 'cd_relato_ocorrencias'));

		$qr_sql = "
			INSERT INTO gestao.relato_ocorrencias
				(
					cd_relato_ocorrencias,
					ds_relato_ocorrencias,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".intval($cd_relato_ocorrencias).",
					".(trim($args['ds_relato_ocorrencias']) != '' ? str_escape($args['ds_relato_ocorrencias']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);

		return $cd_relato_ocorrencias;
	}

	public function atualizar($cd_relato_ocorrencias, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.relato_ocorrencias
			   SET ds_relato_ocorrencias = ".(trim($args['ds_relato_ocorrencias']) != '' ? str_escape($args['ds_relato_ocorrencias']) : "DEFAULT").",
			   	   dt_alteracao 		 = CURRENT_TIMESTAMP,
				   cd_usuario_alteracao  = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_relato_ocorrencias = ".intval($cd_relato_ocorrencias).";";

		$this->db->query($qr_sql);
	}

	public function salvar_verificacao($cd_relato_ocorrencias, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.relato_ocorrencias
			   SET dt_verificacao 		   = ".(trim($args['dt_verificacao']) != '' ? "TO_DATE('".trim($args['dt_verificacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ds_verificacao  		   = ".(trim($args['ds_verificacao']) != '' ? str_escape($args['ds_verificacao']) : "DEFAULT").",
				   cd_usuario_verificacao  = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_relato_ocorrencias = ".intval($cd_relato_ocorrencias).";";

		$this->db->query($qr_sql);
	}

	public function lista_anexo($cd_relato_ocorrencias)
	{
		$qr_sql = "
			SELECT an.cd_relato_ocorrencias_anexo,
			       an.arquivo,
				   an.arquivo_nome,
				   funcoes.get_usuario_nome(an.cd_usuario_inclusao) AS ds_nome_usuario,
				   TO_CHAR(an.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   an.cd_usuario_inclusao
			  FROM gestao.relato_ocorrencias_anexo an
			 WHERE an.dt_exclusao IS NULL
			   AND an.cd_relato_ocorrencias = ".intval($cd_relato_ocorrencias).";";
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($cd_relato_ocorrencias, $cd_usuario, $args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.relato_ocorrencias_anexo
			     (
					cd_relato_ocorrencias,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($cd_relato_ocorrencias).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($cd_usuario)."
				 )";

		$this->db->query($qr_sql);
	}

	public function excluir_anexo($cd_relato_ocorrencias_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.relato_ocorrencias_anexo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_relato_ocorrencias_anexo = ".intval($cd_relato_ocorrencias_anexo).";";
		     
		$this->db->query($qr_sql);
	}
}