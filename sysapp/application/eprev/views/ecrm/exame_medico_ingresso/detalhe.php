<?php
set_title('Exame Médico Ingresso - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nome','pedido_inscricao_local'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/exame_medico_ingresso"); ?>';
	}
	
	function ir_acompanhamento(cd_exame_medico_ingresso)
	{
		location.href='<?php echo site_url("ecrm/exame_medico_ingresso/acompanhamento"); ?>' + "/" + cd_exame_medico_ingresso;
	}	
	
	function exameMedicoExcluir(cd_exame_medico_ingresso)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/exame_medico_ingresso/exameMedicoExcluir"); ?>' + "/" + cd_exame_medico_ingresso;
		}
	}
	
	function exameMedicoEnviar(cd_exame_medico_ingresso)
	{
		if(confirm("Deseja enviar?"))
		{
			location.href='<?php echo site_url("ecrm/exame_medico_ingresso/exameMedicoEnviar"); ?>' + "/" + cd_exame_medico_ingresso;
		}
	}	

	function exameMedicoReceber(cd_exame_medico_ingresso)
	{
		if(confirm("Deseja receber?"))
		{
			location.href='<?php echo site_url("ecrm/exame_medico_ingresso/exameMedicoReceber"); ?>' + "/" + cd_exame_medico_ingresso;
		}
	}	
	
	function carregar_dados_participante(data)
	{
		$('#nome').val(data.nome);
		
		if(data.email != "")
		{
			$('#email').val(data.email);
		}
		else if (data.email_profissional != "")
		{
			$('#email').val(data.email_profissional);
		}
		else
		{
			$('#email').val("");
		}
		
		if ((data.ddd != "000") && (data.telefone != "0"))
		{
			$('#telefone').val("(" + data.ddd + ") " + data.telefone);
		}
		else
		{
			$('#telefone').val("");
		}		
		
		if ((data.ddd_celular != "000") && (data.celular != "0"))
		{
			$('#celular').val("(" + data.ddd_celular + ") " + data.celular);
		}
		else
		{
			$('#celular').val("");
		}		
		
		if ((data.ddd_outro != "000") && (data.telefone_outro != "0"))
		{
			$('#telefone_comercial').val("(" + data.ddd_outro + ") " + data.telefone_outro);
		}
		else
		{
			$('#telefone_comercial').val("");
		}

	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	if(intval($row['cd_exame_medico_ingresso']) > 0)
	{
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, "ir_acompanhamento(".intval($row['cd_exame_medico_ingresso']).");");
	}
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/exame_medico_ingresso/exameMedicoSalvar');
	echo form_start_box( "default_box", "Exame Médico" );
		echo form_default_text('cd_exame_medico_ingresso', "Código: ", intval($row['cd_exame_medico_ingresso']), "style='width:100%;border: 0px;' readonly" );
		if($row['dt_inclusao'] != "")
		{
			echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_usuario_inclusao', "Cadastrado por: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
		if($row['dt_exclusao'] != "")
		{
			echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_usuario_inclusao', "Excluído por: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		

		$p['emp']['id']    = 'cd_empresa';
		$p['re']['id']     = 'cd_registro_empregado';
		$p['seq']['id']    = 'seq_dependencia';
		$p['emp']['value'] = $row['cd_empresa'];
		$p['re']['value']  = $row['cd_registro_empregado'];
		$p['seq']['value'] = $row['seq_dependencia'];
		$p['caption']      = 'Participante';
		$p['callback']     = 'carregar_dados_participante';
		echo form_default_participante_trigger($p);	
		echo form_default_text('nome', "Nome:* ", $row, "style='width:500px;'");		
		echo form_default_text('telefone', "Telefone: ", $row);	
		echo form_default_text('celular', "Celular: ", $row);	
		echo form_default_text('telefone_comercial', "Telefone Comercial: ", $row);	
		echo form_default_text('email', "E-mail: ", $row, "style='width:100%;'");
		
		$ar_exibe_resultado = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'GAP', 'value' => 'GAP'),Array('text' => 'GAD', 'value' => 'GAD'),Array('text' => 'Partipante', 'value' => 'Partipante')) ;
		echo form_default_dropdown('pedido_inscricao_local', 'Pedido de inscrição na:*', $ar_exibe_resultado, Array($row['pedido_inscricao_local']));		
		
		
		if($row['dt_envio_exame'] != "")
		{
			echo form_default_text('dt_envio_exame', "Enviado em: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_usuario_envio_exame', "Enviado por: ", $row, "style='width:100%;border: 0px;' readonly" );
		}	

		if($row['dt_recebido_exame'] != "")
		{
			echo form_default_text('dt_recebido_exame', "Recebido em: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_usuario_recebido_exame', "Recebido por: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if($row['dt_exclusao'] == "")
		{
			if($row['dt_envio_exame'] == "")
			{
				echo button_save("Salvar");
			}
			
			if((intval($row['cd_exame_medico_ingresso']) > 0) and ($row['dt_envio_exame'] == "") and (gerencia_in(array('GAP'))))
			{
				echo button_save("Enviar","exameMedicoEnviar(".$cd_exame_medico_ingresso.")","botao_disabled");
				echo button_save("Excluir","exameMedicoExcluir(".$cd_exame_medico_ingresso.")","botao_vermelho");
			}	

			if((intval($row['cd_exame_medico_ingresso']) > 0) and ($row['dt_envio_exame'] != "") and ($row['dt_recebido_exame'] == "") and (gerencia_in(array('GAD'))))
			{
				echo button_save("Receber","exameMedicoReceber(".$cd_exame_medico_ingresso.")");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
	echo aba_end();
?>
<script>
	jQuery(function($)
	{
	   $("#telefone").mask("(999) 99999999");
	   $("#celular").mask("(999) 99999999");
	   $("#telefone_comercial").mask("(999) 99999999");
	});
	
</script>
<?php
	$this->load->view('footer_interna');
?>