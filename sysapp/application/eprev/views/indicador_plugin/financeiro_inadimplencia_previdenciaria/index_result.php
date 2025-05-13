<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2, $label_3, $label_4, '', $label_5, ''
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
    
    $contador                   = sizeof($collection);
    $contador_ano_atual         = 0;
    $nr_carga_resultado         = 0;
    $nr_inadimplencia_resultado = 0;
    $nr_meta_resultado          = 0;
    $nr_resultado_resultado     = 0;

	$body = array();
	
	foreach($collection as $item)
	{	
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/financeiro_inadimplencia_previdenciaria/cadastro/'.$item['cd_financeiro_inadimplencia_previdenciaria'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        $ultimo_mes = $item['mes_referencia'];

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
        {
            $contador_ano_atual++;

            $nr_carga_resultado         += $item['nr_carga_resultado'];
            $nr_inadimplencia_resultado += $item['nr_inadimplencia_resultado'];
            $nr_meta_resultado          = $item['nr_meta_resultado'];
        }

		$body[] = array(
            $contador--,
            $referencia,
            number_format($item['nr_carga_resultado'], 2, ',', '.'),
            number_format($item['nr_inadimplencia_resultado'], 2, ',', '.'),
            number_format($item['nr_meta_resultado'], 2, ',', '.'),
            number_format($item['nr_resultado_resultado'], 2, ',', '.'),
            $item['ds_tabela'],
            array($item['ds_observacao'], 'text-align:justify'),
            $link
		);
    }
    
    if(intval($contador_ano_atual) > 0)
    {
		if(intval($nr_carga_resultado) > 0)
		{
			$nr_resultado_resultado = ($nr_inadimplencia_resultado / $nr_carga_resultado) * 100;
        }
        
        $body[] = array(
            0,
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
            number_format($nr_carga_resultado, 2, ',', '.'),
            number_format($nr_inadimplencia_resultado, 2, ',', '.'),
            number_format($nr_meta_resultado, 2, ',', '.'),
            number_format($nr_resultado_resultado, 2, ',', '.'),
            '',
            '',
            ''
        );
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
?>
	<input type='hidden' id='ultimo_mes' name='ultimo_mes' value='<?= $ultimo_mes ?>'/>
	<input type='hidden' id='contador' name='contador' value='<?= $contador_ano_atual ?>'/>
	<br/>
	<div style='text-align:center;'> 
		<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
	</div>
	<br/>
	<?= $grid->render() ?>