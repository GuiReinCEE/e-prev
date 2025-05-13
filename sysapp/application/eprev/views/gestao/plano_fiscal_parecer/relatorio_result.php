<?php
$body = array();
$head = array(
    'Ano/Mъs',
	'Item',
	'Descriчуo',
	'Ger.',
	'Respondente',
	'Responsсvel',
	'Copiar',
	'Dt Envio',
	'Dt Enc.',
	'Dt Limite',
	'Dt Resposta',
	'Dt Assinatura',
	'Assinado',
	'Status',
	'Parecer',
	'Retorno'
);

foreach ($collection as $item)
{	
    $body[] = array(
        $item['nr_ano_mes'],
		$item["nr_item"],
		array(nl2br($item['descricao']), 'text-align:justify;'),
		$item["ds_plano_fiscal_parecer_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['fl_copiar_resultado'],
		$item["dt_envio"],
		$item["dt_encaminhamento"],
		$item["dt_limite"],
		$item["dt_resposta"],
      	array($item["dt_confirmacao"],'color:blue; font-weight:bold; text-align:center;'),
		array($item['usuario_confirmacao'],'color:blue; font-weight:bold; text-align:left;'),	  
		$item["ds_status"],
		array(nl2br($item['parecer']), 'text-align:justify;'),
		array(nl2br($item['retorno']), 'text-align:justify;')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>