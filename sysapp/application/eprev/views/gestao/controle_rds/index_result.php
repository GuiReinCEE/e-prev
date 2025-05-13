<?php
	$head = array(
		'Nъmero/Ano',
		'Dt. RDS',
		'Descriзгo',
		'Gerкncia',
		'Ata',
		'Dt. Reuniгo',
		'Dt. Inclusгo',
		'Usuбrio'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		if(gerencia_in(array('GRC')))
		{
			$link = anchor('gestao/controle_rds/cadastro/'.$item['cd_controle_rds'], $item['nr_ano_numero']);
		}
		else
		{
			$link = anchor(base_url().'up/controle_rds/'.$item['arquivo'], $item['nr_ano_numero'], array('target' => "_blank"));
		}

		$body[] = array(
			$link,
			$item['dt_rds'],
			array(anchor('gestao/controle_rds/rds_pdf/'.$item['cd_controle_rds_md5'], $item['ds_controle_rds'], array('target' => "_blank")), 'text-align:justify;'),
			implode(', ', $item['gerencia']),
			$item['nr_ata'],
			$item['dt_reuniao'],
			$item['dt_inclusao'],
			array($item['ds_usuario_inclusao'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>