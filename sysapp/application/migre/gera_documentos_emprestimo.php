<?php
	//
	// Define quais documentos serão impressos, e o tipo de impressão 
	//
   if ( ( (strlen($e) < 1) or (strlen($r) < 1) or (strlen($s)< 1) ) and (strlen($cd_contrato) < 1) ) 
   {
      imprime_docs_vazios($imp_proposta, $tipo, $imp_demonstrativo, $imp_np, $imp_autorizacao, $callcenter, $assinaturas);
   }
   else
   {
      if ($cd_contrato <> '')
      {
         imprime_docs_contrato(0,0,0,$cd_contrato, $tipo, $imp_proposta, $imp_demonstrativo, $imp_np, $imp_autorizacao, $destino, $callcenter,$assinaturas);
      }
      else
      {
		imprime_docs_contrato($e, $r, $s, 'NULL', $tipo, $imp_proposta, $imp_demonstrativo, $imp_np, $imp_autorizacao, $destino, $callcenter,$assinaturas);
      }
   }

   // -----------------------
   // Impressão do contrato
   // -----------------------
   function imprime_docs_contrato($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_contrato, $tipo, 
                                  $imp_proposta, $imp_demonstrativo, $imp_np, $imp_autorizacao, $destino, $callcenter, $assinaturas) {

      global $LISTNER_IP;
      global $LISTNER_PORTA;

      	if (
           ( ($cd_contrato == 'NULL') or (empty($cd_contrato)) ) and
           ( (empty($cd_empresa) and empty($cd_registro_empregado) and empty($seq_dependencia) ) )
         ) 
		{
			imprime_docs_vazios($imp_proposta, $tipo, $imp_demonstrativo, $imp_np, $imp_autorizacao, $callcenter);
      	}
      	else
      	{

			if ( (! empty($cd_contrato)) and ($cd_contrato != 'NULL')  )
			{
				$send = "fnc_gera_proposta;$cd_contrato"; // Obtem dados
			} 
			else 
			{       
				
				if ( ( $cd_empresa != '') and ($cd_registro_empregado!='') and ($seq_dependencia!='') ) 
				{
					$send = "fnc_serialize_table;participantes;cd_empresa=$cd_empresa and cd_registro_empregado=$cd_registro_empregado and seq_dependencia=$seq_dependencia";
				}
			}
			

			$skt = new Socket();
			$skt->SetRemoteHost($LISTNER_IP);
			$skt->SetRemotePort($LISTNER_PORTA);
			$skt->SetBufferLength(262144); // 256KB
			$skt->SetConnectTimeOut(1);

         if ($skt->Connect()) 
         {
			
            $ret  = $skt->Ask($send);
            
            if ($skt->Error())
            { // echo "Ocorreu um erro de conexï¿½o com o webservice";
               $tpl = new TemplatePower('tpl/xmlformerror.txt');
               $tpl->prepare();
               $tpl->assign('fnc', $fnc);
               $tpl->assign('err', $skt->GetErrStr());
            }
            else
            {
               $dom = new DOMDocument();
               $dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret);
               $campos = $dom->getElementsByTagName("fld");

               $erro = getFieldValueXML($campos, 'ERR');
               if ($erro == "NULL") {
                  if ($destino == 'S') {
                     $tpl = new TemplatePower('tpl/tpl_proposta_emprestimo_sedex.html');
                  } else {
                     $tpl = new TemplatePower('tpl/tpl_proposta_emprestimo.html');
                  }
                  $tpl->prepare();
                  
                  // -------------------------------
                  // Impressão da Proposta
                  // -------------------------------
                  if (strlen($imp_proposta) > 0) {
                  
                     $sProposta = geraProposta($campos, $cd_contrato, $tipo, $destino, $imp_proposta, 'S', $assinaturas);
                     
                     $tpl->newBlock('blk_proposta');
                     $tpl->assign('proposta', $sProposta);
                     if ( ($imp_demonstrativo <> 'S') and ($imp_np <> 'S') and ($imp_autorizacao <> 'S')) {
                        $sProposta = geraProposta($campos, $cd_contrato, $tipo, $destino, $imp_proposta, 'N', $assinaturas);
                     }
                     $tpl->newBlock('blk_proposta');
                     $tpl->assign('proposta', $sProposta);
                  }

				
                  // -------------------------------
                  // Impressão da Nota Promissória
                  // -------------------------------
                  if ($imp_np == 'S') {
                  
                     $tpl->newBlock('blk_np_autorizacao');
                     if ($destino == 'S') {
                        $tpl->newBlock('blk_np_valores_sedex');
                     } else {
                        $tpl->newBlock('blk_nota_promissoria');
                     }

                     if ( ($cd_contrato == '') or ($cd_contrato == 'NULL') ) {
                        // ECA!!! Repetição de código! Fazer o que ? É a pressa...
                        $tpl->assign('cd_empresa', '&nbsp;');
                        $tpl->assign('cd_registro_empregado', '&nbsp;');
                        $tpl->assign('seq_dependencia', '&nbsp;');
                        $tpl->assign('nome', '&nbsp;');
                        $tpl->assign('ddd', '&nbsp;');
                        $tpl->assign('telefone', '&nbsp;');
                        $tpl->assign('ramal', '&nbsp;');
                        $tpl->assign('logradouro', '&nbsp;');
                        $tpl->assign('bairro', '&nbsp;');
                        $tpl->assign('cidade', '&nbsp;');
                        $tpl->assign('cep', '&nbsp;');
                        $tpl->assign('cpf_mf', '&nbsp;');
                        $tpl->assign('complemento_cep', '&nbsp;');
                        $tpl->assign('unidade_federativa', '&nbsp;');
                  
                        $tpl->assign('vlr_solicitado', '&nbsp;');
                        $tpl->assign('dt_solicitacao', '&nbsp;');
                        $tpl->assign('dt_deposito', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('perc_tx_juros_ano', '&nbsp;');
                        $tpl->assign('perc_tx_preservacao_patrimonia', '&nbsp;');
                        $tpl->assign('perc_tx_adm', '&nbsp;');
                        $tpl->assign('montante_concedido', '&nbsp;');
                        $tpl->assign('vlr_adm', '&nbsp;');
                        $tpl->assign('vlr_cm_juros', '&nbsp;');
                        $tpl->assign('vlr_iof', '&nbsp;');
                        $tpl->assign('reforma', '&nbsp;');
                        $tpl->assign('vlr_deposito', '&nbsp;');
                        $tpl->assign('nro_prestacoes', '&nbsp;');
                        $tpl->assign('vlr_prestacao', '&nbsp;');
                        $tpl->assign('perc_tx_seguro', '&nbsp;');
                        $tpl->assign('vlr_debitos_descontados', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('vlr_pago_emp_anterior', '&nbsp;');
                        $tpl->assign('prest_atrasadas', '&nbsp;');
                        $tpl->assign('prefixada', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('posfixada', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_normal', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_renegociacao', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_ferias', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_refinanciamento', '&nbsp;&nbsp;&nbsp;');
                     }

                     foreach($campos as $campo) {
					 	$campo->nodeValue = utf8_decode($campo->nodeValue);
                        switch ($campo->getAttribute('id')) {
                           // Formatação diferenciada, em função do hifen                                             
                           case 'DDD'            : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
																	$auxDDD = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                                   break;
                           case 'TELEFONE'       : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
																	$auxTelefone = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                                   break;
                           case 'LOGRADOURO'     : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
																	$auxLogradouro = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                                   break;
                           case 'BAIRRO'         : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
																	$auxBairro = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                                   break;
                           //
                           default               : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
                           
                        }
                     }
                     
                     if ( ($auxDDD != '') and ($auxTelefone != '') ) {
                        $ddd_telefone = $auxDDD.($auxDDD != '&nbsp;' ? ' - ' : '').$auxTelefone;
                     }
                     if ( ($auxLogradouro != '') and ($auxBairro != '') ) {
                        $logradouro_bairro = $auxLogradouro.($auxLogradouro != '&nbsp;' ? ' - ' : '').$auxBairro;
                     }
                     
                     $tpl->assign('ddd_telefone', $ddd_telefone);
                     $tpl->assign('logradouro_bairro', $logradouro_bairro);
                     
                     // Impressão da Autorização
                     $imp_autorizacao = (getFieldValueXML($campos, 'CD_INSTITUICAO') == '41' ? 'S' : 'N'); // Imprime automaticamente se for do Banrisul
      
                     if ( ($imp_autorizacao == 'S') and ($imp_np == 'S') ) {
                        $tpl->newBlock('blk_autorizacao');
                        foreach($campos as $campo) {
                           $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
                        }
                        $tpl->assign('ddd_telefone', $ddd_telefone);
                        $tpl->assign('logradouro_bairro', $logradouro_bairro);
                     }
                  }
                  
                  // ----------------------------
                  // Impressão do Demonstrativo
                  // ----------------------------
                  if ($imp_demonstrativo == 'S') {
                  
                     if ($imp_np == 'S') {
                        $tpl->assign('style', 'style="page-break-after:always;"');
                     }
                     
                     $tpl->newBlock('blk_demontrativo');

                     if ( ($cd_contrato == '') or ($cd_contrato == 'NULL') ) {
                        // ECA!!! Repetição de código! Fazer o que ? É a pressa...
                        $tpl->assign('cd_empresa', '&nbsp;');
                        $tpl->assign('cd_registro_empregado', '&nbsp;');
                        $tpl->assign('seq_dependencia', '&nbsp;');
                        $tpl->assign('nome', '&nbsp;');
                        $tpl->assign('ddd', '&nbsp;');
                        $tpl->assign('telefone', '&nbsp;');
                        $tpl->assign('ramal', '&nbsp;');
                        $tpl->assign('logradouro', '&nbsp;');
                        $tpl->assign('bairro', '&nbsp;');
                        $tpl->assign('cidade', '&nbsp;');
                        $tpl->assign('cep', '&nbsp;');
                        $tpl->assign('cpf_mf', '&nbsp;');
                        $tpl->assign('complemento_cep', '&nbsp;');
                        $tpl->assign('unidade_federativa', '&nbsp;');
                  
                        $tpl->assign('vlr_solicitado', '&nbsp;');
                        $tpl->assign('dt_solicitacao', '&nbsp;');
                        $tpl->assign('dt_deposito', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('perc_tx_juros_ano', '&nbsp;');
                        $tpl->assign('perc_tx_preservacao_patrimonia', '&nbsp;');
                        $tpl->assign('perc_tx_adm', '&nbsp;');
                        $tpl->assign('montante_concedido', '&nbsp;');
                        $tpl->assign('vlr_adm', '&nbsp;');
                        $tpl->assign('vlr_cm_juros', '&nbsp;');
                        $tpl->assign('vlr_iof', '&nbsp;');
                        $tpl->assign('reforma', '&nbsp;');
                        $tpl->assign('vlr_deposito', '&nbsp;');
                        $tpl->assign('nro_prestacoes', '&nbsp;');
                        $tpl->assign('vlr_prestacao', '&nbsp;');
                        $tpl->assign('perc_tx_seguro', '&nbsp;');
                        $tpl->assign('vlr_debitos_descontados', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('vlr_pago_emp_anterior', '&nbsp;');
                        $tpl->assign('prest_atrasadas', '&nbsp;');
                        $tpl->assign('prefixada', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('posfixada', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_normal', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_renegociacao', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_ferias', '&nbsp;&nbsp;&nbsp;');
                        $tpl->assign('mod_refinanciamento', '&nbsp;&nbsp;&nbsp;');
                     }

                     foreach($campos as $campo) {
					 $campo->nodeValue = utf8_decode($campo->nodeValue);
                        $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? $campo->nodeValue : '&nbsp;'));
                        if ($campo->getAttribute('id') == 'ORIGEM_CONTRATO') {
                           if ($campo->nodeValue == 'C') {
                              $callcenter = 'S';
                           }
                        }
                     }
                     if ($callcenter == 'S') {
                     	$tpl->newBlock('dem_call_center');
                     }
                  }
                  
                  $tpl->printToScreen();
                  

               } else {
                  echo " Ocorreu um erro: ".$erro;
               }
            }
         } else {
            $tpl = new TemplatePower('tpl/xmlformerror.txt');
            $tpl->prepare();
            $tpl->assign('fnc', $fnc);
            $tpl->assign('err', $skt->GetErrStr());
            $tpl->printToScreen();
         }
      }
   }


   function imprime_docs_vazios($imp_proposta, $tipo, $imp_demonstrativo, $imp_np, $imp_autorizacao, $callcenter, $assinaturas) 
   {
		$tpl = new TemplatePower('tpl/tpl_proposta_emprestimo.html');
		$tpl->prepare();


		if (strlen($imp_proposta) > 0)
		{
			if ( ($imp_np <> 'S') and ($imp_autorizacao <> 'S')) 
			{
				$sProposta = geraProposta(null, null, $tipo, 'N', $imp_proposta, 'N', 'N');
			}
			else 
			{
				$sProposta = geraProposta(null, null, $tipo, 'N', $imp_proposta, 'S', 'N');
			}
	
			$tpl->newBlock('blk_proposta');
			$tpl->assign('proposta', $sProposta);
		}

      // Impressão da Nota Promissória
      if ($imp_np == 'S')
      {
         $tpl->newBlock('blk_np_autorizacao');
         $tpl->newBlock('blk_nota_promissoria');
         $tpl->assign('nome', '&nbsp;');
         $tpl->assign('ddd', '&nbsp;');
         $tpl->assign('telefone', '&nbsp;');
         $tpl->assign('ramal', '&nbsp;');
         $tpl->assign('logradouro', '&nbsp;');
         $tpl->assign('bairro', '&nbsp;');
         $tpl->assign('cidade', '&nbsp;');
         $tpl->assign('cep', '&nbsp;');
         $tpl->assign('complemento_cep', '&nbsp;');
         $tpl->assign('unidade_federativa', '&nbsp;');
         $tpl->assign('cpf_mf', '&nbsp;');
      }
      if ($imp_autorizacao == 'S') {
         $tpl->newBlock('blk_autorizacao');
      }
      
      $tpl->printToScreen();
   }


   // ---------------------------------------------------------------------------
   // Gera a proposta e retorna como um string para ser incluiído dinamicamente 
   // ---------------------------------------------------------------------------
   function geraProposta($campos, $cd_contrato, $tipo='R', $destino, $paginas, $pgBreak, $assinaturas) {
      $aCadastro = array( 'CD_EMPRESA'
                        , 'CD_REGISTRO_EMPREGADO'
                        , 'SEQ_DEPENDENCIA'
                        , 'NOME'
                        , 'DDD'
                        , 'TELEFONE'
                        , 'RAMAL'
                        , 'LOGRADOURO'
                        , 'BAIRRO'
                        , 'CIDADE'
//                        , 'DT_SOLICITACAO' Comentado em 23/10/2006 por Cleisson						
						);
	    global $db;
		$dt_deposito_emprestimo = "TO_DATE('".getProximoDiaUtil(getProximoDiaUtil(date("d/m/Y")))."','DD/MM/YYYY')";
      if ($destino == 'S') {
         $tpl = new TemplatePower('tpl/tpl_proposta_sedex.html');
      } else {
         $tpl = new TemplatePower('tpl/tpl_proposta.html');
		 
         

      }

      $tpl->prepare();

      if (substr_count($paginas, '1') > 0) {
         $tpl->newBlock('blk_pg1');
         $tpl->assignGlobal('cd_empresa', '&nbsp;');
         $tpl->assignGlobal('cd_registro_empregado', '&nbsp;');
         $tpl->assignGlobal('seq_dependencia', '&nbsp;');
         $tpl->assignGlobal('nome', '&nbsp;');
         $tpl->assignGlobal('ddd_telefone', '&nbsp;');
         $tpl->assignGlobal('ramal', '&nbsp;');
         $tpl->assignGlobal('logradouro_bairro', '&nbsp;');
         $tpl->assignGlobal('cidade', '&nbsp;');
         $tpl->assignGlobal('cep', '&nbsp;');
         $tpl->assignGlobal('cpf_mf', '&nbsp;');
         $tpl->assignGlobal('complemento_cep', '&nbsp;');
         $tpl->assignGlobal('unidade_federativa', '&nbsp;');
   
         $tpl->assignGlobal('vlr_solicitado', '&nbsp;');
         $tpl->assignGlobal('dt_solicitacao', '&nbsp;');
         $tpl->assignGlobal('dt_deposito', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('perc_tx_juros_ano', '&nbsp;');
         $tpl->assignGlobal('perc_tx_preservacao_patrimonia', '&nbsp;');
         $tpl->assignGlobal('perc_tx_adm', '&nbsp;');
         $tpl->assignGlobal('montante_concedido', '&nbsp;');
         $tpl->assignGlobal('vlr_adm', '&nbsp;');
         $tpl->assignGlobal('vlr_cm_juros', '&nbsp;');
         $tpl->assignGlobal('vlr_iof', '&nbsp;');
         $tpl->assignGlobal('reforma', '&nbsp;');
         $tpl->assignGlobal('vlr_deposito', '&nbsp;');
         $tpl->assignGlobal('nro_prestacoes', '&nbsp;');
         $tpl->assignGlobal('vlr_prestacao', '&nbsp;');
         $tpl->assignGlobal('perc_tx_seguro', '&nbsp;');
         $tpl->assignGlobal('vlr_debitos_descontados', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('vlr_pago_emp_anterior', '&nbsp;');
         $tpl->assignGlobal('prest_atrasadas', '&nbsp;');
         $tpl->assignGlobal('prefixada', '&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('posfixada', '&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('mod_normal', '&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('mod_renegociacao', '&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('mod_ferias', '&nbsp;&nbsp;&nbsp;');
         $tpl->assignGlobal('mod_refinanciamento', '&nbsp;&nbsp;&nbsp;');
         
		 
         // Imprime proposta preenchida
         if (isset($campos)) {
            $auxDDD = '';
            $auxTelefone = '';
            $auxLogradouro = '';
            $auxBairro = '';
			
            foreach($campos as $campo) {
				if(strtolower($campo->getAttribute('id')) == "dt_deposito")
				{
					//$dt_deposito_emprestimo = $campo->nodeValue;
					$dt_deposito_emprestimo = "TO_DATE('".$campo->nodeValue."','DD/MM/YYYY')";
				}
			
               if ( ($tipo != 'V') or (($tipo == 'V') and (! in_array($campo->getAttribute('id'), $aCadastro))) ) {
                  switch($campo->getAttribute('id')) {
                     case 'FORMA_CALCULO'  : if ($campo->nodeValue == 'P') { 
                                               $tpl->assign('prefixada', ' X ');
                                               $tpl->assign('posfixada', '&nbsp;&nbsp;&nbsp;');
                                             } else {
                                                $tpl->assign('posfixada', '&nbsp;&nbsp;&nbsp;');
                                                $tpl->assign('prefixada', '&nbsp;&nbsp;&nbsp;');
                                             }
                                             break;
                     case 'TIPO'           : if ($campo->nodeValue == 'F') { 
                                                $tpl->assign('mod_ferias', ' X ');
                                                $tpl->assign('mod_normal', '&nbsp;&nbsp;&nbsp;');
                                             } else {
                                                $tpl->assign('mod_normal', ' X ');
                                                $tpl->assign('mod_ferias', '&nbsp;&nbsp;&nbsp;');
                                             }
                                             break;
                     case 'NUM_REFINAM'    : $tpl->assign('mod_refinanciamento', ($campo->nodeValue == 'Sim' ? ' X ' : '&nbsp;&nbsp;&nbsp;'));
                                             break;
                     case 'RENEGOCIACAO'   : $tpl->assign('mod_renegociacao', ($campo->nodeValue == 'Sim' ? ' X ' : '&nbsp;&nbsp;&nbsp;'));
                                             break;
                     // Formatação diferenciada, em função do hifen                                             
                     case 'DDD'            : $auxDDD = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                             break;
                     case 'TELEFONE'       : $auxTelefone = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                             break;
                     case 'LOGRADOURO'     : $auxLogradouro = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                             break;
                     case 'BAIRRO'         : $auxBairro = ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;');
                                             break;
                     //
                     default               : $tpl->assign(strtolower($campo->getAttribute('id')), ($campo->nodeValue != '' ? ($campo->nodeValue == 'NULL' ? '&nbsp;' : $campo->nodeValue) : '&nbsp;'));
                                             break;
                  }
                  if ( ($auxDDD != '') and ($auxTelefone != '') ) {
                     $tpl->assign('ddd_telefone', $auxDDD.($auxDDD != '&nbsp;' ? ' - ' : '').$auxTelefone);
                  }
                  if ( ($auxLogradouro != '') and ($auxBairro != '') ) {
                     $tpl->assign('logradouro_bairro', $auxLogradouro.($auxLogradouro != '&nbsp;' ? ' - ' : '').$auxBairro);
                  }
               }
            }
         }
      }

      if (substr_count($paginas, '2') > 0) {
         $tpl->assign('style', 'style="page-break-after:always;"');
         $tpl->newBlock('blk_pg2');
      }
      if (substr_count($paginas, '3') > 0) {
         $tpl->assign('style', 'style="page-break-after:always;"');
         $tpl->newBlock('blk_pg3');
		 
		 
			$qr_sql = "
					SELECT idade_limite,
					       taxa_seguro
					  FROM public.seguros
					 WHERE dt_inicio_validade = (SELECT MAX(DISTINCT(dt_inicio_validade))
					                               FROM public.seguros
					                              WHERE dt_inicio_validade <= ".$dt_deposito_emprestimo.")			
			       ";
			$ob_resul = pg_query($db,$qr_sql);
			$nr_total = pg_num_rows($ob_resul);
			$nr_conta = 0;
			$nr_idade_atual = "";
			while ($ar_reg = pg_fetch_array($ob_resul)) 
			{	
				$tpl->newBlock('lst_taxa_administracao');		 
				if($nr_idade_atual == "")
				{
					$tpl->assign('ds_faixa_ini', "Até ");		 
					$tpl->assign('ds_faixa_fim', $ar_reg['idade_limite']." anos");
					$nr_idade_atual = $ar_reg['idade_limite'];
				}
				else if($nr_conta < ($nr_total-1))
				{
					$tpl->assign('ds_faixa_ini', "De ".($nr_idade_atual + 1));		 
					$tpl->assign('ds_faixa_fim', " a ".$ar_reg['idade_limite']." anos");
					$nr_idade_atual = $ar_reg['idade_limite'];					
				}
				else
				{
					$tpl->assign('ds_faixa_ini', "Acima");		 
					$tpl->assign('ds_faixa_fim', " a ".$nr_idade_atual." anos");
				}
				$tpl->assign('nr_taxa', $ar_reg['taxa_seguro']);
				$nr_conta++;
			}
      }

      if (substr_count($paginas,'4') > 0) {
         $tpl->assign('style', 'style="page-break-after:always;"');
         $tpl->newBlock('blk_pg4');
         if ($pgBreak == 'S') {
            $tpl->assign('style', 'style="page-break-after:always;"');
         }
//         $tpl->assign('assVisivel', ($tipo == 'N' ? 'visibility: hidden' : 'visibility: show;'));
         $tpl->assign('assVisivel', ($assinaturas == 'N' ? 'visibility: hidden' : 'visibility: show;'));
      }

      return $tpl->getOutputContent();
   } 

   //----------------------
   //--- Outras funções ---
   //----------------------
   
   function callListener($call, $erro) {
      $skt = new Socket();
      $send = "fnc_serialize_table;participantes;cd_empresa=$emp and cd_registro_empregado=$re and seq_dependencia=$seq";
      global $LISTNER_IP;
      global $LISTNER_PORTA;
      $skt->SetRemoteHost($LISTNER_IP);
      $skt->SetRemotePort($LISTNER_PORTA);
      $skt->SetBufferLength(262144); // 256KB
      $skt->SetConnectTimeOut(10);
      $erro = "";
      if ($skt->Connect()) {
         $ret = $skt->Ask($send);
         if ($skt->Error()) { // echo "Ocorreu um erro de conexï¿½o com o webservice";
            $tpl = new TemplatePower('tpl/xmlformerror.txt');
            $tpl->prepare();
            $tpl->assign('fnc', $fnc);
            $tpl->assign('err', $skt->GetErrStr());
            $erro = $tpl->getOutputContent();
            return '';
         } else {
            return $ret;
         }
      } else {
         $tpl = new TemplatePower('tpl/xmlformerror.txt');
         $tpl->prepare();
         $tpl->assign('fnc', $fnc);
         $tpl->assign('err', $skt->GetErrStr());
         $erro = $tpl->getOutputContent();
         return '';
      }
   }
   
   function getFieldValueXML($campos, $fldId) {
      $pos = -1;
      $i = 0;
      foreach ($campos as $cmp) {
         if ($cmp->getAttribute('id') == $fldId) {
            $pos = $i;
			$campoSelecionado = $cmp;
            break;
         }
		 $i++;
      }
      if ($pos > -1) {
         return $campoSelecionado->nodeValue;
      } else {
         return 'undefined';
      }
   }
   
    
	function getProximoDiaUtil($dt_data)
	{
		global $LISTNER_IP;
		global $LISTNER_PORTA;         
		$skt = new Socket();
        $skt->SetRemoteHost($LISTNER_IP);
        $skt->SetRemotePort($LISTNER_PORTA);
		
        if ($skt->Connect()) 
		{
			$xml_retorno = $skt->Ask("fnc_prox_dia_util;".$dt_data.";EMP");
			
			$dom = new DOMDocument("1.0", "utf-8");
			$dom->loadXML(utf8_encode($xml_retorno)); 
			$campos = $dom->getElementsByTagName("fld");

			if(getFieldValueXML($campos, 'ERR') != "NULL")
			{
				return getFieldValueXML($campos, 'ERR');
			}
			else
			{
				return getFieldValueXML($campos, 'dt_dia');			
			}
		}
	}
?>
