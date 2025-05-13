<?
	include ('inc/pdfClasses/class.ezpdf.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// ---------------------------------------------------------------------------------
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$mes_hoje = date("m");
	$ano_hoje = date("Y");
	$dia_hoje = date("d");
	
	$qr_sql = " 
				SELECT p.cd_plano,
				       p.cd_empresa, 
				       p.cd_registro_empregado, 
					   p.seq_dependencia, 
					   p.nome, 
					   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso,
                       TO_CHAR(t.dt_ingresso_eletro,'DD') as dia_ingresso, 
					   TO_CHAR(t.dt_ingresso_eletro,'MM') as mes_ingresso, 
					   TO_CHAR(t.dt_ingresso_eletro,'YYYY') as ano_ingresso 					   
	              FROM public.participantes p
				  JOIN public.titulares t 
				    ON t.cd_empresa            = p.cd_empresa 
	               AND t.cd_registro_empregado = p.cd_registro_empregado 
	               AND t.seq_dependencia       = p.seq_dependencia 
	             WHERE p.dt_envio_certificado IS NULL 
				   AND p.dt_obito             IS NULL  
				   AND CAST(t.dt_ingresso_eletro AS DATE) BETWEEN ".(trim($_REQUEST['dt_inicial']) != "" ? "TO_DATE('".trim($_REQUEST['dt_inicial'])."','DD/MM/YYYY')" : "CURRENT_DATE")." AND ".(trim($_REQUEST['dt_final']) != "" ? "TO_DATE('".trim($_REQUEST['dt_final'])."','DD/MM/YYYY')" : "CURRENT_DATE")."
				".(trim($_REQUEST['emp']) != "" ? "AND p.cd_empresa = ".intval(trim($_REQUEST['emp'])) : "")."
				".(trim($_REQUEST['plano']) != "" ? "AND p.cd_plano = ".intval(trim($_REQUEST['plano'])) : "")."
				  ORDER BY p.nome
			  ";
	#echo "<PRE>$qr_sql</PRE>"; #exit;
	
	$ob_resul = pg_query($db, $qr_sql);			
	
	$pdf =& new Cezpdf();
	$pdf->selectFont('inc/pdfClasses/fonts/Times-Roman.afm');	
	$pdf->ezStartPageNumbers(-40,-20,10,'','',1);
	$pdf->setColor(0.0,0.0,0.0); // Para imprimir o texto em preto.
	$pdf->ezStopPageNumbers('-20','10','','','',0);  
	$pdf->setlinestyle(1);
	$cont = 0;
	while ($reg=pg_fetch_array($ob_resul)) 
	{		
		$opc = array(justification=>'right', spacing=>1.5);
		#$pdf->ezSetY(810);		
		$pdf->ezSetY(810);
		$pdf->ezText($emp.'/'.$reg['cd_registro_empregado'].'/'.$reg['seq_dependencia']."     ", 12, $opc);
		$opc = array(justification=>'center', spacing=>1.5);  
		$pdf->ezSetY(615); 
		$nome = str_replace(' Das ', ' das ',(str_replace(' Da ', ' da ',(str_replace(' Dos ', ' dos ', str_replace(' De ', ' de ',ucwords(strtolower($reg['nome']))))))));	
		$pdf->ezText ($nome, 20, $opc);	
		$pdf->ezText('', 22, $opc);
		$opc = array(justification=>'center', spacing=>1.0);
		$mes_ing = ($reg['mes_ingresso'] - 1);
		$data_ingresso = $reg['dia_ingresso'] . ' de ' . $meses[$mes_ing]  . ' de ' . $reg['ano_ingresso'] . '.';
		$pdf->ezText('desde ' . $data_ingresso, 15, $opc);
		
		$pdf->ezSetY(195);
		$mes = ($mes_hoje - 1);
		$data_hoje = $dia_hoje . ' de ' . $meses[$mes]  . ' de ' . $ano_hoje . '.';
		$pdf->ezText('Porto Alegre, '.$data_hoje, 15, $opc);
		$pdf->ezNewPage();
	}
//---------------------------------------------------------------------------
	pg_close($db);
//---------------------------------------------------------------------------
	$pdf->ezStream();
//--------------------------------------------------------------	
function convdata_br_iso($dt) {
	// Pressupõe que a data esteja no formato DD/MM/AAAA
	// A melhor forma de gravar datas no PostgreSQL é utilizando 
	// uma string no formato DDDD-MM-AA. Esta função justamente 
	// adequa a data a este formato
	$d = substr($dt, 0, 2);
	$m = substr($dt, 3, 2);
	$a = substr($dt, 6, 4);
	return $a.'-'.$m.'-'.$d;
}
?>