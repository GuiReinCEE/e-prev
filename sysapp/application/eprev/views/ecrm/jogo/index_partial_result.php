<?php
$body=array();
$head = array( 
	'C�digo',
	'Nome',
	'Dt In�cio',
	'Dt Fim',
	'Dt Cadastro',
	'Usu�rio'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor("ecrm/jogo/detalhe/".$item["cd_jogo"], $item["cd_jogo"]),
	array(anchor("ecrm/jogo/detalhe/".$item["cd_jogo"],$item["ds_jogo"]),"text-align:left;"),
	$item["dt_inicio"],
	$item["dt_final"],
	$item["dt_inclusao"],
	array($item["ds_usuario"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
