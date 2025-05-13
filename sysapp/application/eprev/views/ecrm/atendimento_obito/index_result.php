<?php
$body=array();
$head = array( 
	'RE',
	'Nome',
	'Dt Óbito',
	'Dt Dig. Óbito',
	'Dt Últ. Acomp.',
	'Dt Encerrado'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("ecrm/atendimento_obito/detalhe/".$item["cd_atendimento_obito"], $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"]),
		array(anchor("ecrm/atendimento_obito/detalhe/".$item["cd_atendimento_obito"],$item["nome"]),"text-align:left;"),
		$item["dt_obito"],
		$item["dt_dig_obito"],
		$item["dt_acompanha"],
		$item["dt_encerrado"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
