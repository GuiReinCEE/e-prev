<?php
$head = array( 
	'Mês/Ano'
    , 'Abertas'
    , 'Crítica Auto (Conc)'
	, 'Crítica Usuário (Conc)'
	, 'Não Crítica (Conc)'
    , 'Total (Conc)'
    , 'Canceladas'
    , 'Suspensas'
    , 'Atendidas'
    , '% Atendidas'
    , 'Abertas'
    , 'Crítica Auto (Conc)'
	, 'Crítica Usuário (Conc)'
	, 'Não Crítica (Conc)'
    , 'Total (Conc)'
    , 'Canceladas'
    , 'Suspensas'
    , 'Atendidas'
    , '% Atendidas'
);

$body=array();

$sum_abertas_sup    = 0;
$sum_concluida_crit_auto_sup = 0;
$sum_concluida_crit_user_sup = 0;
$sum_concluida_crit_nao_sup = 0;
$sum_concluidas_sup = 0;
$sum_canceladas_sup = 0;
$sum_suspensas_sup  = 0;
$sum_atendida_sup  = 0;

$qt_resumo_anterior_aberta_sup    = $qt_anterior_aberta_sup;
$qt_resumo_anterior_concluida_crit_auto_sup = $qt_anterior_concluida_crit_auto_sup;
$qt_resumo_anterior_concluida_crit_user_sup = $qt_anterior_concluida_crit_user_sup;
$qt_resumo_anterior_concluida_crit_nao_sup  = $qt_anterior_concluida_crit_nao_sup;
$qt_resumo_anterior_concluida_sup = $qt_anterior_concluida_sup;
$qt_resumo_anterior_cancelada_sup = $qt_anterior_cancelada_sup;
$qt_resumo_anterior_suspensa_sup  = $qt_anterior_suspensa_sup;
$qt_resumo_anterior_atendida_sup  = ($qt_anterior_concluida_sup + $qt_anterior_cancelada_sup + $qt_anterior_suspensa_sup);

if($qt_anterior_aberta_sup > 0)
{
    $qt_anterior_atendida_perc_sup = number_format((($qt_anterior_concluida_sup + $qt_anterior_cancelada_sup + $qt_anterior_suspensa_sup) * 100)/$qt_anterior_aberta_sup,2);
}
else
{
    $qt_anterior_atendida_perc_sup = 0;
}

$qt_anteior_atendida_sup = $qt_anterior_concluida_sup + $qt_anterior_cancelada_sup + $qt_anterior_suspensa_sup;

$sum_abertas_sis = 0;
$sum_concluida_crit_auto_sis = 0;
$sum_concluida_crit_user_sis = 0;
$sum_concluida_crit_nao_sis  = 0;
$sum_concluidas_sis = 0;
$sum_canceladas_sis = 0;
$sum_suspensas_sis  = 0;
$sum_atendida_sis   = 0;

$qt_resumo_anterior_aberta_sis    = $qt_anterior_aberta_sis;
$qt_resumo_anterior_concluida_crit_auto_sis = $qt_anterior_concluida_crit_auto_sis;
$qt_resumo_anterior_concluida_crit_user_sis = $qt_anterior_concluida_crit_user_sis;
$qt_resumo_anterior_concluida_crit_nao_sis  = $qt_anterior_concluida_crit_nao_sis;
$qt_resumo_anterior_concluida_sis = $qt_anterior_concluida_sis;
$qt_resumo_anterior_cancelada_sis = $qt_anterior_cancelada_sis;
$qt_resumo_anterior_suspensa_sis  = $qt_anterior_suspensa_sis;
$qt_resumo_anterior_atendida_sis  = ($qt_anterior_concluida_sis + $qt_anterior_cancelada_sis + $qt_anterior_suspensa_sis);

if($qt_anterior_aberta_sis > 0)
{
    $qt_anterior_atendida_perc_sis = number_format((($qt_anterior_concluida_sis + $qt_anterior_cancelada_sis + $qt_anterior_suspensa_sis) * 100)/$qt_anterior_aberta_sis,2);
}
else
{
    $qt_anterior_atendida_perc_sis = 0;
}

$qt_anteior_atendida_sis = $qt_anterior_concluida_sis + $qt_anterior_cancelada_sis + $qt_anterior_suspensa_sis;

$sum_abertas_res = 0;
$sum_concluida_crit_auto_res = 0;
$sum_concluida_crit_user_res = 0;
$sum_concluida_crit_nao_res = 0;
$sum_concluidas_res = 0;
$sum_canceladas_res = 0;
$sum_suspensas_res = 0;
$sum_atendida_res = 0;

$ano_anterior = $ano-1;
$nr_mes = 1;

//echo form_start_box( "default_box", "Atividades Suporte em ". $ano, true, false, "style='text-align:center;'" );
$body[] = array(
    '<b>Até 12/'.$ano_anterior.'</b>'
    ,'<b>'.$qt_anterior_aberta_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_sup.'</b>'
    ,'<b>'.$qt_anterior_cancelada_sup.'</b>'
    ,'<b>'.$qt_anterior_suspensa_sup.'</b>'
    ,'<b>'.$qt_anteior_atendida_sup.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_sup,2,',','.').'%'.'</b>'
    ,'<b>'.$qt_anterior_aberta_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_sup.'</b>'
    ,'<b>'.$qt_anterior_concluida_sup.'</b>'
    ,'<b>'.$qt_anterior_cancelada_sup.'</b>'
    ,'<b>'.$qt_anterior_suspensa_sup.'</b>'
    ,'<b>'.$qt_anteior_atendida_sup.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_sup,2,',','.').'%'.'</b>'  
);

while($nr_mes <= 12)
{
    $qt_atendida_sup = $concluidas_sup[$nr_mes] + $canceladas_sup[$nr_mes] + $suspensas_sup[$nr_mes];

    if($abertas_sup[$nr_mes] > 0)
    {
        $qt_atendida_perc_sup = number_format((($concluidas_sup[$nr_mes] + $canceladas_sup[$nr_mes] + $suspensas_sup[$nr_mes]) * 100)/$abertas_sup[$nr_mes],2);
    }
    else
    {
        $qt_atendida_perc_sup = 0;
    }
   
    $sum_abertas_sup += $abertas_sup[$nr_mes];
    $sum_concluida_crit_auto_sup += $concluida_crit_auto_sup[$nr_mes];
    $sum_concluida_crit_user_sup += $concluida_crit_user_sup[$nr_mes];
    $sum_concluida_crit_nao_sup += $concluida_crit_nao_sup[$nr_mes];
    $sum_concluidas_sup += $concluidas_sup[$nr_mes];
    $sum_canceladas_sup += $canceladas_sup[$nr_mes];
    $sum_suspensas_sup += $suspensas_sup[$nr_mes];
    $sum_atendida_sup += $qt_atendida_sup;

    $body[] = array(
        $nr_mes.'/'.$ano
        ,$abertas_sup[$nr_mes]
        ,$concluida_crit_auto_sup[$nr_mes]
        ,$concluida_crit_user_sup[$nr_mes]
        ,$concluida_crit_nao_sup[$nr_mes]
        ,$concluidas_sup[$nr_mes]
        ,$canceladas_sup[$nr_mes]
        ,$suspensas_sup[$nr_mes]
        ,$qt_atendida_sup
        ,number_format($qt_atendida_perc_sup,2,',','.').'%'
        ,$qt_resumo_anterior_aberta_sup+=$abertas_sup[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_auto_sup+=$concluida_crit_auto_sup[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_user_sup+=$concluida_crit_user_sup[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_nao_sup+=$concluida_crit_nao_sup[$nr_mes]
        ,$qt_resumo_anterior_concluida_sup+=$concluidas_sup[$nr_mes]
        ,$qt_resumo_anterior_cancelada_sup+=$canceladas_sup[$nr_mes]
        ,$qt_resumo_anterior_suspensa_sup+=$suspensas_sup[$nr_mes]
        ,$qt_resumo_anterior_atendida_sup+=$qt_atendida_sup
        ,$qt_resumo_anterior_aberta_sup  > 0 ? number_format((($qt_resumo_anterior_concluida_sup + $qt_resumo_anterior_cancelada_sup + $qt_resumo_anterior_suspensa_sup) * 100)/ $qt_resumo_anterior_aberta_sup,2,',','.').'%' : 0
	);
    $nr_mes++;
}

$body[] = array(
    '<b>Até 12/'.$ano.'</b>'
    ,'<b>'.$sum_abertas_sup.'</b>'
    ,'<b>'.$sum_concluida_crit_auto_sup.'</b>'
    ,'<b>'.$sum_concluida_crit_user_sup.'</b>'
    ,'<b>'.$sum_concluida_crit_nao_sup.'</b>'
    ,'<b>'.$sum_concluidas_sup.'</b>'
    ,'<b>'.$sum_canceladas_sup.'</b>'
    ,'<b>'.$sum_suspensas_sup.'</b>'
    ,'<b>'.$sum_atendida_sup.'</b>'
    ,'<b>'.($sum_abertas_sup > 0 ? number_format(($sum_atendida_sup * 100)/ $sum_abertas_sup,2,',','.').'%' : 20) .'</b>'
    ,'<b>'.$qt_resumo_anterior_aberta_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_auto_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_user_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_nao_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_cancelada_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_suspensa_sup.'</b>'
    ,'<b>'.$qt_resumo_anterior_atendida_sup.'</b>'
    ,'<b>'.($qt_resumo_anterior_aberta_sup > 0 ? number_format((($qt_resumo_anterior_concluida_sup + $qt_resumo_anterior_cancelada_sup + $qt_resumo_anterior_suspensa_sup) * 100)/ $qt_resumo_anterior_aberta_sup,2,',','.').'%' : 0) .'</b>'
);
$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_suporte';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo form_start_box("tb_suporte_box","Atividades Suporte em ".$ano,FALSE);
	echo $grid->render();	
echo form_end_box("tb_suporte_box");
echo br(2);




$nr_mes = 1;
$body=array();

$body[] = array(
    '<b>Até 12/'.$ano_anterior.'</b>'
    ,'<b>'.$qt_anterior_aberta_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_sis.'</b>'
    ,'<b>'.$qt_anterior_cancelada_sis.'</b>'
    ,'<b>'.$qt_anterior_suspensa_sis.'</b>'
    ,'<b>'.$qt_anteior_atendida_sis.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_sis,2,',','.').'%'.'</b>'
    ,'<b>'.$qt_anterior_aberta_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_sis.'</b>'
    ,'<b>'.$qt_anterior_concluida_sis.'</b>'
    ,'<b>'.$qt_anterior_cancelada_sis.'</b>'
    ,'<b>'.$qt_anterior_suspensa_sis.'</b>'
    ,'<b>'.$qt_anteior_atendida_sis.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_sis,2,',','.').'%'.'</b>'
);

while($nr_mes <= 12)
{
    $qt_atendida_sis = $concluidas_sis[$nr_mes] + $canceladas_sis[$nr_mes] + $suspensas_sis[$nr_mes];

    if($abertas_sis[$nr_mes] > 0)
    {
        $qt_atendida_perc_sis = number_format((($concluidas_sis[$nr_mes] + $canceladas_sis[$nr_mes] + $suspensas_sis[$nr_mes]) * 100)/$abertas_sis[$nr_mes],2);
    }
    else
    {
        $qt_atendida_perc_sis = 0;
    }

    $sum_abertas_sis += $abertas_sis[$nr_mes];
    $sum_concluida_crit_auto_sis += $concluida_crit_auto_sis[$nr_mes];
    $sum_concluida_crit_user_sis += $concluida_crit_user_sis[$nr_mes];
    $sum_concluida_crit_nao_sis += $concluida_crit_nao_sis[$nr_mes];
    $sum_concluidas_sis += $concluidas_sis[$nr_mes];
    $sum_canceladas_sis += $canceladas_sis[$nr_mes];
    $sum_suspensas_sis += $suspensas_sis[$nr_mes];
    $sum_atendida_sis += $qt_atendida_sis;

    $body[] = array(
        $nr_mes.'/'.$ano
        ,$abertas_sis[$nr_mes]
        ,$concluida_crit_auto_sis[$nr_mes]
        ,$concluida_crit_user_sis[$nr_mes]
        ,$concluida_crit_nao_sis[$nr_mes]
        ,$concluidas_sis[$nr_mes]
        ,$canceladas_sis[$nr_mes]
        ,$suspensas_sis[$nr_mes]
        ,$qt_atendida_sis
        ,number_format($qt_atendida_perc_sis,2,',','.').'%'
        ,$qt_resumo_anterior_aberta_sis+=$abertas_sis[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_auto_sis+=$concluida_crit_auto_sis[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_user_sis+=$concluida_crit_user_sis[$nr_mes]
        ,$qt_resumo_anterior_concluida_crit_nao_sis+=$concluida_crit_nao_sis[$nr_mes]
        ,$qt_resumo_anterior_concluida_sis+=$concluidas_sis[$nr_mes]
        ,$qt_resumo_anterior_cancelada_sis+=$canceladas_sis[$nr_mes]
        ,$qt_resumo_anterior_suspensa_sis+=$suspensas_sis[$nr_mes]
        ,$qt_resumo_anterior_atendida_sis+=$qt_atendida_sis
        ,$qt_resumo_anterior_aberta_sis > 0 ? number_format((($qt_resumo_anterior_concluida_sis + $qt_resumo_anterior_cancelada_sis + $qt_resumo_anterior_suspensa_sis) * 100)/ $qt_resumo_anterior_aberta_sis,2,',','.').'%' : 0
	);
    $nr_mes++;
}

$body[] = array(
    '<b>Até 12/'.$ano.'</b>'
    ,'<b>'.$sum_abertas_sis.'</b>'
    ,'<b>'.$sum_concluida_crit_auto_sis.'</b>'
    ,'<b>'.$sum_concluida_crit_user_sis.'</b>'
    ,'<b>'.$sum_concluida_crit_nao_sis.'</b>'
    ,'<b>'.$sum_concluidas_sis.'</b>'
    ,'<b>'.$sum_canceladas_sis.'</b>'
    ,'<b>'.$sum_suspensas_sis.'</b>'
    ,'<b>'.$sum_atendida_sis.'</b>'
    ,'<b>'.($sum_abertas_sis > 0 ? number_format(($sum_atendida_sis * 100)/ $sum_abertas_sis,2,',','.').' %': 0) .'</b>'
    ,'<b>'.$qt_resumo_anterior_aberta_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_auto_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_user_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_nao_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_cancelada_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_suspensa_sis.'</b>'
    ,'<b>'.$qt_resumo_anterior_atendida_sis.'</b>'
    ,'<b>'.($qt_resumo_anterior_aberta_sis > 0 ? number_format((($qt_resumo_anterior_concluida_sis + $qt_resumo_anterior_cancelada_sis + $qt_resumo_anterior_suspensa_sis) * 100)/ $qt_resumo_anterior_aberta_sis,2,',','.').' %' : 0) .'</b>'
);

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_sistema';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo form_start_box("tb_sistema_box","Atividades Sistemas em ".$ano,FALSE);
	echo $grid->render();	
echo form_end_box("tb_sistema_box");
echo br(2);





//echo form_end_box("default_box");
$nr_mes = 1;
$body=array();

$qt_anterior_aberto_res = $qt_anterior_aberta_sis + $qt_anterior_aberta_sup;
$qt_anterior_concluida_crit_auto_res = $qt_anterior_concluida_crit_auto_sis + $qt_anterior_concluida_crit_auto_sup;
$qt_anterior_concluida_crit_user_res = $qt_anterior_concluida_crit_user_sis + $qt_anterior_concluida_crit_user_sup;
$qt_anterior_concluida_crit_nao_res = $qt_anterior_concluida_crit_nao_sis + $qt_anterior_concluida_crit_nao_sup;
$qt_anterior_concluida_res = $qt_anterior_concluida_sis + $qt_anterior_concluida_sup;
$qt_anterior_cancelada_res = $qt_anterior_cancelada_sis + $qt_anterior_cancelada_sup;
$qt_anterior_suspensa_res = $qt_anterior_suspensa_sis + $qt_anterior_suspensa_sup;
$qt_anteior_atendida_res = $qt_anteior_atendida_sis + $qt_anteior_atendida_sup;

if($qt_anterior_aberto_res > 0)
{
    $qt_anterior_atendida_perc_res = number_format((($qt_anterior_concluida_res + $qt_anterior_cancelada_res + $qt_anterior_suspensa_res) * 100)/$qt_anterior_aberto_res,2);
}
else
{
    $qt_anterior_atendida_perc_res = 0;
}

$qt_resumo_anterior_aberta_res    = $qt_anterior_aberto_res;
$qt_resumo_anterior_concluida_crit_auto_res = $qt_anterior_concluida_crit_auto_res;
$qt_resumo_anterior_concluida_crit_user_res = $qt_anterior_concluida_crit_user_res;
$qt_resumo_anterior_concluida_crit_nao_res  = $qt_anterior_concluida_crit_nao_res;
$qt_resumo_anterior_concluida_res = $qt_anterior_concluida_res;
$qt_resumo_anterior_cancelada_res = $qt_anterior_cancelada_res;
$qt_resumo_anterior_suspensa_res  = $qt_anterior_suspensa_res;
$qt_resumo_anterior_atendida_res  = ($qt_anterior_concluida_res + $qt_anterior_cancelada_res + $qt_anterior_suspensa_res);

$body[] = array(
    '<b>Até 12/'.$ano_anterior.'</b>'
    ,'<b>'.$qt_anterior_aberto_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_res.'</b>'
    ,'<b>'.$qt_anterior_cancelada_res.'</b>'
    ,'<b>'.$qt_anterior_suspensa_res.'</b>'
    ,'<b>'.$qt_anteior_atendida_res.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_res,2,',','.').'%'.'</b>'
    ,'<b>'.$qt_anterior_aberto_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_auto_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_user_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_crit_nao_res.'</b>'
    ,'<b>'.$qt_anterior_concluida_res.'</b>'
    ,'<b>'.$qt_anterior_cancelada_res.'</b>'
    ,'<b>'.$qt_anterior_suspensa_res.'</b>'
    ,'<b>'.$qt_anteior_atendida_res.'</b>'
    ,'<b>'.number_format($qt_anterior_atendida_perc_res,2,',','.').'%'.'</b>'
);

while($nr_mes <= 12)
{
    $abertas_res = $abertas_sis[$nr_mes] + $abertas_sup[$nr_mes];
    $concluida_crit_auto_res = $concluida_crit_auto_sis[$nr_mes] + $concluida_crit_auto_sup[$nr_mes];
    $concluida_crit_user_res = $concluida_crit_user_sis[$nr_mes] + $concluida_crit_user_sup[$nr_mes];
    $concluida_crit_nao_res = $concluida_crit_nao_sis[$nr_mes] + $concluida_crit_nao_sup[$nr_mes];
    $concluidas_res = $concluidas_sis[$nr_mes] + $concluidas_sup[$nr_mes];
    $canceladas_res = $canceladas_sis[$nr_mes] + $canceladas_sup[$nr_mes];
    $suspensas_res = $suspensas_sis[$nr_mes] + $suspensas_sup[$nr_mes];

    $atendida_res = $concluidas_res + $canceladas_res + $suspensas_res;

    if($abertas_res > 0)
    {
        $atendida_perc_res = number_format((($concluidas_res + $canceladas_res + $suspensas_res) * 100)/$abertas_res,2);
    }
    else
    {
        $atendida_perc_res = 0;
    }

    $sum_abertas_res += $abertas_res;
    $sum_concluida_crit_auto_res += $concluida_crit_auto_res;
    $sum_concluida_crit_user_res += $concluida_crit_user_res;
    $sum_concluida_crit_nao_res += $concluida_crit_nao_res;
    $sum_concluidas_res += $concluidas_res;
    $sum_canceladas_res += $canceladas_res;
    $sum_suspensas_res += $suspensas_res;
    $sum_atendida_res += $atendida_res;

    $body[] = array(
        $nr_mes.'/'.$ano
        ,$abertas_res
        ,$concluida_crit_auto_res
        ,$concluida_crit_user_res
        ,$concluida_crit_nao_res
        ,$concluidas_res
        ,$canceladas_res
        ,$suspensas_res
        ,$atendida_res
        ,number_format($atendida_perc_res,2,',','.').'%'
        ,$qt_resumo_anterior_aberta_res+=$abertas_res
        ,$qt_resumo_anterior_concluida_crit_auto_res+=$concluida_crit_auto_res
        ,$qt_resumo_anterior_concluida_crit_user_res+=$concluida_crit_user_res
        ,$qt_resumo_anterior_concluida_crit_nao_res+=$concluida_crit_nao_res
        ,$qt_resumo_anterior_concluida_res+=$concluidas_res
        ,$qt_resumo_anterior_cancelada_res+=$canceladas_res
        ,$qt_resumo_anterior_suspensa_res+=$suspensas_res
        ,$qt_resumo_anterior_atendida_res+=$atendida_res
        ,$qt_resumo_anterior_aberta_res > 0 ? number_format((($qt_resumo_anterior_concluida_res + $qt_resumo_anterior_cancelada_res + $qt_resumo_anterior_suspensa_res) * 100)/ $qt_resumo_anterior_aberta_res,2,',','.').'%' : 0
	);
    $nr_mes++;
}

$body[] = array(
    '<b>Até 12/'.$ano.'</b>'
    ,'<b>'.$sum_abertas_res.'</b>'
    ,'<b>'.$sum_concluida_crit_auto_res.'</b>'
    ,'<b>'.$sum_concluida_crit_user_res.'</b>'
    ,'<b>'.$sum_concluida_crit_nao_res.'</b>'
    ,'<b>'.$sum_concluidas_res.'</b>'
    ,'<b>'.$sum_canceladas_res.'</b>'
    ,'<b>'.$sum_suspensas_res.'</b>'
    ,'<b>'.$sum_atendida_res.'</b>'
    ,'<b>'.($sum_abertas_res > 0 ? number_format(($sum_atendida_res * 100)/ $sum_abertas_res,2,',','.').' %' : 0) .'</b>'
    ,'<b>'.$qt_resumo_anterior_aberta_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_auto_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_user_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_crit_nao_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_concluida_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_cancelada_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_suspensa_res.'</b>'
    ,'<b>'.$qt_resumo_anterior_atendida_res.'</b>'
    ,'<b>'.($qt_resumo_anterior_aberta_res > 0 ? number_format((($qt_resumo_anterior_concluida_res + $qt_resumo_anterior_cancelada_res + $qt_resumo_anterior_suspensa_res) * 100)/ $qt_resumo_anterior_aberta_res,2,',','.').' %' : 0) .'</b>'
);

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_resumo';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo form_start_box("tb_resumo_box","Resumo Atividades em ".$ano,FALSE);
	echo $grid->render();	
echo form_end_box("tb_resumo_box");
echo br(5);

/*
//
$head = array(
	'Divisão'
    , 'Abertas'
    , 'Concluídas'
	, 'Canceladas'
	, 'Suspensas'
    , 'Atendidas'
    , '% Atendidas'
);

$body=array();
$sum_resmo_aberta = 0;
$sum_resmo_concluida = 0;
$sum_resmo_cancelada = 0;
$sum_resmo_suspensa = 0;
$sum_resmo_atendida = 0;

foreach( $resumo_divisao as $item )
{
    if($item['qt_aberta'] > 0)
	{
        $qt_resumo_atendida_perc =number_format((($item['qt_concluida'] + $item['qt_cancelada']) / $item['qt_aberta']) * 100 ,2);
	}
    else
    {
        $qt_resumo_atendida_perc = 0;
    }

    $sum_resmo_aberta += $item['qt_aberta'];
    $sum_resmo_concluida += $item['qt_concluida'];
    $sum_resmo_cancelada += $item['qt_cancelada'];
    $sum_resmo_suspensa += $item['qt_suspensa'];
    $sum_resmo_atendida += $item['qt_atendida'];

    $body[] = array(
        $item['ds_divisao']
       ,$item['qt_aberta']
       ,$item['qt_concluida']
       ,$item['qt_cancelada']
       ,$item['qt_suspensa']
       ,$item['qt_atendida']
       ,number_format($qt_resumo_atendida_perc,2,',','.').'%'
    );
}

if($sum_resmo_aberta > 0)
{
    $sum_resmo_atendida_perc = number_format((($sum_resmo_concluida + $sum_resmo_cancelada) / $sum_resmo_aberta) * 100 ,2);
}
else
{
    $sum_resmo_atendida_perc = 0;
}

$body[] = array(
    '<b>Total</b>'
   ,'<b>'.$sum_resmo_aberta.'</b>'
   ,'<b>'.$sum_resmo_concluida.'</b>'
   ,'<b>'.$sum_resmo_cancelada.'</b>'
   ,'<b>'.$sum_resmo_suspensa.'</b>'
   ,'<b>'.$sum_resmo_atendida.'</b>'
   ,number_format($sum_resmo_atendida_perc,2,',','.').'%'
);

echo "<BR><h1 style='text-align:left; font-size: 120%;'>Resumo Acumulado por Divisão</h1><BR>";
$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_acumulado';
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
*/
?>