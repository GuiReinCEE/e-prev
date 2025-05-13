<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   include_once('inc/nextval_sequence.php');
// -------------------------------------------------------
   $txt_dt_inclusao  	 = ( $dt_inclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_inclusao)."'" );
   $txt_dt_exclusao  	 = ( $dt_exclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_exclusao)."'" );
   $txt_dt_prevista  	 = ( $dt_prevista  		== '' ? 'Null' : "'".convdata_br_iso($dt_prevista)."'" );
   $txt_dt_legal  		 = ( $dt_legal  		== '' ? 'Null' : "'".convdata_br_iso($dt_legal)."'" );
   $txt_dt_implementacao = ( $dt_implementacao  == '' ? 'Null' : "'".convdata_br_iso($dt_implementacao)."'" );
   $conteudo             = str_replace('/controle_projetos/FCKeditor/editor/css/fck_editorarea.css','',$conteudo);
   
   
	if ($cd_site == 2) {
//		$txt_conteudo = str_replace('<br/>','',str_replace(chr(13),'',str_replace(chr(10),'',$conteudo))); 
//		$txt_conteudo = $conteudo; 
		$txt_ind_alt = str_replace(chr(10),'',$indice_alternativo); 
	}
	else {
//		$txt_conteudo = $conteudo; 
		$txt_ind_alt = $indice_alternativo; 
	}

//   echo 'FCKeditor1' . $FCKeditor1;
//   if ( version_compare( phpversion(), '4.1.0' ) == -1 )
    // prior to 4.1.0, use HTTP_POST_VARS
//    $postArray = &$HTTP_POST_VARS ;
//else
    // 4.1.0 or later, use $_POST
//    $postArray = &$_POST ;
//	foreach ( $postArray as $sForm => $value )
//{
//	$postedValue = htmlspecialchars( stripslashes( $value ) ) ;
//	echo $postedValue;
//}
   
   if ($insere=='I') {
   		$sql = "select max(cd_materia) as cd_materia from projetos.conteudo_site where cd_site = $cd_site and cd_versao = $cd_versao ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$cd_materia = ($reg['cd_materia'] + 1);
//   		$cd_materia = getNextval("projetos", "conteudo_site", "cd_materia", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO
		$sql =        " insert into projetos.conteudo_site ( ";
		$sql = $sql . "       	cd_materia, ";
		$sql = $sql . "       	cd_site, ";
		$sql = $sql . "       	cd_versao, ";
		$sql = $sql . "       	titulo, ";
		$sql = $sql . "       	conteudo, ";
		$sql = $sql . "       	dt_inclusao, ";
		$sql = $sql . "       	dt_alteracao, ";
		$sql = $sql . "       	dt_exclusao, ";
		$sql = $sql . "       	cd_usuario,	";
		$sql = $sql . "       	link1, ";
		$sql = $sql . "       	link2, ";
		$sql = $sql . "			link3, ";
		$sql = $sql . "			link4, ";
		$sql = $sql . "			cd_secao, ";
		$sql = $sql . "			item_menu, ";
		$sql = $sql . "			posicao_imagem, ";
		$sql = $sql . "			ordem, ";
		$sql = $sql . "			visao, ";
		$sql = $sql . "			indice_alternativo, ";
		$sql = $sql . "			alinhamento_imagem )	";
		$sql = $sql . " values (					";
		$sql = $sql . "			$cd_materia, ";
		$sql = $sql . "			$cd_site, ";
		$sql = $sql . "			$cd_versao, ";
		$sql = $sql . "			'$titulo', ";
		$sql = $sql . "			'$conteudo', ";
		$sql = $sql . "			current_timestamp, ";
		$sql = $sql . "			current_timestamp, ";
		$sql = $sql . "			$txt_dt_exclusao, ";
		$sql = $sql . "			$Z, ";
		$sql = $sql . "			'$link1', ";
		$sql = $sql . "			'$link2', ";
		$sql = $sql . "			'$link3', ";
		$sql = $sql . "			'$link4', ";
		$sql = $sql . "			'$cbo_secao', ";
		$sql = $sql . "			'$item_menu', ";
		$sql = $sql . "			'$opt_posicao', ";
		$sql = $sql . "			$ordem, ";
		$sql = $sql . "			'$visao', ";
		$sql = $sql . "			'$txt_ind_alt', ";
		$sql = $sql . "			'$opt_alinhamento' ) ";
   }
   else {
		$sql =        " update projetos.conteudo_site ";
		$sql = $sql . " set titulo = '$titulo', ";
		$sql = $sql . "     conteudo = '$conteudo', ";
		$sql = $sql . "     dt_alteracao = current_timestamp, ";	  
		$sql = $sql . "     dt_exclusao = $txt_dt_exclusao, ";
		$sql = $sql . "     cd_usuario = $Z, ";
		$sql = $sql . "     link1 = '$link1', ";
		$sql = $sql . "     link2 = '$link2', ";
		$sql = $sql . "		link3 = '$link3', ";
		$sql = $sql . "		link4 = '$link4', ";
		$sql = $sql . "		cd_secao = '$cbo_secao', ";
		$sql = $sql . "		cd_versao = $cd_versao, ";
		$sql = $sql . "		item_menu = '$item_menu', ";
		$sql = $sql . "		posicao_imagem = '$opt_posicao', ";
		$sql = $sql . "		ordem = $ordem, ";
		$sql = $sql . "		visao = '$visao', ";
		$sql = $sql . "		indice_alternativo = '$txt_ind_alt', ";
		$sql = $sql . "		alinhamento_imagem = '$opt_alinhamento' ";
		$sql = $sql . " where cd_materia = $cd_materia and cd_site = $cd_site and cd_versao = $cd_versao";
   }
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_conteudo_site.php?op=A&ed=' . $cd_versao . '&cs=' . $cd_site .  '&c='. $cd_materia );
	}
	else {
		pg_close($db);
		header('location: lst_conteudo_sites.php?msg=Ocorreu um erro ao tentar gravar este registro.');
	}
	
function convdata_br_iso($dt) {
      // Pressupѕe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL щ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hora = date("H:m:s");
      return $a.'-'.$m.'-'.$d.' '.$hora;
   }

?>