<?php
	$body = array();

	$head = array();

	$ocultar = array();

	foreach($collection_head as $item)
	{
		$head[] = $item['ds_atividade_minhas_coluna'];

		if(trim($item['fl_info']) == 'N')
		{
			$ocultar[] = intval($item['nr_ordem']);
		}
	}

	foreach($collection_ocultar as $item)
	{
		$ocultar[] = intval($item['nr_ordem']);
	}

	foreach( $collection as $item )
	{
		$link           = anchor(site_url('atividade/atividade_solicitacao/index/'.$item["area"]."/".$item["numero"]), $item["numero"]);
		$link_descricao = anchor(site_url('atividade/atividade_solicitacao/index/'.$item["area"]."/".$item["numero"]), nl2br(strip_tags($item["descricao"])));

		$RE='';
		if((trim($item["cd_empresa"]) != '') AND (intval($item["cd_registro_empregado"]) > 0))
		{
			$RE = $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["cd_sequencia"];
		}

		$sep="";
		$tarefas="";
		if(isset($item['tarefas']))
		{
			foreach($item['tarefas'] as $tarefa)
			{
				$tarefas .= $sep.anchor('atividade/tarefa/cadastro/'.$item["numero"]."/".$tarefa["cd_tarefa"]."/".strtolower($tarefa['fl_tarefa_tipo']), '- '. $tarefa["cd_tarefa"], array("style" => "color:".$tarefa["status_cor"])) ;
				$sep = br();
			}
		}

		$body[] = array(
			 $link,
			 $item["dt_cad"],
			 $item["nomesolic"].'<br /><i>'.$item["nomeatend"].'</i>',
			 array( "<div style='width:500px;'>" . $link_descricao . "</div>",'text-align:justify'),
			 $item["ds_atividade_classificacao"],
			 '<span class="'.$item["status_label"].'">'.$item["status"].'</span>',
			 '<span class="label label-inverse">'.$item["nr_prioridade"].'</span>',
			 $item["div_solic"],

			 (intval($item['qt_anexo']) > 0 ? '<a href="'.(site_url('atividade/atividade_anexo/index/'.$item["numero"].'/'.$item['area'])).'" title="Ver anexos (Total: '.intval($item['qt_anexo']).')" style="white-space:nowrap;"><span style="display:none;">'.intval($item['qt_anexo']).'</span><img src="'.base_url()."/img/atividade_anexo.gif".'" border="0"></a>' : ""),
			 $item['qt_acomp'],
			 $tarefas,
			 $item["projeto_nome"],
			 $item["tipo"],
			 $item["data_limite"],
			 $item["data_limite_teste"],
			 $item["data_conclusao"],
			 $RE
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->col_oculta = (gerencia_in(array('GI')) ?  $ocultar : array(4, 10));

	echo $grid->render();
?>