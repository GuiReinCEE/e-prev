<?php
$body=array();
$head = array( 
	'Ordem',
    'Nome',
    'Peso (%)',
    'Concluído (%)',
	'',
	'Observação',
	''
);

if(((intval($row['cd_produto_financeiro']) > 0) AND (($this->session->userdata('codigo') == $row['cd_usuario_inclusao']) OR ($this->session->userdata('codigo') == $row['cd_usuario_responsavel']) 
	OR ($this->session->userdata('codigo') == $row['cd_usuario_revisor'])  OR (($this->session->userdata('divisao') == 'GIN') AND ($this->session->userdata('tipo') == 'G')))) OR (intval($row['cd_produto_financeiro']) == 0))
{
	$bool = true;
}
else
{
	$bool = false;
}

$peso_total = 0;
$conc_total = 0;

foreach( $collection as $item )
{
	$cfg_obs = array( 
				"name"=>'produto_financeiro_etapa_status['.$item['cd_produto_financeiro_etapa_status'].'][observacao]', 
				"id"=>'produto_financeiro_etapa_status['.$item['cd_produto_financeiro_etapa_status'].'][observacao]',
				"style"=>"width:350px; height:70px;"
				);
	
	$cfg_ordem = array(
				"name"=>'produto_financeiro_etapa_status['.$item['cd_produto_financeiro_etapa_status'].'][nr_ordem]',
				"id"=>"nr_ordem_".$item['cd_produto_financeiro_etapa_status'],
				"onkeydown"=>"$(this).numeric();",
				"style"=>"text-align:center; width:50px;"
				);
				
	$cfg_peso = array(
				"name"=>'produto_financeiro_etapa_status['.$item['cd_produto_financeiro_etapa_status'].'][nr_peso]',
				"id"=>"nr_peso_".$item['cd_produto_financeiro_etapa_status'],
				"onblur"=>"calculaPercentual('".$item['cd_produto_financeiro_etapa_status']."');",
				"onkeydown"=>"$(this).numeric();",
				"style"=>"text-align:center; width:50px;",
				"class"=>"produto_financeiro_etapa_status_peso"
				);				
	
	$cfg_conc = array(
				"name"=>'produto_financeiro_etapa_status['.$item['cd_produto_financeiro_etapa_status'].'][nr_concluido]',
				"id"=>"nr_concluido_".$item['cd_produto_financeiro_etapa_status'],
				"onblur"=>"calculaPercentual('".$item['cd_produto_financeiro_etapa_status']."');",
				"onkeydown"=>"$(this).numeric();",
				"style"=>"text-align:center; width:50px;",
				"class"=>"produto_financeiro_etapa_status_concluido"
				);	
				
	$peso_total += intval($item['nr_peso']);
	$conc_total += ((intval($item['nr_concluido']) * intval($item['nr_peso'])) / 100) ;
	#((pfes.nr_concluido * pfes.nr_peso) / 100)
	
    $body[] = array(
		form_input($cfg_ordem,$item['nr_ordem']),
		array($item['ds_produto_financeiro_etapa'],"text-align:left;"),
		form_input($cfg_peso,$item['nr_peso']),
		form_input($cfg_conc,$item['nr_concluido']).'<span id="ajax_concluido_'.$item['cd_produto_financeiro_etapa_status'].'"></span>',
		array(progressbar(intval($item['nr_concluido']), "pb_".$item['cd_produto_financeiro_etapa_status']),"text-align:left;"),
		form_textarea($cfg_obs,$item['observacao']),
		($bool ? '<a href="javascript:void(0);" onclick="excluir('.$item['cd_produto_financeiro_etapa_status'].')">[excluir]</a>' : '').
		form_hidden('produto_financeiro_etapa_status[' . $item['cd_produto_financeiro_etapa_status'].'][cd_produto_financeiro_etapa_status]', intval($item['cd_produto_financeiro_etapa_status']))		
	);
}

	#### TOTAL DO PRODUTO ####
    $body[] = array(
		'',
		'<b>Total</b>',
		'<input type="text" id="peso_total" style="text-align:center; width:50px; border:0px; font-weight: bold;" value="'.round($peso_total).'">',
		'<input type="text" id="conc_total" style="text-align:center; width:50px; border:0px; font-weight: bold;" value="'.round($conc_total).'">',
		array(progressbar(round($conc_total), "pb_conc_total"),"text-align:left;"),
		'',
		''
	);


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;


if(count($collection) > 0)
{
    echo form_open('atividade/produto_financeiro/salvar_etapas_status');
		echo form_hidden('cd_produto_financeiro', intval($item['cd_produto_financeiro']));
		echo $grid->render();
		echo form_command_bar_detail_start();
			 echo ($bool ?  button_save("Salvar", "salva_etapas()"): '');
        echo form_command_bar_detail_end();
    echo form_close();
}


?>