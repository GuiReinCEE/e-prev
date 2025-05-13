<?php 
	set_title('Autoatendimento Usuário - Cadastro');
	$this->load->view('header'); 
?>
<script>
	
	<?php 
		echo form_default_js_submit(array("cd_usuario"));
	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_usuario"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start( $abas );

	echo form_open('ecrm/auto_atendimento_usuario/salvar');
	echo form_start_box( "default_box", "Usuário" );
		echo form_default_usuario_ajax("cd_usuario");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		echo button_save();
	echo form_command_bar_detail_end();

	echo aba_end();

	echo form_close();

	$this->load->view('footer_interna');
?>