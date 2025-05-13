<?php
$body = array();
$head = array( 
	"Ordem",
	"Rentabilidade",
	"Valor Projetado (%)",
	""
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["nr_ordem"],
		array(anchor("gestao/caderno_cci/projetado/".$item["cd_caderno_cci"]."/".$item["cd_caderno_cci_projetado"], $item["ds_caderno_cci_projetado"]), "text-align:left;"),
		number_format($item["nr_projetado"], 4, ",", "."),
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_projetado"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>