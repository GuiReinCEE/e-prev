<?php
class Solic_fiscalizacao_audit_documento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_documento,
			       ds_solic_fiscalizacao_audit_documento,
                   (CASE WHEN fl_especificar = 'S' 
                         THEN 'Sim'        
                         ELSE 'Não'
                   END) AS ds_especificar,
                   (CASE WHEN fl_especificar = 'S' 
                         THEN 'success'        
                         ELSE 'info'
                   END) AS ds_class_especificar
			  FROM projetos.solic_fiscalizacao_audit_documento 
			 WHERE dt_exclusao IS NULL
			  ".(trim($args['fl_especificar']) != '' ? "AND fl_especificar = '".trim($args['fl_especificar'])."'" : "")."
              ".(trim($args['ds_solic_fiscalizacao_audit_documento']) != '' ? "AND UPPER(funcoes.remove_acento(ds_solic_fiscalizacao_audit_documento)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_solic_fiscalizacao_audit_documento'])."%')))" : "" ).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_solic_fiscalizacao_audit_documento)
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_documento,
				   ds_solic_fiscalizacao_audit_documento,
				   fl_especificar
			  FROM projetos.solic_fiscalizacao_audit_documento 
			 WHERE cd_solic_fiscalizacao_audit_documento = ".intval($cd_solic_fiscalizacao_audit_documento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_documento 
                 (
               		ds_solic_fiscalizacao_audit_documento, 
               		fl_especificar,
               		cd_usuario_inclusao, 
              		cd_usuario_alteracao
                 )
            VALUES 
                 (				     	

                   ".(trim($args['ds_solic_fiscalizacao_audit_documento']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_documento']) : "DEFAULT").",
                   ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",                   
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
	}

	public function atualizar($cd_solic_fiscalizacao_audit_documento, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_documento
			   SET ds_solic_fiscalizacao_audit_documento   = ".(trim($args['ds_solic_fiscalizacao_audit_documento']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_documento']) : "DEFAULT").",
			       fl_especificar                          = ".(trim($args['fl_especificar']) != '' ? "'".trim($args['fl_especificar'])."'" : "DEFAULT").",
			       cd_usuario_alteracao                    = ".intval($args['cd_usuario']).", 
			       dt_alteracao                            = CURRENT_TIMESTAMP
			 WHERE cd_solic_fiscalizacao_audit_documento   = ".intval($cd_solic_fiscalizacao_audit_documento).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_solic_fiscalizacao_audit_documento, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_documento 
			   SET cd_usuario_exclusao                   = ".intval($cd_usuario).", 
			       dt_exclusao                           = CURRENT_TIMESTAMP
			 WHERE cd_solic_fiscalizacao_audit_documento = ".intval($cd_solic_fiscalizacao_audit_documento).";";

		$this->db->query($qr_sql);
	}
}