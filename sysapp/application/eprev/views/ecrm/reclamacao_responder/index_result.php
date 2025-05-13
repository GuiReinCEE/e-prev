<?php
$body = array();
$head = array(
	'Ano/N�mero',
	'Atrasado',
	'Classifica��o',
	'Quantidade ',
	'Respons�vel',
	'Dt Envio',
	'Dt Limite',
	'Dt Prorroga��o',
	'Dt Parecer da Ger�ncia',
	'Usu�rio Parecer da Ger�ncia'
);

foreach ($collection as $item)
{

	$body[] = array(
		anchor("ecrm/reclamacao_responder/cadastro/".$item["cd_reclamacao_analise"], $item['ano_numero']),
		'<span class="label '.trim($item['class_atrasado']).'">'.$item['atrasado'].'</span>',
		$item['ds_reclamacao_analise_classifica'],
		$item['quantidade'],
		array($item['responsavel'], 'text-align: left;'),
		$item['dt_envio'],
		(trim($item['dt_prorrogacao']) == '' ? '<span class="label label-important">'.$item['dt_limite'].'</span>' : '<span class="label">'.$item['dt_limite'].'</span>'),
		'<span class="label label-important">'.$item['dt_prorrogacao'].'</span>',
		'<span class="label label-success">'.$item['dt_retorno'].'</span>',
		array($item['usuario_retorno'], 'text-align: left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>