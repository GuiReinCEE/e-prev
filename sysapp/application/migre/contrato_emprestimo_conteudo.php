<?
		
	$nr_char   = 2.1;
	$nr_quebra = 2.5;
	
	if(($tp_imp == 0) or ($tp_imp == 1) or ($tp_imp == 3)) # 0 -> IMPRIME TUDO, 1 -> IMPRIME PAG 1 E 4, IMPRIME SOMENTE PAG 1
	{
		$ob_pdf->AddPage();
		#### NR PAGINA ####
		preenche($fl_preenche,true);
		$ob_pdf->SetFont('Arial','I',8);
		$ob_pdf->Text(200,288,'1');		
		############################################## PAGINA 1 #####################################################
		preenche($fl_preenche,true);
		$ob_pdf->SetFont('Courier','B',14);
		/*
		if ($AR_DADOS['ORIGEM_CONTRATO'] != 'I')
		{
			$ob_pdf->Text(10,10,'PROPOSTA DE EMPRÉSTIMO'); 
		}
		else
		{
			$ob_pdf->Text(10,10,'DEMONSTRATIVO DE EMPRÉSTIMO'); 
		}
		*/
		
		if ($fl_demonstrativo)
		{
			$ob_pdf->Text(10,10,'DEMONSTRATIVO DE EMPRÉSTIMO'); 
		}
		else
		{
			$ob_pdf->Text(10,10,'PROPOSTA DE EMPRÉSTIMO'); 
		}		
		
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->Text(140,10,'Contrato nº:');
		preenche($fl_preenche,false);
		$ob_pdf->Text(175,10,$_REQUEST['cd_contrato']);
		
		preenche($fl_preenche,true);
		$ob_pdf->SetFont('Arial','B',12);
		$ob_pdf->Text(10,14,'Parte Integrante do Contrato de Mútuo de Empréstimo');
		
		############################### DADOS CADASTRAIS DE IDENTIFICAÇÃO ###############################
		if(!$fl_preenche)
		{
			#### QUADRO ####			
			$ob_pdf->Line(10,18,200,18);
			$ob_pdf->Line(10,18,10,58);
			$ob_pdf->Line(10,58,200,58);
			$ob_pdf->Line(200,18,200,58);
		}
		preenche($fl_preenche,true);
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->Text(66,22,'DADOS CADASTRAIS DE IDENTIFICAÇÃO');
		
		$ob_pdf->SetFont('Courier','',12);
		$ob_pdf->Text(12,28,'Nome Completo:');
		if ($fl_cadastro) $ob_pdf->Text(48,28,$AR_DADOS['NOME']);
		
		$ob_pdf->Text(155,28,'CPF:');
		if ($fl_cadastro) $ob_pdf->Text(167,28,$AR_DADOS['CPF_MF']);
		$ob_pdf->Text(12,32,'Empresa:');
		if ($fl_cadastro) $ob_pdf->Text(34,32,$AR_DADOS['CD_EMPRESA']);
		$ob_pdf->Text(44,32,'Re.d:');
		if ($fl_cadastro) $ob_pdf->Text(60,32,$AR_DADOS['CD_REGISTRO_EMPREGADO']."/".$AR_DADOS['SEQ_DEPENDENCIA']);
		
		$ob_pdf->Text(12,36,'Endereço:');
		if ($fl_cadastro) $ob_pdf->Text(36,36,$AR_DADOS['LOGRADOURO']." ".$AR_DADOS['BAIRRO']);

		$ob_pdf->Text(12,40,'Cidade:');
		if ($fl_cadastro) $ob_pdf->Text(32,40,$AR_DADOS['CIDADE']." - ".$AR_DADOS['UNIDADE_FEDERATIVA']);
		
		$ob_pdf->Text(12,44,'Fone para Contato:');
		if ($fl_cadastro) $ob_pdf->Text(60,44,$AR_DADOS['DDD']." ".$AR_DADOS['TELEFONE']);
		$ob_pdf->Text(100,44,'Ramal:');
		if ($fl_cadastro) $ob_pdf->Text(115,44,$AR_DADOS['RAMAL']);

		$ob_pdf->SetFont('Courier','BU',12);
		$ob_pdf->Text(12,50,'DADOS BANCÁRIOS');
		
		$ob_pdf->SetFont('Courier','',12);
		$ob_pdf->Text(12,54,'Banco:');
		if ($fl_cadastro) $ob_pdf->Text(28,54,$AR_DADOS['CD_INSTITUICAO']);	
		$ob_pdf->Text(50,54,'Agência:');
		if ($fl_cadastro) $ob_pdf->Text(72,54,$AR_DADOS['CD_AGENCIA']);	
		$ob_pdf->Text(95,54,'Conta Corrente:');
		if ($fl_cadastro) $ob_pdf->Text(134,54,$AR_DADOS['CONTA']);	
		
		############################### CONDIÇÕES GERAIS ###############################
		if ($fl_financeiro) 
		{
			preenche($fl_preenche,false);
			$ob_pdf->Line(10,66,200,66);
			$ob_pdf->Line(10,66,10,114);
			$ob_pdf->Line(10,114,200,114);
			$ob_pdf->Line(200,66,200,114);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(85,70,'CONDIÇÕES GERAIS');

			$linha = 76;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text(15,$linha ,'Taxa de Preservação Patrimonial:');
			$ob_pdf->Text(100,$linha ,'[ '.($AR_DADOS['FORMA_CALCULO'] == 'P' ? 'X' : ' ').' ] PRÉ-FIXADA (Tabela PRICE)');
			
			$linha += 4;
			$ob_pdf->SetFont('Courier','BU',10);
			$ob_pdf->Text(15,$linha,'Modalidade:');	
			
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text(100,$linha ,'[ '.($AR_DADOS['FORMA_CALCULO'] == 'O' ? 'X' : ' ').' ] PÓS-FIXADA (Tabela SAC)');
			
			$ob_pdf->SetFont('Courier','BU',10);
			$linha += 4;
			$ob_pdf->Text(65,$linha,'Taxas:');	
			
			$ob_pdf->SetFont('Courier','',12);
			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['TIPO'] != 'F' ? 'X' : ' ').' ] NORMAL');
			$ob_pdf->Text(65,$linha,'Juros');	
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_JUROS_MES']) , $linha,$AR_DADOS['PERC_TX_JUROS_MES'].'%');
			$ob_pdf->Text(175,$linha,'(ao mês)');
			
			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['TIPO'] == 'F' ? 'X' : ' ').' ] FÉRIAS');	
			$ob_pdf->Text(65,$linha,'Preservação Patrimonial');	
			$ob_pdf->Text(151,$linha,':');
			
			if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
			{
				$texto = "INPC/IBGE *";
				$ob_pdf->Text(157, $linha, $texto);
				$texto = "";
			}
			if ($AR_DADOS['FORMA_CALCULO'] == "P") # PREFIXADO
			{
				$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_PRESERVACAO_PATRIMONIA']) , $linha,$AR_DADOS['PERC_TX_PRESERVACAO_PATRIMONIA'].'%');
				$ob_pdf->Text(175,$linha,'(ao mês)');
			}			

			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['MOD_RENEGOCIACAO'] == 'Sim' ? 'X' : ' ').' ] RENEGOCIAÇÃO');
			$ob_pdf->Text(65,$linha,'Efetiva de Juros');		
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_EFETIVA']), $linha,trim($AR_DADOS['PERC_TX_EFETIVA']).'%');
			$ob_pdf->Text(175,$linha,'(ao mês)');				
			
			             
			$linha += 4;
			$ob_pdf->Text(65,$linha,'Administrativa s/Valor Solicitado');		
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_ADM']), $linha,$AR_DADOS['PERC_TX_ADM'].'%');		
			
			$linha += 4;			
			$ob_pdf->Text(65,$linha,'De Risco s/Prestação Mensal');		
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_SEGURO']), $linha,$AR_DADOS['PERC_TX_SEGURO'].'%');	

			#### EXIBE TIR A PARTIR DE 10/03/2008 ####
			$ar_data_tmp = explode("/",$AR_DADOS['DT_SOLICITACAO']);
			if($ar_data_tmp[2]."-".$ar_data_tmp[1]."-".$ar_data_tmp[0] > '2008-03-03')
			{
				$linha += 4;
				$ob_pdf->Text(65,$linha,'Efetiva Total');		
				$ob_pdf->Text(151,$linha,':');
				$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TIR']), $linha,trim($AR_DADOS['PERC_TIR']).'%');
				
				if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
				{
					$ob_pdf->Text(175,$linha,'(ao mês)*');				
				}
				
				if ($AR_DADOS['FORMA_CALCULO'] == "P") # PREFIXADO
				{
					$ob_pdf->Text(175,$linha,'(ao mês)');				
				}
		    }    

			if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
			{			
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Text(15,113,'* A taxa sofrerá alterações pela variação do INPC/IBGE mensal.');			
			}

			############################### DEMONSTRATIVO DE CÁLCULO ###############################
			$ob_pdf->Line(10,118,200,118);
			$ob_pdf->Line(10,118,10,192);
			$ob_pdf->Line(10,192,200,192);
			$ob_pdf->Line(200,118,200,192);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(78,123,'DEMONSTRATIVO DE CÁLCULO');
			
			$coluna1 = 13;
			$coluna3 = 96;
			$linha = 136;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text($coluna1,$linha,'Data Solicitação');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_SOLICITACAO']),$linha,$AR_DADOS['DT_SOLICITACAO']);

			$linha = $linha+4;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'Data Depósito');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_DEPOSITO']),$linha,$AR_DADOS['DT_DEPOSITO']);
		     
			$linha = $linha+4;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text($coluna1,$linha,'Data 1ª Prestação');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_PRIMEIRA_PRESTACAO']),$linha,$AR_DADOS['DT_PRIMEIRA_PRESTACAO']);
		    
			$linha = $linha+4;   
			$ob_pdf->Text($coluna1,$linha,'Data Última Prestação');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_ULTIMA_PRESTACAO']),$linha,$AR_DADOS['DT_ULTIMA_PRESTACAO']);
		    
			$linha = $linha+6;   
			$ob_pdf->Text($coluna1,$linha,'Salário Empréstimo');
			$ob_pdf->Text(62,$linha,'R$:');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['SALARIO_EMPRESTIMO']),$linha,$AR_DADOS['SALARIO_EMPRESTIMO']);

			$linha = $linha+19;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'Número de Prestações');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['NRO_PRESTACOES']),$linha,$AR_DADOS['NRO_PRESTACOES']);			

			if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
			{
				$linha = $linha+6; 
				#$b_pdf->Text($coluna1,$linha,'Prestação Sem Variação
				$ob_pdf->Text($coluna1,$linha,'Prestação Projetada');
				$ob_pdf->Text(63,$linha,'R$*:');
				$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PRESTACAO']),$linha,$AR_DADOS['VLR_PRESTACAO']);				
				
				$ob_pdf->SetFont('Courier','',10);
				$linha = $linha+7; 
				$ob_pdf->Text($coluna1,$linha,'(*)  A  prestação será ajustada pela variação do  INPC-IBGE  divulgada no mês anterior');
				$ob_pdf->Text($coluna1,$linha+4,'ao vencimento.');
			}
			
			if ($AR_DADOS['FORMA_CALCULO'] == "P") # PREFIXADO
			{
				$linha = $linha+6; 
				$ob_pdf->Text($coluna1,$linha,'Valor da Prestação');
				$ob_pdf->Text(60,$linha,'R$:');
				$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PRESTACAO']),$linha,$AR_DADOS['VLR_PRESTACAO']);				
			}
			
			/*
			$linha = 178;
			$ob_pdf->Text($coluna1,$linha,'Valor da Prestação');
			$ob_pdf->Text(62,$linha,'R$:');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PRESTACAO']),$linha,$AR_DADOS['VLR_PRESTACAO']);			
			*/


			########################################################
			$ob_pdf->SetFont('Courier','',12);
			$coluna1 = 98;
			$coluna2 = 165;
			$coluna3 = 197;
			$linha = 128;
			$ob_pdf->Text(114,$linha,'Valor Solicitado');
			$ob_pdf->Text($coluna2,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_SOLICITADO']),$linha,$AR_DADOS['VLR_SOLICITADO']);	

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( + )Taxa Administração');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_ADM']),$linha,$AR_DADOS['VLR_ADM']);	

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( + )Taxa de Juros');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_TX_JUROS']),$linha,$AR_DADOS['VLR_TX_JUROS']);	
			
			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( + )Taxa Preserv. Patrim.');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_TX_PRESERVACAO_PATRIMONIA']),$linha,$AR_DADOS['VLR_TX_PRESERVACAO_PATRIMONIA']);	
		                
			$linha += 4;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'( = )Montante Concedido');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['MONTANTE_CONCEDIDO']),$linha,$AR_DADOS['MONTANTE_CONCEDIDO']);		
			
			
			$ob_pdf->SetFont('Courier','',12);
			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Taxa Administração');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_ADM']),$linha,$AR_DADOS['VLR_ADM']);	

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Taxa de Juros');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_TX_JUROS']),$linha,$AR_DADOS['VLR_TX_JUROS']);	
			
			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Taxa Preserv. Patrim.');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_TX_PRESERVACAO_PATRIMONIA']),$linha,$AR_DADOS['VLR_TX_PRESERVACAO_PATRIMONIA']);	
			
			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Saldo Empr. Anterior');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PAGO_EMP_ANTERIOR']),$linha,$AR_DADOS['VLR_PAGO_EMP_ANTERIOR']);		
			
			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Prestações Atrasadas');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['PREST_ATRASADAS']),$linha,$AR_DADOS['PREST_ATRASADAS']);		

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Débitos em Aberto');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_DEBITOS_DESCONTADOS']),$linha,$AR_DADOS['VLR_DEBITOS_DESCONTADOS']);		

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Valor do IOF');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_IOF']),$linha,$AR_DADOS['VLR_IOF']);	

			$linha += 4;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'( = )Valor do Depósito');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_DEPOSITO']),$linha,$AR_DADOS['VLR_DEPOSITO']);				
		}
		
		################## DEMONSTRATIVO ##################
		if(($tp_imp == 3) and ($AR_DADOS['ORIGEM_CONTRATO'] == 'C'))
		{
			
			############################### ASSINATURA ###############################
			$ob_pdf->setXY(18,193);
			$ob_pdf->Image('img/ass_motta_emp.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), ConvertSize(146,$ob_pdf->pgwidth), ConvertSize(86,$ob_pdf->pgwidth),'','',false);
			$ob_pdf->setXY(68,196);
			$ob_pdf->Image('img/ass_tortorelli_emp.jpg', $ob_pdf->GetX() + 10, $ob_pdf->GetY() - 3, ConvertSize(147,$ob_pdf->pgwidth), ConvertSize(86,$ob_pdf->pgwidth),'','',false);
			texto('
			<BR>
			<BR>
			<BR>                                                             
			<BR>Luiz Eduardo Motta 		       Ricardo Costa Tortorelli      Fundação CEEE 
			<BR>CIC 375.831.230-20   	     CIC 509.666.930-00            Representante

			');			
/*
			$ob_pdf->Text(10,224,'____________________________________');
			$ob_pdf->Text(10,229,'Fundação CEEE /');
			$ob_pdf->Text(10,233,'Representante da Fundação CEEE');
*/			

			$ob_pdf->Text(10,235,'Testemunhas: 1.____________________________ 2.____________________________');


			
			############################### IMPORTANTE ###############################
			if(!$fl_preenche)
			{
				#### QUADRO ####		
				$ob_pdf->Line(10,238,200,238);
				$ob_pdf->Line(10,238,10,263);
				$ob_pdf->Line(10,263,200,263);
				$ob_pdf->Line(200,238,200,263);
			}
			preenche($fl_preenche,true);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(12,242,'IMPORTANTE');	
			$ob_pdf->SetXY(12,243);
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->MultiCell(185, 4, 'Este demonstrativo é parte integrante do contrato de prestação de serviço Call Center e de Empréstimo Pessoal firmado pelo Participante/Beneficiário acima identificado e a Fundaçao CEEE, conforme dispõe a cláusula 10 e encontra-se registrado no Serviço de Registro de Títulos e Documentos de Porto Alegre - RS.');			
		}
		else
		{
			if($tp_imp != 3)
			{
				############################### ASSINATURA ###############################
				preenche($fl_preenche,true);
				$ob_pdf->SetFont('Courier','',12);
				$ob_pdf->Text(10,204,'____________________');
				$ob_pdf->Text(65,204,'_________________ , ____ de _____________ de _____.');
				$ob_pdf->SetFont('Courier','B',12);
				$ob_pdf->Text(10,208,'MUTUÁRIO');
			}
			
			############################### IMPORTANTE ###############################
			if(!$fl_preenche)
			{
				#### QUADRO ####		
				$ob_pdf->Line(10,213,200,213);
				$ob_pdf->Line(10,213,10,241);
				$ob_pdf->Line(10,241,200,241);
				$ob_pdf->Line(200,213,200,241);
			}
			preenche($fl_preenche,true);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(12,218,'IMPORTANTE');	
			$ob_pdf->SetXY(12,220);
			$ob_pdf->SetFont('Courier','',12);
			
			if($AR_DADOS['ORIGEM_CONTRATO'] == 'I')
			{
				$ob_pdf->MultiCell(185, 4, 'Este DEMONSTRATIVO é parte integrante e inseparável do CONTRATO DE PRESTAÇÃO DE SERVIÇO CALL CENTER E DE EMPRÉSTIMO PESSOAL, firmado pelo Participante/Beneficiário acima identificado e a Fundação CEEE, conforme disposto nos artigos 10 a 19 do mesmo e de acordo com os critérios expressos neste DEMONSTRATIVO no quadro CONDIÇÕES GERAIS.');
			}
			else
			{			
				$ob_pdf->MultiCell(185, 4, 'Esta proposta está sujeita à aprovação pela Diretoria da FUNDAÇÃO CEEE. Se acolhida, a obrigação do MUTUÁRIO tem início na data da concessão do empréstimo. Uma vez aprovada a presente proposta passa a viger como contrato, regido pelas cláusulas impressas nas páginas seguintes e as condições aqui fixadas.');
			}
			
			if($tp_imp != 3)
			{
				
				############################### OBSERVAÇÕES ###############################
				if(!$fl_preenche)
				{
					#### QUADRO ####
					$ob_pdf->Line(10,245,200,245);
					$ob_pdf->Line(10,245,10,263);
					$ob_pdf->Line(10,263,200,263);
					$ob_pdf->Line(200,245,200,263);
				}
				preenche($fl_preenche,true);
				$ob_pdf->SetFont('Courier','B',12);
				$ob_pdf->Text(12,249,'OBSERVAÇÕES');
				
			}
		}
		
		############################### USUÁRIO / DATA ###############################
		//if((strtoupper(trim($AR_DADOS['USU_CRIACAO'])) != "WEB") and (trim($AR_DADOS['USU_CRIACAO']) != ""))
		if ($AR_DADOS['ORIGEM_CONTRATO'] != 'I')
		{
			
			if(!$fl_preenche)
			{
				#### QUADRO ####
				$ob_pdf->Line(10,266,200,266);
				$ob_pdf->Line(10,266,10,276);
				$ob_pdf->Line(10,276,200,276);
				$ob_pdf->Line(200,266,200,276);
			}
			preenche($fl_preenche,true);
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text(12,270,'Usuário/Atendente:');	
			//if(trim($AR_DADOS['USU_CRIACAO']) != "")
			//{
				$ob_pdf->Text(58,270,$AR_DADOS['USU_CRIACAO']);	
			//}
			//else if(trim($_SESSION['U']) != "")
			//{
			//	$ob_pdf->Text(51,270,strtoupper($_SESSION['U']));	
			//}
			
			$ob_pdf->Text(96,270,'Data/Hora Digitação:');	
			//if(trim($AR_DADOS['USU_CRIACAO']) != "")
			//{
				$ob_pdf->Text(148,270,$AR_DADOS['DT_CRIACAO']);
			//}
			//else if(trim($_SESSION['U']) != "")
			//{
			//	$ob_pdf->Text(156,270,date('d/m/Y H:m:s'));	
			//}	

			if($fl_demonstrativo)
			{
				$ob_pdf->SetFont('Courier','',9);			
				if($AR_DADOS['USUARIO_CONFIRMACAO'] == "P")
				{
					$ob_pdf->Text(12,274,"* Empréstimo confirmado pelo Participante");
				}
				else
				{
					$ob_pdf->Text(12,274,"* Emprétimo confirmado pelo Atendente");
				}
			}
		}
	}
	
	if(($tp_imp == 0)or ($tp_imp == 2)) # 0 -> IMPRIME TUDO
	{	
	############################################## PAGINA 2 #####################################################
	$ob_pdf->AddPage();
	#### NR PAGINA ####
	preenche($fl_preenche,true);
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'2');		
	
	$ob_pdf->SetFont('Arial','B',14);
	$ob_pdf->Text(10,12,'CONTRATO DE MÚTUO DE EMPRÉSTIMO');

	$ob_pdf->SetFont('Arial','B',12);
	$ob_pdf->Text(10,18,'Especificações Preliminares');	

	$ob_pdf->SetXY(10,20);
	$ob_pdf->SetStyle("p","arial","N",12,"0,0,0",-1);
	$ob_pdf->SetStyle("b","arial","B",12,"0,0,0",-1);
	$ob_pdf->SetStyle("B","arial","B",12,"0,0,0",-1);
	$ob_pdf->WriteTag(190,6,'<t>As partes contratantes reconhecem que o presente empréstimo é feito com recursos garantidores das reservas técnicas da <B>FUNDAÇÃO CEEE</B> constituídas de acordo com os critérios fixados pelo Conselho de Gestão da Previdência Complementar, bem como com aqueles outros recursos, de qualquer origem ou natureza, correspondentes às demais reservas, fundos e provisões e, por isso, nos termos da legislação previdenciária vigente, devem ser aplicados, de modo que sejam atendidas as exigências atuariais dos Planos Previdenciais dela, <B>FUNDAÇÃO CEEE</B>, e lhe sejam conferidas segurança, rentabilidade, solvência e liquidez.</t>',0,"J");
	
texto('
<BR>
Pelo presente instrumento particular e na melhor forma de direito, têm justos e contratados entre si, 
como credora a FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE, com sede em Porto Alegre, capital do Estado do Rio 
Grande do Sul na Rua dos Andradas, 702, 11º andar, inscrita no Cadastro Nacional de Pessoas Jurídicas ­ CNPJ sob 
nº 90.884.412/0001-24, neste instrumento designada <B>FUNDAÇÃO CEEE</B>, e como devedor o Participante identificado 
na página 01 deste, daqui em diante designado <B>MUTUÁRIO</B>, um empréstimo que se regerá pelas cláusulas e condições 
seguintes:
<BR>
<BR>
<B>Cláusula Primeira</B>
<BR>
A <B>FUNDAÇÃO CEEE</B> concede ao <B>MUTUÁRIO</B> um empréstimo em moeda corrente nacional, no valor e condições 
consignadas nesta Proposta, na página 01 deste instrumento.
<BR>
<BR>
<T><B>Parágrafo Único</B>
<BR>
<T>O <B>MUTUÁRIO</B> entrega neste ato à <B>FUNDAÇÃO CEEE</B>, uma Nota Promissória a título de garantia contratual fidejussória, do presente empréstimo.
<BR>
<BR>
<B>Cláusula Segunda</B>
<BR>
A importância do empréstimo e seus encargos será paga pelo <B>MUTUÁRIO</B> no vencimento, conforme segue:
<BR>
<BR>
<T>2.1 - Modalidade NORMAL
<BR>
<T>Em prestações mensais e consecutivas nas datas em que ele, <B>MUTUÁRIO,</B> receber o crédito de sua folha de pagamento e/ou complementações de benefícios.
<BR>
<BR>
<T>2.2 - Modalidade REFINANCIAMENTO
<BR>
<T>Em prestações mensais e consecutivas nas datas em que ele, <B>MUTUÁRIO</B>, receber o crédito de sua folha de pagamento e/ou complementações de benefícios, porém, esta modalidade não poderá gerar valor líquido a depositar, ou seja, o valor líquido de depósito deve ser "ZERO", somente alterando-se o valor das prestações ou a quantidade de prestações do empréstimo anteriormente concedido. Nesta modalidade não poderá haver reforma sem que tenham sido quitadas todas as prestações contratadas.
<BR>
<BR>
<T>2.3 - Modalidade FÉRIAS
<BR>
<T>Em prestação única, na data em que ele <B>MUTUÁRIO</B>, receber o crédito em folha de pagamento, 
dos valores relativos a suas férias. 
<BR>
<BR>
<B>Cláusula Terceira</B>
<BR>
Para a perfeita execução do ajustado na cláusula SEGUNDA, o <B>MUTUÁRIO</B> autoriza, nesta ato, em 
');
	}
	if(($tp_imp == 0) or ($tp_imp == 1)) # 0 -> IMPRIME TUDO, 2 -> 3 E 5
	{		
	############################################## PAGINA 3 #####################################################
	$ob_pdf->AddPage();
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'3');		
	$ob_pdf->WriteTag(190,6,"<B></B>
caráter irrevogável e irretratável e enquanto vigorarem as obrigações decorrentes deste contrato, que a <B>FUNDAÇÃO CEEE</B>, 
a seu critério, proceda na cobrança através do desconto em sua folha de pagamento de salários ou débito em conta 
corrente bancária do <B>MUTUÁRIO</B>, o valor contratado, o qual será pago nos termos do avençado na página 01 deste 
instrumento, em datas certas, no valor ajustado, em prestações mensais e consecutivas(modalidade NORMAL e 
REFINANCIAMENTO) ou em uma única prestação(modalidade FÉRIAS ), calculadas pela aplicação da tabela PRICE, 
tomando sempre por base o saldo devedor remanescente.",0,"J");	
texto('
<BR>
<BR>
<T><B>Parágrafo Primeiro</B>
<BR>
<T>Para os empréstimos na modalidade NORMAL E REFINANCIAMENTO, a primeira prestação será devida no mês 
subsequente ao da concessão. Na modalidade REFINANCIAMENTO, não poderá haver reforma, sem que tenha sido
 quitada a última prestação. 
<BR>
<BR>
<T><B>Parágrafo Segundo</B>
<BR>
<T>Para os empréstimos na modalidade FÉRIAS, a prestação única será devida no mês do crédito em folha de
 pagamento dos valores relativos a suas férias. E em caso de não desconto, no mês de vencimento, por qualquer 
 motivo, o saldo devedor será, automaticamente atualizado , até sua completa liquidação, no que o <B>MUTUÁRIO</B> 
 reconhece e autoriza a <B>FUNDAÇÃO CEEE</B> a executar o presente procedimento.
<BR>
<BR>
<B>Cláusula Quarta</B>
<BR>
O <B>MUTUÁRIO</B>, não obstante o ajustado nas cláusulas segunda e terceira, é responsável pela verificação e
 regularização dos descontos em sua folha de pagamento da quitação do empréstimo ora contratado, <B>obrigando-se desde 
 já, na hipótese de não serem efetuados esses descontos, por qualquer motivo ou em qualquer tempo, a 
 recolher aos cofres da FUNDAÇÃO CEEE, por sua inteira iniciativa, até o dia 10 mês seguinte ao do correspondente 
 vencimento</B>, sob pena de poder tornar-se imediata e antecipadamente, vencido o prazo deste contrato e, ao mesmo 
 tempo, devido e exigível o saldo em dívida, independentemente de qualquer aviso, notificação ou interpelação 
 judicial ou extrajudicial. Em não havendo a regularização do débito, após esgotadas todas as possibilidades de 
 negociação, o presente contrato será encaminhado para a cobrança judicial, ficando acrescidos ao montante do débito
 devidamente atualizado, honorários de advogado a base de até 20%(vinte por cento) sobre o valor do débito, 
 devidamente corrigido, sem prejuízo dos encargos de mora devidos, correspondentes a atualização monetária pela 
 variação do INPC-IBGE, acrescido de juros de mora de 1,0% (um por cento)ao mês e multa, sobre o saldo devedor 
 atualizado, na ordem de 2,0% (dois por cento). 
<BR>
<BR>
<B>Cláusula Quinta</B>
<BR>
Sobre o valor emprestado incidirão os seguintes encargos financeiros: 
<BR>
<BR>
<T>5.1 Taxa de Juros
<BR>
<T>O Saldo Devedor do Empréstimo será reajustado mensalmente, pró-rata no mês de concessão e plena nos demais meses 
de vigência deste contrato, conforme taxa anual de juros declarada no quadro CONDIÇÕES GERAIS;
<BR>
<BR>
<T>5.2 - Taxa de Preservação Patrimonial 
<BR>
<T>O Saldo Devedor do Empréstimo será reajustado mensalmente, pró-rata no mês de concessão
');
	
	}

	if(($tp_imp == 0) or ($tp_imp == 2))
	{		
	############################################## PAGINA 4 #####################################################
	$ob_pdf->AddPage();
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'4');		
	$ob_pdf->WriteTag(190,6,"<B></B> e plena nos demais meses
de vigência deste contrato, conforme taxa de preservação patrimonial declarada no quadro CONDIÇÕES GERAIS. Tal taxa 
poderá ser pré-fixada ou pós-fixada, dependendo dos critérios de concessão definidos pela <B>FUNDAÇÃO CEEE</B>, na data da 
concessão do presente contrato. O indexador deverá estar declarado explicitamente na quadro CONDIÇÕES GERAIS. Em caso 
de extinção do indexador, o mesmo será substituído por outro que venha a ser divulgado pelo Governo Federal para o 
mesmo fim;",0,"J",0,"10,0,0,0");
		
texto('
<BR>
<BR>
<T>5.3 - Taxa de Administração 
<BR>
<T>Ao valor solicitado será acrescido, no momento da concessão, taxa de Administração declarada no item 5 do quadro 
CONDIÇÕES GERAIS, tal valor será incorporado ao saldo devedor.
<BR>
<BR>
<T>5.4 - Taxa Mensal de Risco
<BR>
<T>Será cobrado, juntamente com a parcela mensal do empréstimo a título de contribuição ao Fundo de Oscilação de 
Risco de Inadimplência, para cobertura do saldo devedor em caso de falecimento do <B>MUTUÁRIO</B>, conforme fatores 
vigentes na data da concessão e constante no quadro CONDIÇÕES GERAIS naquela data.
<BR>
<BR>
<B>Cláusula Sexta</B>
<BR>
Ocorrendo o vencimento antecipado da dívida, por cancelamento da inscrição do <B>MUTUÁRIO</B> como Participante ou após 
a rescisão do vínculo contratual que mantém com a Patrocinadora da <B>FUNDAÇÃO CEEE</B>, fica o <B>MUTUÁRIO</B> e a 
<B>FUNDAÇÃO CEEE</B> AUTORIZADA a compensar o saldo da dívida com o valor da restituição de contribuições de que trata o 
Regulamento do respectivo plano de benefícios ao qual o MUTUÁRIA faz parte, até aquele limite. 
<BR>
<BR>
<T><B>Parágrafo Segundo</B>
<BR>
<T>Ocorrendo quaisquer das hipóteses previstas nesta cláusula, o <B>MUTUÁRIO</B>, desde já, por esta e na melhor forma de 
direito, nomeia e constitui em caráter irrevogável e irretratável, enquanto perdurarem suas obrigações contratuais, 
sua bastante procuradora a <B>FUNDAÇÃO CEEE</B>, com a finalidade de receber, junto aos empregadores dele, <B>MUTUÁRIO</B>, 
quaisquer importâncias que lhe forem devidas, até o montante do saldo da dívida e imputá-la no respectivo pagamento, 
podendo para tal fim, passar recibo, dar quitação e assinar tudo o mais que necessário for, sem que este mandato 
implique em prejuízo de qualquer das obrigações assumidas pelo <B>MUTUÁRIO</B> neste Contrato.
<BR>
<BR>

<B>Cláusula Sétima</B>
<BR>Sobre o valor da prestação paga em atraso, fica o <B>MUTUÁRIO</B> sujeito ao pagamento de juros de mora e 
multa correspondentes a atualização monetária pela variação do INPC-IBGE, acrescido de juros de mora de 1,0% 
(um por cento)ao mês e multa, sobre o saldo devedor atualizado, na ordem de 2,0% (dois por cento).
<BR>
<BR>
<B>Cláusula Oitava</B>
<BR>Ocorrendo a inadimplência do <B>MUTUÁRIO</B>, à <B>FUNDAÇÃO CEEE</B> reserva-se o direito de inscrever o 
inadimplente no <B>Cadastro de Proteção ao Crédito do SERASA</B>, conforme contratofirmado entre as entidades.
');
	}
	
	
	if(($tp_imp == 0) or ($tp_imp == 1))
	{		
	############################################## PAGINA 3 #####################################################	
	$ob_pdf->AddPage();
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'5');		
	preenche($fl_preenche,true);

texto('
<BR>
<BR>
<B>Cláusula Nona</B>
<BR>A tolerância da <B>FUNDAÇÃO CEEE</B> por eventuais atrasos nos pagamentos, não importará em alteração 
ou novação do presente Contrato, nem consistirá precedente argüível pelo <B>MUTUÁRIO</B>.
<BR>
<BR>
<B>Cláusula Décima</B>
<BR>Para quaisquer ações decorrentes deste Contrato, o <B>MUTUÁRIO</B> renuncia ao foro a que tem direito e 
elege de comum acordo com a <B>FUNDAÇÃO CEEE</B>, o foro de Porto Alegre, Capital do Estado do Rio Grande do Sul. 
E, por estarem juntos e contratados, firmam o presente instrumento na presença das testemunhas abaixo.
<BR>
<BR>
<B>ATENÇÃO</B>
<BR>É de responsabilidade do <B>MUTUÁRIO</B> pagar a prestação até o dia 10 do mês subsequente, via depósito 
em conta corrente da <B>FUNDAÇÃO CEEE</B>, quando não ocorrer o débito bancário ou desconto em folha.
<BR>
<BR>
<BR>
<BR>________________________________ , ____ de ________________ de _______. 
<BR>
<BR>
<BR>
<BR>
<BR>____________________________                    ____________________________
<BR>MUTUÁRIO                                                          Representante FCEEE/Red
<BR>  
<BR>
<BR><B>Pela FUNDAÇÃO CEEE</B>
<BR>
');
	############################### ASSINATURA ###############################
	if ($fl_assinatura) 
	{
		$ob_pdf->setX(20);
		$ob_pdf->Image('img/ass_motta_emp.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), ConvertSize(146,$ob_pdf->pgwidth), ConvertSize(86,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->setX(115);
		$ob_pdf->Image('img/ass_tortorelli_emp.jpg', $ob_pdf->GetX() - 15, $ob_pdf->GetY() - 3, ConvertSize(147,$ob_pdf->pgwidth), ConvertSize(86,$ob_pdf->pgwidth),'','',false);
	}
texto('
<BR>
<BR>
<BR>
<BR>Luiz Eduardo Motta 				                                          Ricardo Costa Tortorelli 
<BR>CIC 375.831.230-20   			                                       CIC 509.666.930-00 
<BR>
<BR>
<BR>
<BR>
<BR>
<BR>
<BR>____________________________                   ____________________________
<BR>TESTEMUNHA CIC:                                          TESTEMUNHA CIC:
');

	}
	
	if($tp_imp == 0)
	{
		#### NR PAGINA ####
		$ob_pdf->AddPage();
	}
	
?>
