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
			$ob_pdf->Text(10,10,'PROPOSTA DE EMPR�STIMO'); 
		}
		else
		{
			$ob_pdf->Text(10,10,'DEMONSTRATIVO DE EMPR�STIMO'); 
		}
		*/
		
		if ($fl_demonstrativo)
		{
			$ob_pdf->Text(10,10,'DEMONSTRATIVO DE EMPR�STIMO'); 
		}
		else
		{
			$ob_pdf->Text(10,10,'PROPOSTA DE EMPR�STIMO'); 
		}		
		
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->Text(140,10,'Contrato n�:');
		preenche($fl_preenche,false);
		$ob_pdf->Text(175,10,$_REQUEST['cd_contrato']);
		
		preenche($fl_preenche,true);
		$ob_pdf->SetFont('Arial','B',12);
		$ob_pdf->Text(10,14,'Parte Integrante do Contrato de M�tuo de Empr�stimo');
		
		############################### DADOS CADASTRAIS DE IDENTIFICA��O ###############################
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
		$ob_pdf->Text(66,22,'DADOS CADASTRAIS DE IDENTIFICA��O');
		
		$ob_pdf->SetFont('Courier','',12);
		$ob_pdf->Text(12,28,'Nome Completo:');
		if ($fl_cadastro) $ob_pdf->Text(48,28,$AR_DADOS['NOME']);
		
		$ob_pdf->Text(155,28,'CPF:');
		if ($fl_cadastro) $ob_pdf->Text(167,28,$AR_DADOS['CPF_MF']);
		$ob_pdf->Text(12,32,'Empresa:');
		if ($fl_cadastro) $ob_pdf->Text(34,32,$AR_DADOS['CD_EMPRESA']);
		$ob_pdf->Text(44,32,'Re.d:');
		if ($fl_cadastro) $ob_pdf->Text(60,32,$AR_DADOS['CD_REGISTRO_EMPREGADO']."/".$AR_DADOS['SEQ_DEPENDENCIA']);
		
		$ob_pdf->Text(12,36,'Endere�o:');
		if ($fl_cadastro) $ob_pdf->Text(36,36,$AR_DADOS['LOGRADOURO']." ".$AR_DADOS['BAIRRO']);

		$ob_pdf->Text(12,40,'Cidade:');
		if ($fl_cadastro) $ob_pdf->Text(32,40,$AR_DADOS['CIDADE']." - ".$AR_DADOS['UNIDADE_FEDERATIVA']);
		
		$ob_pdf->Text(12,44,'Fone para Contato:');
		if ($fl_cadastro) $ob_pdf->Text(60,44,$AR_DADOS['DDD']." ".$AR_DADOS['TELEFONE']);
		$ob_pdf->Text(100,44,'Ramal:');
		if ($fl_cadastro) $ob_pdf->Text(115,44,$AR_DADOS['RAMAL']);

		$ob_pdf->SetFont('Courier','BU',12);
		$ob_pdf->Text(12,50,'DADOS BANC�RIOS');
		
		$ob_pdf->SetFont('Courier','',12);
		$ob_pdf->Text(12,54,'Banco:');
		if ($fl_cadastro) $ob_pdf->Text(28,54,$AR_DADOS['CD_INSTITUICAO']);	
		$ob_pdf->Text(50,54,'Ag�ncia:');
		if ($fl_cadastro) $ob_pdf->Text(72,54,$AR_DADOS['CD_AGENCIA']);	
		$ob_pdf->Text(95,54,'Conta Corrente:');
		if ($fl_cadastro) $ob_pdf->Text(134,54,$AR_DADOS['CONTA']);	
		
		############################### CONDI��ES GERAIS ###############################
		if ($fl_financeiro) 
		{
			preenche($fl_preenche,false);
			$ob_pdf->Line(10,66,200,66);
			$ob_pdf->Line(10,66,10,114);
			$ob_pdf->Line(10,114,200,114);
			$ob_pdf->Line(200,66,200,114);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(85,70,'CONDI��ES GERAIS');

			$linha = 76;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text(15,$linha ,'Taxa de Preserva��o Patrimonial:');
			$ob_pdf->Text(100,$linha ,'[ '.($AR_DADOS['FORMA_CALCULO'] == 'P' ? 'X' : ' ').' ] PR�-FIXADA (Tabela PRICE)');
			
			$linha += 4;
			$ob_pdf->SetFont('Courier','BU',10);
			$ob_pdf->Text(15,$linha,'Modalidade:');	
			
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text(100,$linha ,'[ '.($AR_DADOS['FORMA_CALCULO'] == 'O' ? 'X' : ' ').' ] P�S-FIXADA (Tabela SAC)');
			
			$ob_pdf->SetFont('Courier','BU',10);
			$linha += 4;
			$ob_pdf->Text(65,$linha,'Taxas:');	
			
			$ob_pdf->SetFont('Courier','',12);
			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['TIPO'] != 'F' ? 'X' : ' ').' ] NORMAL');
			$ob_pdf->Text(65,$linha,'Juros');	
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_JUROS_MES']) , $linha,$AR_DADOS['PERC_TX_JUROS_MES'].'%');
			$ob_pdf->Text(175,$linha,'(ao m�s)');
			
			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['TIPO'] == 'F' ? 'X' : ' ').' ] F�RIAS');	
			$ob_pdf->Text(65,$linha,'Preserva��o Patrimonial');	
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
				$ob_pdf->Text(175,$linha,'(ao m�s)');
			}			

			$linha += 4;
			$ob_pdf->Text(15,$linha,'[ '.($AR_DADOS['MOD_RENEGOCIACAO'] == 'Sim' ? 'X' : ' ').' ] RENEGOCIA��O');
			$ob_pdf->Text(65,$linha,'Efetiva de Juros');		
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_EFETIVA']), $linha,trim($AR_DADOS['PERC_TX_EFETIVA']).'%');
			$ob_pdf->Text(175,$linha,'(ao m�s)');				
			
			             
			$linha += 4;
			$ob_pdf->Text(65,$linha,'Administrativa s/Valor Solicitado');		
			$ob_pdf->Text(151,$linha,':');
			$ob_pdf->Text(172 - $ob_pdf->GetStringWidth($AR_DADOS['PERC_TX_ADM']), $linha,$AR_DADOS['PERC_TX_ADM'].'%');		
			
			$linha += 4;			
			$ob_pdf->Text(65,$linha,'De Risco s/Presta��o Mensal');		
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
					$ob_pdf->Text(175,$linha,'(ao m�s)*');				
				}
				
				if ($AR_DADOS['FORMA_CALCULO'] == "P") # PREFIXADO
				{
					$ob_pdf->Text(175,$linha,'(ao m�s)');				
				}
		    }    

			if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
			{			
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Text(15,113,'* A taxa sofrer� altera��es pela varia��o do INPC/IBGE mensal.');			
			}

			############################### DEMONSTRATIVO DE C�LCULO ###############################
			$ob_pdf->Line(10,118,200,118);
			$ob_pdf->Line(10,118,10,192);
			$ob_pdf->Line(10,192,200,192);
			$ob_pdf->Line(200,118,200,192);
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text(78,123,'DEMONSTRATIVO DE C�LCULO');
			
			$coluna1 = 13;
			$coluna3 = 96;
			$linha = 136;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text($coluna1,$linha,'Data Solicita��o');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_SOLICITACAO']),$linha,$AR_DADOS['DT_SOLICITACAO']);

			$linha = $linha+4;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'Data Dep�sito');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_DEPOSITO']),$linha,$AR_DADOS['DT_DEPOSITO']);
		     
			$linha = $linha+4;
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->Text($coluna1,$linha,'Data 1� Presta��o');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_PRIMEIRA_PRESTACAO']),$linha,$AR_DADOS['DT_PRIMEIRA_PRESTACAO']);
		    
			$linha = $linha+4;   
			$ob_pdf->Text($coluna1,$linha,'Data �ltima Presta��o');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['DT_ULTIMA_PRESTACAO']),$linha,$AR_DADOS['DT_ULTIMA_PRESTACAO']);
		    
			$linha = $linha+6;   
			$ob_pdf->Text($coluna1,$linha,'Sal�rio Empr�stimo');
			$ob_pdf->Text(62,$linha,'R$:');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['SALARIO_EMPRESTIMO']),$linha,$AR_DADOS['SALARIO_EMPRESTIMO']);

			$linha = $linha+19;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'N�mero de Presta��es');
			$ob_pdf->Text(67,$linha,':');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['NRO_PRESTACOES']),$linha,$AR_DADOS['NRO_PRESTACOES']);			

			if ($AR_DADOS['FORMA_CALCULO'] == "O") # POS-FIXADO
			{
				$linha = $linha+6; 
				#$b_pdf->Text($coluna1,$linha,'Presta��o Sem Varia��o
				$ob_pdf->Text($coluna1,$linha,'Presta��o Projetada');
				$ob_pdf->Text(63,$linha,'R$*:');
				$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PRESTACAO']),$linha,$AR_DADOS['VLR_PRESTACAO']);				
				
				$ob_pdf->SetFont('Courier','',10);
				$linha = $linha+7; 
				$ob_pdf->Text($coluna1,$linha,'(*)  A  presta��o ser� ajustada pela varia��o do  INPC-IBGE  divulgada no m�s anterior');
				$ob_pdf->Text($coluna1,$linha+4,'ao vencimento.');
			}
			
			if ($AR_DADOS['FORMA_CALCULO'] == "P") # PREFIXADO
			{
				$linha = $linha+6; 
				$ob_pdf->Text($coluna1,$linha,'Valor da Presta��o');
				$ob_pdf->Text(60,$linha,'R$:');
				$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_PRESTACAO']),$linha,$AR_DADOS['VLR_PRESTACAO']);				
			}
			
			/*
			$linha = 178;
			$ob_pdf->Text($coluna1,$linha,'Valor da Presta��o');
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
			$ob_pdf->Text($coluna1,$linha,'( + )Taxa Administra��o');
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
			$ob_pdf->Text($coluna1,$linha,'( - )Taxa Administra��o');
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
			$ob_pdf->Text($coluna1,$linha,'( - )Presta��es Atrasadas');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['PREST_ATRASADAS']),$linha,$AR_DADOS['PREST_ATRASADAS']);		

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )D�bitos em Aberto');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_DEBITOS_DESCONTADOS']),$linha,$AR_DADOS['VLR_DEBITOS_DESCONTADOS']);		

			$linha += 4;
			$ob_pdf->Text($coluna1,$linha,'( - )Valor do IOF');
			$ob_pdf->Text($coluna2 ,$linha,'R$');
			$ob_pdf->Text($coluna3 - $ob_pdf->GetStringWidth($AR_DADOS['VLR_IOF']),$linha,$AR_DADOS['VLR_IOF']);	

			$linha += 4;
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->Text($coluna1,$linha,'( = )Valor do Dep�sito');
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
			<BR>Luiz Eduardo Motta 		       Ricardo Costa Tortorelli      Funda��o CEEE 
			<BR>CIC 375.831.230-20   	     CIC 509.666.930-00            Representante

			');			
/*
			$ob_pdf->Text(10,224,'____________________________________');
			$ob_pdf->Text(10,229,'Funda��o CEEE /');
			$ob_pdf->Text(10,233,'Representante da Funda��o CEEE');
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
			$ob_pdf->MultiCell(185, 4, 'Este demonstrativo � parte integrante do contrato de presta��o de servi�o Call Center e de Empr�stimo Pessoal firmado pelo Participante/Benefici�rio acima identificado e a Funda�ao CEEE, conforme disp�e a cl�usula 10 e encontra-se registrado no Servi�o de Registro de T�tulos e Documentos de Porto Alegre - RS.');			
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
				$ob_pdf->Text(10,208,'MUTU�RIO');
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
				$ob_pdf->MultiCell(185, 4, 'Este DEMONSTRATIVO � parte integrante e insepar�vel do CONTRATO DE PRESTA��O DE SERVI�O CALL CENTER E DE EMPR�STIMO PESSOAL, firmado pelo Participante/Benefici�rio acima identificado e a Funda��o CEEE, conforme disposto nos artigos 10 a 19 do mesmo e de acordo com os crit�rios expressos neste DEMONSTRATIVO no quadro CONDI��ES GERAIS.');
			}
			else
			{			
				$ob_pdf->MultiCell(185, 4, 'Esta proposta est� sujeita � aprova��o pela Diretoria da FUNDA��O CEEE. Se acolhida, a obriga��o do MUTU�RIO tem in�cio na data da concess�o do empr�stimo. Uma vez aprovada a presente proposta passa a viger como contrato, regido pelas cl�usulas impressas nas p�ginas seguintes e as condi��es aqui fixadas.');
			}
			
			if($tp_imp != 3)
			{
				
				############################### OBSERVA��ES ###############################
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
				$ob_pdf->Text(12,249,'OBSERVA��ES');
				
			}
		}
		
		############################### USU�RIO / DATA ###############################
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
			$ob_pdf->Text(12,270,'Usu�rio/Atendente:');	
			//if(trim($AR_DADOS['USU_CRIACAO']) != "")
			//{
				$ob_pdf->Text(58,270,$AR_DADOS['USU_CRIACAO']);	
			//}
			//else if(trim($_SESSION['U']) != "")
			//{
			//	$ob_pdf->Text(51,270,strtoupper($_SESSION['U']));	
			//}
			
			$ob_pdf->Text(96,270,'Data/Hora Digita��o:');	
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
					$ob_pdf->Text(12,274,"* Empr�stimo confirmado pelo Participante");
				}
				else
				{
					$ob_pdf->Text(12,274,"* Empr�timo confirmado pelo Atendente");
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
	$ob_pdf->Text(10,12,'CONTRATO DE M�TUO DE EMPR�STIMO');

	$ob_pdf->SetFont('Arial','B',12);
	$ob_pdf->Text(10,18,'Especifica��es Preliminares');	

	$ob_pdf->SetXY(10,20);
	$ob_pdf->SetStyle("p","arial","N",12,"0,0,0",-1);
	$ob_pdf->SetStyle("b","arial","B",12,"0,0,0",-1);
	$ob_pdf->SetStyle("B","arial","B",12,"0,0,0",-1);
	$ob_pdf->WriteTag(190,6,'<t>As partes contratantes reconhecem que o presente empr�stimo � feito com recursos garantidores das reservas t�cnicas da <B>FUNDA��O CEEE</B> constitu�das de acordo com os crit�rios fixados pelo Conselho de Gest�o da Previd�ncia Complementar, bem como com aqueles outros recursos, de qualquer origem ou natureza, correspondentes �s demais reservas, fundos e provis�es e, por isso, nos termos da legisla��o previdenci�ria vigente, devem ser aplicados, de modo que sejam atendidas as exig�ncias atuariais dos Planos Previdenciais dela, <B>FUNDA��O CEEE</B>, e lhe sejam conferidas seguran�a, rentabilidade, solv�ncia e liquidez.</t>',0,"J");
	
texto('
<BR>
Pelo presente instrumento particular e na melhor forma de direito, t�m justos e contratados entre si, 
como credora a FUNDA��O CEEE DE SEGURIDADE SOCIAL - ELETROCEEE, com sede em Porto Alegre, capital do Estado do Rio 
Grande do Sul na Rua dos Andradas, 702, 11� andar, inscrita no Cadastro Nacional de Pessoas Jur�dicas � CNPJ sob 
n� 90.884.412/0001-24, neste instrumento designada <B>FUNDA��O CEEE</B>, e como devedor o Participante identificado 
na p�gina 01 deste, daqui em diante designado <B>MUTU�RIO</B>, um empr�stimo que se reger� pelas cl�usulas e condi��es 
seguintes:
<BR>
<BR>
<B>Cl�usula Primeira</B>
<BR>
A <B>FUNDA��O CEEE</B> concede ao <B>MUTU�RIO</B> um empr�stimo em moeda corrente nacional, no valor e condi��es 
consignadas nesta Proposta, na p�gina 01 deste instrumento.
<BR>
<BR>
<T><B>Par�grafo �nico</B>
<BR>
<T>O <B>MUTU�RIO</B> entrega neste ato � <B>FUNDA��O CEEE</B>, uma Nota Promiss�ria a t�tulo de garantia contratual fidejuss�ria, do presente empr�stimo.
<BR>
<BR>
<B>Cl�usula Segunda</B>
<BR>
A import�ncia do empr�stimo e seus encargos ser� paga pelo <B>MUTU�RIO</B> no vencimento, conforme segue:
<BR>
<BR>
<T>2.1 - Modalidade NORMAL
<BR>
<T>Em presta��es mensais e consecutivas nas datas em que ele, <B>MUTU�RIO,</B> receber o cr�dito de sua folha de pagamento e/ou complementa��es de benef�cios.
<BR>
<BR>
<T>2.2 - Modalidade REFINANCIAMENTO
<BR>
<T>Em presta��es mensais e consecutivas nas datas em que ele, <B>MUTU�RIO</B>, receber o cr�dito de sua folha de pagamento e/ou complementa��es de benef�cios, por�m, esta modalidade n�o poder� gerar valor l�quido a depositar, ou seja, o valor l�quido de dep�sito deve ser "ZERO", somente alterando-se o valor das presta��es ou a quantidade de presta��es do empr�stimo anteriormente concedido. Nesta modalidade n�o poder� haver reforma sem que tenham sido quitadas todas as presta��es contratadas.
<BR>
<BR>
<T>2.3 - Modalidade F�RIAS
<BR>
<T>Em presta��o �nica, na data em que ele <B>MUTU�RIO</B>, receber o cr�dito em folha de pagamento, 
dos valores relativos a suas f�rias. 
<BR>
<BR>
<B>Cl�usula Terceira</B>
<BR>
Para a perfeita execu��o do ajustado na cl�usula SEGUNDA, o <B>MUTU�RIO</B> autoriza, nesta ato, em 
');
	}
	if(($tp_imp == 0) or ($tp_imp == 1)) # 0 -> IMPRIME TUDO, 2 -> 3 E 5
	{		
	############################################## PAGINA 3 #####################################################
	$ob_pdf->AddPage();
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'3');		
	$ob_pdf->WriteTag(190,6,"<B></B>
car�ter irrevog�vel e irretrat�vel e enquanto vigorarem as obriga��es decorrentes deste contrato, que a <B>FUNDA��O CEEE</B>, 
a seu crit�rio, proceda na cobran�a atrav�s do desconto em sua folha de pagamento de sal�rios ou d�bito em conta 
corrente banc�ria do <B>MUTU�RIO</B>, o valor contratado, o qual ser� pago nos termos do aven�ado na p�gina 01 deste 
instrumento, em datas certas, no valor ajustado, em presta��es mensais e consecutivas(modalidade NORMAL e 
REFINANCIAMENTO) ou em uma �nica presta��o(modalidade F�RIAS ), calculadas pela aplica��o da tabela PRICE, 
tomando sempre por base o saldo devedor remanescente.",0,"J");	
texto('
<BR>
<BR>
<T><B>Par�grafo Primeiro</B>
<BR>
<T>Para os empr�stimos na modalidade NORMAL E REFINANCIAMENTO, a primeira presta��o ser� devida no m�s 
subsequente ao da concess�o. Na modalidade REFINANCIAMENTO, n�o poder� haver reforma, sem que tenha sido
 quitada a �ltima presta��o. 
<BR>
<BR>
<T><B>Par�grafo Segundo</B>
<BR>
<T>Para os empr�stimos na modalidade F�RIAS, a presta��o �nica ser� devida no m�s do cr�dito em folha de
 pagamento dos valores relativos a suas f�rias. E em caso de n�o desconto, no m�s de vencimento, por qualquer 
 motivo, o saldo devedor ser�, automaticamente atualizado , at� sua completa liquida��o, no que o <B>MUTU�RIO</B> 
 reconhece e autoriza a <B>FUNDA��O CEEE</B> a executar o presente procedimento.
<BR>
<BR>
<B>Cl�usula Quarta</B>
<BR>
O <B>MUTU�RIO</B>, n�o obstante o ajustado nas cl�usulas segunda e terceira, � respons�vel pela verifica��o e
 regulariza��o dos descontos em sua folha de pagamento da quita��o do empr�stimo ora contratado, <B>obrigando-se desde 
 j�, na hip�tese de n�o serem efetuados esses descontos, por qualquer motivo ou em qualquer tempo, a 
 recolher aos cofres da FUNDA��O CEEE, por sua inteira iniciativa, at� o dia 10 m�s seguinte ao do correspondente 
 vencimento</B>, sob pena de poder tornar-se imediata e antecipadamente, vencido o prazo deste contrato e, ao mesmo 
 tempo, devido e exig�vel o saldo em d�vida, independentemente de qualquer aviso, notifica��o ou interpela��o 
 judicial ou extrajudicial. Em n�o havendo a regulariza��o do d�bito, ap�s esgotadas todas as possibilidades de 
 negocia��o, o presente contrato ser� encaminhado para a cobran�a judicial, ficando acrescidos ao montante do d�bito
 devidamente atualizado, honor�rios de advogado a base de at� 20%(vinte por cento) sobre o valor do d�bito, 
 devidamente corrigido, sem preju�zo dos encargos de mora devidos, correspondentes a atualiza��o monet�ria pela 
 varia��o do INPC-IBGE, acrescido de juros de mora de 1,0% (um por cento)ao m�s e multa, sobre o saldo devedor 
 atualizado, na ordem de 2,0% (dois por cento). 
<BR>
<BR>
<B>Cl�usula Quinta</B>
<BR>
Sobre o valor emprestado incidir�o os seguintes encargos financeiros: 
<BR>
<BR>
<T>5.1 Taxa de Juros
<BR>
<T>O Saldo Devedor do Empr�stimo ser� reajustado mensalmente, pr�-rata no m�s de concess�o e plena nos demais meses 
de vig�ncia deste contrato, conforme taxa anual de juros declarada no quadro CONDI��ES GERAIS;
<BR>
<BR>
<T>5.2 - Taxa de Preserva��o Patrimonial 
<BR>
<T>O Saldo Devedor do Empr�stimo ser� reajustado mensalmente, pr�-rata no m�s de concess�o
');
	
	}

	if(($tp_imp == 0) or ($tp_imp == 2))
	{		
	############################################## PAGINA 4 #####################################################
	$ob_pdf->AddPage();
	$ob_pdf->SetFont('Arial','I',8);
	$ob_pdf->Text(200,288,'4');		
	$ob_pdf->WriteTag(190,6,"<B></B> e plena nos demais meses
de vig�ncia deste contrato, conforme taxa de preserva��o patrimonial declarada no quadro CONDI��ES GERAIS. Tal taxa 
poder� ser pr�-fixada ou p�s-fixada, dependendo dos crit�rios de concess�o definidos pela <B>FUNDA��O CEEE</B>, na data da 
concess�o do presente contrato. O indexador dever� estar declarado explicitamente na quadro CONDI��ES GERAIS. Em caso 
de extin��o do indexador, o mesmo ser� substitu�do por outro que venha a ser divulgado pelo Governo Federal para o 
mesmo fim;",0,"J",0,"10,0,0,0");
		
texto('
<BR>
<BR>
<T>5.3 - Taxa de Administra��o 
<BR>
<T>Ao valor solicitado ser� acrescido, no momento da concess�o, taxa de Administra��o declarada no item 5 do quadro 
CONDI��ES GERAIS, tal valor ser� incorporado ao saldo devedor.
<BR>
<BR>
<T>5.4 - Taxa Mensal de Risco
<BR>
<T>Ser� cobrado, juntamente com a parcela mensal do empr�stimo a t�tulo de contribui��o ao Fundo de Oscila��o de 
Risco de Inadimpl�ncia, para cobertura do saldo devedor em caso de falecimento do <B>MUTU�RIO</B>, conforme fatores 
vigentes na data da concess�o e constante no quadro CONDI��ES GERAIS naquela data.
<BR>
<BR>
<B>Cl�usula Sexta</B>
<BR>
Ocorrendo o vencimento antecipado da d�vida, por cancelamento da inscri��o do <B>MUTU�RIO</B> como Participante ou ap�s 
a rescis�o do v�nculo contratual que mant�m com a Patrocinadora da <B>FUNDA��O CEEE</B>, fica o <B>MUTU�RIO</B> e a 
<B>FUNDA��O CEEE</B> AUTORIZADA a compensar o saldo da d�vida com o valor da restitui��o de contribui��es de que trata o 
Regulamento do respectivo plano de benef�cios ao qual o MUTU�RIA faz parte, at� aquele limite. 
<BR>
<BR>
<T><B>Par�grafo Segundo</B>
<BR>
<T>Ocorrendo quaisquer das hip�teses previstas nesta cl�usula, o <B>MUTU�RIO</B>, desde j�, por esta e na melhor forma de 
direito, nomeia e constitui em car�ter irrevog�vel e irretrat�vel, enquanto perdurarem suas obriga��es contratuais, 
sua bastante procuradora a <B>FUNDA��O CEEE</B>, com a finalidade de receber, junto aos empregadores dele, <B>MUTU�RIO</B>, 
quaisquer import�ncias que lhe forem devidas, at� o montante do saldo da d�vida e imput�-la no respectivo pagamento, 
podendo para tal fim, passar recibo, dar quita��o e assinar tudo o mais que necess�rio for, sem que este mandato 
implique em preju�zo de qualquer das obriga��es assumidas pelo <B>MUTU�RIO</B> neste Contrato.
<BR>
<BR>

<B>Cl�usula S�tima</B>
<BR>Sobre o valor da presta��o paga em atraso, fica o <B>MUTU�RIO</B> sujeito ao pagamento de juros de mora e 
multa correspondentes a atualiza��o monet�ria pela varia��o do INPC-IBGE, acrescido de juros de mora de 1,0% 
(um por cento)ao m�s e multa, sobre o saldo devedor atualizado, na ordem de 2,0% (dois por cento).
<BR>
<BR>
<B>Cl�usula Oitava</B>
<BR>Ocorrendo a inadimpl�ncia do <B>MUTU�RIO</B>, � <B>FUNDA��O CEEE</B> reserva-se o direito de inscrever o 
inadimplente no <B>Cadastro de Prote��o ao Cr�dito do SERASA</B>, conforme contratofirmado entre as entidades.
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
<B>Cl�usula Nona</B>
<BR>A toler�ncia da <B>FUNDA��O CEEE</B> por eventuais atrasos nos pagamentos, n�o importar� em altera��o 
ou nova��o do presente Contrato, nem consistir� precedente arg��vel pelo <B>MUTU�RIO</B>.
<BR>
<BR>
<B>Cl�usula D�cima</B>
<BR>Para quaisquer a��es decorrentes deste Contrato, o <B>MUTU�RIO</B> renuncia ao foro a que tem direito e 
elege de comum acordo com a <B>FUNDA��O CEEE</B>, o foro de Porto Alegre, Capital do Estado do Rio Grande do Sul. 
E, por estarem juntos e contratados, firmam o presente instrumento na presen�a das testemunhas abaixo.
<BR>
<BR>
<B>ATEN��O</B>
<BR>� de responsabilidade do <B>MUTU�RIO</B> pagar a presta��o at� o dia 10 do m�s subsequente, via dep�sito 
em conta corrente da <B>FUNDA��O CEEE</B>, quando n�o ocorrer o d�bito banc�rio ou desconto em folha.
<BR>
<BR>
<BR>
<BR>________________________________ , ____ de ________________ de _______. 
<BR>
<BR>
<BR>
<BR>
<BR>____________________________                    ____________________________
<BR>MUTU�RIO                                                          Representante FCEEE/Red
<BR>  
<BR>
<BR><B>Pela FUNDA��O CEEE</B>
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
