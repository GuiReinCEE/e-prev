<?php
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/codigo_barra.php');	
	include_once('inc/codigo_bdl_empresa.php');	

	#echo "<PRE>".print_r($_POST,true)."</PRE>";
	#echo "<PRE>"; print_r($_SERVER); echo "</PRE>";
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$tpl = new TemplatePower('tpl/tpl_familia_pagamento_risco_bdl.html');
		$tpl->prepare();
		$tpl->newBlock('conteudo');

		if($_POST['cd_tipo_pagamento'] == "A")
		{
			$_POST['vl_resumo_prev']  = number_format(str_replace(",",".",str_replace(".","",$_POST['vl_contribuicao_minima'])) + str_replace(",",".",str_replace(".","",$_POST['vl_contribuicao'])),2,",",".");
			$_POST['vl_resumo_risco'] = "0,00";
		}
		
		#### BUSCA PARTICIPANTE ####
		$qr_sql = "
					SELECT p.*, funcoes.format_cpf(p.cpf_mf) AS cpf
					  FROM public.participantes p
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_POST['re_md5']."'
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_participante = pg_fetch_array($ob_resul);	

		$tpl->assign('cta_identificadora', '90884412');
		$tpl->assign('bctype', '2');
		$tpl->assign('dim', '1');
		$tpl->assign('type', '3');	
		
		$tpl->assign('nome_participante', $ar_participante['nome']);
		$tpl->assign('cpf', $ar_participante['cpf']);
		$tpl->assign('endereco_participante1', $ar_participante['logradouro'].', '.$ar_participante['bairro']);
		$tpl->assign('endereco_participante2', $ar_participante['cep'].'-'.$ar_participante['complemento_cep'].' - '.$ar_participante['cidade'].' - '.$ar_participante['unidade_federativa']);
		$tpl->assign('red', $ar_participante['cd_empresa']."/".$ar_participante['cd_registro_empregado']."/".$ar_participante['seq_dependencia']);
		$tpl->assign('dt_vencimento', $_POST['dt_vencimento']);
		$tpl->assign('ds_tipo_pagamento', $_POST['ds_tipo_pagamento']);
		$tpl->assign('nr_competencia', $_POST['nr_competencia']);
		$tpl->assign('num_bloqueto', (intval($_POST['num_bloqueto']) > 0 ? "Identificador: ".$_POST['num_bloqueto'] : ""));
		$tpl->assign('dt_impressao', date('d/m/Y'));
		$tpl->assign('dt_impressao_2', date('d/m/Y G:i:s'));
		$tpl->assign('exibeValorADM',(intval($_POST['cd_tipo_pagamento_adm']) == 1 ? "": "display:none;"));
		$tpl->assign('exibeMsgADM',(intval($_POST['cd_tipo_pagamento_adm']) == 1 ? "display:none;": ""));
		$tpl->assign('agencia',    '100.81');
		$tpl->assign('cod_cedente','870921017');
		$tpl->assign('especie_doc','805076');
		
		$tpl->assign('listaCompMes', $_POST['listaCompMes']);
		$tpl->assign('listaCompPrevAtrasada', $_POST['listaCompPrevAtrasada'].(trim($_POST['listaCompPrevAtrasada']) != "" ? (trim($_POST['listaCompMes']) != "" ? ", " : "") : "" ));
		$tpl->assign('listaCompRiscoAtrasada', $_POST['listaCompRiscoAtrasada'].(trim($_POST['listaCompRiscoAtrasada']) != "" ? (trim($_POST['listaCompMes']) != "" ? ", " : "") : "" ));
		
		$tpl->assign('vl_contribuicao_prev',  $_POST['vl_resumo_prev']);
		$tpl->assign('vl_contribuicao_risco', $_POST['vl_resumo_risco']);
		
		$tpl->assign('vl_contribuicao_adm', $_POST['vl_resumo_adm']);
		$tpl->assign('vl_contribuicao_correio', $_POST['vl_resumo_correio']);
		
		$tpl->assign('vl_boleto', $_POST['vl_resumo_boleto']);
		$tpl->assign('vl_contribuicao_total', $_POST['vl_total_pagar']);
		
		
		#### ID PARA REGISTRO NO BANRISUL ####
		$cd_opcao = ($_POST['cd_tipo_pagamento'] == 'M' ? 1 : ($_POST['cd_tipo_pagamento'] == 'A' ? 2 : 0)); #1 MES / 2 ADICIONAL
		$id_banrisul = getIDBanrisul($ar_participante['cd_empresa'], $ar_participante['cd_registro_empregado'], $ar_participante['seq_dependencia'], $_POST['num_bloqueto'], $cd_opcao, $_POST['dt_vencimento'], str_replace(",",".",str_replace(".","",$_POST['vl_total_pagar'])));
		
		#print_r($id_banrisul); exit;
		
		if(intval($id_banrisul) == 0)
		{
			echo '
					<body style="margin: 0px; text-align:center; padding: 0px;">
						<table width="695" border="0" align="center">
							<tr>
								<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
									<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
										SINPRORS PREVIDÊNCIA - PAGAMENTO BDL
										<BR><BR>
										ERRO ID REGISTRO BDL
										<BR><BR>
										Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
									</h1>
								</td>
							</tr>
						</table>
					</body>		
					<BR>
					<BR>
				 ';
			exit;
		}
		
		#### GERA SEU NUMERO ####
		$seu_numero = substr(sprintf("%02d", $ar_participante['cd_empresa']),0,2).substr(sprintf("%06d", $ar_participante['cd_registro_empregado']),0,6).substr(sprintf("%02d", $ar_participante['seq_dependencia']),0,2);
		if($_POST['cd_tipo_pagamento'] == "A") #ADICIONAL
		{
			$seu_numero.= "009";
		}
		else 
		{
			$seu_numero.= "000";
		}
		
		$ar_linha = get_linha_dig($ar_participante['cd_empresa'], $ar_participante['cd_registro_empregado'], $_POST['vl_total_pagar'], $_POST['dt_vencimento'], $_POST['cd_tipo_pagamento'], $_POST['num_bloqueto'], $id_banrisul);
		$tpl->assign('codigo_barra', fbarcode($ar_linha['CD']));
		$tpl->assign('linha', $ar_linha['DIG']);
		$tpl->assign('nosso_numero', $ar_linha['nosso_numero']);
		$tpl->assign('seu_numero', $seu_numero);
		
		
		$ar_xml['cd_tipo_pagamento']     = $_POST['cd_tipo_pagamento'];
		$ar_xml['seu_numero']            = $seu_numero;
		$ar_xml['cd_empresa']            = $ar_participante['cd_empresa'];
		$ar_xml['cd_registro_empregado'] = $ar_participante['cd_registro_empregado'];
		$ar_xml['seq_dependencia']       = $ar_participante['seq_dependencia'];
		$ar_xml['cpf']                   = $ar_participante['cpf_mf'];
		$ar_xml['nome']                  = $ar_participante['nome'];
		$ar_xml['endereco']              = $ar_participante['logradouro'];
		$ar_xml['cep']                   = sprintf("%05d",$ar_participante['cep']).sprintf("%03d",$ar_participante['complemento_cep']);
		$ar_xml['cidade']                = $ar_participante['cidade'];
		$ar_xml['uf']                    = $ar_participante['unidade_federativa'];
		$ar_xml['dt_vencimento']         = $_POST['dt_vencimento'];
		$ar_xml['num_bloqueto']          = $_POST['num_bloqueto'];
		$ar_xml['vl_valor']              = $_POST['vl_total_pagar'];
		$ar_xml['id_banrisul']           = $id_banrisul;
		
		$registro_ambiente = "P"; #### P -> PRODUCAO, T -> HOMOLOGACAO
		$xml_envio    = geraXML($ar_xml,$registro_ambiente);
		$xml_retorno  = registraBanrisul($xml_envio,$registro_ambiente);
		$ar_check_xml = checkRetorno($xml_retorno,$registro_ambiente);
		
		$_CD_BDL = gravar_log_impressao($ar_participante['cd_empresa'], 
							 $ar_participante['cd_registro_empregado'], 
							 $ar_participante['seq_dependencia'], 
							 $_POST['nr_mes'], 
							 $_POST['nr_ano'], 
							 $_POST['dt_vencimento'], 
							 $_POST['vl_total_pagar'], 
							 $ar_linha['DIG'], 
							 $ar_linha['CD'], 
							 $_SERVER['REMOTE_ADDR'],
							 $_POST['num_bloqueto'],
							 $xml_envio,
							 $xml_retorno,
							 $ar_check_xml,
							 $id_banrisul,
							 $registro_ambiente);
							 
		#echo "<PRE><!-- ".print_r($ar_check_xml,true)." --></PRE>";
		
		
		if($ar_check_xml['fl_erro'] == "S")
		{
			echo '
					<body style="margin: 0px; text-align:center; padding: 0px;">
						<table width="695" border="0" align="center">
							<tr>
								<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
									<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
										SINPRORS PREVIDÊNCIA - PAGAMENTO BDL
										<BR><BR>
										'.$ar_check_xml['titulo'].': '.$ar_check_xml['mensagem'].'
										<BR><BR>
										Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
									</h1>
								</td>
							</tr>
						</table>
					</body>		
					<BR>
					<BR>
				 ';
			exit;			
		}							 
			
			
		#### EXIBE BOLETO EM PDF (10/09/2019) ####
		header("location: bdl.php?b=".md5($_CD_BDL));
		exit;				
			
		$tpl->printToScreen();
	}
	else
	{
	}

	######################################################################################################################
	
	function checkRetorno($xmlText,$registro_ambiente)
	{
		/*
		3.3.1. Código de retorno
		Código adotado para identificar o tipo de retorno da solicitação.
		Código Descrição
		01 Sucesso, boleto registrado Banrisul
		02 Sucesso, boleto registrado Banrisul e centralizado
		03 Falha
		04 Homologado		
		*/
		
		$ar_check['fl_erro']  = "S";
		$ar_check['titulo']   = "ERRO 0";
		$ar_check['mensagem'] = "Não foi possível registra o BDL";				
		$ar_check['debug']    = "";	
		
		$dom = new DOMDocument("1.0");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		libxml_use_internal_errors(true);
		$dom->loadXML($xmlText);
		libxml_clear_errors();
		#print_r($dom);

		$fc_erro = $dom->getElementsByTagName("fcsituacao"); 
		#print_r($fc_erro->length);

		if(intval($fc_erro->length) == 0)
		{
			$dados = $dom->getElementsByTagName("dados");
			if(intval($dados->length) > 0)
			{
				#print_r( $dados->item(0)->getAttribute('retorno'));  #T homologacao P producao
				if(intval($dados->item(0)->getAttribute('retorno')) > 0)
				{
					if(($registro_ambiente == "P") AND (in_array(intval($dados->item(0)->getAttribute('retorno')), array(1,2)))) #PRODUCAO
					{
						$ar_check['fl_erro']  = "N";
						$ar_check['titulo']   = "OK";
						$ar_check['mensagem'] = "REGISTRADO";
						$ar_check['debug']    = "AMBIENTE: ".$dados->item(0)->getAttribute('ambiente');										
					}					
					elseif(intval($dados->item(0)->getAttribute('retorno')) == 4) #HOMOLOGACAO
					{
						$ar_check['fl_erro']  = "N";
						$ar_check['titulo']   = "OK";
						$ar_check['mensagem'] = "REGISTRADO";
						$ar_check['debug']    = "AMBIENTE: ".$dados->item(0)->getAttribute('ambiente');										
					}
					else
					{
						$ocorrencia = $dom->getElementsByTagName("ocorrencia"); 
						#print_r($ocorrencia);
						
						$msg = "";
						foreach($ocorrencia as $e) 
						{
							$msg.= $e->getAttribute('codigo')." - ".$e->getAttribute('mensagem').'<BR>';
						}				

						$ar_check['fl_erro']  = "S";
						$ar_check['titulo']   = "ERRO 4";
						$ar_check['mensagem'] = $msg;	
						$ar_check['debug']    = "";										
					}
				}
				else
				{
					$ar_check['fl_erro']  = "S";
					$ar_check['titulo']   = "ERRO 3";
					$ar_check['mensagem'] = "";	
					$ar_check['debug']    = "";				
				}			
			}
			else
			{
				$ar_check['fl_erro']  = "S";
				$ar_check['titulo']   = "ERRO 2";
				$ar_check['mensagem'] = "";				
				$ar_check['debug']    = "";				
			}
		}
		else
		{
			$ar_check['fl_erro']  = "S";
			$ar_check['titulo']   = "ERRO 1";
			$ar_check['mensagem'] = "";
			$ar_check['debug']    = ($fc_erro->item(0)->getAttribute('id'))."-".($fc_erro->item(0)->nodeValue);
		}		
		
		return $ar_check;
	}	
	
	function registraBanrisul($xml_envio,$registro_ambiente)
	{
		if($registro_ambiente == "P")
		{
			$ws="https://www.fcprev.com.br/wsbanrisul/wsbanrisul-producao.php"; ### PRODUCAO
		}
		else
		{
			$ws="https://www.fcprev.com.br/wsbanrisul/wsbanrisul-desenv.php"; ### HOMOLOGACAO
		}

		$post_data = array('xml' => $xml_envio);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$ws);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

		$xml_response = curl_exec($ch);

		return $xml_response;
	}
	
	function geraXML($ar_dados,$registro_ambiente)
	{
		/*
		0100022406048 # COBRANCA.BDL01.D1303 #ATRASADA / AUTOPATROC
		0100861678053 # COBRANCA.BDL02.D1303 #SENGE
		0100870921017 # COBRANCA.BDL03.D1303 #FAMILIA / SINPRO
		0100022406110 # COBRANCA.BDL04.D1303 #EMPRESTIMO
		PORTO ALEGRE
		*/		
		
		if($ar_dados['cd_tipo_pagamento'] == "A") #ADICIONAL
		{
			#$partic_identificador = getIDPartic($ar_dados['cd_empresa'], $ar_dados['cd_registro_empregado'], $ar_dados['seq_dependencia']);
			#$nosso_numero = substr(sprintf("%08d", $partic_identificador.'9'),0,8); // ID PARTIC (7) + 9 (adicional)
			
			$nosso_numero = substr(sprintf("%08d", $ar_dados['id_banrisul']),0,8); 
			$referencia = "PAGAMENTO ADICIONAL";
		}
		else 
		{
			#$nosso_numero = substr(sprintf("%08d", intval($ar_dados['num_bloqueto']).'0'),0,8); // NUMERO BLOQUETO (7) + 9 (adicional)
			
			$nosso_numero = substr(sprintf("%08d", $ar_dados['id_banrisul']),0,8); 
			$referencia = "PAGAMENTO NORMAL";			
		}

		$ar_dt_vencimento = explode("/",$ar_dados['dt_vencimento']);

		$data_vencimento = $ar_dt_vencimento[2]."-".$ar_dt_vencimento[1]."-".$ar_dt_vencimento[0]; //yyyy-mm-dd
		$nosso_numero    = substr(sprintf("%010d", $nosso_numero.DuploDV_Banrisul($nosso_numero)),0,10); //Numero do bloqueto ou indentificador único participante (adicional) - DG 10			
		$beneficiario    = "0100870921017"; //DG 13 # COBRANCA.BDL03.D1303 #FAMILIA / SINPRO
		$seu_numero      = substr($ar_dados['seu_numero'], 0, 13); #EMPRESA + RE + SEQ + (009 ADICIONAL OU 000 NORMAL)
		$valor_nominal   = str_replace(",",".",str_replace(".","",$ar_dados['vl_valor'])); // 20.00
		$cpf_cnpj        = str_replace("-","",str_replace(".","",$ar_dados['cpf'])); //DG 11
		$data_emissao    = date("Y-m-d"); //yyyy-mm-dd
		$nome            = substr($ar_dados['nome'], 0, 40);//DG 40
		$endereco        = substr($ar_dados['endereco'], 0, 35);//DG 35
		$cep             = $ar_dados['cep']; //90020004
		$cidade          = substr($ar_dados['cidade'], 0, 15);//DG 15
		$uf              = substr($ar_dados['uf'], 0, 2);//DG 2
		
		
		$xml='<dados ambiente="'.($registro_ambiente == "P" ? "P" : "T").'">
			<titulo nosso_numero="'.$nosso_numero.'"
				  seu_numero="'.$seu_numero.'"
				  data_vencimento="'.$data_vencimento.'"
				  valor_nominal="'.$valor_nominal.'"
				  especie="02"
				  data_emissao="'.$data_emissao.'">
		  <beneficiario codigo="'.$beneficiario.'"/>
		  <pagador tipo_pessoa="F"
			 cpf_cnpj="'.$cpf_cnpj.'"
			 nome="'.$nome.'"
			 endereco="'.$endereco.'"
			 cep="'.$cep.'"
			 cidade="'.$cidade.'"
			 uf="'.$uf.'"
			 aceite="A"
		  />
		  <instrucoes>
			<juros codigo="3"/>
			<baixa codigo="1" prazo="01"/>
		  </instrucoes>
		  <pag_parcial autoriza="1" codigo="3"/>
		  <mensagens>
		   <mensagem linha="01" texto="NAO RECEBER APOS O VENCIMENTO"/>
		   <mensagem linha="02" texto="NAO COBRAR JUROS DE MORA"/>
		   <mensagem linha="03" texto="NAO CONCEDER DESCONTO / ABATIMENTOS"/>
		   <mensagem linha="04" texto="CONTRIBUICAO REF: '.$referencia.'"/>
		  </mensagens>
		 </titulo>
		</dados>';

		return $xml;
	}
	
	function gravar_log_impressao($cd_empresa, $cd_registro_empregado, $seq_dependencia, $nr_mes, $nr_ano, $dt_vencimento, $vl_valor, $linha, $cd_barra_interno, $ip, $num_bloqueto, $xml_envio, $xml_retorno, $ar_check_xml, $id_banrisul,$registro_ambiente)
    {
    	global $db;
		
		$dados_post_json   = json_encode($_POST);
		$competencia_lista = "";
		
		if(trim($_POST['listaCompPrevAtrasada']) != "")
		{
			$competencia_lista = trim($_POST['listaCompPrevAtrasada']);
		}
		
		if(trim($_POST['listaCompMes']) != "")
		{
			$competencia_lista.= (trim($competencia_lista) != "" ? ", " : "").trim($_POST['listaCompMes']);
		}		
		
		if(trim($competencia_lista) == "")
		{
			$competencia_lista = trim($_POST['nr_competencia']);
		}

    	$qr_sql = "
					INSERT INTO projetos.auto_atendimento_pagamento_impressao
					     (
						   cd_plano,
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   tp_documento, 
						   vl_valor, 
						   mes_competencia, 
						   ano_competencia, 
						   competencia_lista,
						   dt_vencimento,
						   dt_impressao,
						   ip,
						   codigo_barra,
						   codigo_barra_interno,
						   num_bloqueto,
						   dados_post,
						   dados_post_json,
						   xml_envio,
						   xml_retorno,
						   xml_check,
						   fl_erro_registro,
						   nr_registro,
						   tp_registro_ambiente
						 )
					VALUES
					     (
						   9,
						   ".$cd_empresa.", 
						   ".$cd_registro_empregado.", 
						   ".$seq_dependencia.", 
						   'BDL', 
						   ".str_replace(",",".",str_replace(".","",$vl_valor)).", 
						   ".$nr_mes.", 
						   ".$nr_ano.",
						   ".(trim($competencia_lista) != "" ? "'".$competencia_lista."'" : "DEFAULT").",
						   TO_DATE('".$dt_vencimento."', 'DD/MM/YYYY'),
						   CURRENT_TIMESTAMP,
						   '".$ip."',
						   '".$linha."',
						   '".$cd_barra_interno."',
						   ".(intval($num_bloqueto) == 0 ? "DEFAULT" : intval($num_bloqueto)).",
						   '".print_r($_POST, true)."',
						   '".$dados_post_json."',
						   '".pg_escape_string(utf8_decode($xml_envio))."',
						   '".pg_escape_string(utf8_decode($xml_retorno))."',
						   '".pg_escape_string(print_r($ar_check_xml, true))."',
						   '".$ar_check_xml['fl_erro']."',
						   ".intval($id_banrisul).",
						   '".$registro_ambiente."'
					) RETURNING cd_auto_atendimento_pagamento_impressao
				  ";
		#echo "<PRE>$qr_sql</PRE>";	
		#echo "<PRE><!-- ".$qr_sql." --></PRE>";		
		$ob_resul = pg_query($db, $qr_sql);
		$ar_log = pg_fetch_array($ob_resul);
		
		return $ar_log['cd_auto_atendimento_pagamento_impressao'];
    }	

    function get_linha_dig($cd_empresa, $cd_registro_empregado, $vl_valor, $dt_vencimento, $tipo, $num_bloqueto, $id_banrisul)
    {
    	global $db;
    	$ar_linha['CD'] = "";
    	$ar_linha['DIG'] = "";
    	$ar_linha['nosso_numero'] = "";
		
		$nosso_numero = $id_banrisul;
    	
    	// Buscar a diferença entre a data de vencimento e a data padrão de codigo de barras
		$qr_sql = "
					SELECT TO_DATE('".$dt_vencimento."','DD/MM/YYYY') - TO_DATE('19971007', 'YYYYMMDD') AS fator
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_fator = pg_fetch_array($ob_resul);
        $fator = $ar_fator['fator'];
		
		$fatorvcto  = $fator;   
		$nmoeda     = "9";      // moeda (R$)
		$codbank    = "041";    // numero do Banco Banrisul
		$produto    = "2"; 	    // constante - não mexa
		$constante  = "1";	    // constante - não mexa
		$constante2 = "40";	    // constante - não mexa	
		
        // formata o valor do documento para o codigo de barras
        $v = str_replace(chr(44), "", str_replace('.', '', str_replace(",",".",str_replace(".","",$vl_valor))));
        $valor = sprintf("%010d", $v);
		
		// pega e formata o numero do convenio
		$nconvenio = substr(sprintf("%07d", '8709210'),0, 7);
		
		// pega e formata o nosso numero
		$nnum = substr(sprintf("%08d", $nosso_numero),0,8);		
		
		// calcula DV do nosso número e monta nosso numero com DV
		$p = DuploDV_Banrisul($nnum);
		$nosso_numero = "$nnum-$p";

		// Montagem da agencia e conta cedente
		$agencia = substr(sprintf("%04d", '0100'), 0, 4);

		// pega a conta do cedente e formata com 9 digitos sem DV
		$contacedente = substr(sprintf("%09d", '8709210'),0,9);		
		
		// cálculo do duplo digito verificador 
		$dvcpo = "$produto$constante$agencia$nconvenio$nnum$constante2";
		$ddcb = DuploDV_Banrisul($dvcpo);

		// montagem da linha para cálculo do dac
		$cpodac = "$codbank$nmoeda$fatorvcto$valor$produto$constante$agencia$nconvenio$nnum$constante2$ddcb";
		
		//calculando o DAC
		$dac = _modulo11($cpodac);

		// Numero para o codigo de barras com 44 digitos
		$num = "$codbank$nmoeda$dac$fatorvcto$valor$produto$constante$agencia$nconvenio$nnum$constante2$ddcb";		
		
        // Devolve a linha digitavel
        $linha_digitavel = _montaLinha($num,1);

		$linha = str_replace(".","",$linha_digitavel);
		$cod_barras = $num;
		
		/*
		echo "
			<PRE>
			LINHA => ".$linha."
			BARRA => ".$cod_barras."
			LINHA => ".$linha_digitavel."
			BARRA => ".$num."
			</PRE>
		";		
		*/
		
		$ar_linha['CD'] = $cod_barras;
		$ar_linha['DIG'] = $linha_digitavel;
		$ar_linha['nosso_numero'] = $nosso_numero;
		
        return $ar_linha;
    }	
	
	
	
	###########################################
    function _montaLinha($codigo, $r=0)
    {

	 /*
	   *   Modificado por:
	   *	 Fernando Soares - Setembro/2007 - http://www.fernandosoares.com.br
         *
         *   Função:
         *    Montagem da linha digitável de boletos bancarios conforme 
         *    documentos obtidosda Febraban - www.febraban.org.br 
         *
         *   Entrada:
         *     $codigo: string numérica representativa do código de barras do boleto
         *
         *   Opção:  Modo como o módulo 10 usado para cáculo do DV dos campos da linha digitável (real)
         *     $r: 0 - normal
         *         1 - faz o cáculo de acordo como regulamento do banrisul, real
         *
         *   Saída:
         *     Retorna a linha digitável já formatada.
       */


        // Posição 	Conteúdo
        // 1 a 3    Número do banco
        // 4        Código da Moeda - 9 para Real
        // 5        Digito verificador do Código de Barras
        // 6 a 19   Valor (12 inteiros e 2 decimais)
        // 20 a 44  Campo Livre definido por cada banco

        // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
        // do campo livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 0, 4);
        $p2 = substr($codigo, 19, 5);
        $p3 = _modulo10("$p1$p2", $r);
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 24, 10);
        $p2 = _modulo10($p1, $r);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        // 3. Campo composto pelas posicoes 16 a 25 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 34, 10);
        $p2 = _modulo10($p1, $r);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo3 = "$p4.$p5";

        // 4. Campo - digito verificador do codigo de barras
        $campo4 = substr($codigo, 4, 1);

        // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
        // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
        // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
        $campo5 = substr($codigo, 5, 14);

        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }

	function DuploDV_Banrisul($dvcampo)
	{
        // calculando o primeiro DV do nosso número
		//$dvcampo = $nnum;
		$dvnnum1 = _modulo10($dvcampo, 1);

        // calculando o segundo DV do nosso número
		$dvcampo2 = "$dvcampo$dvnnum1";
		for ($resto = _modulo11($dvcampo2,7,2);$resto == 1;){
			if ($dvnnum1 == 9){
				$dvnnum1 = 0;
			} else {
				$dvnnum1 = $dvnnum1 + 1;
			}
			$dvcampo1 = "$dvcampo$dvnnum1";
			$resto = _modulo11($dvcampo1,7,2);
		}
		if ($resto == 0){
			$dvnnum2 = $resto;
		} else {
			$dvnnum2 = 11 - $resto;
		}
		$dd = "$dvnnum1$dvnnum2";
	//	$dvcampo = "$dvcampo$dd";
	return $dd;
	}

    function _modulo10($num, $r=0)
    {
        /*
            Autor:
                    Pablo Costa <pablo@users.sourceforge.net>
            Função:
                    Calculo do Modulo 10 para geracao do digito verificador 
                    de boletos bancarios conforme documentos obtidos 
                    da Febraban - www.febraban.org.br 

            Entrada:
                    $num: string numérica para a qual se deseja calcularo digito verificador;

                    $r: 0 - normal
                        1 - faz o cáculo de acordo como regulamento do banrisul, real 

            Saída:
                    Retorna o Digito verificador.

            Observações:
                    - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
                    - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
        */                                        

        $numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {

            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);

            // Efetua multiplicacao do numero pelo fator de multiplicação
		if ($r == 0){
            		$parcial10[$i] = $numeros[$i] * $fator;
		} elseif ($r == 1){
			$parcial10[$i] = $numeros[$i] * $fator;
			if ($parcial10[$i] > 9){
				$parcial10[$i] = $parcial10[$i] - 9;
			}
		}

            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 .= $parcial10[$i];

	   // intercala o fator de multiplicacao entre 2 e 1 (modulo 10)
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2;
            }
        }

        $soma = 0;

        // Calculo do modulo 10
        for ($i = strlen($numtotal10); $i > 0; $i--) {
            $numeros[$i] = substr($numtotal10,$i-1,1);
            $soma += $numeros[$i];				
        }

        $resto = $soma % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;
    }

	function _modulo11($num, $base=9, $r=0)
    {
        /**
         *   Autor:
         *           Pablo Costa <pablo@users.sourceforge.net>
	 *
	 *   Modificado por:
	 *	Fernando Soares - Janeiro/2007 - http://www.fernandosoares.com.br
         *
         *   Função:
         *    Calculo do Modulo 11 para geracao do digito verificador 
         *    de boletos bancarios conforme documentos obtidos 
         *    da Febraban - www.febraban.org.br 
         *
         *   Entrada:
         *     $num: string numérica para a qual se deseja calcularo digito verificador;
         *     $base: valor máximo de multiplicacao [2 até $base]
         *     $r: 0 - normal
         *         1 - devolve somente o resto >> normalmente utilizado no cálculo do digito verificador do número do banco
         *         2 - devolve a soma se esta for menor que 11 (banespa santander)
         *         3 - devolve o resto do cálculo de módulo 11 (multiplicada a soma por 10) - para unibanco
         *
         *   Saída:
         *     Retorna o Digito Controlador.
         *
         *   Observações:
         *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
         *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
         */                                        

        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {

            $numeros[$i] = substr($num,$i-1,1);		// pega cada numero isoladamente

            $parcial[$i] = $numeros[$i] * $fator;	// Efetua multiplicação do número pelo falor

            $soma += $parcial[$i];	// Soma dos resultados parciais

            if ($fator == $base) {	// faz com que o fator de multiplicação
                $fator = 1;		// seja restaurado para 2
            }				// no próximo cálculo

            $fator++;	//acresce 1 ao fator de multiplicação
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
		if (in_array((int)$digito,array(0,1,10,11))) {
            $digito = 1;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        } elseif ($r == 2){
		if ($soma < 11){
			return $soma;
		}
		$resto = $soma % 11;
		return $resto;
        } elseif ($r == 3){
            $soma *= 10;
            $resto = $soma % 11;
            return $resto;
        }
    }
	####################################################
?>