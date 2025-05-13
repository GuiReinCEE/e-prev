<?php
$body=array();
$head = array( 
	'Código','Nome', 'Dt Inclusão',/*'Dt Inicio',*/'Dt Libera','','',''
);

foreach( $collection as $item )
{
	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_enquete"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_enquete"]).'" );');
		$bloquear='';
	}

	$link=anchor("ecrm/ri_torcida_enquete/detalhe/".$item["cd_enquete"], "editar");

	$body[] = array(
	    $item["cd_enquete"]
	  , $item["nome"]
	  , $item["dt_inclusao"]
	  /*, $item["dt_inicio"]*/
	  , $liberar
	  , $item["nome_usuario_libera"]
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