<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
//   echo 'i1: '.$chk_instancia_1.'<br>';
//   echo 'i2: '.$chk_instancia_2.'<br>';
//   echo 'i3: '.$chk_instancia_3.'<br>';
//   echo 'i4: '.$chk_instancia_4.'<br>';
//   echo 'i5: '.$chk_instancia_5.'<br>';
//   echo 'i6: '.$chk_instancia_6.'<br>';
// ------------------------------------------------------------
	if ($insere=='I') {
		$sql =        "insert into projetos.eventos ( ";
		$sql = $sql . "		cd_evento, ";
	    $sql = $sql . "		nome, ";	
		$sql = $sql . "		indic_email, ";
		$sql = $sql . "		indic_historico, ";
		$sql = $sql . "		dias_dt_referencia, ";
		$sql = $sql . "		cd_projeto, ";
		$sql = $sql . "		tipo, ";
		$sql = $sql . "		dt_referencia, ";
		$sql = $sql . "		email ";
	    $sql = $sql . " ) ";
    	$sql = $sql . " VALUES ( ";
	    $sql = $sql . "		$codigo, ";
    	$sql = $sql . "		'$nome', ";
	    $sql = $sql . "		'$chk_email',	";
		$sql = $sql . "		'$chk_historico', ";
		$sql = $sql . "		$dias,	";
    	$sql = $sql . "		$projeto, ";
		$sql = $sql . "		'$tipo_evento', ";
		$sql = $sql . "		'$data_referencia', ";
	    $sql = $sql . "    	'$texto_email'	";
    	$sql = $sql . ")";
	}
	else {
		$sql = 			" update projetos.eventos set ";
		$sql = $sql .	"	nome = '$nome', ";
		$sql = $sql .	"	indic_email	= '$chk_email', ";
		$sql = $sql . 	"	indic_historico	= '$chk_historico', ";
		$sql = $sql .	"	dias_dt_referencia = $dias,	";
		$sql = $sql .	"	cd_projeto = $projeto, ";
		$sql = $sql .	"	tipo = '$tipo_evento', ";
		$sql = $sql .	"	dt_referencia = '$data_referencia', ";
		$sql = $sql .	"	email = '$texto_email'	";
		$sql = $sql . 	"	where cd_evento	= $codigo ";	
	}
	if (pg_exec($db, $sql)) {
		if ($insere=='I') {
			$tpEmail = 'I'; 
            $sql =        " select max(cd_evento) as num ";
            $sql = $sql . " from   projetos.eventos ";
            $rs = pg_exec($db, $sql);
            $reg = pg_fetch_array($rs);
            $cod_evento = $reg['num'];
        }
//		$m = fnc_envia_email($cod_evento, $db, $tpEmail);
//------------------------ excluir todas as instancias deste evento...
		$sql = " delete from projetos.instancias_eventos where cd_evento = $codigo ";
//		echo $sql;
		$s = (pg_exec($db, $sql));
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_1);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_2);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_3);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_4);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_5);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_6);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_7);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_8);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_9);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_10);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_11);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_12);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_13);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_14);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_15);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_16);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_17);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_18);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_19);
		$m = fnc_grava_instancias($codigo, $db, $chk_instancia_20);
//------------------------ excluir todas as instancias secundárias deste evento...
		$sql = " delete from projetos.instancias_eventos_sec where cd_evento = $codigo ";
//		echo $sql;
		$s = (pg_exec($db, $sql));
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_1);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_2);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_3);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_4);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_5);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_6);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_7);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_8);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_9);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_10);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_11);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_12);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_13);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_14);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_15);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_16);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_17);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_18);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_19);
		$m = fnc_grava_instancias_sec($codigo, $db, $chk_instancia2_20);
// ------------------------------------------------------------
		pg_close($db);
		header('location: lst_eventos.php');
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar incluir este evento";
   }
// ------------------------------------------------------------
   function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
//-----------------------------------------------------------------------------------------------
function fnc_envia_email($num_atividade, $db, $tp) {
	$e = new Email();
	$e->IsHTML();
	$sql =        "  select a.numero, lts.descricao as tpsolic,  ltp.descricao as tipo, ";	// pj.nome as nomepj, garcia - 07/07/2004
	$sql = $sql . "           a.descricao as descati, u1.usuario as solicitante, u1.nome as nomesolic, ";
	$sql = $sql . "           u2.usuario as atendente, u2.nome as nomeatend, a.status_atual, ";
	$sql = $sql . "		u1.formato_mensagem as fmens_solic, u1.e_mail_alternativo as emailalt_solic, ";		// garcia - 30/03/2004
	$sql = $sql . "		u2.formato_mensagem as fmens_atend, u2.e_mail_alternativo as emailalt_atend, ";		// garcia - 30/03/2004
	$sql = $sql . "           lsa.descricao as situacao ";
	$sql = $sql . "    from   projetos.atividades a, ";
	$sql = $sql . "           projetos.usuarios_controledi u1, ";
	$sql = $sql . "           projetos.usuarios_controledi u2, ";
	$sql = $sql . "           public.listas ltp, ";
	$sql = $sql . "           public.listas lsa, ";
	$sql = $sql . "           public.listas lts ";
	$sql = $sql . "    where  u1.codigo = a.cod_solicitante ";
	$sql = $sql . "      and  u2.codigo = a.cod_atendente ";
	$sql = $sql . "      and  (ltp.codigo=a.tipo and ltp.categoria='TPAT') ";
	$sql = $sql . "      and  (lsa.codigo=a.status_atual and lsa.categoria='STAT') ";
	$sql = $sql . "      and  (lts.codigo=a.tipo_solicitacao and lts.categoria='TPMN') ";
	$sql = $sql . "   and  a.numero = $num_atividade ";
//	  echo $sql;
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
//-----------------------------------------------------------------------------------------------
    if ($reg['status_atual'] == 'CONC' || $reg['status_atual'] == 'APCS') {
		$msg = "<b>Prezada(o) ".$reg['nomesolic']."</b>";
		$e->SetSubject("A seguinte atividade foi concluída: nº $num_atividade");
		$img = 'tit_encerr_ativ.jpg';
		$formato_msg = $reg['fmens_solic'];
	}
	else {
		if ($reg['nomeatend'] == $reg['nomesolic']) {
			$msg = "<b>Prezada(o) ".$reg['nomeatend'];
			$formato_msg = $reg['fmens_solic'];
		}													// garcia - 30/03/2004
		else {												// garcia - 30/03/2004
			$msg = "<b>Prezadas(os) ".$reg['nomeatend']." e ".$reg['nomesolic']."</b>";		// garcia - 30/03/2004
			if (($reg['fmes_atend']) == ($reg['fmens_solic'])) {
				$formato_msg = $reg['fmens_solic'];			// garcia - 30/03/2004
			}
		}													// garcia - 30/03/2004
		$img = 'tit_alt_ativ.jpg';							// garcia - 30/03/2004
        $e->SetSubject("ATENÇÃO: A seguinte atividade NÃO está de acordo com a solicitação do usuário: $num_atividade");
	}
//-----------------------------------------------------------------------------------------------
//	  echo "solicitante : ".$reg['solicitante']." atendente :" .$reg['atendente']."@eletroceee.com.br";
	$e->SetFrom('Controle_de_Projetos');
	$msg = $msg . "<br><br>";
	$msg = $msg . "<table border=0>";
	if ($reg['status_atual'] == 'AINI') {
		$msg = $msg . "<tr><td colspan=2>Foi enviada uma solicitação de ". $reg['tpsolic']. "</td></tr>";
	}
	else {
		$msg = $msg . "<tr><td colspan=2>Alteração de status da atividade.</td></tr>";
	}
// ----------------- garcia - 20/11/2003      $e->AddTO($reg['solicitante']."@eletroceee.com.br");
		$e->AddTO($reg['atendente']."@eletroceee.com.br");
   		$e->AddTO($reg['solicitante']."@eletroceee.com.br");
		if (isset($reg['emailalt_solic'])) {					// garcia - 30/03/2004
	  		$e->AddTO($reg['emailalt_solic']);					// garcia - 30/03/2004	
		}
		if (isset($reg['emailalt_atend'])) {					// garcia - 30/03/2004
	  		$e->AddTO($reg['emailalt_atend']);					// garcia - 30/03/2004	
		}

// ---------------------------------------------------------- Área da mensagem HTML:
	if ($formato_msg == 'H') {
		$msg = "<table border='0' width='100%' cellspacing='0' cellpadding='0' background='http://www.e-prev.com.br/eletroceee/Imagens/img_fundo_nao_conf.jpg'>";
		$msg = $msg . "<tr>";
		$msg = $msg . "<td valign='top'><font size='2' face='Verdana'><img border='0' src='http://www.e-prev.com.br/eletroceee/Imagens/" . $img . "'></font></td>";
		$msg = $msg . "<td align='right'><font size='2' face='Verdana'><img border='0' src='http://www.e-prev.com.br/eletroceee/Imagens/img_logo_iso_2.jpg'></font></td>";
		$msg = $msg . "</tr>";
		$msg = $msg . "</table>";
		$msg = $msg . "<table border='0' width='100%' cellspacing='1' cellpadding='4'>";
		$msg = $msg . "<tr>";
// ----------------------------------------- responsável pelo processo
		$msg = $msg . "<td width='100%' colspan='2' bgcolor='#B8DEC7'><b><font size='2' face='Verdana'>Prezado(a) ".$reg['nom_ger']."</font></b></td>";
		$msg = $msg . "</tr>";
		$msg = $msg . "<tr>";
		if ($reg['status_atual'] == 'AINI') {
			$msg = $msg . "<td width='100%' colspan='2' bgcolor='#B8DEC7'><font size='2' face='Verdana'>Foi enviada uma solicitação de ". $reg['tpsolic']."</font></td>";
		}
		else {
			$msg = $msg . "<td width='100%' colspan='2' bgcolor='#B8DEC7'><font size='2' face='Verdana'>Alteração de status da atividade.</font></td>";
		}
		$msg = $msg . "</tr>";
// ------------------------------------------
		$msg = $msg . "<tr>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><b><font size='2' face='Verdana'>Solicitante:</font></b></td>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><font size='2' face='Verdana'>" . $reg['nomesolic'] . "</font></td>";
		$msg = $msg . "</tr>";
// ------------------------------------------
		$msg = $msg . "<tr>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><b><font size='2' face='Verdana'>Atendente:</font></b></td>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><font size='2' face='Verdana'>" . $reg['nomeatend'] . "</font></td>";
		$msg = $msg . "</tr>";
// ------------------------------------------
		$msg = $msg . "<tr>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><b><font size='2' face='Verdana'>Atividade:</font></b></td>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><a HREF='http://www.e-prev.com.br/controle_projetos/cad_atividade.php?n=".$reg['numero']."'><u><font COLOR='#0000ff' size='2' face='Verdana'>".$reg['numero']."</font></u></a></td>";
		$msg = $msg . "</tr>";
// ------------------------------------------
		$msg = $msg . "<tr>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><b><font size='2' face='Verdana'>Situação:</font></b></td>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><font size='2' face='Verdana'>" . $reg['situacao'] . "</font></td>";
		$msg = $msg . "</tr>";
// ----------------------------------------- descrição da atividade:
		$msg = $msg . "<tr>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><b><font size='2' face='Verdana'>Descrição:</font></b></td>";
		$msg = $msg . "<td bgcolor='#F0E0C7'><font size='2' face='Verdana'>" . str_replace("\r", "<br>", $reg['descati']) . "</font></td>";    
		$msg = $msg . "</tr>";
// ------------------------------------------		
		$msg = $msg . "<tr>";
		$msg = $msg . "<td colspan='2'>";
		$msg = $msg . "<p align='center'><font face='Verdana' size='1'>Mensagem enviada pela rede e-prev</font></td>";
		$msg = $msg . "</tr>";
		$msg = $msg . "</table>";
	}
	else	{
// ------------------------- Área da mensagem texto:
		$msg = $msg . "<tr><td colspan=2><hr></td></tr>";
		$msg = $msg . "<tr><td><b>Solicitante</b></td><td>" . $reg['nomesolic'] . "</td></tr>";
		$msg = $msg . "<tr><td><b>Atendente</b></td><td>" . $reg['nomeatend'] . "</td></tr>";
		$msg = $msg . "<tr><td><b>Atividade</b></td><td>" . $reg['numero'] . "</td></tr>";
		$msg = $msg . "<tr><td><b>Situação</b></td><td>" . $reg['situacao'] . $email_alt . "</td></tr>";
		$msg = $msg . "<tr><td colspan=2><hr></td></tr>";
		$msg = $msg . "<tr><td colspan=2><b>Descrição</b></td></tr>";
		$msg = $msg . "<tr><td colspan=2>" . str_replace("\r", "<br>", $reg['descati']) . "</td></tr>";
		$msg = $msg . "<tr><td colspan=2><hr></td></tr>";
		$msg = $msg . "</table>";
		$msg = $msg . "Esta mensagem foi enviada pela rede e-prev.";
	}
//	echo $msg;
	$e->SetBody($msg);
	return $e->Send();
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_instancias($num_evento, $db, $num_instancia) {
	if (isset($num_instancia)) {
		$sql = 			" insert into projetos.instancias_eventos (";
		$sql = $sql . 	" cd_instancia, cd_evento ";
	    $sql = $sql . 	" ) ";
    	$sql = $sql . 	" VALUES ( ";
		$sql = $sql . 	" $num_instancia, $num_evento ";
    	$sql = $sql . 	")";
//		echo $sql . '<br>';
		$s = (pg_exec($db, $sql));
	}
	return true;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_instancias_sec($num_evento, $db, $num_instancia) {
	if (isset($num_instancia)) {
		$sql = 			" insert into projetos.instancias_eventos_sec (";
		$sql = $sql . 	" cd_instancia, cd_evento ";
	    $sql = $sql . 	" ) ";
    	$sql = $sql . 	" VALUES ( ";
		$sql = $sql . 	" $num_instancia, $num_evento ";
    	$sql = $sql . 	")";
//		echo $sql . '<br>';
		$s = (pg_exec($db, $sql));
	}
	return true;
}
//-----------------------------------------------------------------------------------------------
?>