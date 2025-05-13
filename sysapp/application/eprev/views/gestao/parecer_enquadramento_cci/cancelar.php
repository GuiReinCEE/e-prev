<?php
set_title('Parecer Enquadramento');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(Array('ds_justificativa_cancelamento'));?>

	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci"); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/anexo/".$row['cd_parecer_enquadramento_cci']); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_cancelar', 'Cancelamento', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('gestao/parecer_enquadramento_cci/salvar_cancelamento');
		echo form_start_box( "default_box", "Justificativa" );
			echo form_default_hidden('cd_parecer_enquadramento_cci', "", $row['cd_parecer_enquadramento_cci']);	

			if(intval($row['cd_parecer_enquadramento_cci']) > 0)
			{
				echo form_default_row('nr_ano_numero', 'Ano/Número :', $row['nr_ano_numero']);
				echo form_default_row('dt_inclusao', 'Dt Cadastro :', $row['dt_inclusao']);
				echo form_default_row('usuario_cadastro', 'Usuário Cadastro:', $row['usuario_cadastro']);
				
				if(trim($row['dt_envio']) != '')
				{
					echo form_default_row('dt_envio', 'Dt Envio :', $row['dt_envio']);
					echo form_default_row('usuario_envio', 'Usuário Envio :', $row['usuario_envio']);
				}
				
				if(trim($row['dt_encerrado']) != '')
				{
					echo form_default_row('dt_encerrado', 'Dt Encerrado :', $row['dt_encerrado']);
					echo form_default_row('usuario_encerrado', 'Usuário Encerrado :', $row['usuario_encerrado']);
				}
			}

			echo form_default_textarea('ds_justificativa_cancelamento', 'Justificativa : *', '');
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>