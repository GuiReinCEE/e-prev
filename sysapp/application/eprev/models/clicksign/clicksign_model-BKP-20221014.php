<?php
class clicksign_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
	public function getConfig($cd_banco)
	{
		$qr_sql = "
					SELECT ds_ambiente, 
						   ds_token, 
						   ds_url
					  FROM clicksign.configuracao
					 WHERE UPPER(ds_ambiente) = '".strtoupper(trim($cd_banco))."'
					   AND dt_exclusao IS NULL
			      ";
		#echo $qr_sql;
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function getSignatario($email, $tp_token)
	{
		$qr_sql = "
					SELECT ".($tp_token == "email" ? "id_signatario_email": "id_signatario_sms")." AS id_signatario
					  FROM clicksign.signatario
					 WHERE email = TRIM(LOWER('".strtolower(trim($email))."'))
					   AND ".($tp_token == "email" ? "id_signatario_email IS NOT NULL": "id_signatario_sms IS NOT NULL")."
			      ";
		#echo $qr_sql;
		return $this->db->query($qr_sql)->row_array();
	}	
	
	
	public function salvarDocumento($args = array())
    {
        $qr_sql = "
					INSERT INTO clicksign.documento
					     (
							ip,
							id_doc,
							json_doc,
							dados_post,
							dt_limite
						 )
					VALUES
						 (
							'".$_SERVER['REMOTE_ADDR']."',
							'".$args['AR_DOC']["ARRAY"]['document']['key']."',
							'".$args['AR_DOC']["JSON"]."',
							'".$args["POST"]."',
							TO_TIMESTAMP('".$args['dt_limite']."','YYYY-MM-DD HH24:MI:SS')
						 )
				  ";
		
        $this->db->query($qr_sql);
		
		#echo $qr_sql; exit;
    }	
}
?>
