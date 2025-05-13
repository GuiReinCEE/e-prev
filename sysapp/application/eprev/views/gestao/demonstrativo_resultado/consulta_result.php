<?php
	$head = array(
		'Ano/Mês',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			$item['nr_ano'].'/'.$item['cd_mes'],
			anchor(base_url().'up/demonstrativo_resultado/'.$item['arquivo'], '[PDF]', array('target' => '_blank'))
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>