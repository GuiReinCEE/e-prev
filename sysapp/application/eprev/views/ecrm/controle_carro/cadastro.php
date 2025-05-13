<?php
set_title('Controle Carro - Itinerário');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
			'nr_km_saida', 
			'dt_saida', 
			'hr_saida', 
			'cd_controle_carro_destino', 
			'cd_controle_carro_motivo', 
			'nr_km_retorno', 
			'dt_retorno', 
			'hr_retorno', 
			'cd_controle_carro_motorista',
			'cd_controle_carro_veiculo'
		));
	?>

	function ir_lista()
	{
		location.href = '<?= site_url('ecrm/controle_carro') ?>';
	}

	function ir_abastecimento()
	{
		location.href = '<?= site_url('ecrm/controle_carro/abastecimento/'.$row['cd_controle_carro']) ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_controle_carro']) > 0)
	{
		$abas[] = array('aba_abastecimento', 'Abastecimento', FALSE, 'ir_abastecimento();');
	}

	echo aba_start($abas);
		echo form_open('ecrm/controle_carro/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_controle_carro', '', $row);	
				echo form_default_integer('nr_km_saida', 'Km Saída: (*)', $row);
				echo form_default_date('dt_saida', 'Dt. Saída: (*)', $row);
				echo form_default_time('hr_saida', 'Hr. Saída: (*)', $row);
				echo form_default_dropdown_db('cd_controle_carro_destino', 'Destino: (*)', array('projetos.controle_carro_destino', 'cd_controle_carro_destino', 'ds_controle_carro_destino'), array($row['cd_controle_carro_destino']), '', '', TRUE);
				echo form_default_dropdown_db('cd_controle_carro_motivo', 'Motivo: (*)', array('projetos.controle_carro_motivo', 'cd_controle_carro_motivo', 'ds_controle_carro_motivo'), array($row['cd_controle_carro_motivo']), '', '', TRUE);
				echo form_default_integer('nr_km_retorno', 'Km Retorno: (*)', $row);
				echo form_default_date('dt_retorno', 'Dt. Retorno: (*)', $row);
				echo form_default_time('hr_retorno', 'Hr. Retorno: (*)', $row);
				
				echo form_default_dropdown_db('cd_controle_carro_motorista', 'Motorista: (*)', array('projetos.controle_carro_motorista', 'cd_controle_carro_motorista', 'ds_controle_carro_motorista'), array($row['cd_controle_carro_motorista']), '', '', TRUE);
				echo filter_dropdown('cd_controle_carro_veiculo', 'Veículo: (*)', $veiculo, $row['cd_controle_carro_veiculo']);
				echo form_default_textarea('ds_observacao', 'Observação:', $row,'style="height: 80px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();

	$this->load->view('footer_interna');
?>