<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'Delegacia'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor("planos/familia_previdencia_delegacia_cidade/cadastro/".$item["cd_delegacia_cidade"], $item["cd_delegacia_cidade"]),
	array(anchor("planos/familia_previdencia_delegacia_cidade/cadastro/".$item["cd_delegacia_cidade"],$item["nome"]),"text-align:left;"),
	array($item["ds_delegacia"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
