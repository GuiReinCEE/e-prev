<?php
$body=array();
$head = array( 
	'Código','Bate Bola','Nome','Comentário','IP','Dt Inclusão','Dt Libera','',''
);

foreach( $collection as $item )
{
	$bloquear=$liberar='';
	
	$comentario=anchor('ecrm/ri_torcida_bate_bola_comentario/detalhe/'.$item['cd_bate_bola_comentario'],$item['comentario']);

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"] . ' por ' . $item['nome_usuario_libera'];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_bate_bola_comentario"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_bate_bola_comentario"]).'" );');
		$bloquear='';
	}

	$body[] = array(
		$item["cd_bate_bola_comentario"]
		, $item["descricao_bate_bola"]
		, $item["nome"]
		, array($comentario,'text-alig:left;')
		, $item["ip"]
		, $item["dt_inclusao"]
		, $liberar
		, $bloquear
		, button_delete("ecrm/ri_torcida_bate_bola_comentario/excluir",$item["cd_bate_bola_comentario"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>