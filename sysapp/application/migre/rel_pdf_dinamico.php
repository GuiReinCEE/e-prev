<?
	include ('inc/pdfClasses/class.ezpdf.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// --------------------------------------------------------------------
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$mes_hoje = date("m");
	$ano_hoje = date("Y");
	$dia_hoje = date("d");

	$pdf =& new Cezpdf();
	$sql = "	select 	esquema, tabela, query, clausula_where, ordem, grupo, titulo, num_colunas, fonte, ";
	$sql = $sql . " 	cd_proprietario, restricao_acesso, tipo, ";
	$sql = $sql . " 	pos_x, largura, mostrar_sombreamento, tam_fonte, tam_fonte_titulo, mostrar_cabecalho, mostrar_linhas, orientacao, qt_pagina ";
	$sql = $sql . " 	from 	projetos.relatorios ";
	$sql = $sql . " 	where 	cd_relatorio = $c ";
	$rs=pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$v_titulo = $reg['titulo'];
	$v_num_colunas = $reg['num_colunas'];
	$v_largura = $reg['largura'];	
	$v_pos_x = $reg['pos_x'];	
	$v_tam_fonte = $reg['tam_fonte'];
	$v_tam_fonte_titulo = $reg['tam_fonte_titulo'];
	$v_where = str_replace('{cd_usuario}', $Z, $reg['clausula_where']);	
	$v_where = str_replace('{p1}', $p1, $v_where);	
	$v_where = str_replace('{v1}', $v1, $v_where);	
	if ($reg['mostrar_cabecalho'] == 'S') {
		$v_mostrar_cabecalhos = 1;
	}
	else {
		$v_mostrar_cabecalhos = 0;
	}
	if ($reg['mostrar_linhas'] == 'S') {
		$v_mostrar_linhas = 1;
	}
	else {
		$v_mostrar_linhas = 0;
	}
	if ($reg['mostrar_sombreamento'] == 'S') {
		$v_mostrar_sombreamento = 1;
	}
	else {
		$v_mostrar_sombreamento = 0;
	}
	if ($formato == 'PDF') {
		$v_tipo = 'P';	}
	elseif ($formato == 'TXT') {
		$v_tipo = 'T';	}
	elseif ($formato == 'EXC') {
		$v_tipo = 'X';	}
	else {
		$v_tipo = $reg['tipo'];
	}
	
	if ($v_tipo == 'P') { 			//PDF
		switch ($reg['orientacao']) {
			case 'R': $v_orientacao = 'right'; break;
			case 'C': $v_orientacao = 'center'; break;
			case 'L': $v_orientacao = 'left'; break;
		}
		switch ($reg['fonte']) {
			case 'FAR': $pdf->selectFont('inc/pdfClasses/fonts/Helvetica.afm'); break;
			case 'FVE': $pdf->selectFont('inc/pdfClasses/fonts/Verdana.afm'); break;
			case 'FCO': $pdf->selectFont('inc/pdfClasses/fonts/Courier.afm'); break;
			case 'FTI': $pdf->selectFont('inc/pdfClasses/fonts/Times-Roman.afm'); break;
		}
	// --------------------------------------------------------------------	
		$sql = $reg['query'] . ' from ' . $reg['esquema'] . '.' . $reg['tabela'] .' '. $v_where .' '. $reg['grupo'] .' '. $reg['ordem'];
		$rs=pg_query($db, $sql);
		
		if(!$rs)
		{
			exit;
		}
		
	// -------------------------------------------------------------------- Monta um array com todo o recordset:
	// -------------------------------------------------------------------- Observe que índices de arrays dinâmicos funcionam somente até 3 ocorrências (BUG!!)
		while ($r=pg_fetch_array($rs)) {
			if ($r0_ant == $r[0]) {
				switch ($v_num_colunas) {			
					case 1: $data[] = array('c0'=>$r[0]); break;
					case 2: $data[] = array('c1'=>$r[1]); break;
					case 3: $data[] = array('c1'=>$r[1],'c2'=>$r[2]); break;
					case 4: $data[] = array('c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3]); break;
					case 5: $data[] = array('c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4]); break;
					case 6: $data[] = array('c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5]); break;
					case 7: $data[] = array('c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5],'c6'=>$r[6]); break;
					case 8: $data[] = array('c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5],'c6'=>$r[6],'c7'=>$r[7]); break;
				}
			} else {
				switch ($v_num_colunas) {			
					case 1: $data[] = array('c0'=>$r[0]); break;
					case 2: $data[] = array('c0'=>$r[0],'c1'=>$r[1]); break;
					case 3: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2]); break;
					case 4: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3]); break;
					case 5: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4]); break;
					case 6: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5]); break;
					case 7: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5],'c6'=>$r[6]); break;
					case 8: $data[] = array('c0'=>$r[0],'c1'=>$r[1],'c2'=>$r[2],'c3'=>$r[3],'c4'=>$r[4],'c5'=>$r[5],'c6'=>$r[6],'c7'=>$r[7]); break;
				}
			}
			$r0_ant = $r[0];			
		}
	// -------------------------------------------------------------------- Nomes das colunas
		$sql = "	select 	cd_coluna, nome_coluna, alinhamento, largura ";
		$sql = $sql . " 	from 	projetos.relatorios_colunas ";
		$sql = $sql . " 	where 	cd_relatorio = $c order by cd_coluna";
		$rs=pg_exec($db, $sql);
		$cont = 0;
		while ($reg = pg_fetch_array($rs)) 
		{
			$Ti[$cont] = $reg['nome_coluna'];
			switch ($reg['alinhamento']) {
				case 'R': $al_col[$cont] = 'right'; break;
				case 'C': $al_col[$cont] = 'center'; break;
				case 'L': $al_col[$cont] = 'left'; break;
			}
			$larg_col[$cont] = $reg['largura'];
			
			if($reg['qt_pagina'] > 0)
			{
				if($reg['qt_pagina'] == $cont)
				{
					$pdf->ezNewPage();
				}
			}
			
			$cont = $cont + 1;
		}
		pg_close($db);
		$pdf->ezStartPageNumbers(40,20,10,'','',1);
		$pdf->addJpegFromFile('img/logo_fundacao.jpg',20,$pdf->y-10,100,0);

	// -------------------------------------------------------------------- Joga o array montado, indicando quais colunas considerar e o cabeçalho das mesmas:
	// Variáveis: 	Posicao X, Orientação, Largura relatorio. Orientação e largura das colunas, 
	// 				Mostrar cabecalho, mostrar linhas, mostrar sombreamento, tamanho da fonte, tamanho da fonte do cabecalho.
		switch ($v_num_colunas) {
			case 1: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0])); break;
			case 2: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1])); break;
			case 3: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2])); break;
			case 4: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2]),'c3'=>array('justification'=>$al_col[3], 'width'=>$larg_col[3])); break;
			case 5: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2]),'c3'=>array('justification'=>$al_col[3], 'width'=>$larg_col[3]),'c4'=>array('justification'=>$al_col[4], 'width'=>$larg_col[4])); break;
			case 6: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2]),'c3'=>array('justification'=>$al_col[3], 'width'=>$larg_col[3]),'c4'=>array('justification'=>$al_col[4], 'width'=>$larg_col[4]),'c5'=>array('justification'=>$al_col[5], 'width'=>$larg_col[5])); break;
			case 7: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2]),'c3'=>array('justification'=>$al_col[3], 'width'=>$larg_col[3]),'c4'=>array('justification'=>$al_col[4], 'width'=>$larg_col[4]),'c5'=>array('justification'=>$al_col[5], 'width'=>$larg_col[5]),'c6'=>array('justification'=>$al_col[6], 'width'=>$larg_col[6])); break;
			case 8: $v_array_colunas = array('c0'=>array('justification'=>$al_col[0], 'width'=>$larg_col[0]),'c1'=>array('justification'=>$al_col[1], 'width'=>$larg_col[1]),'c2'=>array('justification'=>$al_col[2], 'width'=>$larg_col[2]),'c3'=>array('justification'=>$al_col[3], 'width'=>$larg_col[3]),'c4'=>array('justification'=>$al_col[4], 'width'=>$larg_col[4]),'c5'=>array('justification'=>$al_col[5], 'width'=>$larg_col[5]),'c6'=>array('justification'=>$al_col[6], 'width'=>$larg_col[6]),'c7'=>array('justification'=>$al_col[7], 'width'=>$larg_col[7])); break;
		}	
	//	$v_array_colunas = array('c0'=>array('justification'=>'right'),'c1'=>array('width'=>70),'c2'=>array('width'=>150) );
		$opc = array('xPos'=>$v_pos_x,'xOrientation'=>$v_orientacao,'width'=>$v_largura,'cols'=>$v_array_colunas, 'showHeadings'=>$v_mostrar_cabecalhos,'shaded'=>$v_mostrar_sombreamento,'showLines'=>$v_mostrar_linhas,'fontSize'=>$v_tam_fonte,'titleFontSize'=>$v_tam_fonte_titulo );
		switch ($v_num_colunas) {
			case 1: $pdf->ezTable($data, array('c0'=>$Ti[0]),$v_titulo,$opc); break;
			case 2: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1]),$v_titulo,$opc); break;
			case 3: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2]),$v_titulo,$opc); break;
			case 4: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2],'c3'=>$Ti[3]),$v_titulo,$opc); break;
			case 5: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2],'c3'=>$Ti[3],'c4'=>$Ti[4]),$v_titulo,$opc); break;
			case 6: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2],'c3'=>$Ti[3],'c4'=>$Ti[4],'c5'=>$Ti[5]),$v_titulo,$opc); break;
			case 7: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2],'c3'=>$Ti[3],'c4'=>$Ti[4],'c5'=>$Ti[5],'c6'=>$Ti[6]),$v_titulo,$opc); break;
			case 8: $pdf->ezTable($data, array('c0'=>$Ti[0],'c1'=>$Ti[1],'c2'=>$Ti[2],'c3'=>$Ti[3],'c4'=>$Ti[4],'c5'=>$Ti[5],'c6'=>$Ti[6],'c7'=>$Ti[7]),$v_titulo,$opc); break;
		}	
		$mes = ($mes_hoje - 1);
		$data_hoje = $dia_hoje . ' de ' . $meses[$mes]  . ' de ' . $ano_hoje . ' - ' . date('H:m:s') . '.';
		$opc = array(justification=>'center', spacing=>2.0);
		$pdf->ezSetY(50);
		$pdf->ezText('Porto Alegre, '.$data_hoje, 7, $opc);
		$pdf->setstrokeColor(0.2,0.5,0.2); // <= Muda a cor das linhas para verde
		$pdf->line(100,20,500,20);
		$pdf->ezStream();
	}
	elseif ($v_tipo == 'T') { 			//TXT
	// --------------------------------------------------------------------	
		$sql = $reg['query'] . ' from ' . $reg['esquema'] . '.' . $reg['tabela'] .' '. $v_where .' '. $reg['grupo'] .' '. $reg['ordem'];
		$rs=pg_exec($db, $sql);
	// -------------------------------------------------------------------- Monta um string:
		while ($r=pg_fetch_array($rs)) {
			switch ($v_num_colunas) {
				case 1: $regis = $regis.$r[0].chr(10);break;
				case 2: $regis = $regis.$r[0].';'.$r[1].chr(10);break;
				case 3: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].chr(10);break;						
				case 4: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].';'.$r[3].chr(10);break;
				case 5: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].';'.$r[3].';'.$r[4].chr(10);break;
				case 6: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].';'.$r[3].';'.$r[4].';'.$r[5].chr(10);break;
				case 7: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].';'.$r[3].';'.$r[4].';'.$r[5].';'.$r[6].chr(10);break;
				case 8: $regis = $regis.$r[0].';'.$r[1].';'.$r[2].';'.$r[3].';'.$r[4].';'.$r[5].';'.$r[6].';'.$r[7].chr(10);break;
			}
		}
	// -------------------------------------------------------------------- Geração do arquivo TXT:
		$arq = '/u/www/upload/temp.txt';
		$i = fopen($arq, 'w+');
		$i = fwrite($i, $regis, 1000000);
		if ($i != strlen($regis)) {
			echo "erro".'<br>';
		}
		else {
			header('location: http://www.e-prev.com.br/upload/temp.txt');
		}
	}
	elseif ($v_tipo == 'X') { 			//Excel
	// --------------------------------------------------------------------	
		$sql = $reg['query'] . ' from ' . $reg['esquema'] . '.' . $reg['tabela'] .' '. $v_where .' '. $reg['grupo'] .' '. $reg['ordem'];
		$rs=pg_exec($db, $sql);
	// -------------------------------------------------------------------- Monta um string:
		while ($r=pg_fetch_array($rs)) {
			switch ($v_num_colunas) {
				case 1: $regis = $regis.$r[0].'$QL&';break;
				case 2: $regis = $regis.$r[0].'$%&'.$r[1].'$QL&';break;
				case 3: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$QL&';break;						
				case 4: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$%&'.$r[3].'$QL&';break;
				case 5: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$%&'.$r[3].'$%&'.$r[4].'$QL&';break;
				case 6: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$%&'.$r[3].'$%&'.$r[4].'$%&'.$r[5].'$QL&';break;
				case 7: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$%&'.$r[3].'$%&'.$r[4].'$%&'.$r[5].'$%&'.$r[6].'$QL&';break;
				case 8: $regis = $regis.$r[0].'$%&'.$r[1].'$%&'.$r[2].'$%&'.$r[3].'$%&'.$r[4].'$%&'.$r[5].'$%&'.$r[6].'$%&'.$r[7].'$QL&';break;
			}
		}
		$regis = str_replace(chr(10),'',$regis);
		$regis = str_replace(chr(13),'',$regis);
		$regis = str_replace(',',';',$regis);
		$regis = str_replace('$QL&',chr(10),$regis);
		$regis = str_replace('$%&',',',$regis);
	// -------------------------------------------------------------------- Geração do arquivo TXT:
		$arq = '/u/www/upload/temp.csv';
		$i = fopen($arq, 'w+');
		$i = fwrite($i, $regis, 1000000); // limita em 1 M o tamanho do arquivo
		if ($i != strlen($regis)) {
			echo "erro".'<br>';
		}
		else {
			$plan = fopen($arq, 'r');
			header('Content-Type:application/csv');
			header('Content-Disposition:attachment; filename=http://www.e-prev.com.br/upload/temp.csv');
			header('Content-Transfer-Encoding:binary');
			fpassthru($plan);
		}
	}
?>