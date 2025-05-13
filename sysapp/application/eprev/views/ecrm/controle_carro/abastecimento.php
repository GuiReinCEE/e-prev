<?php
	set_title('Controle Carro - Abastecimento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_km', 'nr_valor', 'nr_litro', 'dt_abastecimento', 'hr_abastecimento')) ?>

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
		    "Number",
		    "Number",
		    "DateTimeBR",
			null
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(3, true);
	}

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/controle_carro') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/controle_carro/cadastro/'.$row['cd_controle_carro']) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('ecrm/controle_carro/abastecimento/'.$row['cd_controle_carro']) ?>";
	}

	function excluir(cd_controle_carro_abastecimento)
	{	
		var confirmacao = "Deseja excluir a abastecimento?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/controle_carro/abastecimento_excluir/'.$row['cd_controle_carro']) ?>/" + cd_controle_carro_abastecimento;
		}
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_abastecimento', 'Abastecimento', TRUE, 'location.reload();');

	$head = array( 
		'Km',
		'Valor (R$)',
		'Litros',
		'Data e Hora',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/controle_carro/abastecimento/'.$item['cd_controle_carro'].'/'.$item['cd_controle_carro_abastecimento'], $item['nr_km']),
			anchor('ecrm/controle_carro/abastecimento/'.$item['cd_controle_carro'].'/'.$item['cd_controle_carro_abastecimento'], number_format($item['nr_valor'], 2, ',', '.')),
			anchor('ecrm/controle_carro/abastecimento/'.$item['cd_controle_carro'].'/'.$item['cd_controle_carro_abastecimento'], number_format($item['nr_litro'], 2, ',', '.')),
			$item['dt_abastecimento'],
			'<a href="javascript:void(0);"" onclick="excluir('.$item['cd_controle_carro_abastecimento'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('ecrm/controle_carro/abastecimento_salvar');
			echo form_start_box('default_box', 'Itinerário');
				echo form_default_hidden('cd_controle_carro_abastecimento', '', $abastecimento);	
				echo form_default_hidden('cd_controle_carro', '', $row);	
				echo form_default_row('nr_km_saida', 'Km Saída:', $row['nr_km_saida']);
				echo form_default_row('dt_saida', 'Dt. Saída:', $row['dt_saida'].' '.$row['hr_saida']);
				echo form_default_row('ds_controle_carro_destino', 'Destino:', $row['ds_controle_carro_destino']);
				echo form_default_row('ds_controle_carro_motivo', 'Motivo:', $row['ds_controle_carro_motivo']);
				echo form_default_row('nr_km_retorno', 'Km Retorno:', $row['nr_km_retorno']);
				echo form_default_row('dt_retorno', 'Dt. Retorno:', $row['dt_retorno'].' '.$row['hr_retorno']);
				echo form_default_row('ds_controle_carro_motorista', 'Motorista:', $row['ds_controle_carro_motorista']);
				echo form_default_row('ds_controle_carro_veiculo', 'Veículo:', $row['ds_controle_carro_veiculo']);
			echo form_end_box('default_box');
			echo form_start_box('default_abastecimento_box', 'Cadastro');
				echo form_default_integer('nr_km', 'Km: (*)', $abastecimento);
				echo form_default_numeric('nr_valor', 'Valor (R$): (*)' , $abastecimento);
				echo form_default_numeric('nr_litro', 'Litros: (*)' , $abastecimento);
				echo form_default_date('dt_abastecimento', 'Data: (*)', $abastecimento);
				echo form_default_time('hr_abastecimento', 'Hora: (*)', $abastecimento);
			echo form_end_box('default_abastecimento_box');
			
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
				
				if(intval($abastecimento['cd_controle_carro_abastecimento']) > 0)
				{
					echo button_save('Cancelar', 'cancelar();', 'botao_disabled');	
				}

			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>