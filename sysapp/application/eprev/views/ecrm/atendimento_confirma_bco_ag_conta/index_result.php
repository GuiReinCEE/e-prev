<?php
$head = array( 
	'Cód. Atendimento',
	'Origem Atendimento',
	'Atendente',
	'RE',
	'Nome',
	'Dt. Alteração',
	'Motivo da Pendência',
	'',
	''
);

$body = array();

foreach($collection as $item)
{	
	$config = array(
		'name'   => 'nr_motivo_'.$item['cd_atendimento'], 
		'id'     => 'nr_motivo_'.$item['cd_atendimento'],
		'onblur' => 'alterar_motivo('.$item['cd_atendimento'].', '.intval($item['cd_atendimento_confirma_bco_ag_conta']).');',
		'style'  => 'display:none;',
		'rows'   => '5',
		'cols'   => '50'
	);
    
	$body[] = array(
        $item['cd_atendimento'],
		$item['fl_indic_ativo'],
		array($item['atendente'], 'text-align:left;'),
		$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
        array($item['nome'], 'text-align:left;'),
        $item['dt_alteracao'],
		array('<span id="ajax_motivo_valor_'.$item['cd_atendimento'].'"></span> '.
		'<span id="motivo_valor_'.$item['cd_atendimento'].'">'.$item['ds_observacao'].'</span>'.
		form_textarea($config, $item['ds_observacao']), 'text-align:justify;'),
		'<a id="motivo_editar_'.$item['cd_atendimento'].'" href="javascript:void(0);" onclick="editar_motivo('.$item['cd_atendimento'].');" title="Editar Motivo">[motivo pendência]</a>'.
		'<a id="motivo_salvar_'.$item['cd_atendimento'].'" href="javascript:void(0);" style="display:none;" title="Salvar Motivo">[salvar]</a>',
		($item['dt_confirmacao'] == 0 ? '<a onclick="confirmar('.$item['cd_atendimento'].', '.intval($item['cd_atendimento_confirma_bco_ag_conta']).')" href="javascript:void(0);">[confirmar]</a>' : $item['usuario_confirmacao'].' - '.$item['dt_confirmacao'])
	);
}	

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>