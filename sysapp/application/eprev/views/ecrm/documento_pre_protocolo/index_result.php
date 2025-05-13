<?php
$body = array();
$head = array(
    '<input type="checkbox" id="checkboxCheckAll" onclick="checkAllProtocolo();" title="Clique para Marcar ou Desmarcar Todos">',
	'Participante',
	'Nome',
	'Documento',
	'Usuário',
	'Data',
	'Observação',
	'Descartar',
	'Folhas',
	'Arquivo',
	''
);

foreach ($collection as $item)
{	
	$campo_check = array(
		'name'        => 'check',
		'id'          => 'check',
		'value'       => $item['cd_documento_pre_protocolo'],
		'checked'     => FALSE
	);


	$body[] = array(
	    form_checkbox($campo_check),
		anchor("ecrm/documento_pre_protocolo/cadastro/".$item["cd_documento_pre_protocolo"], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
		array($item["nome"], 'text-align:left;'),
		array($item["cd_tipo_doc"] . " - " . $item['descricao_documento'], 'text-align:left;'),
		$item['usuario'],
		$item['dt_inclusao'],
		array($item['ds_observacao'], 'text-align:jutify;'),
		'<span class="label '.(trim($item['fl_descartar']) == 'S' ? 'label-important' : '').'">'.(trim($item['fl_descartar']) == 'S' ? 'Sim' : 'Não').'</span>',
		$item['nr_folha'],
		(trim($item['arquivo']) != "" ? '<a href="'.base_url().'up/documento_pre_documento/'.$item['arquivo'].'" target="_blank">'.$item['arquivo'].'</a>' : ""),
		'<a href="javascript:void(0)" onclick="excluir('.intval($item["cd_documento_pre_protocolo"]).');">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = 'tabela_lista';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>

