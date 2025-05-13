<?php
$body=array();
$head = array( 
	'Cуdigo', 'Descriзгo', 'Dt Libera', '', ''
);

foreach( $collection as $item )
{
	$link=anchor("ecrm/ri_torcida_bate_bola_cadastro/detalhe/" . $item["cd_bate_bola"], "editar"); 

$bloquear=$liberar='';

if($item["dt_libera"]!='')
{
	$liberar = $item["dt_libera"] . ' por ' . $item['nome_usuario_libera'];
	$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_bate_bola"]).'" );');
}
else
{
	$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_bate_bola"]).'" );');
	$bloquear='';
}

$body[] = array(
 $item["cd_bate_bola"]
, array($item["ds_bate_bola"],'text-align:left;')
, $liberar
, $bloquear
, $link 
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>