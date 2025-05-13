<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_6, $label_4, ''
);

if(sizeof($tabela)<=0)
{
	echo "Não foi identificado período aberto para o Indicador";
}
else
{
	$ar_janela = array(
				  'width'      => '700',
				  'height'     => '500',
				  'scrollbars' => 'yes',
				  'status'     => 'yes',
				  'resizable'  => 'yes',
				  'screenx'    => '0',
				  'screeny'    => '0'
				);
	echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";

	$contador_ano_atual = 0;
	$contador           = sizeof($collection);
	$media_ano          = array();
	$a_data             = array(0, 0);
	
	foreach($collection as $item)
	{
		$a_data = explode( "/", $item['ano_mes_referencia'] );
	
		if($item['fl_media'] =='S')
		{
			$link = '';

			$referencia = " Média de " . $item['ano_referencia'];

			$nr_valor_1 = '';
			$nr_valor_2 = '';
			$nr_meta = '';
		}
		else
		{
			$link = anchor("indicador_plugin/atend_callcenter_sem_fila_espera/cadastro/" . $item["cd_atend_callcenter_sem_fila_espera"], "editar");

			$referencia = $item['ano_mes_referencia'];

			$nr_valor_1 = $item["nr_ligacao_sem_fila"];
			$nr_valor_2 = $item["nr_ligacao_atendida"];
			$nr_meta = $item["nr_meta"];
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media'] != 'S')
		{
			$contador_ano_atual ++;
			$media_ano[] = $item['nr_ligacao_atendida_percentual'];
		}

		$body[] = array(
			$contador--,
			$referencia,
			($nr_valor_1 != '') ? $nr_valor_1 : '',
			($nr_valor_2 != '') ? $nr_valor_2 : '',
			(trim($item['nr_ligacao_atendida_percentual'] != '') ? number_format($item['nr_ligacao_atendida_percentual'],2,',','.').'%' : ''),
			(trim($item['nr_meta'] != '') ? number_format($item['nr_meta'],2,',','.').'%' : ''),
            array($item["observacao"], 'text-align:"left"'),
			$link
		);
	}

	if( sizeof($media_ano)>0 )
	{
		$media = 0;
		
		foreach( $media_ano as $valor )
		{
			$media += $valor;
		}

		$media = number_format( ($media / sizeof($media_ano)), 2 );

		$body[] = array(
			0,
			'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
			'',
			'',
			'<big><b>'.number_format($media,2,',','.').'%'.'</b></big>',
			'<big><b>'.number_format($nr_meta,2,',','.').'%'.'</b></big>',
			'',
			''
		);
	}

	echo form_hidden('mes_input',$a_data[0]);
	echo form_hidden('contador_input',$contador_ano_atual);

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>
