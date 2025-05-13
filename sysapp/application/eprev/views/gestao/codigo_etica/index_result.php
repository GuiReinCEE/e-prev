<?php
	$head = array(
		'Dt. Aprovaчуo',
		'Arquivo'

	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			(trim($item['fl_editar']) == 'S' ? anchor('gestao/codigo_etica/cadastro/'.$item['cd_codigo_etica'], $item['dt_referencia']) : $item['dt_referencia']),
			anchor(base_url().'up/codigo_etica/'.$item['arquivo'], '[arquivo]' , array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>