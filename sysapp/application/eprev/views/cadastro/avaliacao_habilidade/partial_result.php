<?php
$body=array();
$head = array( 
	'Cуdigo','Descriзгo',''
);

foreach( $collection as $item )
{
	$link=anchor("cadastro/avaliacao_habilidade/detalhe/" . $item["codigo"], "editar"); 

$body[] = array(
   $item["codigo"]
   , array($item["descricao"], 'text-align:left;')
   , $link 
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>