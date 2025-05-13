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

    $contador = sizeof($collection);
    $contador_ano_atual = 0;
	$ultimo_mes         = 0;

    $nr_resultado_total = 0;
    $nr_meta_total 		= 0;

    foreach($collection as $item)
	{
		$nr_resultado = $item['nr_resultado'];
		$nr_meta 	  = $item['nr_meta'];

        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];

			$nr_resultado = number_format($item['nr_resultado'], 2, ',', '.');
			$nr_meta 	  = number_format($item['nr_meta'], 2, ',', '.');
		}
		else
		{
			$link = anchor('indicador_plugin/financeiro_controle_atrasos_patrocinadora/cadastro/'.$item['cd_financeiro_controle_atrasos_patrocinadora'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes = $item['mes_referencia'];

            $nr_resultado_total += $item['nr_resultado'];
            $nr_meta_total 	  	+= $item['nr_meta'];
        }
        
        $body[] = array(
            $contador--,
            $referencia,
            $nr_resultado,        	
            $nr_meta,
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );  
    }

    if($contador_ano_atual > 0)
    {
        $nr_resultado = $nr_resultado_total / $contador_ano_atual;
        $nr_meta 	  = $nr_meta_total / $contador_ano_atual;

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.number_format($nr_resultado, 2, ',', '.').'</b>',        	
			'<b>'.number_format($nr_meta, 2, ',', '.').'</b>',        	
            '', 
            ''
        );
    }

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