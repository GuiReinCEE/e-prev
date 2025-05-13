<?php
class Indicacao_model extends Model
{
	public function listar($cd_usuario)
	{
		$qr_sql = "
			SELECT cd_indicacao,
				   ds_indicado,
				   nr_telefone,
				   ds_email,
				   ds_parentesco,
				   ds_cidade,
				   ds_observacao,
				   ds_tipo_indicacao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS nome,
				   funcoes.get_usuario_area(cd_usuario_inclusao) AS area
			  FROM expansao.indicacao
			 WHERE dt_exclusao IS NULL
			   AND (cd_usuario_inclusao = ".intval($cd_usuario)." OR ".intval($cd_usuario)." IN (funcoes.get_usuario('lrodriguez'),
																								 funcoes.get_usuario('aconte'),
																								 funcoes.get_usuario('vdornelles'),
																								 funcoes.get_usuario('stavares'),
																								 funcoes.get_usuario('coliveira'),
																								 funcoes.get_usuario('dpastore')));"; 

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuarios()
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE tipo NOT IN ('X', 'T', 'P')
			 ORDER BY text ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuario($cd_usuario_indicacao)
	{
		$qr_sql = "
			SELECT codigo AS cd_usuario,
				   nome AS ds_usuario_inclusao,
				   divisao AS cd_gerencia
			  FROM projetos.usuarios_controledi
			 WHERE codigo = ".intval($cd_usuario_indicacao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO expansao.indicacao
				(
					ds_indicado,
					nr_telefone,
					ds_email,
					ds_parentesco,
					ds_cidade,
					ds_observacao,
					ds_tipo_indicacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_indicado']) != '' ? str_escape($args['ds_indicado']) : "DEFAULT").",
					".(trim($args['nr_telefone']) != '' ? str_escape($args['nr_telefone']) : "DEFAULT").",
					".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
					".(trim($args['ds_parentesco']) != '' ? str_escape($args['ds_parentesco']) : "DEFAULT").",
					".(trim($args['ds_cidade']) != '' ? str_escape($args['ds_cidade']) : "DEFAULT").",
					".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					".(trim($args['ds_tipo_indicacao']) != '' ? str_escape($args['ds_tipo_indicacao']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function salvar_interessado_familia($args = array())
	{
		$qr_sql = "
			INSERT INTO familia_previdencia.cadastro
				(
					nome,  
					cidade, 
					telefone, 
					email, 
					observacoes,
					fl_indicacao_interna,
					fl_associado,
					fl_inscrito,
					fl_participante, 
					cd_instituidor,
					cd_cadastro_situacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES 
				(
					".(trim($args['ds_indicado']) != "" ? "UPPER(funcoes.remove_acento('".trim($args['ds_indicado'])."'))": "DEFAULT").",
					".(trim($args['ds_cidade']) != "" ? "UPPER(funcoes.remove_acento('".trim($args['ds_cidade'])."'))": "DEFAULT").",
					".(trim($args['nr_telefone']) != "" ? "'".trim($args['nr_telefone'])."'": "DEFAULT").",
					".(trim($args['ds_email']) != "" ? "funcoes.remove_acento('".trim($args['ds_email'])."')": "DEFAULT").",
					".(trim($args['ds_observacao_interessado']) != "" ? "'".trim($args['ds_observacao_interessado'])."'": "DEFAULT").",
					".(trim($args['cd_gerencia']) != "" ? "'".trim($args['cd_gerencia'])."'": "DEFAULT").",
					'N',
					'N',
					'N',
					24,
					3,
					9999,
					9999
				);";

		$this->db->query($qr_sql);
	}
}