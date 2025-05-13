<?php
$body=array();
$head = array( 
	'EMP/RE/SEQ',
	'Nome',
	'Nr FAX',
	'Dt Envio', 
	'Usuário',
	'Arquivo'	
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	array($item["nome"],"text-align:left;"),
	$item["nr_telefone"],
	$item["dt_envio"],
	array($item["nome_usuario"],"text-align:left;"),
	anchor(base_url()."up/fax/".$item["ds_arquivo"],"Ver","target='_blank'")
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
