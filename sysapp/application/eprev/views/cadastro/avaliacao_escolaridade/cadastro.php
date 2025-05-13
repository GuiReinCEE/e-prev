<?php 
set_title('Escolaridade');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("nome_comp_inst"));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_escolaridade"); ?>';
	}
	
	function ir_escala()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_escolaridade/escala"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
$abas[] = array('aba_lista', 'Escala', FALSE, 'ir_escala();');

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_escolaridade/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden( 'cd_escolaridade', intval($row['cd_escolaridade']) );
			echo form_default_text("nome_escolaridade", "Nome: *", $row, "style='width:300px;'"); 
			echo form_default_textarea("desc_escolaridade", "Descrição:", $row); 
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>