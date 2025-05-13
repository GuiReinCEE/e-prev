<?php
	set_title('Família Vendas');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array()); ?>

	function ir_lista()
	{
		 location.href = "<?= site_url('ecrm/familiavendas_solicitacao') ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/familiavendas_solicitacao/acompanhamento/'.$row['cd_app_solicitacao']) ?>";
	}

	function formulario_inscricao()
	{
		window.open("http://app.eletroceee.com.br/srvfamiliavendas/index.php/solicitacao/formulario_inscricao/<?= $row['cd_app_solicitacao_md5'] ?>");
	}

	function em_analise()
	{
		var confirmacao = 'Deseja colocar solicitação em análise?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/familiavendas_solicitacao/em_analise/'.$row['cd_app_solicitacao']) ?>";
		}
		
	}

	function concluir()
	{
		var confirmacao = 'Deseja concluir a solicitação?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/familiavendas_solicitacao/concluir/'.$row['cd_app_solicitacao']) ?>";
		}
		
	}

	function cancelar()
	{
		var confirmacao = 'Deseja cancelar a solicitação?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/familiavendas_solicitacao/cancelar/'.$row['cd_app_solicitacao']) ?>";
		}
		
	}
	
	function assinar()
	{
		var confirmacao = 'ATENÇÃO\n\nCONFIRA E SALVE OS DADOS DO FORMULÁRIO ANTES DE DE ENVIAR\n\nDeseja enviar para assinatura?\n\n'+
			'Clique [Ok] para Sim\n'+
			'Clique [Cancelar] para Não\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/familiavendas_solicitacao/assinar/'.$row['cd_app_solicitacao']) ?>";
		}
		
	}	

	$(function(){
	});
</script>
<?php

	$ar_forma_pagamento[] = array('text' => 'Selecione', 'value' => '');
	$ar_forma_pagamento[] = array('text' => 'Boleto', 'value' => 'BDL');
	$ar_forma_pagamento[] = array('text' => 'Débito em Conta', 'value' => 'DCC');
	$ar_forma_pagamento[] = array('text' => 'Folha de Pagamento', 'value' => 'FOL');
	
	$ar_forma_pagamento_extra[] = array('text' => 'Selecione', 'value' => '');
	$ar_forma_pagamento_extra[] = array('text' => 'Não possui', 'value' => 'NAO');
	$ar_forma_pagamento_extra[] = array('text' => 'Boleto', 'value' => 'BDL');
	$ar_forma_pagamento_extra[] = array('text' => 'Débito em Conta', 'value' => 'DCC');
	$ar_forma_pagamento_extra[] = array('text' => 'Folha de Pagamento', 'value' => 'FOL');	
	
	$ar_sexo[] = array('text' => 'Selecione', 'value' => '');
	$ar_sexo[] = array('text' => 'Feminino', 'value' => 'F');
	$ar_sexo[] = array('text' => 'Masculino', 'value' => 'M');

	$ar_ppe[] = array('value' => "", 'text'  => "Não informado");
	$ar_ppe[] = array('value' => "S", 'text'  => "Sim");
	$ar_ppe[] = array('value' => "N", 'text'  => "Não");
	
	$ar_usperson[] = array('value' => "", 'text'  => "Não informado");
	$ar_usperson[] = array('value' => "S", 'text'  => "Sim");
	$ar_usperson[] = array('value' => "N", 'text'  => "Não");	

	$ar_tributacao[] = array('value' => "", 'text'  => "Não informado");
	$ar_tributacao[] = array('value' => "P", 'text'  => "Tabela Progressiva - Tradicional");
	$ar_tributacao[] = array('value' => "R", 'text'  => "Tabela Regressiva ");	

	$ar_lgp[] = array('value' => "", 'text'  => "Não informado");
	$ar_lgp[] = array('value' => "S", 'text'  => "Sim");
	$ar_lgp[] = array('value' => "N", 'text'  => "Não");		

	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

	echo aba_start($abas);
		echo form_open('ecrm/familiavendas_solicitacao/salvar_cadastro');
			echo form_start_box('default_box_cad', 'Cadastro');
				echo form_default_hidden('cd_app_solicitacao','', $row['cd_app_solicitacao']);
				echo form_default_row('nr_protocolo','Nr. Protocolo App:', '<span class="label label-inverse">'.$row['nr_protocolo'].'</span> ');
				echo form_default_row('id_doc_assinatura','Protocolo Assinatura:', '<span class="label label-success">'.$row['id_doc_assinatura'].'</span> '.(trim($row['id_doc_assinatura']) != "" ? anchor('https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/'.$row['id_doc_assinatura'], '[ver situação]', array('target' => '_blank')) : ""));
				echo form_default_row('', '', '', '');
				echo form_default_row('dt_alteracao', 'Dt. Solicitação:', $row['dt_alteracao'], 'style="width:400px;"');
				echo form_default_row('ds_status', 'Status:', '<span class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</span>','style="width:400px;"');
				echo form_default_text('ds_nome', 'Nome:', $row['ds_nome'],'style="width:600px;"');
				echo form_default_text('ds_nome_social', 'Nome Social:', $row['ds_nome_social'],'style="width:600px;"');
				echo form_default_date('dt_nascimento', 'Dt. Nascimento:', $row['dt_nascimento']);
				echo form_default_dropdown('ds_estado_civil', 'Estado Civil:', $ar_estado_civil, $row['ds_estado_civil']);
				echo form_default_dropdown('tp_sexo', 'Sexo:', $ar_sexo, $row['tp_sexo']);
				echo form_default_cpf('ds_cpf', 'CPF:', $row['ds_cpf']);
				echo form_default_text('ds_rg', 'RG:', $row['ds_rg']);
				echo form_default_text('ds_orgao_expedidor', 'Orgão expedidor:', $row['ds_orgao_expedidor']);
				echo form_default_date('dt_expedicao', 'Data de Expedição:', $row['dt_expedicao']);
				echo form_default_text('ds_nome_pai', 'Nome do Pai:', $row['ds_nome_pai'], 'style="width:600px;"');
				echo form_default_text('ds_nome_mae', 'Nome da Mãe:', $row['ds_nome_mae'], 'style="width:600px;"');
				echo form_default_text('ds_naturalidade', 'Naturalidade:', $row['ds_naturalidade'], 'style="width:600px;"');
				echo form_default_text('ds_nacionalidade', 'Nacionalidade:', $row['ds_nacionalidade'], 'style="width:600px;"');
				echo form_default_text('ds_cep', 'CEP (99999-999):', $row['ds_cep']);
				echo form_default_text('ds_endereco', 'Endereço:', $row['ds_endereco'], 'style="width:600px;"');
				echo form_default_text('nr_endereco', 'Número:', $row['nr_endereco']);
				echo form_default_text('ds_complemento', 'Complemento:', $row['ds_complemento'], 'style="width:600px;"');
				echo form_default_text('ds_bairro', 'Bairro:', $row['ds_bairro'],'style="width:400px;"');
				echo form_default_text('ds_cidade', 'Cidade:', $row['ds_cidade'],'style="width:400px;"');
				echo form_default_text('ds_uf', 'UF:', $row['ds_uf']);
				echo form_default_telefone('ds_celular', 'Celular:', $row['ds_celular']);
				echo form_default_telefone('ds_telefone', 'Telefone:', $row['ds_telefone']);
				echo form_default_text('ds_email', 'E-mail:', $row['ds_email'], 'style="width:400px;"');
				echo form_default_dropdown('fl_ppe', 'PPE:', $ar_ppe, $row['fl_ppe']);
				echo form_default_dropdown('fl_usperson', 'US Person:', $ar_usperson, $row['fl_usperson']);
				//echo form_default_dropdown('fl_tributacao', 'Tributação:', $ar_tributacao, $row['fl_tributacao']);

				echo form_default_dropdown('fl_lgpd', 'Tratamento de dados LGPD:', $ar_lgp, $row['fl_lgpd']);	
				
			echo form_end_box('default_box_cad');
			
			echo form_start_box('default_box_vinculo', 'Vinculação');
				echo form_default_dropdown('cd_instituidor', 'Instituidor:', $ar_instituidor, $row['cd_instituidor']);	
				echo form_default_text('ds_associado', 'Associado a:', $row['ds_associado'], 'style="width:400px;"');
				echo form_default_text('ds_vinculo_associado', 'Vínculo:', $row['ds_vinculo_associado'], 'style="width:400px;"');
				echo form_default_text('ds_vinculo_grau', 'Grau de Vínculo:', $row['ds_vinculo_grau'], 'style="width:400px;"');
			echo form_end_box('default_box_vinculo');				
			
			echo form_start_box('default_box_doc', 'Documentos');
				echo ($row['ds_doc_frente'] != '' ? form_default_row('ds_doc_frente', 'Documento Frente:', anchor('https://www.fcprev.com.br/srvfamiliavendas/up/'.$row['cd_app_solicitacao_md5'] .'/'. $row['ds_doc_frente'], $row['ds_doc_frente'], array('target' => '_blank'))):'');
				echo ($row['ds_doc_verso'] != '' ? form_default_row('ds_doc_verso', 'Documento Verso:', anchor('https://www.fcprev.com.br/srvfamiliavendas/up/'.$row['cd_app_solicitacao_md5'] .'/'. $row['ds_doc_verso'], $row['ds_doc_verso'], array('target' => '_blank'))):'');
				echo ($row['ds_doc_representante_frente'] != '' ? form_default_row('ds_doc_representante_frente', 'Representante Frente:', anchor('https://www.fcprev.com.br/srvfamiliavendas/up/'.$row['cd_app_solicitacao_md5'] .'/'. $row['ds_doc_representante_frente'], $row['ds_doc_representante_frente'], array('target' => '_blank'))):'');
				echo ($row['ds_doc_representante_verso'] != '' ? form_default_row('ds_doc_representante_verso', 'Documento Representante Verso:', anchor('https://www.fcprev.com.br/srvfamiliavendas/up/'.$row['cd_app_solicitacao_md5'] .'/'. $row['ds_doc_representante_verso'], $row['ds_doc_representante_verso'], array('target' => '_blank'))):'');
			echo form_end_box('default_box_doc');			

			echo form_start_box('default_box_contrib', 'Contribuição Primeira');
				echo form_default_dropdown('tp_forma_pagamento_primeira', 'Forma de Pagamento:', $ar_forma_pagamento, $row['tp_forma_pagamento_primeira']);
				echo form_default_numeric('nr_contrib_primeira', 'Primeira Contribuição:',number_format($row['nr_contrib_primeira'],2,',','.'));
			echo form_end_box('default_box_contrib');
			
			echo form_start_box('default_box_contrib', 'Contribuição Mensal');
				echo form_default_dropdown('tp_forma_pagamento_mensal', 'Forma de Pagamento:', $ar_forma_pagamento, $row['tp_forma_pagamento_mensal']);
				echo form_default_numeric('nr_contrib_mensal', 'Demais Contribuições:',number_format($row['nr_contrib_mensal'],2,',','.'));
			echo form_end_box('default_box_contrib');
			
			echo form_start_box('default_box_contrib_ini', 'Contribuição Extra-inicial');
				echo form_default_dropdown('tp_forma_pagamento_extra_inicial', 'Forma de Pagamento:', $ar_forma_pagamento_extra, $row['tp_forma_pagamento_extra_inicial']);
				echo form_default_numeric('nr_contrib_extra_inicial', 'Contribuição Extra-inicial:',number_format($row['nr_contrib_extra_inicial'],2,',','.'));			
			echo form_end_box('default_box_contrib_ini');			


			if(($row['tp_forma_pagamento_primeira'] == 'DCC') OR ($row['tp_forma_pagamento_mensal'] == 'DCC') OR ($row['tp_forma_pagamento_extra_inicial'] == 'DCC'))
			{
				echo form_start_box('default_box_debito', 'Autorização Débito em Conta');
					echo form_default_row('', '', '<span style="color: red; font-weight:bold">Cliente com débito em conta, é necessário informar os dados para desconto.</span>','style="width:600px;"');
					echo form_default_text('ds_nome_debito_conta', 'Nome:', $row['ds_nome_debito_conta'], 'style="width:600px;"');
					echo form_default_cpf('cpf_debito_conta', 'CPF:', $row['cpf_debito_conta']);	
					echo form_default_text('email_debito_conta', 'E-mail:', $row['email_debito_conta'], 'style="width:600px;"');
					echo form_default_telefone('telefone_debito_conta', 'Celular:', $row['telefone_debito_conta']);
					echo form_default_row('', 'Banco:', '041 - Banrisul', 'style="width:600px;"');
					echo form_default_text('agencia_debito_conta', 'Agência:', $row['agencia_debito_conta']);
					echo form_default_text('conta_corrente_debito_conta', 'Conta Corrente:', $row['conta_corrente_debito_conta']);
				echo form_end_box('default_box_debito');
			}

			if(($row['tp_forma_pagamento_primeira'] == 'FOL') OR ($row['tp_forma_pagamento_mensal'] == 'FOL') OR ($row['tp_forma_pagamento_extra_inicial'] == 'FOL'))
			{
				echo form_start_box('default_box_folha', 'Autorização Folha de Pagamento');
					echo form_default_row('', '', '<span style="color: red; font-weight:bold">Cliente com desconto em folha de pagamento, é necessário informar os dados para desconto.</span>','style="width:600px;"');
					echo form_default_text('ds_nome_folha_pagamento', 'Nome:', $row['ds_nome_folha_pagamento'], 'style="width:600px;"');
					echo form_default_cpf('cpf_folha_pagamento', 'CPF:', $row['cpf_folha_pagamento']);	
					echo form_default_text('ds_empresa_folha_pagamento', 'Empresa:', $row['ds_empresa_folha_pagamento'], 'style="width:600px;"');
					echo form_default_text('email_folha_pagamento', 'E-mail:', $row['email_folha_pagamento'], 'style="width:600px;"');
					echo form_default_telefone('telefone_folha_pagamento', 'Celular:', $row['telefone_folha_pagamento']);
				echo form_end_box('default_box_folha');
			}

			if($row['fl_menor'] == 'S')
			{
				echo form_start_box('default_box_legal', 'Representante Legal');
					echo form_default_row('', '', '<span style="color: red; font-weight:bold">Cliente menor de idade, é necessário informar o representante legal.</span>','style="width:600px;"');
					echo form_default_text('ds_nome_representante_legal', 'Nome:', $row['ds_nome_representante_legal'], 'style="width:600px;"');
					echo form_default_cpf('ds_cpf_representante_legal', 'CPF:', $row['ds_cpf_representante_legal']);	
					echo form_default_text('email_representante_legal', 'E-mail:', $row['email_representante_legal'], 'style="width:600px;"');
					echo form_default_telefone('telefone_representante_legal', 'Celular:', $row['telefone_representante_legal']);					
				echo form_end_box('default_box_legal');
			}

			$i = 1;
			$f = 4;
			while($i <= $f)
			{
				echo form_start_box('default_box_ben_'.$i, 'Beneficiário '.$i);
					echo form_default_text('beneficiario_'.$i.'_nome', 'Nome:', $row['beneficiario_'.$i.'_nome'],'style="width:600px;"');
					echo form_default_date('beneficiario_'.$i.'_dt_nascimento', 'Dt. Nascimento:', $row['beneficiario_'.$i.'_dt_nascimento']);
					echo form_default_dropdown('beneficiario_'.$i.'_sexo', 'Sexo:', $ar_sexo, $row['beneficiario_'.$i.'_sexo']);
					echo form_default_cpf('beneficiario_'.$i.'_cpf', 'CPF:', $row['beneficiario_'.$i.'_cpf']);
					echo form_default_numeric('beneficiario_'.$i.'_beneficio', 'Benefício por Morte%:', number_format($row['beneficiario_'.$i.'_beneficio'],2,',','.'));
				echo form_end_box('default_box_ben_'.$i);
				$i++;
			}

			echo form_start_box('default_box_vendedor', 'Vendedor');
				echo form_default_text('ds_nome_vendedor', 'Nome do Vendedor:', $row['ds_nome_vendedor'], 'style="width:600px;"');
				echo form_default_date('dt_recebimento', 'Dt. Recebimento:', $row['dt_recebimento']);
				echo form_default_telefone('ds_vendedor_celular', 'Celular:', $row['ds_vendedor_celular']);
				echo form_default_text('ds_vendedor_email', 'E-mail:', $row['ds_vendedor_email'], 'style="width:600px;"');
			echo form_end_box('default_box_vendedor');
			
			echo form_start_box('default_box_indicacao', 'Indicação Interna');
				echo form_default_text('indicacao_interna_nome', 'Nome:', $row['indicacao_interna_nome'], 'style="width:600px;"');
				echo form_default_cpf('indicacao_interna_cpf', 'CPF:', $row['indicacao_interna_cpf']);
			echo form_end_box('default_box_indicacao');		

			echo form_start_box('default_box_indicacao', 'Indicação');
				echo form_default_cpf('cpf_indicacao', 'CPF Indicação:', $row['cpf_indicacao']);
			echo form_end_box('default_box_indicacao');				

			echo br(3);

			echo form_command_bar_detail_start();
				echo button_save('Ver formulário de inscrição', 'formulario_inscricao()', 'botao_disabled');

				if((trim($row['dt_cancelado']) == "") AND (trim($row['dt_concluido']) == ""))
				{
					if(trim($row['id_doc_assinatura']) == "")
					{
						echo button_save('Enviar formulário para assinatura', 'assinar()', 'botao_vermelho');
					}
					echo br(2);
					
					if(trim($row['id_doc_assinatura']) == "")
					{
						echo button_save('Salvar');
						if($row['dt_analise'] == '')
						{
							echo button_save('Em Análise', 'em_analise()', 'botao');
						}
					}
					echo button_save('Concluir', 'concluir()', 'botao_verde');
					echo button_save('Cancelar', 'cancelar()', 'botao_vermelho');
				}
            echo form_command_bar_detail_end();
		echo form_close();
		echo br(10);
	echo aba_end();

	$this->load->view('footer');
?>