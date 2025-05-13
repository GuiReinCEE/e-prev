<?php
class Pauta_sg_objetivo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_pauta_sg_objetivo,
			       ds_pauta_sg_objetivo,
			       fl_anexo_obrigatorio,
			       (CASE WHEN fl_anexo_obrigatorio = 'S'
				         THEN 'Sim'
				         ELSE 'Não'
				   END) AS ds_anexo_obrigatorio
			  FROM gestao.pauta_sg_objetivo 
			 WHERE dt_exclusao IS NULL
			  ".(trim($args['fl_anexo_obrigatorio']) != '' ? "AND fl_anexo_obrigatorio = '".trim($args['fl_anexo_obrigatorio'])."'" : "")."
              ".(trim($args['ds_pauta_sg_objetivo']) != '' ? "AND UPPER(funcoes.remove_acento(ds_pauta_sg_objetivo)) LIKE(UPPER(funcoes.remove_acento('%".$args['ds_pauta_sg_objetivo']."%')))" : "")."
             ORDER BY cd_pauta_sg_objetivo DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_pauta_sg_objetivo)
	{
		$qr_sql = "
			SELECT cd_pauta_sg_objetivo,
				   ds_pauta_sg_objetivo,
				   fl_anexo_obrigatorio
			  FROM gestao.pauta_sg_objetivo 
			 WHERE cd_pauta_sg_objetivo = ".intval($cd_pauta_sg_objetivo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.pauta_sg_objetivo
                 (
               		ds_pauta_sg_objetivo, 
               		fl_anexo_obrigatorio,
               		cd_usuario_inclusao, 
              		cd_usuario_alteracao
                 )
            VALUES 
                 (
                   ".(trim($args['ds_pauta_sg_objetivo']) != '' ? str_escape($args['ds_pauta_sg_objetivo']) : "DEFAULT").",
                   ".(trim($args['fl_anexo_obrigatorio']) != '' ? "'".trim($args['fl_anexo_obrigatorio'])."'" : "DEFAULT").",                   
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
	}

	public function atualizar($cd_pauta_sg_objetivo, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_objetivo
			   SET ds_pauta_sg_objetivo    = ".(trim($args['ds_pauta_sg_objetivo']) != '' ? str_escape($args['ds_pauta_sg_objetivo']) : "DEFAULT").",		       
			       fl_anexo_obrigatorio    = ".(trim($args['fl_anexo_obrigatorio']) != '' ? "'".trim($args['fl_anexo_obrigatorio'])."'" : "DEFAULT").",
			       cd_usuario_alteracao    = ".intval($args['cd_usuario']).", 
			       dt_alteracao            = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_objetivo = ".intval($cd_pauta_sg_objetivo).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_pauta_sg_objetivo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.pauta_sg_objetivo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_pauta_sg_objetivo = ".intval($cd_pauta_sg_objetivo).";";

		$this->db->query($qr_sql);
	}
}