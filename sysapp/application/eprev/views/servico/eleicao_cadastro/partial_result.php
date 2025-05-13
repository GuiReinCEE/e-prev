<?php
$body=array();
$head = array( 
	'RE','Nome','Logradouro','Pixels','Length'
);

foreach( $collection as $item )
{
	$body[] = array(
		 $item["re"]
		, array($item["nome"],'text-align:left;')
		, array($item["logradouro"],'text-align:left;')
		, $item["pixels"]
		, $item["length"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>