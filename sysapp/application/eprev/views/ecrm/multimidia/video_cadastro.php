<?php
set_title('Vídeo - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		$ar_validar = Array('cd_video','dt_evento','titulo','arquivo');
		echo form_default_js_submit($ar_validar);
	?>
	
	function ir_video()
	{
		location.href='<?php echo site_url("ecrm/multimidia"); ?>';
	}
	
</script>
<?php
	$abas[] = array('aba_videos', 'Vídeos', FALSE, 'ir_video();');
	$abas[] = array('aba_video_cadastra', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start( $abas );
	
	echo form_open('ecrm/multimidia/videoSalvar');
	echo form_start_box( "default_box", "Vídeo" );
		
		echo form_default_text('cd_video', "Cod. Vídeo: ", $cd_video," style='width:100%;border: 0px;' readonly" );
		echo form_default_date('dt_evento', "Dt. Evento*: ", $row );
		echo form_default_textarea('titulo', "Descrição:* ", $row, "style='width:700px;'");
		echo form_default_text('ds_local', "Local do Evento: ", $row, "style='width:100%;'");
		echo form_default_text('diretorio', "Diretório: ", $row, "style='width:100%;'");
		echo form_default_text('arquivo', "Arquivo FLV:* ", $row, "style='width:100%;'");
		echo form_default_text('arquivo_original', "Arquivo Original: ", $row, "style='width:100%;'");
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

	echo button_save("Salvar");
		
	echo form_command_bar_detail_end();
	echo form_close();
	echo aba_end();
	
	$this->load->view('footer_interna');
?>