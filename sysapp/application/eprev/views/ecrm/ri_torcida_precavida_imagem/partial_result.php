<?php
$body=array();
$head = array( 
	'Código','Imagem','X1','Y1','X2','Y2','Dt Inclusão','Dt Libera','','',''
);

foreach( $collection as $item )
{
	$link=anchor("ecrm/ri_torcida_precavida_imagem/detalhe/" . $item["cd_precavida_imagem"], "editar");

	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_precavida_imagem"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_precavida_imagem"]).'" );');
		$bloquear='';
	}

	$body[] = array(
		  $item["cd_precavida_imagem"]
		, $item["imagem"] // "<img src='http://www.torcidafundacaoceee.com.br/precavida/".$item["imagem"]."' width='100' />" 
		, $item["x1"]
		, $item["y1"]
		, $item["x2"]
		, $item["y2"]
		, $item["dt_inclusao"]
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