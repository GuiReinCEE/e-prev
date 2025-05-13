<?php 
set_title('Competências Institucionais');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("nome_comp_inst"));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_competencia_institucional"); ?>';
	}
	
	function ir_escala()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_competencia_institucional/escala"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
$abas[] = array('aba_lista', 'Escala', FALSE, 'ir_escala();');

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_competencia_institucional/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden( 'cd_comp_inst', intval($row['cd_comp_inst']) );
			echo form_default_text("nome_comp_inst", "Nome: *", $row, "style='width:300px;'"); 
			echo form_default_textarea("desc_comp_inst", "Descrição:", $row); 
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>