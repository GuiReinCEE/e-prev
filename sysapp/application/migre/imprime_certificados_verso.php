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
				SELECT TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
					   pc.cd_spc AS cd_plano_spc, 
					   pc.nome_certificado AS nome_plano_certificado, 
					   pc.pos_imagem, 
					   pc.largura_imagem, 
					   pc.coluna_1, 
					   pc.coluna_2,
                       pc.cd_plano					   
			      FROM public.planos_certificados pc 
			     WHERE pc.dt_final IS NULL
			       AND pc.cd_plano = CASE WHEN ".intval($_REQUEST['pl'])." = 1 AND ".intval($_REQUEST['emp'])." = 3 THEN 3 ELSE ".intval($_REQUEST['pl'])." END
			  ";
	
	#echo "<PRE>$qr_sql</PRE>"; exit;
			
	$ob_resul = pg_exec($db, $qr_sql);	
	$reg = pg_fetch_array($ob_resul);
	
	
	$pdf =& new Cezpdf();
	$pdf->ezStartPageNumbers(40,20,10,'','',1);
	$pdf->selectFont('inc/pdfClasses/fonts/Helvetica.afm');
	$cont = 0;
	$data = array(array('coluna1'=>$reg['coluna_1'],'coluna2'=>$reg['coluna_2']));
	if (intval($reg['cd_plano']) == 2) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>6.2,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/logo_ceeeprev.jpg',50,30,85,0);
	}
	elseif (intval($reg['cd_plano']) == 1) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>6.2,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/logo_plano_1.jpg',50,30,55,0);
	}
	elseif (intval($reg['cd_plano']) == 3) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>6.2,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/logo_plano_3.jpg',50,30,55,0);
	}	
	elseif (intval($reg['cd_plano']) == 6) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>6.2,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/img_logo_plano_6.jpg',50,30,55,0);
	}
	elseif (intval($reg['cd_plano']) == 7) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>6.2,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/img_logo_plano_7.jpg',50,30,105,0);
	}	
	elseif (intval($reg['cd_plano']) == 8) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>8,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/img_logo_plano_8.jpg',50,30,105,0);
	}
	elseif (intval($reg['cd_plano']) == 9) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>9,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/img_logo_plano_9.jpg',50,30,105,0);
	}		
	
	$pdf->addJpegFromFile('img/img_disqueeletro.jpg',250,30,85,0);
	$pdf->addpngFromFile('img/img_logo_fundacao_prev3.png',420,35,135,0);
	$pdf->setColor(1.0,1.0,1.0); // Para imprimir o número da página em branco.
	$pdf->ezStream();
?>