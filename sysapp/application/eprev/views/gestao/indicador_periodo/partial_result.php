<?php
$body=array();
$head = array( 
	'Descriчуo','Dt Inicio','Dt Fim',''
);

foreach( $collection as $item )
{
	$link=anchor("gestao/indicador_periodo/detalhe/" . $item["cd_indicador_periodo"], "editar"); 
$body[] = array(
 $item["ds_periodo"]
, $item["dt_inicio"]
, $item["dt_fim"]
, $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>