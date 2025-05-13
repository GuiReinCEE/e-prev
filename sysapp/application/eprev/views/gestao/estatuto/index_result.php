<?php
	$head = array(
		'Dt. Aprovação CD',
	    'Nº Ata',
		'Dt. Envio PREVIC',
		'Dt. Aprovação PREVIC',
		'Documento PREVIC',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		anchor('gestao/estatuto/cadastro/'.$item['cd_estatuto'], $item['dt_aprovacao_cd']),
			anchor('gestao/estatuto/cadastro/'.$item['cd_estatuto'], $item['nr_ata_cd']),			
			anchor('gestao/estatuto/cadastro/'.$item['cd_estatuto'], $item['dt_envio_spc']), 
			anchor('gestao/estatuto/cadastro/'.$item['cd_estatuto'], $item['dt_aprovacao_spc']), 
		    array(anchor('gestao/estatuto/cadastro/'.$item['cd_estatuto'],$item['ds_aprovacao_spc']), 'text-align:left;'),
			anchor(base_url().'up/estatuto/'.$item['arquivo'], '[arquivo]' , array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	