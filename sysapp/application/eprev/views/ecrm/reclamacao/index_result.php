<?php
	$head = array( 
		'Número',
	    'Tipo',
		'RE',
		'Nome',
		'Descrição',
		'Classificação.',
		'Retorno Classif.',
		'Cadastro',
		'Dt. Encaminhado',
		'Ger.',
		'Resp.',
		'Dt. Prazo Ação',
		'Dt. Prorrogação Ação',
		'Dt. Prazo Classificação',
		'Dt. Prorrogação Classificação',
		'Dt. Encerrado',
		'Dt. Retorno',
		'Dt. Cancelado'
	);

	$body = array();

	foreach($collection as $item)
	{
		$classificacao = '';
		$retorno = '';

		if((trim($item['dt_classificacao']) != '') AND (trim($item['tipo']) == 'R'))
		{
			$classificacao = '<span class="label '.trim($item['cor']).'">'.$item['dt_classificacao'].br().$item['ds_reclamacao_retorno_classificacao'].'</span>';

			if(trim($item['ds_justificativa']) != '')
			{
				$retorno = array(nl2br($item['ds_justificativa']), 'text-align:justify;');
			}
			else if(trim($item['nr_nc']) != '')
			{
				$retorno = 'NC: '. anchor('gestao/nc/cadastro/'.$item['nr_ano_nc'].$item['nr_nc'], $item['ds_nc'], 'target="_blank"');
			}
		}

		$body[] = array(
			anchor('ecrm/reclamacao/cadastro/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], $item['cd_reclamacao']),
	        '<span class="label label-'.trim($item['ds_class_tipo']).'">'.trim($item['ds_tipo']).'</span>',
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;'),
			array(anchor('ecrm/reclamacao/cadastro/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], nl2br($item['descricao'])), 'text-align:justify;'),
			$classificacao,
			$retorno,
			array($item['dt_inclusao'].br().$item['ds_usuario_reclamacao'], 'text-align:left;'),
			$item['dt_encaminhado'],
			$item['cd_divisao'],
			array($item['ds_usuario_responsavel'],"text-align:left;"),
			'<span class="'.((((trim($item['dt_encerramento']) == '') AND (trim($item['dt_cancela']) == '') AND (trim($item['dt_prorrogacao_acao']) == ''))) ? 'label label-important' : '').'">'.$item['dt_prazo_acao'].'</span>',		
			'<span class="'.((((trim($item['dt_encerramento']) == '') AND (trim($item['dt_cancela']) == ''))) ? 'label label-important' : "").'">'.$item['dt_prorrogacao_acao'].'</span>',
			'<span class="'.((((trim($item['dt_encerramento']) == '') AND (trim($item['dt_cancela']) == '') AND (trim($item['dt_prorrogacao_classificacao']) == ''))) ? 'label label-important' : '').'">'.$item['dt_prazo_classificacao'].'</span>',		
			'<span class="'.((((trim($item['dt_encerramento']) == '') AND (trim($item['dt_cancela']) == ''))) ? 'label label-important' : "").'">'.$item['dt_prorrogacao_classificacao'].'</span>',
			$item['dt_encerramento'],
			$item['dt_retorno'],
			$item['dt_cancela']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>