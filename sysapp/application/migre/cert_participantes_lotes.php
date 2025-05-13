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
	$txt_dt_inicial	= ( $dt_inicial    == '' ? date('Y-m-d') : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? date('Y-m-d') : convdata_br_iso($dt_final));
	$pdf =& new Cezpdf();
	$pdf->selectFont('inc/pdfClasses/fonts/Times-Roman.afm');
	$sql =        " select 	cd_plano, cd_empresa, cd_registro_empregado, seq_dependencia, nome, 
	 		to_char(dt_ingresso,'dd') as dia_ingresso, 
	 		to_char(dt_ingresso,'mm') as mes_ingresso, 
	 		to_char(dt_ingresso,'yyyy') as ano_ingresso,
	 		to_char(dt_aprovacao_spc,'dd/mm/yyyy') as dt_aprovacao_spc, 
	 		cd_plano_spc, 
	 		nome_plano_certificado, 
	 		pos_imagem, 
	 		largura_imagem 
	 from 	projetos.certificados_participantes_tmp cpt
    WHERE dt_aprovacao_spc = 

(SELECT MAX(dt_aprovacao_spc) 
FROM projetos.certificados_participantes_tmp ctp1 
WHERE ctp1.cd_empresa = cpt.cd_empresa 
 and ctp1.cd_registro_empregado = cpt.cd_registro_empregado and ctp1.seq_dependencia = cpt.seq_dependencia)
 
 order by nome
	
	";
//	echo $sql;
//	$sql = $sql . " and 	p.cd_empresa = " . $emp . " and p.cd_registro_empregado = " . $re . " and p.seq_dependencia = " . $seq;
	$pdf->ezStartPageNumbers(40,20,10,'','',1);
	$rs=pg_exec($db, $sql);
	$cont = 0;
		
	while ($reg=pg_fetch_array($rs)) 
	{
		$y = 2;
		/*
		while ($y < 44) {
			$x = 1;
			while ($x < 28) {
				$pdf->addJpegFromFile('img/img_logo_fundacao_fundo7_2.jpg',($x*20),($y*18),16,0);
				$x++;
			}
			$y ++;
		}	
		*/
		$pdf->setColor(0.0,0.0,0.0); // Para imprimir o texto em preto.
		$pdf->addpngFromFile('img/img_logo_fundacao_prev7.png',150,$pdf->y-100,300,0);	
		$pdf->setlinestyle(1);
		$pdf->line(120,710,470,710);

		$pdf->ezSetY(810);	
		$opc = array(justification=>'right', spacing=>1.5);  
		$pdf->ezText($reg['cd_empresa'].'/'.$reg['cd_registro_empregado'].'-'.$reg['seq_dependencia'], 12, $opc);
		
		$pdf->ezSetY(700);
		$opc = array(justification=>'center', spacing=>1.5);  
		$pdf->ezText('CERTIFICADO DE PARTICIPANTE', 24, $opc);
		$pdf->line(100,650,500,650);
		$pdf->ezSetY(630); // <= Altura da próxima linha (Eixo Y)
		$pdf->ezText('A Fundação CEEE de Seguridade Social - ELETROCEEE certifica que ', 15, $opc);
		$nome = str_replace(' Das ', ' das ',(str_replace(' Da ', ' da ',(str_replace(' Dos ', ' dos ', str_replace(' De ', ' de ',ucwords(strtolower($reg['nome']))))))));	
		$pdf->ezText ($nome, 20, $opc);	
		$pdf->ezText('é participante do '.$reg['nome_plano_certificado'], 15, $opc);
		$opc = array(justification=>'center', spacing=>1.0);
		$mes_ing = ($reg['mes_ingresso'] - 1);
		$data_ingresso = $reg['dia_ingresso'] . ' de ' . $meses[$mes_ing]  . ' de ' . $reg['ano_ingresso'] . '.';
		$pdf->ezText('desde ' . $data_ingresso, 15, $opc);
		#$pdf->addpngFromFile('img/logo_plano_'.$reg['cd_plano'].'.png',$reg['pos_imagem'],425,$reg['largura_imagem'],0); // <= (imagem, X, Y, altura,...)
		$pdf->addJpegFromFile('img/logo_plano_'.$reg['cd_plano'].'.jpg',$reg['pos_imagem'],425,$reg['largura_imagem'],0); // <= (imagem, X, Y, altura,...)
		$pdf->ezSetY(410);
		$pdf->ezText('Administradora: Fundação CEEE de Seguridade Social - ELETROCEEE', 15, $opc);
		$pdf->ezText('CNPJ: 90.884.412/0001-24', 15, $opc);
		$pdf->ezSetY(370);	
		$pdf->ezText('Contatos', 12, $opc);
		$pdf->ezText('Disque-Eletro: 0800 51 2596', 12, $opc);
		$pdf->ezText('Site: www.fundacaoceee.com.br', 12, $opc);
		$pdf->ezText('Endereço: Rua dos Andradas, 702/9º - Porto Alegre - RS', 12, $opc);
		$pdf->ezSetY(280);	
		$pdf->ezText($reg['nome_plano_certificado'], 15, $opc);
		$pdf->ezText('Cadastro Nacional de Planos de Benefícios: '.$reg['cd_plano_spc'], 15, $opc);
		$pdf->ezSetY(235);
		$pdf->ezText('O presente Plano de Benefícios é regido por regulamento aprovado pela', 15, $opc);
		$pdf->ezText('Superintendência Nacional de Previdência Complementar - PREVIC', 15, $opc);	
		$pdf->ezSetY(195);
		$mes = ($mes_hoje - 1);
		$data_hoje = $dia_hoje . ' de ' . $meses[$mes]  . ' de ' . $ano_hoje . '.';
		$pdf->ezText('Porto Alegre, '.$data_hoje, 15, $opc);
		$pdf->ezSetY(85);
		$pdf->addJpegFromFile('img/assinatura_presidente_claudio_cerese.jpg',190,45,200,0);
		$pdf->ezText('Claudio Henrique Mendes Ceresér', 15, $opc);  
		$pdf->ezText('Presidente', 15, $opc);		
		
		$pdf->setstrokeColor(0.2,0.5,0.2); // <= Muda a cor das linhas para verde
		$pdf->rectangle(20,20,555,788); // <= Desenha a margem externa
		$pdf->setlinestyle(3);	
		$pdf->rectangle(27,27,540,774);	// <= Desenha a margem interna
		$pdf->setColor(1.0,1.0,1.0); // Para imprimir o número da página em branco.
		$pdf->ezNewPage();
		
		
		
		
		
		/*
		
		##########################
$qr_sql = "
				SELECT TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
					   pc.cd_spc AS cd_plano_spc, 
					   pc.nome_certificado AS nome_plano_certificado, 
					   pc.pos_imagem, 
					   pc.largura_imagem, 
					   pc.coluna_1, 
					   pc.coluna_2 
			      FROM public.planos_certificados pc 
			     WHERE pc.dt_final IS NULL
			       AND pc.cd_plano = 
					CASE WHEN ".intval($reg['cd_plano'])." = 1  AND ".intval($reg['cd_empresa'])." = 3 
					     THEN 3 
						 ELSE ".intval($reg['cd_plano'])." 
					END
			  ";
	
	#echo "<PRE>$qr_sql</PRE>"; #exit;
			
	$ob_resul_verso = pg_exec($db, $qr_sql);	
	$reg_verso = pg_fetch_array($ob_resul_verso);		
		
	$data = array(array('coluna1'=>$reg_verso['coluna_1'],'coluna2'=>$reg_verso['coluna_2']));
	if (intval($reg['cd_plano']) == 2) 
	{
		$pdf->ezTable($data
		,array('coluna1'=>'Cabeçalho 1','coluna_central'=>'<i>Cabeçalho 2</i>','coluna2'=>'<i>Cabeçalho 2</i>')
		,$reg_verso['nome_plano_certificado']
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
		,$reg_verso['nome_plano_certificado']
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
		,$reg_verso['nome_plano_certificado']
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
		,$reg_verso['nome_plano_certificado']
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
		,$reg_verso['nome_plano_certificado']
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
		,$reg_verso['nome_plano_certificado']
		,array('xPos'=>20,'xOrientation'=>'right','width'=>530
		,'cols'=>array('coluna1'=>array('justification'=>'full', 'width'=>270),'coluna_central'=>array('justification'=>'full', 'width'=>20),'coluna2'=>array('justification'=>'full', 'width'=>270))
		,'showHeadings'=>0,'shaded'=>0,'showLines'=>0,'fontSize'=>8,'titleFontSize'=>10)
		);
		$pdf->addJpegFromFile('img/img_logo_plano_8.jpg',50,30,105,0);
	}	
	
	$pdf->addJpegFromFile('img/img_disqueeletro.jpg',250,30,85,0);
	$pdf->addpngFromFile('img/img_logo_fundacao_prev3.png',420,35,135,0);
	$pdf->setColor(1.0,1.0,1.0); // Para imprimir o número da página em branco.		
	$pdf->ezNewPage();		
		
	*/
		##########################
	}
//---------------------------------------------------------------------------
	pg_close($db);
//---------------------------------------------------------------------------
	$pdf->ezStream();
//	$tpl->printToScreen();	
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
//--------------------------------------------------------------	
?>