<?php
	$head = array(
		'Dt. Inclusуo',
		'Ediчуo',
		'Cenсrio',
		'Prazo Legal',
		'Dt. Implementaчуo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$link = '';

	  	if(trim($item['cd_secao']) == 'PVST')
	  	{
	  		$link = array(anchor('ecrm/informativo_cenario_legal/ponto_vista/'.$item['cd_edicao'],$item['n_titulo_cenario']),'text-align:left');
	  	}
	  	else if(trim($item['cd_secao']) == 'AGEN')
	  	{
	  		$link = array(anchor('ecrm/informativo_cenario_legal/agenda/'.$item['cd_edicao'],$item['n_titulo_cenario']),'text-align:left');
	  	}
	  	else if(trim($item['cd_secao']) == 'LGIN')
	  	{
	  		$link = array(anchor('ecrm/informativo_cenario_legal/legislacao/'.$item['cd_edicao'].'/'.$item['cd_cenario'],$item['n_titulo_cenario']),'text-align:left');
	  	}
	  	else
	  	{
	  		$link = array(anchor('ecrm/informativo_cenario_legal/edicoes/'.$item['cd_edicao'],$item['n_titulo_cenario']),'text-align:left');
	  	}


	  	$body[] = array(
			$item['dt_inclusao'],
			array($item['n_titulo'],'text-align:left'),
			$link,
			$item['dt_legal'],
			$item['dt_implementacao']
			
	    );
	}


	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>