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
		$item["nr_sumula"],
		$item["dt_sumula"],
		$item["dt_divulgacao"],
		anchor(site_url('gestao/sumula/sumula_pdf')."/".$item['cd_sumula'], "[ver sъmula]" , array('target' => "_blank")),
		anchor("gestao/sumula/pdf/".$item["cd_sumula"], "[ver resposta]", array('target' => "_blank"))
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>