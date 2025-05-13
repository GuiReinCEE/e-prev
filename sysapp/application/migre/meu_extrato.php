<?php
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

	header('location:'.base_url().'index.php/servico/meu_extrato');

    include_once('inc/class.TemplatePower.inc.php');

    session_start();

    $tpl = new TemplatePower('tpl/tpl_meu_extrato.html');
    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $txt_dt_base = ( $dp == '' ? 'Null' : "'" . convdata_br_iso($dp) . "'" );

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $sql = " select cd_registro_empregado, indic_04 from projetos.usuarios_controledi where codigo='$Z' ";
    $rs = pg_exec($db, $sql);
    $reg = pg_fetch_array($rs);
    $emp = 9;
    $re  = $reg['cd_registro_empregado'];

    $tpl->newBlock('blk_conteudo_extrato');

    #### Verifica plano do participante ####
    $sql = "
		SELECT cd_plano
		FROM public.participantes
		WHERE cd_empresa            = ".$emp."
		AND cd_registro_empregado = ".$re."
		AND seq_dependencia       = 0
	";

    $rs  = pg_query($db,$sql);
    $reg = pg_fetch_array( $rs );
    $CD_PLANO = $reg['cd_plano'];
    
    // planos_patrocinadoras
    
    if ( ($reg['cd_plano'] == 2) )
    {
        #### BUSCA TIPO_PATROCINADORA ####
        $sql = "SELECT tipo_cliente AS tp_patrocinadora
                  FROM public.patrocinadoras
                 WHERE cd_empresa = ".$emp;
        $rs  = pg_query($db, $sql);
        $reg = pg_fetch_array($rs);
        $TP_PATROCINADORA = $reg['tp_patrocinadora'];

        #### BUSCA INDEXADOR ####
        $sql = "SELECT cd_indexador
                  FROM public.planos_patrocinadoras
                 WHERE cd_empresa = ".$emp."
                   AND cd_plano   = ".$CD_PLANO;
        $rs  = pg_query($db,$sql);
        $reg = pg_fetch_array($rs);
        $NR_INDEXADOR = $reg['cd_indexador'];   

        #### BUSCA EXTRATOS ####
        $sql = "
                SELECT ce.nro_extrato AS nr_extrato,
                       ce.ano AS nr_ano,
                       TO_CHAR(ce.data_base,'YYYY-MM-DD') AS dt_base_extrato
                  FROM public.controles_extratos ce
                 WHERE ce.cd_empresa   = " . $emp . "
                   AND ce.cd_plano     = " . $CD_PLANO . "
                   AND ce.dt_liberacao IS NOT NULL
                   AND ce.nro_extrato  IN (SELECT DISTINCT(ep.nro_extrato)
                                             FROM extrato_participantes ep
                                            WHERE ep.cd_empresa            = " . $emp . "
                                              AND ep.cd_registro_empregado = " . $re . "
                                              AND ep.seq_dependencia       = 0
                                              AND ep.cd_plano              = " . $CD_PLANO . ")
                 ORDER BY ce.nro_extrato DESC
                 ";
        $ob_resul = pg_query($db,$sql);
        $nr_ano = "";
        $cont = 0;
        while($ob_reg = pg_fetch_object($ob_resul))
        {   
            $cont = $cont + 1;                  
            if($nr_ano != $ob_reg->nr_ano)
            {
                $conteudo = str_replace('{ext_ano}', $ob_reg->nr_ano, $conteudo);
                $nr_ano = $ob_reg->nr_ano;
            }
            $NR_EXTRATO = $ob_reg->nr_extrato;
            $DT_BASE_EXTRATO = $ob_reg->dt_base_extrato;

            $tpl->newBlock("extrato");
            $tpl->assign("cd_registro_empregado", $re);
            $tpl->assign("cd_plano", $CD_PLANO);
            $tpl->assign("nr_extrato", $NR_EXTRATO);
            $tpl->assign("nr_indexador", $NR_INDEXADOR);
            $tpl->assign("tp_patrocinadora", $TP_PATROCINADORA);
            $tpl->assign("dt_base_extrato", $DT_BASE_EXTRATO);
        }
    }

    $tpl->printToScreen();
    pg_close($db);

    function convdata_br_iso($dt)
    {
        // Pressupѕe que a data esteja no formato DD/MM/AAAA
        // A melhor forma de gravar datas no PostgreSQL щ utilizando 
        // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
        // adequa a data a este formato
        $d = substr($dt, 0, 2);
        $m = substr($dt, 3, 2);
        $a = substr($dt, 6, 4);
        return $a . '-' . $m . '-' . $d;
    }
?>