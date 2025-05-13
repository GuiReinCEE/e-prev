<?php
$body = array();
$head = array( 
	'Ano',
	'Acessos'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["ano"], 
		array($item["nr_acessos"],'text-align:right;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>