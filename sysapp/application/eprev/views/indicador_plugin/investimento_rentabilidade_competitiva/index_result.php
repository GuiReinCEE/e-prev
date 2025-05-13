<?php
	$head = array( 
        '#', 
        $label_0, 
        $label_1, 
        $label_2, 
        $label_3, 
        ''
    );

    $ar_janela = array(
		'width'      => '700',
		'height'     => '500',
		'scrollbars' => 'yes',
		'status'     => 'yes',
		'resizable'  => 'yes',
		'screenx'    => '0',
		'screeny'    => '0'
	);

    $body = array();

    $contador = sizeof($collection)+1;
    $contador_ano_atual = 0;
    $ultimo_mes         = 0;
    
    $nr_valor_1 = 0;
    $nr_valor_2 = 0;
	
	$nr_valor_1_acumulado = 0;
    $nr_valor_2_acumulado = 0;
	
	$nr_acumulado = 9;
	
	$valor_1 = array();
	$valor_2 = array();

    foreach($collection as $item)
	{
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/investimento_rentabilidade_competitiva/cadastro/'.$item['cd_investimento_rentabilidade_competitiva'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes = $item['mes_referencia'];

            $nr_valor_1 = $item['nr_valor_1'];
            $nr_valor_2 = $item['nr_valor_2'];
        }
		else 
		{
			$valor_1[] = $item['nr_valor_1'];
			$valor_2[] = $item['nr_valor_2'];
		}
		
        $body[] = array(
            $contador--,
            $referencia,
            number_format($item['nr_valor_1'], 2, ',', '.').' %',
            number_format($item['nr_valor_2'], 2, ',', '.').' %',
            array(nl2br($item['observacao']), 'text-align:justify;'),
            $link
        );  
    }

    if($contador_ano_atual > 0)
    {
        $body[] = array(
            1,
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
            '<b>'.number_format($nr_valor_1, 2, ',', '.').' %</b>',
            '<b>'.number_format($nr_valor_2, 2, ',', '.').' %</b>',
            '',
            ''
        );
		
		$valor_1[] = $nr_valor_1;
		$valor_2[] = $nr_valor_2;
    }
	
	$valor_1 = array_reverse($valor_1);
	$valor_2 = array_reverse($valor_2);
	
	while($nr_acumulado >= 0)
	{
		$nr_valor_1_realizado = ($valor_1[$nr_acumulado]/100)+1;
		$nr_valor_2_realizado = ($valor_2[$nr_acumulado]/100)+1;

		if($nr_acumulado == 9)
		{
			$nr_valor_1_acumulado = $nr_valor_1_realizado;
			$nr_valor_2_acumulado = $nr_valor_2_realizado;
		}
		else
		{
			$nr_valor_1_acumulado = $nr_valor_1_acumulado * $nr_valor_1_realizado;
			$nr_valor_2_acumulado = $nr_valor_2_acumulado * $nr_valor_2_realizado;
		}
		
		$nr_acumulado --;
	}
	
	$body[] = array(
		0,
		'<b>Acumulado dos últimos 10 anos</b>',
		'<b>'.number_format(($nr_valor_1_acumulado-1)*100, 2, ',', '.').' %</b>',
		'<b>'.number_format(($nr_valor_2_acumulado-1)*100, 2, ',', '.').' %</b>',
		'',
		''
	);

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
?>
	<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
	<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
	<br/>
    <div style="text-align:center;"> 
		<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
	</div>
	<br/>
<?= $grid->render(); ?>