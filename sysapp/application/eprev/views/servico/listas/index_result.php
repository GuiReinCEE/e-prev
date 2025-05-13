<?php
$body = array();
$head = array( 
	'Cуdigo',
	'Descriзгo',
	'Divisгo',
	'Tipo',
	'Valor',
	'Dt Exclusгo'
);
					
foreach( $collection as $item )
{	
	$body[] = array(
		anchor("servico/listas/cadastro/".$item['categoria']."/".$item["codigo"], $item["codigo"]),
		array($item['descricao'], 'text-align:left'),
		$item['divisao'],
		$item['tipo'],
		array($item['valor'], 'text-align:right'),
		$item['dt_exclusao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>