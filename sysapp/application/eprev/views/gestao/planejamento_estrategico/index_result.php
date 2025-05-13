<?php
	$head = array(
		'Diretriz Fundamental',
		'Ano Inicial - Final',
		'Arquivo',
		'Desdobramentos',
		'Objetivos'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			array(anchor('gestao/planejamento_estrategico/cadastro/'.$item['cd_planejamento_estrategico'], $item['ds_diretriz_fundamental']), 'text-align:left'),
		    $item['nr_ano_inicial'].' - '.$item['nr_ano_final'],
		    array(anchor(base_url().'up/planejamento_estrategico/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),		    
		    array(implode(br(), $item['desdobramento']), 'text-align:left'),
		    array(implode(br(), $item['objetivo']), 'text-align:left')
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>