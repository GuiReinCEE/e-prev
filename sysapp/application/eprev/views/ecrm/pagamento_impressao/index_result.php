<?php
$body=array();
$head = array( 
	'Cód',
	'RE',
	'Nome',
	'Tipo Documento',
	'API',
	'PDF',
	'Identificador',
	'Registro',
	'Ambiente',
	'Status',
	'Valor',
	'Pago',
	'Referência',
	'Dt Vencimento',
	'Dt Impressão',
	'IP',
	"Local",
	"Origem",
	'Linha Dig.'
);

foreach($collection as $item)
{
	$body[] = array(
		anchor(site_url("ecrm/pagamento_impressao/cadastro")."/".$item['cd_auto_atendimento_pagamento_impressao'], $item["cd_auto_atendimento_pagamento_impressao"]),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		
		(
			$item["tp_documento"] == "BDL" ? 
				'<span class="label label-inverse">'.$item["tp_documento"].'</span>' 
			: 
				'<span class="label">'.$item["tp_documento"].'</span>'
		),
		(
			$item["fl_tipo_registro"] == "JSON" ? 
				'<span class="label label-inverse">'.$item["fl_tipo_registro"].'</span>' 
			: 
				'<span class="label">'.$item["fl_tipo_registro"].'</span>'
		),		
		
		(($item["tp_documento"] == "BDL" AND $item["tp_registro_ambiente"] == "P") ?
			anchor("https://www.fundacaofamiliaprevidencia.com.br/bdl.php?b=".$item['cd_auto_atendimento_pagamento_impressao_md5'], "[BDL]", array("target" => "_blank"))
			: ""),
		$item["num_bloqueto"],
		$item["nr_registro"],
		($item["tp_registro_ambiente"] == "P" ? '<span class="label label-warning">'.$item["tp_registro_ambiente"].'</span>' : '<span class="label">'.$item["tp_registro_ambiente"].'</span>'),
		($item["fl_erro_registro"] == "S" ? '<span class="label label-important">ERRO</span>' : '<span class="label label-success">OK</span>'),
		number_format($item["vl_valor"],2,',','.'),
		($item["fl_pago"] == "S" ? '<span class="label label-inverse">'.$item["fl_pago"].'</span>' : '<span class="label">'.$item["fl_pago"].'</span>'),
		(trim($item["competencia_lista"]) ? $item["competencia_lista"] : $item["ano_competencia"]."/".$item["mes_competencia"]),
		$item["dt_vencimento"],
		$item["dt_impressao"],
		$item["ip"],
		($item["fl_origem"] == "I" ? '<span class="label label-info">Interno</span>' : '<span class="label">Externo</span>'),
		($item["origem"] == "A" ? '<span class="label label-success">'.$item["ds_origem"].'</span>' : '<span class="label label-warning">'.$item["ds_origem"].'</span>'),
		$item["codigo_barra"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>