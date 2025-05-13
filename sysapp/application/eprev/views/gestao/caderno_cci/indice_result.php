<?php
$body = array();
$head = array( 
	"Ordem",
	"Índice",
	""
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["nr_ordem"],
		array(anchor("gestao/caderno_cci/indice/".$item["cd_caderno_cci"]."/".$item["cd_caderno_cci_indice"], $item["ds_caderno_cci_indice"]), "text-align:left;"),
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_indice"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>