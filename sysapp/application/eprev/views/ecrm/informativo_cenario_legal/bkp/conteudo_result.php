<?php
$body=array();
$head = array( 
	'Código',
	'Título',
	'Dt Cadastro',
	'Dt Exclusão'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_cenario"],
		array( anchor( site_url("/ecrm/informativo_cenario_legal/conteudo_cadastro/".$item["cd_edicao"]."/".$item['cd_cenario']), $item["titulo"]), 'text-align:left;' ),
		$item["dt_inclusao"],
		$item["dt_exclusao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>