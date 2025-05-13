<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3
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
        $nr_ano      = $item["nr_ano"];
        $indicador   = $item['ds_indicador'];
        $indice      = $item['nr_indice'];
        $faixa       = $item['nr_faixa'];

		$body[] = array(
			 $contador--
            , $nr_ano
			, array($indicador, 'text-align:left')
            , $faixa
			, $indice
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
