<?php 
set_title('Ferramentas de desenvolvimento');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		

	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("dev/dev_tarefa"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('dev/dev_tarefa/salvar');
echo form_hidden( '', intval($row['']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Ferramentas de desenvolvimento" );

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row[''])>0  && false  )
{
	echo button_delete("dev/dev_tarefa/excluir",$row[""]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('dev/dev_tarefa')."'; }");
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