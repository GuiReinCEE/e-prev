<?php
	$head = array(
		'Veículo',
		'Motorista',
		'Destino',
		'Motivo',
		'Dt. Saída',
		'Dt. Retorno',
		'Km Saída',
		'Km Retorno',
		'Km Rodado',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('ecrm/controle_carro/cadastro/'.$item['cd_controle_carro'], $item['ds_controle_carro_veiculo']), "text-align:left;"),
			array(anchor('ecrm/controle_carro/cadastro/'.$item['cd_controle_carro'], $item['ds_controle_carro_motorista']), "text-align:left;"),
			array(anchor('ecrm/controle_carro/cadastro/'.$item['cd_controle_carro'], $item['ds_controle_carro_destino']), "text-align:left;"),
			array(anchor('ecrm/controle_carro/cadastro/'.$item['cd_controle_carro'], $item['ds_controle_carro_motivo']), "text-align:left;"),
			$item['dt_saida'],
			$item['dt_retorno'],
			'<span class="label label-success">'.$item['nr_km_saida'].'</span>',
			'<span class="label label-info">'.$item['nr_km_retorno'].'</span>',
			'<span class="label label-important">'.$item['nr_km_rodado'].'</span>',
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_controle_carro'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>