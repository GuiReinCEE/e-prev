<?php
$body = array();

$head = array( 
	'Código',
	'Título',
	'Dt Inclusão',
	'Usuário'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_enquete_grupo"],
		array(anchor("ecrm/operacional_enquete_grupo/cadastro/".$item["cd_enquete_grupo"], $item["ds_titulo"]), 'text-align:left'),
		$item["dt_cadastro"],
		$item["usuario"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>