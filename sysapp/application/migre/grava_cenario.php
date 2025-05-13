<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   $txt_dt_inclusao  		= ( $dt_inclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_inclusao)."'" );
   $txt_dt_exclusao  		= ( $dt_exclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_exclusao)."'" );
   $txt_dt_prevista  		= ( $dt_prevista  		== '' ? 'Null' : "'".convdata_br_iso($dt_prevista)."'" );
   $txt_dt_legal  			= ( $dt_legal  			== '' ? 'Null' : "'".convdata_br_iso($dt_legal)."'" );
   $txt_dt_implementacao  	= ( $dt_implementacao  	== '' ? 'Null' : "'".convdata_br_iso($dt_implementacao)."'" );
//   $conteudo = unhtmlentities(strip_tags($conteudo));
   if ($insere=='I') {
		$sql =        " insert into projetos.cenario ( ";
		$sql = $sql . "       	cd_cenario, ";
		$sql = $sql . "       	titulo, ";
		$sql = $sql . "       	conteudo, ";
		$sql = $sql . "       	dt_inclusao, ";
		$sql = $sql . "       	dt_exclusao, ";
		$sql = $sql . "       	cd_usuario,	";
		$sql = $sql . "			referencia, ";
		$sql = $sql . "			fonte, ";
		$sql = $sql . "			dt_prevista, ";
		$sql = $sql . "			dt_legal, ";
		$sql = $sql . "			dt_implementacao, ";
		$sql = $sql . "			pertinencia, ";
		$sql = $sql . "       	link1, ";
		$sql = $sql . "       	link2, ";
		$sql = $sql . "			link3, ";
		$sql = $sql . "			link4, ";
		$sql = $sql . "			cd_secao, ";
		$sql = $sql . "			cd_edicao,		";
		$sql = $sql . "			indic_aa, ";
		$sql = $sql . "			indic_acs, ";
		$sql = $sql . "			indic_aj, ";
		$sql = $sql . "			indic_da, ";
		$sql = $sql . "			indic_dap, ";
		$sql = $sql . "			indic_db, ";
		$sql = $sql . "			indic_dcg, ";
		$sql = $sql . "			indic_df, ";
		$sql = $sql . "			indic_di, ";
		$sql = $sql . "			indic_die, ";
		$sql = $sql . "			indic_din, ";
		$sql = $sql . "			indic_drh, ";
		$sql = $sql . "			indic_sg ) ";		
		$sql = $sql . " values (					";
		$sql = $sql . "			$cd_cenario, ";
		$sql = $sql . "			'$titulo', ";
		$sql = $sql . "			'$conteudo', ";
		$sql = $sql . "			$txt_dt_inclusao, ";
		$sql = $sql . "			$txt_dt_exclusao, ";
		$sql = $sql . "			$Z, ";
		$sql = $sql . "			'$referencia', ";
		$sql = $sql . "			'$fonte', ";
		$sql = $sql . "			$txt_dt_prevista, ";
		$sql = $sql . "			$txt_dt_legal, ";
		$sql = $sql . "			$txt_dt_implementacao, ";
		$sql = $sql . "			'$pert', ";
		$sql = $sql . "			'$link1', ";
		$sql = $sql . "			'$link2', ";
		$sql = $sql . "			'$link3', ";
		$sql = $sql . "			'$link4', ";
		$sql = $sql . "			'$cbo_secao', ";
		$sql = $sql . "			$cd_edicao, ";
		$sql = $sql . "			'$AA', ";
		$sql = $sql . "			'$ACS', ";
		$sql = $sql . "			'$AJ', ";
		$sql = $sql . "			'$DA', ";
		$sql = $sql . "			'$DAP', ";
		$sql = $sql . "			'$DB', ";
		$sql = $sql . "			'$DCG', ";
		$sql = $sql . "			'$DF', ";
		$sql = $sql . "			'$DI', ";
		$sql = $sql . "			'$DIE', ";
		$sql = $sql . "			'$DIN', ";
		$sql = $sql . "			'$DRH', ";
		$sql = $sql . "			'$SG' ) ;";		
   }
   else {
		$sql =        " update projetos.cenario ";
		$sql = $sql . " set titulo = '$titulo', ";
		$sql = $sql . "     conteudo = '$conteudo', ";
		$sql = $sql . "     dt_inclusao = $txt_dt_inclusao, ";	  
		$sql = $sql . "     dt_exclusao = $txt_dt_exclusao, ";
		$sql = $sql . "     cd_usuario = $Z, ";
		$sql = $sql . "		referencia = '$referencia', ";
		$sql = $sql . "		fonte = '$fonte', ";
		$sql = $sql . "		area_indicada = '$cbo_area', ";
		$sql = $sql . "     dt_prevista = $txt_dt_prevista, ";
		$sql = $sql . "     dt_legal = $txt_dt_legal, ";
		$sql = $sql . "     dt_implementacao = $txt_dt_implementacao, ";
		$sql = $sql . "		pertinencia = '$pert', ";
		$sql = $sql . "     link1 = '$link1', ";
		$sql = $sql . "     link2 = '$link2', ";
		$sql = $sql . "		link3 = '$link3', ";
		$sql = $sql . "		link4 = '$link4', ";
		$sql = $sql . "		cd_secao = '$cbo_secao', ";
		$sql = $sql . "		cd_edicao = $cd_edicao, ";
		$sql = $sql . "		indic_aa = '$AA', ";
		$sql = $sql . "		indic_acs = '$ACS', ";
		$sql = $sql . "		indic_aj = '$AJ', ";
		$sql = $sql . "		indic_da = '$DA', ";
		$sql = $sql . "		indic_dap = '$DAP', ";
		$sql = $sql . "		indic_db = '$DB', ";
		$sql = $sql . "		indic_dcg = '$DCG', ";
		$sql = $sql . "		indic_df = '$DF', ";
		$sql = $sql . "		indic_di = '$DI', ";
		$sql = $sql . "		indic_die = '$DIE', ";
		$sql = $sql . "		indic_din = '$DIN', ";
		$sql = $sql . "		indic_drh = '$DRH', ";
		$sql = $sql . "		indic_sg = '$SG'  ";
		$sql = $sql . " where cd_cenario = $cd_cenario; ";
   }
   
	if ($AA == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GA' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GA');
			   ";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GA' AND dt_exclusao IS NULL;
			   ";	
	}
	
		
	if ($ACS == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GRI' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GRI');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GRI' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($AJ == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GJ' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GJ');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GJ' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if (($DA == "S") or ($DRH == "S"))
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GAD' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GAD');
				";
	}
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GAD' AND dt_exclusao IS NULL;
			   ";	
	}	
	
	if ($DAP == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GAP' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GAP');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GAP' AND dt_exclusao IS NULL;
			   ";	
	}				
				
	if ($DB == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GB' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GB');
				";
	}
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GB' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($DCG == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GC' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GC');
				";
	    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GC' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($DF == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GF' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GF');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GF' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($DI == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GI' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GI');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GI' AND dt_exclusao IS NULL;
			   ";	
	}							
							
	if ($DIE == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'DE' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'DE');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'DE' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($DIN == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GIN' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'GIN');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'GIN' AND dt_exclusao IS NULL;
			   ";	
	}
	
	if ($SG == "S")
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'SG' AND dt_exclusao IS NULL;
				INSERT INTO projetos.cenario_areas(cd_cenario, cd_divisao) VALUES (".$cd_cenario.",'SG');
				";
    }
	else
	{
		$sql.= "
				UPDATE projetos.cenario_areas SET dt_exclusao = CURRENT_TIMESTAMP WHERE cd_cenario = ".$cd_cenario." AND cd_divisao = 'SG' AND dt_exclusao IS NULL;
			   ";	
	}				
   
	#echo "<PRE>"; echo $sql; exit;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_cenario.php?ed=' . $cd_edicao);
	}
	else {
		pg_close($db);
		header('location: lst_cenario.php?msg=Ocorreu um erro ao tentar gravar este registro.');
	}
	
function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hora = date("H:m:s");
      return $a.'-'.$m.'-'.$d.' '.$hora;
   }
function theRealStripTags2($string)
{

    $tam=strlen($string);
    // tam have number of cars the string

    $newstring="";
    // newstring will be returned

    $tag=0;
    /* tag = 0 => copy car from string to newstring
       tag > 0 => don't copy. Find one or mor tag '<' and
          need to find '>'. If we find 3 '<' need to find
          all 3 '>'
    */

    /* I am C programm. seek in a string is natural for me
        and more efficient

        Problem: copy a string to another string is more
        efficient but use more memory!!!
    */
    for ($i=0; $i < $tam; $i++){

        /* If I find one '<', $tag++ and continue whithout copy*/
        if ($string{$i} == '<'){
            $tag++;
            continue;
        }

        /* if I find '>', decrease $tag and continue */
        if ($string{$i} == '>'){
            if ($tag){
                $tag--;
            }
        /* $tag never be negative. If string is "<b>test</b>>" (error, of course)
            $tag stop in 0
        */
            continue;
        }

        /* if $tag is 0, can copy */
        if ($tag == 0){
            $newstring .= $string{$i}; // simple copy, only car
        }
    }
        return $newstring;
}
function unhtmlentities ($string) {
   $trans_tbl1 = get_html_translation_table (HTML_ENTITIES);
   foreach ( $trans_tbl1 as $ascii => $htmlentitie ) {
        $trans_tbl2[$ascii] = '&#'.ord($ascii).';';
   }
   $trans_tbl1 = array_flip ($trans_tbl1);
   $trans_tbl2 = array_flip ($trans_tbl2);
   return strtr (strtr ($string, $trans_tbl1), $trans_tbl2);
}

?>