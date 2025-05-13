<?php
$body=array();
$head = array( 
	'Código','Nr Ip','Ds Link Pagina','Ds Link Quebrado','Dt Erro',''
);

foreach( $collection as $item )
{
	$link=anchor("servico/link_quebrado/detalhe/" . $item["cd_log_link"], "editar"); 
$body[] = array(
 $item["cd_log_link"]
, $item["nr_ip"]
, $item["ds_link_pagina"]
, $item["ds_link_quebrado"]
, $item["dt_erro"]
, $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>