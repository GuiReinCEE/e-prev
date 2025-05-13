<?php
$body=array();
$head = array( 
	'Tipo', 'Local', 'Descrição', 'Data'
);

foreach( $collection as $item )
{
	$body[] = array(
	  $item["tipo"]
	, $item["local"]
	, array($item["descricao"],'text-align:left;'), $item["dt_cadastro"]
);

}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
