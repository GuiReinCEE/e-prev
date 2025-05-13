<?php
$body = array();
$head = array( 
	"Ordem",
	"Benchmark",
	""
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["nr_ordem"],
		array(anchor("gestao/caderno_cci/benchmark/".$item["cd_caderno_cci"]."/".$item["cd_caderno_cci_benchmark"], $item["ds_caderno_cci_benchmark"]), "text-align:left;"),
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_benchmark"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>