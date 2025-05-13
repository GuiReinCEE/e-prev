<?php

######################################################
# CASO ALTERE AS COLUNAS ALTERAR NO CRONOGRAMA E NO  #
# CRONOGRAMA PARTIAL O NUMERO DA COLUNA OCULTA       #   
######################################################

$body=array();
$head = array( 
	'Atividade',
	'Grupo',
	'Dt. Atividade',
	'Solic/Atend',
	'Gerência',
	'Descrição',
	'Prior. Área',
	'Prior. Consenso',
	'',
	'',
	'Prior.',
	'Status',
	'Complexidade',
	'Projeto',
	''
);

foreach( $collection as $item )
{
	$fl_gerente = false;
	$fl_excluir = false;
	$fl = false;
	$fl_n = 0;
	
	if(trim($dt_encerra) == '' AND trim($item['fl_edit']) == 'S' AND $fl_responsavel)
	{
		$fl = true;
		$fl_n = 1;
	}

	if($fl OR ($this->session->userdata('divisao') == $item['divisao'] AND ($this->session->userdata('tipo') == 'G' OR $this->session->userdata("indic_01") == "S")))
	{
		$fl_gerente = true;
	}
	
	if($fl_responsavel AND trim($dt_encerra) == '')
	{
		$fl_excluir = true;
	}

	$editar_gerente = '<a href="javascript: void(0)" id="gerente_editar_'.$item['cd_atividade_cronograma_item'].'" onclick="editar_gerente('.$item['cd_atividade_cronograma_item'].');" title="Editar">[editar]</a>';
	$salvar_gerente = '<a href="javascript: void(0)" id="gerente_salvar_'.$item['cd_atividade_cronograma_item'].'" onclick="salvar_operacinal_gerente('.$item['cd_atividade_cronograma_item'].', $(this));" title="Salvar" style="display:none">[salvar]</a>';
	
	$input_operacional = array(
					"name"=>"nr_prioridade_operacional_".$item['cd_atividade_cronograma_item'], 
					"id"=>"nr_prioridade_operacional_".$item['cd_atividade_cronograma_item'],
					"style"=>"display:none; width:50px;"
					);
	
	$input_gerente = array(
					"name"=>"nr_gerente_".$item['cd_atividade_cronograma_item'], 
					"id"=>"nr_gerente_".$item['cd_atividade_cronograma_item'],
					"style"=>"display:none; width:50px;"
					);
	
	$editar_projeto = '<a href="javascript: void(0)" id="projeto_editar_'.$item['cd_atividade'].'" onclick="editar_projeto('.$item['cd_atividade'].');" title="Editar projeto">[editar]</a>';
	$salvar_projeto = '<a href="javascript: void(0)" id="projeto_salvar_'.$item['cd_atividade'].'" onclick="salvar_projeto('.$item['cd_atividade'].');" title="Salvar projeto" style="display:none">[salvar]</a>';
	
	$editar_complexidade = '<a href="javascript: void(0)" id="complexidade_editar_'.$item['cd_atividade'].'" onclick="editar_complexidade('.$item['cd_atividade'].');" title="Editar complexidade">[editar]</a>';
	$salvar_complexidade = '<a href="javascript: void(0)" id="complexidade_salvar_'.$item['cd_atividade'].'" onclick="salvar_complexidade('.$item['cd_atividade'].');" title="Salvar complexidade" style="display:none">[salvar]</a>';
	
	$editar_grupo = '<a href="javascript: void(0)" id="grupo_editar_'.$item['cd_atividade_cronograma_item'].'" onclick="editar_grupo('.$item['cd_atividade_cronograma_item'].', this, '.intval($item['cd_atividade_cronograma_grupo']).','.$fl_n.');" title="Editar grupo">[editar]</a>';
	
	$excluir = '<a href="javascript: void(0)"  onclick="excluir_item('.$item['cd_atividade_cronograma_item'].')" title="Excluir Atividade">[excluir]</a>';
		
	$body[] = array(
		 $item["cd_atividade"],
		array($item['ds_atividade_cronograma_grupo'].' '.($fl ? $editar_grupo : ''),"text-align:left"),
		
		$item["dt_atividade"],
		$item["solicitante"]."<BR><i>".$item["atendente"]."</i>",
		$item["divisao"],
		
		array("<div style='width:500px;'>" . $item["descricao"]. "</div>",'text-align:justify'),	
		//Operacional		
		$item['nr_prioridade_operacional'],		
		$item['nr_prioridade_gerente'],
		
		array('<span id="ajax_prioridade_'.$item['cd_atividade_cronograma_item'].'"></span> '.'<span id="prioridade_valor_'.$item['cd_atividade_cronograma_item'].'">'.$item['nr_prioridade_operacional'].'</span>'.
		form_input($input_operacional,$item['nr_prioridade_operacional'])."<script> jQuery(function($){ $('#nr_prioridade_operacional_".$item['cd_atividade_cronograma_item']."').numeric(); }); </script>", 'display:none; text-align:center;'),
		//Gerente
		array('<span id="ajax_gerente_'.$item['cd_atividade_cronograma_item'].'"></span> '.'<span id="gerente_valor_'.$item['cd_atividade_cronograma_item'].'">'.$item['nr_prioridade_gerente'].'</span>'.
		form_input($input_gerente,$item['nr_prioridade_gerente'])."<script> jQuery(function($){ $('#gerente_".$item['cd_atividade_cronograma_item']."').numeric(); }); </script>", 'display:none; text-align:center;'),
		($fl_gerente ? $editar_gerente : '').$salvar_gerente,
		array($item["status_atividade"], 'font-weight:bold; color:'.$item["status_cor"].';'),
		//Complexidade
		array('<span id="ajax_complexidade_'.$item['cd_atividade'].'"></span> '.'<span id="complexidade_valor_'.$item['cd_atividade'].'">'.$item['ds_complexidade'].'</span>'.
		form_dropdown("ds_complexidade_".$item['cd_atividade'], $arr_complexidades, array($item['cd_complexidade']), 'style="display:none;"').
		($fl ? $editar_complexidade : '').$salvar_complexidade,"text-align:left"),
		//Projeto
		array('<span id="ajax_projeto_'.$item['cd_atividade'].'"></span> '.'<span id="projeto_valor_'.$item['cd_atividade'].'">'.$item['projeto_nome'].'</span>'.
		form_dropdown("projeto_nome_".$item['cd_atividade'], $arr_projetos, array($item['cd_projeto']), 'style="display:none;"').
		($fl ? $editar_projeto : '').$salvar_projeto,"text-align:left"),
		($fl_excluir ? $excluir : '')
	);
	
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_oculta = array('8','9');
echo $grid->render();
?>