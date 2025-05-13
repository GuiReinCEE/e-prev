<?php
$body=array();
$head = array(
	'Ano/Mês',
	'Item',
	'parecer/Método de Cálculo', 
	'Situação',
	'Gerência', 
	'Respondente', 
	'Responsável', 
	'Dt Envio', 
	'Dt Limite', 
	'Dt Resposta',
    'Dt Assinado',
	'Assinado',
	'Status',
	'Parecer',
	'Retorno'
);

foreach($collection as $item )
{    
	$sit = "";
	if($item['fl_situacao'] == "NR")
	{
		$sit = array("Não Respondido",'color:red; font-weight:bold; text-align:center;');
	}
	elseif($item['fl_situacao'] == "RE")
	{
		$sit = array("Respondido<BR><nobr>(Não Assinado)</nobr>",'color:green; font-weight:bold; text-align:center;');
	}
	elseif($item['fl_situacao'] == "AS")
	{
		$sit = array("Assinado",'color:blue; font-weight:bold; text-align:center;');
	}	
	
	$body[] = array(
		$item['nr_ano_mes'],
		$item['nr_item'],
		array(anchor('gestao/plano_fiscal_parecer/resposta/'.$item['cd_plano_fiscal_parecer_item'], $item['descricao']), 'text-align:left'),
		$sit,
		$item["ds_plano_fiscal_parecer_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['dt_envio'],
		array($item['dt_limite'],"text-align:center; ".(trim($item["dt_confirmacao"]) == "" ? "color:orange; font-weight:bold;": "")),
		$item["dt_resposta"],
        $item["dt_confirmacao"],
		array($item['usuario_confirmacao'], 'text-align:left'),
		$item['ds_status'],
		array($item['parecer'], 'text-align:left'),
		array($item['retorno'], 'text-align:left')
	);
}

$ar_window = Array(13,14);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();

?>