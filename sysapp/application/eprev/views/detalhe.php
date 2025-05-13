<?php 
set_title('');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_nome", "str") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno_grupo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/cadastro_protocolo_interno_grupo/salvar');
echo form_hidden( 'cd_documento_recebido_grupo', intval($row['cd_documento_recebido_grupo']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "" );
echo form_default_text("ds_nome", "Grupo *", $row, "style='width:300px;'", "255"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_documento_recebido_grupo'])>0  )
{
	echo button_delete("ecrm/cadastro_protocolo_interno_grupo/excluir",$row["cd_documento_recebido_grupo"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/cadastro_protocolo_interno_grupo')."'; }");
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