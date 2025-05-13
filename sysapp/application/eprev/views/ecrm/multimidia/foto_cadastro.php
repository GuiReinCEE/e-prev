<?php
set_title('Foto - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		$ar_validar = Array('cd_fotos','dt_data','ds_titulo','ds_caminho');
		echo form_default_js_submit($ar_validar);
	?>
	
	function ir_foto()
	{
		location.href='<?php echo site_url("ecrm/multimidia/foto"); ?>';
	}
	
</script>
<?php
	$abas[] = array('aba_foto', 'Fotos', FALSE, 'ir_foto();');
	$abas[] = array('aba_foto_cadastra', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start( $abas );
	
	echo form_open('ecrm/multimidia/fotoSalvar');
	echo form_start_box( "default_box", "Foto" );
		
		echo form_default_text('cd_fotos', "Cod. Foto: ", $cd_fotos," style='width:100%;border: 0px;' readonly" );
		echo form_default_date('dt_data', "Dt. Data*: ", $row );
		echo form_default_textarea('ds_titulo', "Descrição:* ", $row, "style='width:700px;'");
		echo form_default_text('ds_caminho', "Diretório*: ", $row, "style='width:100%;'");
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

	echo button_save("Salvar");
		
	echo form_command_bar_detail_end();
	echo form_close();
	echo aba_end();
	
	$this->load->view('footer_interna');
?>