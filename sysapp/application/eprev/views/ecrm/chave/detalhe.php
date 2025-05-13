<?php 
set_title('Chaves');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_chave", "str") 
		,array("cd_sala", "int") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/chave"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/chave/salvar');
echo form_hidden( 'cd_chave', intval($row['cd_chave']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Chaves" );
echo form_default_text("ds_chave", "Chave *", $row, "style='width:300px;'", "0"); 
echo form_default_integer("cd_sala", "Sala *", $row, ""); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_chave'])>0 )
{
	echo button_delete("ecrm/chave/excluir",$row["cd_chave"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/chave')."'; }");
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