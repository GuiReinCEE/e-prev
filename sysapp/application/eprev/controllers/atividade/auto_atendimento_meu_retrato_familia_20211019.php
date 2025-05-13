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
	
	
	#### FAMILIA ####
	$_CD_PLANO = 9;
	if(!in_array(intval($_SESSION['EMP']), array(8,10,11,12,19,20,24,25,26,27,28,29,30,31)))
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
	
	if(intval($_REQUEST['ED']) == 0)
	{
		#### ULTIMA EDICAO ####
		$qr_sql = "
					SELECT cd_edicao
					  FROM meu_retrato.edicao
					 WHERE cd_empresa  = ".intval($_SESSION['EMP'])."
					   AND dt_exclusao IS NULL
					 ORDER BY dt_base_extrato DESC
					 LIMIT 1

				  ";
		$ob_resul = pg_query($db,$qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);	
		$_REQUEST['ED'] = intval($ar_reg['cd_edicao']);
	}
	
	#### BUSCA TEMPLATE DO PLANO ####
	$template = getTemplate(intval($_SESSION['EMP']), intval($_SESSION['RE']), intval($_SESSION['SEQ']),intval($_REQUEST['ED']));
	
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
		$tpl = new TemplatePower('meu_retrato/tpl/tpl_meu_retrato_'.trim($template).'-20211019.html');
	}
	$tpl->prepare();
	
	$VERSAO_MR = "v20211019";

	$tpl->assign("FL_VOLTAR", (intval($_SESSION['MR_CONSULTA']) == 1 ? "display:none;" : ""));
	
	if($_REQUEST['pdf'] != "S")
	{
		#### DEPENDENTES ####
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_QUANTIDADE");
		$_QT_DEP = intval($ar_item['vl_valor']);		
		$tpl->assign('PARTICIPANTE_DEPENDENTE_QUANTIDADE', (intval($_QT_DEP) > 0 ? "Meus dependentes (".intval($_QT_DEP)."):" : "Você não possui dependentes cadastrados"));
		
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_DT_ATUALIZACAO");
		$tpl->assign('PARTICIPANTE_DEPENDENTE_DT_ATUALIZACAO', (trim($ar_item['ds_linha']) != "" ? "Atualizado em ".trim($ar_item['ds_linha']) : ""));		
		
		$i = 1;
		$_LT_DEPENDENTE = "";
		while ($i <= $_QT_DEP)
		{
			$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_".$i);
			$_LT_DEPENDENTE.= "<br>- ".trim($ar_item['ds_linha']);			
			$i++;
		}
		$tpl->assign('PARTICIPANTE_DEPENDENTE_LISTA', $_LT_DEPENDENTE);		
		
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

		#### COMPOSICAO SALDO ACUMULADO ####
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PARTIC");
		$tpl->assign('GRAF_CONTRIB_ATE_HOJE_PARTIC_VALOR', floatval($ar_item['vl_valor']));
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PORTAB");
		$tpl->assign('GRAF_CONTRIB_ATE_HOJE_PORTAB_VALOR', floatval($ar_item['vl_valor']));
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PATROC");
		$tpl->assign('GRAF_CONTRIB_ATE_HOJE_PATROC_VALOR', floatval($ar_item['vl_valor']));		
		$ar_item = getMeuRetratoItem("SALDO_RENDIMENTO");
		$tpl->assign('GRAF_SALDO_RENDIMENTO_VALOR',        (floatval($ar_item['vl_valor']) < 0 ? 0 : floatval($ar_item['vl_valor'])));			
		
		
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
		$fl_incorporado  = "display:none;";
		$fl_bpd          = "";
		$nr_simulacao    = 860 + 180;
	
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
			
			if (in_array(trim($ar_reg['cd_linha']), Array("SIMULA_BENEFICIO_PRAZO_ANOS_ATUAL_C1","SIMULA_BENEFICIO_PRAZO_ANOS_ATUAL_C2","SIMULA_BENEFICIO_PRAZO_ANOS_ATUAL_C3","SIMULA_BENEFICIO_PRAZO_ANOS_NOVO_C1","SIMULA_BENEFICIO_PRAZO_ANOS_NOVO_C2","SIMULA_BENEFICIO_PRAZO_ANOS_NOVO_C3"))) 
			{
				$tpl->assign(trim($ar_reg['cd_linha']).'_TEXTO', $ar_reg['ds_linha']);
				$tpl->assign(trim($ar_reg['cd_linha']).'_VALOR', intval($ar_reg['vl_valor']));					
			}
			elseif (in_array(trim($ar_reg['cd_linha']), Array("SIMULA_TEMPO","SIMULA_TEMPO_C1","SIMULA_TEMPO_C2","SIMULA_TEMPO_C3"))) 
			{
				$tpl->assign(trim($ar_reg['cd_linha']).'_TEXTO', $ar_reg['ds_linha']);
				$tpl->assign(trim($ar_reg['cd_linha']).'_VALOR', intval($ar_reg['vl_valor']));	
			}			
			else
			{
				$tpl->assign($ar_reg['cd_linha'].'_TEXTO', $ar_reg['ds_linha']);
				$tpl->assign($ar_reg['cd_linha'].'_VALOR', number_format($ar_reg['vl_valor'],2,",","."));
			}
			
			if(trim($ar_reg['cd_linha']) == "CONTRIB_ATE_HOJE_PORTAB") 
			{
				$fl_portab_exibe = "";
			}
			
			if((trim($ar_reg['cd_linha']) == "PARTICIPANTE_DT_INCORPORADO") AND (trim($ar_reg['vl_valor'] != "")))
			{
				$fl_incorporado = "";
			}			

			if((trim($ar_reg['cd_linha']) == "FL_BPD") and (trim($ar_reg['vl_valor']) == 1))
			{
				$fl_bpd = "display:none;";
				$nr_simulacao = 550 + 180;
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
				$tpl->assign('GRAFICO_COMPARATIVO', 'grafico_compara_FAMILIA_'.$ar_reg['ano_base_extrato'].'.png');
			}				
		}
		$tpl->assign('VERSAO_MR', $VERSAO_MR);
		$tpl->assign('PORTAB_EXIBE', $fl_portab_exibe);
		$tpl->assign('INCORPORADO_EXIBE', $fl_incorporado);
		$tpl->assign('FL_BPD', $fl_bpd);
		$tpl->assign('NR_SIMULACAO', $nr_simulacao);
		
	
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
		$a = ceil(max($ar_graf_comparativo_max) + 15);
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
		
	
		
		#### PAGINA 1 ####
		## FRENTE ##
		$ob_pdf->AddPage();
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'_20211019.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']." - ".$VERSAO_MR);		
		$ob_pdf->Text(100,284, "1/6");		

		#### DADOS PLANO ####
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(118,46, "Data Base:");
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->Text(118,51, $ar_pessoal['dt_base_extrato']);
		
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CNPB_PLANO");
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(118,55, "CNPB:");		
		$ob_pdf->SetFont('segoeuil','',11);
		$ob_pdf->Text(118,59, $ar_item['ds_linha']);		
		
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(155,46, "Data Ingresso:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
		$ob_pdf->Text(155,51, $ar_item['ds_linha']);
		
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INCORPORADO");
		if(trim($ar_item['ds_linha']) != "")
		{
			$ob_pdf->SetFont('segoeuil','',8);
			$ob_pdf->Text(155,55, "Data Incorporação:");		
			$ob_pdf->SetFont('segoeuil','',11);
			$ob_pdf->Text(155,59, $ar_item['ds_linha']);		
		}
		
		$ob_pdf->SetFont('segoeuil','',10);
		$ob_pdf->SetXY(117,69);
		$ob_pdf->MultiCell(66, 4,"Meu Plano:", 0, "L");				
		$ob_pdf->SetFont('segoeuil','',13);
		$ob_pdf->SetXY(117,73.5);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->MultiCell(66, 6,$ar_item['ds_linha'], 0, "C");		

		
		#### DADOS CADASTRAL ####
		$nr_col_dado = 84;
		
		$ob_pdf->SetFont('segoeuil','',20);
		$ob_pdf->SetXY(30,41.5);
		$ob_pdf->MultiCell($nr_col_dado, 4.5, "Extrato", 0, "L");		
		
		$ob_pdf->SetFont('segoeuib','',10);
		$ob_pdf->SetXY(30,50);
		$ob_pdf->MultiCell($nr_col_dado, 4.5, "DADOS PESSOAIS", 0, "L");
		
		$ob_pdf->SetFont('segoeuib','',12);
		$ob_pdf->SetXY(30,57.5);
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
		$ob_pdf->Text(31, 105.2, "CONTRIBUIÇÃO ACUMULADA"); 		
		
		$font_saldo_1 = 16;
		$font_saldo_2 = 19;
		$nr_linha_tam = 8;
		$nr_linha = 115;
		$nr_linha_portab = 4;
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PORTAB");
		if(trim($ar_item['ds_linha']) != "")
		{
			$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
			$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
			$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
			$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 
			$nr_linha_portab = $nr_linha_tam;
		}
		
		$nr_linha = $nr_linha + $nr_linha_portab;
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PARTIC");
		$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 

		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("CONTRIB_ATE_HOJE_PATROC");
		$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 

		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("SALDO_RENDIMENTO");
		$ob_pdf->SetFont('segoeuil','',$font_saldo_1);
		$ob_pdf->Text(31,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_saldo_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 	

		$nr_linha = $nr_linha + 20;
		$ar_item = getMeuRetratoItem("SALDO_ACUMULADO");
		$ob_pdf->SetFont('segoeuil','',16);
		$ob_pdf->Text(34,($nr_linha - $nr_linha_portab), $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuib','',22);
		$ob_pdf->Text(125,(($nr_linha + 0.5) - $nr_linha_portab), "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 			
	
	
		#### CONTRIBUICAO MES ####
		$font_contrib_1 = 15;
		$font_contrib_2 = 17;
		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_TOTAL_PARTIC");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(32,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 		
		
		$nr_linha = $nr_linha + $nr_linha_tam;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_TOTAL_PATROC");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(32,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 	
		
		#### CONTRIBUICAO RISCO
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 192.5, "CONTRIBUIÇÃO RISCO"); 		
		
		$nr_linha = 205;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_RISCO_TOTAL");
		$ob_pdf->SetFont('segoeuil','',19);
		$ob_pdf->Text(34,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',19);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 

		$nr_linha = $nr_linha + 6;

		$ar_item = getMeuRetratoItem("CONTRIB_MES_RISCO_INVALIDEZ_PREMIO");
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->SetXY(31,$nr_linha);
		$ob_pdf->MultiCell(65, 4.5,$ar_item['ds_linha']."\n"."R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");				
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_RISCO_MORTE_PREMIO");
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->SetXY(120,$nr_linha);
		$ob_pdf->MultiCell(65, 4.5,$ar_item['ds_linha']."\n"."R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");			
		
		$nr_linha = $nr_linha + 12;

		$ar_item = getMeuRetratoItem("CONTRIB_MES_RISCO_INVALIDEZ_CAPITAL");
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->SetXY(31,$nr_linha);
		$ob_pdf->MultiCell(65, 4.5,$ar_item['ds_linha']."\n"."R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");				
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_RISCO_MORTE_CAPITAL");
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->SetXY(120,$nr_linha);
		$ob_pdf->MultiCell(65, 4.5,$ar_item['ds_linha']."\n"."R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");			
		
		#### CONTRIBUICAO ADMINISTRATIVA
		/*
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 181.5, "CONTRIBUIÇÃO ADMINISTRATIVA"); 		
		*/
		
		
		#### PAGINA 2 ####
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-SIMULACAO_20190916.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "2/6");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56.3, "SIMULAÇÃO");	
		
		$ob_pdf->SetFont('segoeuil','',10);
		$ob_pdf->SetXY(15, 64);
		$ob_pdf->MultiCell(180, 3, "O seu saldo acumulado no plano depende de três fatores: nível de contribuição, tempo de acumulação e rentabilidade. 

Confira, nos cenários abaixo, como seu dinheiro pode crescer no Familia Previdência.", 0, "C");		

		$cd_cenario = 1;
		while($cd_cenario <= 3)
		{
			if($cd_cenario == 1)
			{
				$nr_linha_cenario = 87;
			}
			elseif($cd_cenario == 2)
			{
				$nr_linha_cenario = 150;
			}			
			elseif($cd_cenario == 3)
			{
				$nr_linha_cenario = 212;
			}
			
			$ob_pdf->SetFont('segoeuib','',18);
			$ob_pdf->Text(26, $nr_linha_cenario, "CENÁRIO ".$cd_cenario);			
			
			#### SIMULA CONTRIB NORMAL ####
			$nr_linha = $nr_linha + 20;
			$ob_pdf->SetFont('segoeuil','',11.5);
			$ob_pdf->SetXY(50, ($nr_linha_cenario + 5));
			$ob_pdf->MultiCell(50, 4.5, "SE EU CONTINUAR CONTRIBUINDO COM", 0, "L");
			
			$ar_item = getMeuRetratoItem("SIMULA_CONTRIB_ATUAL_C".$cd_cenario);
			$ob_pdf->SetFont('segoeuib','',13);	
			$ob_pdf->SetXY(50, ($nr_linha_cenario + 15));
			$ob_pdf->SetTextColor(90,90,90);		
			$ob_pdf->MultiCell(50, 4.5, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "L");	
			$ob_pdf->SetTextColor(0,0,0);

			$ar_tempo  = getMeuRetratoItem("SIMULA_TEMPO_C".$cd_cenario);
			$ar_rentab = getMeuRetratoItem("SIMULA_RENTABILIDADE_C".$cd_cenario);
			$ob_pdf->SetFont('segoeuil','',11.5);
			$ob_pdf->SetXY(50, ($nr_linha_cenario + 21));	
			$ob_pdf->MultiCell(50, 4.5, "POR ".intval($ar_tempo['vl_valor'])." ANOS, COM UMA RENTABILIDADE ANUAL DE ".number_format($ar_rentab['vl_valor'],2,",",".")."%".($cd_cenario == 3 ? " *":"").", MEU SALDO ACUMULADO SERÁ DE:", 0, "L");		
			
			$ar_item = getMeuRetratoItem("SIMULA_SALDO_ACUMULADO_ATUAL_C".$cd_cenario);
			if(floatval($ar_item['vl_valor']) > 999999.99)
			{
				$nr_font = 16;
			}
			elseif(floatval($ar_item['vl_valor']) > 99999.99)
			{
				$nr_font = 20;
			}
			else
			{
				$nr_font = 22;
			}		
			
			$ob_pdf->SetFont('segoeuib','',$nr_font);
			$ob_pdf->SetXY(40, ($nr_linha_cenario + 44));
			$ob_pdf->SetTextColor(90,90,90);		
			$ob_pdf->MultiCell(60, 4.5, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "L");				
			$ob_pdf->SetTextColor(0,0,0);

			#--------------------------------------------------------------------------------------#
			
			#### SIMULA DOBRANDO CONTRIB ####
			$ob_pdf->SetFont('segoeuil','',11.5);
			$ob_pdf->SetXY(107, ($nr_linha_cenario + 5));
			$ob_pdf->MultiCell(55, 4.5, "MAS SE EU AUMENTAR MINHA CONTRIBUIÇÃO PARA", 0, "L");		
			
			$ar_item = getMeuRetratoItem("SIMULA_CONTRIB_NOVO_C".$cd_cenario);
			$ob_pdf->SetFont('segoeuib','',13);	
			$ob_pdf->SetXY(107, ($nr_linha_cenario + 15));	
			$ob_pdf->SetTextColor(90,90,90);		
			$ob_pdf->MultiCell(50, 4.5, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "L");	
			$ob_pdf->SetTextColor(0,0,0);
			
			$ar_tempo  = getMeuRetratoItem("SIMULA_TEMPO_C".$cd_cenario);
			$ar_rentab = getMeuRetratoItem("SIMULA_RENTABILIDADE_C".$cd_cenario);
			$ob_pdf->SetFont('segoeuil','',11.5);
			$ob_pdf->SetXY(107, ($nr_linha_cenario + 21));	
			$ob_pdf->MultiCell(50, 4.5, "POR ".intval($ar_tempo['vl_valor'])." ANOS, COM UMA RENTABILIDADE ANUAL DE ".number_format($ar_rentab['vl_valor'],2,",",".")."%, MEU SALDO ACUMULADO SERÁ DE:", 0, "L");		
			
			$ar_item = getMeuRetratoItem("SIMULA_SALDO_ACUMULADO_NOVO_C".$cd_cenario);
			if(floatval($ar_item['vl_valor']) > 999999.99)
			{
				$nr_font = 24;
			}
			elseif(floatval($ar_item['vl_valor']) > 99999.99)
			{
				$nr_font = 28;
			}
			else
			{
				$nr_font = 30;
			}		
			
			$ob_pdf->SetFont('segoeuib','',$nr_font);
			$ob_pdf->SetXY(107, ($nr_linha_cenario + 44));	
			$ob_pdf->SetTextColor(90,90,90);
			$ob_pdf->MultiCell(73, 4.5, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "L");	
			$ob_pdf->SetTextColor(0,0,0);	

			$cd_cenario++;
		}
		
		#### PAGINA 3 ####
		## GRAFICO ##
		$ob_pdf->AddPage();
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-GRAF_20190916.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);	
		$ob_pdf->Text(100,284, "3/6");		
		
		#### TABELA RENTABILIDADE MENSAL ####
		$ob_pdf->SetFont('segoeuib','',17);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->Text(16.5,56.3, "RENTABILIDADE DO PLANO ".$ar_item['ds_linha']);	
		
		
		$rentab = getRentabilidade(intval($_REQUEST['ED']), intval($_SESSION['EMP']), $_CD_PLANO);
		#echo "<PRE>".print_r($rentab,true); exit;
		
		$nr_coluna = 30;
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
				$nr_coluna = 30;
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
		$ob_pdf->Image($im, 28, 113);			

		#### ULTIMOS ANOS ####
		$ob_rentab_ano = getRentabilidadeAnterior(intval($_REQUEST['ED']), $_SESSION['EMP'], $_CD_PLANO);
		$ob_pdf->SetFont('segoeuil','',12);
		$ob_pdf->Text(139.5,117, "RENTABILIDADE NOS");		
		$ob_pdf->Text(141.5,122, "ÚLTIMOS ".count($ob_rentab_ano)." ANOS");		
		
		$nr_linha = 130;
		#while($ar_rentab_ano = pg_fetch_array($ob_rentab_ano))
		foreach($ob_rentab_ano as $ar_rentab_ano)
		{
			$ob_pdf->SetFont('segoeuib','',13);
			$ob_pdf->Text(142,$nr_linha, $ar_rentab_ano['nr_ano']);	
			
			$ob_pdf->SetFont('segoeuil','',13);
			$ob_pdf->Text(161,$nr_linha, number_format($ar_rentab_ano['nr_cota_acumulada'],2,",",".")."%");				
			
			$nr_linha = $nr_linha + 5.1;
		}	

		#### COMPARATIVO ####
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(28,190, "COMPARATIVO");	

		$im = getComparativoIMG(intval($_REQUEST['ED']));
		$ob_pdf->Image($im, 29, 195);	

		
		#### PAGINA 4 ####
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20190916.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "4/6");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "GLOSSÁRIO");	
		
		$nr_tam = 180;
		$nr_lin = 3.2;
		$font_1 = 9;
		$font_2 = 8.5;
		$ob_pdf->SetY(62);
		
		#### FAQ ####
		$ar_faq[] = array("PERGUNTA" => "Saldo acumulado:", "RESPOSTA" => "Corresponde ao total de contribuições previdenciárias (do participante e de eventuais aportes do seu empregador) acrescido do rendimento financeiro.");
		$ar_faq[] = array("PERGUNTA" => "Data de Ingresso", "RESPOSTA" => "Data da primeira contribuição para o plano Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Minha portabilidade", "RESPOSTA" => "Valor transferido de outro plano previdenciário para o plano Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Minha contribuição", "RESPOSTA" => "Corresponde ao total das suas contribuições previdenciárias ao Família Previdência. Este valor vai para a sua conta individual no plano. É destinado para a cobertura dos benefícios de Aposentadoria Normal, Abono Anual, Pecúlio por Invalidez e Benefício por Morte.");
		$ar_faq[] = array("PERGUNTA" => "Aporte do Empregador", "RESPOSTA" => "Total dos aportes feitos por seu empregador. A empresa a qual você está vinculado também pode contribuir para a sua poupança previdenciária. Esta contribuição é facultativa, podendo ser contratada pelo empregador junto ao Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Rendimento financeiro", "RESPOSTA" => "Valor correspondente à rentabilidade acumulada do plano Família Previdência. A rentabilidade impacta diretamente no benefício do plano.");
		$ar_faq[] = array("PERGUNTA" => "Minha contribuição deste mês", "RESPOSTA" => "Corresponde à contribuição previdenciária ao Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Contribuição Administrativa", "RESPOSTA" => "Contribuição destinada à cobertura das despesas da Fundação para administração do plano Família Previdência. Não é resgatável.");
		$ar_faq[] = array("PERGUNTA" => "Total Contribuição", "RESPOSTA" => "Corresponde ao somatório de sua contribuição com o aporte feito por seu empregador (se houver) no mês de referência.");
		$ar_faq[] = array("PERGUNTA" => "Contribuição Total de Risco", "RESPOSTA" => "Somatório das contribuições destinadas à cobertura dos benefícios de aposentadoria por invalidez e pensão por morte do participante. Esses benefícios são contratados facultativamente pelo participante do Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Contribuição de Risco - Invalidez", "RESPOSTA" => "Contribuição destinada somente para a cobertura do benefício de aposentadoria por invalidez. Esse benefício é contratado facultativamente pelo participante do Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Contribuição de Risco - Pensão por Morte", "RESPOSTA" => "Contribuição destinada somente para a cobertura do benefício de pensão por morte do participante. Esse benefício é contratado facultativamente pelo participante do Família Previdência.");
		$ar_faq[] = array("PERGUNTA" => "Capital de Risco - Invalidez", "RESPOSTA" => "Corresponde ao valor contratado junto ao Família Previdência. Caso o participante se invalide, ele receberá o valor da cobertura contratada dividido em parcelas (mínimo de 60 parcelas).");
		$ar_faq[] = array("PERGUNTA" => "Capital de Risco - Pensão por Morte", "RESPOSTA" => "Corresponde ao valor contratado junto ao Família Previdência. Em caso de falecimento do participante, seus beneficiários receberão o valor da cobertura contratada dividido em parcelas (mínimo de 60 parcelas).");
			
			
			
		foreach($ar_faq as $ar_faq_item)
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','',$font_1);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_faq_item["PERGUNTA"], 0, "J");			
			$ob_pdf->SetFont('segoeuil','',$font_2);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_faq_item["RESPOSTA"], 0, "J");		
		}
		
		#### PAGINA 5 ####
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20190916.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "5/6");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "Comentários sobre a rentabilidade");	
		
		$nr_tam = 180;
		$nr_lin = 4.5;
		$font_1 = 10;
		$ob_pdf->SetY(62);		
		
		$ob_pdf->SetFont('segoeuil','',$font_1);
		$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_item['comentario_rentabilidade'], 0, "J");			
		
		#### PAGINA 6 ####
		## DEPENDENTES ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20190916.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		$ob_pdf->Text(100,284, "6/6");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "Dependentes");	
		
		$nr_tam = 180;
		$nr_lin = 4.5;
		$font_1 = 10;
		$ob_pdf->SetY(62);		
		
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_QUANTIDADE");
		$_QT_DEP = intval($ar_item['vl_valor']);		
		
		if(intval($_QT_DEP) > 0)
		{
			$i = 1;
			$_LT_DEPENDENTE = "";
			while ($i <= $_QT_DEP)
			{
				$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_".$i);
				$_LT_DEPENDENTE.= "- ".trim($ar_item['ds_linha']).chr(10);			
				$i++;
			}
			
			$ob_pdf->SetFont('segoeuil','',$font_1);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, $_LT_DEPENDENTE, 0, "J");	

			$ar_item = getMeuRetratoItem("PARTICIPANTE_DEPENDENTE_DT_ATUALIZACAO");
			$ob_pdf->SetFont('segoeuil','',$font_1 - 3);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, (trim($ar_item['ds_linha']) != "" ? chr(10)."Atualizado em ".trim($ar_item['ds_linha']) : ""), 0, "J");				
		}
		else
		{
			$ob_pdf->SetFont('segoeuil','',$font_1);
			$ob_pdf->MultiCell($nr_tam, $nr_lin, "Você não possui dependentes cadastrados", 0, "J");			
		}		
		
		
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
					   AND epd.cd_linha             = 'SALDO_ACUMULADO'
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
		#$dir_fonte = $_SERVER['DOCUMENT_ROOT'].((($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) ? "" : "/eletroceee")."/meu_retrato/inc/pChart2.1.3/fonts/";
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
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."calibri.ttf","FontSize"=>13)); 
		$myPicture->drawText(150,30,'Rentabilidade do Plano - '.$nr_ano,array("FontSize"=>12,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 

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
		
		#$dir_img = $_SERVER['DOCUMENT_ROOT'].((($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) ? "" : "/eletroceee")."/meu_retrato/graf/";
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