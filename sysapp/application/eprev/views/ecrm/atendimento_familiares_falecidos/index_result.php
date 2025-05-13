<?php
	$head = array(
	    'Código',
	    'Dt. Registro',
	    'RE',
	    'Nome',
	    'Contato',
	    'Observação',
	    'Usuário',
	    'Atendimento',
	    'Acompanhamento',
	    'Encerrado'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$encerrar = '<a href="javascript:void(0)" onclick="encerrar('.$item['cd_atendimento_familiares_falecidos'].')">[Encerrar]</a>';

		$body[] = array(
			anchor('ecrm/atendimento_familiares_falecidos/cadastro/'.$item['cd_atendimento_familiares_falecidos'], $item['cd_atendimento_familiares_falecidos']),
			$item['dt_inclusao'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array(anchor('ecrm/atendimento_familiares_falecidos/cadastro/'.$item['cd_atendimento_familiares_falecidos'], $item['nome']), 'text-align:left;'),
			array($item['contato'], 'text-align:left;'),
			array(nl2br($item['observacao']), 'text-align:justify;'),
			array($item['usuario_inclusao'], 'text-align:left;'),
			$item['cd_atendimento'],
			array(nl2br($item['acompanhamento']), 'text-align:justify;'),
			(trim($item['dt_encerramento']) != '' ? $item['dt_encerramento'] : $encerrar)
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>