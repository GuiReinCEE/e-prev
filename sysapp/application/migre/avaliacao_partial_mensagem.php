<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    if ( isset($_REQUEST['r']) )
    {
        $r = $_REQUEST['r'];
    }
    else
    {
        $r = $_POST['r'];
    }

    if ($h == 'CI')
    {
        $v_desc = 'Competência Institucional';
    }
    elseif ($h == 'CE')
    {
        $v_desc = 'Competência Específica';
    }
    elseif ($h == 'ES')
    {
        $v_desc = 'Escolaridade';
    }
    elseif ($h == 'RE')
    {
        $v_desc = 'Responsabilidades';
    }

    if ($r == 1)
    {
        $sql = " 
            SELECT nome_comp_inst, desc_comp_inst 
              FROM projetos.comp_inst 
             WHERE cd_comp_inst = " . (int)$h . " 
        ";
    }
    elseif ($r == 2)
    {
        $sql = "
            SELECT nome_comp_espec, desc_comp_espec 
              FROM projetos.comp_espec 
             WHERE cd_comp_espec = " . (int)$h . "
        ";
    }
    elseif ($r == 3)
    {
        $sql = "
            SELECT nome_escolaridade, desc_escolaridade 
              FROM projetos.escolaridade 
             WHERE cd_escolaridade = " . (int)$h . "
        ";
    }
    elseif ($r == 4)
    {
        $sql = "
            SELECT nome_responsabilidade, desc_responsabilidade 
              FROM projetos.responsabilidades 
             WHERE cd_responsabilidade = " . (int)$h . "
        ";
    }
    elseif ($r == 5)
    {
        $sql = "
            SELECT cd_escala, descricao 
              FROM projetos.escala_proficiencia 
             WHERE cd_origem = '" . $h . "' 
               AND dt_exclusao IS NULL 
          ORDER BY cd_escala
        ";
    }
    $rs = pg_query( $db, $sql );

    
    if (isset($v_desc)) {
        echo "<b>". $v_desc . "</b>";
	}
    echo( "<br /><br />" );

    while ( $reg = pg_fetch_row($rs) )
    {
        if($r==4)
    		echo "" . $reg[1] . "<br />";
    	else
    		echo "<b>".$reg[0]."</b> - " . $reg[1] . "<br />";
    		
    }
    
    echo( "<br /><br />" );

    pg_close( $db );
?>