<?php
set_title('Controle Eventos - Detalhe');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array(
											'cd_controle_evento',
											'ds_evento',
											'dt_evento',
											'cd_controle_evento_tipo',
											'cd_controle_evento_local'
										 ));
	?>
	
	function excluir(cd_controle_evento)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/ri_controle_evento/excluir"); ?>' + "/" + cd_controle_evento;
		}
	}		
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_controle_evento"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('ecrm/ri_controle_evento/salvar');
		echo form_start_box("default_box", "Evento");
			
			echo form_default_text('cd_controle_evento', "Código:", $row, 'style="width: 500px; border: 0px;" readonly');	
			echo form_default_text('ds_evento', "Evento:(*)", $row, 'style="width: 500px;"');	
			echo form_default_date('dt_evento', "Dt Evento:(*)", $row);	
		    echo form_default_dropdown_db("cd_controle_evento_tipo", "Tipo:(*)", 
											array('crm.controle_evento_tipo', 'cd_controle_evento_tipo', 'ds_controle_evento_tipo' ), 
											array($row['cd_controle_evento_tipo']), "", "", false);
		    echo form_default_dropdown_db("cd_controle_evento_local", "Local:(*)", 
											array('crm.controle_evento_local', 'cd_controle_evento_local', 'ds_controle_evento_local' ), 
											array($row['cd_controle_evento_local']), "", "", TRUE);

			echo form_default_integer('nr_convidado', "Convidado:", $row);	
			echo form_default_integer('nr_estimado', "Estimado:", $row);	
			echo form_default_integer('nr_presente', "Presente:", $row);	
			echo form_default_integer('nr_respondente', "Respondente:", $row);	
			echo form_default_integer('nr_satisfeito', "Satisfeito:", $row);	
			echo form_default_textarea('obs', 'Observação:', $row);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row["dt_exclusao"]) == "")
			{
				echo button_save("Salvar");	
				
				if(intval($row['cd_controle_evento']) > 0)
				{
					echo button_save("Excluir","excluir(".intval($row['cd_controle_evento']).")","botao_vermelho");
				}				
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>