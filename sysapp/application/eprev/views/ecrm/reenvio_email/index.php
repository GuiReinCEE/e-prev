<?php 
set_title('Reenvio de Email');
$this->load->view('header'); 
?>
<script>

	function salvar(form)
	{
		if(confirm('ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja REENVIAR o email?'))
		{
			form.submit();
		}
	}	
	
	function listarAnexo()
	{
		$.post('<?php echo base_url() . index_page(); ?>/ecrm/reenvio_email/listarAnexo',
		{
			cd_email : $('#cd_email').val()
		},
		function(data)
		{
			$('#obListarAnexo').html(data);
		});
	}	
	
	function buscaLink()
	{
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/reenvio_email/listaLink'
			,{
				cd_email : $('#cd_email').val()
			}
			,
			function(data)
			{
				$('#obListaLink').html(data);
			}
		);
	}	
	
	$(document).ready(function() 
	{
		if($("#cd_email").val() != "")
		{
			listarAnexo();
			buscaLink();
		}
	});	
</script>
<?php
	$abas[] = array('aba_detalhe', 'Reenvio', true, 'location.reload();');
	echo aba_start( $abas );
	echo form_open('ecrm/reenvio_email/salvar');
	echo form_start_box( "default_box", "Email" );

	echo form_default_text('cd_email', "Código Email: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('qt_email_filho', "Qt E-mail Descendente: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_row("","Código E-mail Origem:", anchor("ecrm/reenvio_email/index/".$row["cd_email_pai"], $row["cd_email_pai"], array("target" => "_blank")));
	echo form_default_hidden('cd_email_pai', 'Código Email Origem', $row['cd_email_pai']);
	echo form_default_text('dt_envio', "Dt Cadastro: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('nome_usuario', "Usuário: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('dt_email_enviado', "Dt Envio: ", ($row['dt_email_enviado'] == "" ? "Aguardando Envio" : $row['dt_email_enviado']), "style='width:100%;border: 0px; ".($row['dt_email_enviado'] == "" ? "color: blue; font-weight:bold;" : "")."' readonly" );
	echo form_default_text('dt_schedule_email', "Dt Envio Agendado: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('fl_retornou', "Situação: ", ($row['fl_retornou'] == "S" ? "Retornou" : "Normal"), "style='width:100%;border: 0px; font-weight:bold; ".($row['fl_retornou'] == "S" ? "color:Red;" : "color:Green;")."' readonly" );
	
	$participante['cd_empresa']            = $row['cd_empresa'];
	$participante['cd_registro_empregado'] = $row['cd_registro_empregado'];
	$participante['seq_dependencia']       = $row['seq_dependencia'];
	$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	echo form_default_participante( $conf, "Participante:", $participante, TRUE, FALSE );
	echo form_default_text('nome', "Nome: ", $row, "style='width:100%;border: 0px;' readonly" );

	echo form_default_integer('cd_divulgacao', "Cód. Divulgação: ", $row, "style='width:100%;'");
	
	echo form_default_integer('cd_evento', "Cód. Evento: ", $row, "style='width:100%;'");
	echo form_default_text('ds_evento', "Evento: ", $row, "style='width:100%;border: 0px;' readonly" );
	
	echo form_default_text('de', "De: ", $row, "style='width:100%;'");
	echo form_default_text('para', "Para: ", $row, "style='width:100%;'");
	echo form_default_text('cc', "Com Cópia Para: ", $row, "style='width:100%;'");
	echo form_default_text('cco', "Com Cópia Oculta Para: ", $row, "style='width:100%;'");
	echo form_default_text('assunto', "Assunto: ", $row, "style='width:100%;'");
	
	if ((strtoupper(trim($row['formato'])) == "HTML") or (strtoupper(trim($row['formato'])) == "TEXT_HTML"))
	{
		echo form_default_editor_html('texto', "Texto:", $row, 'style="height: 300px;"');
	}
	else
	{
		echo form_default_textarea("texto", "Texto:" ,$row, "style='width:800px; height: 300px;'");
	}
	
	echo form_default_text('tp_email', "Tipo Email: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('formato', "Formato: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('fl_comprova', "Comprova: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_text('qt_anexo', "Qt Anexo: ", $row, "style='width:100%;border: 0px;' readonly" );
	echo form_default_row("","Anexo:", '<div id="obListarAnexo"></div>');

	echo form_end_box("default_box");
	// Barra de comandos ...
	echo form_command_bar_detail_start();
	echo button_save("Reenviar o email");
	echo form_command_bar_detail_end();
	echo form_start_box( "link_box", "Link" );
?>
<div id="obListaLink"><br><br><span style='color:green;'><b></b></span></div>
<?php	
	echo form_end_box("lista_box");
?>
<BR>
<BR>
<?php
	echo aba_end();
	// FECHAR FORM
	echo form_close();

	$this->load->view('footer_interna');
?>