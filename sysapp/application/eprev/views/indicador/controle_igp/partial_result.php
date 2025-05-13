<?php
$body=array();
$head = array( 
	'Ano/Mês',
	'Encerrado por',
	'Dt. Encerrado'
);

foreach( $collection as $item )
{
	$link = '<a href="javascript:void(0)" onclick="encerrar('.$item["cd_igp"].',\''.$item["dt_referencia"].'\')">[encerrar]</a>';

	$body[] = array(
		$item["dt_referencia"],
		$item["nome"],
		(trim($item['dt_encerrar']) == '' ? $link : $item['dt_inclusao'])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>