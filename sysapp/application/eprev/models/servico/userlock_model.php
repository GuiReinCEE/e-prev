<?php
class userlock_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function ferias_listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT brf.dt_ini_ferias,
						   uc.divisao,
						   uc.guerra,
						   uc.nome,
						   uc.usuario,
						   TO_CHAR(brf.dt_ini_ferias,'DD/MM/YYYY') AS dt_ferias_ini,
						   TO_CHAR(brf.dt_fim_ferias,'DD/MM/YYYY') AS dt_ferias_fim
					  FROM public.benef_rh_ferias brf
					  JOIN projetos.usuarios_controledi uc
						ON COALESCE(uc.cd_registro_empregado,0) = brf.cd_registro_empregado
					 WHERE brf.cd_empresa       = 9
					   AND brf.seq_dependencia  = 0
					   AND uc.tipo <> 'X' 
					   AND uc.usuario NOT IN('coliveira','rpfeuffer')
					   AND brf.dt_ini_ferias    >= (CURRENT_DATE - '45 days'::INTERVAL)::DATE
					   AND brf.dt_fim_ferias    <= (CURRENT_DATE + '45 days'::INTERVAL)::DATE
					 ORDER BY brf.dt_ini_ferias, uc.usuario	
                  ";
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }

    function listarFeriasBot(&$result, $args=array())
    {
        $qr_sql = "
					SELECT uc.divisao,
						   uc.guerra,
						   uc.nome,
						   uc.usuario,uc.tipo 
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.tipo NOT IN('D','X','T') 
					   AND uc.usuario NOT IN('coliveira')
					   AND uc.usuario ".($args["fl_ferias"] == "N" ? "NOT" : "")." IN 
					                        (SELECT uc1.usuario
					                           FROM public.benef_rh_ferias brf1
					                           JOIN projetos.usuarios_controledi uc1
						                     ON COALESCE(uc1.cd_registro_empregado,0) = brf1.cd_registro_empregado
					                          WHERE brf1.cd_empresa       = 9
					                            AND brf1.seq_dependencia  = 0
					                            AND uc1.tipo <> 'X' 
					                            AND uc1.usuario NOT IN('coliveira')
					                            AND CURRENT_DATE BETWEEN brf1.dt_ini_ferias AND brf1.dt_fim_ferias)
					 ORDER BY uc.usuario
                  ";
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }

}
?>
