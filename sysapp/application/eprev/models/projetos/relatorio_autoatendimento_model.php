<?php
class relatorio_autoatendimento_model extends Model
{
    function __construct()
	{
		parent::Model();
	}
	
    function empresa( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT p.sigla AS empresa,
						   COUNT(*) AS qt_total
					  FROM public.log_acessos l
					  JOIN public.patrocinadoras p
					    ON p.cd_empresa = l.cd_empresa
					 WHERE l.sistema   = 'AUTO_ATENDIMENTO'
					   AND l.data_hora >= TO_DATE('01/01/2012','DD/MM/YYYY')
					   AND l.data_hora::DATE BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY') 
					 GROUP BY empresa
					 ORDER BY qt_total DESC;			
	              ";

        $result = $this->db->query($qr_sql);
    }		

    function tipo_participante( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT CASE WHEN tipo_participante IN ('SEMP','OUTR','ERRO') THEN 'Não identificado'
								WHEN tipo_participante = 'APOS' THEN 'Aposentado'
								WHEN tipo_participante = 'ATIV' THEN 'Ativo'
								WHEN tipo_participante = 'AUXD' THEN 'Auxílio Doença'
								WHEN tipo_participante = 'CTP' THEN 'CTP'
								WHEN tipo_participante = 'EXAU' THEN 'Ex-Autárquico'
								WHEN tipo_participante = 'PENS' THEN 'Pensionista'
								ELSE tipo_participante
						   END AS tipo,
						   COUNT(*) AS qt_total
					  FROM public.log_acessos l
					 WHERE l.sistema   = 'AUTO_ATENDIMENTO'
					   AND l.data_hora >= TO_DATE('01/01/2012','DD/MM/YYYY')
					   AND l.data_hora::DATE BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY') 
					 GROUP BY tipo
					 ORDER BY qt_total DESC;			
	              ";

        $result = $this->db->query($qr_sql);
    }
	
    function tipo_senha_participante( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT CASE WHEN tipo_senha_participante = '2' THEN 'Completa'
								WHEN tipo_senha_participante = '1' THEN 'Consulta'
								ELSE 'Sem Senha'
						   END AS tipo,
						   COUNT(*) AS qt_total
					  FROM public.log_acessos l
					 WHERE l.sistema   = 'AUTO_ATENDIMENTO'
					   AND l.data_hora >= TO_DATE('01/01/2012','DD/MM/YYYY')
					   AND l.data_hora::DATE BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY') 
					 GROUP BY tipo
					 ORDER BY qt_total DESC;			
	              ";

        $result = $this->db->query($qr_sql);
    }	
 
}
?>