<?php
$body=array();
$head = array( 
	'Código','Título','Dt Inclusão','Dt Libera','','', ''
);

foreach( $collection as $item )
{
	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"].' por '.$item['nome_usuario_libera'];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_tv"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_tv"]).'" );');
		$bloquear='';
	}

	$link=anchor("ecrm/ri_torcida_tv/detalhe/".$item["cd_tv"], "editar"); 

	$body[] = array(
		 $item["cd_tv"]
		, $item["titulo"]
		, $item["dt_inclusao"]
		, $liberar
		, $bloquear
		, $link
		, button_delete("ecrm/ri_torcida_tv/excluir",$item["cd_tv"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>