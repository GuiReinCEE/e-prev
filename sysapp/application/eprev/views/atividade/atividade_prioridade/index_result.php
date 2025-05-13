<?php
	$head = array(
		'Ativ.',
		'Data',
		'Descrição',
		'Status',
		'Solicitante',
		'Prior. Atual',
		'Define Prior.',
		'Nova Prior.',
		'Dt Prioridade',
		'Def Prioridade'
	);

	$body = array();

	$nr_conta = 0;

	foreach($collection as $item)
	{
		$descricao = '
			<a href="javascript: void(0)" onclick="$(\'#obPrioridadeDescricao'.$nr_conta.'\').toggle();">[ver/ocultar]</a>
			<br/>
			<div id="obPrioridadeDescricao'.$nr_conta.'" style="text-align:justify; display:none;"><br/>'.nl2br($item['descricao']).'</div>';


		$body[] = array(
			anchor(site_url('atividade/atividade_solicitacao/index/'.$item['area_atendente'].'/'.$item['numero']), $item['numero']) , 
			$item['dt_cadastro'], 
			array($descricao, 'text-align:left;'),

			'<span class="'.$item["status_label"].'">'.$item['ds_status'].'</span>',

			array($item['ds_solicitante'], 'text-align:left;'),

			'<span id="obPrioridade'.$nr_conta.'" class="label">'.$item['nr_prioridade'].'</span>', 
			
			((trim($item['cd_status']) == "ETES" OR trim($item['cd_status']) == "EMAN") ? '<span class="label">'.$item['nr_prioridade'].'</span>' : "").
			'
			<input type="hidden" id="status_atividade_'.$item['numero'].'" name="status[]" value="'.$item['cd_status'].'">

			<input type="'.(($item['cd_status'] == "ETES" OR $item['cd_status'] == "EMAN") ? "hidden" : "text").'" id="obPrioridadeValor'.$nr_conta.'" name="ar_prioridade[]" value="'.$item['nr_prioridade'].'" style="width: 50px; text-align: center;"  data-numero="'.$item['numero'].'">
				<script>
					jQuery(function($){
						$("#obPrioridadeValor'.$nr_conta.'").numeric();
						$("#obPrioridadeValor'.$nr_conta.'").change(function() {
						
							if($("#obPrioridadeValor'.$nr_conta.'").val() == "")
							{
								$("#obPrioridadeValor'.$nr_conta.'").val($("#obPrioridadeValorAnterior'.$nr_conta.'").val());
								$("#obPrioridadeNova'.$nr_conta.'").html($("#obPrioridadeValorAnterior'.$nr_conta.'").val());
							}
							else
							{
								$("#obPrioridadeNova'.$nr_conta.'").removeClass("label-success");
								$("#obPrioridadeNova'.$nr_conta.'").addClass("label-important");
								$("#obPrioridadeNova'.$nr_conta.'").html($("#obPrioridadeValor'.$nr_conta.'").val());
							}
							
							if($("#obPrioridadeValor'.$nr_conta.'").val() != $("#obPrioridadeValorAnterior'.$nr_conta.'").val())
							{
								$("#obPrioridadeNova'.$nr_conta.'").removeClass("label-success");
								$("#obPrioridadeNova'.$nr_conta.'").addClass("label-important");						
							}
							else
							{
								$("#obPrioridadeNova'.$nr_conta.'").removeClass("label-important");
								$("#obPrioridadeNova'.$nr_conta.'").addClass("label-success");						
							}
						});
					});
				</script>			
				<input type="hidden" id="obPrioridadeValorAnterior'.$nr_conta.'" name="ar_prioridade_anterior[]" value="'.$item['nr_prioridade'].'">
				<input type="hidden" name="ar_atividade[]" value="'.$item['numero'].'">',
			'<span id="obPrioridadeNova'.$nr_conta.'" class="label label-success">'.$item['nr_prioridade'].'</span>',
			$item['dt_prioridade'], 
			array($item['ds_prioridade_usuario'], 'text-align:left;')
		);

		$nr_conta++;
	}

	$this->load->helper('grid');
	$grid             = new grid();
	$grid->id_tabela  = 'tabela_atividades';
	$grid->col_oculta = (trim($fl_atividade_prior_editar) == 'S' ? Array() : Array(6,7));
	$grid->head       = $head;
	$grid->body       = $body;


	echo form_open('atividade/atividade_prioridade/salvar', 'id="formSetPrioridadeAtividades" method="post"');
		echo form_default_hidden('cd_atendente', '','');
		echo form_default_hidden('cd_area_solicitante', '','');

		if (trim($fl_atividade_prior_editar) == 'S')
		{
			echo '
				<input type="button" value="Salvar Prioridade(s)" class="btn btn-danger" onclick="set_prioridade();">
				<input type="button" value="Reordenar" class="btn btn-success" onclick="reordenar();">';
			echo br(2);
		}

		echo $grid->render();
	echo form_close();	
?>