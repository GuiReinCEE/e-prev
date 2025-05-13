<?php
$body=array();
$head = array( 
	'Resumo','Descriчуo','Atividade','Tarefa'
);

foreach( $collection as $item )
{
	$body[] = array(
	 $item["resumo"]
	, array($item["descricao"],'text-align:left;')
	, $item["cd_atividade"]
	, $item["cd_tarefa"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>