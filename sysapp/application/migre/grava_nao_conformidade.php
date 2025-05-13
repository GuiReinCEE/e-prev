<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');

	$cd_responsavel = ($_POST['responsavel'] == '' ? '0' : $_POST['responsavel']);
	$cd_gerente = ($gerente == '' ? '0' : $gerente);

	if ($insere=='I') 
	{
		$sql = "INSERT INTO projetos.nao_conformidade 
		                  (
						    cd_processo,          	
							cd_nao_conformidade,	
							descricao,            	
							disposicao,			
							evidencias,			
							causa,                	
							dt_cadastro,          	
							cd_responsavel,       	
							aberto_por,       		
							numero_cad_nc			
						  )                        	
					 VALUES 
					      (                 	
							".$_POST['processo'].",   	
							".$_POST['cod_nao_conf'].", 	
							'".$_POST['descricao']."', 
							'".$_POST['disposicao']."', 
							'".$_POST['evidencias']."', 
							'".$_POST['causa']."', 
							TO_DATE('".$_POST['dt_cadastro']."','DD/MM/YYYY'),
							".$cd_responsavel.", 
							".$_POST['aberto_por'].",
							'".$_POST['numero_cad_nc']."'							
						  )";
	}
	else 
	{
		$sql = "UPDATE projetos.nao_conformidade 
		           SET cd_processo         = ".$_POST['processo'].",    
			           descricao           = '".$_POST['descricao']."',     	  
			           disposicao          = '".$_POST['disposicao']."',  	
			           evidencias		   = '".$_POST['evidencias']."',		
			           causa               = '".$_POST['causa']."',          
			           dt_cadastro         = TO_DATE('".$_POST['dt_cadastro']."','DD/MM/YYYY'),  	
			           cd_responsavel      = ".$cd_responsavel.", 
			           cd_gerente          = ".$cd_gerente.",  		
			           aberto_por		   = ".$_POST['aberto_por'].", 		
			           numero_cad_nc	   = '".$_POST['numero_cad_nc']."' 
		         WHERE cd_nao_conformidade = ".$_POST['cod_nao_conf'];
	}
	
	
	if (pg_exec($db, $sql)) 
	{
		if ($insere=='I') 
		{
			$tpEmail = 'I'; 
			$sql = "SELECT MAX(cd_nao_conformidade) as cd_nao_conformidade 
			          FROM projetos.nao_conformidade 
			         WHERE cd_processo = ".$_POST['processo'];
			$rs  = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			$cd_nao_conf = $reg['cd_nao_conformidade'];
			$m = fnc_envia_email($cd_nao_conf, $db, $tpEmail);	
		}		
		else
		{
			$cd_nao_conf = $_POST['cod_nao_conf'];
		}
		pg_close($db);
		
		//header('location: lst_nao_conf.php');
		header("location: cad_nao_conformidade.php?c=".$cd_nao_conf."&tr=U&msg=0");
	}
	else 
	{
		pg_close($db);
		echo "Ocorreu um erro ao tentar incluir esta nгo conformidade";
	}
 

#################################################### ENVIA EMAIL #####################################################
  
//--------------------------------- Envio de e-mails quando do cadastro de uma nova nгo-conformidade: (Garcia - 22/03/2004)
	function fnc_envia_email($num_nao_conf, $db, $tp) {	
//-------------------------------------------------------------------------- Busca Dados de email de abertura da NC
		$sql =        "select   cd_evento, nome, email ";
		$sql = $sql . "from 	projetos.eventos ";
		$sql = $sql . "where 	dt_referencia = 'DANC' and ";
		$sql = $sql . "			tipo = 'E'  and ";
		$sql = $sql . "			indic_email = 'S' ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$v_assunto =  $reg['nome'];
		$v_texto = $reg['email'];
		$cd_evento = $reg['cd_evento'];
//-------------------------------------------------------------------------- Determina as instвncias envolvidas neste evento
		$sql =        "select   tipo_instancia ";
		$sql = $sql . "from 	projetos.instancias_eventos ie, projetos.instancias i ";
		$sql = $sql . "where 	ie.cd_evento = $cd_evento ";
		$sql = $sql . " and		ie.cd_instancia = i.cd_instancia ";
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs))
   		{
			$instancia =  $reg['tipo_instancia'];
		}
//--------------------------------------------------------------------------
		$e = new Email();
		$e->IsHTML();
		$sql =        "select   nc.cd_processo, nc.cd_nao_conformidade, nc.descricao, nc.evidencias, ";
		$sql = $sql . "			pr.cod_responsavel, pr.envolvidos, pr.procedimento, ";
		$sql = $sql . "			nc.numero_cad_nc, ";
		$sql = $sql . "			div.nome, div.cod_gerente, ucdi.nome as nom_ger, ucdi.usuario ";
		$sql = $sql . "from 	projetos.nao_conformidade nc, projetos.processos pr, ";	
		$sql = $sql . "			projetos.divisoes div, projetos.usuarios_controledi ucdi ";
		$sql = $sql . "where 	nc.cd_processo = pr.cd_processo and ";
		$sql = $sql . "			div.codigo = pr.cod_responsavel and ";
		$sql = $sql . "			div.cod_gerente = ucdi.codigo and ";
		$sql = $sql . "			nc.cd_nao_conformidade = $num_nao_conf ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
// -------------------------------------------------------------------------
		$sql2 =        "select   usuario ";
		$sql2 = $sql2 . "from    projetos.usuarios_controledi ";
		$sql2 = $sql2 . "where 	 (strpos('". $reg['envolvidos'] . "', divisao) > 0) and tipo = 'G'";		
//		echo $sql2;
		$rs2 = pg_exec($db, $sql2);

		while ($reg2=pg_fetch_array($rs2))
   		{
			$gerentes = $gerentes . $reg2['usuario'] . ", ";
		}
//		echo $gerentes;
//		Responsбvel (se houver)
		$sql1 = 		"  	select 	nome                                    ";
		$sql1 = $sql1 . "  	from	projetos.usuarios_controledi,          ";
		$sql1 = $sql1 . "  			projetos.nao_conformidade                ";
		$sql1 = $sql1 . "	where 	cd_responsavel = codigo           ";
		$sql1 = $sql1 . " 	and		cd_nao_conformidade = " . $reg['cd_nao_conformidade'];
		$rs1 = pg_exec($sql1);
		$reg1 = pg_fetch_array($rs1);
		$responsavel = $reg1['nome'];
// -------------------------------------------------------------------------
		$v_para = $reg['usuario']."@eletroceee.com.br";
//		$v_para = $v_para . '; ' . $reg['cod_responsavel']."@eletroceee.com.br"; 
		$v_env = $reg['envolvidos'] . ",";
		$v_env = str_replace("DIE,","", $v_env);	// Retira Diretoria Executiva
		$v_env = str_replace("CD,","", $v_env);		// Retira Conselho Deliberativo
		$v_env = str_replace("CF,","", $v_env);		// Retira Conselho Fiscal
		$v_envolvidos = str_replace(",","@eletroceee.com.br; ", $gerentes) . $reg['cod_responsavel']."@eletroceee.com.br"; //$v_env);
// 		echo $v_envolvidos;
		$v_cc = $v_envolvidos; 
		$v_cco = "";
		$v_de = 'Sistema de Gestгo da Qualidade';	// Garcia - 22/03/2004
// ---------------------------------------------------------- Бrea da mensagem HTML:
		$vbcrlf = chr(10).chr(13);
		$msg = "Comunicaзгo de Abertura de Nгo Conformidade" . $vbcrlf ;
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
// ----------------------------------------- responsбvel pelo processo
		$msg = $msg . "Prezado(a) ".$reg['nom_ger'].", Responsбvel: " . $responsavel . " e demais envolvidos no processo:" . $vbcrlf;
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
		$msg = $msg . $v_texto . $vbcrlf;
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
		$msg = $msg . "Processo: " . $reg['cd_processo'] . " - " . $reg['procedimento'] . $vbcrlf;
		$msg = $msg . "Nъmero da Nгo Conformidade: ".$reg['cd_nao_conformidade'] . $vbcrlf;
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
		$msg = $msg . "Descriзгo da Nгo Conformidade:" . $vbcrlf;
		$msg = $msg . $reg['descricao'] . $vbcrlf;    
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
		$msg = $msg . "Evidкncias Objetivas:" . $vbcrlf;
		$msg = $msg . $reg['evidencias'] . $vbcrlf;
		$msg = $msg . "--------------------------------------------" . $vbcrlf;
		$date = date("d/m/Y");
		$sql =        " insert into projetos.envia_emails ( ";
		$sql = $sql . "		dt_envio, ";
		$sql = $sql . "		de, ";
		$sql = $sql . "		para, ";
		$sql = $sql . "		cc,	";
		$sql = $sql . "		cco, ";
		$sql = $sql . "		assunto, ";
		$sql = $sql . "		texto ";
		$sql = $sql . " ) ";
		$sql = $sql . " VALUES ( ";
		$sql = $sql . "		current_date, ";
		$sql = $sql . "		'$v_de', ";
		$sql = $sql . "		'$v_para', ";
		$sql = $sql . "		'$v_cc', ";
		$sql = $sql . "    	'$v_cco', ";
		$sql = $sql . "    	'$v_assunto', ";
		$sql = $sql . "    	'$msg' ";
		$sql = $sql . ")";	 
//	echo $sql;
		pg_exec($db, $sql);
	}
?>