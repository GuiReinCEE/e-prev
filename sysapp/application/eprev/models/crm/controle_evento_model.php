<?php
class controle_evento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT ce.cd_controle_evento, 
					       
						   ce.cd_controle_evento_tipo, 
					       cet.ds_controle_evento_tipo, 
						   
						   ce.cd_controle_evento_local, 
						   cel.ds_controle_evento_local, 

						   TO_CHAR(ce.dt_evento,'DD/MM/YYYY') AS dt_evento,
						   ce.ds_evento, 
						   ce.nr_convidado, 
						   ce.nr_estimado, 
						   ce.nr_presente, 
						   ce.nr_respondente, 
						   ce.nr_satisfeito, 
						   
						   TO_CHAR(ce.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   ce.cd_usuario_inclusao, 
						   
						   TO_CHAR(ce.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   ce.cd_usuario_alteracao,
						   uc.nome AS usuario_alteracao

					  FROM crm.controle_evento ce
					  JOIN crm.controle_evento_tipo cet
					    ON cet.cd_controle_evento_tipo = ce.cd_controle_evento_tipo
					  JOIN crm.controle_evento_local cel
					    ON cel.cd_controle_evento_local = ce.cd_controle_evento_local						
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ce.cd_usuario_alteracao
					 WHERE ce.dt_exclusao IS NULL
					 ".(trim($args["ds_evento"]) != "" ? "AND UPPER(funcoes.remove_acento(ce.ds_evento)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_evento"])."%'))" : "")."
					 ".(intval($args["cd_controle_evento_tipo"]) > 0 ? "AND ce.cd_controle_evento_tipo = ".intval($args["cd_controle_evento_tipo"]) : "")."
					 ".(intval($args["cd_controle_evento_local"]) > 0 ? "AND ce.cd_controle_evento_local = ".intval($args["cd_controle_evento_local"]) : "")."
					 ".(((trim($args['dt_ini']) != "") AND  (trim($args['dt_fim']) != "")) ? " AND CAST(ce.dt_evento AS DATE) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY ce.dt_evento DESC
				  ";
        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_controle_evento, 
					       cd_controle_evento_tipo, 
						   cd_controle_evento_local, 
						   TO_CHAR(dt_evento,'DD/MM/YYYY') AS dt_evento, 
						   ds_evento, 
						   nr_convidado, 
						   nr_estimado, 
						   nr_presente, 
						   nr_respondente, 
						   nr_satisfeito, 
						   dt_inclusao, 
						   cd_usuario_inclusao, 
						   dt_alteracao, 
						   cd_usuario_alteracao, 
						   dt_exclusao, 
						   cd_usuario_exclusao,
						   obs
					  FROM crm.controle_evento
			         WHERE cd_controle_evento = ".intval($args['cd_controle_evento'])."
				  ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_controle_evento']) == 0)
		{
			$cd_controle_evento = intval($this->db->get_new_id("crm.controle_evento", "cd_controle_evento"));
			
			$qr_sql = "
						INSERT INTO crm.controle_evento
							 (
								cd_controle_evento, 
								cd_controle_evento_tipo, 
								cd_controle_evento_local, 
								dt_evento, 
								ds_evento, 
								nr_convidado, 
								nr_estimado, 
								nr_presente, 
								nr_respondente, 
								nr_satisfeito,
								obs,
								cd_usuario_inclusao,
								cd_usuario_alteracao
							 )
						VALUES
							 (
								".intval($cd_controle_evento).",
								".(intval($args['cd_controle_evento_tipo']) > 0 ? "'".intval($args['cd_controle_evento_tipo'])."'" : "DEFAULT").",
								".(intval($args['cd_controle_evento_local']) > 0 ? "'".intval($args['cd_controle_evento_local'])."'" : "DEFAULT").",
								".(trim($args['dt_evento']) != '' ? "TO_DATE('".trim($args['dt_evento'])."','DD/MM/YYYY')" : "DEFAULT").",
								".(trim($args['ds_evento']) != '' ? "'".trim($args['ds_evento'])."'" : "DEFAULT").",
								".(intval($args['nr_convidado']) > 0 ? "'".intval($args['nr_convidado'])."'" : "DEFAULT").",
								".(intval($args['nr_estimado']) > 0 ? "'".intval($args['nr_estimado'])."'" : "DEFAULT").",
								".(intval($args['nr_presente']) > 0 ? "'".intval($args['nr_presente'])."'" : "DEFAULT").",
								".(intval($args['nr_respondente']) > 0 ? "'".intval($args['nr_respondente'])."'" : "DEFAULT").",
								".(intval($args['nr_satisfeito']) > 0 ? "'".intval($args['nr_satisfeito'])."'" : "DEFAULT").",
								".(trim($args['obs']) != '' ? str_escape($args['obs']) : "DEFAULT").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							 );
					 ";
		}
		else
		{
			$cd_controle_evento = intval($args['cd_controle_evento']);
			
			$qr_sql = "
						UPDATE crm.controle_evento
						   SET cd_controle_evento_tipo  = ".(intval($args['cd_controle_evento_tipo']) > 0 ? "'".intval($args['cd_controle_evento_tipo'])."'" : "DEFAULT").",
							   cd_controle_evento_local = ".(intval($args['cd_controle_evento_local']) > 0 ? "'".intval($args['cd_controle_evento_local'])."'" : "DEFAULT").",
							   dt_evento                = ".(trim($args['dt_evento']) != '' ? "TO_DATE('".trim($args['dt_evento'])."','DD/MM/YYYY')" : "DEFAULT").",
							   ds_evento                = ".(trim($args['ds_evento']) != '' ? "'".trim($args['ds_evento'])."'" : "DEFAULT").",
							   nr_convidado             = ".(intval($args['nr_convidado']) > 0 ? "'".intval($args['nr_convidado'])."'" : "DEFAULT").",
							   nr_estimado              = ".(intval($args['nr_estimado']) > 0 ? "'".intval($args['nr_estimado'])."'" : "DEFAULT").",
							   nr_presente              = ".(intval($args['nr_presente']) > 0 ? "'".intval($args['nr_presente'])."'" : "DEFAULT").",
							   nr_respondente           = ".(intval($args['nr_respondente']) > 0 ? "'".intval($args['nr_respondente'])."'" : "DEFAULT").",
							   nr_satisfeito            = ".(intval($args['nr_satisfeito']) > 0 ? "'".intval($args['nr_satisfeito'])."'" : "DEFAULT").",
						       obs                      = ".(trim($args['obs']) != '' ? str_escape($args['obs']) : "DEFAULT").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_controle_evento = ".intval($args['cd_controle_evento']).";
				     ";
		}
		
		$this->db->query($qr_sql);
		
		return $cd_controle_evento;
	}		
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE crm.controle_evento
					   SET cd_usuario_exclusao  = ".intval($args['cd_usuario']).",
						   dt_exclusao          = CURRENT_TIMESTAMP,
						   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
						   dt_alteracao         = CURRENT_TIMESTAMP				   
					 WHERE cd_controle_evento = ".intval($args['cd_controle_evento']).";
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
    function resumoListar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT TO_CHAR(ce.dt_evento,'YYYY/MM') AS nr_mes,
					       SUM(ce.nr_convidado) AS nr_convidado, 
					       SUM(ce.nr_estimado) AS nr_estimado, 
					       SUM(ce.nr_presente) AS nr_presente, 
					       SUM(ce.nr_respondente) AS nr_respondente, 
					       SUM(ce.nr_satisfeito) AS nr_satisfeito
					  FROM crm.controle_evento ce
					 WHERE ce.dt_exclusao IS NULL
					 ".(intval($args["nr_ano"]) > 0 ? "AND TO_CHAR(ce.dt_evento,'YYYY') = '".intval($args["nr_ano"])."'" : "")."
					 ".(intval($args["nr_mes"]) > 0 ? "AND TO_CHAR(ce.dt_evento,'MM')::INTEGER = ".intval($args["nr_mes"]) : "")."
					 ".(intval($args["cd_controle_evento_tipo"]) > 0 ? "AND ce.cd_controle_evento_tipo = ".intval($args["cd_controle_evento_tipo"]) : "")."
					 GROUP BY nr_mes
					 ORDER BY nr_mes
				  ";
		#echo $qr_sql; exit;
        $result = $this->db->query($qr_sql);
    }	
}
?>