<?php

$body = array();
$head = array(
	'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'Dt Solicitação',
	'Dt Envio',
	'RE',
	'Nome',
	'Atual. Endereço',
	'Tipo',
	'Solicitante',
	'Enviado por'
);

foreach ($collection as $item)
{
	$campo_check = array(
      'name' => 'solicita_kit[]',
      'id' => 'solicita_kit[]',
      'value' => $item['cd_solicita_kit'],
      'checked' => FALSE,
    );
 
 
    $body[] = array(
		(trim($item["dt_envio"]) == '' ? form_checkbox($campo_check) : '') ,
		$item["dt_inclusao"],
		(trim($item["dt_envio"]) != '' ? $item["dt_envio"] : ((gerencia_in(array('GAD'))) ? '<a href="javascript:void(0)" onclick="enviar('.intval($item["cd_solicita_kit"]).')">[enviar]</a>' : '')) ,
		$item['re'],
		array($item['nome'], 'text-align:left;'),
		(trim($item['fl_endereco_atualizado']) == 'S' ? 'Sim' : 'Não'),
		array($item['ds_solicita_kit_tipo'], 'text-align:left;'),
		array($item['solicitante'], 'text-align:left;'),
		array($item['enviado'], 'text-align:left;')
    );
}

if (gerencia_in(array('GAD')))
{
	echo form_start_box("botoes_box", "Opções",true);
		echo form_default_row('', '', '<input type="button" onclick="enviar_todos();" value="Enviar Marcados" class="botao" style="width: 150px;">');			   
	echo form_end_box("botoes_box");
}
$ar_window = Array(8);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
//$grid->col_window = $ar_window;

echo $grid->render();
?>