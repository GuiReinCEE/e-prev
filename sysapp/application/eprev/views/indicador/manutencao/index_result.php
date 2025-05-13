<?php
$body=array();
$head = array( 
	'Grupo',
	'Tipo',
	'Indicador',
	'Processo',
	'Controle',
	'Dt. Limite Atualizar',
	'Responsável',
	'Dt. Atualização',
	'IGP',
	'PODER'	
);


foreach( $collection as $item )
{
	$body[] = array(
		$item['ds_indicador_grupo'],
		$item['ds_tipo'],
		array(anchor($item['plugin_nome'], $item['ds_indicador']),'text-align:left;'),
		array($item['ds_processo'],'text-align:left;'),
		$item['ds_indicador_controle'],
		'<span class="label '.$item['status_atualizar'].'">'.$item["dt_limite_atualizar"].'</span>',
		array("- ".$item['responsavel'].br()."- ".$item['substituto'],'text-align:left;'),
		$item['dt_atualizacao'],
		$item['igp'],
		$item['poder'],		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>