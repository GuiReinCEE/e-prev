<?php
$body = array();
$head = array(
	'N�mero',
	'Dt S�mula',
	'Dt Divulga��o',
	'S�mula',
	'Resposta'
);

foreach ($collection as $item)
{
	$body[] = array(
		$item["nr_sumula_interventor"],
		$item["dt_sumula_interventor"],
		$item["dt_divulgacao"],
		anchor(site_url('gestao/sumula_interventor/sumula_interventor_pdf')."/".$item['cd_sumula_interventor'], "[ver s�mula]" , array('target' => "_blank")),
		anchor("gestao/sumula_interventor/pdf/".$item["cd_sumula_interventor"], "[ver resposta]", array('target' => "_blank"))
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>