<?php
	$head = array( 
        '#', 
        $label_0, 
        $label_1, 
        $label_8, 
        $label_2, 
        $label_5, 
        $label_4, 
        $label_3,
        $label_6,
        $label_7,
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

    $nr_total_interesse   = 0;
    $nr_total_efetivas    = 0;
    $nr_total_negociacao  = 0;
    $nr_total_resultado   = 0;
    $nr_total_nao_retido  = 0;
    $nr_total_cliente     = 0;
    $nr_meta = 0;

    foreach ($collection as $item)
    {
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/atendimento_retencao_cliente/cadastro/'.$item['cd_atendimento_retencao_cliente'], '[editar]');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes++;

            $nr_total_interesse  += $item['nr_interesse'];
            $nr_total_cliente    += $item['nr_cliente'];
            $nr_total_efetivas   += $item['nr_efetivas'];
            $nr_total_nao_retido += $item['nr_nao_retido'];
            $nr_total_negociacao += $item['nr_negociacao'];
            $nr_meta = $item['nr_meta'];
        }

        $body[] = array(
            $contador--,
            $referencia,
            $item['nr_interesse'],
            $item['nr_cliente'],
            $item['nr_efetivas'],
            $item['nr_nao_retido'],
            $item['nr_negociacao'],
            number_format($item['nr_resultado'], 2, ',', '.').' %',
            number_format($item['nr_meta'], 2, ',', '.').' %',
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );
    }


    if($contador_ano_atual > 0)
    {
        $nr_total_resultado = ($nr_total_efetivas / ($nr_total_cliente > 0 ? $nr_total_cliente : 1)) * 100;

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
            '<b>'.$nr_total_interesse.'</b>',
			'<b>'.$nr_total_cliente.'</b>',
            '<b>'.$nr_total_efetivas.'</b>',
            '<b>'.$nr_total_nao_retido.'</b>',
            '<b>'.$nr_total_negociacao.'</b>',
            '<b>'.number_format($nr_total_resultado, 2, ',', '.').' % </b>',
            '<b>'.number_format($nr_meta, 2, ',', '.').' % </b>',
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