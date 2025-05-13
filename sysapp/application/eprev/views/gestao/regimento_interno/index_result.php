<?php
	$head = array(
		'Versуo',
		'Regimento interno',
		'Ger. Responsсvel',
		'Pub. Site',
		'Dt. Aprovaчуo',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/regimento_interno/cadastro/'.$item['cd_regimento_interno'], $item['nr_versao']),
		    array($item['ds_regimento_interno_tipo'], 'text-align:left;'),
		    $item['cd_gerencia_responsavel'],
		    $item['ds_publicado_site'],
			anchor('gestao/regimento_interno/cadastro/'.$item['cd_regimento_interno'], $item['dt_referencia']),
			anchor(base_url().'up/regimento_interno/'.$item['arquivo'], '[arquivo]', array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>