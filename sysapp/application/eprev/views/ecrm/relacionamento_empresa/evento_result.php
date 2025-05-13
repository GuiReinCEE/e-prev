<?php
$body = array();

$head = array( 
	'Evento',
	''
);

foreach( $collection as $item )
{
	$body[] = array( 
		array($item["ds_empresa_evento"], "text-align:left;"),
		'<a href="javascript:void(0)" onclick="excluir_evento('.$item["cd_empresa_evento_relaciona"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
?>