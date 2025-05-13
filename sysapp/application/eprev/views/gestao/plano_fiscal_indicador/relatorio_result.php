<?php
$body = array();
$head = array(
    'Ano/Mês',
	'Item',
	'Indicador/Método de Cálculo',
	'Ger.',
	'Respondente',
	'Responsável',
	'Copiar',
	'Dt Envio',
	'Dt Enc.',
	'Dt Limite',
	'Dt Resposta',
	'Dt Assinatura',
	'Assinado',
    'Meta',
    'Unidade',
	'Status',
	'Resultado',
	'Retorno'
);

foreach ($collection as $item)
{	
    $body[] = array(
        $item['nr_ano_mes'],
		$item["nr_item"],
		array(nl2br($item['descricao']), 'text-align:justify;'),
		$item["ds_plano_fiscal_indicador_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['fl_copiar_resultado'],
		$item["dt_envio"],
		$item["dt_encaminhamento"],
		$item["dt_limite"],
		$item["dt_resposta"],
      	array($item["dt_confirmacao"],'color:blue; font-weight:bold; text-align:center;'),
		array($item['usuario_confirmacao'],'color:blue; font-weight:bold; text-align:left;'),	
        (trim($item["meta"]) != '' ? $item["meta"] : ''),
		$item["unidade"],
		$item["ds_status"],
		array(nl2br($item['resultado']), 'text-align:justify;'),
		array(nl2br($item['retorno']), 'text-align:justify;')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>