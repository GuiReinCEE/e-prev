<?php
$qt_tarefa_aberta = 0;
$qt_tarefa_exec   = 0;
if(count($collection) > 0)
{
	$body = array();
	$head = array('Cód', 'Descrição', 'Status', 'Dt Início', 'Dt Liberação', 'Dt Conclusão', 'Responsável');

	foreach($collection as $item)
	{
		$body[] = array(
					anchor("atividade/tarefa/cadastro/".$item["cd_atividade"]."/".$item["cd_tarefa"]."/".strtolower($item['fl_tarefa_tipo']), $item["cd_tarefa"]),
					array(anchor("atividade/tarefa/cadastro/".$item["cd_atividade"]."/".$item["cd_tarefa"]."/".strtolower($item['fl_tarefa_tipo']), $item['resumo']),"text-align:left;"), 
					array($item['status'],"text-align:left;"),
					$item['dt_inicio_prog'],
					$item['dt_fim_prog'],
					$item['dt_ok_anal'],
					array($item['ds_responsavel'],"text-align:left;")
				);
				
		if(($item['st_tarefa'] != 'CONC') and ($item['st_historico'] != 'CONC'))
		{
			$qt_tarefa_aberta++;
		}

		if(($item['st_tarefa'] == 'EMAN') and ($item['st_historico'] == 'EMAN'))
		{
			$qt_tarefa_exec++;
		}				
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head       = $head;
	$grid->body       = $body;
	$grid->view_count = false;

	echo $grid->render();
}
echo '
		<script>
			$("#qt_tarefa_aberta").val('.intval($qt_tarefa_aberta).');
			$("#qt_tarefa_exec").val('.intval($qt_tarefa_exec).');
		</script>
     ';
?>