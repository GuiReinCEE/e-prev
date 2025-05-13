<?php
$body=array();
$head=array( 
	'Código',
	'Família',
	'Cargo'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("cadastro/avaliacao_cargo/cadastro/" . $item["cd_cargo"], $item["cd_cargo"]),
		$item["nome_familia"],
		array(anchor("cadastro/avaliacao_cargo/cadastro/" . $item["cd_cargo"], $item["nome_cargo"]),"text-align:left;") 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>