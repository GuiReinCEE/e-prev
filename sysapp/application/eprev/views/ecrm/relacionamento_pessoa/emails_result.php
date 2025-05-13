<?php
$body = array();

$head = array( 
	'Email',
	''
);

foreach( $collection as $item )
{
	$body[] = array( 
		array($item["ds_email"], "text-align:left;"),
		'<a href="javascript:void(0)" onclick="excluir_email('.$item["cd_pessoa_email"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>