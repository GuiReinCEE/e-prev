<?php
	set_title('Usuário e-prev: Liberar horário - cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario', 'dt_liberar','hr_ini','hr_fim')) ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('servico/usuario_horario') ?>";
	}
	
	function excluirHorario()
	{
		var confirmacao = "Deseja EXCLUIR?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/usuario_horario/excluir/'.$row['cd_usuario_horario']) ?>";
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('servico/usuario_horario/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_usuario_horario', '', $row['cd_usuario_horario']);
				echo form_default_usuario_ajax('cd_usuario', $row['divisao'], $row['cd_usuario']);
				echo form_default_date('dt_liberar', 'Dt Liberar: (*)', $row['dt_liberar']);
				echo form_default_time('hr_ini', 'Hr Inicial: (*)', $row['hr_ini']);
				echo form_default_time('hr_fim', 'Hr Final: (*)', $row['hr_fim']);
				echo form_default_textarea('ds_obs', 'Observação:', $row['ds_obs'], 'style="height: 100px;"');
			echo form_end_box('default_box');

			echo form_command_bar_detail_start();

				echo button_save('Salvar');
				
				if(intval($row['cd_usuario_horario']) > 0)
				{
					echo button_save('Excluir', 'excluirHorario()', 'botao_vermelho');
				}
				
			echo form_command_bar_detail_end();
		
		echo form_close();
		
		echo br(2);
	echo aba_end();
$this->load->view('footer_interna');
?>