<?php
class Ocorrencia_ponto_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar_ocorrencia($cd_usuario)
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
			   AND op.cd_usuario   = ".intval($cd_usuario)."
			   AND opt.dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_ocorrencia_ponto)
	{
		$qr_sql = "
			SELECT cd_ocorrencia_ponto,
				   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   cd_ocorrencia_ponto_tipo,
				   nr_quantidade
			  FROM rh_avaliacao.ocorrencia_ponto
			 WHERE dt_exclusao IS NULL
			   AND cd_ocorrencia_ponto = ".intval($cd_ocorrencia_ponto).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.ocorrencia_ponto
				(
					cd_usuario,
				   	dt_referencia,
				   	cd_ocorrencia_ponto_tipo,
				   	nr_quantidade,
				   	cd_usuario_inclusao,
				   	cd_usuario_alteracao
				)
			VALUES
				(
					".(intval($args['cd_usuario_cadastro']) > 0 ? intval($args['cd_usuario_cadastro']) : "DEFAULT").",
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(intval($args['cd_ocorrencia_ponto_tipo']) > 0 ? intval($args['cd_ocorrencia_ponto_tipo']) : "DEFAULT").",
					".(intval($args['nr_quantidade']) > 0 ? intval($args['nr_quantidade']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_ocorrencia_ponto, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.ocorrencia_ponto
			   SET dt_referencia 			= ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   cd_ocorrencia_ponto_tipo = ".(intval($args['cd_ocorrencia_ponto_tipo']) > 0 ? intval($args['cd_ocorrencia_ponto_tipo']) : "DEFAULT").",
				   nr_quantidade 			= ".(intval($args['nr_quantidade']) > 0 ? intval($args['nr_quantidade']) : "DEFAULT").",
				   dt_alteracao 			= CURRENT_TIMESTAMP,
				   cd_usuario_alteracao		= ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_ocorrencia_ponto = ".intval($cd_ocorrencia_ponto).";";

		$this->db->query($qr_sql);
	}
}