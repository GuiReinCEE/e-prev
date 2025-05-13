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
        '',
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
    $contador_ano_atual = 0;
	$ultimo_mes         = 0;

    $nr_improcede_total = 0;
    $nr_parcial_total   = 0;
    $nr_procede_total   = 0;
    $nr_total           = 0;

    $pr_improcede = 0;
    $pr_parcial   = 0;
    $pr_procede   = 0;

    foreach($collection as $item)
	{
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/juridico_sucesso_acoes_ribeiro_civel_mensal/cadastro/'.$item['cd_juridico_sucesso_acoes_ribeiro_civel_mensal'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes = $item['mes_referencia'];

            $nr_improcede_total += $item['nr_improcede_total'];
            $nr_parcial_total   += $item['nr_parcial_total'];
            $nr_procede_total   += $item['nr_procede_total'];
            $nr_total           += $item['nr_total'];
        }
        
        $body[] = array(
            $contador--,
            $referencia,
            $item['nr_inicial'],
            $item['nr_improcede_total'],
            number_format($item['pr_improcede'], 2, ',', '.').' %',
            $item['nr_parcial_total'],
            number_format($item['pr_parcial'], 2, ',', '.').' %',
            $item['nr_procede_total'],           
            number_format($item['pr_procede'], 2, ',', '.').' %',
            $item['nr_total'],
            $item['ds_tabela'],
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );  
    }

    if($contador_ano_atual > 0)
    {
        $pr_improcede = ($nr_improcede_total / ($nr_total > 0 ? $nr_total : 1)) * 100;
        $pr_parcial   = ($nr_parcial_total / ($nr_total > 0 ? $nr_total : 1)) * 100;
        $pr_procede   = ($nr_procede_total / ($nr_total > 0 ? $nr_total : 1)) * 100;

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
            '',
            '<b>'.$nr_improcede_total.'</b>', 
            number_format($pr_improcede, 2, ',', '.').' %',
            '<b>'.$nr_parcial_total.'</b>', 
            number_format($pr_parcial, 2, ',', '.').' %',
            '<b>'.$nr_procede_total.'</b>', 
            number_format($pr_procede, 2, ',', '.').' %',
            '<b>'.$nr_total.'</b>', 
            '',
            '', 
            ''
        );
    }

    /*
    $item['nr_inicial'],
            $item['nr_improcede_total'],
            number_format($item['pr_improcede'], 2, ',', '.').' %',
            $item['nr_parcial_total'],
            number_format($item['pr_parcial'], 2, ',', '.').' %',
            $item['nr_procede_total'],           
            number_format($item['pr_procede'], 2, ',', '.').' %',
            $item['nr_total'],
            $item['ds_tabela'],
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
    */



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