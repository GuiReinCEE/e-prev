<?php
set_title('Empréstimo (Confirma) - EMP_002 ---- '.$row['cd_empresa']."/".$row['cd_registro_empregado']."/".$row['seq_dependencia']);
$this->load->view('header');
?>
<style type="text/css">
	.bordaTabela {
		border: thin solid #006633;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 16px;
		font-style: normal;
		line-height: normal;
		font-weight: normal;
		font-variant: normal;
		text-transform: none;
	}
	.bordaCallCenter {
		border: thin solid #990000;
	}
	
	.bordaInfoEmprestimo {
		border: thin solid gray;
	}
	
	.style1 {
		border: thin solid #006633;
		color: #FFFFFF;
		font-weight: bold;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	.style2 {
		font-size: 12px;
		font-weight: normal;
		font-family: Verdana, Arial, Helvetica, sans-serif;
	}
	.textboxFlat {
		border: 1px solid #CCCCCC;
		padding: 0px;
		margin: 0px;
	}
</style>
<script>
	function validaForm() 
	{
		var erro = '';

		if ($('#cd_instituicao').val() == '') { erro = erro + '   - O campo BANCO deve ser preenchido\n'; }
		if ($('#cd_agencia').val() == '')     { erro = erro + '   - O campo AGENCIA deve ser preenchido\n'; }
		if ($('#conta_folha').val() == '')    { erro = erro + '   - O campo CONTA deve ser preenchido\n'; }
		
		if (($('#tp_autenticar_atendente').is(':checked')) && ($('#usuario').val() == ''))      { erro = erro + '   - O campo USUARIO deve ser preenchido\n'; }

		if(($('#tp_autenticar_participante').is(':checked')) && ($('#senha_participante').val() == '')) { erro = erro + '   - O campo SENHA DO PARTICIPANTE deve ser preenchido\n'; }

		if(($('#tp_autenticar_atendente').is(':checked')) && ($('#senha').val() == '')){ erro = erro + '   - O campo SENHA deve ser preenchido\n'; }

		if($('#fl_dados_bancarios').is(':checked') == false){ erro = erro + '   - O campo Confirmar dados bancários deve ser marcado\n'; }

		if($('#fl_valor_credito').is(':checked') == false){ erro = erro + '   - O campo Valor do crédito deve ser marcado\n'; }

		if($('#fl_valor_prestacao').is(':checked') == false){ erro = erro + '   - O campo Valor da prestação deve ser marcado\n'; }

		if($('#fl_data_deposito').is(':checked') == false){ erro = erro + '   - O campo Informar data do depósito deve ser marcado\n'; }

		if($('#fl_data_primeiro_pagamento').is(':checked') == false){ erro = erro + '   - O campo Informar data do 1º pagamento deve ser marcado\n'; }

		if($('#fl_reforma_contrato').val() == 'S' && $('#fl_reforma').is(':checked') == false){ erro = erro + '   - O campo Informar se haverá devolução da prestação do empréstimo anterior e data da devolução deve ser marcado\n'; }

		if (erro != '') 
		{
			erro = 'Os seguintes erros foram encontratos: \n\n' + erro;
			erro = erro + '\n Corrija estas pendências e tente novamente';
			alert(erro);
			return false;
		} 
		else 
		{
			var confirmacao = 'Confirma a CONCESSÃO Empréstimo?\n\n'+
                              'VALOR: R$ '+ $('#vlr_deposito').val() + '\n'+
							  'PARCELA(S): '+ $('#numero_prestacoes').val() + '\n'+
							  'DEPÓSITO: '+ $('#dt_deposito').val() + '\n\n'+ 
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
			if(confirm(confirmacao))
			{			
				return true;
			}
			else
			{
				return false;
			}
		}
	}


	function getAgencia()
	{
		$('#tmpNomeBanco').val($('#cd_instituicao').val());
		
		var select = $('#cd_agencia');
		if(select.prop) 
		{
			var options = select.prop('options');
		}
		else 
		{
			var options = select.attr('options');
		}
		$('option', select).remove();
		options[options.length] = new Option("Carregando...","");
		
		$.post('<?php echo site_url('/ecrm/emprestimo_confirma/agencia'); ?>',
		{
			cd_instituicao : $('#cd_instituicao').val(),
			cd_agencia     : $('#cd_agencia_default').val()
		},
		function(data)
		{
			$('option', select).remove();
			var selecionado = "";
			$.each(data.list, function(i, val) 
			{
				options[options.length] = new Option(val.text, val.value);
				
				if(val.selected == "TRUE")
				{
					selecionado = val.value;
				}
			});	
			
			if(selecionado != "")
			{
				$('#cd_agencia').val(selecionado);		
			}
			
			alteraInfDeposito();
		},
		'json');		
	}

	function alteraInfDeposito() 
	{
		if ($('#origem').val() == 'C') 
		{
			$("#divTextoConta").html("- Na agência "+$('#cd_agencia').val()+", no banco "+$("#cd_instituicao option:selected").text()+", e o número da conta é "+$('#conta_folha').val());
		}
	}	
	
	function setAutenticar(tp_autenticar)
	{
		if((tp_autenticar == "P") && (!document.getElementById('tp_autenticar_participante').disabled))
		{
			$('#ob_autenticar_participante').show()
			$('#ob_autenticar_atendente').hide();
			$('#senha_participante').focus();
		}
		else
		{
			$('#ob_autenticar_participante').hide();
			$('#ob_autenticar_atendente').show();
			$('#senha').focus();
		}
	}	
	
	$(function(){
		<?php
			if($fl_mostrar_header == "N")
			{
				echo '$(".header_fundo").hide();';
			}
		?>
	});
</script>
<BR>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="1" class="bordaTabela">
	<tr>
		<td colspan="3" bgcolor="#006633">
			<div align="center">
				<strong>
					<font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">CONFIRME A CONCESSÃO DO EMPRÉSTIMO</font>
				</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr valign="top">
				<td>
					<table width="440" border="0" cellpadding="0" cellspacing="1">
					<tr>
						<td height="20" colspan="2" bgcolor="#006633" class="style1">Participante</td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Emp</font></strong></td>
						<td><input name="empresa" type="text" class="textboxFlat" id="empresa" value="<?= $row['cd_empresa']?> - <?= $row['nome_empresa']?>" style="width: 98%" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">RE</font></strong></td>
						<td>
							<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td><input name="re" type="text" class="textboxFlat" id="re" value="<?= $row['cd_registro_empregado']?>" size="6" maxlength="6" readonly></td>
								<td width="30">&nbsp;</td>
								<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Seq&nbsp;</font></strong></td>
								<td><input name="sequencia" type="text" class="textboxFlat" id="sequencia" value="<?= $row['seq_dependencia']?>" size="2" maxlength="2" readonly></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nome</font></strong></td>
						<td><input name="nome" type="text" class="textboxFlat" id="nome" value="<?= $row['nome']?>" style="width: 98%" readonly></td>
					</tr>
					<tr bgcolor="#006633">
						<td height="20" colspan="2" bgcolor="#006633"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" class="style1">Endereço</font></td>
					</tr>
					<tr>
						<td>
							<strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Opção</font></strong>
						</td>
						<td>
							<input name="opcao_envio" type="text" class="textboxFlat" id="opcao_envio" value="<?= $row['opcao_envio']?>" style="width: 98%; font-weight: bold;" readonly>
						</td>
					</tr>				  
					<tr>
						<td>
							<strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Email 1</font></strong>
						</td>
						<td>
							<input name="email" type="text" class="textboxFlat" id="email" value="<?= $row['email']?>" style="width: 98%" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Email 2</font></strong>
						</td>
						<td>
							<input name="email_profissional" type="text" class="textboxFlat" id="email_profissional" value="<?= $row['email_profissional']?>" style="width: 98%" readonly>
						</td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Logradouro</font></strong></td>
						<td><input name="logradouro" type="text" class="textboxFlat" id="logradouro" value="<?= $row['logradouro']?>" style="width: 98%" readonly></td>
					</tr>				  
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bairro</font></strong></td>
						<td><input name="bairro" type="text" class="textboxFlat" id="bairro" value="<?= $row['bairro']?>" style="width: 98%" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cidade</font></strong></td>
						<td><input name="cidade" type="text" class="textboxFlat" id="cidade" value="<?= $row['cidade']?>" style="width: 98%" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Estado</font></strong></td>
						<td>
							<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td><input name="unidade_federativa" type="text" class="textboxFlat" id="unidade_federativa" value="<?= $row['unidade_federativa']?>" size="2" maxlength="2" readonly></td>
								<td width="30">&nbsp;</td>
								<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CEP&nbsp;</font></strong></td>
								<td>
									<input name="cep" type="text" class="textboxFlat" id="cep" value="<?= $row['cep']?>" size="6" readonly>
									<input name="complemento_cep" type="text" class="textboxFlat" id="complemento_cep" value="<?= $row['complemento_cep']?>" size="4" maxlength="3" readonly>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					
				<?php
					if($row['origem'] == "C") #### EMPRESTIMO CALLCENTER
					{
						if($row['forma_calculo'] == "P") ## PRE-FIXADO
						{
				?>				
							<table width="440" border="0" cellpadding="0" cellspacing="1" class="bordaCallCenter">
							<tr>
								<td height="20" bgcolor="#990000">
									<div align="center" class="style4 style5">
										<font color="#FFFFFF" face="Tahoma, Verdana, Arial">Mensagem Call-Center</font>
									</div>
								</td>
							</tr>
							<tr>
								<td bgcolor="#Fafafa">
									<table width="100%" border="0" cellpadding="1" cellspacing="0">
									<tr>
										<td bgcolor="#DDDDDD">
											<span class="style2">- A sua empresa é <?= $row['nome_empresa']?>, o seu re.d é <?= $row['cd_registro_empregado']?></span>
										</td>
									</tr>
									<tr>
										<td>
											<span class="style2">- Você optou pelo empréstimo <b>PREFIXADO</b></span>
										</td>
									</tr>						
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- O montante concedido é de R$ <?= $row['montante_concedido']?></span></td>
									</tr>
									<tr>
										<td><span class="style2">- O Valor do depósito será de R$ <?= $row['vlr_deposito']?> </span></td>
									</tr>
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- A data do depósito será no dia <?= $row['dt_deposito']?></span></td>
									</tr>
									<tr>
										<td>
											<span class="style2">
												<span id="divTextoConta" class="style2">
												- Na agência <?= $row['cd_agencia']?>, no banco <?= $row['nome_banco']?>, e o número da conta é <?= $row['conta_folha']?>
												</span>
											</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#DDDDDD">
											<span class="style2">- O número de parcelas contratas é de <?= $row['nro_prestacoes']?>, no valor de R$ <?= $row['vlr_prestacao']?></span>
										</td>
									</tr>
									<tr>
										<td style="padding: 3px;">
											<span class="style2">
												<?php
													if($row['cd_opcao_envio'] == "I") #### ELETRONICO
													{
														echo '
																<BR>O demonstrativo deste empréstimo contendo todas as taxas aplicadas no mês, será enviado para a seu email(s): 
																<BR>
																'.$row['email'].'
																<BR>
																'.$row['email_profissional'].'
																<br>
																<br>												     
															 ';
													}
													else #### IMPRESSO
													{
														echo '
																<BR>E o demonstrativo do empréstimo, contendo as taxas aplicadas no mês, será enviado para a sua residência.
																<br>
																<br>												     
															 ';												
													}
												?>
											</span>
										</td>
									</tr>
									</table>    
								</td>
							</tr>
							</table>				
				<?php
						}
						elseif($row['forma_calculo'] == "O") ## POS-FIXADO
						{
				?>
							<table width="440" border="0" cellpadding="0" cellspacing="1" class="bordaCallCenter">
							<tr>
								<td height="20" bgcolor="#990000"><div align="center" class="style4 style5"><font color="#FFFFFF" face="Tahoma, Verdana, Arial">Mensagem Call-Center</font></div></td>
							</tr>
							<tr>
								<td bgcolor="#Fafafa">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- A sua empresa é <?= $row['nome_empresa']?>, o seu re.d é <?= $row['cd_registro_empregado']?></span></td>
									</tr>
									<tr>
										<td><span class="style2">- Você optou pelo empréstimo <b>PÓS-FIXADO</b></span></td>
									</tr>						
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- O montante concedido é de R$ <?= $row['montante_concedido']?></span></td>
									</tr>
									<tr>
										<td><span class="style2">- O Valor do depósito será de R$ <?= $row['vlr_deposito']?></span></td>
									</tr>
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- A data do depósito será no dia <?= $row['dt_deposito']?></span></td>
									</tr>
									<tr>
										<td>
											<span class="style2">
												<span id="divTextoConta" class="style2">
													- Na agência <?= $row['cd_agencia']?>, no banco <?= $row['nome_banco']?>, e o número da conta é <?= $row['conta_folha']?>
												</span>
											</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#DDDDDD"><span class="style2">- O número de parcelas contratas será de <?= $row['nro_prestacoes']?>.</span></td>
									</tr>
									<tr>
										<td bgcolor="#DDDDDD">
											<span class="style2">
												- O Valor da 1ª prestação projetada é de R$ <?= $row['vlr_prestacao']?>.
												A prestação será ajustada pela variação do INPC-IBGE divulgada no mês anterior ao vencimento.						   
											</span>
										</td>
									</tr>						
									<tr>
										<td style="padding: 3px;">
											<span class="style2">
												<?php
													if($row['cd_opcao_envio'] == "I") #### ELETRONICO
													{
														echo '
																<BR>O demonstrativo deste empréstimo contendo todas as taxas aplicadas no mês, será enviado para a seu email(s): 
																<BR>
																'.$row['email'].'
																<BR>
																'.$row['email_profissional'].'
																<br>
																<br>												     
															 ';
													}
													else #### IMPRESSO
													{
														echo '
																<BR>E o demonstrativo do empréstimo, contendo as taxas aplicadas no mês, será enviado para a sua residência.
																<br>
																<br>												     
															 ';												
													}
												?>
											</span>
										</td>
									</tr>
									</table>                     
								</td>
							</tr>
							</table>
				<?php
						}
					}
					else
					{
				?>
					<table width="440" border="0" cellpadding="0" cellspacing="1" class="bordaCallCenter">
					<tr>
						<td height="20" bgcolor="#990000">
							<div align="center" class="style4 style5">
								<font color="#FFFFFF" face="Tahoma, Verdana, Arial">Opção Empréstimo</font>
							</div>
						</td>
					</tr>
					<tr>
						<td bgcolor="#Fafafa">
							<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="style2" style="text-align:center;">
									<BR>
									<b>
									<?php
										if($row['forma_calculo'] == "O") ## POS-FIXADO
										{
											echo "PÓS-FIXADO";
										}
										
										if($row['forma_calculo'] == "P") ## PRE-FIXADO
										{
											echo "PREFIXADO";
										}	
									?>	
									</b>
									<BR><BR>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>		
				<?php
					}
				?>				
				</td>
			</tr>
			</table>
		</td>
		<td width="10">&nbsp;</td>
		<td valign="top">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<table width="350" border="0" cellpadding="1" cellspacing="1">
					<tr bgcolor="#006633">
						<td height="20" colspan="2" class="bordaTabela"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" class="style1">Empréstimo</font></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data Depósito</font></strong></td>
						<td><input name="dt_deposito" type="text" class="textboxFlat" id="dt_deposito" value="<?= $row['dt_deposito']?>" size="12" maxlength="10" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Valor Depósito</font></strong></td>
						<td><input name="vlr_deposito" type="text" class="textboxFlat" id="vlr_deposito" value="<?= $row['vlr_deposito']?>" size="12" maxlength="15" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nr	Prestações</font></strong></td>
						<td><input name="numero_prestacoes" type="text" class="textboxFlat" id="numero_prestacoes" value="<?= $row['nro_prestacoes']?>" size="2" maxlength="2" readonly></td>
					</tr>
					<tr>
						<td>
							<strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							<?php
								if($row['forma_calculo'] == "O") ##POS-FIXADO
								{
									echo "1ª Prest. Projetada";
								}
								elseif($row['forma_calculo'] == "P") ##PRE-FIXADO
								{
									echo "Primeira Prest.";
								}								
							?>
							</font></strong>
						</td>
						<td><input name="vlr_prestacao" type="text" class="textboxFlat" id="vlr_prestacao" value="<?= $row['vlr_prestacao']?>" size="12" maxlength="15" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Primeira Prest.</font></strong></td>
						<td><input name="dt_primeira_prestacao" type="text" class="textboxFlat" id="dt_primeira_prestacao" value="<?= $row['dt_primeira_prestacao']?>" size="12" maxlength="10" readonly></td>
					</tr>
					<tr>
						<td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Última Prest.</font></strong></td>
						<td><input name="dt_ultima_prestacao" type="text" class="textboxFlat" id="dt_ultima_prestacao" value="<?= $row['dt_ultima_prestacao']?>" size="12" maxlength="10" readonly></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<form action="concede_emprestimo_dap.php" method="post" onSubmit="return validaForm()">
						<input type="hidden" name="usuario_emp"                                      value="">
						<input type="hidden" name="session_id"            id="session_id"            value="<?= $id_simulacao?>">
						<input type="hidden" name="fl_mostrar_header"     id="fl_mostrar_header"     value="<?= $fl_mostrar_header?>">
						<input type="hidden" name="nro_prestacoes"        id="nro_prestacoes"        value="<?= $row['nro_prestacoes']?>">
						<input type="hidden" name="cd_empresa"            id="cd_empresa"            value="<?= $row['cd_empresa']?>">
						<input type="hidden" name="cd_registro_empregado" id="cd_registro_empregado" value="<?= $row['cd_registro_empregado']?>">
						<input type="hidden" name="seq_dependencia"       id="seq_dependencia"       value="<?= $row['seq_dependencia']?>">
						<input type="hidden" name="origem"                id="origem"                value="<?= $row['origem']?>">		
						<input type="hidden" name="tmpNomeBanco"          id="tmpNomeBanco">
						<input type="hidden" name="cd_agencia_default"    id="cd_agencia_default"    value="<?= $row['cd_agencia']?>">
					
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0">
						<tr bgcolor="#006633">
							<td height="20" colspan="2"><strong class="style1"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Informações Bancárias</font></strong></td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Banco</strong> </font></td>
							<td bgcolor="#f0f0f0">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<select name="cd_instituicao" id="cd_instituicao" onChange="getAgencia();" style="width:160px">
									<?php
										foreach($banco['list'] as $item)
										{
											echo '<option value="'.$item['value'].'" '.($item['selected'] == "TRUE" ? "selected": "").'>'.$item['text'].'</option>';
										}
									?>
									</select>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Agência</strong></font></td>
							<td bgcolor="#f0f0f0">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<select name="cd_agencia" id="cd_agencia" onChange="alteraInfDeposito();">
									<?php
										foreach($agencia['list'] as $item)
										{
											echo '<option value="'.$item['value'].'" '.($item['selected'] == "TRUE" ? "selected": "").'>'.$item['text'].'</option>';
										}
									?>								
								</select>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Conta</strong></font></td>
							<td bgcolor="#f0f0f0">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<input name="conta_folha" type="text" id="conta_folha" value="<?= $row['conta_folha']?>" size="10" maxlength="10" onBlur="alteraInfDeposito();">
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Pagamento</strong></font></td>
							<td bgcolor="#f0f0f0">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<select name="pagamento" id="pagamento">
									<option value="BCO" <?= ($row['forma_pgto_fundacao'] == "BCO" ? "selected" : "")?>>Banco</option>
									<option value="CXA" <?= ($row['forma_pgto_fundacao'] == "CXA" ? "selected" : "")?>>Caixa</option>
								</select>
								</font>
							</td>
						</tr>
						<tr height="20" bgcolor="#006633">
							<td colspan="2"><strong class="style1"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Confirmação</font></strong></td>
						</tr>
						<tr>
							<td colspan="2">
								<?php
									$fl_autenticar_atendente     = "";
									$fl_autenticar_participante  = "";
									$ver_autenticar_atendente    = "";
									$ver_autenticar_participante = "";
									$tp_autenticar = "";
									if((intval($contrato['tp_senha_callcenter']) == 2) and ($row['origem'] == "N"))
									{
										$fl_autenticar_participante = "checked";
										$ver_autenticar_atendente   = "display:none;";
										$tp_autenticar = "P";
									}
									else
									{
										$fl_autenticar_atendente     = "checked";
										$ver_autenticar_participante = "display:none;";
										$tp_autenticar = "A";
									}
								?>
								<nobr>
								<input type="radio" name="tp_autenticar" id="tp_autenticar_atendente"    value="A" <?= $fl_autenticar_atendente?>    onclick="setAutenticar('A');"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Atendente</strong></font>
								<input type="radio" name="tp_autenticar" id="tp_autenticar_participante" value="P" <?= $fl_autenticar_participante?> onclick="setAutenticar('P');"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Participante</strong></font>
								
								<script> 
									$(function(){
										<?php
											echo 'setAutenticar("'.$tp_autenticar.'")';
										?>
									});								
								</script>
								</nobr>
							</td>
						</tr>	
						<tr>	
							<td colspan="2">
								<table border="0" id="ob_autenticar_atendente" style="<?= $ver_autenticar_atendente?>">
									 <tr>
										<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Usuário</strong></font></td>
										<td><input name="usuario" type="text" id="usuario" value="<?= $this->session->userdata('usuario')?>"></td>
									 </tr>
									 <tr>
										<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Senha</strong></font></td>
										<td><input name="senha" type="password" id="senha"></td>
									 </tr>
								</table>
							
								<table border="0" id="ob_autenticar_participante" style="<?= $ver_autenticar_participante?>">
									 <tr>
										<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Senha</strong></font></td>
										<td><input type="password" name="senha_participante" id="senha_participante"></td>
									 </tr>
								</table>						
							</td>
						</tr>
    
						<tr height="20" bgcolor="#006633">
							<td colspan="2"><strong class="style1"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Checklist - Confirmação de Empréstimo</font></strong></td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="checkbox" name="fl_dados_bancarios" id="fl_dados_bancarios" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Confirmar dados bancários</strong>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="checkbox" name="fl_valor_credito" id="fl_valor_credito" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Valor do crédito</strong>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="checkbox" name="fl_valor_prestacao" id="fl_valor_prestacao" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Valor da prestação</strong>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="checkbox" name="fl_data_deposito" id="fl_data_deposito" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Informar data do depósito</strong>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="checkbox" name="fl_data_primeiro_pagamento" id="fl_data_primeiro_pagamento" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Informar data do 1º pagamento</strong>
								</font>
							</td>
						</tr>
					    <tr style="<?= (trim($row['fl_reforma']) == "N" ? "display:none;" : "")?>">
							<td bgcolor="#f0f0f0" colspan="2">
								<input type="hidden" name="fl_reforma_contrato" id="fl_reforma_contrato" value="<?= $row['fl_reforma']?>">
								<input type="checkbox" name="fl_reforma" id="fl_reforma" value="S">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
									<strong>Informar se haverá devolução da prestação do empréstimo anterior e data  da devolução. (Contrato Origem <?= $row['cd_contrato_origem']?>)</strong>
								</font>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
								<input name="btnVoltar" type="button"  value="Voltar" onClick="voltar();" class="botao_disabled" style="width: 100px;" id="btnVoltar">
								&nbsp;
								<input type="submit" name="Submit" value="Confirmar" class="botao" style="width: 100px;">
								</div>
							</td>
						</tr>					 
						</table>
					</form>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr> 
		<td align="left">
			<div style="text-align:left; font-size: 7pt;"><?= date("d/m/Y H:i:s")?></div>		
		</td>
		<td align="right" colspan="2">
			<div style="text-align:right; font-size: 7pt;"><?= "[".$row["origem"]."]"."[".$SKT_IP.":".$SKT_PORTA."]"?></div>		
		</td>		
	</tr>   
</table>
	
<?php
    echo br(10);
    $this->load->view('footer_interna');
?>	