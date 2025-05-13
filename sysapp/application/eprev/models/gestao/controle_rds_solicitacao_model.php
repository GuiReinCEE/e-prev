<?php
class Controle_rds_solicitacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_controle_rds_solicitacao,
  				   ds_controle_rds_solicitacao,
  				   cd_gerencia,
  				   TO_CHAR(dt_controle_rds_solicitacao, 'DD/MM/YYYY') AS dt_controle_rds_solicitacao,
  				   gestao.nr_controle_rds_solicitacao(nr_ano, nr_numero) AS nr_controle_rds_solicitacao
			  FROM gestao.controle_rds_solicitacao
			 WHERE dt_exclusao IS NULL
			   ".(intval($args['nr_ano']) != '' ? "AND nr_ano =" .intval($args['nr_ano']) : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_controle_rds_solicitacao)
	{
		$qr_sql = "
			SELECT cd_controle_rds_solicitacao,
  				   ds_controle_rds_solicitacao,
  				   TO_CHAR(dt_controle_rds_solicitacao, 'DD/MM/YYYY') AS dt_controle_rds_solicitacao,
  				   gestao.nr_controle_rds_solicitacao(nr_ano, nr_numero) AS nr_controle_rds_solicitacao,
  				   cd_gerencia
			  FROM gestao.controle_rds_solicitacao
			 WHERE dt_exclusao IS NULL
			   AND cd_controle_rds_solicitacao = ".intval($cd_controle_rds_solicitacao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_controle_rds_solicitacao = $this->db->get_new_id('gestao.controle_rds_solicitacao', 'cd_controle_rds_solicitacao');

		$qr_sql = "
			INSERT INTO gestao.controle_rds_solicitacao
				(
					cd_controle_rds_solicitacao,
					ds_controle_rds_solicitacao,
					dt_controle_rds_solicitacao,
					cd_gerencia,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".intval($cd_controle_rds_solicitacao).",
					".(trim($args['ds_controle_rds_solicitacao']) != '' ? str_escape($args['ds_controle_rds_solicitacao']) : "DEFAULT").",
					".(trim($args['dt_controle_rds_solicitacao']) != '' ? "TO_DATE('".trim($args['dt_controle_rds_solicitacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);

		return $cd_controle_rds_solicitacao;
	}

	public function atualizar($cd_controle_rds_solicitacao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.controle_rds_solicitacao
			   SET ds_controle_rds_solicitacao = ".(trim($args['ds_controle_rds_solicitacao']) != '' ? str_escape($args['ds_controle_rds_solicitacao']) : "DEFAULT").",
				   dt_controle_rds_solicitacao = ".(trim($args['dt_controle_rds_solicitacao']) != '' ? "TO_DATE('".trim($args['dt_controle_rds_solicitacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   cd_gerencia 				   = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
				   cd_usuario_alteracao        = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_controle_rds_solicitacao = ".intval($cd_controle_rds_solicitacao).";";

		$this->db->query($qr_sql);
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente('DIV');";

		return $this->db->query($qr_sql)->result_array();
	}
}