<?php
$body = array();
$head = array(
    '<input type="checkbox" name="checkboxCheckAll" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'Ano/Número',
	'',
	'Dt. Resolução',
	'Dt. Divulgação',
	'Descrição',
	'Situação',
	'Ata',
	'RDS',
	'Área/Div./Ger.',
	'Observação',
	'Abrangência',
	'Dt Alteração',
	'Usuário'
);

foreach ($collection as $item)
{	
	$body[] = array(
		(
			(trim($item['dt_divulgacao']) == "")
			?
			'<input type="checkbox" value="'.intval($item['cd_resolucao_diretoria']).'">'
			:
			""
		),
		(gerencia_in(array('SG')) ? anchor("gestao/resolucao_diretoria/cadastro/".$item["cd_resolucao_diretoria"], $item["ano_numero"]) : $item["ano_numero"]),
		(
			trim(substr($item['arquivo'],0,2)) == "\\\\"
			?
			anchor_file((str_replace('\\', '/',  $item['arquivo'])), "[ver]", array('target' => '_black'))
			:
			(
				trim($item['arquivo']) != ""
				?
				anchor(base_url().'up/resolucao_diretoria/'.$item['arquivo'], "[ver]", array('target' => "_blank"))
				:
				""
			)
		),
		$item["dt_resolucao_diretoria"],
		$item["dt_divulgacao"],
		array($item['ds_resolucao_diretoria'], 'text-align:left;'),
		array('<span class="'.trim($item['class_situacao']).'">'.$item['situacao'].'</span>','text-align:center;'),
		$item["nr_ata"],
		$item["rds"],
		$item["area"],
		array(nl2br($item['observacao']), 'text-align:justify;'),
		array($item['ds_resolucao_diretoria_abrangencia'], 'text-align:left;'),
		$item["dt_alteracao"],
		array($item['nome'], 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
if(!gerencia_in(array('SG')))
{
	$grid->col_oculta = Array(0);
}
echo $grid->render();
?>