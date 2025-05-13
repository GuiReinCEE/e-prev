<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_3, $label_4, $label_5, ''
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
	$total=0;
	$a_data=array(0, 0);
	foreach( $collection as $item )
	{
        $observacao = $item["observacao"];
		$a_data = explode( "/", $item['mes_referencia'] );
		
		$nr_valor_1 = $item["nr_valor_ceee"];
		$nr_valor_2 = $item["nr_valor_aes"];
		$nr_valor_3 = $item["nr_valor_cgtee"];
		$nr_valor_4 = $item["nr_valor_crm"];

		$link = "";
		if( $item['fl_media'] !='S' )
		{
			$link = anchor("indicador_plugin/atend_ativo_pi/detalhe/" . $item["cd_atend_ativo_pi"], "editar");
			
			$contador_ano_atual++;
		}
		
		$referencia = $item['mes_referencia'];

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,2,",","."):''
			, ($nr_valor_3!='')?number_format($nr_valor_3,2,",","."):''
			, ($nr_valor_4!='')?number_format($nr_valor_4,2,",","."):''
            , array($observacao, 'text-align:"left"')
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
