<?php
	$head = array(
		'RE',
		'Nome',
		'Ativ.',
		'Data',
		'Solic/Atend',
		'Descrição',
		'Status',
		'Dt. Limite',
		'Dt. Limite Doc.',
		'Dt. Teste',
		'Dt. Conclusão'
	);

	$body = array();

	foreach($collection as $item)
	{
		$re = '';

		if(trim($item['cd_registro_empregado']) != '')
		{
			$re = $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['cd_sequencia'];
		}

		$dt_limite_doc = '';

		if(gerencia_in(array('GCM')) AND intval($item['cd_atendimento']) > 0)
		{
			$dt_limite_doc = $item['dt_limite_doc'];
		}

		$body[] = array(
			$re,
			array($item['nome_participante'], 'text-align:left'), 
			anchor(site_url('atividade/atividade_solicitacao/index/'.$item['area'].'/'.$item['numero']), $item["numero"]),
			$item['dt_cad'],
			$item['nomesolic'].'<br /><i>'.$item['nomeatend'].'</i>',
			array('<div style="width:500px;">'.anchor(site_url('atividade/atividade_solicitacao/index/'.$this->session->userdata('divisao').'/'.$item['numero']), $item['descricao']).'</div>','text-align:left'),
			'<span class="'.$item['status_label'].'">'.$item['status'].'</span>',
			$item['data_limite'],
			$dt_limite_doc,
			$item['data_limite_teste'],
			$item['data_conclusao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>