<?php
class indicador_box_estilo
{
    static function listar_propriedades(&$param=array())
    {
        $tm=array();
        for($i=0;$i<sizeof($param);$i++)
        {
            $tm[]=trim(strtolower($param[$i][0]));
        }
        
        // parray($tm);

        if( !in_array( 'background-color', $tm ) ){ $param[]=array('background-color',''); }
        if( !in_array( 'text-align', $tm ) ){ $param[]=array('text-align',''); }
        if( !in_array( 'font-weight', $tm ) ){ $param[]=array('font-weight',''); }
        if( !in_array( 'font-size', $tm ) ){ $param[]=array('font-size',''); }

        return $param;
    }
    
    static function propriedade($v)
    {
        $param['background-color']='Cor de Fundo';
        $param['text-align']='Alinhamento';
        $param['font-weight']='Estilo da fonte';
        $param['font-size']='Tamanho da fonte';

        if( isset($param[trim($v)]) )
        {
            $v=$param[trim(strtolower($v))];
        }

        return $v;
    }

    static function opcoes($v,$sel='')
    {
        if( $v=='text-align' )
        {
            $a['']='';
            $a['right']='Alinhado a direita';
            $a['left']='Alinhado a esquerda';
            $a['center']='Alinhado ao centro';
            return form_dropdown($v, $a, array($sel), "class='estilo_objeto'");
        }
        elseif( $v=='font-weight' )
        {
            $a['']='';
            $a['normal']='Normal';
            $a['bold']='Negrito';
            return form_dropdown($v, $a, array($sel), "class='estilo_objeto'");
        }
        elseif( $v=='font-size' )
        {
            $a['']='';
            $a['10px']='10';
            $a['11px']='11';
            $a['12px']='12';
            $a['14px']='14';
            $a['16px']='16';
            $a['18px']='18';
            $a['20px']='20';
            return form_dropdown($v, $a, array($sel), "class='estilo_objeto'");
        }
        else
        {
            return '<input class="estilo_objeto" name="'.$v.'" style="width:100%;" value="'.$sel.'" />' ;
        }
    }
}

class indicador_tools
{
    public $nr_largura = 800;
    public $nr_altura  = 450;
    
    static function separar_celulas($formula)
    {
        // Solução para problema encontrado na expressão regular que no A10 encontrava A1
        // então primeiro separamos da formula todas linhas dezenas, depois as inferiores a 10
        // quando for necessário deve ser incluido bloco para centenas ou melhorada a expressão regular
        
        // *** letra + 2 digitos ( ex   A10 )
        $matches=array();
        $__formula=$formula;
        preg_match_all("/[a-zA-Z]{1,2}[0-9]{2}/", $__formula, $matches_2);
        foreach($matches_2[0] as $encontrado)
        {
            $matches[0][]=$encontrado;
            $__formula=str_replace($encontrado,'',$__formula);
        }
        // *** -------

        // *** letra + 1 digito ( ex   A1 )

        preg_match_all("/[a-zA-Z]{1,2}[0-9]{1}/", $__formula, $matches_1);
        foreach($matches_1[0] as $encontrado)
        {
            $matches[0][]=$encontrado;
            $__formula=str_replace($encontrado,'',$__formula);
        }
        
        return $matches;
    }
    
    // static function resultado_formula($cd_indicador, $celula='',$coluna=-1,$linha=-1)
    static function resultado_formula($cd_indicador_tabela, $celula='',$coluna=-1,$linha=-1)
    {
        $ci=&get_instance();
        $alfa=array( 'A'=>0,'B'=>1,'C'=>2,'D'=>3,'E'=>4,'F'=>5,'G'=>6,'H'=>7,'I'=>8,'J'=>9,'K'=>10,'L'=>11,'M'=>12,'N'=>13,'O'=>14,'P'=>15,'Q'=>16,'R'=>17,'S'=>18,'T'=>19,'U'=>20,'V'=>21,'W'=>22,'X'=>23,'Y'=>24,'Z'=>25 );

        if( $celula!='' )
        {
            $cel_f=strtoupper($celula);
            $nr_coluna=$alfa[preg_replace('/[.\0-9\-]/i','',$cel_f)];
            $nr_linha=preg_replace('/[.\a-zA-Z\-]/i','',$cel_f);
        }
        else
        {
            if( $linha>-1 && $coluna>-1 )
            {
                $nr_coluna=$coluna;
                $nr_linha=$linha;
            }
            else
            {
                echo "Função indicador_tools::resultado_formula() não possui os parametros mínimos para funcionar corretamente";
                return false;
            }
        }

        // *** RESGATA FÓRMULA
        $q=$ci->db->query("
            SELECT ip.ds_valor 
            FROM indicador.indicador_parametro ip 
            JOIN indicador.indicador_tabela it ON ip.cd_indicador_tabela=it.cd_indicador_tabela
            WHERE it.cd_indicador_tabela=? AND nr_coluna=? AND nr_linha=? AND ip.dt_exclusao IS NULL
        "
        , array( $cd_indicador_tabela, $nr_coluna, $nr_linha ) );
        $r=$q->row_array();
        $formula=$r['ds_valor'];

        $resultado='';

        if( trim($formula)!='' )
        {
            $impossivel_calcular=false;
            $referencia=array();

            $matches = indicador_tools::separar_celulas( $formula );

            if(is_array($matches))
            {
                foreach($matches[0] as $it)
                {
                    $ref=strtoupper($it);
                    $ref_linha=preg_replace('/[.\a-zA-Z\-]/i','',$ref);
                    $ref_coluna=strtoupper(preg_replace('/[.\0-9\-]/i','',$ref));
                    $ref_valor='';

                    $q=$ci->db->query("
                    SELECT ip.ds_valor 
                    FROM indicador.indicador_parametro ip 
                    JOIN indicador.indicador_tabela it ON ip.cd_indicador_tabela=it.cd_indicador_tabela
                    WHERE it.cd_indicador_tabela=? 
                    AND nr_coluna=? AND nr_linha=? AND ip.dt_exclusao IS NULL"
                    , array( $cd_indicador_tabela, $alfa[$ref_coluna], $ref_linha ) );
                    $r=$q->row_array();

                    if($r)
                    {
                        $ref_valor=$r['ds_valor'];
                        
                        if(preg_match("/^=/", $ref_valor))
                        {
                            $ref_valor=indicador_tools::resultado_formula($cd_indicador_tabela, '', $alfa[$ref_coluna], $ref_linha);
                        }
                        else
                        {
                            $ref_valor=str_replace( ".", "", $ref_valor );
                            $ref_valor=str_replace( ",", ".", $ref_valor );
                        }

                        if( !is_numeric($ref_valor) )
                        {
                            $impossivel_calcular=true;
                        }
                    }

                    $referencia[$ref]=$ref_valor;
                }
            }

            $formula_ = $formula;
            $formula_ = str_replace('=','',$formula_);
            foreach($referencia as $k=>$v)
            {
                $formula_ = str_replace( strtoupper($k), "$v::decimal", $formula_ );
                $formula_ = str_replace( strtolower($k), "$v::decimal", $formula_ );
            }
            if( !$impossivel_calcular )
            {
                $q = $ci->db->query( "SELECT  $formula_  AS formula_calculada" );

                if( $q )
                {
                    $r=$q->row_array();
                    $resultado=$r['formula_calculada'];
                }
            }
            else
            {
                $resultado="";
            }
        }

        return $resultado;
    }

    function gerar_grafico($cd_indicador_tabela)
    {
        $ob_ci = &get_instance();
        $ob_ci->load->plugin('pchart');
        
        $INDICADOR_GRAFICO_LINHA           = 1;
        $INDICADOR_GRAFICO_BARRA_ACUMULADO = 2;
        $INDICADOR_GRAFICO_BARRA_MULTIPLO  = 3;
        $INDICADOR_GRAFICO_PIZZA           = 4;     
        
        $qr_sql = "
                    SELECT g.* 
                      FROM indicador.indicador_tabela it 
                      JOIN indicador.indicador_tabela_grafico g 
                        ON g.cd_indicador_tabela = it.cd_indicador_tabela 
                     WHERE it.cd_indicador_tabela = ".intval($cd_indicador_tabela)." 
                       AND g.dt_exclusao          IS NULL       
                  ";
        
        $ob_resul = $ob_ci->db->query($qr_sql);
        $row = $ob_resul->row_array();

        $grafico='';
        if($row)
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
                    $grafico = piechart($rotulo,$valores,$legenda,$this->nr_largura, $this->nr_altura);
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
                #$grafico = $this->nr_largura; 
                
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
                    
                    $grafico = accumulate_barchart($rotulo, $valores, $tipo, $legenda, $this->nr_largura, $this->nr_altura,$ar_referencia);
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
                    
                    $grafico = group_barchart($rotulo,$valores,$tipo,$legenda,$this->nr_largura, $this->nr_altura,$ar_referencia);
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
                
                #$grafico = $this->nr_largura; 
                
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
                    
                    $grafico = linechart($tick, $valores, $legenda, $this->nr_largura, $this->nr_altura, $ar_referencia);
                }
                else
                {
                    $grafico = str_replace(base_url(),"",skin())."img/indicador_grafico_erro.png";
                }               
            }
        }
        
        return $grafico;
    }
    
    public static function gerar_tabela($cd_indicador_tabela, $ocultacao_colunas=false, $ocultar_nome_linha_coluna=false,$fl_ret_array=false)
    {
        $ob_ci = &get_instance();
        
        $ar_tabela = Array();

        $alfa=array( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        $linhas  = intval($ob_ci->input->post('linhas'));
        $colunas = intval($ob_ci->input->post('colunas'));

        if($linhas == 0 && $colunas == 0 && $cd_indicador_tabela > 0)
        {
            $qr_sql = "
                        SELECT MAX(nr_linha) AS maior_linha, 
                               MAX(nr_coluna) AS maior_coluna, 
                               COUNT(*) AS quantos 
                          FROM indicador.indicador_parametro 
                         WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)." 
                           AND dt_exclusao IS NULL
                      ";
            $ob_resul = $ob_ci->db->query($qr_sql);
            $ar_reg = $ob_resul->row_array();
            if(intval($ar_reg['quantos'])>0)
            {
                $linhas  = intval($ar_reg['maior_linha'])+1;
                $colunas = intval($ar_reg['maior_coluna'])+1;
            }
        }

        $k = $linhas;

        $qr_sql = " 
                    SELECT * 
                      FROM indicador.indicador_tabela 
                     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)."
                  ";
        $ob_resul = $ob_ci->db->query($qr_sql);
        $tabela = $ob_resul->row_array();

        $coluna_ocultar_collection = explode( ",", $tabela['ds_coluna_ocultar'] );

        #$m = "<table border='0' cellpadding='0' cellspacing='0' style='font-size:10px;font-family:verdana;border-style:solid;border-width:1px;background-color:white;width:150px;height:20;'>";
        $m = '<table class="sort-table" cellspacing="2" cellpadding="2" align="center" style="border: 1px solid #b5b5b5;">';
        
        if(!$ocultar_nome_linha_coluna)
        {
            $m.= "
                    <thead>
                    <tr>
                        <td></td>
                 ";
                 
            for($j=0;$j<$colunas;$j++)
            {
                if($ocultacao_colunas)
                {
                    if( trim($tabela['ds_coluna_ocultar'])=='' ||  !in_array( $j, $coluna_ocultar_collection ) )
                    {
                        $sty="";
                    }
                    else
                    {
                        $sty="display:none;";
                    }
                }
                else
                {
                    $sty="";
                }

                $m.="<td nowrap='nowrap' style='$sty'>$alfa[$j]</td>";              
            }
            $m.= "
                    </tr>
                    </thead>
                 ";
        }
        
        for($i=0;$i<$linhas;$i++)
        {
            $m.= "  
                    <tbody>
                    <tr id='tr_$i' onmousemove='$(\"#tr_$i\").attr(\"style\", \"background-color:#EEEEEE\");' onmouseout=' $(\"#tr_$i\").attr(\"style\", \"background-color:#FFFFFF\");'>
                 ";

            if(!$ocultar_nome_linha_coluna)
            {
                $m .= "<td nowrap='nowrap'>$i</td>";
            }
            
            $ar_valor = Array();
            for($j=0; $j<$colunas; $j++)
            {
                $qr_sql = "
                            SELECT * 
                              FROM indicador.indicador_parametro 
                             WHERE nr_linha            = ".(intval($i) == 0 ? $i : $k)." 
                               AND nr_coluna           = ".$j."
                               AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
                               AND dt_exclusao IS NULL
                          ";
                
                $ob_resul = $ob_ci->db->query($qr_sql);
                $r = $ob_resul->row_array();

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
                    if( trim($tabela['ds_coluna_ocultar'])=='' ||  ! in_array( $j, $coluna_ocultar_collection ) )
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
                
                /*
                $m.="
                    <td class='td_$j' 
                        nowrap='nowrap' 
                        onclick='escolher_celula_na_tabela(\"$cl\");' 
                        style='$sty ; border-bottom-style:solid;border-bottom-width:1px; $ds_style;'>
                        <div style='padding:5 10 5 10;margin:0;'>".(($ds_valor!="") ? $ds_valor : nbsp())."</div>
                    </td>
                    ";
                */
                $m.="
                    <td nowrap='nowrap' style='$sty $ds_style'>
                         ".(($ds_valor!="") ? $ds_valor : nbsp())."
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
        $m.="
                </tbody>
                </table>
            ";
        
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

class indicador_db
{
    static function pegar_valores($cd_indicador_tabela,$nr_coluna_inicio,$nr_coluna_fim,$nr_linha_inicio,$nr_linha_fim)
    {
        $ob_ci = &get_instance();
        
        $qr_sql = "
                    SELECT ip.ds_valor, 
                           ip.nr_coluna, 
                           ip.nr_linha
                      FROM indicador.indicador_parametro ip 
                      JOIN indicador.indicador_tabela it 
                        ON it.cd_indicador_tabela=ip.cd_indicador_tabela
                     WHERE ip.dt_exclusao         IS NULL
                       AND it.cd_indicador_tabela = ".intval($cd_indicador_tabela)."
                       AND ip.nr_coluna           BETWEEN ".intval($nr_coluna_inicio)." AND ".intval($nr_coluna_fim)."
                       AND ip.nr_linha            BETWEEN ".intval($nr_linha_inicio)." AND ".intval($nr_linha_fim)."
                     ORDER BY nr_linha, 
                              nr_coluna
                  ";
        $ob_resul = $ob_ci->db->query($qr_sql);
        $ar_reg = $ob_resul->result_array();
        $ar_retorno = Array();
        foreach($ar_reg as $row)
        {
            if(preg_match("/^=/", $row['ds_valor']))
            {
                $ar_retorno[] = indicador_tools::resultado_formula($cd_indicador_tabela,'', $row['nr_coluna'], $row['nr_linha']);
            }
            else
            {
                $ar_retorno[] = str_replace( ',', '.', str_replace( '.', '', $row['ds_valor'] ) );
            }
        }
        return $ar_retorno;
    }

    static function pegar_rotulos($cd_indicador_tabela,$nr_coluna_inicio,$nr_coluna_fim,$nr_linha_inicio,$nr_linha_fim)
    {
        $ob_ci = &get_instance();
        
        $qr_sql = "
                    SELECT ip.ds_valor, 
                           ip.nr_coluna, 
                           ip.nr_linha
                      FROM indicador.indicador_parametro ip 
                      JOIN indicador.indicador_tabela it 
                        ON it.cd_indicador_tabela = ip.cd_indicador_tabela
                     WHERE ip.dt_exclusao         IS NULL
                       AND it.cd_indicador_tabela = ".intval($cd_indicador_tabela)."
                       AND ip.nr_coluna           BETWEEN ".intval($nr_coluna_inicio)." AND ".intval($nr_coluna_fim)."
                       AND ip.nr_linha            BETWEEN ".intval($nr_linha_inicio)." AND ".intval($nr_linha_fim)."
                     ORDER BY nr_linha, 
                              nr_coluna
                  ";
        #echo "<PRE>".$sql."</PRE>"; #exit;
        
        $ob_resul = $ob_ci->db->query($qr_sql);
        $ar_reg = $ob_resul->result_array();
        $ar_retorno = Array();
        foreach($ar_reg as $row)
        {
            $ar_retorno[] = $row['ds_valor'];
        }
        return $ar_retorno;
    }
    
    static function indicador_get_label($cd_indicador = 0)
    {
        $ci = &get_instance();
        
        $qr_sql = "
                    SELECT id_label, 
                           ds_label
                      FROM indicador.indicador_label
                     WHERE dt_exclusao IS NULL
                       AND cd_indicador = ".intval($cd_indicador)."
                  ";
        
        $ob_resul = $ci->db->query($qr_sql);
        $ar_reg = $ob_resul->result_array();        
        
        return $ar_reg;
    }       

    /**
     * @param $cd_indicador_tabela
     * @param $nr_coluna
     * @param $nr_linha
     * @param $ds_valor
     * @param $ds_style                 valores padronizados separados por vírgula "[background][,right|left|center]"
     */
    static function sql_inserir_celula($cd_indicador_tabela, 
                                       $nr_coluna, 
                                       $nr_linha, 
                                       $ds_valor, 
                                       $ds_style='', 
                                       $fl_numero='N', 
                                       $nr_precisao=0, 
                                       $fl_percentual='N')
    {
        $ar_style = explode(',', $ds_style);

        $style = "";
        $style.= ((in_array('background',$ar_style)) ? "background:url({URL_SKIN}img/indicador_tabela_back.gif); font-weight: bold; font-size:10;" : "");
        $style.= ((in_array('right',$ar_style)) ? "text-align:right;" : "");
        $style.= ((in_array('center',$ar_style)) ? "text-align:center;" : "");
        $style.= ((in_array('left',$ar_style)) ? "text-align:left;" : "");

        $qr_sql = "
                    INSERT INTO indicador.indicador_parametro 
                         (
                           cd_indicador_tabela,
                           nr_coluna,
                           nr_linha,
                           ds_valor,
                           ds_style,
                           fl_numero,
                           nr_precisao,
                           fl_percentual
                         ) 
                    VALUES 
                         (
                           ".intval($cd_indicador_tabela).",
                           ".intval($nr_coluna).",
                           ".intval($nr_linha).",
                           '".trim(utf8_decode($ds_valor))."',
                           '".trim($style)."',
                           '".trim($fl_numero)."',
                           ".intval($nr_precisao).",
                           '".trim($fl_percentual)."'
                         );
                  ";
        return $qr_sql;
    }

    static function sql_inserir_grafico($cd_indicador_tabela, 
                                        $tipo_grafico, 
                                        $ds_range_legenda, 
                                        $ds_range_tick, 
                                        $ds_range_valor, 
                                        $usuario_id, 
                                        $coluna_ocultar='',
                                        $nr_pos_meta='-1',
                                        $nr_pos_tendendencia=-1,
                                        $nr_pos_referencia=-1,
                                        $fl_inverte="N"
                                        )
    {
        // gerar gráfico
        $qr_sql = " 
                    DELETE FROM indicador.indicador_tabela_grafico itg 
                     WHERE itg.cd_indicador_tabela = ".intval($cd_indicador_tabela)."; 

                    INSERT INTO indicador.indicador_tabela_grafico
                         (
                           cd_indicador_tabela,
                           cd_indicador_grafico_tipo,
                           ds_range_legenda,
                           ds_range_tick,
                           ds_range_valor,
                           nr_pos_meta,
                           nr_pos_tendendencia,
                           nr_pos_referencia,
                           fl_inverte,
                           dt_inclusao,
                           cd_usuario_inclusao
                         ) 
                    VALUES 
                         ( 
                           ".intval($cd_indicador_tabela).",
                           ".intval($tipo_grafico).",
                           '".trim($ds_range_legenda)."',
                           '".trim($ds_range_tick)."',
                           '".trim($ds_range_valor)."',
                           '".trim($nr_pos_meta)."',
                           ".intval($nr_pos_tendendencia).",
                           ".intval($nr_pos_referencia).",
                           '".$fl_inverte."',
                           CURRENT_TIMESTAMP,
                           ".intval($usuario_id)."
                         ); 
        
                    UPDATE indicador.indicador_tabela 
                       SET ds_coluna_ocultar = '".trim($coluna_ocultar)."'
                     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)."; 
                  ";
        return $qr_sql; 
    }

    static function abrir_periodo_para_indicador($cd_indicador,$cd_usuario_logado)
    {
        // VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
        $tabela = indicador_tabela_aberta(intval($cd_indicador));
        $ci = &get_instance();

        $periodo_aberto = array();
        if(count($tabela) == 0)
        {
            $qr_sql = "
                        SELECT *
                          FROM indicador.indicador_periodo 
                         WHERE dt_exclusao IS NULL 
                           AND CURRENT_TIMESTAMP BETWEEN dt_inicio AND dt_fim
                           AND NOT EXISTS(SELECT ip.*
                                            FROM indicador.indicador as i 
                                            JOIN indicador.indicador_tabela as it ON i.cd_indicador=it.cd_indicador
                                            JOIN indicador.indicador_periodo as ip ON ip.cd_indicador_periodo=it.cd_indicador_periodo
                                           WHERE it.dt_exclusao IS NULL
                                             AND it.cd_indicador = ".intval($cd_indicador)."
                                             AND it.dt_fechamento_periodo IS NOT NULL
                                             AND ip.cd_indicador_periodo=indicador.indicador_periodo.cd_indicador_periodo)
                         ORDER BY nr_ano_referencia ASC         
                      ";
            #echo "<PRE>$qr_sql</PRE>"; exit;
            $query = $ci->db->query($qr_sql);
            
            $periodo_aberto = $query->result_array();
            
            // SE EXISTE UM OU MAIS PERÍODOS ABERTOS QUE AINDA NÃO FORAM FECHADOS PARA TABELA
            if(count($periodo_aberto) > 0)
            {
                $qr_sql = "
                            SELECT * 
                              FROM indicador.indicador 
                             WHERE cd_indicador = ".intval($cd_indicador)."
                          ";
                $q = $ci->db->query($qr_sql);
                $r = $q->row_array();

                #### INSERE O PERIODO MAIS ANTIGO ABERTO ####
                $qr_sql = "
                            INSERT INTO indicador.indicador_tabela
                                 (
                                   cd_indicador,
                                   ds_indicador_tabela,
                                   cd_processo,
								   cd_indicador_grupo,
								   cd_tipo,
                                   dt_inclusao,
                                   cd_usuario_inclusao,
                                   cd_indicador_periodo
                                 )
                            VALUES 
                                 (
                                   ".intval($cd_indicador).",
                                   '".$r['ds_indicador']."',
                                   ".intval($r['cd_processo']).",
                                   ".intval($r['cd_indicador_grupo']).",
                                   '".trim($r['cd_tipo'])."',
                                   CURRENT_TIMESTAMP,
                                   ".intval($cd_usuario_logado).",
                                   ".$periodo_aberto[0]['cd_indicador_periodo']."
                                 );             
                          ";
                #echo "<PRE>$qr_sql</PRE>"; exit;
                $ci->db->query($qr_sql);
                
                return true;
            }
            else
            {
                echo("NÃO HÁ NENHUM PERÍODO ABERTO PARA DATA DE HOJE. (indicador_helper.php => abrir_periodo_para_indicador)");
                exit;
            }
        }
        else
        {
            return false;
        }
    }

    static function fechar_periodo_para_indicador($cd_indicador_tabela, $cd_usuario)
    {
        // indicar que o período foi fechado para o indicador_tabela
        $qr_sql = "
					UPDATE indicador.indicador_tabela 
					   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
					       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
				     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela)."; 
				  ";
        return $qr_sql;
    }

    static function verificar_permissao($cd_usuario, $gerencia)
    {
        $ci = &get_instance();
        
        #### VERIFICA ADMINISTRADOR ####
        $qr_sql = "
                    SELECT COUNT(*) AS fl_permite
                      FROM indicador.indicador_administrador ia 
                     WHERE ia.cd_usuario  = ".intval($cd_usuario)." 
                       AND UPPER(ds_tipo) = 'ADMINISTRADOR'
                  ";
        $ob_resul = $ci->db->query($qr_sql);
        $ar_reg = $ob_resul->row_array();   
        
        if(count($ar_reg) > 0)
        {
            if(intval($ar_reg['fl_permite']) > 0)
            {
                return true;
            }
        }

        #### VERIFICA RESPONSAVEL ####
        $qr_sql = "
                    SELECT COUNT(*) AS fl_permite
                      FROM indicador.indicador_administrador ia 
                      JOIN projetos.usuarios_controledi uc 
                        ON uc.codigo = ia.cd_usuario
                     WHERE ia.cd_usuario  = ".intval($cd_usuario)." 
                       AND UPPER(ds_tipo) = 'RESPONSAVEL'
                  ";
				  
				  
				
        $ob_resul = $ci->db->query($qr_sql);
        $ar_reg = $ob_resul->row_array();

        if(count($ar_reg) > 0)
        {
            if(intval($ar_reg['fl_permite']) > 0)
            {
                if(gerencia_in(array($gerencia)))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                #exibir_mensagem('ACESSO NÃO PERMITIDO');
                return false;
            }
        }
        else
        {
            #exibir_mensagem('ACESSO NÃO PERMITIDO');
            return false;
        }
    }

}

// ----------------------------------------------------------------------------------------------------

/**
 * Retorna array com períodos abertos para configuração de indicadores.
 */
function indicador_periodo_aberto()
{
    $ci=&get_instance();
    $return = array();

    $query = $ci->db->query( " SELECT * FROM indicador.indicador_periodo WHERE dt_exclusao IS NULL AND current_timestamp BETWEEN dt_inicio AND dt_fim " );

    if( $query )
    {
        $return = $query->result_array();
    }

    return $return;
}

function indicador_tabela_aberta( $cd_indicador )
{
    $ci=&get_instance();

    $ci->load->model( 'projetos/Indicador_model', 'dbm' );

    $return = $ci->dbm->listar_indicador_tabela_aberta_de_indicador( intval($cd_indicador) );

    #echo "<PRE>".print_r($return,true)."</PRE>"; exit;
    
    return $return;
}

/** perform regression analysis on the input data, make the trend line y=ax+b
* @author Son Nguyen
* @since 11/18/2005
* @package Framework
* @subpackage Math
*/ 
class CRegressionLinear 
{
    /*
    * perform regression analysis on the input data, make the trend line y=ax+b
    * http://blog.trungson.com/2005/11/linear-regression-php-class.html
    */
    private $mDatas; // input data, array of (x1,y1);(x2,y2);... pairs, or could just be a time-series (x1,x2,x3,...)
    private $sStyle;

    /** constructor */
    function __construct($pDatas)
    {
        $this->mDatas = $pDatas;
    }

    /** compute the coeff, equation source: http://people.hofstra.edu/faculty/Stefan_Waner/RealWorld/calctopic1/regression.html */
    function calculate()
    {
        $n = count($this->mDatas);
        $vSumXX = $vSumXY = $vSumX = $vSumY = 0;
        foreach ($this->mDatas AS $vCnt => $vOne) 
        {
            $x = $vCnt; $y = $vOne;
            $vSumXY += $x*$y;
            $vSumXX += $x*$x;
            $vSumX += $x;
            $vSumY += $y;
            $vCnt++;
        } // rof
        $vTop = ($n*$vSumXY - $vSumX*$vSumY);
        $vBottom = ($n*$vSumXX - $vSumX*$vSumX);
        $a = $vBottom!=0?$vTop/$vBottom:0;
        $b = ($vSumY - $a*$vSumX)/$n;
        return array($a,$b);
    }

    /**
     * given x, return the prediction y
     */
    function predict($x)
    {
        list($a, $b) = $this->calculate();
        $y = ($a*$x)+$b;            // straight line

        return $y;
    }

} // function CRegressionLinear

function calcular_tendencia_logaritmica($Y)
{
    /* Now, here's how to use the equations given to estimate $a and $b. */

    if( sizeof($Y)>1 )
    {

        for($i=0;$i<sizeof($Y);$i++)
        {
            $X[]=$i+1;
        }

        // Now convert to log-scale for X
        $logX = array_map('log', $X);

        // Now estimate $a and $b using equations from Math World
        $n = count($X);
        $square = create_function('$x', 'return pow($x,2);');
        $x_squared = array_sum(array_map($square, $logX));
        $y_squared = array_sum(array_map($square, $Y));
        $xy = array_sum(array_map(create_function('$x,$y', 'return $x*$y;'), $logX, $Y));

        $bFit = ($n * $xy - array_sum($Y) * array_sum($logX)) /
          ($n * $x_squared - pow(array_sum($logX), 2));

        $aFit = (array_sum($Y) - $bFit * array_sum($logX)) / $n;

        $Yfit = array();
        foreach($X as $x){
            $Yfit[] = $aFit + $bFit * log($x);
        }

        return array( $aFit, $bFit, $Yfit );
    }
    else
    {
        return array(0,0,0);
    }
}
?>