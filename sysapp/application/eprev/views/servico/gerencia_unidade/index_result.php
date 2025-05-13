<?php
	$head = array(
		'Cуdigo',
		'Descriзгo',
		'Unidades',
		'Diretoria',
		'Tipo',
		'Gerente',
		'Supervisor',
		'Vigкncia'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('servico/gerencia_unidade/cadastro/'.$item['codigo'], $item['codigo']),
			array(anchor('servico/gerencia_unidade/cadastro/'.$item['codigo'], $item['ds_gerencia']), 'text-align:left;'),
			array(implode(br(), $item['unidade']), 'text-align:left;'),
			array($item['ds_diretoria'], 'text-align:left;'),
			array($item['ds_tipo'], 'text-align:left;'),
			array($item['ds_gerente'], 'text-align:left;'),
			array(implode(br(), $item['supervisor']), 'text-align:left;'),
			$item['dt_vigencia_ini']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>