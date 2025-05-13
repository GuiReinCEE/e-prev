<?php
$body = array();
$head = array( 
	'Nome',
	'Telefone 1',
	'Telefone 2',	
	'Departamento',
	'Cargo'
);

foreach( $collection as $item )
{
	$body[] = array( 
		array(anchor("ecrm/relacionamento_pessoa/cadastro/".$item["cd_pessoa"].'/'.$cd_empresa, $item["ds_pessoa"] ),"text-align:left;"),
		$item["telefone_1"], 
		$item["telefone_2"],		
		array($item["ds_pessoa_departamento"], "text-align:left;"),
		array($item["ds_pessoa_cargo"], "text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = ($fl_count_grid == "S" ? true : false);
echo $grid->render();
?>