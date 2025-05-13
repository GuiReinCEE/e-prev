<?php
	$head = array( 
		'Usuário Solicitação',
		'Nome do Evento',
		'Promotor',
		'Cidade',
		'UF', 
		'Dt. Início',
		'Dt. Final',
		'Tipo', 
		'Carga Horária(Horas)',
		'Certificado',
		'Dt. Inclusão',
		'Status',
		'Dt. Validação RH',
		'Usuário validação RH',
		'Observação'
	);

	$body = array();

	foreach($collection as $item)
	{
		if(trim($item['dt_validacao']) == '')
		{
			$link = anchor('servico/solicitacao_treinamento/cadastro/'.$item['cd_solicitacao_treinamento'], $item['ds_evento']);
		}
		else
		{
			$link = $item['ds_evento'];
		}

	    $body[] = array(
	    	$item['ds_usuario_inclusao'],
			array($link, 'text-align:left'),
	        array($item['ds_promotor'], 'text-align:left'),
	        array($item['ds_cidade'], 'text-align:left'),
	        $item['ds_uf'],
	        $item['dt_inicio'],
	        $item['dt_final'],
	        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
	        str_replace('.', ',', $item['nr_carga_horaria']),
	        array(anchor(base_url().'up/certificado_treinamento/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
	        $item['dt_inclusao'],
	        '<span class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</span>',
	        $item['dt_validacao'],
	        $item['ds_usuario_validacao'],
	        array(nl2br($item['ds_descricao']), 'text-align : justify')
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	if(!$usuario_rh)
	{
		$grid->col_oculta = array(0);
	}

	echo $grid->render();
?>