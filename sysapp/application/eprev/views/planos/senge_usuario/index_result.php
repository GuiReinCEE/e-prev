<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'CPF',
	'Usuário',
	'Email',
	'Telefone',
	'Telefone',
	'Tipo'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("planos/senge_usuario/cadastro/".$item["cd_usuario"], $item["cd_usuario"]),
		array(anchor("planos/senge_usuario/cadastro/".$item["cd_usuario"],$item["nome"]),"text-align:left;"),
		$item["cpf"],
		array($item["usuario"],"text-align:left;"),
		array($item["email"],"text-align:left;"),
		$item["telefone_1"],
		$item["telefone_2"],
		array($item["ds_tipo_usuario"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>