<?php
set_title('Bloqueto (Autoatendimento)');
$this->load->view('header');
?>
<script>
	function salvar(form)
	{
		var arquivo = $("#userfile").val();
	
		if($("#userfile").val() == "")
		{
			alert("Informe o Arquivo para Envio");
			$("#userfile").focus();
			return false;
		}

		if($("#tp_origem").val() == "")
		{
			alert("Informe a Origem.");
			$("#tp_origem").focus();
			return false;
		}
	
		if($("#dt_envio_banco").val() == "")
		{
			alert("Informe a Data de Envio para o Banrisul");
			$("#dt_envio_banco").focus();
			return false;
		}
		
		if($("#dt_envio_participantes").val() == "")
		{
			alert("Informe a Data de Envio do E-mail para Participantes");
			$("#dt_envio_participantes").focus();
			return false;
		}
		else
		{
			var dt_envio_participantes = Date.fromString($('#dt_envio_participantes').val());
			dt_envio_participantes.zeroTime();
			var dt_envio_banco = Date.fromString($('#dt_envio_banco').val());
			dt_envio_banco.zeroTime();
			
			if(dt_envio_participantes <= dt_envio_banco)
			{
				alert("A Data de Envio do E-mail para Participantes deve ser Posterior a Data de Envio para o Banrisul.");
				$("#dt_envio_participantes").focus();
				return false;
			}
			
			var dt_minima = new Date();
			dt_minima.addDays(+1);
			dt_minima.zeroTime();
			
			if(dt_envio_participantes < dt_minima)
			{
				alert("A Data de Envio do E-mail para Participantes não pode ser Menor que Hoje + 1 dia.");
				$("#dt_envio_participantes").focus();
				return false;
			}
		}
		
		if($("#dt_bloqueio").val() == "")
		{
			alert("Informe a Data de Bloqueio do acesso para os Participantes");
			$("#dt_bloqueio").focus();
			return false;
		}	
		else
		{
			var dt_bloqueio = Date.fromString($('#dt_bloqueio').val());
			dt_bloqueio.zeroTime();
			var dt_envio_banco = Date.fromString($('#dt_envio_banco').val());
			dt_envio_banco.zeroTime();
			
			if(dt_bloqueio <= dt_envio_banco)
			{
				alert("A Data de Bloqueio do acesso para os Participantes deve ser Posterior a Data de Envio para o Banrisul.");
				$("#dt_bloqueio").focus();
				return false;
			}
		}		
		
		if((arquivo.substr(arquivo.lastIndexOf('.'),arquivo.length)).toLowerCase()!='.txt')
		{
			alert('Tipo de arquivo inválido.\n\nSomente arquivos .TXT');
			return false;
		} 		

		if(confirm("ATENÇÃO!\nEste processo ENVIARÁ EMAIL(S) aos PARTICIPANTES deste arquivo, em "+$('#dt_envio_participantes').val()+" às 9 horas.\n\nConfirma?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			form.submit();
		}
		
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto"); ?>';
	}
	
	function ir_lista_bloqueto()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto/bloqueto"); ?>';
	}	
	
	$(function(){
		<?php
		/*
		if($fl_retorno == "OK")
		{
			echo "alert('Total de linhas: ".$qt_linha."\\n\\nTotal de RE: ".$qt_registro_empregado."');";
			echo "ir_lista();";
		}
		*/
		?>
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Bloquetos Disponível',    FALSE, 'ir_lista_bloqueto();');
$abas[] = array('aba_detalhe', 'Enviar Arquivo', TRUE, 'location.reload();');

$origem = array(
	array('value' => 'P', 'text' => 'Patroc'),
	array('value' => 'T', 'text' => 'Patroc - Ex-autárquicos'),
	array('value' => 'A', 'text' => 'Autopatroc'),
	array('value' => 'E', 'text' => 'Acordos - Empréstimos'),
);

echo aba_start($abas);
	echo form_open_multipart('ecrm/auto_atendimento_bloqueto/envia_arquivo');
		echo form_start_box( "default_box", "Envio de arquivo" );
			echo form_default_row("","Arquivo (.TXT): (*)","<input type='file' name='userfile' id='userfile'>");
			echo form_default_dropdown('tp_origem', 'Origem: (*)', $origem);
			echo form_default_date('dt_envio_banco', 'Dt de Envio arquivo para o Banrisul: (*)');
			echo form_default_date('dt_envio_participantes', 'Dt Agenda do Envio do E-mail para Participantes: (*)');
			echo form_default_date('dt_bloqueio', 'Dt Agenda o Bloqueio do acesso para Participantes: (*)');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Enviar");
			echo button_save("Cancelar", "location.href='".site_url('ecrm/auto_atendimento_bloqueto')."'", "botao_disabled");
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>