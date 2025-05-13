<?php
$body = array();
$head = array( 
	"Ordem",
	"Estrutura",
	"Estrutura Oracle",
	"Rentabilidade Esperada PI (%)",
	"Limite Política Mín. (%)",
	"Limite Política Máx. (%)",
	"Limite Legal Mín. (%)",
	"Limite Legal Máx. (%)",
	"Alocação Estratégica (%)",
	"Fundo de Investimento",
	"Grupo",
	"Agrupar",
	""
);

foreach( $collection as $item )
{
	$body[] = array(
		array($item["nr_ordem"], "text-align:left"),
		array(anchor("gestao/caderno_cci/estrutura/".$item["cd_caderno_cci"]."/".$item["cd_caderno_cci_estrutura"], $item["ds_caderno_cci_estrutura"]), "text-align:left;"),
		array($item["ds_estrutura_oracle"], "text-align:left;"),
		number_format($item["nr_rentabilidade"], 2, ",", "."),
		number_format($item["nr_politica_min"], 2, ",", "."),
		number_format($item["nr_politica_max"], 2, ",", "."),
		number_format($item["nr_legal_min"], 2, ",", "."),
		number_format($item["nr_legal_max"], 2, ",", "."),
		number_format($item["nr_alocacao_estrategica"], 2, ",", "."),
		'<label class="label label-'.($item["fl_fundo"] == "S" ? 'success">Sim' : 'important">Não').'</label>',
		'<label class="label label-'.($item["fl_grupo"] == "S" ? 'success">Sim' : 'important">Não').'</label>',
		'<label class="label label-'.($item["fl_agrupar"] == "S" ? 'success">Sim' : 'important">Não').'</label>',
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_estrutura"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>