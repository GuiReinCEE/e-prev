<?php
$body=array();
$head = array( 
	'#',
	'Nome',
	'Empresa',
	'Cargo',
	'Email',
	'Telefone',
	'Celular',
	'Plano',
	'Acertos',
	'Dt Cadastro'
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["cd_quiz_cadastro"],
	array("<nobr>".$item["nome"]."</nobr>","text-align:left;"),
	array($item["empresa"],"text-align:left;"),
	array($item["cargo"],"text-align:left;"),
	array($item["email"],"text-align:left;"),
	array($item["telefone"],"text-align:left;"),
	array($item["celular"],"text-align:left;"),
	array($item["plano"],"text-align:left;"),
	$item["qt_acerto"],
	$item["dt_inclusao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
