<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_7, ''
);

$tabela = indicador_tabela_aberta( intval( enum_indicador::RH_HORA_HOMEM_TREINAMENTO ) );

if(sizeof($tabela)<=0)
{
	echo "Não foi identificado período aberto para o Indicador";
}
else
{
    echo "<BR>";
    $ar_janela = array(
                  'width'      => '700',
                  'height'     => '500',
                  'scrollbars' => 'yes',
                  'status'     => 'yes',
                  'resizable'  => 'yes',
                  'screenx'    => '0',
                  'screeny'    => '0'
                );
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);

	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$media_ano=array();
	$a_data=array(0, 0);
	$nr_acumulado_anterior = 0;
	$nr_total_hora_total = 0;	
	$nr_efetivo_total = 0;
	$nr_meta_total = 0;
	$nr_referencial_total = 0;
	
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];
		$nr_referencial = $item["nr_referencial"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Acumulado de " . $item['ano_referencia'];

			$nr_total_hora= '';
			$nr_efetivo= '';
            $observacao = '';
			$nr_acumulado_f = $item['nr_acumulado_f']; // valor da média dos anos anteriores é gravada nessa coluna quando o período é fechado
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_hhtr/detalhe/" . $item["cd_administrativo_hhtr"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_total_hora = $item["nr_total_hora"];
			$nr_efetivo = $item["nr_efetivo"];
            $observacao = $item["observacao"];

            if($nr_total_hora != 0)
            {
                $nr_acumulado_f = (floatval($nr_total_hora)/floatval($nr_efetivo)) + floatval($nr_acumulado_anterior);
            }
            else
            {
              $nr_acumulado_f = floatval($nr_acumulado_anterior);
            }
			
			$nr_acumulado_anterior = $nr_acumulado_f;
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$nr_total_hora_total += $nr_total_hora;
			$nr_efetivo_total += $nr_efetivo;
			$nr_meta_total = $nr_meta;
			$nr_referencial_total = $nr_referencial;
			$contador_ano_atual++;
			$media_ano[] = $nr_acumulado_f;
		}

		$body[] = array(
			$contador--,
			$referencia,
			($nr_total_hora!='')?number_format($nr_total_hora,2,',','.'):'',
			($nr_efetivo!='')?number_format($nr_efetivo,0):'',
			($nr_acumulado_f!='')?number_format($nr_acumulado_f, 2, ',', '.'):'',
			($nr_meta!='')?number_format($nr_meta,2,',','.'):'',
			($nr_referencial!='')?number_format($nr_referencial,2,',','.'):'',
            array(nl2br($observacao), 'text-align:"justify"'),
			$link
		);
	}
		
	if($contador_ano_atual > 0)
	{
		$body[] = array(
			0,
			'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
			'<b>'.number_format($nr_total_hora_total,2,',','.').'</b>',
			'<b>'.number_format($nr_efetivo_total,0).'</b>',
			'<b>'.number_format($nr_acumulado_f,2,',','.').'</b>',
			'<b>'.number_format($nr_meta_total,2,',','.').'</b>',
			'<b>'.number_format($nr_referencial_total,2,',','.').'</b>',
            '',
			'' 
		);
	}
	
	echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
	echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>
