<?php
$body=array();
$head = array( 
	'Código','TV','Nome','Comentário','IP', 'Dt Inclusão','Dt Libera','','',''
);

foreach( $collection as $item )
{
	$bloquear=$liberar='';
	
	$comentario=anchor( 'ecrm/ri_torcida_tv_comentario/detalhe/'.$item['cd_tv_comentario'], $item['comentario'] );

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"];
		$bloquear=comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_tv_comentario"]).'" );');
	}
	else
	{
		$liberar=comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_tv_comentario"]).'" );');
		$bloquear='';
	}

	$body[] = array(
		 $item["cd_tv_comentario"]
		, $item["titulo_tv"]
		, $item["nome"]
		, array($comentario,'text-align:left;')
		, $item["ip"]
		, $item["dt_inclusao"]
		, $liberar
		, $item["nome_usuario_libera"]
		, $bloquear
		, button_delete("ecrm/ri_torcida_tv_comentario/excluir",$item["cd_tv_comentario"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>