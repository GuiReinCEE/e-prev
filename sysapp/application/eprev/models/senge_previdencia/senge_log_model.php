<?php
class Senge_log_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT spl.cd_log,
			       spu.nome,
			       spl.ds_request,
			       TO_CHAR(spl.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
			       spl.ds_link
			  FROM senge_previdencia.log spl
			  JOIN senge_previdencia.usuario spu
			    ON spu.cd_usuario = spl.cd_usuario 
			 WHERE spl.dt_inclusao IS NOT NULL			  
               ".(((trim($args['dt_inclusao_ini']) != '') AND trim($args['dt_inclusao_fim']) != '') ? "AND DATE_TRUNC('day', spl.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : '')."
		       ".(intval($args['cd_usuario']) > 0 ? "AND spl.cd_usuario = ".intval($args['cd_usuario']) : "").";";

        return $this->db->query($qr_sql)->result_array();		
	}

	public function get_usuarios()
    {
        $qr_sql = "
            SELECT spl.cd_usuario AS value,
                   spu.nome || (CASE WHEN dt_exclusao IS NOT NULL 
                                 THEN  ' [Excluído em '|| TO_CHAR(dt_exclusao, 'DD/MM/YYYY') || ']'
                                 ELSE ''
                   		   END) AS text
              FROM senge_previdencia.log spl
              JOIN senge_previdencia.usuario spu 
                ON spu.cd_usuario = spl.cd_usuario;";

        return $this->db->query($qr_sql)->result_array();
    }
}