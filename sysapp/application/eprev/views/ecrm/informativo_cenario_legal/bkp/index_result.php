<?php
$body = array();
$head = array( 
	'Edição', 
	'Título', 
	'Conteúdo', 
	'Dt Inclusão'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_edicao"], 
		array( anchor( site_url("/ecrm/informativo_cenario_legal/cadastro/".$item["cd_edicao"]), $item["tit_capa"] ), 'text-align:left;' ),
		array( anchor( site_url("/ecrm/informativo_cenario_legal/conteudo/".$item["cd_edicao"]), "[editar]"), 'text-align:center;' ),
		$item["dt_edicao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>