<?php
$body=array();
$head = array( 
	'Código',
	'Descrição',
	'Link',
	''
);

foreach( $collection as $item )
{
	
	$body[] = array(
		anchor("gestao/formulario/cadastro/".$item["cd_formulario"], $item["nr_formulario"]),
		array(anchor("gestao/formulario/cadastro/".$item["cd_formulario"], nl2br($item["ds_formulario"])) ,'text-align:left'),
		(trim($item['arquivo_nome']) != '' ? array(anchor(base_url().'up/cadastro_formulario/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;") : ''),
		'<a href="javascript:void(0);" onclick="excluir_formulario('.$item["cd_formulario"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>