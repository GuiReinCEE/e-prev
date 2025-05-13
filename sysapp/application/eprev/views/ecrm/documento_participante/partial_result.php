<?php
$body=array();
$head = array( 
	'EMP/RE/SEQ',
	'Documento',
	'Dt Documento',
	'Imagem'
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	array($item["nome_documento"],"text-align:left;"),
	$item["dt_documento"],
	anchor("ecrm/documento_participante/documento/".base64_encode("\\".$item["caminho_imagem"]),"[visualizar]","target='_blank'")
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
