<?php 
set_title('Campos');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(

		array("nome", "str") 

	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/cadastro_instancia"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('gestao/cadastro_instancia/salvar');
echo form_hidden( 'cd_instancia', intval($row['cd_instancia']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Campos" );
echo form_default_text("nome", "Nome *", $row, "style='width:300px;'", "100");
echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('gestao/cadastro_instancia')."'; }");
echo form_command_bar_detail_end();
?>
<script>
	// $('{PRIMEIRO_CAMPO}').focus();
</script>
<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
?>