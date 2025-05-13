<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('dt_projeto_cronograma_realizado_ini', 'dt_projeto_cronograma_realizado_fim'));
	?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/projeto') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/projeto/cadastro/'.$row['cd_projeto']) ?>";
	}

	function ir_indicador()
	{
		location.href = "<?= site_url('gestao/projeto/indicador/'.$row['cd_projeto']) ?>";
	}

	function ir_custo()
	{
		location.href = "<?= site_url('gestao/projeto/custo/'.$row['cd_projeto']) ?>";
	}

	function ir_cronograma()
	{
		location.href = "<?= site_url('gestao/projeto/cronograma/'.$row['cd_projeto']) ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
	$abas[] = array('aba_custo', 'Custos Projetados', FALSE, 'ir_custo();');
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	$abas[] = array('aba_cronograma_realizado', 'Cronograma Realizado', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/projeto/atualizar_cronograma_realizado');
			echo form_start_box('default_box', 'Projeto');
				echo form_default_hidden('cd_projeto', '', $row);	
				echo form_default_row('ds_projeto', 'Projeto :', $row['ds_projeto'], 'style="width:350px;"');
			echo form_end_box('default_box');
			
			echo form_start_box('default_indicador_box', 'Cronograma');
				echo form_default_hidden('cd_projeto_cronograma', '', $cronograma);	
				echo form_default_row('ds_projeto_cronograma', 'Etapa :', $cronograma['ds_projeto_cronograma']);
				echo form_default_row('dt_projeto_cronograma_ini', 'Início Previsto :', $cronograma['dt_projeto_cronograma_ini']);
				echo form_default_row('dt_projeto_cronograma_fim', 'Fim Previsto :', $cronograma['dt_projeto_cronograma_fim']);
				echo form_default_row('gerencia', 'Gerência Responsável :', implode(', ', $gerencia));
				echo form_default_date('dt_projeto_cronograma_realizado_ini', 'Início Realizado :*', $cronograma);
				echo form_default_date('dt_projeto_cronograma_realizado_fim', 'Fim Realizado :*', $cronograma);
			echo form_end_box('default_indicador_box');
			
			echo form_command_bar_detail_start();
				if($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel'])
				{
					echo button_save('Salvar');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>