<?php 
set_title('Torcida - Precavida Imagem');
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array(
		array("y1", "int") 
		,array("x2", "int") 
		,array("y2", "int") 
		)
	);
	?>
	function _salvar(form)
	{
		if(confirm('Salvar?'))
		{
			form.action="<?php echo site_url('ecrm/ri_torcida_precavida_imagem/salvar'); ?>";
			form.target='';
			form.encoding='';
			form.submit();
		}
	}
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_precavida_imagem"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_precavida_imagem/salvar');
echo form_hidden( 'cd_precavida_imagem', intval($row['cd_precavida_imagem']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - Precavida Imagem" );
echo form_default_upload_iframe('imagem', 'torcida_precavida', "Imagem * <br>(largura 320 pixels e altura 280 pixels)", $row['imagem'] ); 
echo form_default_integer("x1", "X1 *", $row, ""); 
echo form_default_integer("y1", "Y1 *", $row, ""); 
echo form_default_integer("x2", "X2 *", $row, ""); 
echo form_default_integer("y2", "Y2 *", $row, ""); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_precavida_imagem'])>0  )
{
	echo button_delete("ecrm/ri_torcida_precavida_imagem/excluir",$row["cd_precavida_imagem"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_precavida_imagem')."'; }");
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