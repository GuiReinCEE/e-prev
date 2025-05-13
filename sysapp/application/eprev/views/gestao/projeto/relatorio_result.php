<?php

$head = array( 
		'Código',
		'Projeto',
		'Etapas Previstas',
		'Etapas Realizadas',
		'Etapas Pendentes',
		'% de Conclusão'
	);
	
	$body = array();

	foreach($collection as $item)
	{
		
		
		$body[] = array(
			$item['cd_projeto'],
			array(anchor('gestao/projeto/cadastro/'.$item['cd_projeto'], $item['ds_projeto']), 'text-align:left'),
			$item['etapas_previstas'],
			$item['etapas_realizadas'],
			$item['etapas_pendentes'],
			(intval($item['etapas_previstas']) == 0 ? array(progressbar(0), number_format(0, 2, ',', '.')) : array(progressbar(intval( ($item['etapas_realizadas']*100)/$item['etapas_previstas'] )), number_format(($item['etapas_realizadas']*100)/$item['etapas_previstas'], 2, ',', '.')))
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo $grid->render();
?>