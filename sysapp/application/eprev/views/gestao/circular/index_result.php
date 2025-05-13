<?php
$body = array();
$head = array(
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'Ano/Número',
	'',
	'Dt. Circular',
	'Dt. Divulgação',
	'Descrição',
	'Situação',
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
			'<input type="checkbox" value="'.intval($item['cd_circular']).'">'
			:
			""
		),
		(gerencia_in(array('GC')) ? anchor("gestao/circular/cadastro/".$item["cd_circular"], $item["ano_numero"]) : $item["ano_numero"]),
		(
			trim(substr($item['arquivo'],0,2)) == "\\\\"
			?
			anchor_file((str_replace('\\', '/',  $item['arquivo'])), "[ver]", array('target' => '_black'))
			:
			(
				trim($item['arquivo']) != ""
				?
				anchor(base_url().'up/circular/'.$item['arquivo'], "[ver]", array('target' => "_blank"))
				:
				""
			)
		),
		$item["dt_circular"],
		$item["dt_divulgacao"],
		array($item['ds_circular'], 'text-align:left;'),
		array('<span class="'.trim($item['class_situacao']).'">'.$item['situacao'].'</span>','text-align:center;'),
		array(nl2br($item['observacao']), 'text-align:justify;'),
		array($item['ds_circular_abrangencia'], 'text-align:left;'),
		$item["dt_alteracao"],
		array($item['nome'], 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
if(!gerencia_in(array('GC')))
{
	$grid->col_oculta = Array(0);
}
echo $grid->render();
?>