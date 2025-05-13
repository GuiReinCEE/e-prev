<?
	include ('inc/pdfClasses/class.ezpdf.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// ---------------------------------------------------------------------------------
// RELATÓRIO DE ATENDIMENTOS
// --------------
// Estrutura:
// 1. Capa
// 2. Índice
// 3. Conteúdo
// 4. Encerramento (DADOS PARA PUBLICAÇÃO)
// ---------------------------------------------------------------------------------
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$mes_hoje = date("m");
	$ano_hoje = date("Y");
	$dia_hoje = date("d");
// --------------------------------------------------------------------------------- Capa (Dados do período):
	$txt_dt_inicial	= ( $dt_inicial    == '' ? date('Y-m-d') : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? date('Y-m-d') : convdata_br_iso($dt_final));
	$pdf =& new Cezpdf();
	$pdf->selectFont('inc/pdfClasses/fonts/Helvetica.afm');
	$pdf->ezStartPageNumbers(40,20,10,'','',1);
//-------------------------------------------------------------- Capa
	$pdf->setColor(0.0,0.0,0.0); // Para imprimir o texto em preto.
	$pdf->addpngFromFile('img/img_logo_fundacao_prev7.png',150,$pdf->y-100,300,0);	
	$pdf->setlinestyle(1);
	$pdf->line(120,710,470,710);
	$pdf->addJpegFromFile('img/img_marcadagua.jpg',185,20,400,0);
	$pdf->ezSetY(810);	
	$opc = array(justification=>'right', spacing=>1.5);  
	$pdf->ezSetY(600);
	$opc = array(justification=>'center', spacing=>1.5);  
	$pdf->setstrokeColor(0.2,0.5,0.2); // <= Muda a cor das linhas para verde
	$pdf->setColor(0.2,0.4,0.2); // <= Muda a cor do texto para verde Fundação
	$pdf->ezText('Relatório de Correspondências', 24, $opc);
	$pdf->setColor(0.2,0.2,0.2); // <= Muda a cor do texto da capa
	$pdf->line(150,500,450,500);
	$pdf->ezSetY(470); // <= Altura da próxima linha (Eixo Y)	
	$pdf->ezText('Período abrangido entre '.$dt_inicial.' e '.$dt_final, 15, $opc);
	$mes_ed = ($reg['mes_edicao'] - 1);
	$pdf->ezText('', 20, $opc);
	$pdf->ezSetY(270); // <= Altura da próxima linha (Eixo Y)
	$data_ingresso = $dia_hoje . ' de ' . $meses[$mes_hoje-1]  . ' de ' . $ano_hoje . '.';	
	$pdf->ezText('Publicado em ' . $data_ingresso, 15, $opc);
	$opc = array(justification=>'center', spacing=>1.0);
	$pdf->rectangle(20,20,555,788); // <= Desenha a margem externa
	$pdf->ezNewPage();
// ----------------------------------------------------------------------------------------- Resultados por divisao
	$sql = "select distinct divisao, nome,  count(*) as nregs from projetos.correspondencias c, projetos.divisoes d
			where DATE_TRUNC('day', data) BETWEEN TO_DATE('".$txt_dt_inicial."','YYYY-MM-DD') AND TO_DATE('".$txt_dt_final."','YYYY-MM-DD') 
			and c.divisao = d.codigo
			group by divisao, nome
			order by nregs desc";
	$rs1 = pg_exec($db, $sql);
	$qt_total = 0;
	while ($reg = pg_fetch_array($rs1)) {
		$num_corr = number_format($reg['nregs'],0,',','.');
		$divisao = $reg['nome'];
		$data[] = array('divisao' => $divisao, 'ncorr' => $num_corr);
	}	
	$opc2 = array('xPos'=>300,'width'=>500,'showHeadings'=>1,'shaded'=>1,'showLines'=>0,'fontSize'=>10,'titleFontSize'=>14 );
	$pdf->addJpegFromFile('img/img_marcadagua.jpg',185,20,400,0);
	$pdf->setstrokeColor(0.2,0.5,0.2); // <= Muda a cor das linhas para verde
	$pdf->rectangle(20,20,555,788); // <= Desenha a margem externa
	$pdf->ezTable($data, array('divisao'=>'<b>Divisão</b>', 'ncorr'=>'<i>Número de correspondências</i>'),'Contagem por divisão', $opc2);	
	$pdf->ezText('Período entre '.$dt_inicial.' e '.$dt_final.'.',14,$opc);
//---------------------------------------------------------------------------
	$sql = "select codigo, nome from projetos.divisoes 
			where tipo not in ('COM', 'CON')
			order by codigo";
	$rs1 = pg_exec($db, $sql);
	$qt_total = 0;
	$opc2 = array('xPos'=>300,'width'=>500,'showHeadings'=>1,'shaded'=>1,'showLines'=>0,'fontSize'=>9,'titleFontSize'=>14 );
	while ($reg = pg_fetch_array($rs1)) {
		$divisao = $reg['codigo'];
		$nome_divisao = $reg['nome'];
		$sql = "select numero, ano, destinatario_nome, assunto from projetos.correspondencias
			where DATE_TRUNC('day', data) BETWEEN TO_DATE('".$txt_dt_inicial."','YYYY-MM-DD') AND TO_DATE('".$txt_dt_final."','YYYY-MM-DD') 
			and divisao = '".$divisao."'
			order by numero";
		$rs = pg_exec($db, $sql);
		$imprimir = 'N';
		while ($r = pg_fetch_array($rs)) {
			$data[$divisao][] = array('numero'=>$r['numero'].'/'.$r['ano'],'nome'=>$r['destinatario_nome'],'assunto'=>$r['assunto']);
			$imprimir = 'S';
		}
		if ($imprimir == 'S') {		
			$pdf->ezNewPage();
			$pdf->ezTable($data[$divisao], array('numero'=>'<b>Número</b>','nome'=>'<i>Destinatário</i>','assunto'=>'<i>Assunto</i>'),$nome_divisao, $opc2);	
			$pdf->ezText('Período entre '.$dt_inicial.' e '.$dt_final.'.',14,$opc);
		}
	}	
//---------------------------------------------------------------------------
	pg_close($db);
//---------------------------------------------------------------------------
	$pdf->ezStream();
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