<?php 
set_title('Competências Institucionais');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("cd_escala", "descricao"));	?>

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
$abas[] = array('aba_lista', 'Escala', FALSE, 'ir_escala();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_competencia_institucional/salvar_escala');
		echo form_start_box( "default_box", "Escala de Proficiência" );
			echo form_hidden( 'insert', (trim($row['cd_escala']) == '' ? 0 : 1) );
			if(trim($row['cd_escala']) == '')
			{
				echo form_default_text("cd_escala", "Grau: *", $row, 'style="width:300px;"'); 
			}
			else
			{
				echo form_default_text("cd_escala", "Grau:", $row, 'style="width:300px; font-weight:bold; border: 0px; readonly"'); 
			}
			echo form_default_textarea("descricao", "Descrição:", $row); 
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>