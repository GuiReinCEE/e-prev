<?php
class Pauta_sg_justificativa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_pauta_sg_justificativa,
			       ds_pauta_sg_justificativa
			  FROM gestao.pauta_sg_justificativa 
			 WHERE dt_exclusao IS NULL
              ".(trim($args['ds_pauta_sg_justificativa']) != '' ? "AND UPPER(funcoes.remove_acento(ds_pauta_sg_justificativa)) LIKE(UPPER(funcoes.remove_acento('%".$args['ds_pauta_sg_justificativa']."%')))" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_pauta_sg_justificativa)
	{
		$qr_sql = "
			SELECT cd_pauta_sg_justificativa,
				   ds_pauta_sg_justificativa
			  FROM gestao.pauta_sg_justificativa 
			 WHERE cd_pauta_sg_justificativa = ".intval($cd_pauta_sg_justificativa).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.pauta_sg_justificativa
                 (
               		ds_pauta_sg_justificativa, 
               		cd_usuario_inclusao, 
              		cd_usuario_alteracao
                 )
            VALUES 
                 (
                   ".(trim($args['ds_pauta_sg_justificativa']) != '' ? str_escape($args['ds_pauta_sg_justificativa']) : "DEFAULT").",
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
	}

	public function atualizar($cd_pauta_sg_justificativa, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_justificativa
			   SET ds_pauta_sg_justificativa = ".(trim($args['ds_pauta_sg_justificativa']) != '' ? str_escape($args['ds_pauta_sg_justificativa']) : "DEFAULT").",		       
			       cd_usuario_alteracao    = ".intval($args['cd_usuario']).", 
			       dt_alteracao            = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_justificativa = ".intval($cd_pauta_sg_justificativa).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_pauta_sg_justificativa, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_justificativa
			   SET cd_usuario_exclusao = ".intval($cd_usuario).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_justificativa = ".intval($cd_pauta_sg_justificativa).";";

		$this->db->query($qr_sql);
	}
}