<?php
	set_title('Eventos Institucionais - Imagens');
	$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('cd_evento'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional"); ?>';
	}
	
	function detalhe(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/detalhe"); ?>' + "/" + cd_evento;
	}	
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "detalhe('".intval($row['cd_evento'])."');");
	$abas[] = array('aba_imagem', 'Imagens', TRUE, "location.reload();");


	echo form_open('ecrm/ri_evento_institucional/salvarImagem');
	echo aba_start( $abas );
	
	
	echo form_start_box("default_imagem", "Cadastro" );
		echo form_default_text('cd_evento', "Código: ", intval($row['cd_evento']), "style='width:100%;border: 0px;' readonly" );

		echo form_default_upload_iframe('img_inscricao', 'evento_institucional', 'Incrição: (L = 750px, .jpg)');
		echo form_default_row('', 'Incrição Atual:', '<img src="../../../../../eletroceee/img/evento_institucional/'.$row['img_inscricao'].'" border="0">');
		
		echo form_default_upload_iframe('img_confirma', 'evento_institucional', 'Confirmação: (L = 750px, .jpg)');
		echo form_default_row('', 'Confirmação Atual:', '<img src="../../../../../eletroceee/img/evento_institucional/'.$row['img_confirma'].'" border="0">');
		
		echo form_default_upload_iframe('img_encerra', 'evento_institucional', 'Encerrado: (L = 750px, .jpg)');
		echo form_default_row('', 'Encerrado Atual:', '<img src="../../../../../eletroceee/img/evento_institucional/'.$row['img_encerra'].'" border="0">');
		
	echo form_end_box("default_imagem");	
	
	echo form_command_bar_detail_start();
		echo button_save();
	echo form_command_bar_detail_end();	

?>
<br><br>
<?php
	echo aba_end();
	echo form_close();
	$this->load->view('footer_interna');
?>