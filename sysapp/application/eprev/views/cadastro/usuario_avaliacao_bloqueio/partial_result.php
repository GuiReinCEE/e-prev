<?php
$body=array();
$head = array( 
	'Código','Nome','Login','Data de Bloqueio',''
);

foreach( $collection as $item )
{
	$link=anchor("cadastro/usuario_avaliacao_bloqueio/detalhe/" . $item["cd_usuario_avaliacao_bloqueio"], "editar"); 
$body[] = array(
 $item["cd_usuario_avaliacao_bloqueio"]
, $item["nome"]
, $item["usuario"]
, $item["dt_bloqueio"]
, $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>