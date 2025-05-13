<?php
$body=array();
$head = array( 
	'Código','Chave','Sala'
);

foreach( $collection as $item )
{
	$link=anchor("ecrm/chave/detalhe/" . $item["cd_chave"], $item["ds_chave"]);

$body[] = array(
 $item["cd_chave"]
, array($link,'text-align:left;')
, $item["cd_sala"]
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>