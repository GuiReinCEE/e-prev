<?php
$body = array();

$head = array( 
	'Segmento',
	''
);

foreach( $collection as $item )
{
	$body[] = array( 
		array($item["ds_empresa_segmento"], "text-align:left;"),
		'<a href="javascript:void(0)" onclick="excluir_segmento('.$item["cd_empresa_segmento_relaciona"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
?>