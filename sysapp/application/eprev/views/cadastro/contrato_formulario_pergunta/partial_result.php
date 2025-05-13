<?php
$body=array();
$head = array( 
	'Código','Pergunta','Ordem','Data de cadastro','Usuário','Grupo','Formulário'
);

foreach( $collection as $item )
{
	$link=anchor( "cadastro/contrato_formulario_pergunta/detalhe/".intval($item["cd_contrato_formulario_pergunta"]), $item["ds_contrato_formulario_pergunta"] );
	$body[] = array(
		$item["cd_contrato_formulario_pergunta"]
		, array($link, 'text-align:left;')
		, $item["nr_ordem"]
		, $item["dt_inclusao"]
		, $item["nome_usuario_inclusao"]
		, $item["grupo"]
		, $item["formulario"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
