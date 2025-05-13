<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?php 
		if(trim($row['dt_solic_prorrogacao']) == '')
		{
			echo form_default_js_submit(array('dt_solicitacao_prorrogacao', 'arquivo_minuta', 'arquivo_minuta_nome'), '_salvar(form)');
		}
		else
		{
			echo form_default_js_submit(array('fl_confirmacao', 'dt_prazo_porrogado'), '_salvar_confirmacao(form)');
		}
	?>

	function _salvar_confirmacao(form)
	{
		if($("#fl_confirmacao").val() == 'N')
		{
			if($("#ds_confirmacao_prorrogacao").val() == '')
			{
				alert("Informe a descrição da não confirmação do prazo!\n\n");
				return false;
			}
			else
			{
				var confirmacao = 'Deseja não confirmar a Prorrogação do Prazo e Encaminhar para a Área Consolidadora?\n\n'+
		            'Clique [Ok] para Sim\n\n'+
		            'Clique [Cancelar] para Não\n\n';
			}
		}
		else
		{
			var confirmacao = 'Deseja confirmar a Prorrogação do Prazo e Encaminhar para todos os envolvidos?\n\n'+
		            'Clique [Ok] para Sim\n\n'+
		            'Clique [Cancelar] para Não\n\n';
		}

		if(confirm(confirmacao))
		{
			form.submit();
		}
	}

	function _salvar(form)
	{
		var confirmacao = 'Deseja Solicitar a Prorrogação do Prazo e Encaminhar para a GRC?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			form.submit();
		}
	}

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit') ?>";
    }

    function ir_cadastro()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_acompanhamento()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/acompanhamento/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_documentacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function set_confirmacao(fl_confirmacao)
    {
    	if(fl_confirmacao == 'N')
    	{
    		$("#ds_confirmacao_prorrogacao_row").show();
    	}
    	else
    	{
    		$("#ds_confirmacao_prorrogacao_row").hide();
    		$("#ds_confirmacao_prorrogacao").val("");
    	}

    	if(fl_confirmacao == 'S')
    	{
    		$("#dt_prazo_porrogado_row").show();
    		$("#arquivo_pedido_row").show();
    		$("#dt_prazo_porrogado").val("<?= $row['dt_solicitacao_prorrogacao'] ?>");
    	}
    	else
    	{
    		$("#dt_prazo_porrogado_row").hide();
    		$("#dt_prazo_porrogado").val("");

       		$("#arquivo_pedido_row").hide();
    		$("#arquivo_pedido").val("");
    		$("#arquivo_pedido_nome").val("");
    	}
    }

    $(function(){
    	$("#ds_confirmacao_prorrogacao_row").hide();
    	$("#dt_prazo_porrogado_row").hide();
        $("#arquivo_pedido_row").hide();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', TRUE, 'location.reload();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

	if(trim($row['dt_envio']) != '')
	{
		$abas[] = array('aba_documentacao', 'Documentação/Informação', FALSE, 'ir_documentacao();');
	}

	$confirmacao = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
		echo form_start_box('default_solicitacao_box', 'Solicitação');
			echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
			echo form_default_row('', 'Origem:', $row['ds_solic_fiscalizacao_audit_origem'].(trim($row['ds_origem']) != '' ? ' ('.$row['ds_origem'].')' : ''));
			echo form_default_row('', 'Data Recebimento:', $row['dt_recebimento']);
			echo form_default_row('', 'Tipo:', $row['ds_solic_fiscalizacao_audit_tipo'].(trim($row['ds_tipo']) != '' ? ' ('.$row['ds_tipo'].')' : ''));
			echo form_default_row('', 'Área Consolidadora:', $row['cd_gerencia']);
			
			if(count($row['gestao']) > 0)
			{
				echo form_default_row('', 'Gestão:', implode(', ', $row['gestao']));
			}

			echo form_default_row('', 'Documento:', $row['ds_documento']);
			echo form_default_row('', 'Teor:', $row['ds_teor']);
			echo form_default_row('', 'Dt. Prazo:', $row['dt_prazo']);
			echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
			echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);
			echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
			echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);

			if(trim($row['dt_envio_solicitacao_documento']) != '')
			{
				echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
			}

			if(trim($row['dt_prazo_porrogado']) != '')
			{
				echo form_default_row('', 'Minuta de Prorrogação:', anchor(base_url().'up/solic_fiscalizacao_audit/'.$row['arquivo_minuta'], '[ver arquivo]', array('target' => '_blank')));
				
				$ext = pathinfo($row['arquivo_pedido'], PATHINFO_EXTENSION);

				if(in_array($ext, array('tif', 'pdf', 'png', 'jpg', 'jpeg', 'bmp', 'svg')))
				{
					$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento_liquid/'.$row['cd_liquid_minuta'];
				}
				else
				{
					$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento/'.$row['cd_liquid_minuta'].'/'.$ext;
				}

				//echo form_default_row('', 'Pedido de Prorrogação:', anchor($link_documento, '[ver arquivo]', array('target' => '_blank')));	
				echo form_default_row('', 'Pedido de Prorrogação:', anchor(base_url().'up/solic_fiscalizacao_audit/'.$row['arquivo_pedido'], '[ver arquivo]', array('target' => '_blank')));
			}
		echo form_end_box('default_solicitacao_box');
		if(trim($row['dt_prazo_porrogado']) == '')
		{
			echo form_open('atividade/solic_fiscalizacao_audit/salvar_prorrogacao');
				echo form_start_box('default_prorrogacao_box', 'Solicitação de Prorrogação');
					echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);

					if(trim($row['dt_prazo_porrogado']) != '')
					{
						echo form_default_row('', '', '<span class="label label-info">PRORROGADO</span>');
					}
					else if(trim($row['dt_solic_prorrogacao']) != '')
					{
						echo form_default_row('', '', '<span class="label label-warning">AGUARDANDO CONFIRMAÇÃO</span>');
					}

					echo form_default_date('dt_solicitacao_prorrogacao', 'Dt. Prorrogação: (*)', $row);
					echo form_default_upload_iframe('arquivo_minuta', 'solic_fiscalizacao_audit', 'Minuta de Prorrogação: (*)', array($row['arquivo_minuta'], $row['arquivo_minuta_nome']), 'solic_fiscalizacao_audit');
					echo form_default_textarea('ds_solicitacao_prorrogacao', 'Descrição:', $row, 'style="height:80px;"');
					
					if(trim($row['dt_solic_prorrogacao']) != '')
					{
						echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_solic_prorrogacao']);
						echo form_default_row('', 'Usuário:', $row['ds_usuario_solic_prorrogacao']);
					}

				echo form_end_box('default_prorrogacao_box');
				echo form_command_bar_detail_start();
					if($fl_permissao_prorrogacao AND trim($row['dt_solic_prorrogacao']) == '' AND trim($row['dt_solic_prorrogacao']) == '')
					{
						echo button_save('Salvar');  
					}
				echo form_command_bar_detail_end();
			echo form_close();
		}

		if(trim($row['dt_solic_prorrogacao']) != '' AND trim($row['dt_prazo_porrogado']) == '' AND $fl_permissao_confirmacao)
		{
			echo form_open('atividade/solic_fiscalizacao_audit/confirma_prorrogacao');
				echo form_start_box('default_box', 'Confirmação de Prorrogação');
					echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
					echo form_default_dropdown('fl_confirmacao', 'Confirma a Prorrogação: (*)', $confirmacao, '', 'onchange="set_confirmacao($(this).val())"');
					echo form_default_textarea('ds_confirmacao_prorrogacao', 'Descrição: (*)', '', 'style="height:80px;"');
					echo form_default_date('dt_prazo_porrogado', 'Dt. Novo Prazo:', $row['dt_solicitacao_prorrogacao']);
					echo form_default_upload_iframe('arquivo_pedido', 'solic_fiscalizacao_audit', 'Pedido de Prorrogação: (*)', array($row['arquivo_pedido'], $row['arquivo_pedido_nome']), 'solic_fiscalizacao_audit');
				echo form_end_box('default_box');
				echo form_command_bar_detail_start();
					echo button_save('Salvar');  
				echo form_command_bar_detail_end();
			echo form_close();
		}

		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');

?>