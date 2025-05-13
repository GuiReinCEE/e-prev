<?php 
set_title('Habilidades');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("descricao", "str") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_habilidade"); ?>';
	}
</script>
<?php
if( usuario_administrador_avaliacao() )
{
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
	echo aba_start( $abas );
	
	echo form_open('cadastro/avaliacao_habilidade/salvar');
	echo form_hidden( 'codigo', intval($row['codigo']) );
	
	// Registros da tabela principal ...
	echo form_start_box( "default_box", "Habilidades" );
	echo form_default_text("descricao", "Descrição *", $row, "style='width:300px;'", "100"); 
	echo form_default_textarea("obs", "Observações", $row, "style='width:600px;'", "0"); 

	echo form_end_box("default_box");
	
	// Barra de comandos ...
	echo form_command_bar_detail_start();
	echo button_save();
	
	echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('cadastro/avaliacao_habilidade')."'; }");
	echo form_command_bar_detail_end();
}
else
{
	echo "<br><br><center>Cadastro de escolaridade permitido apenas para gerentes e diretores.</center>";
}
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