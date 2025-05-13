<?php
class Pauta_sg_integrante_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT psi.cd_pauta_sg_integrante,
			       psi.ds_pauta_sg_integrante,
			       psi.fl_tipo,
			       psi.cd_pauta_sg_integrante_titular,
			       psi.fl_secretaria,
			       psi.fl_presidente,
			       psi.fl_colegiado,
			       TO_CHAR(psi.dt_removido, 'DD/MM/YYYY HH24:MI:SS') AS dt_removido,
			       (CASE WHEN psi.fl_indicado_eleito = 'I' THEN 'Indicado'
			             WHEN psi.fl_indicado_eleito = 'E' THEN 'Eleito'
				     ELSE ''
			        END) AS ds_indicado_eleito,
			       (CASE WHEN psi.fl_tipo = 'S' THEN 'Suplente'
				     ELSE 'Titular'
			        END) AS ds_tipo,
			       (CASE WHEN psi.fl_secretaria = 'S' THEN 'Sim'
				     ELSE 'Não'
			        END) AS ds_secretaria,
			       (CASE WHEN psi.fl_secretaria = 'S' THEN 'label label-warning'
				     ELSE 'label label-disabled'
			        END) AS ds_label_secretaria,
			       (CASE WHEN psi.fl_presidente = 'S' THEN 'Sim'
				     ELSE 'Não'
			        END) AS ds_presidente,
			       (CASE WHEN psi.fl_presidente = 'S' THEN 'label label-warning'
				     ELSE 'label label-disabled'
			        END) AS ds_label_presidente,
			       (CASE WHEN psi.fl_colegiado = 'DE' THEN 'Diretoria Executiva'
				     WHEN psi.fl_colegiado = 'CD' THEN 'Conselho Deliberativo'
				     ELSE 'Conselho Fiscal'
			        END) AS ds_colegiado,
			       (CASE WHEN psi.dt_removido IS NULL THEN 'Não'
				     ELSE 'Sim'
			        END) AS ds_removido,
			       (CASE WHEN psi.dt_removido IS NULL THEN 'label label-success'
				     ELSE 'label label-important'
			        END) AS ds_label,
			       psi2.ds_pauta_sg_integrante AS ds_pauta_sg_integrante_titular,
				   psi.email,
				   psi.celular,
				   psi.cargo

			  FROM gestao.pauta_sg_integrante psi
			  LEFT JOIN gestao.pauta_sg_integrante psi2
			    ON psi2.cd_pauta_sg_integrante = psi.cd_pauta_sg_integrante_titular
			 WHERE psi.dt_exclusao IS NULL
			 ".(trim($args['fl_colegiado']) != '' ? "AND psi.fl_colegiado = ".str_escape($args['fl_colegiado']) : "")."
			 ".(trim($args['fl_removido']) == 'S' ? "AND psi.dt_removido IS NOT NULL" : "")."
			 ".(trim($args['fl_removido']) == 'N' ? "AND psi.dt_removido IS NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_titulares($fl_colegiado, $cd_pauta_sg_integrante = 0)
	{
		$qr_sql = "
			SELECT psi.cd_pauta_sg_integrante AS value,
			       psi.ds_pauta_sg_integrante AS text
			  FROM gestao.pauta_sg_integrante psi
			 WHERE psi.fl_tipo       = 'T'
			   AND psi.fl_secretaria = 'N'
			   AND psi.fl_colegiado  = '".trim($fl_colegiado)."'
			   AND psi.dt_exclusao  IS NULL
			   AND (psi.cd_pauta_sg_integrante NOT IN (
			   		SELECT COALESCE(psi2.cd_pauta_sg_integrante_titular, 0)
			          FROM gestao.pauta_sg_integrante psi2
			         WHERE psi2.dt_exclusao  IS NULL
			           AND psi2.dt_removido  IS NULL)
 					OR
 					psi.cd_pauta_sg_integrante = ".intval($cd_pauta_sg_integrante)."
 			   )
			   ".(intval($cd_pauta_sg_integrante) == 0 ? "AND psi.dt_removido IS NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_pauta_sg_integrante)
	{
		$qr_sql = "
			SELECT cd_pauta_sg_integrante,
	 			   ds_pauta_sg_integrante,
				   fl_colegiado,
				   fl_presidente,
				   fl_secretaria,
				   fl_indicado_eleito,
				   fl_tipo,
				   cd_pauta_sg_integrante_titular,
				   email,
				   celular,
				   cargo
			  FROM gestao.pauta_sg_integrante
			 WHERE dt_exclusao IS NULL
			   AND cd_pauta_sg_integrante = ".intval($cd_pauta_sg_integrante).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.pauta_sg_integrante
				(
					fl_colegiado,
					fl_presidente,
					fl_secretaria,
					fl_indicado_eleito,
					ds_pauta_sg_integrante,
					fl_tipo,
					cd_pauta_sg_integrante_titular,
					email,
				    celular,
				    cargo,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['fl_colegiado']) != '' ? str_escape($args['fl_colegiado']) : "DEFAULT").",
					".(trim($args['fl_presidente']) != '' ? str_escape($args['fl_presidente']) : "DEFAULT").",
					".(trim($args['fl_secretaria']) != '' ? str_escape($args['fl_secretaria']) : "DEFAULT").",
					".(trim($args['fl_indicado_eleito']) != '' ? str_escape($args['fl_indicado_eleito']) : "DEFAULT").",
					".(trim($args['ds_pauta_sg_integrante']) != '' ? str_escape($args['ds_pauta_sg_integrante']) : "DEFAULT").",
					".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
					".(intval($args['cd_pauta_sg_integrante_titular']) > 0 ? intval($args['cd_pauta_sg_integrante_titular']) : "DEFAULT").",
					".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
					".(trim($args['celular']) != '' ? str_escape($args['celular']) : "DEFAULT").",
					".(trim($args['cargo']) != '' ? str_escape($args['cargo']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_pauta_sg_integrante, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_integrante
			   SET fl_colegiado 		  		  = ".(trim($args['fl_colegiado']) != '' ? str_escape($args['fl_colegiado']) : "DEFAULT").",
			       fl_presidente 		  		  = ".(trim($args['fl_presidente']) != '' ? str_escape($args['fl_presidente']) : "DEFAULT").",
			       fl_secretaria 		  		  = ".(trim($args['fl_secretaria']) != '' ? str_escape($args['fl_secretaria']) : "DEFAULT").",
			       fl_indicado_eleito 		      = ".(trim($args['fl_indicado_eleito']) != '' ? str_escape($args['fl_indicado_eleito']) : "DEFAULT").",
				   ds_pauta_sg_integrante 		  = ".(trim($args['ds_pauta_sg_integrante']) != '' ? str_escape($args['ds_pauta_sg_integrante']) : "DEFAULT").",
				   fl_tipo 						  = ".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
				   email                          = ".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
				   celular                       = ".(trim($args['celular']) != '' ? str_escape($args['celular']) : "DEFAULT").",
				   cargo                          = ".(trim($args['cargo']) != '' ? str_escape($args['cargo']) : "DEFAULT").",
				   cd_pauta_sg_integrante_titular = ".(intval($args['cd_pauta_sg_integrante_titular']) > 0 ? intval($args['cd_pauta_sg_integrante_titular']) : "DEFAULT").",
				   cd_usuario_alteracao   		  = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 		  		  = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_integrante = ".intval($cd_pauta_sg_integrante).";";

		$this->db->query($qr_sql);
	}

	public function remover($cd_pauta_sg_integrante, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_integrante
			   SET dt_removido 		   = CURRENT_TIMESTAMP,
			   	   cd_usuario_removido = ".intval($cd_usuario)."
			 WHERE cd_pauta_sg_integrante = ".intval($cd_pauta_sg_integrante).";";

		$this->db->query($qr_sql);
	}

	public function ativar($cd_pauta_sg_integrante)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_integrante
			   SET dt_removido 		   = NULL,
			   	   cd_usuario_removido = NULL
			 WHERE cd_pauta_sg_integrante = ".intval($cd_pauta_sg_integrante).";";

		$this->db->query($qr_sql);
	}
}