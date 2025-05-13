<?php 
set_title('Avaliação - Cadastro de Cargo');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array('nome_cargo', 'cd_familia'));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_cargo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );

	echo form_open('cadastro/avaliacao_cargo/salvar');
		echo form_start_box( "default_box", "Avaliação - Cadastro de Cargo" );
			echo form_default_hidden('cd_cargo', '', $row['cd_cargo']);
			echo form_default_text("nome_cargo", "Cargo:*", $row['nome_cargo'], "style='width:500px;'");

			if(intval($row['cd_cargo']) > 0)
			{
				echo form_default_checkbox_group('institucionais', 'Competências Institucionais:', $institucionais, $institucionais_chk, 300);
				echo form_default_checkbox_group('especificas', 'Competências Específicas:', $especificas, $especificas_chk, 300);
			}
			
			echo form_default_dropdown('cd_familia', 'Família escolaridade:*', $familia, array($row['cd_familia']));

			if(intval($row['cd_cargo']) > 0)
			{
				echo form_default_checkbox_group('responsabilidades', 'Responsabilidades:', $responsabilidades, $responsabilidades_chk, 300);
			}

			echo form_default_textarea('desc_cargo', "Descrição:", $row['desc_cargo']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if( intval(0)>0 )
			{
				echo button_delete("cadastro/avaliacao_cargo/excluir",$row[""]);
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>