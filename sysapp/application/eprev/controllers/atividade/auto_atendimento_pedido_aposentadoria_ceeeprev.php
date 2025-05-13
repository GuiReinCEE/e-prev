<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

	$ds_arq   = "tpl/tpl_auto_atendimento_pedido_aposentadoria_ceeeprev.html";

	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));

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
			   'PEDIDO_APOSENTADORIA_CEEEPREV'
			 );";
	@pg_query($db,$qr_sql);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_pedido_aposentadoria_ceeeprev");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode($_RETORNO, TRUE);
	if (!(json_last_error() === JSON_ERROR_NONE))
	{
		switch (json_last_error()) 
		{
			case JSON_ERROR_NONE:
				$FL_RETORNO = TRUE;
			break;
				default:
				$FL_RETORNO = FALSE;
			break;
		}
	}

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			$pedido_aposentadoria = $_RETORNO['result']['pedido_aposentadoria'];

			if(trim($pedido_aposentadoria['fl_carencia']) == 'S' AND (trim($pedido_aposentadoria['dt_encaminhamento']) == '' OR trim($pedido_aposentadoria['dt_indeferido']) != ''))
			{
				$conteudo = str_replace('{FL_ADICIONAR_DEPENDENTE}', $_GET['fl_adicionar_dependente'], $conteudo);
				$conteudo = str_replace('{FL_ADICIONAR_DEPENDENTE_PREV}', $_GET['fl_adicionar_dependente_prev'], $conteudo);

				if(trim($_GET['fl_adicionar_dependente']) == 'S')
				{
					$conteudo = str_replace('{FORM_DEPENDENTE_IR}', '', $conteudo);
					$conteudo = str_replace('{CAD_DEPENDENTE_IR}', 'style="display:none;"', $conteudo);
				}
				else
				{
					$conteudo = str_replace('{FORM_DEPENDENTE_IR}', 'style="display:none;"', $conteudo);
					$conteudo = str_replace('{CAD_DEPENDENTE_IR}', '', $conteudo);
				}

				if(trim($_GET['fl_adicionar_dependente_prev']) == 'S')
				{
					$conteudo = str_replace('{FORM_DEPENDENTE_PREV}', '', $conteudo);
					$conteudo = str_replace('{CAD_DEPENDENTE_PREV}', 'style="display:none;"', $conteudo);
				}
				else
				{
					$conteudo = str_replace('{FORM_DEPENDENTE_PREV}', 'style="display:none;"', $conteudo);
					$conteudo = str_replace('{CAD_DEPENDENTE_PREV}', '', $conteudo);
				}

				$conteudo = str_replace('{DIV_FORMULARIO}', '', $conteudo);

				$conteudo = str_replace('{CD_PEDIDO_APOSENTADORIA_CEEEPREV}', $pedido_aposentadoria['cd_pedido_aposentadoria_ceeeprev'], $conteudo);
				$conteudo = str_replace('{DS_NOME}', $pedido_aposentadoria['ds_nome'], $conteudo);
				$conteudo = str_replace('{DT_NASCIMENTO}', $pedido_aposentadoria['dt_nascimento'], $conteudo);
				$conteudo = str_replace('{DS_CPF}', $pedido_aposentadoria['ds_cpf'], $conteudo);
				$conteudo = str_replace('{DS_NATURALIDADE}', $pedido_aposentadoria['ds_naturalidade'], $conteudo);
				$conteudo = str_replace('{DS_NACIONALIDADE}', $pedido_aposentadoria['ds_nacionalidade'], $conteudo);
				$conteudo = str_replace('{DS_ENDERECO}', $pedido_aposentadoria['ds_endereco'], $conteudo);
				$conteudo = str_replace('{NR_ENDERECO}', $pedido_aposentadoria['nr_endereco'], $conteudo);
				$conteudo = str_replace('{DS_COMPLEMENTO_ENDERECO}', $pedido_aposentadoria['ds_complemento_endereco'], $conteudo);
				$conteudo = str_replace('{DS_BAIRRO}', $pedido_aposentadoria['ds_bairro'], $conteudo);
				$conteudo = str_replace('{DS_CIDADE}', $pedido_aposentadoria['ds_cidade'], $conteudo);
				$conteudo = str_replace('{DS_UF}', $pedido_aposentadoria['ds_uf'], $conteudo);
				$conteudo = str_replace('{DS_CEP}', $pedido_aposentadoria['ds_cep'], $conteudo);
				$conteudo = str_replace('{DS_TELEFONE1}', $pedido_aposentadoria['ds_telefone1'], $conteudo);
				$conteudo = str_replace('{DS_TELEFONE2}', $pedido_aposentadoria['ds_telefone2'], $conteudo);
				$conteudo = str_replace('{DS_CELULAR}', $pedido_aposentadoria['ds_celular'], $conteudo);
				$conteudo = str_replace('{DS_EMAIL1}', $pedido_aposentadoria['ds_email1'], $conteudo);
				$conteudo = str_replace('{DS_EMAIL2}', $pedido_aposentadoria['ds_email2'], $conteudo);
				$conteudo = str_replace('{DS_AGENCIA}', $pedido_aposentadoria['ds_agencia'], $conteudo);
				$conteudo = str_replace('{DS_CONTA}', $pedido_aposentadoria['ds_conta'], $conteudo);

				$estado_civil = '';
				$estado_civil_dep = '';

				foreach ($_RETORNO['result']['estado_civil'] as $key => $item) 
				{
					$selected = '';
					
					if($pedido_aposentadoria['ds_estado_civil'] == $item['descricao_estado_civil'])
					{
						$selected = 'selected=""';
					}
					
					$estado_civil .= '<option value="'.$item['descricao_estado_civil'].'" '.$selected.'>'.utf8_decode($item['descricao_estado_civil']).'</option>';
					$estado_civil_dep .= '<option value="'.$item['descricao_estado_civil'].'">'.utf8_decode($item['descricao_estado_civil']).'</option>';
				}

				$conteudo = str_replace('{ESTADO_CIVIL}', $estado_civil, $conteudo);
				$conteudo = str_replace('{ESTADO_CIVIL_DEPENDENTE}', $estado_civil_dep, $conteudo);

				$instituicao_financeira = '';

				foreach ($_RETORNO['result']['instituicao_financeira'] as $key => $item) 
				{
					$selected = '';
					
					if($pedido_aposentadoria['ds_banco'] == $item['razao_social_nome'])
					{
						$selected = 'selected=""';
					}
					
					$instituicao_financeira .= '<option value="'.$item['razao_social_nome'].'" '.$selected.'>'.utf8_decode($item['razao_social_nome']).'</option>';
				}

				$conteudo = str_replace('{INSTITUICAO_FINANCEIRA}', $instituicao_financeira, $conteudo);

				$tipo = '';

				foreach ($_RETORNO['result']['tipo'] as $key => $item) 
				{
					$selected = '';
					
					if($pedido_aposentadoria['tp_pedido_aposentadoria'] == $key)
					{
						$selected = 'selected=""';

						$tipo .= '<option value="'.$key.'" '.$selected.'>'.utf8_decode($item).'</option>';
					}
				}

				$conteudo = str_replace('{TIPO}', $tipo, $conteudo);

				$adiantamento_cip = '';

				foreach ($_RETORNO['result']['adiantamento_cip'] as $key => $item) 
				{
					$selected = '';

					if(intval($pedido_aposentadoria['nr_adiantamento_cip']) == intval($key))
					{
						$selected = 'selected=""';
					}

					$adiantamento_cip .= '<option value="'.$key.'" '.$selected.'>'.utf8_decode($item).'</option>';
				}

				$conteudo = str_replace('{ADIANTAMENTO_CIP}', $adiantamento_cip, $conteudo);

				if(trim($pedido_aposentadoria['adiantamento_cipb']) == 'N')
               	{
               		$conteudo = str_replace('{ADIANTAMENTO_CIP_BOX}', 'style="display:none;"', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{ADIANTAMENTO_CIP_BOX}', '', $conteudo);

               		if($pedido_aposentadoria['fl_adiantamento_cip'] == 'S')
					{
						$conteudo = str_replace('{ADIANTAMENTO_CIP_S}', 'selected=""', $conteudo);
						$conteudo = str_replace('{ADIANTAMENTO_CIP_N}', '', $conteudo);
					}
					else if($pedido_aposentadoria['fl_adiantamento_cip'] == 'N')
					{
						$conteudo = str_replace('{ADIANTAMENTO_CIP_S}', '', $conteudo);
						$conteudo = str_replace('{ADIANTAMENTO_CIP_N}', 'selected=""', $conteudo);
					}
					else
					{
						$conteudo = str_replace('{ADIANTAMENTO_CIP_S}', '', $conteudo);
						$conteudo = str_replace('{ADIANTAMENTO_CIP_N}', '', $conteudo);
					}
               	}

               	if(trim($pedido_aposentadoria['reversao_benef_pensao']) == 'N')
               	{
               		$conteudo = str_replace('{REVERSAO_BENEFICIO_BOX}', 'style="display:none;"', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{REVERSAO_BENEFICIO_BOX}', '', $conteudo);

					if($pedido_aposentadoria['fl_reversao_beneficio'] == 'S')
					{
						$conteudo = str_replace('{REVERSAO_BENEFICIO_S}', 'selected=""', $conteudo);
						$conteudo = str_replace('{REVERSAO_BENEFICIO_N}', '', $conteudo);
					}
					else if($pedido_aposentadoria['fl_reversao_beneficio'] == 'N')
					{
						$conteudo = str_replace('{REVERSAO_BENEFICIO_S}', '', $conteudo);
						$conteudo = str_replace('{REVERSAO_BENEFICIO_N}', 'selected=""', $conteudo);
					}
					else
					{
						$conteudo = str_replace('{REVERSAO_BENEFICIO_S}', '', $conteudo);
						$conteudo = str_replace('{REVERSAO_BENEFICIO_N}', '', $conteudo);
					}
				}

				if($pedido_aposentadoria['fl_politicamente_exposta'] == 'S')
				{
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_S}', 'selected=""', $conteudo);
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_N}', '', $conteudo);
				}
				else if($pedido_aposentadoria['fl_politicamente_exposta'] == 'N')
				{
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_S}', '', $conteudo);
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_N}', 'selected=""', $conteudo);
				}
				else
				{
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_S}', '', $conteudo);
					$conteudo = str_replace('{POLITICAMENTE_EXPOSTO_N}', '', $conteudo);
				}

				if($pedido_aposentadoria['fl_us_person'] == 'S')
				{
					$conteudo = str_replace('{US_PERSON_S}', 'selected=""', $conteudo);
					$conteudo = str_replace('{US_PERSON_N}', '', $conteudo);
				}
				else if($pedido_aposentadoria['fl_us_person'] == 'N')
				{
					$conteudo = str_replace('{US_PERSON_S}', '', $conteudo);
					$conteudo = str_replace('{US_PERSON_N}', 'selected=""', $conteudo);
				}
				else
				{
					$conteudo = str_replace('{US_PERSON_S}', '', $conteudo);
					$conteudo = str_replace('{US_PERSON_N}', '', $conteudo);
				}

				$grau_parentesco = '';

				foreach ($_RETORNO['result']['grau_parentesco'] as $key => $item) 
				{
					$grau_parentesco .= '<option value="'.$item['ds_grau_parentesco'].'">'.utf8_decode($item['ds_grau_parentesco']).'</option>';
				}

				$conteudo = str_replace('{GRAU_PARENTESCO}', $grau_parentesco, $conteudo);

				$grau_parentesco_ir = '';

				foreach ($_RETORNO['result']['grau_parentesco_ir'] as $key => $item) 
				{
					$grau_parentesco_ir .= '<option value="'.$item['ds_grau_parentesco'].'">'.utf8_decode($item['ds_grau_parentesco']).'</option>';
				}

				$conteudo = str_replace('{GRAU_PARENTESCO_IR}', $grau_parentesco_ir, $conteudo);

				$dependente = '';

				if(count($_RETORNO['result']['dependente']) > 0)
				{
					foreach ($_RETORNO['result']['dependente'] as $key => $item) 
					{
						$dependente .= '<tr>';
						$dependente .= '<td>'.utf8_decode($item['ds_nome']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['dt_nascimento']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['ds_sexo']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['ds_grau_parentesco']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['ds_estado_civil']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['fl_incapaz']).'</td>';
						$dependente .= '<td>'.utf8_decode($item['fl_estudante']).'</td>';
						//$dependente .= '<td><a href="javascript:void(0)" onclick="excluir_dependente('.$item['cd_pedido_aposentadoria_ceeeprev_dependente'].')">[excluir]</a></td>';
						$dependente_prev .= '<td></td>';
						$dependente .= '</tr>';
					}
				}
				else 
				{
					$dependente = '<tr><td colspan="11" style="text-align:center;"><b>Nenhum dependente cadastrado.</b></td></tr>';
				}
				
				$conteudo = str_replace('{LISTA_DEPENDENTE}', $dependente, $conteudo);

				$dependente_prev = '';

				if(count($_RETORNO['result']['dependente_previdenciario']) > 0)
				{
					$qt_opcao = 0;
					$dep_prev_input = '';

					foreach ($_RETORNO['result']['dependente_previdenciario'] as $key => $item) 
					{
						$dependente_prev .= '<tr>';
						$dependente_prev .= '<td>'.utf8_decode($item['ds_nome']).'</td>';
						$dependente_prev .= '<td>'.utf8_decode($item['dt_nascimento']).'</td>';
						$dependente_prev .= '<td>'.utf8_decode($item['ds_sexo']).'</td>';
						$dependente_prev .= '<td>'.utf8_decode($item['ds_grau_parentesco']).'</td>';
						$dependente_prev .= '<td>'.utf8_decode($item['ds_estado_civil']).'</td>';
						$dependente_prev .= '<td>'.utf8_decode($item['fl_incapaz']).'</td>';
						
						
						if(trim($item['re_cripto_dep']) != '')
						{
							$dep_prev_input .= '<input type="hidden" value="'.trim($item['fl_opcao']).'" id="dependente_opcao_'.$item['re_cripto_dep'].'" name="dependente['.$item['re_cripto_dep'].']" />';
						}

						if(trim($item['ds_opcao']) == '')
						{
							$qt_opcao++;
							/*
							$dependente_prev .= '
								<td>
									<a href="javascript:void(0)" onclick="manter_dependente(\''.$item['re_cripto_dep'].'\')">[manter]</a> 
									<a href="javascript:void(0)" onclick="excluir_dependente(\''.$item['re_cripto_dep'].'\')">[excluir]</a> 
								</td>';
								*/
							
						}
						
						$dependente_prev .= '
							<td>
								<select class="dependente" id="dependente_'.$item['re_cripto_dep'].'"  onchange="set_opcao(\''.$item['re_cripto_dep'].'\')">
							  		<option value="">MANTER ou EXCLUIR</option>
							  		<option value="M" '.(trim($item['fl_opcao']) == 'M' ? 'selected=""' : '').'>MANTER</option>
							  		<option value="E" '.(trim($item['fl_opcao']) == 'E' ? 'selected=""' : '').'>EXCLUIR</option>
								</select>
							</td>';

						if(trim($item['fl_opcao']) == 'I')
						{/*
							$dependente_prev .= '
								<td>
									<a href="javascript:void(0)" onclick="excluir_dependente_prev('.$item['cd_pedido_aposentadoria_ceeeprev_dependente_prev'].')">[excluir]</a>
								</td>';
								*/
							$dependente_prev .= '<td></td>';
						}
						else
						{
							$dependente_prev .= '<td></td>';
						
						}

						$dependente_prev .= '</tr>';
					}
				}
				else 
				{
					$dependente_prev = '<tr><td colspan="11" style="text-align:center;"><b>Nenhum dependente cadastrado.</b></td></tr>';
				}
				
				$conteudo = str_replace('{LISTA_DEPENDENTE_PREV}', $dependente_prev, $conteudo);
				$conteudo = str_replace('{DEP_PREV_INPUT}', $dep_prev_input, $conteudo);
				$conteudo = str_replace('{QT_OPCAO}',$qt_opcao, $conteudo);

               	if(trim($pedido_aposentadoria['arquivo_conta_bancaria']) != '')
               	{
               		$conteudo = str_replace('{COMPROVANTE_CONTA_CORRENTE}', 'display:none;', $conteudo);
               		$conteudo = str_replace('{COMPROVANTE_CONTA_CORRENTE_REMOVER}', '', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{COMPROVANTE_CONTA_CORRENTE}', '', $conteudo);
               		$conteudo = str_replace('{COMPROVANTE_CONTA_CORRENTE_REMOVER}', 'display:none;', $conteudo);
               	}

               	if(trim($pedido_aposentadoria['arquivo_doc_identidade']) != '')
               	{
               		$conteudo = str_replace('{DOCUMENTO_IDENTIDADE}', 'display:none;', $conteudo);
               		$conteudo = str_replace('{DOCUMENTO_IDENTIDADE_REMOVER}', '', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{DOCUMENTO_IDENTIDADE}', '', $conteudo);
               		$conteudo = str_replace('{DOCUMENTO_IDENTIDADE_REMOVER}', 'display:none;', $conteudo);
               	}

               	if(trim($pedido_aposentadoria['arquivo_doc_cpf']) != '')
               	{
               		$conteudo = str_replace('{DOCUMENTO_CPF}', 'display:none;', $conteudo);
               		$conteudo = str_replace('{DOCUMENTO_CPF_REMOVER}', '', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{DOCUMENTO_CPF}', '', $conteudo);
               		$conteudo = str_replace('{DOCUMENTO_CPF_REMOVER}', 'display:none;', $conteudo);
               	}

               	if(trim($pedido_aposentadoria['arquivo_recisao_contrato']) != '')
               	{
               		$conteudo = str_replace('{RESICAO_CONTRATO}', 'display:none;', $conteudo);
               		$conteudo = str_replace('{RESICAO_CONTRATO_REMOVER}', '', $conteudo);
               	}
               	else
               	{
               		$conteudo = str_replace('{RESICAO_CONTRATO}', '', $conteudo);
               		$conteudo = str_replace('{RESICAO_CONTRATO_REMOVER}', 'display:none;', $conteudo);
               	}
			}
			else
			{
				$conteudo = str_replace('{DIV_FORMULARIO}', 'style="display:none;"', $conteudo);
			}

			if(trim($pedido_aposentadoria['ds_mensagem']) != '')
			{
				$conteudo = str_replace('{DIV_MENSAGEM}', '', $conteudo);
				$conteudo = str_replace('{MENSAGEM}', utf8_decode($pedido_aposentadoria['ds_mensagem']), $conteudo);
				
				if(trim($pedido_aposentadoria['ds_motivo_indeferido']) != '')
				{
					$conteudo = str_replace('{MENSAGEM_INDEFERIDO}', utf8_decode($pedido_aposentadoria['ds_motivo_indeferido']), $conteudo);
				}
				else
				{
					$conteudo = str_replace('{MENSAGEM_INDEFERIDO}', '', $conteudo);
				}
			}
			else
			{
				$conteudo = str_replace('{DIV_MENSAGEM}', 'style="display:none;"', $conteudo);
				$conteudo = str_replace('{MENSAGEM}', '', $conteudo);
				$conteudo = str_replace('{MENSAGEM_INDEFERIDO}', '', $conteudo);
			}
		}
		else
		{
			#echo 'ERRO - [2]<br/>';
			#echo implode(' ', $_RETORNO['error']['mensagem']);
		}
	}
	else 
	{
		#echo 'ERRO [1]';
	}



	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();