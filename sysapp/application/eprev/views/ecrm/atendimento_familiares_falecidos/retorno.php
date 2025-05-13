<?php
	set_title('Contato Familiares Ex-autárquicos Falecidos - Retorno');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_atendimento_familiares_falecidos_retorno')) ?>  

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/cadastro/'.$row['cd_atendimento_familiares_falecidos']) ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/acompanhamento/'.$row['cd_atendimento_familiares_falecidos']) ?>";
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_retorno', 'Retorno', TRUE, 'location.reload();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_familiares_falecidos/retorno_salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_row('re', 'Participante:', $contato['cd_empresa'].'/'.$contato['cd_registro_empregado'].'/'.$contato['seq_dependencia']);
				echo form_default_row('nome', 'Nome:', $contato['nome']);
				if(trim($contato['cd_atendimento']) != '')
				{
					echo form_default_row('cd_atendimento', 'Atendimento:', $contato['cd_atendimento']);
				}
				if(trim($contato['dt_encerramento']) != '')
				{
					echo form_default_row('dt_encerramento', 'Data Encerramento:', $contato['dt_encerramento']);
				}
			echo form_end_box('default_box');

			echo form_start_box('default_retorno_box', 'Retorno');
				echo form_default_hidden('cd_atendimento_familiares_falecidos', '', $row['cd_atendimento_familiares_falecidos']);
				echo form_default_hidden('cd_atendimento_familiares_falecidos_retorno', '', $row['cd_atendimento_familiares_falecidos_retorno']);
				echo form_default_textarea('ds_atendimento_familiares_falecidos_retorno', 'Observação:*', $row);
			echo form_end_box('default_retorno_box');
			echo form_command_bar_detail_start();    
				if(trim($contato['dt_encerramento']) == '')
				{
					echo button_save('Salvar');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');
?>