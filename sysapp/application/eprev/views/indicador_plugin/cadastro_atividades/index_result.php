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
        $label_10,
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

    $nr_atividade_aberta    = 0;
    $nr_atividade_andamento = 0;
    $nr_atividade_concluida = 0;
    $nr_atividade_cancelada = 0;
    $nr_atividade_acumulada = 0;
    $nr_tempo_min           = 0;
    $nr_tempo_hora          = 0;

    $nr_atividade_atendidas = 0;
    $nr_meta                = 0;

    foreach ($collection as $item)
    {
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/cadastro_atividades/cadastro/'.$item['cd_cadastro_atividades'], '[editar]');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes++;

            $nr_atividade_aberta    += $item['nr_atividade_aberta'];
            $nr_atividade_andamento = $item['nr_atividade_andamento'];
            $nr_atividade_concluida += $item['nr_atividade_concluida'];
            $nr_atividade_cancelada += $item['nr_atividade_cancelada'];
            $nr_atividade_acumulada = $item['nr_atividade_acumulada'];

            $nr_tempo_min           += $item['nr_tempo_min'];
            $nr_tempo_hora          += $item['nr_tempo_hora'];
            $nr_meta                = $item['nr_meta'];
        }

        $body[] = array(
            $contador--,
            $referencia,
            $item['nr_atividade_aberta'],
            $item['nr_atividade_andamento'],
            $item['nr_atividade_concluida'],
            $item['nr_atividade_cancelada'],
            $item['nr_atividade_acumulada'],
            number_format($item['nr_atividade_atendidas'], 2, ',', '.').' %',
            number_format($item['nr_meta'], 2, ',', '.').' %',

            $item['nr_tempo_min'],
            $item['nr_tempo_hora'],
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );
    }


    if($contador_ano_atual > 0)
    {
        $nr_atividade_atendidas = 0;

        if(intval($nr_atividade_aberta) > 0)
        {
            $nr_atividade_atendidas = ($nr_atividade_concluida / $nr_atividade_aberta) * 100;
        }

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
            '<b>'.$nr_atividade_aberta.'</b>',
			'<b>'.$nr_atividade_andamento.'</b>',
            '<b>'.$nr_atividade_concluida.'</b>',
            '<b>'.$nr_atividade_cancelada.'</b>',
            '<b>'.$nr_atividade_acumulada.'</b>',
            '<b>'.number_format($nr_atividade_atendidas, 2, ',', '.').' % </b>',
            '<b>'.number_format($nr_meta, 2, ',', '.').' % </b>',
            '<b>'.$nr_tempo_min.'</b>',
            '<b>'.$nr_tempo_hora.'</b>',
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