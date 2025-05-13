<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'Usuário',
	'Email',
	'Telefone',
	'Telefone',
	'Tipo',
	'Função',
	'Delegacia'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor("planos/familia_previdencia_usuario/cadastro/".$item["cd_usuario"], $item["cd_usuario"]),
	array(anchor("planos/familia_previdencia_usuario/cadastro/".$item["cd_usuario"],$item["nome"]),"text-align:left;"),
	array($item["usuario"],"text-align:left;"),
	array($item["email"],"text-align:left;"),
	$item["telefone_1"],
	$item["telefone_2"],
	array($item["ds_tipo_usuario"],"text-align:left;"),
	array($item["funcao"],"text-align:left;"),
	array($item["delegacia"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
