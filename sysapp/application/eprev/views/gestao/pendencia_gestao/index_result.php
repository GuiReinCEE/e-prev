<?php
	$head = array(
		'Cod.',
		'Item',
		'Reunião',
		'Dt. Reunião',
		'Prazo',
		'Responsável',
		'Superior',
		'Situação',
		'Acompanhamento',
		'Plano de Ação',
		'Anexo(s)'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/pendencia_gestao/cadastro/'.$item['cd_pendencia_gestao'], $item['cd_pendencia_gestao']),
			array(anchor('gestao/pendencia_gestao/cadastro/'.$item['cd_pendencia_gestao'], nl2br($item['ds_item'])), 'text-align:justify;'),
			$item['ds_reuniao_sistema_gestao_tipo'],
			$item['dt_reuniao'],
			$item['dt_prazo'],
			implode(', ', $item['responsavel']),
			$item['cd_superior'],
			'<span class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</span>',
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
			array(anchor(base_url().'up/pendencia_gestao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')) ,'text-align:left'),
			(intval($item['qt_anexo']) > 0 ? '<a href="'.(site_url('gestao/pendencia_gestao/anexo/'.$item['cd_pendencia_gestao'])).'" title="Ver anexos (Total: '.intval($item['qt_anexo']).')" style="white-space:nowrap;"><span style="display:none;">'.intval($item['qt_anexo']).'</span><img src="'.base_url()."/img/atividade_anexo.gif".'" border="0"></a>' : '')
	    );
	}


	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>