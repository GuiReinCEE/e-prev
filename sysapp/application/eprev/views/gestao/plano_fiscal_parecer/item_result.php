<?php

$body = array();
$head = array(
	'Item',
	'Descrição',
	'Ger.',
	'Respondente',
	'Responsável',
	'Copiar',
	'Dt Envio',
	'Dt Limite',
	'Dt Resposta',
	'Dt Assinatura',
	'Assinado',
	'Status',
	'Parecer',
	'Retorno',
	''
);

foreach ($collection as $item)
{	
	$editar  = '<a href="javascript:void(0);" onclick="editar('.$item['cd_plano_fiscal_parecer_item'].')">[editar]</a>';
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_plano_fiscal_parecer_item'].')" style="color:red;font-weight:bold;">[excluir]</a>';
    $enviar  = '<a href="javascript:void(0);" onclick="enviar('.$item['cd_plano_fiscal_parecer_item'].')">[enviar]</a>';
    $reabrir = '<a href="javascript:void(0);" onclick="reabrir('.$item['cd_plano_fiscal_parecer_item'].')" style="color:green;font-weight:bold;">[reabrir]</a>';
    
    $body[] = array(
		$item["nr_item"],
		array($item['descricao'], 'text-align:left'),
		$item["ds_plano_fiscal_parecer_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['fl_copiar_resultado'],
		$item["dt_envio"],
		$item["dt_limite"],
		$item["dt_resposta"],
      	array($item["dt_confirmacao"],'color:blue; font-weight:bold; text-align:center;'),
		array($item['usuario_confirmacao'],'color:blue; font-weight:bold; text-align:left;'),	  
		$item["ds_status"],
		array($item['parecer'], 'text-align:left'),
		array($item['retorno'], 'text-align:left'),
        (trim($item["dt_encerra"]) == '' ?
            $editar.' '.$excluir.
            (trim($item["dt_envio"]) == '' ? $enviar : '').
            (trim($item["dt_confirmacao"]) != '' ? $reabrir : '') 
        : '')
    );
}

$ar_window = Array(1,12,13);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();
?>