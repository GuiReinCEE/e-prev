<?php
$body = array();
$head = array( 
	'Nº Extrato', 'Dt Extrato'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor(site_url('servico/meu_extrato/imprimir/'.$cd_registro_empregado.'/'.$cd_plano.'/'.intval($item["nr_extrato"]).'/'.$cd_indexador.'/'.$tp_patrocinadora.'/'.$item["data_base"]), $item["nr_extrato"], array("target"=>"blank")),
		$item["dt_extrato"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>