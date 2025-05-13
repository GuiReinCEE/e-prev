<?php
$body = array();
$head = array( 
	'Solicitante / Atendente',
	'Atividade',
	'Tarefa',
	'Prior.',
	'Nível',
	'Resumo',
	'',
	'Status',
	'Encamin',
	'Ini Prev',
	'Fim Prev',
	'Ini Real',
	'Fim Real',
	'Concluído'
);
foreach( $collection as $item )
{
	$body[] = array(
		$item['guerra_usuario_solicitante'].br().$item['guerra_usuario_atendente'],
		$item['cd_atividade'],
		"<a href='".site_url('atividade/tarefa_execucao/index/'.$item['cd_atividade'].'/'.$item['cd_tarefa'])."'>".$item['cd_tarefa']."</a>",
		($item['prioridade']== 'S' ? "<span style='color:red'><b>Sim</b></span>" : "Não"),
		$item['nr_nivel_prioridade'],
		array( "<div style='width:300px;'><a href='".site_url('atividade/tarefa_execucao/index/'.$item['cd_atividade'].'/'.$item['cd_tarefa'])."'>".$item['resumo']."</a></div>", 'text-align:left;' ),
		(intval($item['tl_anexos']) > 0 ? 
				'<a href="'.(site_url('atividade/tarefa_anexo/index/'.$item["cd_atividade"].'/'.$item['cd_tarefa'])).'" title="Ver anexos (Total: '.intval($item['tl_anexos']).')" style="white-space:nowrap;"><span style="display:none;">'.intval($item['tl_anexos']).'</span><img src="'.base_url()."/img/atividade_anexo.gif".'" border="0"></a>' 
				: ""),
		'<span class="'.$item["status_label"].'">'.$item["status_descricao"].'</span>',
		$item['dt_encaminhamento'],
		$item['dt_inicio_prev'],
		$item['dt_fim_prev'],
		$item['dt_inicio_prog'],
		$item['dt_fim_prog'],
		$item['dt_ok_anal']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();