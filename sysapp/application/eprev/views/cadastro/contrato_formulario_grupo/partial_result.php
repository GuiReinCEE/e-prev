<?php
$body=array();
$head = array( 
	'Código', 'Formulário','Descrição','Ordem','Dt Inclusão'
);

foreach( $collection as $item )
{
	$link=anchor(
"cadastro/contrato_formulario_grupo/detalhe/" . $item["cd_contrato_formulario_grupo"], $item["ds_contrato_formulario_grupo"]
); 

$body[] = array(
 $item["cd_contrato_formulario_grupo"]
, $item["ds_contrato_formulario"]
, array($link, 'text-align:left;')
, $item["nr_ordem"]
, $item["dt_inclusao"]
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
