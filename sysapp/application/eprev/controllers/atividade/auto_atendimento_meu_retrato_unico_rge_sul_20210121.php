<?php
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

	#### PLANO UNICO AES_SUL ####
	$_CD_PLANO = 1;
	if(!in_array($_SESSION['EMP'], array(2))) 
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
					   AND dt_liberacao IS NOT NULL
					 ORDER BY dt_base_extrato DESC
					 LIMIT 1
				  ";
		$ob_resul = pg_query($db,$qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);	
		$_REQUEST['ED'] = intval($ar_reg['cd_edicao']);
	}

	#### BUSCA TEMPLATE DO PLANO ####
	$template = getTemplate(intval($_SESSION['EMP']), intval($_SESSION['RE']), intval($_SESSION['SEQ']),intval($_REQUEST['ED']));
	
	if(trim($template) == "")
	{
		exit;
	}
	
	$tpl = new TemplatePower('meu_retrato/tpl/tpl_meu_retrato_'.trim($template).'-20210121.html');

	$tpl->prepare();	
	
	$tpl->assign("FL_VOLTAR", (intval($_SESSION['MR_CONSULTA']) == 1 ? "display:none;" : ""));
	
	if($_REQUEST['pdf'] != "S")
	{
		#### MONTA RETRATO HTML ####
		$ob_res = getMeuRetrato();
		
		$FL_SIMULACAO_MOTIVO_1              = "display:none;";
		$FL_SIMULACAO_MOTIVO_2              = "display:none;";
		$FL_CONTRIB_MES_JOIA                = "display:none;";
		$FL_CONTRIB_MES_EXTRAORDINARIA      = "display:none;";
		$FL_CONTRIB_MES_TAXA_INSCRICAO      = "display:none;";
		$FL_CONTRIB_MES_TAXA_INTEGRALIZACAO = "display:none;";		

		$NR_AJUDA_BENEFICIO_INICIAL  = 14;
		
		$NR_BOX_CONTRIBUICAO         = 515;
		$NR_BOX_CONTRIBUICAO_DETALHE = 345;
		$NR_BOX_TAM_LINHA            = 95;
		
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
		
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$tpl->assign('CD_EDICAO', intval($_REQUEST['ED']));
			$tpl->assign('FICAADICA', nl2br($ar_reg['ficaadica']));
			$tpl->assign('COMENTARIO_RENTABILIDADE', nl2br($ar_reg['comentario_rentabilidade']));

			#### LRODRIGUEZ 09/06/2016 ####
			$tpl->assign('GRAFICO_EQUILIBRIO_PLANO', $ar_reg['arquivo_comparativo']);
			$tpl->assign('PREMISSAS_PLANO', $ar_reg['arquivo_premissas_atuariais']);
			#### LRODRIGUEZ 09/06/2016 ####
			
			$tpl->assign('ANO_BASE_EXTRATO', $ar_reg['ano_base_extrato']);
			$tpl->assign('DT_HOJE', $ar_reg['dt_hoje']);
			
			$tpl->assign('EMP', intval($_SESSION['EMP']));
			$tpl->assign('RE',  intval($_SESSION['RE']));
			$tpl->assign('SEQ', intval($_SESSION['SEQ']));
			
			$tpl->assign($ar_reg['cd_linha'].'_TEXTO', $ar_reg['ds_linha']);
			$tpl->assign($ar_reg['cd_linha'].'_VALOR', number_format($ar_reg['vl_valor'],2,",","."));
			
			
			if((trim($ar_reg['cd_linha']) == "BEN_SIMULACAO_MOTIVO") and (intval($ar_reg['vl_valor']) == 1))
			{
				$FL_SIMULACAO_MOTIVO_1 = "";
				$FL_SIMULACAO_MOTIVO_2 = "display:none;";
			}	
			elseif((trim($ar_reg['cd_linha']) == "BEN_SIMULACAO_MOTIVO") and (intval($ar_reg['vl_valor']) == 2))
			{
				$FL_SIMULACAO_MOTIVO_1 = "display:none;";
				$FL_SIMULACAO_MOTIVO_2 = "";
			}		

			if((trim($ar_reg['cd_linha']) == "BEN_PERCENTUAL_PROPORCIONAL") and (floatval($ar_reg['vl_valor']) > 0))
			{
				$NR_AJUDA_BENEFICIO_INICIAL = 15;
			}
			
			if((trim($ar_reg['cd_linha']) == "BEN_PISO_MINIMO_INTEGRAL") and (intval($ar_reg['vl_valor']) == 1))
			{
				$NR_AJUDA_BENEFICIO_INICIAL = 16;
			}			

			if((trim($ar_reg['cd_linha']) == "CONTRIB_MES_JOIA") and (floatval($ar_reg['vl_valor']) > 0))
			{
				$FL_CONTRIB_MES_JOIA = "";
				$NR_BOX_CONTRIBUICAO         += $NR_BOX_TAM_LINHA;
				$NR_BOX_CONTRIBUICAO_DETALHE += $NR_BOX_TAM_LINHA;
			}	

			if((trim($ar_reg['cd_linha']) == "CONTRIB_MES_EXTRAORDINARIA") and (floatval($ar_reg['vl_valor']) > 0))
			{
				$FL_CONTRIB_MES_EXTRAORDINARIA = "";
				$NR_BOX_CONTRIBUICAO         += $NR_BOX_TAM_LINHA;
				$NR_BOX_CONTRIBUICAO_DETALHE += $NR_BOX_TAM_LINHA;				
			}	

			if((trim($ar_reg['cd_linha']) == "CONTRIB_MES_TAXA_INSCRICAO") and (floatval($ar_reg['vl_valor']) > 0))
			{
				$FL_CONTRIB_MES_TAXA_INSCRICAO = "";
				$NR_BOX_CONTRIBUICAO         += $NR_BOX_TAM_LINHA;
				$NR_BOX_CONTRIBUICAO_DETALHE += $NR_BOX_TAM_LINHA;				
			}	

			if((trim($ar_reg['cd_linha']) == "CONTRIB_MES_TAXA_INTEGRALIZACAO") and (floatval($ar_reg['vl_valor']) > 0))
			{
				$FL_CONTRIB_MES_TAXA_INTEGRALIZACAO = "";
				$NR_BOX_CONTRIBUICAO         += $NR_BOX_TAM_LINHA;
				$NR_BOX_CONTRIBUICAO_DETALHE += $NR_BOX_TAM_LINHA;				
			}				
		}
		$tpl->assign('FL_SIMULACAO_MOTIVO_1',              $FL_SIMULACAO_MOTIVO_1);
		$tpl->assign('FL_SIMULACAO_MOTIVO_2',              $FL_SIMULACAO_MOTIVO_2);
		$tpl->assign('NR_AJUDA_BENEFICIO_INICIAL',         $NR_AJUDA_BENEFICIO_INICIAL);
		
		$tpl->assign('NR_BOX_CONTRIBUICAO',                $NR_BOX_CONTRIBUICAO);
		$tpl->assign('NR_BOX_CONTRIBUICAO_DETALHE',        $NR_BOX_CONTRIBUICAO_DETALHE);
		
		$tpl->assign('FL_CONTRIB_MES_JOIA',                $FL_CONTRIB_MES_JOIA);
		$tpl->assign('FL_CONTRIB_MES_EXTRAORDINARIA',      $FL_CONTRIB_MES_EXTRAORDINARIA);
		$tpl->assign('FL_CONTRIB_MES_TAXA_INSCRICAO',      $FL_CONTRIB_MES_TAXA_INSCRICAO);
		$tpl->assign('FL_CONTRIB_MES_TAXA_INTEGRALIZACAO', $FL_CONTRIB_MES_TAXA_INTEGRALIZACAO);	


		$ar_graf_equilibrio = getEquilibrio(intval($_REQUEST['ED']), $_CD_PLANO);
		#print_r($ar_graf_equilibrio);
		
		$tpl->assign('GRAF_EQUILIBRIO_TITULO',  $ar_graf_equilibrio['titulo']);
		$tpl->assign('GRAF_EQUILIBRIO_REFERENCIA',  $ar_graf_equilibrio['ar_ano']);
		$tpl->assign('GRAF_EQUILIBRIO_PROVISAO',  $ar_graf_equilibrio['ar_provisao']);
		$tpl->assign('GRAF_EQUILIBRIO_COBERTURA',  $ar_graf_equilibrio['ar_cobertura']);
		$tpl->assign('GRAF_EQUILIBRIO_DESCRICAO',  $ar_graf_equilibrio['ds_equilibrio']);
		$tpl->assign('GRAF_EQUILIBRIO_POSICAO',  $ar_graf_equilibrio['posicao']);
		
		$tb_equilibrio = "";
		$i = 0;
		$f = count($ar_graf_equilibrio["ar_tabela"]);
		while($i < $f)
		{
			$tb_equilibrio.='
								<tr>
									<td align="center" height="30" style="font-family:Montserrat; font-weight:bold; color: #000000; font-size: 120%;">'.$ar_graf_equilibrio["ar_tabela"][$i]['nr_ano'].'</td>
									<td></td>
									<td align="right"><div style="font-size: 120%; font-family:Montserrat; padding-left: 10px; padding-right: 10px; border-radius: 13px; background-color: #059D44; color:#FFFFFF; font-weight:bold;">'.number_format($ar_graf_equilibrio["ar_tabela"][$i]['vl_provisao'],2,",",".").'</div></td>
									<td></td>
									<td align="right"><div style="font-size: 120%; font-family:Montserrat; padding-left: 10px; padding-right: 10px; border-radius: 13px; background-color: #3C2690; color:#FFFFFF; font-weight:bold;">'.number_format($ar_graf_equilibrio["ar_tabela"][$i]['vl_cobertura'],2,",",".").'</div></td>									
								</tr>
							';
			
			$i++;
		}
		$tpl->assign('GRAF_EQUILIBRIO_TABELA',  $tb_equilibrio);		
		
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
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'_20191008.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);		
		#$ob_pdf->Text(100,284, "1/4");		

		#### DADOS PLANO ####
		$ob_pdf->SetFont('segoeuil','',10);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CNPB_PLANO");	
		$ob_pdf->Text(133, 48, (trim($ar_item['ds_linha']) != "" ? "CNPB: " : "").$ar_item['ds_linha']);		
		
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(120,55, "Dt das Contribuições:");
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("DT_BASE_CONTRIBUICAO");
		$ob_pdf->Text(120,59, $ar_item['ds_linha']);
		
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(150,55, "Dt de Simulação Benefício:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("DT_BASE_SIMULACAO");
		$ob_pdf->Text(150,59, $ar_item['ds_linha']);
		

		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(120,64, "Meu Plano:");
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->Text(120,68, $ar_item['ds_linha']);
		
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->Text(150,64, "Data de Ingresso:");		
		$ob_pdf->SetFont('segoeuil','',12);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
		$ob_pdf->Text(150,68, $ar_item['ds_linha']);	
		
		
		#### DADOS CADASTRAL ####
		$nr_col_dado = 84;
		
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->SetXY(30,50);
		$ob_pdf->MultiCell($nr_col_dado, 4.5, "DADOS PESSOAIS", 0, "L");
		
		$ob_pdf->SetFont('segoeuib','',12);
		$ob_pdf->SetXY(30,56.5);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_NOME");
		$ob_pdf->MultiCell($nr_col_dado, 4.5, $ar_item['ds_linha'], 0, "L");
		
		$ob_pdf->SetY($ob_pdf->GetY() + 1);
		$ob_pdf->SetFont('segoeuil','',8);
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_NASCIMENTO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Dt Nascimento: ".$ar_item['ds_linha'], 0, "L");		
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CPF");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "CPF: ".$ar_item['ds_linha']."       "."RE: ".intval($_SESSION['EMP'])."/".intval($_SESSION['RE'])."/".intval($_SESSION['SEQ']), 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_ENDERECO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Endereço: ".trim($ar_item['ds_linha']), 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_BAIRRO");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Bairro: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_CEP");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "CEP: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item1 = getMeuRetratoItem("PARTICIPANTE_CIDADE");
		$ar_item2 = getMeuRetratoItem("PARTICIPANTE_UF");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "Cidade/UF: ".$ar_item1['ds_linha']."/".$ar_item2['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_EMAIL_1");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "E-mail 1: ".$ar_item['ds_linha'], 0, "L");
		$ob_pdf->SetX(30);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_EMAIL_2");
		$ob_pdf->MultiCell($nr_col_dado, 3.8, "E-mail 2: ".$ar_item['ds_linha'], 0, "L");

		#### CARENCIA ####
		$ob_pdf->SetFont('segoeuil','',10);
		$ob_pdf->SetXY(117,77);
		$ob_pdf->MultiCell(66, 4,"Carência do Plano", 0, "L");				
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(117,81.5);
		$ar_item = getMeuRetratoItem("BEN_CARENCIA_IDADE");
		$ob_pdf->MultiCell(66, 4, "- ".$ar_item['ds_linha'].".", 0, "L");
		$ob_pdf->SetY($ob_pdf->GetY() );
		$ob_pdf->SetX(117);
		$ar_item = getMeuRetratoItem("BEN_CARENCIA_TEMPO_INSS");
		$ob_pdf->MultiCell(66, 4, "- ".$ar_item['ds_linha'].".", 0, "L");
		$ob_pdf->SetY($ob_pdf->GetY() );
		$ob_pdf->SetX(117);
		$ar_item = getMeuRetratoItem("BEN_CARENCIA_TEMPO_PLANO");
		$ob_pdf->MultiCell(66, 4, "- ".$ar_item['ds_linha'].".", 0, "L");		


		#### CONTRIBUICAO ####
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, 108,  "CONTRIBUIÇÃO DO MÊS"); 				
		
		$font_saldo_1 = 16;
		$font_saldo_2 = 19;
		$nr_linha_tam = 8;
		
		$nr_linha =  118.5;
		$ar_item = getMeuRetratoItem("SALARIO_CONTRIB");
		$ob_pdf->SetFont('segoeuil','',$font_contrib_1);
		$ob_pdf->Text(32,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',$font_contrib_2);
		$ob_pdf->Text(125,$nr_linha, "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 			
		
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
		

		$nr_linha = $nr_linha + 12;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_TOTAL");
		$ob_pdf->SetFont('segoeuil','',16);
		$ob_pdf->Text(34,$nr_linha, $ar_item['ds_linha']); 
		$ob_pdf->SetFont('segoeuil','',22);
		$ob_pdf->Text(125,($nr_linha + 0.5), "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 	
		
		$nr_linha = $nr_linha + 8.5;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_PREVIDENCIARIA");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(31,$nr_linha -1);
		$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(31,($nr_linha + 5));
		$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_ADM");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(83,$nr_linha -1);
		$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(83,($nr_linha + 5));
		$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");			
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_SUPLEMENTAR");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(136,$nr_linha -1);
		$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(136,($nr_linha + 5));
		$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		
		
		$nr_linha = $nr_linha + 16.5;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_JOIA");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(57,$nr_linha -1);
		if(floatval($ar_item['vl_valor']) > 0)
		{
			$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		}
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(57,($nr_linha + 5));
		if(floatval($ar_item['vl_valor']) > 0)
		{		
			$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		}
		
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_EXTRAORDINARIA");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(109,$nr_linha -1);
		if(floatval($ar_item['vl_valor']) > 0)
		{
			$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		}	
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(109,($nr_linha + 5));
		if(floatval($ar_item['vl_valor']) > 0)
		{		
			$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		}	


		$nr_linha = $nr_linha + 16.5;
		$ar_item = getMeuRetratoItem("CONTRIB_MES_TAXA_INSCRICAO");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(57,$nr_linha -1);
		if(floatval($ar_item['vl_valor']) > 0)
		{
			$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		}
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(57,($nr_linha + 5));
		if(floatval($ar_item['vl_valor']) > 0)
		{		
			$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		}
		
		
		$ar_item = getMeuRetratoItem("CONTRIB_MES_TAXA_INTEGRALIZACAO");
		$ob_pdf->SetFont('segoeuil','',9);
		$ob_pdf->SetXY(109,$nr_linha -1);
		if(floatval($ar_item['vl_valor']) > 0)
		{
			$ob_pdf->MultiCell(48.5, 4, $ar_item['ds_linha'], 0, "C");		
		}	
		$ob_pdf->SetFont('segoeuil','',14);
		$ob_pdf->SetXY(109,($nr_linha + 5));
		if(floatval($ar_item['vl_valor']) > 0)
		{		
			$ob_pdf->MultiCell(48.5, 4, "R$ ".number_format($ar_item['vl_valor'],2,",","."), 0, "C");	
		}		
		
		
		#### SIMULA BENEFICIO ####
		$nr_linha =  210;
		
		$ob_pdf->SetFont('segoeuib','',17);
		$ob_pdf->Text(31, ($nr_linha + 4), "SIMULADOR DE BENEFÍCIO"); 			
		
		/*
		$ar_item = getMeuRetratoItem("BEN_RENTABILIDADE");
		$ob_pdf->SetFont('segoeuib','',13);
		$ob_pdf->SetXY(120,$nr_linha);
		$ob_pdf->MultiCell(65, 4, "RENTABILIDADE: ".number_format($ar_item['vl_valor'],2,",",".")."%", 0, "R");			
		*/
		
		$nr_linha = 226;
		$nr_linha_tam = 11;	
		
		$FL_BEN_MENSAGEM = "";
		$ar_item = getMeuRetratoItem("BEN_PERCENTUAL_PROPORCIONAL");
		if(floatval($ar_item['vl_valor']) > 0)
		{
			$FL_BEN_MENSAGEM = chr(10).chr(13).chr(10).chr(13).'Corresponde ao valor do benefício simulado, como se o participante já tivesse cumprido as carências do plano. Os participantes que optaram por NÃO pagar joia terão o valor de seu benefício reduzido, inclusive com redução do piso mínimo de benefício.';
		}	

		$ar_item = getMeuRetratoItem("BEN_PISO_MINIMO_INTEGRAL");
		if(floatval($ar_item['vl_valor']) == 1)
		{
			$FL_BEN_MENSAGEM = chr(10).chr(13).chr(10).chr(13).'Corresponde ao valor do benefício simulado, como se o participante já tivesse cumprido as carências do plano. O plano assegura um piso mínimo de benefício que é reajustado anualmente.';
		}		

		$ar_item = getMeuRetratoItem("BEN_SIMULACAO_MOTIVO");
		if(intval($ar_item['vl_valor']) == 1) //COM 36 CONTRIB
		{
			$ar_item = getMeuRetratoItem("BEN_INICIAL");
			$ob_pdf->SetFont('segoeuil','',16);
			$ob_pdf->Text(33,$nr_linha - 0.5, $ar_item['ds_linha']); 
			$ob_pdf->SetFont('segoeuil','',22);
			$ob_pdf->Text(125,($nr_linha), "R$ ".number_format($ar_item['vl_valor'],2,",",".")); 	
			
			$nr_linha = $nr_linha + 7;
			$ob_pdf->SetXY(30, $nr_linha);
			$ob_pdf->SetFont('segoeuil','',8);
			$ob_pdf->MultiCell(155, 3, 'Os resultados constantes no bloco "SIMULADOR DE BENEFÍCIO" não asseguram a futura percepção do benefício complementar no valor indicado como Benefício Inicial Simulado. A importância apresentada corresponde ao cálculo do benefício com base em parâmetros atuais (Salário Real de Contribuição e INSS calculado pela Fundação). O valor real do benefício dependerá do cálculo desses critérios na data da aposentadoria.'.$FL_BEN_MENSAGEM, 0, "J");								
		}
		elseif(intval($ar_item['vl_valor']) == 2) //SEM 36 CONTRIB
		{
			$nr_linha = $nr_linha + 7;
			$ob_pdf->SetXY(30, $nr_linha);
			$ob_pdf->SetFont('segoeuil','',11.5);
			$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
			$ob_pdf->MultiCell(155, 4, 'Para simulação de benefício é necessário ter, pelo menos, 36 contribuições para o plano.'.chr(10).chr(13).chr(10).chr(13).'Sua data de ingresso é: '.$ar_item['ds_linha'].'.'.chr(10).chr(13).chr(10).chr(13).'Aguarde as próxima edições do Meu Retrato para ver a simulação do seu benefício.'.$FL_BEN_MENSAGEM, 0, "J");								
		}
		
	
		
		#### PAGINA 2 ####
		## GRAFICO ##
		$ob_pdf->AddPage();
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-GRAF_20191008.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);	
		#$ob_pdf->Text(100,284, "2/4");		
		
		#### EQUILÍBRIO ATUARIAL ####
		$ob_pdf->SetFont('segoeuib','',18);
		$ar_item = getMeuRetratoItem("PARTICIPANTE_DS_PLANO");
		$ob_pdf->Text(35,58, "EQUILÍBRIO ATUARIAL DO PLANO ".$ar_item['ds_linha']);			
		
		
		#### GRAFICO EQUILÍBRIO ATUARIAL ####
		$im = getEquilibrioGrafico(intval($_REQUEST['ED']), $_CD_PLANO);
		$ob_pdf->Image($im, 35, 72);
		
		$ar_graf_equilibrio = getEquilibrio(intval($_REQUEST['ED']), $_CD_PLANO);	
		
		$ob_pdf->SetFont('segoeuil','',7);

		$nr_linha = 90;
		$ob_pdf->Text(145,$nr_linha, "Provisões");		
		$ob_pdf->Text(145,$nr_linha+4, "Matemáticas");		
		$ob_pdf->Text(165,$nr_linha, "Patrimônio");			
		$ob_pdf->Text(165,$nr_linha+4, "de Cobertura");			

		$nr_linha+= 10;
		foreach($ar_graf_equilibrio['ar_tabela'] as $ar_rentab_ano)
		{
			$ob_pdf->SetFont('segoeuil','',10);
			$ob_pdf->Text(132,$nr_linha, $ar_rentab_ano['nr_ano']);	
			
			$ob_pdf->SetFont('segoeuil','',10);
			$ob_pdf->Text(145,$nr_linha, number_format($ar_rentab_ano['vl_provisao'],2,",","."));		

			$ob_pdf->SetFont('segoeuil','',10);
			$ob_pdf->Text(165,$nr_linha, number_format($ar_rentab_ano['vl_cobertura'],2,",","."));				
			
			$nr_linha = $nr_linha + 5.1;
		}	

		$ob_pdf->SetFont('segoeuib','',8);
		$ob_pdf->Text(129,$nr_linha + 4, $ar_graf_equilibrio['ds_equilibrio']);				

		#### PREMISSAS ATUARIAIS ####
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(35,174.5, "PREMISSAS ATUARIAIS");		
		$ob_pdf->Image('meu_retrato/img/'.$ar_item['arquivo_premissas_atuariais'], 35, 194, ConvertSize(572,$ob_pdf->pgwidth), ConvertSize(233,$ob_pdf->pgwidth),'','',false);
		

		#### PAGINA 3 ####
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20191008.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		#$ob_pdf->Text(100,284, "3/4");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "GLOSSÁRIO");	
		
		$nr_tam = 180;
		$nr_lin = 3.2;
		$font_1 = 9;
		$font_2 = 8.5;
		$ob_pdf->SetY(62);
		
		$fl_ajuda_pdf = "S";
		include("meu_retrato/inc/ajuda_UNICO_AES_SUL.php");
		foreach($ar_faq as $ar_faq_item)
		{
			if($ar_faq_item["FL_PDF"] == "S")
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 1.2);
				$ob_pdf->SetFont('segoeuib','',$font_1);
				$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_faq_item["PERGUNTA"].":", 0, "J");			
				$ob_pdf->SetFont('segoeuil','',$font_2);
				$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_faq_item["RESPOSTA"], 0, "J");
			}
		}
		
			
		#### PAGINA 4 ####
		## FAQ ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20191008.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
		#$ob_pdf->Text(100,284, "4/4");
		$ob_pdf->SetFont('segoeuib','',18);
		$ob_pdf->Text(16.5,56, "Comentários sobre o equilíbrio do plano");	
		
		$nr_tam = 180;
		$nr_lin = 4.5;
		$font_1 = 10;
		$ob_pdf->SetY(62);		
		
		$ob_pdf->SetFont('segoeuil','',$font_1);
		$ob_pdf->MultiCell($nr_tam, $nr_lin, $ar_item['comentario_rentabilidade'], 0, "J");			
		
		
		#### PAGINA 5 ######################################################################################################
		## DEPENDENTES ##
		$ob_pdf->AddPage();
		$ob_pdf->SetMargins(16,14,5);
		$ob_pdf->Image('meu_retrato/img/'.trim($template).'-FAQ_20191008.jpg', 0, 0, ConvertSize(800,$ob_pdf->pgwidth), ConvertSize(1131,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetFont('Courier','',6);
		$ob_pdf->Text(15,284, $ar_pessoal['dt_hoje']);
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
						SELECT TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
							   TO_CHAR(e.dt_base_extrato,'YYYY') AS ano_base_extrato,
							   TO_CHAR(CURRENT_TIMESTAMP,'DD/MM/YYYY HH24:MI:SS') AS dt_hoje,
							   epd.*,
							   e.ficaadica,
							   e.comentario_rentabilidade,
							   e.arquivo_comparativo,
							   e.arquivo_premissas_atuariais
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

	
	function getEquilibrio($cd_edicao, $cd_plano)
	{
		global $db;
		
		$sql = "
				SELECT pl.descricao
				  FROM public.planos pl
				 WHERE pl.cd_plano = ".intval($cd_plano);
		$rs  = pg_query($db,$sql);
		$reg = pg_fetch_array($rs);
		
		$NOME_PLANO = $reg['descricao'];		

		$qr_select = "
						SELECT ee.nr_ano, 
						       ee.vl_provisao, 
							   ee.vl_cobertura,
							   TO_CHAR(e.dt_equilibrio,'DD/MM/YYYY') AS dt_equilibrio,
							   e.ds_equilibrio
						  FROM meu_retrato.edicao_equilibrio ee
						  JOIN meu_retrato.edicao e
						    ON e.cd_edicao = ee.cd_edicao
						 WHERE ee.dt_exclusao IS NULL
						   AND ee.cd_edicao = ".intval($cd_edicao)."
						 ORDER BY nr_ano 
					 ";
		#echo $qr_select;
		$ob_res = pg_query($db, $qr_select);	
		
		$ar_ano       = "[";
		$ar_provisao  = "[";
		$ar_cobertura = "[";
		$ar_tabela    = Array();
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$ds_equilibrio = $ar_reg['ds_equilibrio'];
			$dt_equilibrio = $ar_reg['dt_equilibrio'];
			
			$ar_ano.= ($ar_ano != "[" ? "," : "")."".$ar_reg['nr_ano']."";
			$ar_provisao.= ($ar_provisao != "[" ? "," : "")."".$ar_reg['vl_provisao']."";
			$ar_cobertura.= ($ar_cobertura != "[" ? "," : "")."".$ar_reg['vl_cobertura']."";
			
			$ar_tabela[] = $ar_reg;
		}
		$ar_ano      .= "]";
		$ar_provisao .= "]";
		$ar_cobertura.= "]";

		
		$ar_ret['titulo']         = 'PLANO '.$NOME_PLANO.' - '.$dt_equilibrio;  
		$ar_ret['ar_tabela']      = $ar_tabela;
		$ar_ret['ar_ano']         = $ar_ano;
		$ar_ret['ar_provisao']    = $ar_provisao;
		$ar_ret['ar_cobertura']   = $ar_cobertura;
		$ar_ret['ds_equilibrio']  = $ds_equilibrio;
		$ar_ret['dt_equilibrio']  = $dt_equilibrio;
		$ar_ret['posicao']        = (trim($dt_equilibrio) != "" ? "Posição referente à ".$dt_equilibrio : "");
		
		return $ar_ret;		
	}


	function getEquilibrioGrafico($cd_edicao, $cd_plano)
	{
		/* pChart library inclusions */ 
		include("meu_retrato/inc/pChart2.1.3/class/pData.class.php"); 
		include("meu_retrato/inc/pChart2.1.3/class/pDraw.class.php"); 
		include("meu_retrato/inc/pChart2.1.3/class/pImage.class.php"); 	
		include("meu_retrato/inc/pChart2.1.3/class/pIndicator.class.php");		
		
		
		$ar_reg = getEquilibrio($cd_edicao, $cd_plano);
		
		$ds_plano          = $ar_reg['titulo'];
		$dt_referencia     = $ar_reg['dt_equilibrio'];
		$ar_titulo         = $ar_reg['ar_titulo'];
		$ar_cota_acumulada = $ar_reg['ar_cota_acumulada'];
		
		
		
		
		$nr_conta     = 0;
		$nr_fim       = count($ar_reg['ar_tabela']);
		$ar_ano       = Array();
		$ar_provisao  = Array();
		$ar_cobertura = Array();
		while($nr_conta < $nr_fim)
		{
			$ar_ano[]       = $ar_reg['ar_tabela'][$nr_conta]['nr_ano'];
			$ar_provisao[]  = $ar_reg['ar_tabela'][$nr_conta]['vl_provisao'];
			$ar_cobertura[] = $ar_reg['ar_tabela'][$nr_conta]['vl_cobertura'];
			$nr_conta++;
		}		
				
		/*
		echo "<PRE>";
		#print_r($ar_reg);
		print_r($ar_provisao);
		print_r($ar_cobertura);
		print_r($ar_ano);
		exit;
		*/
		
		#### CONFIG ####
		$dir_fonte = $_SESSION['MR_DIR_FONTE'];
		
		/* Create and populate the pData object */ 
		$MyData = new pData();   
		$MyData->addPoints($ar_provisao,"Provisões Matemáticas"); 
		$MyData->addPoints($ar_cobertura,"Patrimônio de Cobertura"); 
		$MyData->addPoints($ar_ano,"Labels"); 
		$MyData->setSerieDescription("Labels","Months"); 
		$MyData->setAbscissa("Labels"); 	
		/* Will replace the whole color scheme by the "light" palette */
		$MyData->loadPalette("meu_retrato/inc/pChart2.1.3/palettes/evening.color", TRUE);		
		
		$width     = 380;
		$height    = 282;		
		
		/* Create the pChart object */ 
		$myPicture = new pImage($width,$height,$MyData); 

		/* Turn of Antialiasing */ 
		$myPicture->Antialias = FALSE; 

		/* Add a border to the picture */ 
		$myPicture->drawRectangle(0,0,($width - 5),($height - 5),array("R"=>255,"G"=>255,"B"=>255)); 
		
		/* Write the chart title */  
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."Forgotte.ttf","FontSize"=>11)); 
		$myPicture->drawText(150,30,$ds_plano ,array("FontSize"=>12,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 

		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."calibri.ttf","FontSize"=>4)); 
		$myPicture->drawText(150,($height - 20),"Posição referente à ".$dt_referencia." - em Milhões",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 
		
		/* Set the default font */ 
		$myPicture->setFontProperties(array("FontName"=>$dir_fonte."pf_arma_five.ttf","FontSize"=>6)); 

		/* Define the chart area */ 
		$myPicture->setGraphArea(40,50,($width - 40),($height - ((trim($dt_referencia) != "") ? 63 : 50)));   

		/* Draw the scale */ 
		$scaleSettings = array("Mode"=>SCALE_MODE_ADDALL, "XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
		$myPicture->drawScale($scaleSettings); 

		/* Write the chart legend */ 
		$myPicture->drawLegend(($width - 150),20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL)); 
		
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
?>