<?php
	$head = array( 
		'Cуdigo',
		'Sistema',
		'Gerкncia Responsбvel',
		'Responsбvel',
		'Solicitante',
		'Dt. Publicaзгo',
		'Descriзгo',
		'Acompanhamento'
	);

	$body = array();
						
	foreach($collection as $item)
	{	
		$body[] = array(
			anchor('servico/sistema_eprev/cadastro/'.$item['cd_sistema'] ,$item['cd_sistema']),
			array(anchor('servico/sistema_eprev/cadastro/'.$item['cd_sistema'] ,$item['ds_sistema']), 'text-align:justify'),
			$item['cd_gerencia_responsavel'],
			array($item['ds_responsavel'], 'text-align:left'),
			array($item['ds_solicitante'], 'text-align:left'),
			$item['dt_publicacao'],
			array(nl2br($item['ds_descricao']), 'text-align:justify'),
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>