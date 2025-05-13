<?php
class controle_informativo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        #### ATENCAO USADO NOS INDICADORES ####
		$qr_sql = "
					SELECT ci.cd_controle_informativo, 
					       
						   ci.cd_controle_informativo_tipo, 
					       cit.ds_controle_informativo_tipo, 

						   TO_CHAR(ci.dt_envio_limite,'DD/MM/YYYY') AS dt_envio_limite,
						   TO_CHAR(ci.dt_envio,'DD/MM/YYYY') AS dt_envio,
						   
						   ci.fl_envio,
						   
						   CASE WHEN ci.fl_envio = 'N' THEN 'S'
                                WHEN ci.dt_envio_limite < ci.dt_envio THEN 'S' 
                                ELSE 'N' 
                           END fl_atrasado,
						   
						   ci.ds_informativo,
						   
						   ci.nr_exemplar,
						   ci.nr_publico,
						   ci.nr_retrabalho,
						   ci.nr_reclamacao,
						   ci.observacao,
						   
						   TO_CHAR(ci.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   ci.cd_usuario_inclusao, 
						   
						   TO_CHAR(ci.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   ci.cd_usuario_alteracao,
						   uc.nome AS usuario_alteracao

					  FROM crm.controle_informativo ci
					  JOIN crm.controle_informativo_tipo cit
					    ON cit.cd_controle_informativo_tipo = ci.cd_controle_informativo_tipo
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ci.cd_usuario_alteracao
					 WHERE ci.dt_exclusao IS NULL
					 ".(trim($args["ds_informativo"]) != "" ? "AND UPPER(funcoes.remove_acento(ci.ds_informativo)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_informativo"])."%'))" : "")."
					 ".(intval($args["cd_controle_informativo_tipo"]) > 0 ? "AND ci.cd_controle_informativo_tipo = ".intval($args["cd_controle_informativo_tipo"]) : "")."
					 ".(((trim($args['dt_ini']) != "") AND  (trim($args['dt_fim']) != "")) ? " AND CAST(ci.dt_envio_limite AS DATE) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY ci.dt_envio_limite DESC
				  ";
        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_controle_informativo, 
					       cd_controle_informativo_tipo, 
						   TO_CHAR(dt_envio_limite,'DD/MM/YYYY') AS dt_envio_limite, 
						   TO_CHAR(dt_envio,'DD/MM/YYYY') AS dt_envio, 
						   fl_envio,
						   ds_informativo, 
						   nr_exemplar,
						   nr_publico,
						   nr_retrabalho,
						   nr_reclamacao,
						   observacao,
						   dt_inclusao, 
						   cd_usuario_inclusao, 
						   dt_alteracao, 
						   cd_usuario_alteracao, 
						   dt_exclusao, 
						   cd_usuario_exclusao
					  FROM crm.controle_informativo
			         WHERE cd_controle_informativo = ".intval($args['cd_controle_informativo'])."
				  ";
			 
		$result = $this->db->query($qr_sql);
	}	
			
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_controle_informativo']) == 0)
		{
			$cd_controle_informativo = intval($this->db->get_new_id("crm.controle_informativo", "cd_controle_informativo"));
			
			$qr_sql = "
						INSERT INTO crm.controle_informativo
							 (
								cd_controle_informativo, 
								cd_controle_informativo_tipo, 
								ds_informativo, 
								dt_envio_limite, 
								dt_envio, 
								fl_envio, 
								nr_exemplar, 
								nr_publico, 
								nr_retrabalho, 
								nr_reclamacao, 
								observacao,
								cd_usuario_inclusao,
								cd_usuario_alteracao
							 )
						VALUES
							 (
								".intval($cd_controle_informativo).",
								".(intval($args['cd_controle_informativo_tipo']) > 0 ? "'".intval($args['cd_controle_informativo_tipo'])."'" : "DEFAULT").",
								".(trim($args['ds_informativo']) != '' ? "'".trim($args['ds_informativo'])."'" : "DEFAULT").",
								".(trim($args['dt_envio_limite']) != '' ? "TO_DATE('".trim($args['dt_envio_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
								".(trim($args['dt_envio']) != '' ? "TO_DATE('".trim($args['dt_envio'])."','DD/MM/YYYY')" : "DEFAULT").",
								".(trim($args['fl_envio']) != '' ? "'".trim($args['fl_envio'])."'" : "DEFAULT").",
								".(intval($args['nr_exemplar']) > 0 ? "'".intval($args['nr_exemplar'])."'" : "DEFAULT").",
								".(intval($args['nr_publico']) > 0 ? "'".intval($args['nr_publico'])."'" : "DEFAULT").",
								".(intval($args['nr_retrabalho']) > 0 ? "'".intval($args['nr_retrabalho'])."'" : "DEFAULT").",
								".(intval($args['nr_reclamacao']) > 0 ? "'".intval($args['nr_reclamacao'])."'" : "DEFAULT").",
								".(trim($args['observacao']) != '' ? "'".trim($args['observacao'])."'" : "DEFAULT").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							 );
					 ";
		}
		else
		{
			$cd_controle_informativo = intval($args['cd_controle_informativo']);
			
			$qr_sql = "
						UPDATE crm.controle_informativo
						   SET cd_controle_informativo_tipo = ".(intval($args['cd_controle_informativo_tipo']) > 0 ? "'".intval($args['cd_controle_informativo_tipo'])."'" : "DEFAULT").",
							   ds_informativo               = ".(trim($args['ds_informativo']) != '' ? "'".trim($args['ds_informativo'])."'" : "DEFAULT").",
							   dt_envio_limite              = ".(trim($args['dt_envio_limite']) != '' ? "TO_DATE('".trim($args['dt_envio_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
							   dt_envio                     = ".(trim($args['dt_envio']) != '' ? "TO_DATE('".trim($args['dt_envio'])."','DD/MM/YYYY')" : "DEFAULT").",
							   fl_envio                     = ".(trim($args['fl_envio']) != '' ? "'".trim($args['fl_envio'])."'" : "DEFAULT").",
							   nr_exemplar                  = ".(intval($args['nr_exemplar']) > 0 ? "'".intval($args['nr_exemplar'])."'" : "DEFAULT").",
							   nr_publico                   = ".(intval($args['nr_publico']) > 0 ? "'".intval($args['nr_publico'])."'" : "DEFAULT").",
							   nr_retrabalho                = ".(intval($args['nr_retrabalho']) > 0 ? "'".intval($args['nr_retrabalho'])."'" : "DEFAULT").",
							   nr_reclamacao                = ".(intval($args['nr_reclamacao']) > 0 ? "'".intval($args['nr_reclamacao'])."'" : "DEFAULT").",
							   observacao                   = ".(trim($args['observacao']) != '' ? "'".trim($args['observacao'])."'" : "DEFAULT").",
							   cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
							   dt_alteracao                  = CURRENT_TIMESTAMP
						 WHERE cd_controle_informativo = ".intval($args['cd_controle_informativo']).";
				     ";
		}
		
		$this->db->query($qr_sql);
		
		return $cd_controle_informativo;
	}	

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE crm.controle_informativo
					   SET cd_usuario_exclusao  = ".intval($args['cd_usuario']).",
						   dt_exclusao          = CURRENT_TIMESTAMP,
						   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
						   dt_alteracao         = CURRENT_TIMESTAMP				   
					 WHERE cd_controle_informativo = ".intval($args['cd_controle_informativo']).";
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
    function resumoListar(&$result, $args=array())
    {
        #### ATENCAO USADO NOS INDICADORES ####
		$qr_sql = "
					SELECT TO_CHAR(ci.dt_envio_limite,'YYYY/MM') AS nr_mes,
					       COUNT(*) AS qt_informativo,
					       SUM(CASE WHEN ci.fl_envio = 'N' THEN 1
                                    WHEN ci.dt_envio_limite < ci.dt_envio THEN 1
                                    ELSE 0 
                               END) AS qt_atrasado,
					       SUM(CASE WHEN COALESCE(ci.nr_retrabalho,0) > 0 THEN 1 ELSE 0 END) AS qt_retrabalho,					
					       SUM(ci.nr_exemplar) AS nr_exemplar, 
					       SUM(ci.nr_publico) AS nr_publico, 
					       SUM(ci.nr_retrabalho) AS nr_retrabalho, 
					       SUM(ci.nr_reclamacao) AS nr_reclamacao
					  FROM crm.controle_informativo ci
					 WHERE ci.dt_exclusao IS NULL
					 ".(intval($args["nr_ano"]) > 0 ? "AND TO_CHAR(ci.dt_envio_limite,'YYYY') = '".intval($args["nr_ano"])."'" : "")."
					 ".(intval($args["nr_mes"]) > 0 ? "AND TO_CHAR(ci.dt_envio_limite,'MM')::INTEGER = ".intval($args["nr_mes"]) : "")."
					 ".(intval($args["cd_controle_informativo_tipo"]) > 0 ? "AND ci.cd_controle_informativo_tipo = ".intval($args["cd_controle_informativo_tipo"]) : "")."
					 GROUP BY nr_mes
					 ORDER BY nr_mes
				  ";
        $result = $this->db->query($qr_sql);
    }	
}
?>