<?php
$body=array();
$head = array( 
	'Cód.',
	'Dt. Inicial',
	'Dt. Final',
	'Dia Limite'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("atividade/entidade_termo/cadastro/".$item["cd_termo"], $item["cd_termo"]),
		$item['dt_inicial'],
		$item['dt_final'],
		$item['nr_dia_termo']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>