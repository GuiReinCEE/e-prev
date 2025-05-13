<?php
$body=array();
$head = array( 
	'#',
	'Nome',
	'Situação',
	'Familiar',
	'Cidade',
	'UF',
	'Telefone',
	'Telefone',
	'Email',
	'Dt Alteração',
	'Usuário'
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["cd_cadastro"],
	array($item["nome"],"text-align:left;"),
	array($item["ds_cadastro_situacao"],"text-align:left;"),
	$item["qt_familiar"],
	array($item["cidade"],"text-align:left;"),
	$item["uf"],
	$item["telefone"],
	$item["celular"],
	array($item["email"],"text-align:left;"),
	$item["dt_alteracao"],
	array($item["ds_usuario_alteracao"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
