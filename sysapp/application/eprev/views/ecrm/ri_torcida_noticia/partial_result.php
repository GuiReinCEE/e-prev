<?php
$body=array();
$head = array( 
	'Código', 'Título','Tipo','Dt Inclusão','Dt Libera','','', ''
);

foreach( $collection as $item )
{
	$link=anchor("ecrm/ri_torcida_noticia/detalhe/" . $item["cd_noticia"], "editar");

	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"] . ' por ' . $item['nome_usuario_libera'];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_noticia"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_noticia"]).'" );');
		$bloquear='';
	}
	 
	$body[] = array(
	$item["cd_noticia"]
	, array( $item["ds_titulo"], 'text-align:left;' )
	, (  ($item["tp_noticia"]=='G')?'Gol Contra':'Novidade'  )
	, $item["dt_inclusao"]
	, $liberar
	, $bloquear 
	, $link 
	, button_delete("ecrm/ri_torcida_noticia/excluir", $item["cd_noticia"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>