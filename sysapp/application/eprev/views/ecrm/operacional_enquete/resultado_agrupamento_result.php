<?php
$body = Array();
$head = array('Agrupamento','Média');

foreach($ar_reg as $item )
{
	$body[] = array( 
		array($item["nome"], 'text-align:left'),
		number_format($item["vl_media"],2,",",".")
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
