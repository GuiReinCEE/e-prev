<?php
	$head = array( 
		'Dt. Erro',
		'Funчуo',
		'Job',
		'Comando',
		'Erro'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url('log/ver_log/'.$item['cd_job_log']), $item['dt_erro']),
			array($item['ds_funcao'],'text-align:left;'),
			array($item['ds_job'],'text-align:left;'),
			array(substr($item['ds_comando'], 0, 100),'text-align:left;'),
			array(substr($item['ds_erro'], 0, 100),'text-align:justify;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>