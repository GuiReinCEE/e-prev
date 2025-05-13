<?php
$body = array();
$head = array( 
	'Cód',
	'Indicador',
	'Grupo',
	'Tipo',
	'Config SA',
	'Controle',
	'Dt Limite Atualizar',
	'Responsável',
	'Dt Atualização',
	'Processo',
	'PE',
	'PODER',
	'Ordem',	
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item['cd_indicador'],
		array(anchor('indicador/cadastro/detalhe/'.$item['cd_indicador'], $item['ds_indicador']),'text-align:left;'),
		$item['ds_indicador_grupo'],
		$item['cd_tipo'],
		'<span class="label '.($item['fl_sa'] == "S" ? "label-inverse" : "").'">'.$item["fl_sa"].'</span>',
		$item['ds_indicador_controle'],
		'<span class="label '.$item['status_atualizar'].'">'.$item["dt_limite_atualizar"].'</span>',
		array("- ".$item['responsavel'].br()."- ".$item['substituto'],'text-align:left;'),
		$item['dt_atualizacao'],
		array($item['ds_processo'],'text-align:left;'),
		$item['igp'],
		$item['poder'],
		$item['nr_ordem'],
		"<a href='javascript:carregar_tabelas(".$item['cd_indicador'].");'>[exibir tabelas]</a>".br(2)."<div id='div_indicador_".$item['cd_indicador']."' style='margin-left:20px; display:none;'></div>" 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>