<?
	/*
	#####################################################################################
		PARÂMETROS
		cd_contrato 						--> busca dados a partir do contrato
		cd_emp (cd_empresa) 				--> busca dados a partir do participante
		cd_re  (cd_registro_empregado) 		--> busca dados a partir do participante
		cd_seq (seq_dependencia) 			--> busca dados a partir do participante
		fl_pre = S 						--> imprime somente os valores (força fl_fin = S e fl_ass = S e tp_imp = 1)
		fl_cad = S 						--> imprime dados cadastrais
		fl_fin = S 						--> imprime dados financeiro
		fl_ass = S 						--> imprime assinaturas digital
		fl_dem = S 						--> define titulo. S para DEMONSTRATIVO e outros como PROPOSTA
		tp_imp = 0 						--> imprime todo conteudo
		tp_imp = 1 						--> imprime somente paginas 1, 3 e 5
		tp_imp = 2 						--> imprime somente paginas 2 e 4
		tp_imp = 3 						--> imprime somente a pagina 1 (Demonstrativo)
		nr_via     						--> número de vias do contrato
	#####################################################################################
	*/
	#require_once('inc/sessao.php');
	include_once('inc/class.SocketAbstraction2.inc.php');
	require_once('inc/config.inc.php');
	#define(SKT_IP, '10.63.255.98'); 
	#define(SKT_PORTA, '4444');  
	$LISTNER_IP    = SKT_IP;
	$LISTNER_PORTA = SKT_PORTA;
	
	#### PARAMETROS ####
	$fl_preenche   = ($_REQUEST['fl_pre']  == 'S' ? true : false); #IMPRIME SOMENTE OS VALORES
	$fl_cadastro   = ($_REQUEST['fl_cad']  == 'S' ? true : false); #IMPRIME DADOS CADASTRAIS
	$fl_financeiro = ($_REQUEST['fl_fin']  == 'S' ? true : false); #IMPRIME DADOS FINANCEIROS
	$fl_assinatura = ($_REQUEST['fl_ass']  == 'S' ? true : false); #IMPRIME ASSINATURAS
	$fl_demonstrativo = ($_REQUEST['fl_dem']  == 'S' ? true : false); #DEFINE TITULO
	$tp_imp        = ($_REQUEST['tp_imp']  == ''  ? 0    : $_REQUEST['tp_imp']); # 0 -> IMPRIME TUPO, 1 -> IMPRIME PAG 1 E 4, 2 -> IMPRIME PAG 2 E 3, 3 -> IMPRIME SOMENTE PAG 1
	$tp_imp        = ($tp_imp              >  3   ? 0    : $tp_imp);
	$nr_via        = ($_REQUEST['nr_via']  == ''  ? 1    : $_REQUEST['nr_via']); #NUMERO DE VIAS DO CONTRATO
	$nr_via        = ($nr_via              <  1   ? 1    : $nr_via);
	
	if($fl_preenche)
	{
		#### FORÇA IMPRESSÃO ####
		$fl_financeiro = true;
		$fl_assinatura = true;
		$tp_imp        = 3;
	}
	
	if(trim($_REQUEST['cd_contrato']) != "")
	{
		#### DADOS DO PARTICIPANTE E CONTRATO DE EMPRESTIMO ####
		$skt = new Socket();
        $skt->SetRemoteHost($LISTNER_IP);
        $skt->SetRemotePort($LISTNER_PORTA);
		
        if ($skt->Connect()) 
		{
			$cmd= "fnc_gera_proposta;".$_REQUEST['cd_contrato'];
			$xml_retorno = $skt->Ask($cmd);
			//echo "$xml_retorno";
            if ($skt->Error()) 
			{ 
				if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
				{
					echo "<PRE style='color:red;'>ERRO DE CONEXÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<BR><BR>".$cmd."<BR><BR>".$skt->GetErrStr()."</PRE>";
				}
				else
				{
					echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
				}
				exit;				
            } 
			else 
			{
	            $ob_dom = new DOMDocument();
	            $ob_dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$xml_retorno);
				$ob_dados = $ob_dom->getElementsByTagName("fld");
				//print_r($ob_dados);
				foreach($ob_dados as $ob_campo) 
				{
					//$ob_campo->nodeValue = utf8_decode($ob_campo->nodeValue);
					$AR_DADOS[$ob_campo->getAttribute('id')] = str_replace("NULL","",$ob_campo->nodeValue);
	            }			   
				#echo "<PRE>";print_r($AR_DADOS);exit;
				
				if(trim($AR_DADOS['ERR']) != "")
				{
					if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
					{
						echo "<PRE style='color:red;'>ERRO DE EXECUÇÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<BR><BR>".$cmd."</PRE>";
					}
					else
					{
						echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
					}
					exit;				
				}
			}
		}
		else
		{
			if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
			{
				echo "<PRE style='color:red;'>ERRO DE CONEXÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<PRE>";
			}
			else
			{
				echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
			}
			exit;
		}		

	}
	else if ((trim($_REQUEST['cd_emp']) != "") and (trim($_REQUEST['cd_re']) != "") and (trim($_REQUEST['cd_seq']) != "")) 
	{
		#### DADOS CADASTRAIS DO PARTICIPANTES S/ CONTRATO ####
		$skt = new Socket();
        $skt->SetRemoteHost($LISTNER_IP);
        $skt->SetRemotePort($LISTNER_PORTA);
		
        if ($skt->Connect()) 
		{
			$cmd = "fnc_serialize_table;participantes;cd_empresa=".$_REQUEST['cd_emp']." and cd_registro_empregado=".$_REQUEST['cd_re']." and seq_dependencia=".$_REQUEST['cd_seq'];
			$xml_retorno = $skt->Ask($cmd);
			//echo "$xml_retorno";
			
            if ($skt->Error()) 
			{ 
				if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
				{
					echo "<PRE style='color:red;'>ERRO DE EXECUÇÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<BR><BR>".$cmd."<BR><BR>".$skt->GetErrStr()."</PRE>";
				}
				else
				{
					echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
				}
				exit;				
            } 
			else 
			{
	            $ob_dom = new DOMDocument();
	            $ob_dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$xml_retorno);
				$ob_dados = $ob_dom->getElementsByTagName("fld");
				//print_r($ob_dados);
				foreach($ob_dados as $ob_campo) 
				{
					//$ob_campo->nodeValue = utf8_decode($ob_campo->nodeValue);
					$AR_DADOS[$ob_campo->getAttribute('id')] = str_replace("NULL","",$ob_campo->nodeValue);
	            }			   
				//echo "<PRE>";
				//print_r($AR_DADOS);
				
				if(trim($AR_DADOS['ERR']) != "")
				{
					if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
					{
						echo "<PRE style='color:red;'>ERRO DE CONEXÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<BR><BR>".$cmd."</PRE>";
					}
					else
					{
						echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
					}
					exit;				
				}
			}			
			
			$fl_financeiro = false; # NÃO IMPRIME DADOS FINANCEIROS
		}
		else
		{
			if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
			{
				echo "<PRE style='color:red;'>ERRO DE CONEXÃO COM O LISTENER: ".$LISTNER_IP.":".$LISTNER_PORTA."<PRE>";
			}
			else
			{
				echo "<PRE style='color:red;'>Entre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.<PRE>";
			}
			exit;
		}
	}
	else
	{
		$fl_financeiro = false; # NÃO IMPRIME DADOS FINANCEIROS
	}

	require('inc/fpdf153/WriteTag.php');
	
	$ob_pdf=new PDF();
	/*
	class pPDF extends PDF
	{
		var $nr_pagina = 1;
		
		function Footer()
		{
		    //Go to 1.5 cm from bottom
		    $this->SetY(-22);
		    //Select Arial italic 8
		    $this->SetFont('Arial','I',8);
		    //Print current and total page numbers
		    //$this->Cell(0,10,'Página '.($this->PageNo()-1).'/{nb}',0,0,'C');
			if($this->nr_pagina == 4)
			{
				
				$this->Cell(0,10,($this->nr_pagina),0,0,'R');
				$this->nr_pagina = 1;
			}
			else
			{
				$this->Cell(0,10,($this->nr_pagina),0,0,'R');
				$this->nr_pagina++;
			}
				
			
		}
	}

	$ob_pdf = new pPDF();
	$ob_pdf->AliasNbPages();
	*/ 
	
	$nr_conta = 0;
	while($nr_conta < $nr_via)
	{
		include('contrato_emprestimo_conteudo.php');
		$nr_conta++;
	}
	
	$ob_pdf->Output();
	
	############################# FUNCOES AUXILIARES ####################################
	
	function preenche($fl_preenche,$fl_muda)
	{
		global $ob_pdf;
		if($fl_muda)
		{
			#### OCULTA PARA PREENCHIMENTO ####
			if($fl_preenche)
			{
				$ob_pdf->SetTextColor(255,255,255);
				$ob_pdf->SetStyle("","courier","N",12,"255,255,255",-1);
				$ob_pdf->SetStyle("t","courier","N",12,"255,255,255",-1);
				$ob_pdf->SetStyle("p","courier","N",12,"255,255,255",-1);
				$ob_pdf->SetStyle("b","courier","B",12,"255,255,255",-1);
				$ob_pdf->SetStyle("B","courier","B",12,"255,255,255",-1);				
			}
			else
			{
				$ob_pdf->SetTextColor(0, 0, 0); 
				$ob_pdf->SetStyle("p","arial","N",12,"0,0,0",-1);
				$ob_pdf->SetStyle("b","arial","B",12,"0,0,0",-1);
				$ob_pdf->SetStyle("B","arial","B",12,"0,0,0",-1);				
			}
		}
		else
		{
			$ob_pdf->SetTextColor(0, 0, 0);
			$ob_pdf->SetStyle("p","arial","N",12,"0,0,0",-1);
			$ob_pdf->SetStyle("b","arial","B",12,"0,0,0",-1);
			$ob_pdf->SetStyle("B","arial","B",12,"0,0,0",-1);		
		}
	}
	
	function texto($texto)
	{
	 	$texto = str_replace("\r\n","\n",$texto); 
	 	$texto = str_replace("\f",'',$texto); 
		$texto = str_replace("\r",'',$texto); 
		$ar_quebra = explode("<BR>",$texto);
		$nr_fim   = count($ar_quebra);
		$nr_conta = 0;
		while($nr_conta < $nr_fim)
		{
			$fl_identa = substr(trim($ar_quebra[$nr_conta]),0,3);
			$ar_quebra[$nr_conta] = str_replace("<T>",'',$ar_quebra[$nr_conta]); 
			if($fl_identa == "<T>")
			{
				if(trim($ar_quebra[$nr_conta]) == "")
				{
					textoI(3,$ar_quebra[$nr_conta]);
				}
				else
				{
					textoI(0,$ar_quebra[$nr_conta]);
				}	
			}
			else
			{
				if(trim($ar_quebra[$nr_conta]) == "")
				{
					textoN(3,$ar_quebra[$nr_conta]);
				}
				else
				{
					textoN(0,$ar_quebra[$nr_conta]);
				}
			}
			$nr_conta++;
		}
	}

	function textoI($quebra,$texto)
	{
		global $ob_pdf;
		$ob_pdf->Ln($quebra);	
		$ob_pdf->WriteTag(190,6,$texto,0,"J",0,"10,0,0,0");
	}

	function textoN($quebra,$texto)
	{
		global $ob_pdf;
		$ob_pdf->Ln($quebra);	
		$ob_pdf->WriteTag(190,6,$texto,0,"J");
	}

	function ConvertSize($size=5,$maxsize=0)
	{
		// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
		//Identify size (remember: we are using 'mm' units here)
		if ( stristr($size,'px') ) $size *= 0.2645; //pixels
		elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
		elseif ( stristr($size,'mm') ) $size += 0; //millimeters
		elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
		elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
		elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
		elseif ( stristr($size,'%') )
		{
			$size += 0; //make "90%" become simply "90" 
			$size *= $maxsize/100;
		}
		else $size *= 0.2645; //nothing == px
	  
		return $size;
	}	
	
	function getFieldValueXML($campos, $fldId) 
	{
		$pos = -1;
		$i = 0;
		foreach ($campos as $cmp) 
		{
			if ($cmp->getAttribute('id') == $fldId) 
			{
				$pos = $i;
				$campoSelecionado = $cmp;
				break;
			}
			$i++;
		}
		if ($pos > -1) 
		{
			return $campoSelecionado->nodeValue;
		} 
		else 
		{
			return 'undefined';
		}
	}	
?>