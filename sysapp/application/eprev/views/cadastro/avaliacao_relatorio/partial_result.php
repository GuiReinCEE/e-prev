<?php
$body = array();
$head = array(
	'Gerência',
	'Nome do avaliado',
	'Nome do avaliador',
	'Período',
	'Resultado final', 
	'Tipo', 
	'',
	'Promoção Dupla',
	'Imprimir' 	
);

foreach( $collection as $item )
{
	$autoavaliacao = (intval($item['cd_avaliacao_tipo_a']) > 0 ? '<a href="javascript:void(0)" onclick="imprimir('.intval($item['cd_avaliacao_capa']).', '.intval($item['cd_avaliacao_tipo_a']).')">[autoavaliação]</a>' : '' );
	
	$superior = (intval($item['cd_avaliacao_tipo_s']) > 0 ? '<a href="javascript:void(0)" onclick="imprimir('.intval($item['cd_avaliacao_capa']).', '.intval($item['cd_avaliacao_tipo_s']).')">[superior]</a>' : '' );

	$acordo = '<a href="javascript:void(0)" onclick="acordo('.intval($item['cd_avaliacao_capa']).')">[acordo]</a>';
	
	$resumo = '<a href="javascript:void(0)" onclick="resumo('.intval($item['cd_avaliacao_capa']).')">[resumo]</a>';
	
	$body[] = array(
		$item['divisao'],
        array($item['nome_avaliado'],'text-align:left;'),
        array($item['nome_avaliador'],'text-align:left;'),
        $item['dt_periodo'],
		'<span class="label label-inverse">'.number_format($item["media_geral"],2,",",".").'</span>',
        '<span class="label '.$item['tipo_promocao_color'].'">'.$item['tipo_promocao'].'</span>',
        '<span class="label '.$item['acordo_color'].'">'.$item['acordo'].'</span>',
        '<span class="label '.(trim($item['promocao_dupla']) == 'Sim' ? 'label-important' : '').'">'.$item['promocao_dupla'].'</span>',
		$autoavaliacao.' '.$superior.' '.$acordo.' '.$resumo 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>