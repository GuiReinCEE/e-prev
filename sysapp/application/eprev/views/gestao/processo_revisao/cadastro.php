<?php
	set_title('Revis�o de Processos');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array('fl_revisado')) ?>  

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/processo/revisao') ?>";
	}

	function encerrar(form)
	{
		var confirmacao = "Deseja ENCERRAR a Revis�o do Processo?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para N�o\n\n";	

		if(confirm(confirmacao))
		{
			form.submit();
		}
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Processo', TRUE, 'location.reload();');

	$revisado[] = array('value' => 'N', 'text' => 'N�o');
	$revisado[] = array('value' => 'S', 'text' => 'Sim');

	echo aba_start($abas);

		echo form_open('gestao/processo/revisao_salvar');
			echo form_start_box('default_processo_box', 'Processo');
				echo form_default_row('procedimento', 'Processo:', $row['procedimento']);
				echo form_default_row('dt_inclusao', 'Dt. Inclus�o:', $row['dt_inclusao']);
				echo form_default_row('dt_referencia', 'Dt. Refer�ncia:', $row['dt_referencia']);
				echo form_default_row('dt_limite', 'Dt. Limite:', $row['dt_limite']);
			echo form_end_box('default_processo_box');

			echo form_start_box('default_box', 'Revis�o');
				echo form_default_hidden('cd_processo_revisao', '', $row['cd_processo_revisao']);
				echo form_default_dropdown('fl_alterado', 'Ocorreu Altera��o no Processo:*', $revisado, $row['fl_alterado']);
				echo form_default_textarea('observacao', 'Observa��o:', $row);

				if(trim($row['dt_revisao']) != '')
				{
					echo form_default_row('dt_revisao', 'Data da Revis�o:', $row['dt_revisao']);
					echo form_default_row('usuario_revisao', 'Usu�rio Revis�o:', $row['usuario_revisao']);
				}
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();    
				if(trim($row['dt_revisao']) == '')
				{
					echo button_save('Encerrar', 'encerrar(form);', 'botao_verde');
				}
			echo form_command_bar_detail_end();
		echo form_close();

		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');
?>