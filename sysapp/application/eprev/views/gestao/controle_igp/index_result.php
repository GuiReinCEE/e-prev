<?php
	$head = array(
		'Ano',
		'Indicadores',
		'Usuário',
		'Dt. Fechamento',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$link = '<a href="javascript:void(0)" onclick="fechar('.$item['cd_controle_igp'].')">[fechar]</a>';

		$body[] = array(
			($item['dt_fechamento'] == '' ? anchor('gestao/controle_igp/cadastro/'.$item['cd_controle_igp'], $item['nr_ano']) : anchor('gestao/controle_igp/resultado_mes/'.$item['cd_controle_igp'], $item['nr_ano'])),
			array((count($item['indicadores']) > 0 ? implode(br(), $item['indicadores']) : ''), 'text-align:justify'),
			$item['cd_usuario'],
			$item['dt_fechamento'],
			($item['dt_fechamento'] == '' ? $link : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>