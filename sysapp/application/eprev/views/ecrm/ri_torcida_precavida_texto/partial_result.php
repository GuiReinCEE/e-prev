<?php
$body=array();
$head = array( 
	'Código', 'Texto', 'Dt Inclusão', 'Dt Libera', '', '', '', ''
);

foreach( $collection as $item )
{
	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_precavida_texto"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_precavida_texto"]).'" );');
		$bloquear='';
	}

	$link=anchor("ecrm/ri_torcida_precavida_texto/detalhe/" . $item["cd_precavida_texto"], "editar");

	$body[] = array(
		 $item["cd_precavida_texto"]
		, array($item["texto"],'text-alig:left;')
		, $item["dt_inclusao"]
		, $liberar
		, $item["nome_usuario_libera"]
		, $bloquear
		, $link 
		, button_delete("ecrm/ri_torcida_precavida_texto/excluir",$item["cd_precavida_texto"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>