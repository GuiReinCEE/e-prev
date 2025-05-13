<?php
$body=array();
$head = array( 
	'Código','Descrição','Dt cadastro','Usuário', ''
);

foreach( $collection as $item )
{
	$link_duplicar="<input class='botao' type='button' value='Duplicar' onclick='duplicar(\"".$item['cd_contrato_formulario']."\")' />";
	$link=anchor("cadastro/contrato_formulario/detalhe/" . $item["cd_contrato_formulario"], $item["ds_contrato_formulario"]);
	$body[] = array($item["cd_contrato_formulario"], array($link, 'text-align:left'), $item["dt_inclusao"], $item["nome_usuario_inclusao"], $link_duplicar);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
