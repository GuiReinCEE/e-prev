<?php
$body = Array();
$head = array( 
	'Cód',
	'Resposta',
	'Valor',
	'Ordem',
);

foreach($collection as $item )
{
	$body[] = array( 
		$item["cd_resposta"],
		array(anchor('ecrm/operacional_enquete/resposta/'.$item["cd_enquete"].'/'.$item["cd_resposta"], $item["ds_resposta"]), 'text-align:left'),
		number_format($item["valor"],2,",","."),
		$item["nr_ordem"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = "tbResposta";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
