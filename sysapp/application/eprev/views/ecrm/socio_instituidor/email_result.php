<?php

	$head = array( 
		'Cód.',  
		'Dt Email', 
		'Dt Envio', 
		'Assunto',
		'Situação',
	    'Para',
		'Cc',
	    'Cco'
	);

	$body = array();

	foreach($collection as $item)
	{
		$status = '';

		if(trim($item['fl_retornou']) == 'S')
		{
			$status = '<span class="label label-important">Retornou</span>';
		}
		else
		{
			if(trim($item['dt_envio']) != '')
			{
				$status = '<span class="label">Normal</span>';
			}		
			else
			{
				$status = '<span class="label label-info">Aguardando envio</span>';
			}
		}

		$body[] = array(
		    anchor('ecrm/reenvio_email/index/'.$item['cd_email'], $item['cd_email'], 'target="_blank"'),
		    $item['dt_email'],
		    $item['dt_envio'],
		    array($item['assunto'], 'text-align:left'),
			$status,
		    array($item['para'], 'text-align:left'),
		    array($item['cc'], 'text-align:left'),
		    array($item['cco'], 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>