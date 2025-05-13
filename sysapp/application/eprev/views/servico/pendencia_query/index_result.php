<?php
	$head = array(
		'Pendъncia',
		'Descriчуo',
		'Superior'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array(anchor('servico/pendencia_query/cadastro/'.$item['cd_pendencia_minha_query'], $item['cd_pendencia_minha'].' - '.$item['ds_pendencia_minha']), 'text-align:left;'),
			array($item['ds_descricao'], 'text-align:left'),
			$item['ds_superior']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>