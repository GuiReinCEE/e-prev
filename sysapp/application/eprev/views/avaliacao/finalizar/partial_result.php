<?php
$body=array();
$head = array( 
	'Avaliacao Capa','Per�odo','Tipo de promo��o','Avaliado','Avaliador',''
);

foreach( $collection as $item )
{
	$link = anchor( "avaliacao/finalizar/salvar/" . md5($item["cd_avaliacao_capa"].'-f1nal1zar' ), "finalizar", array("onclick"=>"return confirm('Finalizar avalia��o?');"));

	$body[] = array(
	 $item["cd_avaliacao_capa"]
	, $item["dt_periodo"]
	, $item["tipo_promocao"]
	, $item["nome_avaliado"]
	, $item["nome_avaliador"]
	, $link );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>