<?php
	$head = array( 
        '#', 
        $label_0, 
        $label_1, 
        $label_2, 
        '',
        $label_3, 
        $label_11, 
        $label_12, 
        $label_4, 
        '',
        $label_5, 
        $label_13, 
        $label_14, 
        $label_6,
        '',
        $label_7,
        $label_15,
        $label_16,
        $label_8,
        $label_9,
        '',
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
    $contador_ano_atual = 0;
	$ultimo_mes         = 0;

    $nr_inicial         = 0;
    $nr_improcede_total = 0;
    $nr_parcial_total   = 0;
    $nr_procede_total   = 0;
    $nr_totalizador     = 0;
    $nr_total           = 0;

    $pr_improcede = 0;
    $pr_parcial   = 0;
    $pr_procede   = 0;

    $nr_improc_min_total  = 0;
	$nr_improc_max_total  = 0;
	$nr_parcial_min_total = 0;
	$nr_parcial_max_total = 0;
	$nr_proc_min_total    = 0;
	$nr_proc_max_total    = 0;

	$status_improc  = '';
	$status_parcial = '';
	$status_proc    = '';

	$pr_improcede_ref = 0;
	$pr_parcial_ref   = 0;
	$pr_procede_ref   = 0;

    foreach($collection as $item)
	{
        if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];

			if(intval($item['ano_referencia']) == (intval($tabela[0]['nr_ano_referencia']) - 1))
			{
				$pr_improcede_ref = $item['pr_improcede'];
				$pr_parcial_ref   = $item['pr_parcial'];
				$pr_procede_ref   = $item['pr_procede'];
			}
		}
		else
		{
			$link = anchor('indicador_plugin/juridico_sucesso_acoes_consolidado_trab_mensal/cadastro/'.$item['cd_juridico_sucesso_acoes_consolidado_trab_mensal'], 'editar');

			$referencia = $item['mes_ano_referencia'];
        }

        if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
        {
            $contador_ano_atual++;

            $ultimo_mes = $item['mes_referencia'];

            $nr_inicial         += $item['nr_inicial'];
            $nr_improcede_total += $item['nr_improcede_total'];
            $nr_parcial_total   += $item['nr_parcial_total'];
            $nr_procede_total   += $item['nr_procede_total'];
            $nr_totalizador     += $item['nr_totalizador'];
            $nr_total           += $item['nr_total'];

        	$nr_improc_min_total  = $item['nr_improc_min'];
			$nr_improc_max_total  = $item['nr_improc_max'];
			$nr_parcial_min_total = $item['nr_parcial_min'];
			$nr_parcial_max_total = $item['nr_parcial_max'];
			$nr_proc_min_total    = $item['nr_proc_min'];
			$nr_proc_max_total    = $item['nr_proc_max'];
        }

        $status_improc  = indicador_status($item["fl_meta_improc"], $item["fl_direcao_improc"]);
		$status_parcial = indicador_status($item['fl_meta_parcial'], $item['fl_direcao_parcial']);
		$status_proc    = indicador_status($item['fl_meta_proc'], $item['fl_direcao_proc']);
        
        $body[] = array(
            $contador--,
            $referencia,
            $item['nr_inicial'],        	
            $item['nr_improcede_total'],
            $status_improc,
            number_format($item['pr_improcede'], 2, ',', '.').' %',
            number_format($item['nr_improc_min'], 2, ',', '.').' %',
            number_format($item['nr_improc_max'], 2, ',', '.').' %',
            $item['nr_parcial_total'],
            $status_parcial,
            number_format($item['pr_parcial'], 2, ',', '.').' %',
            number_format($item['nr_parcial_min'], 2, ',', '.').' %',
            number_format($item['nr_parcial_max'], 2, ',', '.').' %',
            $item['nr_procede_total'],           
            $status_proc,
            number_format($item['pr_procede'], 2, ',', '.').' %',
            number_format($item['nr_proc_min'], 2, ',', '.').' %',
            number_format($item['nr_proc_max'], 2, ',', '.').' %',
            $item['nr_totalizador'],
            $item['nr_total'],
            $item['ds_tabela'],
            array(nl2br($item['ds_observacao']), 'text-align:justify;'),
            $link
        );  
    }
/*
    if($contador_ano_atual > 0)
    {
        $pr_improcede = ($nr_improcede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
        $pr_parcial   = ($nr_parcial_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
        $pr_procede   = ($nr_procede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;

		//STATUS RESULTADO ANO IMPROCEDE
			//META
		if(floatval($pr_improcede) >= floatval($nr_improc_min_total))
		{
			$fl_meta_improcede_ref = 'S';
		}
		else if(floatval($pr_improcede) <= floatval($nr_improc_min_total))
		{
			$fl_meta_improcede_ref = 'N';
		}
		else
		{
			$fl_meta_improcede_ref = '';
		}

			//DIREÇÃO
		if(floatval($pr_improcede) > floatval($pr_improcede_ref))
		{
			$fl_direcao_improcede_ref = 'C';
		}
		else if(floatval($pr_improcede) < floatval($pr_improcede_ref))
		{
			$fl_direcao_improcede_ref = 'B';
		}
		else
		{
			$fl_direcao_improcede_ref = 'I';
		}

		//STATUS RESULTADO ANO PARCIAL
			//META
		if(floatval($pr_parcial) <= floatval($nr_parcial_max_total))
		{
			$fl_meta_parcial_ref = 'S';
		}
		else if(floatval($pr_parcial) >= floatval($nr_parcial_max_total))
		{
			$fl_meta_parcial_ref = 'N';
		}
		else
		{
			$fl_meta_parcial_ref = '';
		}

			//DIREÇÃO
		if(floatval($pr_parcial) > floatval($pr_parcial_ref))
		{
			$fl_direcao_parcial_ref = 'C';
		}
		else if(floatval($pr_parcial) < floatval($pr_parcial_ref))
		{
			$fl_direcao_parcial_ref = 'B';
		}
		else
		{
			$fl_direcao_parcial_ref = 'I';
		}

		//STATUS RESULTADO ANO PROCEDE
			//META
		if(floatval($pr_procede) <= floatval($nr_proc_max_total))
		{
			$fl_meta_proc_ref = 'S';
		}
		else if(floatval($pr_procede) >= floatval($nr_proc_max_total))
		{
			$fl_meta_proc_ref = 'N';
		}
		else
		{
			$fl_meta_proc_ref = '';
		}

			//DIREÇÃO
		if(floatval($pr_procede) > floatval($pr_procede_ref))
		{
			$fl_direcao_proc_ref = 'C';
		}
		else if(floatval($pr_procede) < floatval($pr_procede_ref))
		{
			$fl_direcao_proc_ref = 'B';
		}
		else
		{
			$fl_direcao_proc_ref = 'I';
		}

        $body[] = array(
            0, 
            '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
            '<b>'.$nr_inicial.'</b>',
            '<b>'.$nr_improcede_total.'</b>', 
            indicador_status($fl_meta_improcede_ref, $fl_direcao_improcede_ref),
            number_format($pr_improcede, 2, ',', '.').' %',
            number_format($nr_improc_min_total, 2, ',', '.').' %',
            number_format($nr_improc_max_total, 2, ',', '.').' %',
            '<b>'.$nr_parcial_total.'</b>', 
            indicador_status($fl_meta_parcial_ref, $fl_direcao_parcial_ref),
            number_format($pr_parcial, 2, ',', '.').' %',
            number_format($nr_parcial_min_total, 2, ',', '.').' %',
            number_format($nr_parcial_max_total, 2, ',', '.').' %',
            '<b>'.$nr_procede_total.'</b>', 
            indicador_status($fl_meta_proc_ref, $fl_direcao_proc_ref),
            number_format($pr_procede, 2, ',', '.').' %',
            number_format($nr_proc_min_total, 2, ',', '.').' %',
            number_format($nr_proc_max_total, 2, ',', '.').' %',
            '<b>'.$nr_totalizador.'</b>', 
            '<b>'.$nr_total.'</b>', 
            '',
            '', 
            ''
        );
    }
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