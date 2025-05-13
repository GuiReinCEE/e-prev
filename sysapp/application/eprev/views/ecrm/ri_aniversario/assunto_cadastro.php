<?php
set_title('Aniversário - Assunto - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'assunto'
					)
			 );
	?>
	
	function assuntoExcluir(cd_aniversario_assunto)
	{
		var confirmacao = 'Confirma a exclusão?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{
			location.href = '<?php echo site_url("ecrm/ri_aniversario/assuntoExcluir/"); ?>/' + cd_aniversario_assunto;
		}
	}
	
	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/ri_aniversario/assunto"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/ri_aniversario/assuntoSalvar');
	echo form_start_box("default_box", "Detalhe");
		echo (trim($row['dt_exclusao']) != "" ? form_default_text('dt_exclusao', "Dt. Exclusão:", $row, "style='width:100%;border: 0px;' readonly" ) : "");
		echo form_default_text('cd_aniversario_assunto', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('assunto', "Assunto:*", $row, 'style="width:500px;" maxlength="100"');
		echo form_default_row('msg_assunto', "", "<i style='font-size: 95%;'>(Informe o assunto com o máximo de 100 caracteres)</i>");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(trim($row['dt_exclusao']) == "")
		{
			echo button_save("Salvar");
			
			if (intval($row["cd_aniversario_assunto"]) > 0) 
			{
				echo button_save("Excluir","assuntoExcluir(".$row["cd_aniversario_assunto"].")","botao_vermelho");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
	
	echo br(5);
	echo aba_end();
	$this->load->view('footer_interna');
?>