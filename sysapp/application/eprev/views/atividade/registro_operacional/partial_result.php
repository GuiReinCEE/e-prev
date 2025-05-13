<?php
$body = array();
$head = array( 
	'Projeto', 
	'Processo', 
	'Dt Finalização', 
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		array(anchor(site_url('atividade/registro_operacional/cadastro/'.intval($item['cd_acompanhamento_registro_operacional'])), $item["ds_projeto"]), 'text-align:left'),
		array(anchor(site_url('atividade/registro_operacional/cadastro/'.intval($item['cd_acompanhamento_registro_operacional'])), $item["ds_registro"]), 'text-align:left'),
		$item["dt_finalizado"],
		'<a href="javascript:void(0);" onclick="imprimir('.$item["cd_acompanhamento_registro_operacional"].')">[imprimir]</a> '.
		(trim($item['dt_finalizado']) == '' ? '<a href="javascript:void(0);" onclick="excluir('.$item["cd_acompanhamento_registro_operacional"].')">[excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>