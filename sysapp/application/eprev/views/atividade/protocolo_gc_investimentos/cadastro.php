<?php
	set_title('Controle de Documentos GC Investimento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('docuemtno')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/protocolo_gc_investimentos') ?>";
	}
    
    function ir_acompanhamento()
	{       
       location.href = "<?= site_url('atividade/protocolo_gc_investimentos/acompanhamento/'.$row['cd_protocolo_gc_investimentos']) ?>";
	}

	function enviar_gc()
	{
		var confirmacao = 'Deseja enviar o controle?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('atividade/protocolo_gc_investimentos/enviar_gc/'.$row['cd_protocolo_gc_investimentos']) ?>";
		}
	}
	
	function recusar()
	{       
   		location.href = "<?= site_url('atividade/protocolo_gc_investimentos/recusar/'.$row['cd_protocolo_gc_investimentos']) ?>";
	}
	
	function receber()
	{
		var confirmacao = 'Deseja receber o controle?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('atividade/protocolo_gc_investimentos/receber/'.$row['cd_protocolo_gc_investimentos']) ?>";
		}
	}
	
	function encerrar()
	{
		var confirmacao = 'Deseja encerrar o controle?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('atividade/protocolo_gc_investimentos/encerrar/'.$row['cd_protocolo_gc_investimentos']) ?>";
		}
	}

	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url('atividade/protocolo_gc_investimentos/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var usuario = $("#cd_usuario_sg"); 
									
			if(usuario.prop) 
			{
				var usuario_opt = usuario.prop("options");
			}
			else
			{
				var usuario_opt = usuario.attr("options");
			}

			$("option", usuario).remove();

			usuario_opt[usuario_opt.length] = new Option("Selecione", "");

			$.each(data, function(val, text) {
				usuario_opt[usuario_opt.length] = new Option(text.text, text.value);
			});

		}, "json", true);
	}

	function habilita_ar()
	{
		if($("#fl_retorno").val() == "S")
		{
			$("#arquivo_row").show();
			$("#ds_doc_pendente_row").show();
		}
		else
		{
			$("#arquivo_row").hide();
			$("#ds_doc_pendente_row").hide();
		}
	}

	$(function(){
		habilita_ar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_protocolo_gc_investimentos']) > 0)
	{
	    $abas[] = array('aba_acompanhemento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	}

	$retorno = array(
		array('value' => 'S', 'text' => 'Se aplica'),
		array('value' => 'N', 'text' => 'Não se aplica')
	);

	$fl_editar_anexo = false;

	if((trim($row['dt_encerrar']) == '') AND ((trim($row['dt_envio_gc']) == '') OR (trim($row['dt_recebido']) != '')))
	{
		$fl_editar_anexo = true;
	}
	
	echo aba_start($abas);
		echo form_open('atividade/protocolo_gc_investimentos/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_protocolo_gc_investimentos', '', $row);	

				if(trim($row['dt_envio_gc']) == '')
				{
					echo form_default_text('documento', 'Documento: (*)', $row, 'style="width:300px;"');
					echo form_default_hidden('fl_retorno', '', $row);
					
				}
				else
				{
					echo form_default_hidden('documento', '', $row);
					echo form_default_row('', 'Documento:', $row['documento']);
					echo form_default_dropdown('fl_retorno', 'Retorno:', $retorno, $row['fl_retorno'], 'onchange="habilita_ar();"');
				}

				echo form_default_upload_iframe(
                    'arquivo', 
                    'protocolo_gc_investimentos', 
                    'AR:', 
                    array($row['arquivo'], $row['arquivo_nome']),
                    'protocolo_gc_investimentos', 
                    $fl_editar_anexo
                );

                echo form_default_text('ds_doc_pendente', 'Doc. Pendente:', $row, 'style="width:300px;"');

				echo form_default_textarea('observacao', 'Observação:', $row, (trim($row['dt_envio_gc']) != '' ? 'style="border: 0px;" readonly' : '')) ;
				
				if(trim($row['dt_envio_gc']) != '')
				{
					echo form_default_row('dt_envio_gc', 'Dt Envio:', $row['dt_envio_gc']);
					echo form_default_row('ds_usuario_envio_gc', 'Envio por:', $row['ds_usuario_envio_gc']);
				}
				
				if(trim($row['dt_recebido']) != '')
				{
					echo form_default_row('dt_recebido', 'Dt Recebido:', $row['dt_recebido']);
					echo form_default_row('ds_usuario_recebido', 'Recebido por:', $row['ds_usuario_recebido']);
					echo form_default_date('dt_envio_sg', 'Dt Envio SG:', $row);
					echo form_default_gerencia('cd_gerencia_sg', 'Gerência:', $row['cd_gerencia_sg'], 'onchange="get_usuarios(this.value)"');
					echo form_default_dropdown('cd_usuario_sg', 'Usuário:', $usuario, $row['cd_usuario_sg']);
					echo form_default_date('dt_expedicao', 'Dt Expedição:', $row);
				}
				
				if(trim($row['dt_recusado']) != '')
				{
					echo form_default_row('dt_recusado', 'Dt Recusado:', $row['dt_recusado']);
					echo form_default_row('ds_usuario_recusado', 'Recusado por:', $row['ds_usuario_recusado']);
					echo form_default_textarea('ds_justificativa', 'Justificativa:', $row['ds_justificativa']);
				}
				
				if(trim($row['dt_encerrar']) != '')
				{
					echo form_default_row('dt_encerrar', 'Dt Encerrado:', $row['dt_encerrar']);
					echo form_default_row('ds_usuario_encerrar', 'Encerrado por:', $row['ds_usuario_encerrar']);
				}

			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(trim($row['dt_encerrar']) == '')
				{
					if((trim($row['dt_envio_gc']) == '') OR (trim($row['dt_recebido']) != ''))
					{
						echo button_save('Salvar');	
					}
					
					if((trim($row['dt_envio_gc']) == '') AND (intval($row['cd_protocolo_gc_investimentos']) > 0))
					{
						echo button_save('Enviar GC', 'enviar_gc();', 'botao_verde');	
					} 
					else if((trim($row['dt_envio_gc']) != '') AND (trim($row['dt_recebido']) == ''))
					{
						echo button_save('Receber', 'receber();', 'botao_verde');	
						echo button_save('Recusar', 'recusar();', 'botao_vermelho');	
					}
					else if(trim($row['dt_recebido']) != '')
					{
						echo button_save('Encerrar', 'encerrar();', 'botao_vermelho');	
					}
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>