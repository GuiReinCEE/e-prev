<?php
$body = array();
$head = array( 
	'Ano/Mês', 
	'Qt Dia Útil', 
	'Qt Com',
	'Qt Sem',
	'Resultado'
);


foreach( $collection as $item )
{
	$percent = 0;

	if(intval($item['qt_dia_mes']) > 0)
	{
		$percent = (intval($item['qt_dia_mes']) - intval($item['qt_dia_sem'])) / ($item['qt_dia_mes']) * 100;
	}
	$body[] = array(
		$item['ano_mes'], 
		$item['qt_dia_mes'], 
		intval($item['qt_dia_mes']) - intval($item['qt_dia_sem']), 
		$item['qt_dia_sem'], 
		array(progressbar($percent, "pb_".$item['ano_mes_percent']),"text-align:left;"),
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>