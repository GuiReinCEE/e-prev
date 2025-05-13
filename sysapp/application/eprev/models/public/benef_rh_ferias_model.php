<?php
class benef_rh_ferias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT brf.dt_ini_ferias,
						   uc.nome,
						   TO_CHAR(brf.dt_ini_ferias,'DD/MM/YYYY') AS dt_ferias_ini,
						   TO_CHAR(brf.dt_fim_ferias,'DD/MM/YYYY') AS dt_ferias_fim
					  FROM public.benef_rh_ferias brf
					  JOIN projetos.usuarios_controledi uc
						ON COALESCE(uc.cd_registro_empregado,0) = brf.cd_registro_empregado
					  JOIN funcoes.get_usuario_gerencia('".trim($args["cd_gerencia"])."') u
						ON u.codigo = uc.codigo
					 WHERE brf.cd_empresa            = 9
					   AND brf.seq_dependencia       = 0
					   AND (brf.dt_ini_ferias >= CURRENT_DATE OR brf.dt_fim_ferias >= CURRENT_DATE)
					 ORDER BY brf.dt_ini_ferias			
				   ";

		$result = $this->db->query($qr_sql);
	}
	
	function programacao_mes( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT brf.dt_ini_ferias,
					       uc.guerra AS nome,
					       uc.divisao,
					       TO_CHAR(brf.dt_ini_ferias,'DD/MM/YYYY') AS dt_ferias_ini,
					       TO_CHAR(brf.dt_fim_ferias,'DD/MM/YYYY') AS dt_ferias_fim
					  FROM public.benef_rh_ferias brf
					  JOIN projetos.usuarios_controledi uc
					    ON COALESCE(uc.cd_registro_empregado,0) = brf.cd_registro_empregado
					 WHERE brf.cd_empresa            = 9
					   AND brf.seq_dependencia       = 0
					   AND uc.tipo != 'X'
					   AND DATE_TRUNC('month', brf.dt_ini_ferias)::DATE  = TO_DATE('01/".$args['nr_mes_referencia']."/".$args['nr_ano_referencia']."','DD/MM/YYYY')
					 ORDER BY nome			
				   ";

		$result = $this->db->query($qr_sql);
	}	
}
?>