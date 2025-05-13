<?php
	$head = array(
		'Versуo',
		'Manual',
		'Ger. Responsсvel',
		'Pub. Site',
		'Dt. Aprovaчуo',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/manual/cadastro/'.$item['cd_manual'], $item['nr_versao']),
		    array($item['ds_manual_tipo'], 'text-align:left;'),
		    $item['cd_gerencia_responsavel'],
		    $item['ds_publicado_site'],
			anchor('gestao/manual/cadastro/'.$item['cd_manual'], $item['dt_referencia']),
			anchor(base_url().'up/manual/'.$item['arquivo'], '[arquivo]', array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>