<?php
$body = array();

$head = array( 
	'Grupo',
	''
);

foreach( $collection as $item )
{
	$body[] = array( 
		array($item["ds_empresa_grupo"], "text-align:left;"),
		'<a href="javascript:void(0)" onclick="excluir_grupo('.$item["cd_empresa_grupo_relaciona"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
?>