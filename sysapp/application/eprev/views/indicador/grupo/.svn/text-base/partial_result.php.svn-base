<?php
$body=array();
$head = array( 
	'Descriчуo',''
);

foreach( $collection as $item )
{
	$link=anchor("indicador/grupo/detalhe/" . $item["cd_indicador_grupo"], "editar"); 
	$body[] = array(array($item["ds_indicador_grupo"],'text-align:left;'), $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>