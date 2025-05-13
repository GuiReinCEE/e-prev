<?php 
set_title('Torcida - Precavida Texto');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("texto", "text") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_precavida_texto"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_precavida_texto/salvar');
echo form_hidden( 'cd_precavida_texto', intval($row['cd_precavida_texto']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - Precavida Texto" );
echo form_default_textarea("texto", "Texto *", $row, "", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_precavida_texto'])>0  )
{
	echo button_delete("ecrm/ri_torcida_precavida_texto/excluir",$row["cd_precavida_texto"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_precavida_texto')."'; }");
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