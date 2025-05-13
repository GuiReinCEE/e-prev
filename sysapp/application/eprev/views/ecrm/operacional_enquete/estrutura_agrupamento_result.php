<?php
$body = Array();
$head = array( 
	'Cód',
	'Agrupamento',
	'Ordem'
);

foreach($collection as $item )
{
	$body[] = array( 
		$item["cd_agrupamento"],
		array(anchor('ecrm/operacional_enquete/agrupamento/'.$item["cd_enquete"].'/'.$item["cd_agrupamento"], $item["ds_agrupamento"]), 'text-align:left'),
		$item["nr_ordem"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = "tbAgrupamento";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
