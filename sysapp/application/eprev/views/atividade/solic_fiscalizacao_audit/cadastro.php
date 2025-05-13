<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?
	if((trim($row['dt_envio']) == ''))
	{
		echo form_default_js_submit(array(
			'cd_solic_fiscalizacao_audit_origem',
			'dt_recebimento',
			'cd_solic_fiscalizacao_audit_tipo',
			'ds_teor',
			'ds_documento',
			'fl_prazo'
		), 'valida_formulario(form)');
	}
	else
	{
		echo form_default_js_submit(array(
			'fl_enviar_email',
			'dt_envio_atendimento',
		), 'valida_atendimento(form)');
	}
	?>
	
	function valida_formulario(form)
	{
		if($("#fl_especificar_origem").val() == "S" && $("#ds_origem").val() == "")
		{
			alert("Informe os campos obrigatórios!\n\n(Especificar Origem)");
			$("#ds_origem").focus();
			return false;
		}

		if($("#fl_especificar_tipo").val() == "S" && $("#ds_tipo").val() == "")
		{
			alert("Informe os campos obrigatórios!\n\n(Especificar Tipo)");
			$("#ds_tipo").focus();
			return false;
		}

		var fl_marcado = false;
		$("input[type='checkbox'][id='gerencia_opcional']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);	

		if($("#cd_gerencia").val() == "" && !fl_marcado)
		{
			alert("Informe uma área consolidadora ou um envio opcional!\n\n");
			return false;
		}

		if(($("#fl_prazo").val() == "N" || $("#fl_prazo").val() == "C") && $("#nr_dias_prazo").val() == "")
		{
			alert("Informe o nº de dias para o prazo!\n\n");
			return false;
		}
		else if($("#fl_prazo").val() == "D" && $("#dt_prazo").val() == "")
		{
			alert("Informe a data do przao!\n\n");
			return false;
		}

		if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function valida_atendimento(form)
    {
    	if($("#nr_correspondencia_ano").val() == '' && $("#ds_justificativa_atendimento").val() == '' && $("#arquivo").val() == '' && $("#arquivo_nome").val() == '')
    	{
    		alert('Prencha o campo Justificativa, Número da Correspondência ou anexe o arquivo.');
    	}
    	else
    	{
    		if(confirm("Salvar?"))
			{
				$("#form_atendimento").submit();
			}
    	}
    }

	function set_origem(cd_solic_fiscalizacao_audit_origem)
	{
		if(cd_solic_fiscalizacao_audit_origem != "")
		{
			$.post("<?= site_url('atividade/solic_fiscalizacao_audit/get_origem') ?>",
			{
				cd_solic_fiscalizacao_audit_origem : cd_solic_fiscalizacao_audit_origem
			},
			function(data)
			{
				if(data.fl_especificar == "S")
				{
					$("#ds_origem_row").show();
					$("#fl_especificar_origem").val("S");
				}
				else
				{
					$("#ds_origem_row").hide();
					$("#ds_origem").val("");
					$("#fl_especificar_origem").val("N");
				}
			}, "json");
		}
		else
		{
			$("#ds_origem_row").hide();
			$("#ds_origem").val("");
			$("#fl_especificar_origem").val("N");
		}
	}

	function set_tipo(cd_solic_fiscalizacao_audit_tipo)
	{
		if(cd_solic_fiscalizacao_audit_tipo != "")
		{
			$.post("<?= site_url('atividade/solic_fiscalizacao_audit/get_tipo') ?>",
			{
				cd_solic_fiscalizacao_audit_tipo : cd_solic_fiscalizacao_audit_tipo
			},
			function(data)
			{
				$("#tl_gestao").val(data.tl_gestao);

				if(data.fl_especificar == "S")
				{
					$("#ds_tipo_row").show();
					$("#fl_especificar_tipo").val("S");
				}
				else
				{
					$("#ds_tipo_row").hide();
					$("#ds_tipo").val("");
					$("#fl_especificar_tipo").val("N");
				}

				if(data.cd_gerencia != null)
				{
					$("#cd_gerencia").val(data.cd_gerencia);
				}
				else
				{
					$("#cd_gerencia").val("");
					$("#cd_gerencia").change("");
				}

				$("input[name=gestao_item[]]").each(function(i, obj){

					if(jQuery.inArray($(this).val(), data.gestao) > -1)
					{
						$(this).attr("checked", true); 
					}
					else
					{
						$(this).removeAttr("checked");
					}
				});

			}, "json");
		}
		else
		{
			$("#ds_tipo_row").hide();
			$("#ds_tipo").val("");
			$("#fl_especificar_tipo").val("N");

			$("#cd_gerencia").val("");
		}
	}

    function set_prazo(fl_prazo)
    {
    	if(fl_prazo == "N" || fl_prazo == "C")
		{
			$("#nr_dias_prazo_row").show();
			$("#dt_prazo_row").hide();

			$("#dt_prazo").val("");
		}
		else if(fl_prazo == "D")
		{
			$("#dt_prazo_row").show();
			$("#nr_dias_prazo_row").hide();

			$("#nr_dias_prazo").val("");
		}
		else
		{
			$("#nr_dias_prazo_row").hide();
			$("#dt_prazo_row").hide();

			$("#nr_dias_prazo").val("");
			$("#dt_prazo").val("");
		}
    }

    function gerar_pdf()
    {
    	window.open("<?= site_url('atividade/solic_fiscalizacao_audit/pdf/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>");
    }

    function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit') ?>";
    }

    function ir_prorrogacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_acompanhamento()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/acompanhamento/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_documentacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function enviar()
    {
    	var confirmacao = 'Deseja enviar e-mail?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/enviar/'.$row['cd_solic_fiscalizacao_audit']) ?>";
        }
    }

    $(function(){    	
    	if($("#fl_especificar_origem").val() == "S")
		{
			$("#ds_origem_row").show();
		}
		else
		{
			$("#ds_origem_row").hide();
			$("#ds_origem").val("");
		}
    	
    	if($("#fl_especificar_tipo").val() == "S")
		{
			$("#ds_tipo_row").show();
		}
		else
		{
			$("#ds_tipo_row").hide();
			$("#ds_tipo").val("");
		}

		set_prazo($("#fl_prazo").val());
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_solic_fiscalizacao_audit']) > 0 AND (trim($row['dt_envio']) != ''))
	{
		$abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', FALSE, 'ir_prorrogacao();');
	}

	if(intval($row['cd_solic_fiscalizacao_audit']) > 0)
	{
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	}

	if(intval($row['cd_solic_fiscalizacao_audit']) > 0 AND (trim($row['dt_envio']) != ''))
	{
		$abas[] = array('aba_documentacao', 'Documentação/Informação', FALSE, 'ir_documentacao();');
	}

	$tipo_prazo = array(
		array('value' => 'N', 'text' => 'Nº Dias Úteis'),
		array('value' => 'C', 'text' => 'Nº Dias Corridos'),
		array('value' => 'D', 'text' => 'Data'),
	);
	
	echo aba_start($abas);
		echo form_open('atividade/solic_fiscalizacao_audit/salvar');
			if(intval($cd_correspondencia_recebida_item) > 0)
			{
				echo form_start_box('correspondencia_recebida_box', 'Correspondência Recebida');
					echo form_default_hidden('cd_correspondencia_recebida_item', '', $cd_correspondencia_recebida_item);
					echo form_default_row('', 'Ano/Número:', $correspondencia_recebida['nr_ano_numero']);
					echo form_default_row('', 'DT. Correspondência:', $correspondencia_recebida['dt_correspondencia']);
					echo form_default_row('', 'Origem:', $correspondencia_recebida['origem']);
					echo form_default_row('', 'Tipo:', $correspondencia_recebida['ds_correspondencia_recebida_tipo']);
					echo form_default_row('', 'Ver Protocolo:', anchor('ecrm/correspondencia_recebida/receber/'.$correspondencia_recebida['cd_correspondencia_recebida'], '<u>[link]</u>', array('target' => '_blank')));

				echo form_end_box('correspondencia_recebida_box');
			}

			echo form_start_box('default_box', 'Cadastro');
				if(intval($row['cd_solic_fiscalizacao_audit']) > 0)
				{
					echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
				}
				echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
				echo form_default_hidden('fl_especificar_origem', '', $row['fl_especificar_origem']);
				echo form_default_hidden('fl_especificar_tipo', '', $row['fl_especificar_tipo']);
				echo form_default_hidden('tl_gestao', '', $row['tl_gestao']);
				
				echo form_default_row('', 'Origem: (*)', '<i style="color:red">Remetente</i>');
				echo form_default_dropdown('cd_solic_fiscalizacao_audit_origem', '', $origem, $row['cd_solic_fiscalizacao_audit_origem'], 'onchange="set_origem($(this).val())"');
				echo form_default_text('ds_origem', 'Especificar Origem: (*)', $row, 'style="width:350px;"');
				
				echo form_default_row('', 'Dt. Recebimento: (*)', '<i style="color:red">Data da chegada do documento</i>');
				echo form_default_date('dt_recebimento', '', $row);	

				echo form_default_dropdown_optgroup('cd_solic_fiscalizacao_audit_tipo', 'Tipo: (*)', $tipo, array($row['cd_solic_fiscalizacao_audit_tipo']), 'onchange="set_tipo($(this).val())"');
				echo form_default_text('ds_tipo', 'Especificar Tipo: (*)', $row, 'style="width:350px;"');

				echo form_default_dropdown('cd_gerencia', 'Área Consolidadora:', $gerencia, $row['cd_gerencia']);
				echo form_default_checkbox_group('gestao_item', 'Gestão:', $gestao, $row['gestao'], 150, 350);

				echo form_default_checkbox_group('gerencia_opcional', 'Envio Opcional:', $gerencia, $row['gerencia_opcional'], 150, 350);

				echo form_default_checkbox_group('grupo_opcional', 'Envio Opcional (Grupo):', $grupos, $row['grupo_opcional'], 150, 350);

				echo form_default_row('', 'Documento: (*)', '<i style="color:red">Nome do Documento</i>');
				echo form_default_text('ds_documento', '', $row, 'style="width:400px;"');
				
				echo form_default_row('', 'Teor: (*)', '<i style="color:red">Especificar o conteúdo do documento</i>');
				echo form_default_text('ds_teor', '', $row, 'style="width:400px;"');

				echo form_default_row('', 'Prazo: (*)', '<i style="color:red">Prazo Final para atendimento</i>');
				echo form_default_dropdown('fl_prazo', '', $tipo_prazo, $row['fl_prazo'], 'onchange="set_prazo($(this).val())"');
				echo form_default_integer('nr_dias_prazo', 'Prazo (dias) (*):', $row);
				echo form_default_date('dt_prazo', 'Dt. para Providências: (*)', $row);
			
				if(trim($row['dt_envio']) == '')
				{
					echo form_default_upload_iframe('arquivo', 'solic_fiscalizacao_audit', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'solic_fiscalizacao_audit', TRUE);
				}
				else
				{
					$ext = pathinfo($row['arquivo'], PATHINFO_EXTENSION);

					if(in_array($ext, array('tif', 'pdf', 'png', 'jpg', 'jpeg', 'bmp', 'svg')))
					{
						$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento_liquid/'.$row['cd_liquid'];
					}
					else
					{
						$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento/'.$row['cd_liquid'].'/'.$ext;
					}

					$link_documento = base_url('./up/solic_fiscalizacao_audit/'.$row['arquivo']);

					echo form_default_row('', 'Arquivo:', anchor($link_documento, '[ver arquivo]', array('target' => '_blank')));
				}
				
				if(intval($row['cd_solic_fiscalizacao_audit']) > 0)
				{
					echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
					echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);

					if(trim($row['dt_envio']) != '')
					{
						echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
						echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
					}

					if(trim($row['dt_envio_solicitacao_documento']) != '')
					{
						echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
						echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
					}
				}

			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(gerencia_in(array('GC', 'GRC', 'AI')) AND (trim($row['dt_envio']) == ''))
       			{
					echo button_save('Salvar');  

					if(intval($row['cd_solic_fiscalizacao_audit']) > 0)
					{
						echo button_save('Enviar', 'enviar()', 'botao_vermelho');  
					}
				}    
				/*
				if(intval($row['cd_solic_fiscalizacao_audit']) > 0)
	    		{
	    			echo button_save('PDF', 'gerar_pdf()', 'botao_verde');
	    		}
				*/
		    echo form_command_bar_detail_end();
		echo form_close();

		if($fl_atendimento)
		{
			echo form_open('atividade/solic_fiscalizacao_audit/salvar_atendimento_correspondencia', 'id="form_atendimento"');
				echo form_start_box('default_atendimento_box', 'Atendimento');
					echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
					echo form_default_dropdown('fl_enviar_email', 'Enviar e-mail:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), 'S');
					echo form_default_date('dt_envio_atendimento', 'Dt. Atendimento: (*)', $row);
					echo form_default_integer_ano('nr_correspondencia_ano', 'nr_correspondencia_numero', 'Número da correspondência FCEEE:', $row['nr_correspondencia_ano'], $row['nr_correspondencia_numero']);
					echo form_default_upload_iframe('arquivo_atendimento', 'solic_fiscalizacao_audit', 'Arquivo:', array($row['arquivo_atendimento'], $row['arquivo_atendimento_nome']), 'solic_fiscalizacao_audit', TRUE);
					echo form_default_textarea('ds_justificativa_atendimento', 'Justificativa:', $row['ds_justificativa_atendimento'], 'style="width: 400px; height: 80px;"');
					
					if(trim($row['ds_link_acesso']) != '')
					{
						echo form_default_row('', 'Link de Acesso:', $row['ds_link_acesso']);
						echo form_default_row('', 'Chave de Acesso:', $row['ds_chave_acesso']);
					}
					
				echo form_end_box('default_atendimento_box');
			echo form_close();
			echo form_command_bar_detail_start();
				echo button_save('Salvar'); 
			echo form_command_bar_detail_end();	
		}
		else if(trim($row['ds_link_acesso']) != '')
		{
			echo form_start_box('default_atendimento_box', 'Atendimento');
				echo form_default_row('', 'Link de Acesso:', $row['ds_link_acesso']);
				echo form_default_row('', 'Chave de Acesso:', $row['ds_chave_acesso']);
			echo form_end_box('default_atendimento_box');
		}
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>