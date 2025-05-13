<?php
	set_title('Agenda Atendimento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_link_zoom')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento') ?>";
	}

	function enviar_email()
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento/salvar_envio_email/'.$row['cd_atendimento_agendamento']) ?>";

	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_editar', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_agendamento/salvar_editar_agendamento');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_atendimento_agendamento', '', $row);	
				echo form_default_hidden('cd_empresa', '', $row);	
				echo form_default_hidden('cd_registro_empregado', '', $row);	
				echo form_default_hidden('seq_dependencia', '', $row);
				echo form_default_row('', 'Tipo:', $row['ds_tipo_agendamento']);
				echo form_default_row('nome', 'Nome:', $row['nome']);
				echo form_default_row('cpf', 'CPF:', $row['cpf']);
				echo form_default_text('email', 'E-mail:(*)', $row['email'], 'style="width:400px;"');
				echo form_default_row('telefone_1', 'Telefone 1:', $row['telefone_1']); 
				echo form_default_row('telefone_2', 'Telefone 2:', $row['telefone_2']); 
				echo form_default_row('cd_empresa', 'Empresa', $row['cd_empresa'] );
				echo form_default_row('cd_registro_empregado', 'RE', $row['cd_registro_empregado'] );
				echo form_default_row('seq_dependencia', 'Sequencia', $row['seq_dependencia'] );	
				echo form_default_row('dt_inclusao', 'Data do agendamento:', $row['dt_inclusao']);
				echo form_default_row('dt_agenda', 'Data agendada:', $row['dt_agenda']);
				echo form_default_row('ds_atendimento_agendamento_tipo', 'Tipo:', $row['ds_atendimento_agendamento_tipo']);

				if($row['tp_agendamento'] == 'V')
				{
					if(intval($row['cd_usuario_envio_email']) == 0)
					{
						echo form_default_text('ds_link_zoom','Link Zoom: (*)',$row['ds_link_zoom'], 'style="width:300px;"');
						echo form_default_text('ds_senha_zoom','Senha Zoom:',$row['ds_senha_zoom'], 'style="width:300px;"');
					}
					else
					{
						echo form_default_row('ds_link_zoom', 'Link Zoom:', $row['ds_link_zoom']);
						echo form_default_row('ds_senha_zoom', 'Senha Zoom:',$row['ds_senha_zoom']);
					}
				}

			echo form_end_box('default_box');
			echo form_command_bar_detail_start();   
	            if(intval($row['cd_usuario_envio_email']) == 0)
	            {
	                echo button_save('Salvar');

	                if(trim($row['ds_link_zoom']) != '')
	                {
	                	echo button_save('Enviar Email Link', 'enviar_email();', 'botao_verde');
	                }
	            }
            echo form_command_bar_detail_end();
		echo form_close();

		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
