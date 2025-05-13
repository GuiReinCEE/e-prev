<?php
$body = array();
$head = array(
    'Ano/Número',
	'Participante',
	'Nome',
	'Status',
	'Dt Início',
	'Dt Encerrado',
    'Tempo Atendimento',
    'Início por',
	'Encerrado por',
	'Acompanhamento'
);


foreach ($collection as $item)
{	
	$body[] = array(
		anchor("ecrm/atendimento_individual/cadastro/".$item["cd_atendimento_individual"], $item['ano_numero']),
		$item['re'],
		array($item["nome"], 'text-align:left;'),
		'<span class="label '.trim($item['class_status']).'">'.trim($item['status']).'</span>',
		$item['dt_encaminhamento'],
        $item['dt_encerramento'],
        $item['hr_tempo_atendimento'],
		array($item["usuario_encaminhado"], 'text-align:left;'),
		array($item["usuario_encerrado"], 'text-align:left;'),
		array($item['acompanhamento'], 'text-align:justify;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>

