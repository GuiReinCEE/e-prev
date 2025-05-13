<?php
class Solic_fiscalizacao_audit_origem_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_origem,
			       ds_solic_fiscalizacao_audit_origem,
                   (CASE WHEN fl_especificar = 'S' 
                         THEN 'Sim'        
                         ELSE 'Não'
                   END) AS ds_especificar,
                   (CASE WHEN fl_especificar = 'S' 
                         THEN 'success'        
                         ELSE 'info'
                   END) AS ds_class_especificar
			  FROM projetos.solic_fiscalizacao_audit_origem 
			 WHERE dt_exclusao IS NULL
			  ".(trim($args['fl_especificar']) != '' ? "AND fl_especificar = '".trim($args['fl_especificar'])."'" : "")."
              ".(trim($args['ds_solic_fiscalizacao_audit_origem']) != '' ? "AND UPPER(funcoes.remove_acento(ds_solic_fiscalizacao_audit_origem)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_solic_fiscalizacao_audit_origem'])."%')))" : "" ).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_solic_fiscalizacao_audit_origem)
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_origem,
				   ds_solic_fiscalizacao_audit_origem,
				   fl_especificar
			  FROM projetos.solic_fiscalizacao_audit_origem 
			 WHERE cd_solic_fiscalizacao_audit_origem = ".intval($cd_solic_fiscalizacao_audit_origem).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_origem 
                 (
               		ds_solic_fiscalizacao_audit_origem, 
               		fl_especificar,
               		cd_usuario_inclusao, 
              		cd_usuario_alteracao
                 )
            VALUES 
                 (				     	

                   ".(trim($args['ds_solic_fiscalizacao_audit_origem']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_origem']) : "DEFAULT").",
                   ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",                   
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
	}

	public function atualizar($cd_solic_fiscalizacao_audit_origem, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_origem 
			   SET ds_solic_fiscalizacao_audit_origem    = ".(trim($args['ds_solic_fiscalizacao_audit_origem']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_origem']) : "DEFAULT").",
			       fl_especificar                        = ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",
			       cd_usuario_alteracao                  = ".intval($args['cd_usuario']).", 
			       dt_alteracao                          = CURRENT_TIMESTAMP
			 WHERE cd_solic_fiscalizacao_audit_origem    = ".intval($cd_solic_fiscalizacao_audit_origem).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_solic_fiscalizacao_audit_origem, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_origem 
			   SET cd_usuario_exclusao                = ".intval($cd_usuario).", 
			       dt_exclusao                        = CURRENT_TIMESTAMP
			 WHERE cd_solic_fiscalizacao_audit_origem = ".intval($cd_solic_fiscalizacao_audit_origem).";";

		$this->db->query($qr_sql);
	}
}