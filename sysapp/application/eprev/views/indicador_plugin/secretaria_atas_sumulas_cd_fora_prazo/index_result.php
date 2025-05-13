<?php
	$head = array( 
        '#', 
        $label_0, 
        $label_1, 
        $label_2, 
        $label_3, 
        $label_4, 
        $label_5, 
        $label_6,
        $label_7,
        $label_8,
        $label_9,
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
    $ultimo_mes         = 0;
    $contador_ano_atual = 0;

    $nr_total_sumulas_atas           = 0;
    $nr_total_atas_10_dias           = 0;
    $nr_total_atas_30_dias           = 0;
    $nr_total_sumulas_48_horas       = 0;

    $nr_total_resultado_atas_10_dias     = 0;
    $nr_resultado_atas_30_dias           = 0;
    $nr_total_resultado_sumulas_48_horas = 0;

    foreach ($collection as $item)
    {
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo/cadastro/'.$item['cd_secretaria_atas_sumulas_cd_fora_prazo'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes = $item['mes_referencia'];

            $nr_total_sumulas_atas               += $item['nr_sumulas_atas'];
            $nr_total_atas_10_dias               += $item['nr_atas_10_dias'];
            $nr_total_atas_30_dias               += $item['nr_atas_30_dias'];
            $nr_total_sumulas_48_horas           += $item['nr_sumulas_48_horas'];

        }

        $body[] = array(
            $contador--,
            $referencia,
            $item['nr_sumulas_atas'],
            $item['nr_atas_10_dias'],
            number_format($item['nr_resultado_atas_10_dias'], 2, ',', '.').' %',
            $item['nr_atas_30_dias'],
            number_format($item['nr_resultado_atas_30_dias'], 2, ',', '.').' %',
            $item['nr_sumulas_48_horas'],
            number_format($item['nr_resultado_sumulas_48_horas'], 2, ',', '.').' %',
            number_format($item['nr_meta'], 2, ',', '.').' %',
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );
    }


    if($contador_ano_atual > 0)
    {
        $nr_total_resultado_atas_10_dias     = ($nr_total_atas_10_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
        $nr_total_resultado_atas_30_dias     = ($nr_total_atas_30_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
        $nr_total_resultado_sumulas_48_horas = ($nr_total_sumulas_48_horas / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
            '<b>'.$nr_total_sumulas_atas.'</b>',
            '<b>'.$nr_total_atas_10_dias.'</b>',
            '<b>'.number_format($nr_total_resultado_atas_10_dias, 2, ',', '.').' % </b>',
            '<b>'.$nr_total_atas_30_dias.'</b>',
            '<b>'.number_format($nr_total_resultado_atas_30_dias, 2, ',', '.').' % </b>',
            '<b>'.$nr_total_sumulas_48_horas.'</b>',
            '<b>'.number_format($nr_total_resultado_sumulas_48_horas, 2, ',', '.').' % </b>',
            '<b>'.number_format($item['nr_meta'], 2, ',', '.').' % </b>',
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