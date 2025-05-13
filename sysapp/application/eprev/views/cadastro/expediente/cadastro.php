<?php
	set_title('Comitê de Ética - Expediente');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_descricao', 'cd_expediente_origem')) ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/expediente') ?>";
	}
	
	function ir_andamento()
	{
		location.href = "<?= site_url('cadastro/expediente/andamento/'.$row['cd_expediente']) ?>";
	}	
	
	function ir_anexo()
	{
		location.href = "<?= site_url('cadastro/expediente/anexo/'.$row['cd_expediente']) ?>";
	}		
	
	function concluir()
	{
		var confirmacao = "Deseja CONCLUIR o expediente?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('cadastro/expediente/concluir/'.$row['cd_expediente']) ?>";
		}
	}	

	function enviar_email()
	{
		var confirmacao = "Deseja Enviar e-mail do expediente para o Comitê?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('cadastro/expediente/enviar_email/'.$row['cd_expediente']) ?>";
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['nr_expediente']) > 0)
	{
		$abas[] = array('aba_andamento', 'Andamento', FALSE, 'ir_andamento();');
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	}

	echo aba_start($abas);
		echo form_open('cadastro/expediente/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_expediente', '', $row['cd_expediente']);
			
				if(intval($row['cd_expediente']) > 0)
				{
					echo form_default_row('', 'Cód Expediente:', '<span class="label label-inverse">'.$row['nr_expediente'].'</span>');
					echo form_default_row('', 'Dt Registro:', '<span class="label">'.$row['dt_inclusao'].'</span>');
					echo form_default_row('', 'Dt Atualização:', '<span class="label">'.$row['dt_alteracao'].'</span>');
					echo form_default_row('', 'Dt Envio Comitê:', '<span class="label">'.$row['dt_envio_comite'].'</span>');
					echo form_default_row('', 'Dt Conclusão:', '<span class="label label-success">'.$row['dt_conclusao'].'</span>');
					echo form_default_row('', 'Status:', '<span class="label label-warning">'.$row['ds_expediente_status'].'</span>');
				}
				echo form_default_textarea('ds_descricao', 'Descrição: (*)', $row['ds_descricao'], 'style="height: 100px;"');

				echo form_default_dropdown_db('cd_expediente_origem', 'Origem: (*)', array('comite_etica.expediente_origem', 'cd_expediente_origem', 'ds_expediente_origem'), array($row['cd_expediente_origem']), '', '', TRUE);
			echo form_end_box('default_box');

			echo form_command_bar_detail_start();
				if(trim($row['dt_conclusao']) == '')
				{
					if(intval($row['cd_expediente_origem']) != 1)
					{
						echo button_save('Salvar');
					}

					if((intval($row['cd_expediente']) > 0) AND (trim($row['dt_envio_comite']) == ''))
					{
						echo button_save('Enviar E-mail Comitê', 'enviar_email()', 'botao_verde');
					}
				
					if(intval($row['cd_expediente']) > 0)
					{
						echo button_save('Concluir', 'concluir()', 'botao_vermelho');
					}
				}
			echo form_command_bar_detail_end();
		
		echo form_close();
		
		echo br(2);
	echo aba_end();
$this->load->view('footer_interna');
?>