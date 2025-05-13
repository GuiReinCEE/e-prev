<?php
$body=array();
$head = array(
	'Ano/Mês',
	'Item',
	'Indicador/Método de Cálculo', 
	'Situação',	
	'Gerência', 
	'Respondente', 
	'Responsável', 
	'Dt Envio', 
	'Dt Limite', 
	'Dt Resposta',
    'Dt Assinado',
	'Assinado',
	#'Peso',
	'Meta',
	'Unidade',
	'Status',
	'Resultado',
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
		$sit = array('Respondido<BR><BR><nobr><span style="color:orangered; font-weight:bold;">(Não Assinado)</span></nobr>','color:green; font-weight:bold; text-align:center;');
	}
	elseif($item['fl_situacao'] == "AS")
	{
		$sit = array("Assinado",'color:blue; font-weight:bold; text-align:center;');
	}	
	
	$body[] = array(
		$item['nr_ano_mes'],
		$item['nr_item'],
		array(anchor('gestao/plano_fiscal_indicador/resposta/'.$item['cd_plano_fiscal_indicador_item'], $item['descricao']), 'text-align:left'),
		$sit,
		$item["ds_plano_fiscal_indicador_area"],
		array($item['nome'], 'text-align:left'),
		array($item['gerente'], 'text-align:left'),
		$item['dt_envio'],
		array($item['dt_limite'],"text-align:center; ".(trim($item["dt_confirmacao"]) == "" ? "color:orangered; font-weight:bold;": "")),
		$item["dt_resposta"],
		$item["dt_confirmacao"],
		array($item['usuario_confirmacao'], 'text-align:left'),
		#(trim($item["peso"]) != '' ? number_format($item["peso"], $item['qt_decimal'], ',', '.') : ''),
		(trim($item["meta"]) != '' ? $item["meta"] : ''),
		$item["unidade"],
		$item["ds_status"],
		array($item['resultado'], 'text-align:left'),
		array($item['retorno'], 'text-align:left')
	);
}



$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->w_detalhe = true;
$grid->w_detalhe_col_iniciar = 2;
echo $grid->render();

?>