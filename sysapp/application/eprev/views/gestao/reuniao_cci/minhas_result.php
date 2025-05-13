<?php
$body = array();
$head = array(
	'Ano/Número',
	'Tipo',
	'Data',
	'Horário',
	'Local',
	'Dt Envio',
    'Status',
    'Dt. Aprovado',
    'Dt. Desaprovado'
);

foreach ($collection as $item)
{	
	$body[] = array(
		anchor("gestao/reuniao_cci/responder/".$item["cd_reuniao_cci"], $item['ano_numero']),
		array($item['ds_reuniao_cci_tipo'], 'text-align:left'),
        $item['dt_reuniao_cci'],
        $item['hr_reuniao_cci'],
        array($item['ds_reuniao_cci_local'], 'text-align:left'),
        $item['dt_enviado'],
        '<span class="'.trim($item['class_status']).'">'.trim($item['status']).'</span>',
        $item['dt_aprovado'],
        $item['dt_desaprovado']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>