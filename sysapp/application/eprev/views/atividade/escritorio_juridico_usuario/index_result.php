<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'CPF',
	'Escritório Jurídico',
	'Email',
	'Telefone',
	'Telefone'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("atividade/escritorio_juridico_usuario/cadastro/".$item["cd_usuario"], $item["cd_usuario"]),
		array(anchor("atividade/escritorio_juridico_usuario/cadastro/".$item["cd_usuario"],$item["nome"]),"text-align:left;"),
		$item["cpf"],
		array($item["ds_escritorio"],"text-align:left;"),
		array($item["email"],"text-align:left;"),
		$item["telefone1"],
		$item["telefone2"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>