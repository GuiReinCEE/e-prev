<?php
$body=array();
$head = array( 
	'#', $label_0, "", $label_1, $label_4, $label_6, ''
);

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
	$a_data=array(0, 0);

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta         = $item["nr_meta"];
        $nr_valor_1      = $item["nr_valor_1"];
        $referencia      = $item['mes_referencia'];
        $nr_faixa        = $item['nr_faixa'];

		$link = anchor("indicador_poder/participantes_inst/detalhe/" . $item["cd_participantes_inst"], "editar");

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, indicador_status($item["fl_meta"], $item["fl_direcao"])
			, ($nr_valor_1!='')?$nr_valor_1:''
			, $nr_meta
            , $nr_faixa
			, $link 
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
