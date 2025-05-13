<?php
class parametro_meta_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function getMeta($args=array())
	{
		$qr_sql = "
					SELECT CASE WHEN i.tp_analise = '+' THEN '>='
								WHEN i.tp_analise = '-' THEN '<='
								ELSE ''
						   END AS tp_analise,
						   pm.nr_faixa,
						   pm.meta_ini,
						   pm.meta_fim,
						   COALESCE(pm.nr_digito,0) AS nr_digito,
						   pm.fl_numero
					  FROM indicador.indicador_tabela it
					  JOIN indicador.indicador i
						ON i.cd_indicador = it.cd_indicador
					  JOIN indicador_poder.parametro_meta pm	    
						ON pm.cd_indicador = it.cd_indicador
					 WHERE it.cd_indicador_tabela = ".intval($args["cd_indicado_tabela"])."
					   AND pm.dt_exclusao         IS NULL
					   AND pm.nr_semestre         = funcoes.get_semestre(CAST('".trim($args["dt_referencia"])."' AS TIMESTAMP))
					   AND pm.nr_ano              = CAST(TO_CHAR(CAST('".trim($args["dt_referencia"])."' AS TIMESTAMP),'YYYY') AS INTEGER)
					 ORDER BY pm.nr_faixa
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$retorno = '
					<div style="width: 100%; padding-right: 10px;">
					<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #FFFFFF; font-size:11px;font-family:verdana;background-color:white;">
				   ';
		$linha_0 = "";
		$linha_1 = "";
		$linha_2 = "";
		$nr_conta = 0;
		foreach($ar_reg as $ar_item)
		{
			$cor = (($nr_conta % 2) > 0 ? "#EEEEEE" : "#FFFFFF");

			$vl_1 = $ar_item['meta_ini'];
			$vl_2 = $ar_item['meta_fim'];
			
			if($ar_item['fl_numero'] == "S")
			{
				$vl_1 = number_format($vl_1,$ar_item['nr_digito'],",",".");
				$vl_2 = (trim($vl_2) == "" ? "" : number_format($vl_2,$ar_item['nr_digito'],",","."));
			}
			
			$vl_1 = (trim($ar_item['meta_fim']) == "" ? $ar_item['tp_analise'] : "").$vl_1;
			
			$linha_0.= '<td class="td_1" align="center" style="border-bottom: 1px solid black; padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$ar_item['nr_faixa'].'</td>';
			$linha_1.= '<td class="td_1" align="center" style="padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$vl_1.'</td>';
			$linha_2.= '<td class="td_1" align="center" style="padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$vl_2.'</td>';
			
			$nr_conta++;
		}
		$retorno.= '
						<tr>
							<td></td>
							'.$linha_0 .'
						</tr>
						<tr>
							<td style="border-right: 1px solid black; padding-left: 4px;padding-right: 4px;">De</td>
							'.$linha_1 .'
						</tr>
						<tr>
							<td style="border-right: 1px solid black; padding-left: 4px;padding-right: 4px;">Até</td>
							'.$linha_2.'
						</tr>
					</table>
					</div>
		           ';
		return $retorno;
	}
	
	function getIndiceFixo($args=array())
	{
		$qr_sql = "
					SELECT nr_faixa, 
					       indice_ini, 
						   indice_fim
					  FROM indicador_poder.parametro_indice
					 WHERE dt_exclusao IS NULL
					   AND tipo        = 'F'
					   AND nr_ano      = ".intval($args['nr_ano'])."

		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$retorno = '
					<div style="width: 100%; padding-right: 10px;">
					<BR>
					<b>Escala de arredondamento</b>
					<br>
					<br>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #FFFFFF; font-size:11px;font-family:verdana;background-color:white;">
				   ';
		$linha_0 = "";
		$linha_1 = "";
		$linha_2 = "";
		$nr_conta = 0;
		foreach($ar_reg as $ar_item)
		{
			$cor = (($nr_conta % 2) > 0 ? "#EEEEEE" : "#FFFFFF");

			$vl_1 = number_format($ar_item['indice_ini'],2,",",".");
			$vl_2 = (trim($ar_item['indice_fim']) == "" ? "" : number_format($ar_item['indice_fim'],2,",","."));
			
			$linha_0.= '<td class="td_1" align="center" style="border-bottom: 1px solid black; padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$ar_item['nr_faixa'].'</td>';
			$linha_1.= '<td class="td_1" align="center" style="padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$vl_1.'</td>';
			$linha_2.= '<td class="td_1" align="center" style="padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$vl_2.'</td>';
			
			$nr_conta++;
		}
		$retorno.= '
						<tr>
							<td></td>
							'.$linha_0 .'
						</tr>
						<tr>
							<td style="border-right: 1px solid black; padding-left: 4px;padding-right: 4px;">De</td>
							'.$linha_1 .'
						</tr>
						<tr>
							<td style="border-right: 1px solid black; padding-left: 4px;padding-right: 4px;">Até</td>
							'.$linha_2.'
						</tr>
					</table>
					<BR>
					</div>
		           ';
		return $retorno;
	}	
	
	function getIndiceVariavel($args=array())
	{
		$qr_sql = "
					SELECT nr_faixa, 
					       indice_ini, 
						   indice_fim
					  FROM indicador_poder.parametro_indice
					 WHERE dt_exclusao IS NULL
					   AND tipo        = 'V'
					   AND nr_ano      = ".intval($args['nr_ano'])."

		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		$retorno = '
					<div style="width: 100%; padding-right: 10px;">
					<BR>
					<b>Índice de Aplicação - VPF</b>
					<br>
					<br>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #FFFFFF; font-size:11px;font-family:verdana;background-color:white;">
				   ';
		$linha_0 = "";
		$linha_1 = "";
		$linha_2 = "";
		$nr_conta = 0;
		foreach($ar_reg as $ar_item)
		{
			$cor = (($nr_conta % 2) > 0 ? "#EEEEEE" : "#FFFFFF");

			$vl_1 = number_format($ar_item['indice_ini'],4,",",".");
			
			$linha_0.= '<td class="td_1" align="center" style="border-bottom: 1px solid black; padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$ar_item['nr_faixa'].'</td>';
			$linha_1.= '<td class="td_1" align="center" style="padding-left: 4px;padding-right: 4px;background-color:'.$cor.'">'.$vl_1.'</td>';
			
			$nr_conta++;
		}
		$retorno.= '
						<tr>
							<td></td>
							'.$linha_0 .'
						</tr>
						<tr>
							<td style="border-right: 1px solid black; padding-left: 4px;padding-right: 4px;">De</td>
							'.$linha_1 .'
						</tr>
					</table>
					<BR>
					</div>
		           ';
		return $retorno;
	}		

	function getIndiceFaixa($args=array())
	{
		$qr_sql = "
					SELECT nr_faixa, 
					       indice_ini, 
						   indice_fim
					  FROM indicador_poder.parametro_indice
					 WHERE dt_exclusao IS NULL
					   AND tipo        = 'F'
					   AND nr_ano      = ".intval($args['nr_ano'])."
					   AND ".floatval($args['vl_indice'])." BETWEEN indice_ini AND COALESCE(indice_fim,".floatval($args['vl_indice']).");

		          ";
		#echo "<PRE>$qr_sql</PRE>"; #exit;
				  
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->row_array();
		
		if(count($ar_reg) > 0)
		{
			return $ar_reg['nr_faixa'];
		}
		else
		{
			return 0;
		}
	}
}
?>