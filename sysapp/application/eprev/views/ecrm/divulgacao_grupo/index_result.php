<?php
$body = array();
$head = array(
	'Cód',
	'Descrição',
	'Qt Registro',
	'Cód Antigo',
	'Dt Alteração',
	'Alteração',
	'Dt Inclusão',
    'Inclusão',
    'Dt. Exclusão',
    'Usuário'
);

foreach ($collection as $item)
{
	$body[] = array(
		anchor("ecrm/divulgacao_grupo/cadastro/".$item["cd_divulgacao_grupo"], $item['cd_divulgacao_grupo']),
		array(anchor("ecrm/divulgacao_grupo/cadastro/".$item["cd_divulgacao_grupo"], $item['ds_divulgacao_grupo']), 'text-align: left;'),
		'<span class="label '.(intval($item['qt_registro']) > 0 ? "label-success" : "label-important").'">'.$item['qt_registro'].'</span>',
		$item['cd_lista'],
		$item['dt_alteracao'],
		array($item['usuario_alteracao'], 'text-align: left;'),
		$item['dt_inclusao'],
        array($item['usuario_inclusao'], 'text-align: left;'),
        $item['dt_exclusao'],
        array($item['usuario_exclusao'], 'text-align: left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>