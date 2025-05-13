<?php
set_title('Reclamatória');
$this->load->view('header');
?>
<script>
	function salvar( form )
	{
	
		if( $("#observacao").val()=="" )
		{
			alert("Informe a Observação." );
			$("#observacao").focus();
			return false;
		}
		else
		{
			form.submit();
		}
		
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria"); ?>';
	}
	
	function ir_reclamatoria()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/detalhe/".$cd_atendimento_reclamatoria); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Reclamatória', FALSE, 'ir_reclamatoria();');
	$abas[] = array('aba_retorno', 'Retorno', TRUE, 'location.reload();');
	
	echo aba_start( $abas );

	echo form_open('ecrm/atendimento_reclamatoria/retorno_salvar');
	echo form_start_box( "default_box", "Registro" );
	echo form_hidden('cd_atendimento_reclamatoria', intval($cd_atendimento_reclamatoria));
	echo form_hidden('cd_atendimento_reclamatoria_retorno', intval($cd_atendimento_reclamatoria_retorno));
	echo form_default_textarea("observacao","Observação:*",$observacao);
	echo form_end_box("default_box");
	
	// Barra de comandos ...
	echo form_command_bar_detail_start();
	echo button_save("Salvar");
	echo form_command_bar_detail_end();
	
	echo aba_end();
	// FECHAR FORM
	echo form_close();

	$this->load->view('footer_interna');
?>