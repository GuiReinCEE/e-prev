<?php
$body = array();
$head = array(
	'C�d.',
    'Descri��o',
    'Qt dias',
    'Dia �til (Sim ou N�o)',
    'E-mail(s)'
);

foreach ($collection as $item)
{	
	$body[] = array(
		anchor("gestao/atas_cci_etapas_investimento/cadastro/".$item["cd_atas_cci_etapas_investimento"], $item['cd_atas_cci_etapas_investimento']),
		array(anchor("gestao/atas_cci_etapas_investimento/cadastro/".$item["cd_atas_cci_etapas_investimento"], $item['ds_atas_cci_etapas_investimento']),'text-align:left;'),
		$item['qt_dias'],
		$item['dia_util'],
		$item['email']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>