<?php
class Relatorio_central_telefonica_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function maiorValorRamal( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.vl_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   vl_ligacao 
							  FROM asterisk.ramal_vl_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   {cd_conta}, 
								   NULL, 
								   2, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   vl_ligacao NUMERIC
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.vl_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
	
	function maiorValorDestino( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT destino,
						   vl_ligacao 
					  FROM asterisk.destino_vl_ligacao
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'),   
						   {cd_conta}, 
						   NULL, 
						   2, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   vl_ligacao NUMERIC
						 )
					 ORDER BY vl_ligacao DESC
					 LIMIT {nr_top}						 
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorValorRamalDiretoria( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.vl_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   vl_ligacao 
							  FROM asterisk.ramal_vl_ligacao_diretoria
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   '{cd_diretoria}', 
								   NULL, 
								   2, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   vl_ligacao NUMERIC
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.vl_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorValorRamalDiretoriaDestino( &$result, $args=array() )
	{
		$qr_sql = "
				    SELECT destino,
						   vl_ligacao 
					  FROM asterisk.destino_vl_ligacao_diretoria
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
						   '{cd_diretoria}', 
						   NULL, 
						   2, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   vl_ligacao NUMERIC
						 )
					 ORDER BY vl_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorDuracaoRamal( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.hr_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   hr_ligacao 
							  FROM asterisk.ramal_hr_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   {cd_conta}, 
								   NULL, 
								   1, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   hr_ligacao INTERVAL
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.hr_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
	
	function maiorDuracaoDestino( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT destino,
						   hr_ligacao 
					  FROM asterisk.destino_hr_ligacao
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'),   
						   {cd_conta}, 
						   NULL, 
						   1, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   hr_ligacao INTERVAL
						 )
					 ORDER BY hr_ligacao DESC
					 LIMIT {nr_top}						 
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorDuracaoRamalDiretoria( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.hr_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   hr_ligacao 
							  FROM asterisk.ramal_hr_ligacao_diretoria
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   '{cd_diretoria}', 
								   NULL, 
								   1, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   hr_ligacao INTERVAL
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.hr_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorDuracaoRamalDiretoriaDestino( &$result, $args=array() )
	{
		$qr_sql = "
				    SELECT destino,
						   hr_ligacao 
					  FROM asterisk.destino_hr_ligacao_diretoria
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
						   '{cd_diretoria}', 
						   NULL, 
						   1, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   hr_ligacao INTERVAL
						 )
					 ORDER BY hr_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		#echo "<pre>$qr_sql</pre>";
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorQuantidadeRamal( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.qt_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   qt_ligacao 
							  FROM asterisk.ramal_qt_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'),  
								   {cd_conta}, 
								   NULL, 
								   NULL, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   qt_ligacao BIGINT
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.qt_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorQuantidadeDestino( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT destino,
						   qt_ligacao 
					  FROM asterisk.destino_qt_ligacao
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'),   
						   {cd_conta}, 
						   NULL, 
						   NULL, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   qt_ligacao BIGINT
						 )
					 ORDER BY qt_ligacao DESC
					 LIMIT {nr_top}						 
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		$qr_sql = str_replace("{cd_conta}", ($args["cd_conta"] != "" ? ("(SELECT cd_conta FROM asterisk.conta WHERE conta ='".$args["cd_conta"]."')") : "NULL"),$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorQuantidadeRamalDiretoria( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT q.ramal,
						   q.qt_ligacao,
						   s.conta,
						   s.nome
					  FROM (SELECT ramal,
								   qt_ligacao 
							  FROM asterisk.ramal_qt_ligacao_diretoria
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   '{cd_diretoria}', 
								   NULL, 
								   NULL, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   qt_ligacao BIGINT
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 ORDER BY q.qt_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function maiorQuantidadeRamalDiretoriaDestino( &$result, $args=array() )
	{
		$qr_sql = "
				    SELECT destino,
						   qt_ligacao 
					  FROM asterisk.destino_qt_ligacao_diretoria
						 (
						   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
						   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
						   '{cd_diretoria}', 
						   NULL, 
						   1, 
						   NULL, 
						   NULL, 
						   20
						 ) 
						AS 
						 (
						   destino TEXT, 
						   qt_ligacao BIGINT
						 )
					 ORDER BY qt_ligacao DESC
					 LIMIT {nr_top}	
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		esc("{nr_top}", $args["nr_top"],$qr_sql);
		esc("{cd_diretoria}", $args["cd_diretoria"],$qr_sql);
		
		#echo "<pre>$qr_sql</pre>";
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function gerenciaValor( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT s.conta,
						   SUM(q.vl_ligacao) AS vl_ligacao
					  FROM (SELECT ramal,
								   vl_ligacao 
							  FROM asterisk.ramal_vl_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   NULL, 
								   NULL, 
								   2, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   vl_ligacao NUMERIC
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 GROUP BY s.conta
					 ORDER BY vl_ligacao DESC
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function gerenciaDuracao( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT s.conta,
						   SUM(q.hr_ligacao) AS hr_ligacao
					  FROM (SELECT ramal,
								   hr_ligacao 
							  FROM asterisk.ramal_hr_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'), 
								   NULL, 
								   NULL, 
								   1, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   hr_ligacao INTERVAL
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
					 GROUP BY s.conta
					 ORDER BY hr_ligacao DESC
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}	
	
	function gerenciaQuantidade( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT s.conta,
						   SUM(q.qt_ligacao) AS qt_ligacao
					  FROM (SELECT ramal,
								   qt_ligacao 
							  FROM asterisk.ramal_qt_ligacao
								 (
								   TO_DATE('{dt_ini}','DD/MM/YYYY'), 
								   TO_DATE('{dt_fim}','DD/MM/YYYY'),  
								   NULL, 
								   NULL, 
								   NULL, 
								   NULL, 
								   NULL, 
								   20
								 ) 
								AS 
								 (
								   ramal TEXT, 
								   qt_ligacao BIGINT
								 )) AS q
					  LEFT JOIN asterisk.sip s
						ON s.nr_ramal = q.ramal::integer
				     GROUP BY s.conta
					 ORDER BY qt_ligacao DESC
		       ";
		esc("{dt_ini}", $args["dt_ini"],$qr_sql);
		esc("{dt_fim}", $args["dt_fim"],$qr_sql);
		
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}

}
?>