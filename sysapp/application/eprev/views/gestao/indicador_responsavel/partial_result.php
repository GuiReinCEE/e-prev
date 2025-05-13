<?php
$body=array();
$head=array( 'Responsável','' );

foreach( $collection as $item )
{
	$link=anchor("gestao/indicador_responsavel/detalhe/".$item["cd_indicador_administrador"], "editar"); 
	$body[]=array( array( $item["nome"], 'text-align:left;' ), $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head=$head;
$grid->body=$body;
echo $grid->render();
?>