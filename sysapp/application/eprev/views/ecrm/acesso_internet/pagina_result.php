<?php
$body = array();
$head = array( 
	'Página',
	'Acessos'
);

foreach( $collection as $item )
{
	$body[] = array(
		array($item["pagina"], "text-align:left"),
		#$item["nr_acessos"]
		array($item["nr_acessos"],'text-align:right;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>