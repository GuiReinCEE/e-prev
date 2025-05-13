<?php
	$head = array( 
		'Código',
		'Projeto',
		'Dt. Inlcusão',
		'Responsável',
		'Envolvidos',
		''
	);

	$body = array();
	
	foreach($collection as $item)
	{
		$body[] = array(
			anchor('gestao/projeto/cadastro/'.$item['cd_projeto'], $item['cd_projeto']),
			array(anchor('gestao/projeto/cadastro/'.$item['cd_projeto'], $item['ds_projeto']), 'text-align:left'),
			anchor('gestao/projeto/cadastro/'.$item['cd_projeto'], $item['dt_inclusao']),
			$item['cd_gerencia_resposanvel'],
			implode(', ', $item['gerencia_envolvida']),
			'<a href="'.site_url('gestao/projeto/pdf/'.intval($item['cd_projeto'])).'" target="_blank">[PDF]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>