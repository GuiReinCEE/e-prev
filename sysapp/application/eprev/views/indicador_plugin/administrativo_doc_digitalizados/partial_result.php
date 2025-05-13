<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, ''
);

if(sizeof($tabela)<=0)
{
	echo "N�o foi identificado per�odo aberto para o Indicador";
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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresenta��o', $ar_janela);

	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$total=0;
	$a_data=array(0, 0);
    $media_ano=array();
	foreach( $collection as $item )
	{
        $observacao = $item["observacao"];
		$a_data = explode( "/", $item['mes_referencia'] );
		

        if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = $item['ano_referencia'];

            $nr_valor_1 = $item["nr_valor_1"];
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_doc_digitalizados/detalhe/" . $item["cd_administrativo_doc_digitalizados"], "editar");
            $referencia = $item['mes_referencia'];

            $nr_valor_1 = $item["nr_valor_1"];
            $total+=floatval($nr_valor_1);
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?$nr_valor_1:''
            , array($observacao, 'text-align:"left"')
			, $link 
		);

        if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_valor_1;
		}
	}

    if( sizeof($media_ano)>0 )
	{
        $body[] = array(
            0
            , '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
            , '<big><b>'.$total.'</b></big>'
            , '', ''
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
