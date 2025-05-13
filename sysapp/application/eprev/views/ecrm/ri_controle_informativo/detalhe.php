<?php
set_title('Controle Informativos - Detalhe');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array(
											'cd_controle_informativo',
											'ds_informativo',
											'cd_controle_informativo_tipo',
											'dt_envio_limite',
											'fl_envio'
										 ),'formValida(form)');
	?>
	function formValida(form)
	{
		if(($('#fl_envio').val() == "S") && ($("#dt_envio").val() == ""))
		{
			alert("Informe a Data de Envio.");
			return false;	
		}
		else
		{
			form.submit();
		}
	}	
	
	function excluir(cd_controle_informativo)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/ri_controle_informativo/excluir"); ?>' + "/" + cd_controle_informativo;
		}
	}		
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_controle_informativo"); ?>';
	}
	
	$(function() {
		$("#fl_envio").change(function() {
			$("#dt_envio_row").hide();
			if($(this).val() == "S")
			{
				$("#dt_envio_row").show();
			}
		});
		
		$("#fl_envio").change();
		$("#ds_informativo").focus();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('ecrm/ri_controle_informativo/salvar');
		echo form_start_box("default_box", "Evento");
			
			echo form_default_text('cd_controle_informativo', "Código:", $row, 'style="width: 500px; border: 0px;" readonly');	
			echo form_default_text('ds_informativo', "Informativo:(*)", $row, 'style="width: 500px;"');	
		    echo form_default_dropdown_db("cd_controle_informativo_tipo", "Tipo:(*)", 
											array('crm.controle_informativo_tipo', 'cd_controle_informativo_tipo', 'ds_controle_informativo_tipo' ), 
											array($row['cd_controle_informativo_tipo']), "", "", false);
			echo form_default_date('dt_envio_limite', "Dt Limite Envio:(*)", $row);	
			
			$ar_enviado = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
			echo form_default_dropdown('fl_envio', 'Enviado:(*)', $ar_enviado, Array($row['fl_envio']));	
			
			echo form_default_date('dt_envio', "Dt Envio:", $row);	
			echo form_default_integer('nr_exemplar', "Exemplares:", $row);	
			echo form_default_integer('nr_publico', "Público:", $row);	
			echo form_default_integer('nr_retrabalho', "Retrabalho:", $row);	
			echo form_default_integer('nr_reclamacao', "Reclamações:", $row);	
			echo form_default_textarea('observacao', "Observação:", $row,'style="height: 80px;"');	
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row["dt_exclusao"]) == "")
			{
				echo button_save("Salvar");	
				
				if(intval($row['cd_controle_informativo']) > 0)
				{
					echo button_save("Excluir","excluir(".intval($row['cd_controle_informativo']).")","botao_vermelho");
				}				
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view('footer_interna');
?>