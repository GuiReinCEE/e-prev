<?php
$body = array();
$head = array(
	'Código',
	'Grupo',
	'Dt Inclusão',
	'Usuário'
);

foreach ($collection as $item)
{
	$body[] = array(		
		anchor(site_url('atividade/atividade_cronograma_grupo/cadastro/'.intval($item['cd_atividade_cronograma_grupo'])),$item['cd_atividade_cronograma_grupo']),
		array(anchor(site_url('atividade/atividade_cronograma_grupo/cadastro/'.intval($item['cd_atividade_cronograma_grupo'])),$item['ds_atividade_cronograma_grupo']),'text-align:left'),
		$item['dt_inclusao'],
		array($item['nome'], 'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>