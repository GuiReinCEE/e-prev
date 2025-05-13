<?php 
set_title('Torcida - Bate Bola Cadastro');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_bate_bola", "text") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_bate_bola_cadastro"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_bate_bola_cadastro/salvar');
echo form_hidden( 'cd_bate_bola', intval($row['cd_bate_bola']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - Bate Bola Cadastro" );
echo form_default_textarea("ds_bate_bola", "Descrição *", $row, "", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_bate_bola'])>0  )
{
	echo button_delete("ecrm/ri_torcida_bate_bola_cadastro/excluir",$row["cd_bate_bola"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_bate_bola_cadastro')."'; }");
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