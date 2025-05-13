<?php
$body=array();
$head = array( 
	'',
	'Patrimônio',
	'Equipamento',
	'Dt Cad.',
	'Tipo',
	'Situação',
	'Nome',
	'IP',
	'Sis. Operacional',
	'Instalação SO',
	'Processador',
	'Memória',
	'Resolução',
	'Navegador',
	'Gerência',
	'Sala',
	'CPUScanner',
	'Versão',
	'Login no e-prev'
	
);

foreach( $collection as $item )
{
	$bg_color = '';		
	if($item['fl_cpuscanner'] != 'S')
	{
		$bg_color = 'red';
	}

	$equipamento = "<span style='color:$bg_color;'>".$item["ds_equipamento"]."</span>";

	$body[] = array(
		'<img title="Clique para confirmar verficação do CPUScanner Manual" src="'.base_url().'img/btn_confirmar.gif" onclick="setCPUScannerManual('.$item["nr_patrimonio"].');" style="cursor:pointer;">'
		, anchor("cadastro/equipamento/detalhe/".$item["nr_patrimonio"], $item["nr_patrimonio"])
		, anchor("cadastro/equipamento/detalhe/".$item["nr_patrimonio"], $equipamento)
		, $item["dt_equipamento"]
		, $item["ds_tipo"]
		, $item["ds_situacao"]
		, $item["nome_computador"]
		, $item["nr_ip"]
		, $item["sistema_operacional_categoria"]." ".$item["sistema_operacional_tipo"]
		, $item["dt_instalacao_os"]
		, $item["processador_nome"]
		, $item["qt_memoria"]
		, $item["monitor_resolucao"]
		, 
		(trim($item["versao_explorer"]) != "" ? "<nobr>IExplorer: ".substr($item["versao_explorer"],0,20)."</nobr>" : "").
		(trim($item["versao_firefox"]) != "" ? br()."<nobr>Firefox: ".substr($item["versao_firefox"],0,20)."</nobr>" : "").
		(trim($item["versao_chrome"]) != "" ? br()."<nobr>Chrome: ".substr($item["versao_chrome"],0,20)."</nobr>" : "")
		, $item["cd_divisao"]
		, $item["ds_sala"]
		, $item["dt_cpuscanner"]
		, $item["versao_cpuscanner"]
		, $item["dt_eprev"]
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>