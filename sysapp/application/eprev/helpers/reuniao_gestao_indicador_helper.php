<?php

function get_grafico_indicador($row)
{
    $ob_ci = &get_instance();

    $ob_ci->load->plugin('pchart');
    $ob_ci->load->helper('indicador');
    
    $INDICADOR_GRAFICO_LINHA           = 1;
    $INDICADOR_GRAFICO_BARRA_ACUMULADO = 2;
    $INDICADOR_GRAFICO_BARRA_MULTIPLO  = 3;
    $INDICADOR_GRAFICO_PIZZA           = 4;     
    
    $nr_largura = 760;
    $nr_altura  = 450;

    $grafico = '';


    if($row AND count($row['parametro']) > 0)
    {
        $ar_referencia = Array();
        $array_pos_meta = explode(';', $row['nr_pos_meta']);

        if(count($array_pos_meta) == 1)
        {
            $ar_referencia["M"] = $array_pos_meta[0];
        }
        else if(count($array_pos_meta) > 1)
        {
            $ar_referencia["M"] = $array_pos_meta;
        }
        else
        {
            $ar_referencia["M"] = '-1';
        }
        
        $ar_referencia["T"] = $row['nr_pos_tendendencia'];
        $ar_referencia["R"] = $row['nr_pos_referencia'];

        $cd_indicador_tabela = $row['cd_indicador_tabela'];
        
        $fl_inverte = $row['fl_inverte'];
        
        if($row['cd_indicador_grafico_tipo'] == $INDICADOR_GRAFICO_PIZZA)
        {
            $t = explode( ',', $row['ds_range_tick'] );
            $al = explode( ';', $row['ds_range_legenda'] );
            $av = explode( ';', $row['ds_range_valor'] );
            $valores = Array();

            $rotulo = indicador_db::pegar_rotulos($cd_indicador_tabela, $t[0],$t[1], $t[2],$t[3]);

            foreach($al as $item)
            {
                $l = explode( ',', $item );
                $legendas[] = indicador_db::pegar_rotulos($cd_indicador_tabela, $l[0],$l[1], $l[2],$l[3]);
            }
            
            foreach( $legendas as $legs )
            {
                foreach( $legs as $leg )
                $legenda[] = $leg;
            }
            
            foreach($av as $item)
            {
                $v = explode( ',', $item );
                $ar_valor = indicador_db::pegar_valores($cd_indicador_tabela, $v[0],$v[1], $v[2],$v[3]);
                $valores[] = $ar_valor[0];                  
            }               

            #echo "<PRE>".print_r($valores[0],true)."</PRE>"; exit;
            if((count($valores) > 0) and (count($valores[0]) > 0))
            {
                $grafico = piechart($rotulo,$valores,$legenda,$nr_largura, $nr_altura);
            }
            else
            {
                $grafico = str_replace(base_url(),"",skin())."img/indicador_grafico_erro.png";
            }               
        }
        elseif($row['cd_indicador_grafico_tipo'] == $INDICADOR_GRAFICO_BARRA_ACUMULADO)
        {
            $t = explode( ',', $row['ds_range_tick'] );
            $l = explode( ',', $row['ds_range_legenda'] );
            $al = explode( ';', $row['ds_range_legenda'] );
            $av = explode( ';', $row['ds_range_valor'] );

            $tick = indicador_db::pegar_rotulos($cd_indicador_tabela, $t[0],$t[1], $t[2],$t[3]);

            foreach($al as $item)
            {
                $l = explode( ',', $item );
                $legendas[] = indicador_db::pegar_rotulos($cd_indicador_tabela, $l[0],$l[1], $l[2],$l[3]);
            }
            
            foreach($legendas as $legs)
            {
                foreach($legs as $leg)
                {
                    $legenda[] = $leg;
                }
            }
            
            /*
            foreach($av as $item)
            {
                $v = explode(',', $item );
                $valores[] = indicador_db::pegar_valores($cd_indicador_tabela, $v[0],$v[1], $v[2],$v[3]);
            }
            */
            #$grafico = $nr_largura; 
            
            #echo "<PRE>".print_r($t,true)."</PRE>"; exit;
            $rotulo = indicador_db::pegar_rotulos($cd_indicador_tabela, $t[0],$t[1], $t[2],$t[3]);              
           
            // tipos de objetos q serão exibidos no gráfico
            $idx=0;
            foreach($av as $item)
            {
                $part = explode('-', $item);
                $v = explode( ',', $part[0] );
                $valores[] = indicador_db::pegar_valores($cd_indicador_tabela, $v[0],$v[1], $v[2],$v[3]);
                
                if( sizeof($part)==2 )
                {
                    // apenas duas opções aceitas, barra é PADRÃO, então se não for linha, qualquer outro valor torna-se BARRA
                    if($part[1]=='linha')
                    {
                        $tipo[$idx]='linha';
                    }
                    else
                    {
                        $tipo[$idx]='barra';
                    }
                }
                else
                {
                    $tipo[$idx]='barra';
                }

                $idx++;
            }              
           
            #echo "<PRE>".print_r($valores,true)."</PRE>"; exit;
            if((count($valores) > 0) and (count($valores[0]) > 0))
            {
                if($fl_inverte == "S")
                {
                    $nr_fim   = count($valores);
                    $nr_conta = 0;
                    while($nr_conta < $nr_fim)
                    {
                        $valores[$nr_conta] = array_reverse($valores[$nr_conta]);
                        $nr_conta++;
                    }
                }
                
                $rotulo = ($fl_inverte == "S" ? array_reverse($rotulo) : $rotulo);
                
                $grafico = accumulate_barchart($rotulo, $valores, $tipo, $legenda, $nr_largura, $nr_altura,$ar_referencia);
            }
            else
            {
                $grafico = str_replace(base_url(),"",skin())."img/indicador_grafico_erro.png";
            }   

        }
        elseif($row['cd_indicador_grafico_tipo'] == $INDICADOR_GRAFICO_BARRA_MULTIPLO)
        {
            $t = explode( ',', $row['ds_range_tick'] );
            $l = explode( ',', $row['ds_range_legenda'] );
            $av = explode( ';', $row['ds_range_valor'] );
            $al = explode( ';', $row['ds_range_legenda'] );
            $valores = Array();
            
            #echo "<PRE>".print_r($al,true)."</PRE>"; #exit;
            
            foreach($al as $item)
            {
                $l = explode( ',', $item );
                $legendas[] = indicador_db::pegar_rotulos($cd_indicador_tabela, $l[0],$l[1], $l[2],$l[3]);
            }
            
            #echo "<PRE>".print_r($legendas,true)."</PRE>"; exit;
            
            foreach($legendas as $legs)
            {
                foreach($legs as $leg)
                {
                    $legenda[] = $leg;
                }
            }
            
            #echo "<PRE>".print_r($t,true)."</PRE>"; exit;
            $rotulo = indicador_db::pegar_rotulos($cd_indicador_tabela, $t[0],$t[1], $t[2],$t[3]);
            
            // tipos de objetos q serão exibidos no gráfico
            $idx=0;
            foreach($av as $item)
            {
                $part = explode('-', $item);
                $v = explode( ',', $part[0] );
                $valores[] = indicador_db::pegar_valores($cd_indicador_tabela, $v[0],$v[1], $v[2],$v[3]);
                
                if( sizeof($part)==2 )
                {
                    // apenas duas opções aceitas, barra é PADRÃO, então se não for linha, qualquer outro valor torna-se BARRA
                    if($part[1]=='linha')
                    {
                        $tipo[$idx]='linha';
                    }
                    else
                    {
                        $tipo[$idx]='barra';
                    }
                }
                else
                {
                    $tipo[$idx]='barra';
                }

                $idx++;
            }
            
            #echo "<PRE>".print_r($valores[0],true)."</PRE>"; exit;
            if((count($valores) > 0) and (count($valores[0]) > 0))
            {
                if($fl_inverte == "S")
                {
                    $nr_fim   = count($valores);
                    $nr_conta = 0;
                    while($nr_conta < $nr_fim)
                    {
                        $valores[$nr_conta] = array_reverse($valores[$nr_conta]);
                        $nr_conta++;
                    }
                }
                
                $rotulo = ($fl_inverte == "S" ? array_reverse($rotulo) : $rotulo);
                
                $grafico = group_barchart($rotulo,$valores,$tipo,$legenda,$nr_largura, $nr_altura,$ar_referencia);
            }
            else
            {
                $grafico = str_replace(base_url(),"",skin())."img/indicador_grafico_erro.png";
            }
        }
        elseif($row['cd_indicador_grafico_tipo'] == $INDICADOR_GRAFICO_LINHA)
        {
            $t = explode( ',', $row['ds_range_tick'] );
            $al = explode( ';', $row['ds_range_legenda'] );
            $av = explode( ';', $row['ds_range_valor'] );

            $tick = indicador_db::pegar_rotulos($cd_indicador_tabela, $t[0],$t[1], $t[2],$t[3]);

            foreach($al as $item)
            {
                $l = explode( ',', $item );
                $legendas[] = indicador_db::pegar_rotulos($cd_indicador_tabela, $l[0],$l[1], $l[2],$l[3]);
            }
            foreach( $legendas as $legs )
            {
                foreach( $legs as $leg )
                $legenda[] = $leg;
            }
            foreach($av as $item)
            {
                $v = explode( ',', $item );
                $valores[] = indicador_db::pegar_valores($cd_indicador_tabela, $v[0],$v[1], $v[2],$v[3]);
            }
            
            #$grafico = $nr_largura; 
            
            #echo "<PRE>".print_r($valores[0],true)."</PRE>"; exit;
            if((count($valores) > 0) and (count($valores[0]) > 0))
            {
                if($fl_inverte == "S")
                {
                    $nr_fim   = count($valores);
                    $nr_conta = 0;
                    while($nr_conta < $nr_fim)
                    {
                        $valores[$nr_conta] = array_reverse($valores[$nr_conta]);
                        $nr_conta++;
                    }
                }
                
                $tick = ($fl_inverte == "S" ? array_reverse($tick) : $tick);
                
                $grafico = linechart($tick, $valores, $legenda, $nr_largura, $nr_altura, $ar_referencia);
            }
            else
            {
                $grafico = str_replace(base_url(),"",skin())."img/indicador_grafico_erro.png";
            }               
        }
    }
    
    return $grafico;
}

function get_tabela_indicador($row, $ocultacao_colunas = FALSE, $ocultar_nome_linha_coluna = FALSE, $fl_ret_array = FALSE)
{
    $ob_ci = &get_instance();
    $ob_ci->load->helper('indicador');
    
    $indicador_parametro = $row['parametro'];

    if(count($indicador_parametro) > 0)
    {
    $ar_tabela = Array();

    $cd_indicador_tabela = $row['cd_indicador_tabela'];

    $alfa=array( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    $linhas  = intval($ob_ci->input->post('linhas'));
    $colunas = intval($ob_ci->input->post('colunas'));

    if($linhas == 0 && $colunas == 0 && $cd_indicador_tabela > 0)
    {
        if(intval($row['quantos'])>0)
        {
            $linhas  = intval($row['maior_linha'])+1;
            $colunas = intval($row['maior_coluna'])+1;
        }
    }

    $k = $linhas;

    $coluna_ocultar_collection = explode( ",", $row['ds_coluna_ocultar'] );

    $ds_coluna_ocultar = $row['ds_coluna_ocultar'];

    $m = '<table class="table table-striped table-bordered table_table indicador_table">';

    for($i=0;$i<$linhas;$i++)
    {
        if($i == 0)
        {
            $m .= '
                <thead>
                    <tr id="tr_$i">';
        }
        else
        {
            if($i == 1)
            {
                $m .= '<tbody>';
            }

            $m .= '<tr id="tr_$i" class="'.(($i % 2) == 0 ? 'tr_par' : 'tr_impar').'">';
        }
        
        $ar_valor = Array();

        for($j=0; $j<$colunas; $j++)
        {
            $r = $indicador_parametro[(intval($i) == 0 ? $i : $k)][$j];

            $ds_valor='';
            $ds_style='';

            if($r)
            {
                $ds_valor = $r['ds_valor'];
                $ds_style = $r['ds_style'];
            }

            $ds_style = str_replace("{URL_SKIN}",skin(),$ds_style);

            // *** formula
            $input_formula = "";
            $com_formula   = "";
            
            if(preg_match("/^=/", $ds_valor))
            {
                $ds_valor = indicador_tools::resultado_formula($cd_indicador_tabela,'',$j,$i);
            }

            $cl = $alfa[$j].''.$i;

            $fl_ocultar = false;
            if($ocultacao_colunas)
            {
                if( trim($ds_coluna_ocultar)=='' ||  ! in_array( $j, $coluna_ocultar_collection ) )
                {
                    $sty="";
                }
                else
                {
                    $sty="display:none;";
                    $fl_ocultar = true;
                }
            }
            else
            {
                $sty="";
            }

            if( $r && $r['fl_numero']=='S' )
            {
                if($ds_valor!="")
                {
                    $ds_valor = number_format( app_decimal_para_db($ds_valor), $r['nr_precisao'], ',', '.');
                }
            }

            if( $r && $r['fl_percentual']=='S' )
            {
                if($ds_valor!="")
                {
                    $ds_valor = $ds_valor.' %';
                }
            }
            
            $ds_valor = str_replace("{BASE_URL}",base_url(),$ds_valor);
            
            $m.="
                <td nowrap='nowrap' style='$sty $ds_style'>
                     ".(($ds_valor!="") ? nl2br(utf8_decode($ds_valor)) : nbsp())."
                </td>
                ";              
            
            
            if(!$fl_ocultar)
            {
                $ar_valor[] = $ds_valor;
            }
        }
        
        $k --;
        
        $ar_tabela[] = $ar_valor;
        $m.="</tr>";
    }

    if($i == 0)
    {
        $m .= '</thead>';
    }
    else if($i == 1)
    {
        $m .= '</tbody>';
    }

    $m .= '</table>';
    
    if($fl_ret_array)
    {
        return $ar_tabela;
    }
    else
    {
        return $m;
    }
    }
}
