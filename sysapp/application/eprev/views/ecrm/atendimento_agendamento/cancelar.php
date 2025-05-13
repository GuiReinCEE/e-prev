<?php
set_title('Agenda Atendimento');
$this->load->view('header');
?>
<script>

	<?= form_default_js_submit(array('ds_justificativa_cancelamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cancelamento', 'Cancelamento', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_agendamento/cancelar_agendamento');
			echo form_start_box('default_box', 'Cancelamento');
				echo form_default_hidden('cd_atendimento_agendamento', '', $row);	
				echo form_default_hidden('cd_empresa', '', $row);	
				echo form_default_hidden('cd_registro_empregado', '', $row);	
				echo form_default_hidden('seq_dependencia', '', $row);	
				echo form_default_row('nome', 'Nome:', $row['nome']);
				echo form_default_row('cpf', 'CPF:', $row['cpf']);
				echo form_default_row('email', 'E-mail:', $row['email']); 
				echo form_default_row('telefone_1', 'Telefone 1:', $row['telefone_1']); 
				echo form_default_row('telefone_2', 'Telefone 2:', $row['telefone_2']); 
				echo form_default_row('cd_empresa', 'Empresa', $row['cd_empresa'] );
				echo form_default_row('cd_registro_empregado', 'RE', $row['cd_registro_empregado'] );
				echo form_default_row('seq_dependencia', 'Sequencia', $row['seq_dependencia'] );	
				echo form_default_row('dt_inclusao', 'Data do agendamento:', $row['dt_inclusao']);
				echo form_default_row('dt_agenda', 'Data agendada:', $row['dt_agenda']);
				echo form_default_row('ds_atendimento_agendamento_tipo', 'Tipo:', $row['ds_atendimento_agendamento_tipo']); 
				echo form_default_textarea('ds_justificativa_cancelamento','Justificativa:(*)');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
		echo form_close();

		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
