<?php
	$head = array( 
		'Ediзгo', 
		'Tнtulo', 
		'Normativo',
		'Arquivo'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor(site_url('ecrm/informativo_cenario_legal/normativo/'.$item['cd_edicao'].'/'.$item['cd_cenario']), $item['cd_edicao']), 'text-align:left;'),
			array(anchor(site_url('ecrm/informativo_cenario_legal/normativo/'.$item['cd_edicao'].'/'.$item['cd_cenario']), $item['tit_capa']), 'text-align:left;'),
			array(anchor(site_url('ecrm/informativo_cenario_legal/normativo/'.$item['cd_edicao'].'/'.$item['cd_cenario']), $item['titulo']), 'text-align:left;'),
			array(anchor(base_url().'up/cenario/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>