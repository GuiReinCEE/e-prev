<?php
class RI_perfil_participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function Ativo(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT COUNT(*) AS qt_total
					  FROM public.participantes p
					 WHERE projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('ATIV','AUXD','CTP','EXAU')
					   ".(array_key_exists('sexo', $args) ? "AND p.sexo = '".$args['sexo']."'" : "")."
					   ".(array_key_exists('cd_plano', $args) ? "AND p.cd_plano = ".$args['cd_plano'] : "")."
					   ".(array_key_exists('cd_empresa', $args) ? "AND p.cd_empresa = ".$args['cd_empresa'] : "")."
					   ".(array_key_exists('ar_idade', $args) ? "AND EXTRACT(YEARS FROM AGE(p.dt_nascimento)) BETWEEN ".$args['ar_idade']['min']." AND ".$args['ar_idade']['max'] : "")."
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}
	
	function Assistido(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT COUNT(*) AS qt_total
					  FROM public.participantes p
					 WHERE projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN ('APOS')
					   ".(array_key_exists('sexo', $args) ? "AND p.sexo = '".$args['sexo']."'" : "")."
					   ".(array_key_exists('cd_plano', $args) ? "AND p.cd_plano = ".$args['cd_plano'] : "")."
					   ".(array_key_exists('cd_empresa', $args) ? "AND p.cd_empresa = ".$args['cd_empresa'] : "")."
					   ".(array_key_exists('ar_idade', $args) ? "AND EXTRACT(YEARS FROM AGE(p.dt_nascimento)) BETWEEN ".$args['ar_idade']['min']." AND ".$args['ar_idade']['max'] : "")."
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}	
	
	function Pensao(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT COUNT(*) AS qt_total
                      FROM public.participantes pa
                      JOIN (SELECT DISTINCT p.cd_empresa, 
							       p.cd_registro_empregado
							  FROM public.participantes p
							  JOIN public.dependentes d
							    ON d.cd_empresa            = p.cd_empresa
							   AND d.cd_registro_empregado = p.cd_registro_empregado
							   AND d.seq_dependencia       = p.seq_dependencia          
							 WHERE p.dt_obito        IS NULL 
							   AND p.cd_plano        > 0 
							   AND p.seq_dependencia > 0
							   AND p.tipo_folha      IN (2,45,80)
							   AND d.dt_desligamento IS NULL
							   AND d.id_pensionista  = 'S') p1
                        ON p1.cd_empresa = pa.cd_empresa
                       AND p1.cd_registro_empregado = pa.cd_registro_empregado
                     WHERE pa.seq_dependencia = 0
					   ".(array_key_exists('sexo', $args) ? "AND pa.sexo = '".$args['sexo']."'" : "")."
					   ".(array_key_exists('cd_plano', $args) ? "AND pa.cd_plano = ".$args['cd_plano'] : "")."
					   ".(array_key_exists('cd_empresa', $args) ? "AND pa.cd_empresa = ".$args['cd_empresa'] : "")."
					   ".(array_key_exists('ar_idade', $args) ? "AND EXTRACT(YEARS FROM AGE(pa.dt_nascimento)) BETWEEN ".$args['ar_idade']['min']." AND ".$args['ar_idade']['max'] : "")."					   
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}	
	
	function Pensionista(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT COUNT(*) AS qt_total
					  FROM public.participantes p
					  JOIN public.dependentes d
						ON d.cd_empresa            = p.cd_empresa
					   AND d.cd_registro_empregado = p.cd_registro_empregado
					   AND d.seq_dependencia       = p.seq_dependencia          
					 WHERE p.dt_obito        IS NULL 
					   AND p.cd_plano        > 0 
					   AND p.seq_dependencia > 0
					   AND p.tipo_folha      IN (2,45,80)
					   AND d.dt_desligamento IS NULL
					   AND d.id_pensionista  = 'S' 
					   ".(array_key_exists('sexo', $args) ? "AND p.sexo = '".$args['sexo']."'" : "")."
					   ".(array_key_exists('cd_plano', $args) ? "AND p.cd_plano = ".$args['cd_plano'] : "")."
					   ".(array_key_exists('cd_empresa', $args) ? "AND p.cd_empresa = ".$args['cd_empresa'] : "")."
					   ".(array_key_exists('ar_idade', $args) ? "AND EXTRACT(YEARS FROM AGE(p.dt_nascimento)) BETWEEN ".$args['ar_idade']['min']." AND ".$args['ar_idade']['max'] : "")."					   

		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}	


	function plano(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT cd_plano,
						   descricao AS ds_plano
					  FROM public.planos
					 WHERE cd_plano > 0	
                     ORDER BY descricao					 
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}

	function planoEmpresa(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT p.cd_empresa,
						   p.sigla AS ds_empresa,
						   pl.cd_plano,
						   pl.descricao AS ds_plano
					  FROM public.patrocinadoras p
					  JOIN public.planos_patrocinadoras pp
						ON pp.cd_empresa = p.cd_empresa
					  JOIN public.planos pl
						ON pl.cd_plano = pp.cd_plano
					 WHERE pp.cd_plano > 0
					   AND p.cd_empresa NOT IN (4,5)	
                     ORDER BY ds_empresa, 
					          ds_plano
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; 
		#exit;

		$result = $this->db->query($qr_sql);
	}		
	
	
}
?>