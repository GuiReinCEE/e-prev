<?php
	$head = array( 
		'Dt. Acesso',
		'Usuário',
		'URI',
		'Request'
	);

	$body = array();	

	foreach($collection as $item)
	{	
		$request = json_decode($item['ds_request']);

		if(!is_array($request))
		{
			$request = json_decode(json_encode($request), True);
		}

	    $body[] = array(
			$item['dt_acesso'],
			array($item['nome'], 'text-align:left'),
			array($item['ds_link'], 'text-align:left'),
			array('<pre>'.print_r($request, TRUE).'</pre>', 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>