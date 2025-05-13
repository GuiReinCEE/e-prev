<?php
	$head = array(
		'Colegiado',
		'Nº Súmula',
		'Assunto'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{	
		$body[] = array(
			'<span class="'.$item['ds_class'].'">'.$item['fl_colegiado'].'</span>',
			$item['nr_sumula'],
			array(nl2br($item['ds_sumula']), 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>