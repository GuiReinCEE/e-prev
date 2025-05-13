<?php
set_title('Aniversário - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'nome',
						'area',
						array('dt_nascimento','data'),
					)
			 );
	?>
	
	function aniversarioExcluir(cd_aniversario)
	{
		var confirmacao = 'Confirma a exclusão?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{
			location.href = '<?php echo site_url("ecrm/ri_aniversario/excluir/"); ?>/' + cd_aniversario;
		}
	}
	
	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/ri_aniversario"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/ri_aniversario/salvar');
	echo form_start_box("default_box", "Detalhe");
		echo (trim($row['dt_exclusao']) != "" ? form_default_text('dt_exclusao', "Dt. Exclusão:", $row, "style='width:100%;border: 0px;' readonly" ) : "");
	
		echo form_default_text('cd_aniversario', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('origem', "Origem:", $row, "style='border: 0px;' readonly" );
		
		echo form_default_text('nome', "Nome:*", $row, "style='width:500px;'".(trim($row["origem"]) == "USU" ? " readonly " : ""));
		echo form_default_text('area', "Área:*", trim($row["area"]), 'maxlength="5"'.(trim($row["origem"]) == "USU" ? " readonly " : ""));
		echo form_default_date('dt_nascimento', "Dt Nascimento:*", $row);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(trim($row['dt_exclusao']) == "")
		{
			echo button_save("Salvar");
			
			if ((intval($row["cd_aniversario"]) > 0) and (trim($row["origem"]) == "CAD"))
			{
				echo button_save("Excluir","aniversarioExcluir(".$row["cd_aniversario"].")","botao_vermelho");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
	
	echo br(5);
	echo aba_end();
	$this->load->view('footer_interna');
?>