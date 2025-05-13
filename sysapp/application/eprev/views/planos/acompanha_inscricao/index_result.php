<?php
	$head = array( 
		'RE',
		'Participante',
		'Forma PG',
		'Dt Solicitação',
		'Dt Envio GP',
		'Dt Receb',
		'Calendário CAD',
		'Dt Inclusão',
		'Dt Confirma',
		'Dt Cobrança',
		'Dt Envio',
		'Dt Dig Ingresso',
		'Dt Ingresso',
		'Vl Primero Pagamento.',
		'Dt Desliga',
		'Dt Cancela',
		'Qt Dia Cadastro',
		'Qt Dia Cobrança',
		'Qt Dia Envio',
		'Qt Dia Ingresso'
	);

	$soma_dia_cadastro  = 0;
	$count_dia_cadastro = 0;
	$maior_dia_cadastro = 0;
	$menor_dia_cadastro = 0;
	$media_dia_cadastro = 0;

	$soma_dia_cobranca  = 0;
	$count_dia_cobranca = 0;
	$maior_dia_cobranca = 0;
	$menor_dia_cobranca = 0;
	$media_dia_cobranca = 0;

	$soma_dia_envio  = 0;
	$count_dia_envio = 0;
	$maior_dia_envio = 0;
	$menor_dia_envio = 0;
	$media_dia_envio = 0;

	$soma_dia_ingresso = 0;
	$count_dia_ingresso = 0;
	$maior_dia_ingresso = 0;
	$menor_dia_ingresso = 0;
	$media_dia_ingresso = 0;

	$body = array();

	foreach($collection as $item)
	{
		if(trim($item['qt_dia_cadastro']) != '')
		{
			$soma_dia_cadastro += intval($item['qt_dia_cadastro']);
			$count_dia_cadastro ++;
			
			if((intval($maior_dia_cadastro) == 0) OR (intval($item['qt_dia_cadastro']) > intval($maior_dia_cadastro)))
			{
				$maior_dia_cadastro = intval($item['qt_dia_cadastro']);
			}
			
			if((intval($menor_dia_cadastro) == 0) OR (intval($item['qt_dia_cadastro']) < intval($menor_dia_cadastro)))
			{
				$menor_dia_cadastro = intval($item['qt_dia_cadastro']);
			}
		}
		
		if(trim($item['qt_dia_geracao']) != '')
		{
			$soma_dia_cobranca += intval($item['qt_dia_geracao']);
			$count_dia_cobranca ++;
			
			if((intval($maior_dia_cobranca) == 0) OR (intval($item['qt_dia_geracao']) > intval($maior_dia_cobranca)))
			{
				$maior_dia_cobranca = intval($item['qt_dia_geracao']);
			}
			
			if((intval($menor_dia_cobranca) == 0) OR (intval($item['qt_dia_geracao']) < intval($menor_dia_cobranca)))
			{
				$menor_dia_cobranca = intval($item['qt_dia_geracao']);
			}
		}
		
		if(trim($item['qt_dia_envio']) != '')
		{
			$soma_dia_envio += intval($item['qt_dia_envio']);
			$count_dia_envio ++;
			
			if((intval($maior_dia_envio) == 0) OR (intval($item['qt_dia_envio']) > intval($maior_dia_envio)))
			{
				$maior_dia_envio = intval($item['qt_dia_envio']);
			}
			
			if((intval($menor_dia_envio) == 0) OR (intval($item['qt_dia_envio']) < intval($menor_dia_envio)))
			{
				$menor_dia_envio = intval($item['qt_dia_envio']);
			}
		}
		
		if(trim($item['qt_dia_ingresso']) != '')
		{
			$soma_dia_ingresso += intval($item['qt_dia_ingresso']);
			$count_dia_ingresso ++;
			
			if((intval($maior_dia_ingresso) == 0) OR (intval($item['qt_dia_ingresso']) > intval($maior_dia_ingresso)))
			{
				$maior_dia_ingresso = intval($item['qt_dia_ingresso']);
			}
			
			if((intval($menor_dia_ingresso) == 0) OR (intval($item['qt_dia_ingresso']) < intval($menor_dia_ingresso)))
			{
				$menor_dia_ingresso = intval($item['qt_dia_ingresso']);
			}
		}

		$body[] = array(
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;'),
			$item['forma_pagamento'],
			$item['dt_solicitacao'],
			$item['dt_envio_cadastro'],
			$item['dt_recebimento'],
			$item['dt_inicio_calendario'].br().$item['dt_fim_calendario'],
			$item['dt_inclusao'],
			$item['dt_confirma'],
			$item['dt_cobranca'],
			$item['dt_envio'],
			$item['dt_dig_ingresso'],
			$item['dt_ingresso'],
			number_format($item['vl_contrib'], 2, ',', '.'),
			$item['dt_desliga'],
			$item['dt_cancela'],
			'<span class="label label-success">'.$item['qt_dia_cadastro'].'</span>',
			'<span class="label label-warning">'.$item['qt_dia_geracao'].'</span>',
			'<span class="label label-info">'.$item['qt_dia_envio'].'</span>',
			'<span class="label label-inverse">'.$item['qt_dia_ingresso'].'</span>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();

	if(intval($count_dia_cadastro) > 0)
	{
		$media_dia_cadastro = ($soma_dia_cadastro / $count_dia_cadastro);
	}

	if(intval($count_dia_cobranca) > 0)
	{
		$media_dia_cobranca = ($soma_dia_cobranca / $count_dia_cobranca);
	}

	if(intval($count_dia_envio) > 0)
	{
		$media_dia_envio = ($soma_dia_envio / $count_dia_envio);
	}

	if(intval($count_dia_ingresso) > 0)
	{
		$media_dia_ingresso = ($soma_dia_ingresso / $count_dia_ingresso);
	}

	$body = array();
	$head = array( 
		'Resumo',
		'Qt Dia Cadastro',
		'Qt Dia Cobrança',
		'Qt Dia Envio',
		'Qt Dia Ingresso'
	);

	$body[] = array(
		'Média',
		round($media_dia_cadastro),
		round($media_dia_cobranca),
		round($media_dia_envio),
		round($media_dia_ingresso)
	);

	$body[] = array(
		'Maior',
		$maior_dia_cadastro,
		$maior_dia_cobranca,
		$maior_dia_envio,
		$maior_dia_ingresso
	);


	$body[] = array(
		'Menor',
		$menor_dia_cadastro,
		$menor_dia_cobranca,
		$menor_dia_envio,
		$menor_dia_ingresso
	);

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->id_tabela = "table-2";
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();


?>