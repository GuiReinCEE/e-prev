<?php
$body = array();
$head = array(
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'Ano/Número',
	'',
	'Dt. Deliberação',
	'Dt. Divulgação',
	'Descrição',
	'Situação',
	'Ata',
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
			'<input type="checkbox" value="'.intval($item['cd_deliberacao_conselho']).'">'
			:
			""
		),
		(gerencia_in(array('SG')) ? anchor("gestao/deliberacao_conselho/cadastro/".$item["cd_deliberacao_conselho"], $item["ano_numero"]) : $item["ano_numero"]),
		(
			trim(substr($item['arquivo'],0,2)) == "\\\\"
			?
			anchor_file((str_replace('\\', '/',  $item['arquivo'])), "[ver]", array('target' => '_black'))
			:
			(
				trim($item['arquivo']) != ""
				?
				anchor(base_url().'up/deliberacao_conselho/'.$item['arquivo'], "[ver]", array('target' => "_blank"))
				:
				""
			)
		),
		$item["dt_deliberacao_conselho"],
		$item["dt_divulgacao"],
		array($item['ds_deliberacao_conselho'], 'text-align:left;'),
		array('<span class="'.trim($item['class_situacao']).'">'.$item['situacao'].'</span>','text-align:center;'),
		$item["nr_ata"],
		array(nl2br($item['observacao']), 'text-align:justify;'),
		array($item['ds_deliberacao_conselho_abrangencia'], 'text-align:left;'),
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