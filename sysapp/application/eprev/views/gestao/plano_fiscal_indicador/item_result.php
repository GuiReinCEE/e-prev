<?php

$body = array();
$head = array(
	'Item',
	'Indicador/Método de Cálculo',
	'Critério',
	'Periodicidade',
	'Ger.',
	'Respondente',
	'Responsável',
	'Copiar',
	'Dt Envio',
	'Dt Limite',
	'Dt Resposta',
	'Dt Assinatura',
	'Assinado',
	#'Peso',
	'Meta',
	'Unidade',
	'Status',
	'Resultado',
	'Retorno',
	''
);

foreach ($collection as $item)
{
    $editar  = '<a href="javascript:void(0);" onclick="editar('.$item['cd_plano_fiscal_indicador_item'].')">[editar]</a>';
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_plano_fiscal_indicador_item'].')" style="color:red;font-weight:bold;">[excluir]</a>';
    $enviar  = '<a href="javascript:void(0);" onclick="enviar('.$item['cd_plano_fiscal_indicador_item'].')">[enviar]</a>';
    $reabrir = '<a href="javascript:void(0);" onclick="reabrir('.$item['cd_plano_fiscal_indicador_item'].')" style="color:green;font-weight:bold;">[reabrir]</a>';
    
    $body[] = array(
		$item["nr_item"],
		array($item['descricao'], 'text-align:left'),
		$item['criterio'],
		$item['ds_plano_fiscal_indicador_periodicidade'],
		$item["ds_plano_fiscal_indicador_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['fl_copiar_resultado'],
		$item["dt_envio"],
		array($item["dt_limite"],'color:red; font-weight:bold; text-align:center;'),
		$item["dt_resposta"],
		array($item["dt_confirmacao"],'color:blue; font-weight:bold; text-align:center;'),
		array($item['usuario_confirmacao'],'color:blue; font-weight:bold; text-align:left;'),
		#(trim($item["peso"]) != '' ? number_format($item["peso"], $item['qt_decimal'], ',', '.') : ''),
		(trim($item["meta"]) != '' ? $item["meta"] : ''),
		$item["unidade"],
		$item["ds_status"],
		array($item['resultado'], 'text-align:left'),
		array($item['retorno'], 'text-align:left'),
		(trim($item["dt_encerra"]) == '' ?
            $editar.' '.$excluir.
            (trim($item["dt_envio"]) == '' ? $enviar : '').
            (trim($item["dt_confirmacao"]) != '' ? $reabrir : '') 
        : '')
    );
}



$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->w_detalhe = true;
$grid->w_detalhe_col_iniciar = 14;
echo $grid->render();
?>