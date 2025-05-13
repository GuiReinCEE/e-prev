<?php
	$head = array(
		'Versуo',
		'Dt. Aprovaчуo',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			(trim($item['fl_editar']) == 'S' ? anchor('gestao/plano_continuidade_negocios/cadastro/'.$item['cd_plano_continuidade_negocios'], $item['nr_versao']) : $item['nr_versao']),
			(trim($item['fl_editar']) == 'S' ? anchor('gestao/plano_continuidade_negocios/cadastro/'.$item['cd_plano_continuidade_negocios'], $item['dt_referencia']) : $item['dt_referencia']),
			anchor(base_url().'up/plano_continuidade_negocios/'.$item['arquivo'], '[arquivo]' , array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>