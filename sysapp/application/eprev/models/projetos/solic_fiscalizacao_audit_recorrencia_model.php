<?php
class Solic_fiscalizacao_audit_recorrencia_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_mensal($nr_dia_recorrencia)
    {
    	$qr_sql = "
    		SELECT cd_solic_fiscalizacao_audit_recorrencia,
    		       TO_CHAR(TO_DATE(nr_dia_recebimento::text || '-' || TO_CHAR(CURRENT_DATE, 'MM-YYYY'), 'DD-MM-YYYY'), 'DD/MM/YYYY') AS dt_recebimento,
                   TO_CHAR(funcoes.dia_util('ANTES', TO_DATE((nr_dia_providencia+1)::text || '-' || TO_CHAR(CURRENT_DATE + interval '2 month', 'MM-YYYY'), 'DD/MM/YYYY'), 1), 'DD/MM/YYYY') AS dt_prazo,
                   REPLACE (ds_tipo, '[MES_ANO]', TO_CHAR(CURRENT_DATE, 'MM-YYYY')) AS ds_tipo,
                   cd_solic_fiscalizacao_audit_origem, 
                   ds_origem, 
                   cd_solic_fiscalizacao_audit_tipo, 
                   ds_documento, 
                   cd_gerencia, 
                   ds_teor
    		  FROM projetos.solic_fiscalizacao_audit_recorrencia
    		 WHERE dt_exclusao IS NULL
    		   AND tp_recorrencia     = 'M'
    		   AND nr_dia_recorrencia = ".intval($nr_dia_recorrencia).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_gestao($cd_solic_fiscalizacao_audit_recorrencia)
    {
    	$qr_sql = "
    		SELECT cd_gerencia
    		  FROM projetos.solic_fiscalizacao_audit_recorrencia_gestao
			 WHERE cd_solic_fiscalizacao_audit_recorrencia = ".intval($cd_solic_fiscalizacao_audit_recorrencia)."
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_documentacao($cd_solic_fiscalizacao_audit_recorrencia)
    {
    	$qr_sql = "
    		SELECT cd_solic_fiscalizacao_audit_recorrencia_documentacao,
    		       ds_solic_fiscalizacao_audit_documentacao,
			       cd_gerencia,
				   nr_item
			  FROM projetos.solic_fiscalizacao_audit_recorrencia_documentacao
			 WHERE cd_solic_fiscalizacao_audit_recorrencia = ".intval($cd_solic_fiscalizacao_audit_recorrencia)."
			   AND dt_exclusao IS NULL
			 ORDER BY nr_item::INTEGER;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function get_documentacao_responsavel($cd_solic_fiscalizacao_audit_recorrencia_documentacao)
    {
    	$qr_sql = "
    		SELECT cd_usuario
	          FROM projetos.solic_fiscalizacao_audit_recorrencia_documentacao_responsavel
	         WHERE cd_solic_fiscalizacao_audit_recorrencia_documentacao = ".intval($cd_solic_fiscalizacao_audit_recorrencia_documentacao)."
	           AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
    }
	
	public function get_documentacao_original($cd_solic_fiscalizacao_audit)
	{
		$qr_sql = "
    		SELECT *
	          FROM projetos.solic_fiscalizacao_audit_documentacao
	         WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
	           AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_responsavel_documentacao_original($cd_solic_fiscalizacao_audit_documentacao)
	{
		$qr_sql = "
    		SELECT *
	          FROM projetos.solic_fiscalizacao_audit_documentacao_responsavel
	         WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
	           AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function insere_documento($cd_solic_fiscalizacao_audit_recorrencia, $documentacao)
	{
		$cd_solic_fiscalizacao_audit_recorrencia_documentacao = intval($this->db->get_new_id('projetos.solic_fiscalizacao_audit_recorrencia_documentacao', 'cd_solic_fiscalizacao_audit_recorrencia_documentacao'));
		
		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_recorrencia_documentacao
			     (
					cd_solic_fiscalizacao_audit_recorrencia_documentacao, 
					cd_solic_fiscalizacao_audit_recorrencia, 
					ds_solic_fiscalizacao_audit_documentacao, 
					cd_gerencia, 
					nr_item, 
					cd_usuario_inclusao, 
					cd_usuario_alteracao
				 )
			VALUES
			     (
					".intval($cd_solic_fiscalizacao_audit_recorrencia_documentacao).",
					".intval($cd_solic_fiscalizacao_audit_recorrencia).",
					'".trim($documentacao['ds_solic_fiscalizacao_audit_documentacao'])."',
					'".trim($documentacao['cd_gerencia'])."',
					'".trim($documentacao['nr_item'])."',
					251,
					251
				 )";
				 
		$this->db->query($qr_sql);
				 
		return $cd_solic_fiscalizacao_audit_recorrencia_documentacao;
	}
	
	public function insere_documento_responsavel($cd_solic_fiscalizacao_audit_recorrencia_documentacao, $responsavel)
	{

		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_recorrencia_documentacao_responsavel
			     (
					cd_solic_fiscalizacao_audit_recorrencia_documentacao, 
					cd_usuario,
					cd_usuario_inclusao, 
					cd_usuario_alteracao
				 )
	        VALUES 
			     (
					".intval($cd_solic_fiscalizacao_audit_recorrencia_documentacao).",
					".intval($responsavel['cd_usuario']).",
					251,
					251
				 );";
				 
		$this->db->query($qr_sql);
	}
}