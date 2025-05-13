<?php
class emprestimo_np extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

	function completa($cd_contrato = 0)
	{
		if(intval($cd_contrato) > 0)
		{
			#### 1 - COMPLETA ####
			$ar_dado = $this->getSocket("fnc_gera_proposta;".intval($cd_contrato));
			$this->np($ar_dado, 1);
		}
	}

	function proposta($cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0)
	{
		#### 2 - PROPOSTA ####
		$ar_dado = $this->getSocket("fnc_serialize_table;participantes;cd_empresa=".intval($cd_empresa)." and cd_registro_empregado=".intval($cd_registro_empregado)." and seq_dependencia=".intval($seq_dependencia)."");
		$this->np($ar_dado, 2);
	}
	
	function proposta_preenche($cd_contrato = 0)
	{
		if(intval($cd_contrato) > 0)
		{		
			#### 3 - PREENCHE PROPOSTA ####
			$ar_dado = $this->getSocket("fnc_gera_proposta;".intval($cd_contrato));
			$this->np($ar_dado, 3);
		}
	}	
	
	function branco_nota()
	{
		#### 4 - NOTA PROMISSORIA EM BRANCO ####
		$ar_dado = Array();
		$this->np($ar_dado, 4);
	}	
	
	function branco_nota_autoriza()
	{
		#### 5 - NOTA PROMISSORIA E AUTORIZACAO EM BRANCO ####
		$ar_dado = Array();
		$this->np($ar_dado, 5);
	}	

	private function preencheValoresContrato(&$ob_pdf, $ar_dado)
	{
		#### VALORES ####
		$ob_pdf->SetFont('segoeuib','',12);
		$ob_pdf->Text(14,28.5, "Nº ".$ar_dado["cd_contrato"]);
		$ob_pdf->Text(84,28.5, "R$ ".$ar_dado["montante_concedido"]);
		$ob_pdf->SetFont('segoeuib','',9.5);
		$ob_pdf->Text(16,60, $ar_dado["montante_concedido_extenso"]);	
	}
	
    private function np($ar_dado = Array(), $tp = 0)
    {
		/*
			## TIPO ##
			1 => COMPLETA
			2 => DADOS CADASTRAIS PARTICIPANTE (PROPOSTA)
			3 => SOMENTE INFO CONTRATO (CD CONTRATO, VL MONTANTE, VL MONTANTE POR EXTENSO) - PREENCHE PROPOSTA
			4 => NOTA PROMISSORIA EM BRANCO
			5 => NOTA PROMISSORIA E AUTORIZACAO EM BRANCO
		*/	
		
		$this->load->plugin('fpdf');
		$ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->SetMargins(10,14,5);	
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');		
		
		if(intval($tp) == 3)
		{
			#### PREENCHE PROPOSTA ####
			$ob_pdf->AddPage();
			$this->preencheValoresContrato($ob_pdf,$ar_dado);
		}
		elseif((intval($tp) > 0) and (intval($tp) < 6))
		{
			$ob_pdf->SetNrPagDe(true);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "NOTA PROMISSÓRIA";
			$ob_pdf->AddPage();
						

			#### NR CONTRATO ####			
			$ob_pdf->Line(10,22,52,22);
			$ob_pdf->Line(10,22,10,32);
			$ob_pdf->Line(10,32,52,32);
			$ob_pdf->Line(52,22,52,32);
			
			#### VL VALOR ####			
			$ob_pdf->Line(80,22,200,22);
			$ob_pdf->Line(80,22,80,32);
			$ob_pdf->Line(80,32,200,32);
			$ob_pdf->Line(200,22,200,32);	

			#### QUADRO ####			
			$ob_pdf->Line(10,34,200,34);
			$ob_pdf->Line(10,34,10,147);
			$ob_pdf->Line(10,147,200,147);
			$ob_pdf->Line(200,34,200,147);	

			$ob_pdf->SetFont('courier','',6);
			$ob_pdf->Text(10,149.5, "Cód. 283 fl 05/99");		
			
			if(in_array(intval($tp),array(1,2)))
			{
				if(intval($tp) == 1)
				{
					#### DADOS DO CONTRATO ####
					$this->preencheValoresContrato($ob_pdf,$ar_dado);
				}
			
				#### DADOS PARTICIPANTES ####
				$ob_pdf->SetFont('segoeuil','',9.5);
				$ob_pdf->Text(36,110.5, $ar_dado["nome"]);
				$ob_pdf->Text(36,126.5, $ar_dado["cpf_mf"]);
				$ob_pdf->SetXY(35,129.3);$ob_pdf->MultiCell(160, 7, $ar_dado["logradouro"]." - ".$ar_dado["bairro"]." - ".$ar_dado["cidade"]."/".$ar_dado["unidade_federativa"], 0, 'J');
			}
		
			$ob_pdf->SetXY(14,37);
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 5.5, "No dia _____ de ______________________ de ________ pagarei por esta NOTA PROMISSÓRIA a FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE, CGC Nº 90.884.412/0001-24 ou à sua ordem, na cidade de _______________________________________________ a quantia de:", 0, 'J');		

			$ob_pdf->SetXY(14,$ob_pdf->GetY() + 5);
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 5.5, "____________________________________________________________________________________
em moeda corrente do país, e por qualquer atraso que ocorrer pagarei mais juro de mora de ______% ao ano.", 0, 'J');


			$ob_pdf->SetXY(14,$ob_pdf->GetY() + 6);
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 4, "_______________________ , ______ de ____________________ de __________", '0', 'R');


			$ob_pdf->SetXY(14,$ob_pdf->GetY() + 1);
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 4, "Avalistas:

___________________________________               ___________________________________
CNPJ/CPF:                                         CNPJ/CPF: ", 0, 'J');


			$ob_pdf->SetXY(14,106);
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 8, "Emitente: ___________________________________________________________________________
Assinatura: _________________________________________________________________________
CNPJ/CPF: ___________________________________________________________________________", 0, 'J');
			$ob_pdf->SetXY(14,$ob_pdf->GetY());
			$ob_pdf->SetFont('courier','',10);
			$ob_pdf->MultiCell(182, 7, "Endereço: ___________________________________________________________________________
          ___________________________________________________________________________", 0, 'J');		  

			
			#################################################################################################################
			$cd_instituicao = (array_key_exists("cd_instituicao", $ar_dado) ? intval($ar_dado["cd_instituicao"]) : -1);
			
			if (($cd_instituicao == 41) or (($cd_instituicao == -1) and (intval($tp) == 5)))
			{
				#### AUTORIZACAO DE DEBITO EM CONTA BANRISUL ####
				#### QUADRO ####			
				$ob_pdf->Line(10,152,200,152);
				$ob_pdf->Line(10,152,10,280);
				$ob_pdf->Line(10,280,200,280);
				$ob_pdf->Line(200,152,200,280);	
				
				#### TITULO ####			
				$ob_pdf->Line(80,156,195,156);
				$ob_pdf->Line(80,156,80,163);
				$ob_pdf->Line(80,163,195,163);
				$ob_pdf->Line(195,156,195,163);		
				$ob_pdf->SetFont('segoeuib','',12);
				$ob_pdf->Text(93,161, "AUTORIZAÇÃO PARA DÉBITO C/C BANRISUL");		
				
				#### LOGO ####
				$ob_pdf->Image('./img/logofundacao_carta.jpg', 14, 155, $ob_pdf->ConvertSize(150), $ob_pdf->ConvertSize(33),'','',false);
				
				if(in_array(intval($tp),array(1,2)))
				{
					#### DADOS PARTICIPANTES ####
					$ob_pdf->SetFont('segoeuil','',9.5);
					$ob_pdf->Text(27,231, $ar_dado["nome"]);
					$ob_pdf->SetXY(34,233); $ob_pdf->MultiCell(161, 6.5, $ar_dado["logradouro"]." - ".$ar_dado["bairro"]." - ".$ar_dado["cidade"]."/".$ar_dado["unidade_federativa"], 0, 'J');
					$ob_pdf->Text(35,250, "(".intval($ar_dado["ddd"]).") ".intval($ar_dado["telefone"]));
					$ob_pdf->Text(61,256.7, (array_key_exists("conta", $ar_dado) ? $ar_dado["conta"] : ""));
					$ob_pdf->Text(51,263.3, $ar_dado["cd_empresa"]."/".$ar_dado["cd_registro_empregado"]."/".$ar_dado["seq_dependencia"]);
				}
				
				$ob_pdf->SetXY(14,167);
				$ob_pdf->SetFont('segoeuib','',12);
				$ob_pdf->MultiCell(182, 5, "Ao BANCO DO ESTADO DO RIO GRANDE DO SUL, S.A", 0, 'J');				
				
				$ob_pdf->SetXY(14,$ob_pdf->GetY() + 3);
				$ob_pdf->SetFont('segoeuil','',11);
				$ob_pdf->MultiCell(182, 5, "Agência: _______________________________________________", 0, 'J');			
				
				$ob_pdf->SetXY(14,$ob_pdf->GetY() + 4);
				$ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(182, 5, "AUTORIZAÇÃO PARA PAGAMENTO E DÉBITO EM CONTA", 0, 'J');	

				$ob_pdf->SetXY(14,$ob_pdf->GetY());
				$ob_pdf->SetFont('segoeuil','',8);
				$ob_pdf->MultiCell(182, 3, "Solicito(amos) efetuarem o pagamento, ao(s) beneficiário(s), do(s) encargo(s) a seguir relacionado(s), no(s) respectivo(s) vencimento(s):", 0, 'J');		
				
				
				$ob_pdf->SetXY(14,$ob_pdf->GetY() + 1);
				$ob_pdf->SetLineWidth(0.2);
				$ob_pdf->SetDrawColor(0,0,0);
				$ob_pdf->SetWidths(array(90,90));
				$ob_pdf->SetAligns(array('C','C'));
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->Row(array("ENCARGO", "ENCARGO EM NOME DE:"));
				$ob_pdf->SetX(14);
				$ob_pdf->Row(array("\n\n\n", ""));

				$ob_pdf->SetXY(14,$ob_pdf->GetY()+1);
				$ob_pdf->SetFont('segoeuil','',8);
				$ob_pdf->MultiCell(180, 3, "Fica, desde já, este Banco autorizado a debitar a(s) importância(s) paga(s), bem como as despesas bancárias devidas as esta Instituição, na minha/nossa conta corrente que mantenho(emos) junto a esta Agência. Outrossim, asseguro(amos) que não haverá nenhum ônus a esse Banco por eventual não atendimento do(s) compromisso(s) na(s) data(s) de vencimento, em caso de falta de fundos em minha/nossa conta corrente, na data em que deveria ocorrer o pagamento ao(s) beneficiário(s).", 0, 'J');					
				
				$ob_pdf->SetXY(14,227);
				$ob_pdf->SetFont('courier','',9.5);
				$ob_pdf->MultiCell(182, 6.5, "Nome: ___________________________________________________________________________________
Endereço: _______________________________________________________________________________
          _______________________________________________________________________________
Telefone: _______________________________________________________________________________
Número Conta Corrente: __________________________________________________________________
Empresa/Re.D/Seq: _______________________________________________________________________", 0, 'J');	

				$ob_pdf->SetXY(14,$ob_pdf->GetY() + 6);
				$ob_pdf->SetFont('courier','',9.5);
				$ob_pdf->MultiCell(182, 4, "Assinatura: _____________________________________________________________________________", 0, 'J');		
			}
		}
		
		$ob_pdf->Output();
		exit; 		
    }
	
	private function getSocket($cmd = "")
	{
		if(trim($cmd))
		{
			$ar_conf    = getListner();
			$ar_retorno = Array();

			$this->load->plugin('socketfc');
			$ob_socket = new socketfc();
			$ob_socket->SetRemoteHost($ar_conf["IP"]);
			$ob_socket->SetRemotePort($ar_conf["PORTA"]);
			$ob_socket->SetBufferLength(262144); // 256KB
			$ob_socket->SetConnectTimeOut(1);	
			
			if ($ob_socket->Connect()) 
			{
				$skt_retorno = $ob_socket->Ask($cmd);
				
				if ($ob_socket->Error()) 
				{
					echo("ERRO 3:".br(2).$cmd.br(2).print_r($ar_conf,true).br(2).$ob_socket->GetErrStr()); 
					exit;
				}	
				else
				{
					$ob_dom = new DOMDocument();
					$ob_dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$skt_retorno);
					$ob_fld = $ob_dom->getElementsByTagName("fld");				
				
					$ds_erro = $ob_socket->getFieldValueXML($ob_fld, 'ERR');
					
					if(trim($ds_erro) == "NULL") 
					{	
						
						foreach($ob_fld as $campo) 
						{
							$campo->nodeValue = utf8_decode($campo->nodeValue);
							$ar_retorno[strtolower($campo->getAttribute('id'))] = $campo->nodeValue;
							
							#echo $campo->getAttribute('id')." => ".$campo->nodeValue.br(1); 
						}						
						
						return $ar_retorno;
					}
					else
					{
						echo("ERRO 4:".br(2).$cmd.br(2).print_r($ar_conf,true).br(2).$ds_erro); 
						exit;
					}					
				}
			}
			else 
			{
				echo("ERRO 2:".br(2).$cmd.br(2).print_r($ar_conf,true).br(2).$ob_socket->GetErrStr());
				exit;
			}		
        }
		else 
		{
			echo("ERRO 1");
			exit;
		}		
	}	
}
