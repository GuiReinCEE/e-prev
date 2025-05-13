<?php
	$body = array();
	$head = array(
		'Gerência',
		'Usuário',
		''
	);

	foreach ($collection as $item)
	{	
		$body[] = array(
			$item['divisao'],
			array($item['nome'], 'text-align:left;'),
			'<a href="javascript:void(0);" onclick="remover('.intval($item['codigo']).')">[remover]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>