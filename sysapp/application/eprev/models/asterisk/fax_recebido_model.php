<?php
class Fax_recebido_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT f.cd_fax, 
						   f.email, 
						   UPPER(REPLACE(REPLACE(f.email, '@eletroceee.com.br',''),'fax','')) AS destino,
						   f.ramal, 
						   f.arquivo, 
						   f.device, 
						   f.commid, 
						   f.msg, 
						   f.cidnumber, 
						   f.cidname, 
						   f.destination, 
						   TO_CHAR(f.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   (SELECT fc.descricao 
							  FROM asterisk.fax_acompanhamento fc
							 WHERE fc.dt_exclusao IS NULL
							   AND fc.cd_fax = f.cd_fax 
							 ORDER BY cd_fax_acompanhamento DESC
							 LIMIT 1) AS acompanhamento
					  FROM asterisk.fax f
					 WHERE CAST(f.dt_inclusao AS DATE) BETWEEN ".(trim($args['dt_ini']) != "" ? "TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY')" : "CURRENT_DATE")." AND ".(trim($args['dt_fim']) != "" ? "TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')" : "CURRENT_DATE")."
					 ".(count($args['ar_email']) > 0 ? "AND LOWER(f.email) IN('".implode("','",$args['ar_email'])."') " : "")."
					 ".(trim($args['destino']) != "" ? "AND UPPER(REPLACE(REPLACE(f.email, '@eletroceee.com.br',''),'fax','')) = UPPER('".trim($args['destino'])."')" : "")."
					 ORDER BY f.dt_inclusao
                  ";
            $result = $this->db->query($qr_sql);
            #echo "<pre style='text-align:left;'>$qr_sql</pre>"; #exit;
    }
    
    function listar_acompanhamento( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT fc.descricao,
                   TO_CHAR(fc.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
                   uc.nome
              FROM asterisk.fax_acompanhamento fc
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = fc.cd_usuario_inclusao
             WHERE fc.dt_exclusao IS NULL
               AND fc.cd_fax = ".intval($args['cd_fax'])." 
             ORDER BY cd_fax_acompanhamento DESC";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO asterisk.fax_acompanhamento
                 (
                   cd_fax,
                   descricao,
                   cd_usuario_inclusao
                 )
            VALUES
                 (
                   ".intval($args['cd_fax']).",
                   '".trim($args['descricao'])."' ,
                   ".intval($args['cd_usuario'])."
                 )";
        
        $this->db->query($qr_sql);
    }
	
    function getFAX( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT cd_fax, 
                   email, 
                   ramal, 
                   arquivo, 
                   device, 
                   commid, 
                   msg, 
                   cidnumber, 
                   cidname, 
                   destination, 
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM asterisk.fax
             WHERE cd_fax = ".intval($args['cd_fax'])."
             ORDER BY dt_inclusao
                  ";
        $result = $this->db->query($qr_sql);
        #echo "<pre style='text-align:left;'>$qr_sql</pre>"; #exit;
    }	

	function destinoCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT UPPER(REPLACE(REPLACE(email, '@eletroceee.com.br',''),'fax','')) AS value, 
					       UPPER(REPLACE(REPLACE(email, '@eletroceee.com.br',''),'fax','')) AS text 
					  FROM asterisk.fax 
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	
}
?>