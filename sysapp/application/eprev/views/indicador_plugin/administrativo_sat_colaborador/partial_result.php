<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4,$label_6, ''
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

	$contador = sizeof($collection);
	foreach( $collection as $item )
	{
		$link = anchor("indicador_plugin/administrativo_sat_colaborador/detalhe/" . $item["cd_administrativo_sat_colaborador"], "editar");
		
		$nr_meta = $item["nr_meta"];
		$referencia = $item['periodo_ini']."/".$item['periodo_fim'];
		$nr_valor_1 = $item["nr_valor_1"];
		$nr_valor_2 = $item["nr_valor_2"];
		$nr_percentual_f = $item["nr_percentual_f"];
        $observacao = $item["observacao"];

		
		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,0,',','.'):''
			, ($nr_valor_2!='')?number_format($nr_valor_2,2,',','.'):''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f,2,',','.')."%":''
			, number_format($item["nr_meta"],2,',','.')."%"
            , array($observacao, 'text-align:"left"')
			, ($item['fl_editar'] == "S" ? $link : "")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>
