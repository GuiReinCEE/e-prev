<?php 
set_title('');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(
		array("nome", "text") 
		,array("comentario", "text") 
	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_bate_bola_comentario"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_bate_bola_comentario/salvar');
echo form_hidden( 'cd_bate_bola_comentario', intval($row['cd_bate_bola_comentario']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "" );
echo form_default_text("nome", "Nome - *", $row, "style='width:300px;'", '0'); 
echo form_default_textarea("comentario", "Comentario *", $row, "", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_bate_bola_comentario'])>0  && false  )
{
	echo button_delete("ecrm/ri_torcida_bate_bola_comentario/excluir",$row["cd_bate_bola_comentario"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_bate_bola_comentario')."'; }");
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