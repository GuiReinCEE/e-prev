<?php
$body = array();

$head = array( 
	'Código',
	'Nome',
	'Empresa', 
	'Departamento', 
	'Cargo' 
);

foreach( $collection as $item )
{
	$body[] = array( 
		$item["cd_pessoa"],
		array(anchor( "ecrm/relacionamento_pessoa/cadastro/".$item["cd_pessoa"], $item["ds_pessoa"] ),"text-align:left;"),
		array($item["ds_empresa"], "text-align:left;"),
		array($item["ds_pessoa_departamento"], "text-align:left;"),
		array($item["ds_pessoa_cargo"], "text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>