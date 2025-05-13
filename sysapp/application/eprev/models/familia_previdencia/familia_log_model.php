<?php
class Familia_log_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT fpl.cd_log,
			       fpu.nome,
			       fpl.ds_request,
			       TO_CHAR(fpl.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
			       fpl.ds_link
			  FROM familia_previdencia.log fpl
			  JOIN familia_previdencia.usuario fpu
			    ON fpu.cd_usuario = fpl.cd_usuario 
			 WHERE fpl.dt_inclusao IS NOT NULL
               ".(((trim($args['dt_inclusao_ini']) != '') AND trim($args['dt_inclusao_fim']) != '') ? "AND DATE_TRUNC('day', fpl.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : '')."
		       ".(intval($args['cd_usuario']) > 0 ? "AND fpl.cd_usuario = ".intval($args['cd_usuario']) : "").";";

        return $this->db->query($qr_sql)->result_array();		
	}

	public function get_usuarios()
    {
        $qr_sql = "
            SELECT fpl.cd_usuario AS value,
                   fpu.nome || (CASE WHEN dt_exclusao IS NOT NULL 
                                 THEN  ' [Excluído em '|| TO_CHAR(dt_exclusao, 'DD/MM/YYYY') || ']'
                                 ELSE ''
                   		   END) AS text
              FROM familia_previdencia.log fpl
              JOIN familia_previdencia.usuario fpu 
                ON fpu.cd_usuario = fpl.cd_usuario;";

        return $this->db->query($qr_sql)->result_array();
    }
}