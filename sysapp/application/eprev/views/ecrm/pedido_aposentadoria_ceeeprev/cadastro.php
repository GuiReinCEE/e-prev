<?php
	set_title('Pedido de Aposentadoria CeeePrev');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_nome')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev') ?>";
    }

    function ir_dependente()
    {
    	location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/dependente/'.$row['cd_pedido_aposentadoria_ceeeprev']) ?>";
    }

    function adiantamento()
    {
    	if($("#fl_adiantamento_cip").val() == 'S')
    	{	
    		$("#nr_adiantamento_cip_row").show();
    	}
    	else
    	{
    		$("#nr_adiantamento_cip_row").hide();
    		$("#nr_adiantamento_cip").val("");
    	}
    }

    function em_analise()
	{
		var confirmacao = 
		 	'Deseja colocar em análise o pedido de aposentadoria?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/analise/'.intval($row['cd_pedido_aposentadoria_ceeeprev'])) ?>";
		}
	}

	function indeferir()
	{
		if($("#ds_motivo_indeferido").val() != '')
		{
			var confirmacao = 
			 	'Deseja indeferir o pedido de aposentadoria?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';

	        if(confirm(confirmacao))
	        { 
				$("#form_pedido").attr("action", "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/indeferir') ?>");
				$("#form_pedido").submit();
			}
		}
		else
		{
			alert("Informe o motivo do indeferimento");
		}
	}

	function assinatura()
	{
		var confirmacao = 
		 	'Deseja enviar o pedido de aposentadoria para assinatura?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/assinatura/'.intval($row['cd_pedido_aposentadoria_ceeeprev'])) ?>";
		}
	}

	function deferido()
	{
		var confirmacao = 
		 	'Deseja deferir o pedido de aposentadoria?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
        	$("#btn_deferido").hide();
        	location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/deferido/'.intval($row['cd_pedido_aposentadoria_ceeeprev'])) ?>";
        }		
	}

	function ir_dependente_prev()
    {
    	location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario/'.$row['cd_pedido_aposentadoria_ceeeprev']) ?>";
    }

	function formulario()
	{
		location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/formulario/'.intval($row['cd_pedido_aposentadoria_ceeeprev'])) ?>";
	}

    $(function(){
    	$("#ds_telefone1").mask("(999) 999999999");
		$("#ds_telefone2").mask("(999) 999999999");
		$("#ds_celular").mask("(999) 999999999");
		adiantamento();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	$abas[] = array('aba_dependente', 'Dependentes IR', FALSE, 'ir_dependente();');
	$abas[] = array('aba_dependente_prev', 'Dependentes Previdenciário', FALSE, 'ir_dependente_prev();');

	$drop = array(
		array('value' => 'N', 'text' => 'NÃO'),
		array('value' => 'S', 'text' => 'SIM'),
	);

	echo aba_start($abas);
		echo form_open('ecrm/pedido_aposentadoria_ceeeprev/salvar', 'id="form_pedido"');
			echo form_start_box('default_box_cadastro', 'Dados Cadastrais');
				echo form_default_hidden('cd_pedido_aposentadoria_ceeeprev', '', $row['cd_pedido_aposentadoria_ceeeprev']);
				echo form_default_row('', 'RE:', '<span class="label label-inverse">'.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'].'</span>');
				echo form_default_row('', 'Pedido de Aposentadoria:', '<span class="label label-inverse">'.$row['ds_pedido_aposentadoria'].'</span>');
				echo form_default_row('', 'Status:', '<span class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</span>');
				echo form_default_text('ds_nome', 'Nome:', $row['ds_nome'], 'style="width:350px;"');
				echo form_default_date('dt_nascimento', 'Data de Nascimento:', $row['dt_nascimento']);
				echo form_default_cpf('ds_cpf', 'CPF:', $row['ds_cpf']);
				echo form_default_dropdown('ds_estado_civil', 'Estado Cívil:', $estado_civil, $row['ds_estado_civil']);
				echo form_default_text('ds_naturalidade', 'Naturalidade:', $row['ds_naturalidade'], 'style="width:350px;"');
				echo form_default_text('ds_nacionalidade', 'Nacionalidade:', $row['ds_nacionalidade'], 'style="width:350px;"');
				echo form_default_text('ds_endereco', 'Endereço Residencial:', $row['ds_endereco'], 'style="width:350px;"');
				echo form_default_integer('nr_endereco', 'Número:', $row['nr_endereco']);
				echo form_default_text('ds_complemento_endereco', 'Complemento:', $row['ds_complemento_endereco'], 'style="width:350px;"');
				echo form_default_text('ds_bairro', 'Bairro:', $row['ds_bairro'], 'style="width:350px;"');
				echo form_default_text('ds_cidade', 'Cidade:', $row['ds_cidade'], 'style="width:350px;"');
				echo form_default_text('ds_uf', 'UF:', $row['ds_uf']);
				echo form_default_cep('ds_cep', 'CEP:', $row['ds_cep']);
				echo form_default_text('ds_telefone1', 'Telefone 1:', $row['ds_telefone1']);
				echo form_default_text('ds_telefone2', 'Telefone 2:', $row['ds_telefone2']);
				echo form_default_text('ds_celular', 'Celular:', $row['ds_celular']);
				echo form_default_text('ds_email1', 'Email 1:', $row['ds_email1'], 'style="width:350px;"');
				echo form_default_text('ds_email2', 'Email 2:', $row['ds_email2'], 'style="width:350px;"');
			echo form_end_box('default_box_cadastro');

			echo form_start_box('default_box_bancario', 'Dados Bancários');
				echo form_default_dropdown('ds_banco', 'Banco:', $banco, $row['ds_banco']);
				echo form_default_integer('ds_agencia', 'Agência:', $row['ds_agencia']);
				echo form_default_integer('ds_conta', 'Conta:', $row['ds_conta']);
			echo form_end_box('default_box_bancario');

			echo form_start_box('default_box_adiantamento', 'Adiantamento da CIP');
				echo form_default_dropdown('fl_adiantamento_cip', 'Receber Adiantamento:', $drop, $row['fl_adiantamento_cip'], 'onchange="adiantamento();"');
				echo form_default_dropdown('nr_adiantamento_cip', 'Percentual:', $percentual_adiantamento, $row['nr_adiantamento_cip']);
			echo form_end_box('default_box_adiantamento');

			echo form_start_box('default_box_beneficio', 'Benefício de Pensão por Morte');
				echo form_default_dropdown('fl_reversao_beneficio', 'Reversão para o Benefício de Pensão por Morte:', $drop, $row['fl_reversao_beneficio']);
			echo form_end_box('default_box_beneficio');

			echo form_start_box('default_box_politicamente_exposta', 'Pessoa politicamente exposta');
				echo form_default_dropdown('fl_politicamente_exposta', 'É pessoa politicamente exposta?:', $drop, $row['fl_politicamente_exposta']);
			echo form_end_box('default_box_politicamente_exposta');

			echo form_start_box('default_box_usperson', 'Pessoa politicamente exposta');
				echo form_default_dropdown('fl_us_person', 'É pessoa dos Estados Unidos (US Person):', $drop, $row['fl_us_person']);
			echo form_end_box('default_box_usperson');

			echo form_start_box('default_box_documento', 'Documentos');
				echo form_default_upload_iframe('arquivo_doc_identidade', 'pedido_aposentadoria_ceeeprev', 'Documento de Identidade:', array($row['arquivo_doc_identidade'], $row['arquivo_doc_identidade']), 'pedido_aposentadoria_ceeeprev');
				echo form_default_upload_iframe('arquivo_doc_cpf', 'pedido_aposentadoria_ceeeprev', 'Documento com CPF:', array($row['arquivo_doc_cpf'], $row['arquivo_doc_cpf']), 'pedido_aposentadoria_ceeeprev');
				echo form_default_upload_iframe('arquivo_recisao_contrato', 'pedido_aposentadoria_ceeeprev', 'Rescisão de Contrato:', array($row['arquivo_recisao_contrato'], $row['arquivo_recisao_contrato']), 'pedido_aposentadoria_ceeeprev');
				echo form_default_upload_iframe('arquivo_conta_bancaria', 'pedido_aposentadoria_ceeeprev', 'Comprovante de Conta Corrente:', array($row['arquivo_conta_bancaria'], $row['arquivo_conta_bancaria']), 'pedido_aposentadoria_ceeeprev');
			echo form_end_box('default_box_documento');

			echo form_start_box('default_box_simulacao', 'Simulação');
				echo form_default_upload_iframe('arquivo_simulacao', 'pedido_aposentadoria_ceeeprev', 'Arquivo:', array($row['arquivo_simulacao'], $row['arquivo_simulacao']), 'pedido_aposentadoria_ceeeprev');
			echo form_end_box('default_box_simulacao');

			if(trim($row['dt_analise']) != '' AND trim($row['dt_assinatura']) == '')
			{
				echo form_start_box('default_box_indeferir', 'Indeferir');
					echo form_default_textarea('ds_motivo_indeferido', 'Motivo:', $row['ds_motivo_indeferido'], 'style="height:150px;"');
					echo form_default_row('', '', '<i>ATENÇÃO: As informações registradas neste campo será recebida pelo participante.</i>');
				echo form_end_box('default_box_indeferir');
			}

			echo form_command_bar_detail_start();
				echo button_save('Formulário', 'formulario()', 'botao_disabled');
				
				if(trim($row['dt_indeferido']) == '' AND trim($row['dt_assinatura']) == '')
				{
					echo button_save('Salvar');
					if(trim($row['dt_analise']) == '')
					{
						echo button_save('Em Análise', 'em_analise()');
					}

					if(trim($row['dt_analise']) != '')
					{
						echo button_save('Enviar para Assinatura', 'assinatura()', 'botao_verde');
						echo button_save('Indeferir', 'indeferir()', 'botao_vermelho');
					}
				}

				if(trim($row['dt_assinatura']) != '' AND trim($row['dt_deferido']) == '')
				{
					echo button_save('Deferido', 'deferido()', 'botao_verde', 'id="btn_deferido"');
				}
				
            echo form_command_bar_detail_end();
		echo form_close();
		echo br(10);
	echo aba_end();

	$this->load->view('footer');
?>