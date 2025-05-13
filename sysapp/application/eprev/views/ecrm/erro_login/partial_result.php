<?php
$body=array();
$head = array( 
	'Empresa','Re','Erro','Data'
);

foreach( $collection as $item )
{
	$body[] = array(
		 $item["empresa"]
		, $item["re"]
		, $item["erro"]
		, $item["data"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>