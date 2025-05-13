<?php
	/* pChart library inclusions */ 
	include("meu_retrato/inc/pChart2.1.3/class/pData.class.php"); 
	include("meu_retrato/inc/pChart2.1.3/class/pDraw.class.php"); 
	include("meu_retrato/inc/pChart2.1.3/class/pImage.class.php"); 	
	include("meu_retrato/inc/pChart2.1.3/class/pIndicator.class.php");	
	
	#include_once('inc/conexao.php');
	$db = @pg_connect('host=srvpg.eletroceee.com.br port=5555 dbname=fundacaoweb user=gerente');
	#$db = @pg_connect('host=127.0.0.1 port=5432 dbname=fundacaoweb user=gerente');

	
	$ar_meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	
	if ((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) and ((($_REQUEST['EMP'] != "") and ($_REQUEST['RE'] != "") and ($_REQUEST['SEQ'] != "")) or ($_REQUEST['P'] != "")))
	{
		session_start();
		$_SESSION['SID']  = 0;
		
		if($_REQUEST['P'] != "")
		{
			$qr_sql = "
						SELECT cd_empresa,
							   cd_registro_empregado,
							   seq_dependencia
						  FROM public.participantes
						 WHERE funcoes.cripto_re(cd_empresa,cd_registro_empregado,seq_dependencia) = '".$_REQUEST['P']."'
					  ";
			$ob_resul = pg_query($db,$qr_sql);	
			$ar_reg = pg_fetch_array($ob_resul);
			
			$_REQUEST['EMP'] = $ar_reg['cd_empresa'];
			$_REQUEST['RE']  = $ar_reg['cd_registro_empregado'];
			$_REQUEST['SEQ'] = $ar_reg['seq_dependencia'];
		}
		
		$_SESSION['EMP']  = $_REQUEST['EMP'];
		$_SESSION['RE']   = $_REQUEST['RE'];
		$_SESSION['SEQ']  = $_REQUEST['SEQ'];
		$_SESSION['ID_AUTOATENDIMENTO'] = session_id();
	}
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	
	#echo "<PRE>".print_r($_SESSION,true)."</PRE>"; exit;
	
	#### CRMPREV ####
	$_CD_PLANO = 6;
	if(!in_array($_SESSION['EMP'], array(6))) 
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
						Acesso não permitido
					</h1>
				</center>
				<br><br><br>
             ";
		exit;
	}
	
	#### LOG ####
	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 ) 
				VALUES
					 (
					   ".$_SESSION['SID'].",
					   CURRENT_TIMESTAMP,
					   'MEU_RETRATO_".($_REQUEST['pdf'] == "S" ? "PDF" : "")."'
					 )
		      ";
	@pg_query($db,$qr_sql);	
	
	#### BUSCA TEMPLATE DO PLANO ####
	$template = getTemplate(intval($_SESSION['EMP']), intval($_SESSION['RE']), intval($_SESSION['SEQ']),intval($_REQUEST['ED']));
	
	if(trim($template) == "")
	{
		exit;
	}
	
	#### BUSCA ARQUIVO GRAFICO COMPARATIVO ####
	$qr_sql = "
				SELECT e.arquivo_comparativo
				  FROM meu_retrato.edicao e
				 WHERE e.cd_edicao = ".intval($_REQUEST['ED'])."
			  ";
	$ob_resul = pg_query($db,$qr_sql);	
	$ar_reg   = pg_fetch_array($ob_resul);	
	$_ARQ_COMPARATIVO = trim($ar_reg['arquivo_comparativo']);	
	
	#### BUSCA ARQUIVO TPL ####
	$qr_sql = "
				SELECT e.arquivo_tpl
				  FROM meu_retrato.edicao e
				 WHERE e.cd_edicao = ".intval($_REQUEST['ED'])."
			  ";
	$ob_resul = pg_query($db,$qr_sql);	
	$ar_reg   = pg_fetch_array($ob_resul);	
	$_ARQ_TPL = trim($ar_reg['arquivo_tpl']);
	
	if(trim($_ARQ_TPL) != "")
	{
		$tpl = new TemplatePower('meu_retrato/tpl/'.trim($_ARQ_TPL));
	}
	else
	{
		$tpl = new TemplatePower('meu_retrato/tpl/tpl_meu_retrato_'.trim($template).'-20200115.html');
	}
	$tpl->prepare();	
	
	$tpl->assign("FL_VOLTAR", (intval($_SESSION['MR_CONSULTA']) == 1 ? "display:none;" : ""));
	
	if($_REQUEST['pdf'] != "S")
	{
		#### GRAFICO EVOLUCAO DO SALDO ACUMULADO ####
		$ar_evo_saldo = getGraficoEvolucaoSaldo();
		$tpl->assign('GRAF_EVOL_SALDO_ACUMULADO', $ar_evo_saldo["saldo_acumulado"]);
		$tpl->assign('GRAF_EVOL_SALDO_DATA',      $ar_evo_saldo["saldo_referencia"]);
		$tpl->assign('GRAF_EVOL_SALDO_MAX',       $ar_evo_saldo["saldo_max"] );		
		
		#### GRAFICO EVOLUCAO DO SALDO ACUMULADO NOVO ####
		$ar_evo_saldo = getGraficoEvolucaoSaldoNovo();
		$tpl->assign('GRAF_EVOL_SALDO_ACUMULADO_NOVO', $ar_evo_saldo["saldo_acumulado"]);
		$tpl->assign('GRAF_EVOL_SALDO_DATA_NOVO',      $ar_evo_saldo["saldo_referencia"]);
		$tpl->assign('GRAF_EVOL_SALDO_MAX_NOVO',       $ar_evo_saldo["saldo_max"] );	
		
		#### VALOR TOTAL PAGO EM BENEFICIO ####
		$vl_beneficio_pago_total = getBeneficioPagoTotal();
		$tpl->assign('APOSENTADO_VL_BENEFICIO_PAGO_TOTAL_VALOR', number_format($vl_beneficio_pago_total,2,",","."));
		
		#### TABELA E GRAFICO RENTABILIDADE MENSAL ####
		$rentab = getRentabilidade(intval($_REQUEST['ED']), intval($_SESSION['EMP']), $_CD_PLANO);
		$tpl->assign('RENTAB_DT_REFERENCIA', $rentab['dt_referencia']);
		$tpl->assign('RENTAB_ANO', $rentab['nr_ano']);

		#echo "<PRE>".print_r($rentab,true); exit;

		$nr_conta = 0;
		$nr_fim = count($rentab['ar_titulo']);
		$GRAF_RENTAB_MENSAL = "";
		$GRAF_RENTAB_ACUMULADA = "";
		while($nr_conta < $nr_fim)
		{
			$tpl->assign('RENTAB_MES_'.($nr_conta + 1), ($rentab['ar_cota_mes'][$nr_conta] == "" ? "-" : number_format($rentab['ar_cota_mes'][$nr_conta],2,",",".")));
			$tpl->assign('RENTAB_MES_ACUM_'.($nr_conta + 1), ($rentab['ar_cota_acumulada'][$nr_conta] == "" ? "-" : number_format($rentab['ar_cota_acumulada'][$nr_conta],2,",",".")));
			
			$tpl->assign('RENTAB_COR_MES_'.($nr_conta + 1), ($rentab['ar_cota_mes'][$nr_conta] == "" ? "#10171F" : ($rentab['ar_cota_mes'][$nr_conta] >= 0.00 ? "#06FA3A" : "#FC0003")));
			$tpl->assign('RENTAB_COR_ACUM_MES_'.($nr_conta + 1), ($rentab['ar_cota_acumulada'][$nr_conta] == "" ? "#10171F" : ($rentab['ar_cota_acumulada'][$nr_conta] >= 0.00 ? "#06FA3A" : "#FC0003")));

			if(trim($rentab['ar_cota_acumulada'][$nr_conta]) != "")
			{
				$GRAF_RENTAB_ACUMULADA.= ($GRAF_RENTAB_ACUMULADA != "" ? "," : "")."[".$nr_conta.",".floatval($rentab['ar_cota_acumulada'][$nr_conta])."]";
				$GRAF_RENTAB_MENSAL.= ($GRAF_RENTAB_MENSAL != "" ? "," : "")."[".$nr_conta.",".floatval($rentab['ar_cota_mes'][$nr_conta])."]";
			}
			
			$nr_conta++;
		}
		
		$tpl->assign('GRAF_RENTAB_MENSAL',$GRAF_RENTAB_MENSAL);
		$tpl->assign('GRAF_RENTAB_ACUMULADA',$GRAF_RENTAB_ACUMULADA);	

		#### RENTABILIDADE ANOS ANTERIORES #### 
		$ob_rentab_ano = getRentabilidadeAnterior(intval($_REQUEST['ED']), $_SESSION['EMP'], $_CD_PLANO, "ASC");
		$nr_conta = 0;
		$GRAF_RENTAB_ANO_ANTERIOR = "";
		$GRAF_RENTAB_ANO_ANTERIOR_LABEL = "";
		#while($ar_rentab_ano = pg_fetch_array($ob_rentab_ano))
		foreach($ob_rentab_ano as $ar_rentab_ano)
		{
			$GRAF_RENTAB_ANO_ANTERIOR_LABEL.= ($GRAF_RENTAB_ANO_ANTERIOR_LABEL != "" ? "," : "")."[".$nr_conta.",".intval($ar_rentab_ano['nr_ano'])."]";
			$GRAF_RENTAB_ANO_ANTERIOR.= ($GRAF_RENTAB_ANO_ANTERIOR != "" ? "," : "")."[".$nr_conta.",".floatval($ar_rentab_ano['nr_cota_acumulada'])."]";
			$nr_conta++;
		}		
		$tpl->assign('GRAF_RENTAB_ANO_ANTERIOR',$GRAF_RENTAB_ANO_ANTERIOR);	
		$tpl->assign('GRAF_RENTAB_ANO_ANTERIOR_LABEL',$GRAF_RENTAB_ANO_ANTERIOR_LABEL);	
		$tpl->assign('GRAF_RENTAB_ANO_ANTERIOR_LABEL_QT',$nr_conta);		
		
		
		#### MONTA RETRATO HTML ####
		$ob_res = getMeuRetrato();
		
		$fl_portab_exibe = "display:none;";
		$fl_ben_carencia = "";
		$ft_ben_carencia = "font-size: 280%;";
		
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$tpl->assign('EMP',                      $_SESSION['EMP']);
			$tpl->assign('RE',                       $_SESSION['RE']);
			$tpl->assign('SEQ',                      $_SESSION['SEQ']);
			$tpl->assign('CD_EDICAO',                intval($_REQUEST['ED']));
			$tpl->assign('FICAADICA',                nl2br($ar_reg['ficaadica']));
			$tpl->assign('COMENTARIO_RENTABILIDADE', nl2br($ar_reg['comentario_rentabilidade']));
			$tpl->assign('ANO_BASE_EXTRATO',         $ar_reg['ano_base_extrato']);
			$tpl->assign('DT_BASE_EXTRATO',          $ar_reg['dt_base_extrato']);
			$tpl->assign('DT_HOJE',                  $ar_reg['dt_hoje']);			
			
			$tpl->assign($ar_reg['cd_linha'].'_TEXTO', $ar_reg['ds_linha']);
			$tpl->assign($ar_reg['cd_linha'].'_VALOR', number_format($ar_reg['vl_valor'],2,",","."));
			
			if(trim($ar_reg['cd_linha']) == "CONTRIB_ATE_HOJE_PORTAB") 
			{
				$fl_portab_exibe = "";
			}
			
			if((trim($ar_reg['cd_linha']) == "BEN_MESES_FALTAM") and (trim($ar_reg['vl_valor']) == 0))
			{
				$fl_ben_carencia = "display:none;";
				$ft_ben_carencia = "font-size: 180%; color: #FFF808; line-height: 30px;";
			}

			#### GRAFICO COMPARATIVO #### 
			if(trim($_ARQ_COMPARATIVO) != "")
			{
				$tpl->assign('GRAFICO_COMPARATIVO', $_ARQ_COMPARATIVO);
			}
			else
			{
				$tpl->assign('GRAFICO_COMPARATIVO', 'grafico_compara_CRMPREV_'.$ar_reg['ano_base_extrato'].'.png');
			}			
		}
		$tpl->assign('PORTAB_EXIBE', $fl_portab_exibe);
		$tpl->assign('FL_BEN_CARENCIA', $fl_ben_carencia);
		$tpl->assign('FT_BEN_CARENCIA', $ft_ben_carencia);
		
	
		#### GRAFICO COMPARATIVO #### 
		$ar_graf_comparativo = getComparativo(intval($_REQUEST['ED']));
		
		$tpl->assign('GRAF_COMP_DT_INICIAL', $ar_graf_comparativo['mes_inicial']." ".$ar_graf_comparativo['ano_inicial']);
		$tpl->assign('GRAF_COMP_DT_FINAL',   $ar_graf_comparativo['mes_final']." ".$ar_graf_comparativo['ano_final']);
		$tpl->assign('GRAF_COMP_PLANO',      $ar_graf_comparativo['vl_plano']);
		$tpl->assign('GRAF_COMP_CDI',        $ar_graf_comparativo['vl_cdi']);
		$tpl->assign('GRAF_COMP_POUPANCA',   $ar_graf_comparativo['vl_poupanca']);
		$tpl->assign('GRAF_COMP_INPC',       $ar_graf_comparativo['vl_inpc']);
		$tpl->assign('GRAF_COMP_IGPM',       $ar_graf_comparativo['vl_igpm']);
		$tpl->assign('GRAF_COMP_IPCA_IBGE',  $ar_graf_comparativo['vl_ipca_ibge']);		
		
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_plano'];
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_cdi'];
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_poupanca'];
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_inpc'];
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_igpm'];
		$ar_graf_comparativo_max[] = $ar_graf_comparativo['vl_ipca_ibge'];	
		
		#### CALCULA O VALOR MÁXIMO PARA AJUSTAR A ESCALA ####
		$a = ceil(max($ar_graf_comparativo_max) + 25);
		$b = $a/10;
		$c = $b - floor($b);
		$d = $c * 10;
		$e = $a - $d;
		#echo "$a | $b | $c | $d | $e"; exit;
		
		$tpl->assign('GRAF_COMP_MAX_INDICADOR',  $e);		
		
		$tpl->printToScreen();  
	}
	else
	{
		#### MONTA RETRATO PDF ####
		$ob_res = getMeuRetrato();
		$ar_pessoal = pg_fetch_array($ob_res);
		
		require('meu_retrato/inc/fpdf17/fpdf.php');
		require('meu_retrato/inc/fpdf17/pdf.php');
		
		$ob_pdf = new PDF('P','mm','A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
		$ob_pdf->SetMargins(5,14,5);
		
		
		#### PAGINA 1 ############################################################################################################
		## FRENTE ##
		$ob_pdf->AddPage();
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-APOS-20191023.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);		
		$ob_pdf->Text(100,284, "1/4");		

		#### DADOS PLANO ####
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(120,60, "Data Base:");
		$ob_pdf->SetFont('segoeuil','',12);
		#$ob_pdf->SetXY(120,63);
		#$ob_pdf->MultiCell(60, 4, $ar_pessoal['dt_base_extrato'], 0, "C");	
		$ob_pdf->Text(120,68,$ar_pessoal['dt_base_extrato']);
		
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(155,60, "Data Ingresso:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
		$ob_pdf->Text(155,68, $ar_item['ds_linha']);		

		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(120,86, "Meu Plano:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->Text(120,94, $ar_item['ds_linha']);	

		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(155,86, "CNPB:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CNPB_PLANO");
		$ob_pdf->Text(155,94, $ar_item['ds_linha']);			


		
		#### DADOS CADASTRAL ####
		$nr_col_dado = 84;
		
		$ob_pdf->SetFont('segoeuil','',20);
		$ob_pdf->SetXY(30,45.5);
		$ob_pdf->MultiCell($nr_col_dado, 4.5, "Extrato", 0, "L");		
		
		$ob_pdf->SetFont('segoeuib','',10);
		$ob_pdf->SetXY(30,56);
		$ob_pdf->MultiCell($nr_col_dado, 4.5, "DADOS PESSOAIS", 0, "L");
		
		$ob_pdf->SetFont('segoeuib','',12);
		$ob_pdf->SetXY(30,63);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_NOME");
		$ob_pdf->MultiCell($nr_col_dado, 4.5, $ar_item['ds_linha'], 0, "L");		
				
		$ob_pdf->SetY($ob_pdf->GetY() + 1);
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_NASCIMENTO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Dt Nascimento: ".$ar_item['ds_linha'], 0, "L");		
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CPF");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "CPF: ".$ar_item['ds_linha']."       "."RE: ".$_SESSION['EMP']."/".$_SESSION['RE']."/".$_SESSION['SEQ'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_ENDERECO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Endereço: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_BAIRRO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Bairro: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CEP");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "CEP: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_cidade = getMeuRetratoItem("PARTICIPANTE_CIDADE");
		$ar_uf     = getMeuRetratoItem("PARTICIPANTE_UF");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Cidade/UF: ".$ar_cidade['ds_linha']."/".$ar_uf['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_EMAIL_1");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "E-mail 1: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_EMAIL_2");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "E-mail 2: ".$ar_item['ds_linha'], 0, "L");
		
	

		#### SALDO ####
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 120, "BENEFÍCIO"); 		
		
		$font_saldo_1 = 17;
		$font_saldo_2 = 20;
		$nr_linha_tam = 12;
		$nr_linha     = 124;

		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("APOSENTADO_DT_BENEFICIO");
		$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
		$ob_pdf->Text(31,$nr_linha, "Data de Aposentadoria na Fundação"); 
		$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
		$ob_pdf->Text(145,$nr_linha, $ar_item['ds_linha']); 
		
		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("APOSENTADO_VL_BENEFICIO");
		$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
		$ob_pdf->Text(145,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 
	
		#### EXTRATO ####
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 168.2, "EXTRATO"); 				
		
		$font_contrib_1 = 16;
		$font_contrib_2 = 19;
		$nr_linha_tam   = 13;		
		$nr_linha       = 170;
		
		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("APOSENTADO_VL_SALDO_ANTERIOR");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(140,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 	


		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("APOSENTADO_VL_BENEFICIO_PAGO");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(140,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 


		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("APOSENTADO_VL_RENTABILIDADE");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(140,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 
		
		
		#### SALDO ATUAL ####
		$ar_item = getMeuRetratoItem("APOSENTADO_VL_SALDO_ATUAL");
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 228, strtoupper($ar_item['ds_linha'])); 
		$ob_pdf->SetXY(31,241);
		$ob_pdf->SetFont('segoeuib','',36);
		$ob_pdf->MultiCell(154, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		
		
		#### PAGINA 2 ############################################################################################################
		## GRAFICO ##
		$ob_pdf->AddPage();
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-APOS-GRAF-20191023.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);	
		$ob_pdf->Text(100,284, "2/4");		
		
		#### TABELA RENTABILIDADE MENSAL ####
		$ob_pdf->SetFont('segoeuib','',18);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->Text(16.5,52.3, "RENTABILIDADE DO PLANO ".$ar_item['ds_linha']);		
		
		$rentab = getRentabilidade(intval($_REQUEST['ED']), intval($_SESSION['EMP']), $_CD_PLANO);
		#echo "<PRE>".print_r($rentab,true); exit;
		
		$nr_coluna = 36;
		$nr_linha  = 64.5;
		$nr_conta = 0;
		$nr_fim = count($rentab['ar_titulo']);
		while($nr_conta < $nr_fim)
		{
			$ob_pdf->SetFont('segoeuil','',7);
			$ob_pdf->Text($nr_coluna, $nr_linha, "MENSAL");		
			$ob_pdf->Text($nr_coluna, ($nr_linha + 8.5), "ACUMULADA");		
			
			#### MESES ####
			$ob_pdf->SetXY($nr_coluna, ($nr_linha + 14.8));
			$ob_pdf->MultiCell(21, 4, strtoupper($ar_meses[$nr_conta]), 0, "R");				
			
			$ob_pdf->SetFont('segoeuil','',13);
			$ob_pdf->Text($nr_coluna, ($nr_linha + 4.5), ($rentab['ar_cota_mes'][$nr_conta] == "" ? "" : number_format($rentab['ar_cota_mes'][$nr_conta],2,",",".")."%"));
			
			$ob_pdf->SetFont('segoeuil','',16);
			$ob_pdf->Text($nr_coluna, ($nr_linha + 14), ($rentab['ar_cota_acumulada'][$nr_conta] == "" ? "" : number_format($rentab['ar_cota_acumulada'][$nr_conta],2,",",".")."%"));
			
			if($nr_conta == 5)
			{
				$nr_coluna = 36;
				$nr_linha  = 87.5;
			}
			else
			{
				$nr_coluna = $nr_coluna + 25.5;
			}
			
			$nr_conta++;
		}
		
		#### GRAFICO RENTABILIDADE MENSAL ####
		$im = getRentabilidadeGrafico(intval($_REQUEST['ED']), intval($_SESSION['EMP']), $_CD_PLANO);
		$ob_pdf->Image($im, 35, 113);			

		#### ULTIMOS 10 ANOS ####
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->Text(146,117, "RENTABILIDADE NOS");		
		$ob_pdf->Text(148,122, "ÚLTIMOS 10 ANOS");		
		
		$ob_rentab_ano = getRentabilidadeAnterior(intval($_REQUEST['ED']), $_SESSION['EMP'], $_CD_PLANO);
		$nr_linha = 130;
		#while($ar_rentab_ano = pg_fetch_array($ob_rentab_ano))
		foreach($ob_rentab_ano as $ar_rentab_ano)
		{
			$ob_pdf->SetFont('segoeuib','',13);
			$ob_pdf->Text(149,$nr_linha, $ar_rentab_ano['nr_ano']);	
			
			$ob_pdf->SetFont('segoeuil','',13);
			$ob_pdf->Text(168,$nr_linha, number_format($ar_rentab_ano['nr_cota_acumulada'],2,",",".")."%");				
			
			$nr_linha = $nr_linha + 5.1;
		}	

		#### COMPARATIVO ####
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(35,190, "COMPARATIVO");
		
		$im = getComparativoIMG(intval($_REQUEST['ED']));
		$ob_pdf->Image($im, 35, 195);			
		
		#### PAGINA 3 ############################################################################################################
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-APOS-FAQ-20191023.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "3/4");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "GLOSSÁRIO");	
		
		$nr_tam = 180;
		$nr_lin = 3.2;
		$font_1 = 9;
		$font_2 = 8.5;
		$ob_pdf->SetY(62);
		
		
		$ar_faq[0] = array("PERGUNTA" => "Data de Ingresso", "RESPOSTA" => "Corresponde à data de inscrição no plano, quando ocorreu a primeira contribuição para o CRMPrev.");
		$ar_faq[1] = array("PERGUNTA" => "Benefício do Mês", "RESPOSTA" => "Valor do benefício pago no mês de referência do extrato.");
		$ar_faq[2] = array("PERGUNTA" => "Data de Aposentadoria na Fundação", "RESPOSTA" => "Corresponde à data em que o participante passou para condição de assistido do Plano CRMPrev.");
		$ar_faq[3] = array("PERGUNTA" => "Saldo de Conta Anterior", "RESPOSTA" => "Corresponde ao total de recursos disponíveis para pagamento de benefícios, posicionado no mês anterior a este extrato.");
		$ar_faq[4] = array("PERGUNTA" => "Saldo Atual", "RESPOSTA" => "Corresponde ao total de recursos disponíveis para pagamento de benefícios futuros, acrescido do rendimento financeiro.");
		$ar_faq[5] = array("PERGUNTA" => "Valor dos Benefícios Pagos", "RESPOSTA" => "Corresponde ao total de benefícios recebidos pelo participante no mês de referência do extrato.");
		$ar_faq[6] = array("PERGUNTA" => "Rendimento Financeiro", "RESPOSTA" => "Valor correspondente à rentabilidade do CRMPrev no mês de referência do extrato.");		

		foreach ($ar_faq as $faq)
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 1.2);
			$ob_pdf->SetFont('segoeuib','',$font_1);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, $faq["PERGUNTA"], 0, "J");			
			$ob_pdf->SetFont('segoeuil','',$font_2);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, $faq["RESPOSTA"], 0, "J");					
		}

		
		#### PAGINA 4 ############################################################################################################
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-APOS-FAQ-20191023.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "4/4");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "Comentários sobre a rentabilidade");	
		
		$nr_tam = 180;
		$nr_lin = 4.5;
		$font_1 = 10;
		$ob_pdf->SetY(62);		
		
		$ob_pdf->SetFont('segoeuil','',$font_1);
		$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_item['comentario_rentabilidade'], 0, "J");			
		
		
		#### GERA PDF ####
		$ob_pdf->Output();		
		exit;
	}
	
	########################################################
	
	function getMeuRetratoItem($cd_linha = "")
	{
		return pg_fetch_array(getMeuRetrato($cd_linha));
	}
	
	function getMeuRetrato($cd_linha = "")
	{
		global $db;
		$qr_select = "
						SELECT p.cd_empresa,
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   p.nome,
							   p.sexo,
							   p.email AS email_1,
							   p.email_profissional AS email_2,
							   CASE WHEN COALESCE(p.celular,0) > 0
									THEN TO_CHAR(p.ddd_celular,'FM(00)') || TO_CHAR(p.celular,'FM 999999999') 
									ELSE TO_CHAR(p.ddd,'FM(00)') || TO_CHAR(p.telefone,'FM 999999999') 
							   END AS telefone_contato,
							   COALESCE(p.email, p.email_profissional) AS email_contato,
							   funcoes.format_cpf(p.cpf_mf::bigint) AS cpf,
							   p.endereco,
							   p.nr_endereco,
							   p.complemento_endereco,
							   p.bairro,
							   p.cidade,
							   p.unidade_federativa AS uf,
							   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
							   TO_CHAR(p.cep,'FM99999') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
							   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
							   TO_CHAR(e.dt_base_extrato,'YYYY') AS ano_base_extrato,
							   TO_CHAR(CURRENT_TIMESTAMP,'DD/MM/YYYY HH24:MI:SS') AS dt_hoje,
							   epd.*,
							   e.ficaadica,
							   e.comentario_rentabilidade
						  FROM meu_retrato.edicao e
						  JOIN meu_retrato.edicao_participante ep
							ON ep.cd_edicao = e.cd_edicao
						  JOIN meu_retrato.edicao_participante_dado epd
							ON epd.cd_edicao_participante = ep.cd_edicao_participante
						  JOIN public.participantes p
							ON p.cd_empresa            = ep.cd_empresa
						   AND p.cd_registro_empregado = ep.cd_registro_empregado
						   AND p.seq_dependencia       = ep.seq_dependencia
						 WHERE ep.cd_empresa            = ".intval($_SESSION['EMP'])." 
						   AND ep.cd_registro_empregado = ".intval($_SESSION['RE'])." 
						   AND ep.seq_dependencia       = ".intval($_SESSION['SEQ'])."
						   AND e.cd_edicao              = ".intval($_REQUEST['ED'])."
						   ".(trim($cd_linha) != "" ? "AND epd.cd_linha = '".trim($cd_linha)."'" : "")."
					 ";
		$ob_res = pg_query($db, $qr_select);	
		
		return $ob_res;
	}
	
	function getBeneficioPagoTotal()
	{
		global $db;
		#APOSENTADO_VL_BENEFICIO_PAGO_TOTAL_VALOR
		$qr_sql = "
					SELECT SUM(epd.vl_valor) AS vl_total
					  FROM meu_retrato.edicao e
					  JOIN meu_retrato.edicao_participante ep 
						ON ep.cd_edicao = e.cd_edicao
					  JOIN meu_retrato.edicao_participante_dado epd
						ON epd.cd_edicao_participante = ep.cd_edicao_participante
					 WHERE e.dt_exclusao  IS NULL
					   --AND e.dt_liberacao IS NOT NULL 
					   AND ep.cd_empresa            = ".$_SESSION['EMP']."
					   AND ep.cd_registro_empregado = ".$_SESSION['RE']."
					   AND ep.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND e.cd_edicao              <= ".intval($_REQUEST['ED'])."
					   AND e.tp_participante        = (SELECT e1.tp_participante FROM meu_retrato.edicao e1 WHERE e1.cd_edicao = ".intval($_REQUEST['ED']).")
					   AND epd.cd_linha             = 'APOSENTADO_VL_BENEFICIO_PAGO'
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		
		return floatval($ar_reg['vl_total']);
	}	
	
	function getGraficoEvolucaoSaldoNovo()
	{
		global $db;
		$ar_mes = array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");
		
		$qr_sql = "
					SELECT TO_CHAR(e.dt_base_extrato,'MM') AS mes, 
					       TO_CHAR(e.dt_base_extrato,'YYYY') AS ano, 
					       epd.vl_valor
					  FROM meu_retrato.edicao e
					  JOIN meu_retrato.edicao_participante ep 
						ON ep.cd_edicao = e.cd_edicao
					  JOIN meu_retrato.edicao_participante_dado epd
						ON epd.cd_edicao_participante = ep.cd_edicao_participante
					 WHERE e.dt_exclusao  IS NULL
					   --AND e.dt_liberacao IS NOT NULL 
					   AND ep.cd_empresa            = ".$_SESSION['EMP']."
					   AND ep.cd_registro_empregado = ".$_SESSION['RE']."
					   AND ep.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND e.cd_edicao              <= ".intval($_REQUEST['ED'])."
					   AND e.tp_participante        = (SELECT e1.tp_participante FROM meu_retrato.edicao e1 WHERE e1.cd_edicao = ".intval($_REQUEST['ED']).")
					   AND epd.cd_linha             = 'APOSENTADO_VL_SALDO_ATUAL'
					 ORDER BY e.dt_base_extrato
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_saldo = "[";
		$ar_refer = "[";
		$nr_conta = 0;
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			$ar_saldo.= ($ar_saldo != "" ? "," : "")."".$ar_reg["vl_valor"]."";
			$ar_refer.= ($ar_refer != "" ? "," : "")."'".$ar_mes[(intval($ar_reg["mes"]) - 1)]."/".$ar_reg["ano"]."'";
			$nr_conta++;
		}
		$ar_saldo.= "]";
		$ar_refer.= "]";
		
		$ar_grafico["saldo_acumulado"]  = $ar_saldo;
		$ar_grafico["saldo_referencia"] = $ar_refer;
		$ar_grafico["saldo_max"]        = $nr_conta;
		
		return $ar_grafico;
	}
	
	function getGraficoEvolucaoSaldo()
	{
		global $db;
		$ar_mes = array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");
		
		$qr_sql = "
					SELECT TO_CHAR(e.dt_base_extrato,'MM') AS mes, 
					       TO_CHAR(e.dt_base_extrato,'YYYY') AS ano, 
					       epd.vl_valor
					  FROM meu_retrato.edicao e
					  JOIN meu_retrato.edicao_participante ep 
						ON ep.cd_edicao = e.cd_edicao
					  JOIN meu_retrato.edicao_participante_dado epd
						ON epd.cd_edicao_participante = ep.cd_edicao_participante
					 WHERE e.dt_exclusao  IS NULL
					   --AND e.dt_liberacao IS NOT NULL 
					   AND ep.cd_empresa            = ".$_SESSION['EMP']."
					   AND ep.cd_registro_empregado = ".$_SESSION['RE']."
					   AND ep.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND e.cd_edicao              <= ".intval($_REQUEST['ED'])."
					   AND e.tp_participante        = (SELECT e1.tp_participante FROM meu_retrato.edicao e1 WHERE e1.cd_edicao = ".intval($_REQUEST['ED']).")
					   AND epd.cd_linha             = 'SALDO_ACUMULADO'
					 ORDER BY e.dt_base_extrato
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_saldo = "[";
		$ar_refer = "[";
		$nr_conta = 0;
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			$ar_saldo.= ($ar_saldo != "[" ? "," : "")."[".$nr_conta.",".$ar_reg["vl_valor"]."]";
			$ar_refer.= ($ar_refer != "[" ? "," : "")."[".$nr_conta.",'".$ar_mes[(intval($ar_reg["mes"]) - 1)]."/".$ar_reg["ano"]."']";
			$nr_conta++;
		}
		$ar_saldo.= "]";
		$ar_refer.= "]";
		
		$ar_grafico["saldo_acumulado"]  = $ar_saldo;
		$ar_grafico["saldo_referencia"] = $ar_refer;
		$ar_grafico["saldo_max"]        = $nr_conta;
		
		return $ar_grafico;
	}	
	
	function getRentabilidadeAnterior($cd_edicao, $cd_empresa, $cd_plano, $ordem = "DESC")
	{
		global $db;
		$qr_select = "
						SELECT er.nr_ano,
							   er.nr_cota_acumulada
						  FROM meu_retrato.edicao_rentabilidade er
						 WHERE er.cd_edicao  = ".intval($cd_edicao)."
						   AND er.cd_empresa = ".intval($cd_empresa)."
						   AND er.cd_plano   = ".intval($cd_plano)."
						   AND er.nr_ano     < (SELECT MAX(er1.nr_ano)
												  FROM meu_retrato.edicao_rentabilidade er1
												 WHERE er1.cd_edicao  = er.cd_edicao
												   AND er1.cd_empresa = er.cd_empresa
												   AND er1.cd_plano   = er.cd_plano)

						 UNION

						SELECT er.nr_ano,
							   er.nr_cota_acumulada
						  FROM meu_retrato.edicao_rentabilidade er
						 WHERE er.cd_edicao         = ".intval($cd_edicao)."
						   AND er.cd_empresa        = ".intval($cd_empresa)."
						   AND er.cd_plano          = ".intval($cd_plano)."
						   AND er.nr_cota_acumulada IS NOT NULL
						   AND UPPER(er.mes)        = 'DEZ'
						   AND er.nr_ano            = (SELECT MAX(er1.nr_ano)
									                     FROM meu_retrato.edicao_rentabilidade er1
									                    WHERE er1.cd_edicao  = er.cd_edicao
									                      AND er1.cd_empresa = er.cd_empresa
									                      AND er1.cd_plano   = er.cd_plano)
												   
						 ORDER BY nr_ano ASC
						 LIMIT 11
					 ";					 
		#echo "<PRE>".$qr_select."</PRE>";exit;
		$ob_res = pg_query($db, $qr_select);
		$ar_ret = Array();
		
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$ar_ret[] = $ar_reg;
		}
		
		if($ordem == "ASC")
		{
			if(count($ar_ret) > 10)
			{
				unset($ar_ret[0]);
			}
		}		
		elseif($ordem == "DESC")
		{
			rsort($ar_ret);
			if(count($ar_ret) > 9)
			{
				unset($ar_ret[10]);	
			}			
		}
		else
		{
			$ar_ret = Array();
		}

		#echo "<PRE>".$ordem."</PRE>";
		#echo "<PRE>".print_r($ar_ret,TRUE)."</PRE>";
		#exit;
		
		return $ar_ret;
	}	

	function getTemplate($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_edicao)
	{
		global $db;
		$qr_select = "
						SELECT epd.ds_linha
						  FROM meu_retrato.edicao e
						  JOIN meu_retrato.edicao_participante ep
							ON ep.cd_edicao = e.cd_edicao
						  JOIN meu_retrato.edicao_participante_dado epd
							ON epd.cd_edicao_participante = ep.cd_edicao_participante
						 WHERE ep.cd_empresa            = ".intval($cd_empresa)."
						   AND ep.cd_registro_empregado = ".intval($cd_registro_empregado)."
						   AND ep.seq_dependencia       = ".intval($seq_dependencia)."
						   AND e.cd_edicao              = ".intval($cd_edicao)."
						   AND epd.cd_linha             = 'PLANO'
					 ";
		$ob_res = pg_query($db, $qr_select);	
		$ar_reg = pg_fetch_array($ob_res);
		
		return $ar_reg['ds_linha'];
	}

	function getDadoParticipante($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		$qr_select = "
						SELECT CASE WHEN tp.dt_migracao IS NOT NULL THEN 'S' ELSE 'N' END AS fl_migrado, 
							   tp.cd_plano,
							   pl.descricao AS ds_plano,
							   CASE WHEN tp.dt_migracao IS NOT NULL 
									THEN TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') 
									ELSE TO_CHAR(tp.dt_ingresso_plano,'DD/MM/YYYY') 
							   END AS dt_ingresso_plano,					   
							   TO_CHAR(tp.dt_migracao,'DD/MM/YYYY') AS dt_migracao,
							   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento
						  FROM public.titulares_planos tp
						  JOIN public.titulares t
							ON t.cd_empresa            = tp.cd_empresa            
						   AND t.cd_registro_empregado = tp.cd_registro_empregado 
						   AND t.seq_dependencia       = tp.seq_dependencia 				  
						  JOIN public.participantes p
							ON p.cd_empresa            = tp.cd_empresa            
						   AND p.cd_registro_empregado = tp.cd_registro_empregado 
						   AND p.seq_dependencia       = tp.seq_dependencia   
						  JOIN public.planos pl
							ON pl.cd_plano = tp.cd_plano    
						 WHERE tp.cd_empresa            = ".intval($cd_empresa)."
						   AND tp.cd_registro_empregado = ".intval($cd_registro_empregado)."
						   AND tp.seq_dependencia       = ".intval($seq_dependencia)."
						   ---AND tp.dt_deslig_plano       IS NULL
						   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
															 FROM public.titulares_planos tp1 
															WHERE tp1.cd_empresa            = tp.cd_empresa 
															  AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
															  AND tp1.seq_dependencia       = tp.seq_dependencia)
					 ";
		$ob_res = pg_query($db, $qr_select);	
		$ar_reg = pg_fetch_array($ob_res);
		
		return $ar_reg;
	}
	
	function getRentabilidade($cd_edicao, $cd_empresa, $cd_plano)
	{
		global $db;
		$qr_sql = "
					SELECT p.descricao AS ds_plano,
						   er.nr_ano,
						   er.mes, 
						   er.nr_cota_mes, 
						   er.nr_cota_acumulada,
						   TO_CHAR(er.dt_indice,'DD/MM/YYYY') AS dt_indice
					  FROM meu_retrato.edicao_rentabilidade er
					  JOIN public.planos p
						ON p.cd_plano   = er.cd_plano
					 WHERE er.cd_edicao  = ".intval($cd_edicao)."
					   AND er.cd_empresa = ".intval($cd_empresa)."
					   AND er.cd_plano   = ".intval($cd_plano)."
					   AND er.nr_ano     = (SELECT MAX(er1.nr_ano)
											  FROM meu_retrato.edicao_rentabilidade er1
											 WHERE er1.cd_edicao  = er.cd_edicao
											   AND er1.cd_empresa = er.cd_empresa
											   AND er1.cd_plano   = er.cd_plano)
					ORDER BY er.dt_indice
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		$dt_referencia = "";
		$nr_ano = "";
		$ds_plano = "";
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			
			$ds_plano            = $ar_reg['ds_plano'];
			$nr_ano              = $ar_reg['nr_ano'];
			$ar_titulo[]         = $ar_reg['mes'];
			$ar_cota_mes[]       = $ar_reg['nr_cota_mes'];
			$ar_cota_acumulada[] = $ar_reg['nr_cota_acumulada'];
			
			if(trim($ar_reg['dt_indice']) != "")
			{
				$dt_referencia = $ar_reg['dt_indice'];
			}
		}
		
		$ar_retorno['ds_plano']          = $ds_plano;
		$ar_retorno['nr_ano']            = $nr_ano;
		$ar_retorno['ar_titulo']         = $ar_titulo;
		$ar_retorno['ar_cota_mes']       = $ar_cota_mes;
		$ar_retorno['ar_cota_acumulada'] = $ar_cota_acumulada;
		$ar_retorno['dt_referencia']     = $dt_referencia;
	
		/*	
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/	
		
		
		return $ar_retorno;
	}
	
	function getRentabilidadeGrafico($cd_edicao, $cd_empresa, $cd_plano)
	{
		$ar_reg = getRentabilidade($cd_edicao, $cd_empresa, $cd_plano);
		
		$ds_plano          = $ar_reg['ds_plano'];
		$nr_ano            = $ar_reg['nr_ano'];
		$dt_referencia     = $ar_reg['dt_referencia'];
		$ar_titulo         = $ar_reg['ar_titulo'];
		$ar_cota_acumulada = $ar_reg['ar_cota_acumulada'];
		
		$nr_conta = 0;
		$nr_fim   = count($ar_reg['ar_cota_mes']);
		$ar_cota_mes = Array();
		while($nr_conta < $nr_fim)
		{
			if(trim($ar_reg['ar_cota_mes'][$nr_conta]) != "")
			{
				$ar_cota_mes[] = $ar_reg['ar_cota_mes'][$nr_conta];
			}
			$nr_conta++;
		}		
		
		$nr_conta = 0;
		$nr_fim   = count($ar_reg['ar_cota_acumulada']);
		$ar_cota_acumulada = Array();
		while($nr_conta < $nr_fim)
		{
			if(trim($ar_reg['ar_cota_acumulada'][$nr_conta]) != "")
			{
				$ar_cota_acumulada[] = $ar_reg['ar_cota_acumulada'][$nr_conta];
			}
			$nr_conta++;
		}		
		
		/*
		echo "<PRE>";
		#print_r($ar_reg);
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/
		
		#### CONFIG ####
		$width     = 405;
		$height    = 259;	
		$dir_fonte = $_SESSION['MR_DIR_FONTE'];
		
		/* Create and populate the pData object */ 
		$MyData = new pData();   
		$MyData->addPoints($ar_cota_mes,"Mensal"); 
		$MyData->addPoints($ar_cota_acumulada,"Acumulada"); 
		$MyData->addPoints($ar_titulo,"Labels"); 
		$MyData->setSerieDescription("Labels","Months"); 
		$MyData->setAbscissa("Labels"); 	
		/* Will replace the whole color scheme by the "light" palette */
		$MyData->loadPalette("meu_retrato/inc/pChart2.1.3/palettes/evening.color", TRUE);		
		
		$width     = 395;
		$height    = 248;		
		
		/* Create the pChart object */ 
		$myPicture = new pImage($width,$height,$MyData); 

		/* Turn of Antialiasing */ 
		$myPicture->Antialias = FALSE; 

		/* Add a border to the picture */ 
		$myPicture->drawRectangle(0,0,($width - 5),($height - 5),array("R"=>255,"G"=>255,"B"=>255)); 
		
		/* Write the chart title */  
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."Forgotte.ttf","FontSize"=>11)); 
		$myPicture->drawText(150,30,'Rentabilidade do Plano '.$ds_plano.' - '.$nr_ano,array("FontSize"=>15,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 

		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."calibri.ttf","FontSize"=>4)); 
		$myPicture->drawText(150,($height - 20),"Posição referente à ".$dt_referencia,array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 
		
		/* Set the default font */ 
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."pf_arma_five.ttf","FontSize"=>6)); 

		/* Define the chart area */ 
		$myPicture->setGraphArea(40,50,($width - 40),($height - ((trim($dt_referencia) != "") ? 63 : 50)));   

		/* Draw the scale */ 
		$scaleSettings = array("Mode"=>SCALE_MODE_ADDALL, "XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
		$myPicture->drawScale($scaleSettings); 

		/* Write the chart legend */ 
		$myPicture->drawLegend(($width - 80),20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL)); 
		
		$Config = array("R"=>0, "G"=>0, "B"=>0, "Alpha"=>50, "AxisID"=>0, "Ticks"=>4, "DrawBox"=>1);
		$myPicture->drawThreshold(0,$Config);		
	
		/* Turn on Antialiasing */ 
		$myPicture->Antialias = TRUE; 

		/* Draw the area chart */ 
		$myPicture->drawAreaChart(); 

		/* Draw a line and a plot chart on top */ 
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10)); 
		$myPicture->drawLineChart(); 
		$myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"PlotSize"=>3,"BorderSize"=>1,"Surrounding"=>-60,"BorderAlpha"=>80)); 
		
		$dir_img = $_SESSION['MR_DIR_IMG'];
		$graph_file_name = md5(uniqid(rand(), true)).'.png';
		#$myPicture->autoOutput($dir_img.$graph_file_name); 	exit;
		$myPicture->Render($dir_img.$graph_file_name); 	
		
		return "meu_retrato/graf/".$graph_file_name;		
	}	
	
	function getComparativo($cd_edicao)
	{
		global $db;
		
		$qr_sql = "
					SELECT cd_edicao_comparativo,
					       cd_edicao, 
						   dt_inicial, 
						   dt_final,
						   TO_CHAR(dt_inicial,'YYYY') AS ano_inicial,
						   TO_CHAR(dt_final,'YYYY') AS ano_final,						   
						   funcoes.mes_extenso(dt_inicial) AS mes_inicial,
						   funcoes.mes_extenso(dt_final) AS mes_final,
						   vl_plano, 
						   vl_cdi, 
						   vl_poupanca, 
						   vl_inpc, 
						   vl_igpm, 
						   vl_ipca_ibge
					  FROM meu_retrato.edicao_comparativo
					 WHERE dt_exclusao IS NULL
					   AND cd_edicao  = ".intval($cd_edicao)."
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
	
		/*	
		echo "<PRE>";
		print_r($qr_sql);
		print_r($ar_reg);
		exit;
		*/	
		
		return $ar_reg;
	}	
	
	function getComparativoIMG($cd_edicao)
	{
		#### CONFIG ####
		$width     = 570;
		$height    = 280;	
		$dir_fonte = $_SESSION['MR_DIR_FONTE'];	
		
		#### DADOS ####
		$ar_graf_comparativo = getComparativo(intval($cd_edicao));	
		
		/* Create and populate the pData object */
		$MyData = new pData();  
		$MyData->addPoints(array($ar_graf_comparativo['vl_plano'],$ar_graf_comparativo['vl_poupanca'],$ar_graf_comparativo['vl_inpc'],$ar_graf_comparativo['vl_igpm']),"Hits");
		$MyData->addPoints(array("PLANO","POUPANÇA","INPC","IGPM"),"Browsers");
		$MyData->setSerieDescription("Browsers","Browsers");
		$MyData->setAbscissa("Browsers");
		
		$MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");
		
		/* Create the pChart object */
		$myPicture = new pImage($width,$height,$MyData);

		/* Turn of Antialiasing */ 
		$myPicture->Antialias = FALSE; 
		
		/* Write the chart title */  
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."calibri.ttf","FontSize"=>4)); 
		$myPicture->drawText(100,20, "Acumulado de ".$ar_graf_comparativo['mes_inicial']." ".$ar_graf_comparativo['ano_inicial']." a ".$ar_graf_comparativo['mes_final']." ".$ar_graf_comparativo['ano_final'],array("FontSize"=>12,"Align"=>TEXT_ALIGN_MIDDLE_LEFT)); 		

		/* Set the default font */ 
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."Forgotte.ttf","FontSize"=>14)); 

		/* Draw the chart scale */ 
		$myPicture->setGraphArea(70,50,($width - 40),($height - 30));
		
		/* Draw the scale */
		$scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
		$myPicture->drawScale($scaleSettings);

		/* Turn on shadow computing */ 
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));


		/* Create the per bar palette */
		$Palette = array("0"=>array("R"=>54,"G"=>162,"B"=>235,"Alpha"=>100),
						 "1"=>array("R"=>75,"G"=>192,"B"=>192,"Alpha"=>100),
						 "2"=>array("R"=>255,"G"=>99,"B"=>132,"Alpha"=>100),
						 "3"=>array("R"=>255,"G"=>159,"B"=>64,"Alpha"=>100));

		/* Draw the chart */ 
		$myPicture->drawBarChart(array("DisplayPos"=>LABEL_POS_TOP,"DisplayValues"=>TRUE,"Rounded"=>FALSE,"Surrounding"=>30,"OverrideColors"=>$Palette));
	
		
		$dir_img = $_SESSION['MR_DIR_IMG'];
		$graph_file_name = md5(uniqid(rand(), true)).'.png';
		#$myPicture->autoOutput($dir_img.$graph_file_name); 	exit;
		$myPicture->Render($dir_img.$graph_file_name); 	
		
		return "meu_retrato/graf/".$graph_file_name;		
	}	
	
	function YAxisFormat($valor)
	{
		return(number_format($valor,2,",",".")."%");  
	}	
?>