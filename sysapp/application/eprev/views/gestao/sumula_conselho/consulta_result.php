<?php
$body = array();
$head = array(
	'Nъmero',
	'Dt Sъmula',
	'Dt Divulgaзгo',
	'Sъmula',
	'Resposta'
);

foreach ($collection as $item)
{
	$body[] = array(
		$item["nr_sumula_conselho"],
		$item["dt_sumula_conselho"],
		$item["dt_divulgacao"],
		anchor(site_url('gestao/sumula_conselho/sumula_pdf')."/".$item['cd_sumula_conselho'], "[ver sъmula]" , array('target' => "_blank")),
		anchor("gestao/sumula_conselho/pdf/".$item["cd_sumula_conselho"], "[ver resposta]", array('target' => "_blank"))
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>