<?php
	$head = array(
		'Versгo',
		'Polнtica',
		'Ger. Responsбvel',
		'Pub. Site',
		'Dt. Aprovaзгo',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/politica/cadastro/'.$item['cd_politica'], $item['nr_versao']),
		    array($item['ds_politica_tipo'], 'text-align:left;'),
		    $item['cd_gerencia_responsavel'],
		    $item['ds_publicado_site'],
			anchor('gestao/politica/cadastro/'.$item['cd_politica'], $item['dt_referencia']),
			anchor(base_url().'up/politica/'.$item['arquivo'], '[arquivo]', array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>