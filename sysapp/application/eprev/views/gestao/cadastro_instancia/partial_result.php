<?php
$body=array();
$head = array( 
	'C�digo','Nome',''
);

foreach( $collection as $item )
{
	$link=anchor("gestao/cadastro_instancia/detalhe/" . $item["cd_instancia"], "editar"); 
	$body[] = array(
		 $item["cd_instancia"]
		, array($item["nome"],'text-align:left;')
		, $link 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>