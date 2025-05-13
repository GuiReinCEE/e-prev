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
		$item["nr_sumula_conselho_fiscal"],
		$item["dt_sumula_conselho_fiscal"],
		$item["dt_divulgacao"],
		anchor(site_url('gestao/sumula_conselho_fiscal/sumula_pdf')."/".$item['cd_sumula_conselho_fiscal'], "[ver sъmula]" , array('target' => "_blank")),
		anchor("gestao/sumula_conselho_fiscal/pdf/".$item["cd_sumula_conselho_fiscal"], "[ver resposta]", array('target' => "_blank"))
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>