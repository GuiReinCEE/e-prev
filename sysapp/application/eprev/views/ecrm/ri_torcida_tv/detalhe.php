<?php 
set_title('Torcida - TV');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("titulo", "str") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_tv"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_tv/salvar');
echo form_hidden( 'cd_tv', intval($row['cd_tv']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - TV" );
echo form_default_text("titulo", "Titulo *", $row, "style='width:300px;'", "0"); 
echo form_default_textarea("resumo", "Resumo", $row, "", "0"); 
echo form_default_text("caminho", "Caminho", $row, "style='width:300px;'", "0"); 
echo form_default_text("icone", "Icone", $row, "style='width:300px;'", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_tv'])>0  )
{
	echo button_delete("ecrm/ri_torcida_tv/excluir",$row["cd_tv"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_tv')."'; }");
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