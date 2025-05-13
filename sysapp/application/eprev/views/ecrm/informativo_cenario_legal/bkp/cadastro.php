<?php 
set_title('Cenário Legal - Edição');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array('tit_capa', 'texto_capa'));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/"); ?>';
	}
		
	function excluir()
	{
		if(confirm("Deseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("ecrm/informativo_cenario_legal/excluir"); ?>/' + $('#cd_edicao').val();
		}
	}	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/informativo_cenario_legal/salvar');
		echo form_start_box( "default_box", "Edição Cenário Legal" );
			echo form_default_hidden('cd_edicao', "", intval($row['cd_edicao']));

			if(intval($row['cd_edicao']) > 0)
			{
				echo form_default_text("dt_edicao", "Data Edição:", $row, "style='width:100%;border: 0px;' readonly"); 
				echo form_default_text("dt_exclusao", "Data Exclusão:", $row, "style='width:100%;border: 0px;' readonly"); 
			}

			echo form_default_text("tit_capa", "Título: *", $row, "style='width:500px;'");
			echo form_default_textarea("texto_capa","Texto: *",$row, "style='width:100%;'");

		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			if($row['dt_exclusao'] == "")
			{
				echo button_save();
			}

			if((intval($row['cd_edicao']) > 0) and ($row['dt_exclusao'] == ""))
			{
				echo button_save("Excluir", "excluir();", 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>