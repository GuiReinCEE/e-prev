<?php
class adocao_entidade_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
	function adocao_entidade_periodo(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_adocao_entidade_periodo AS value, 
			       ds_adocao_entidade_periodo AS text
              FROM projetos.adocao_entidade_periodo
			 WHERE dt_exclusao IS NULL;";
			 
		 $result = $this->db->query($qr_sql);
	}
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT ae.cd_adocao_entidade, 
			       ae.ds_adocao_entidade, 
				   aep.ds_adocao_entidade_periodo,   
				   CASE WHEN ae.fl_adocao_entidade_tipo = 'C' THEN 'Crianças'
				        ELSE 'Idosos'
				   END ds_adocao_entidade_tipo,
				   (SELECT TO_CHAR(aea.dt_adocao_entidade_acompanhamento, 'DD/MM/YYYY') || ' : ' || aea.ds_adocao_entidade_acompanhamento
				      FROM projetos.adocao_entidade_acompanhamento aea
					 WHERE aea.cd_adocao_entidade = ae.cd_adocao_entidade
					   AND aea.dt_exclusao IS NULL
					 ORDER BY aea.dt_inclusao DESC
					 LIMIT 1) AS acompanhamento
			  FROM projetos.adocao_entidade ae
			  JOIN projetos.adocao_entidade_periodo aep
			    ON aep.cd_adocao_entidade_periodo = ae.cd_adocao_entidade_periodo
			 WHERE ae.dt_exclusao IS NULL
			   ".(trim($args['ds_adocao_entidade']) != '' ? "AND UPPER(funcoes.remove_acento(ae.ds_adocao_entidade)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_adocao_entidade"])."%'))" : '')."
			   ".(trim($args['cd_adocao_entidade_periodo']) != '' ? " AND ae.cd_adocao_entidade_periodo = ".intval($args['cd_adocao_entidade_periodo']) : '')."
			   ".(trim($args['fl_adocao_entidade_tipo']) != '' ? " AND ae.fl_adocao_entidade_tipo = '".trim($args['fl_adocao_entidade_tipo'])."'" : '')."
			   ".(((trim($args['dt_adocao_entidade_acompanhamento_ini']) != "") AND (trim($args['dt_adocao_entidade_acompanhamento_fim']) != "")) ? "AND 0 < ( 
					SELECT COUNT(*)
				      FROM projetos.adocao_entidade_acompanhamento aea
					 WHERE aea.cd_adocao_entidade = ae.cd_adocao_entidade
					   AND aea.dt_exclusao IS NULL	
					   AND CAST(dt_adocao_entidade_acompanhamento AS DATE) BETWEEN TO_DATE('".$args["dt_adocao_entidade_acompanhamento_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_adocao_entidade_acompanhamento_fim"]."','DD/MM/YYYY') 
				)" : "")."
			   ;";
			 //
		 $result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
    {
		$qr_sql = "
			SELECT ae.cd_adocao_entidade, 
			       ae.ds_adocao_entidade, 
				   aep.ds_adocao_entidade_periodo,   
				   ae.fl_adocao_entidade_tipo,
				   CASE WHEN ae.fl_adocao_entidade_tipo = 'C' THEN 'Crianças'
				        ELSE 'Idosos'
				   END ds_adocao_entidade_tipo
			  FROM projetos.adocao_entidade ae
			  JOIN projetos.adocao_entidade_periodo aep
			    ON aep.cd_adocao_entidade_periodo = ae.cd_adocao_entidade_periodo
			 WHERE ae.cd_adocao_entidade = ".intval($args['cd_adocao_entidade']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_adocao_entidade']) == 0)
		{
			$cd_adocao_entidade = intval($this->db->get_new_id("projetos.adocao_entidade", "cd_adocao_entidade"));
		
			$qr_sql = "
				INSERT INTO projetos.adocao_entidade
				     (
					    cd_adocao_entidade,
						ds_adocao_entidade, 
						fl_adocao_entidade_tipo, 
						cd_adocao_entidade_periodo, 
						cd_usuario_inclusao
					 )
                VALUES 
				    (
					   ".intval($cd_adocao_entidade).",
					   ".(trim($args['ds_adocao_entidade']) != '' ? str_escape($args['ds_adocao_entidade']) : "DEFAULT").",
					   ".(trim($args['fl_adocao_entidade_tipo']) != '' ? str_escape($args['fl_adocao_entidade_tipo']) : "DEFAULT").",
					   ".(trim($args['cd_adocao_entidade_periodo']) != '' ? intval($args['cd_adocao_entidade_periodo']) : "DEFAULT").",
					   ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
					);";
		}
		else
		{
			$cd_adocao_entidade = intval($args['cd_adocao_entidade']);
		
			$qr_sql = "
				UPDATE projetos.adocao_entidade
				   SET ds_adocao_entidade         = ".(trim($args['ds_adocao_entidade']) != '' ? str_escape($args['ds_adocao_entidade']) : "DEFAULT").",
				       fl_adocao_entidade_tipo    = ".(trim($args['fl_adocao_entidade_tipo']) != '' ? str_escape($args['fl_adocao_entidade_tipo']) : "DEFAULT").",
					   cd_usuario_alteracao       = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
					   dt_alteracao               = CURRENT_TIMESTAMP
				 WHERE cd_adocao_entidade = ".intval($cd_adocao_entidade).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_adocao_entidade;
	}
	
	function listar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT aea.cd_adocao_entidade_acompanhamento,  
			       aea.ds_adocao_entidade_acompanhamento, 
                   TO_CHAR(aea.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(aea.dt_adocao_entidade_acompanhamento, 'DD/MM/YYYY') AS dt_adocao_entidade_acompanhamento,
				   uc.nome
              FROM projetos.adocao_entidade_acompanhamento aea
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = aea.cd_usuario_inclusao
             WHERE cd_adocao_entidade = ".intval($args['cd_adocao_entidade'])."
               AND dt_exclusao IS NULL;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompahamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.adocao_entidade_acompanhamento
			     (
                   cd_adocao_entidade, 
                   ds_adocao_entidade_acompanhamento, 
				   dt_adocao_entidade_acompanhamento,
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".(trim($args['cd_adocao_entidade']) != '' ? intval($args['cd_adocao_entidade']) : "DEFAULT").",
				   ".(trim($args['ds_adocao_entidade_acompanhamento']) != '' ? str_escape($args['ds_adocao_entidade_acompanhamento']) : "DEFAULT").",
				   ".(trim($args['dt_adocao_entidade_acompanhamento']) != '' ? "TO_DATE('".$args['dt_adocao_entidade_acompanhamento']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
				 );";
				 
		$result = $this->db->query($qr_sql);
	}
}