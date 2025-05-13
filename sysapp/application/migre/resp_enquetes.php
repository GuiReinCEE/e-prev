<?
	if($_REQUEST['c'] == 65)
	{
		include_once('inc/sessao_enquetes.php');
	}
	else if(($_REQUEST['c'] == 30) or ($_REQUEST['c'] == 79) or ($_REQUEST['c'] == 102) or ($_REQUEST['c']==111))
	{
	}
	else
	{
		include_once('inc/sessao.php');
	}
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$sql = "
			SELECT dt_inicio,
                   dt_fim,  
				   to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as dt_inicio_ed, 
				   to_char(dt_fim, 'DD/MM/YYYY HH24:MI') as dt_fim_ed, 
				   current_timestamp as data_hoje, 
				   cd_responsavel,
				   CASE WHEN CURRENT_TIMESTAMP >= dt_inicio
					    THEN 'S'
					    ELSE 'N'
				   END AS fl_inicio,
				   CASE WHEN CURRENT_TIMESTAMP >= dt_fim
					    THEN 'S'
					    ELSE 'N'
				   END AS fl_fim
	          FROM projetos.enquetes  
			 WHERE cd_enquete = ".$_REQUEST['c']." 			  
	       ";
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);	
	if ((($reg['fl_inicio'] == 'S') and ($reg['fl_fim'] == 'N')) or ($reg['cd_responsavel'] == $_SESSION['Z'])) 
	{		
	
	
	
// ----------------------------------------------- se for tipo formulário, atualizar o último respondente
	$sql = "select controle_respostas, ultimo_respondente, tipo_layout, tipo_enquete, obrigatoriedade from projetos.enquetes where cd_enquete = ".$_REQUEST['c'];
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$controle_respostas = $reg['controle_respostas'];
	$obrigatoriedade = $reg['obrigatoriedade'];
	$tp_layout = 'N';
	
	if($reg['controle_respostas'] == 'R') 
	{
		session_start("PESQUISA_RE");
		if($_SESSION['ENQ_CD_REGISTRO_EMPREGADO'] == "")
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=enquete_login_re.php?c='.$_REQUEST['c'].'">';
		}
	}	
	
	ECHO "<!--"; print_r($_REQUEST); echo "-->";
	
	if ($reg['tipo_layout'] == 3) {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_'.$_REQUEST['c'].'.html');
		$tpl->prepare();
	} elseif ($reg['tipo_layout'] == 2) {
		
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_fceee.html');
		$tpl->prepare();
	} elseif ($reg['tipo_layout'] == 4) {
	    $tp_layout = 'S';
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_atendimento.html');
		$tpl->prepare();		
	} else {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes.html');
		$tpl->prepare();
		$tpl->assign('n', $n);
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');
		$tpl->assign('usuario', $_SESSION['N']);
		$tpl->assign('divsao', $_SESSION['D']);
	}
	
	//echo "Proxima: $proxima_ordem";
//-----------------------------------------------   
	if ($proxima_ordem == '0') {
		$ultima_tela = 'S'; 
	}
//-----------------------------------------------   
	if ($proxima_ordem == '' ) { 
		$proxima_ordem = 1; // indica início da pesquisa
		$sql =        " select 	cd_agrupamento, ordem
						from 	projetos.enquete_agrupamentos 
						where 	cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and ordem = " . $proxima_ordem ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		//ECHO "AQUI1: $sql";
		
		$proximo_agrupamento = $reg['cd_agrupamento'];
		if ($agrup == '') { $agrup = $proximo_agrupamento; }
	}
	//echo 'Agrup:'.$agrup;
//-----------------------------------------------   	
	$sql =        " select 	ordem
					from 	projetos.enquete_agrupamentos 
					where 	cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and ordem > " . $proxima_ordem . " order by ordem";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$proxima_ordem = $reg['ordem'];
//----------------------------------------------- Verifica se usuário já preencheu a enquete
	/*
	if ($controle_respostas == 'P') 
	{
		$sql =        " select 	count(*) as num_regs ";
		$sql = $sql . " from 	projetos.enquetes_participantes ep, projetos.enquetes e  ";
		$sql = $sql . " where 	ep.cd_enquete = $c and cd_empresa = $EMP and ep.cd_registro_empregado = $RE and ep.seq_dependencia = $SEQ and ep.cd_enquete = e.cd_enquete and e.controle_respostas = 'P' ";
//			echo $sql;
	} else 
	{
		$sql =        " select 	count(*) as num_regs ";
		$sql = $sql . " from 	projetos.usuarios_enquetes ue, projetos.enquetes e  ";
		$sql = $sql . " where 	ue.cd_enquete = $c and cd_usuario = $Z and ue.cd_enquete = e.cd_enquete and e.controle_respostas = 'U' ";
	}
	*/
		if ($controle_respostas == 'P') 
		{
			$sql =        " select 	count(*) as num_regs ";
			$sql = $sql . " from 	projetos.enquetes_participantes ep, projetos.enquetes e  ";
			$sql = $sql . " where 	ep.cd_enquete = ".$_REQUEST['c']." and cd_empresa = $EMP 
			and ep.cd_registro_empregado = $RE and ep.seq_dependencia = $SEQ and ep.cd_enquete = e.cd_enquete and e.controle_respostas = 'P' ";
//			echo $sql;
		} 
		else if ($controle_respostas == 'R') 
		{
			$sql = " 
					SELECT COUNT(*) AS num_regs 
					  FROM projetos.enquetes_participantes ep, 
					       projetos.enquetes e  
					 WHERE ep.cd_enquete = ".$_REQUEST['c']." 
					   AND cd_empresa               = ".$_SESSION['ENQ_CD_EMPRESA']." 
			           AND ep.cd_registro_empregado = ".$_SESSION['ENQ_CD_REGISTRO_EMPREGADO']." 
					   AND ep.seq_dependencia       = ".$_SESSION['ENQ_SEQ_DEPENDENCIA']." 
					   AND ep.cd_enquete            = e.cd_enquete 
					   AND e.controle_respostas     = 'R' 
				   ";
		} 		
		else if ($controle_respostas == 'I') 
		{
			$sql = " 
					SELECT COUNT(*) as num_regs
					  FROM projetos.enquete_resultados e
					 WHERE e.cd_enquete = ".$_REQUEST['c']." 
					   AND e.ip = '".$_SERVER['REMOTE_ADDR']."'
				   ";
			if(($_REQUEST['c'] == 30) or ($_REQUEST['c'] == 79) or ($_REQUEST['c'] == 102) or ($_REQUEST['c'] == 111))
			{
				$sql = " 
						SELECT 0 as num_regs
					   ";			
			}
		}
		else {
			$sql =        " select 	count(*) as num_regs ";
			$sql = $sql . " from 	projetos.usuarios_enquetes ue, projetos.enquetes e  ";
			$sql = $sql . " where 	ue.cd_enquete = ".$_REQUEST['c']." 
			and cd_usuario = ".$_SESSION['Z']." 
			and ue.cd_enquete = e.cd_enquete 
			and e.controle_respostas = 'U' ";
		}	
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	if ($reg['num_regs'] > 0) 
	{
		$tpl->newBlock('mensagem');
		$tpl->assign('mensagem', 'Você já respondeu a esta pesquisa!');
	}
	else {
//----------------------------------------------- Informações do agrupamento
		
		$v_cor_fundo1 = "#FFFFFF";
		$v_cor_fundo2 = "#F2F8FC";
		
		$tpl->newBlock('cadastro');
		$tpl->assign('cor_fundo1', $v_cor_fundo1);
		$tpl->assign('cor_fundo2', $v_cor_fundo2);
		$tpl->assign('eq', $_REQUEST['c']);		
		$tpl->assign('proxima_ordem', $proxima_ordem);
		$sql =        " select 	cd_enquete, titulo, to_char(dt_inicio, 'DD/MM/YYYY') as dt_inicio, texto_encerramento, 
						to_char(dt_fim, 'DD/MM/YYYY') as dt_fim, cd_site, cd_responsavel, controle_respostas
						from 	projetos.enquetes 
						where 	cd_enquete = ".$_REQUEST['c']." ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_enquete']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('dt_inicio', $reg['dt_inicio']);
		$tpl->assign('dt_fim', $reg['dt_fim']);
		$v_controle_respostas = $reg['controle_respostas'];
		$v_site = $reg['cd_site'];
		$v_responsavel = $reg['cd_responsavel'];
		$v_controle_respostas = $reg['controle_respostas'];
		$v_texto_encerramento = $reg['texto_encerramento'];
// ------------------------------------------------------------- Agrupamento:
		if ($agrup == '') { // verifica em qual agrupamento parou
//			$sql =        " select 	min(cd_agrupamento) as agrup ";
//			$sql = $sql . " from 	projetos.enquete_agrupamentos  ";
//			$sql = $sql . " where 	cd_enquete = $c ";
//			$rs = pg_exec($db, $sql);
//			$reg = pg_fetch_array($rs);
//			$agrup = $reg['agrup'];
			$agrup = $proximo_agrupamento;
		}	
		$sql =        " select 	count(*) as num_regs  ";
		$sql = $sql . " from 	projetos.enquete_agrupamentos  ";
		$sql = $sql . " where 	cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and cd_agrupamento = $agrup";
//		if ($Z == 110) { echo $sql.' - '.$ultima_tela; }
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		
		//ECHO "<!-- AQUI $sql -->";
//------------------------------------------------------------------------------------
		if (($obrigatoriedade == 'P') and ($v_controle_respostas == 'P') and (($reg['num_regs'] == 0) or ($ultima_tela == 'S'))){
			$sql = "select 	count(*) as num_regs 
					from 	projetos.enquete_resultados  
					where 	cd_enquete = ".$_REQUEST['c']." and ip = '".$EMP.$RE.$SEQ."'";
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			if ($reg['num_regs'] == 0){
				$tpl->assign('grupo', 'Para seu voto ser válido, você deve votar ao menos em uma entidade. Por favor, vote novamente, clicando no link abaixo:<br><a href="http://www.e-prev.com.br/controle_projetos/login_eleicoes_fsolidaria.php">VOTAR NOVAMENTE!</a>');					
//				$tpl->newBlock('bola_continuar');
				$tpl->assign('continuar', 'votar_novamente');
				pg_close($db);
				$tpl->printToScreen();	
				exit;
			}
		}
//------------------------------------------------------------------------------------
		if (($reg['num_regs'] == 0) or ($ultima_tela == 'S')){
//------------------------------------------------------------------------------------
			if ($v_texto_encerramento == '') {
				#$tpl->assign('grupo', 'Obrigado por responder nossa pesquisa. Logo estaremos disponibilizando os resultados.');
				$tpl->assign('grupo', 'Obrigado por responder nossa pesquisa.');
			} else {
				$tpl->assign('grupo', $v_texto_encerramento);
			}
			
			if ($v_controle_respostas == 'U') {
				$sql =        " insert into projetos.usuarios_enquetes values(".$_SESSION['Z'].", ".$_REQUEST['c'].") ";
				$rs = pg_exec($db, $sql);
				$reg=pg_fetch_array($rs);
			} elseif ($v_controle_respostas == 'P') {
				$sql =        " insert into projetos.enquetes_participantes values(".$_REQUEST['c'].",$EMP, $RE, $SEQ) ";
				$rs = pg_exec($db, $sql);
				$reg=pg_fetch_array($rs);
			}elseif ($v_controle_respostas == 'R') {
				$sql = " insert into projetos.enquetes_participantes values(".$_REQUEST['c'].",".$_SESSION['ENQ_CD_EMPRESA'].",".$_SESSION['ENQ_CD_REGISTRO_EMPREGADO'].",".$_SESSION['ENQ_SEQ_DEPENDENCIA'].") ";
				$rs = pg_exec($db, $sql);
				$reg=pg_fetch_array($rs);
			}
			
			
			$tpl->assign('ultima_tela', 'S');
//			$tpl->newBlock('bola_continuar');
			if ($v_controle_respostas == 'F') 
			{
				if($tp_layout == 'S')
				{
					$tpl->assign('display', 'style="display:none;"');
				}
				else
				{
					$tpl->assign('fl_nova', '<a href="resp_enquetes_capa.php?c='.$_REQUEST['c'].'"><img src="img/img_bola_nova.jpg" border="0"></a>');
					$tpl->assign('fl_nova_exibe', 'display:none;');
				}
			} 
			else 
			{
				if($tp_layout == 'S')
				{
					$tpl->assign('display', 'style="display:none;"');
				}
				else if($tp_layout == 'N')
				{
					
					$tpl->assign('fl_nova_exibe', 'display:none;');
				}					
				else
				{				
					$tpl->assign('fl_nova_exibe', 'display:none;');
				}
			}
		}	
		else {
//			$tpl->newBlock('bola_continuar');
			$tpl->assign('continuar', 'continuar');
			$sql =        " select 	nome, indic_escala, mostrar_valores, ncolsamp_diss, nota_rodape, disposicao ";
			$sql = $sql . " from 	projetos.enquete_agrupamentos  ";
			$sql = $sql . " where 	cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and cd_agrupamento = $agrup";
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('cor_fundo', '#CDCDCD');
			$tpl->assign('grupo', $reg['nome']); ### VERIFICAR ISSO 19/12/2007
			$grupo_nome = $reg['nome'];
			$tpl->assign('cd_agrupamento', $agrup);
			$v_nota_rodape = $reg['nota_rodape'];
			$v_mostrar_valores = $reg['mostrar_valores'];
			$v_ncolsamp_diss = $reg['ncolsamp_diss'];
			$disposicao = $reg['disposicao'];
//-------------------------------------------------------------- Valores das respostas
			if ($reg['indic_escala'] == 'S') 
			{
				$sql = "SELECT nome ";
				$sql = $sql . " FROM projetos.enquete_respostas r ";
				$sql = $sql . " where cd_enquete = ".$_REQUEST['c']." ";
				$sql = $sql . " order by ordem ";
				$rs = pg_exec($db, $sql);
				$tpl->newBlock('escala');
				$tpl->assign('grupo', $grupo_nome);
				
				### AQUI
				$tpl->newBlock('cabecalho_respostas');				
				while ($reg = pg_fetch_array($rs)) 
				{
					$tpl->newBlock('grau');
					$tpl->assign('grau', $reg['nome']);
				}
//-------------------------------------------------------------- Lista de questões
				$sql = "SELECT cd_pergunta, texto, 
				               r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, 
				               r1_complemento, r2_complemento, r3_complemento, r4_complemento, r5_complemento, r6_complemento, 
							   r7_complemento, r8_complemento, r9_complemento, r10_complemento, r11_complemento, r12_complemento,
 				               r_diss, 
							   r_justificativa, ";
				$sql = $sql . " rotulo1, rotulo2, rotulo3, rotulo4, rotulo5, rotulo6, rotulo7, rotulo8, rotulo9, rotulo10, rotulo11, rotulo12, ";
				$sql = $sql . " rotulo_dissertativa, rotulo_justificativa, pergunta_texto ";
				$sql = $sql . " FROM projetos.enquete_perguntas p ";
				$sql = $sql . " where p.cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and p.cd_agrupamento = $agrup ";
				$sql = $sql . " order by cd_pergunta ";
				$rs = pg_exec($db, $sql);
				
// ------------------------------------------------------------ Início do laço 1:
				while ($reg = pg_fetch_array($rs)) {
					if ($disposicao == 'V') { $tpl->newBlock('pergunta_vertical'); } else { $tpl->newBlock('pergunta_linha'); }
					$tpl->assign('codigo', $_REQUEST['c']);
					if ($reg['pergunta_texto'] != '') {
						$tpl->assign('titulo', $reg['pergunta_texto']);
						$tpl->newBlock('resposta_texto');
					}
					else {
						if ($cor == 1) {
							$tpl->assign('cor_fundo', $v_cor_fundo1);
							$cor = 2;
						}
						else {
							$tpl->assign('cor_fundo', $v_cor_fundo2);
							$cor = 1;
						}
						if (($reg['r1'] == 'S') or 
							($reg['r2'] == 'S') or
							($reg['r3'] == 'S') or
							($reg['r4'] == 'S') or
							($reg['r5'] == 'S') or
							($reg['r6'] == 'S') or
							($reg['r7'] == 'S') or
							($reg['r8'] == 'S') or
							($reg['r9'] == 'S') or
							($reg['r10'] == 'S') or
							($reg['r11'] == 'S') or
							($reg['r12'] == 'S'))
						{
							$v_limite = $v_limite + 1;
						}

						$tpl->assign('titulo', $reg['texto']);
						if ($reg['r1'] == 'S') {				
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '1');
							$tpl->assign('R1', $reg['r1']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo1'] != '') {
									$tpl->assign('rotulo', $reg['rotulo1']);
								} else {
									$tpl->assign('rotulo', '1');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_1\',\''.$reg['r1_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							if ($reg['r1_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_1" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_1" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_1" 
								                                       cols="20" rows="2" ></textarea></div>');
							}							
						}
						if ($reg['r2'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '2');
							$tpl->assign('R2', $reg['r2']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo2'] != '') {
									$tpl->assign('rotulo', $reg['rotulo2']);
								} else {
									$tpl->assign('rotulo', '2');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_2\',\''.$reg['r2_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r2_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_2" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_2" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_2" 
								                                       cols="20" rows="2" ></textarea></div>');
							}							
						}
						if ($reg['r3'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '3');
							$tpl->assign('R3', $reg['r3']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo3'] != '') {
									$tpl->assign('rotulo', $reg['rotulo3']);
								} else {
									$tpl->assign('rotulo', '3');
								}
							}	
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_3\',\''.$reg['r3_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							if ($reg['r3_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_3" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_3" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_3" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r4'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '4');
							$tpl->assign('R4', $reg['r4']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo4'] != '') {
									$tpl->assign('rotulo', $reg['rotulo4']);
								} else {
									$tpl->assign('rotulo', '4');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_4\',\''.$reg['r4_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							
							if ($reg['r4_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_4" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_4" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_4" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r5'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '5');
							$tpl->assign('R5', $reg['r5']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo5'] != '') {
									$tpl->assign('rotulo', $reg['rotulo5']);
								} else {
									$tpl->assign('rotulo', '5');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_5\',\''.$reg['r5_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							
							if ($reg['r5_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_5" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_5" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_5" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r6'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '6');
							$tpl->assign('R6', $reg['r6']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo6'] != '') {
									$tpl->assign('rotulo', $reg['rotulo6']);
								} else {
									$tpl->assign('rotulo', '6');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_6\',\''.$reg['r6_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							
							if ($reg['r6_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_6" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_6" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_6" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r7'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '7');
							$tpl->assign('R7', $reg['r7']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo7'] != '') {
									$tpl->assign('rotulo', $reg['rotulo7']);
								} else {
									$tpl->assign('rotulo', '7');
								}
							}

							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_7\',\''.$reg['r7_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);
							
							
							if ($reg['r7_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_7" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_7" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_7" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r8'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '8');
							$tpl->assign('R8', $reg['r8']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo8'] != '') {
									$tpl->assign('rotulo', $reg['rotulo8']);
								} else {
									$tpl->assign('rotulo', '8');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_8\',\''.$reg['r8_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r8_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_8" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_8" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_8" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r9'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '9');
							$tpl->assign('R9', $reg['r9']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo9'] != '') {
									$tpl->assign('rotulo', $reg['rotulo9']);
								} else {
									$tpl->assign('rotulo', '9');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_9\',\''.$reg['r9_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r9_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_9" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_9" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_9" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r10'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '10');
							$tpl->assign('R10', $reg['r10']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo10'] != '') {
									$tpl->assign('rotulo', $reg['rotulo10']);
								} else {
									$tpl->assign('rotulo', '10');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_10\',\''.$reg['r10_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r10_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_10" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_10" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_10" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r11'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '11');
							$tpl->assign('R11', $reg['r11']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo11'] != '') {
									$tpl->assign('rotulo', $reg['rotulo11']);
								} else {
									$tpl->assign('rotulo', '11');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_11\',\''.$reg['r11_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r11_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_11" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_11" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_11" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r12'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta_linha'); }
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '12');
							$tpl->assign('R12', $reg['r12']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo12'] != '') {
									$tpl->assign('rotulo', $reg['rotulo12']);
								} else {
									$tpl->assign('rotulo', '12');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_12\',\''.$reg['r12_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);							
							
							if ($reg['r12_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_12" style="display:none;">Complemente sua resposta:<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_12" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_12" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r_diss'] == 'S') {
							$tpl->newBlock('resposta_dissertativa_linha');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '0');
							$tpl->assign('R_diss', $reg['r_diss']);
							$tpl->assign('ncolsamp_diss', $v_ncolsamp_diss);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_dissertativa'] != '') {
									$tpl->assign('rotulo_dissertativa', $reg['rotulo_dissertativa']);
								} else {
									$tpl->assign('rotulo_dissertativa', '...');
								}
							}
						}
						if ($reg['r_justificativa'] == 'S') {
							$tpl->newBlock('justificativa_linha');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_justificativa'] != '') {
									$tpl->assign('rotulo_justificativa', $reg['rotulo_justificativa']);
								} else {
									$tpl->assign('rotulo_justificativa', '...');
								}
							}
						}
						if ($reg['pergunta_texto'] != '') {
							$tpl->newBlock('resposta_texto_linha');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '0');
							$tpl->assign('R_diss', $reg['r_diss']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_dissertativa'] != '') {
									$tpl->assign('rotulo_dissertativa', $reg['rotulo_dissertativa']);
								} else {
									$tpl->assign('rotulo', '...');
								}
							}
						}
						$tpl->assign('chk_site', ($reg['cd_site'] == $v_site ? ' selected' : ''));
					}			
				}
// ----------------------------------------------------------------------- Fim do laço 1
			} 
			else 
			{
//-------------------------------------------------------------- Lista de questões
				$sql = "SELECT cd_pergunta, texto, 
							   r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, 
				               r1_complemento, r2_complemento, r3_complemento, r4_complemento, r5_complemento, r6_complemento, 
							   r7_complemento, r8_complemento, r9_complemento, r10_complemento, r11_complemento, r12_complemento,
							   r1_complemento_rotulo, r2_complemento_rotulo, r3_complemento_rotulo, r4_complemento_rotulo, r5_complemento_rotulo, r6_complemento_rotulo, 
							   r7_complemento_rotulo, r8_complemento_rotulo, r9_complemento_rotulo, r10_complemento_rotulo, r11_complemento_rotulo, r12_complemento_rotulo,
							   r_diss, r_justificativa, ";
				$sql = $sql . " rotulo1, rotulo2, rotulo3, rotulo4, rotulo5, rotulo6, rotulo7, rotulo8, rotulo9, rotulo10, rotulo11, rotulo12, ";
				$sql = $sql . " rotulo_dissertativa, rotulo_justificativa, pergunta_texto ";
				$sql = $sql . " FROM projetos.enquete_perguntas p ";
				$sql = $sql . " where p.cd_enquete = ".$_REQUEST['c']." and dt_exclusao is null and p.cd_agrupamento = $agrup ";
				$sql = $sql . " order by cd_pergunta ";
				$rs = pg_exec($db, $sql);
// ------------------------------------------------------------ Início do laço 2: (original)
				while ($reg = pg_fetch_array($rs)) {
					if ($disposicao == 'V') { $tpl->newBlock('pergunta_vertical'); } else { $tpl->newBlock('pergunta'); }
					$tpl->assign('codigo', $_REQUEST['c']);
					if ($reg['pergunta_texto'] != '') {
						$tpl->assign('titulo', $reg['pergunta_texto']);
						$tpl->newBlock('resposta_texto');
					}
					else {
						if ($cor == 1) {
							$tpl->assign('cor_fundo', $v_cor_fundo1);
							$cor = 2;
						}
						else {
							$tpl->assign('cor_fundo', $v_cor_fundo2);
							$cor = 1;
						}
						if (($reg['r1'] == 'S') or 
							($reg['r2'] == 'S') or
							($reg['r3'] == 'S') or
							($reg['r4'] == 'S') or
							($reg['r5'] == 'S') or
							($reg['r6'] == 'S') or
							($reg['r7'] == 'S') or
							($reg['r8'] == 'S') or
							($reg['r9'] == 'S') or
							($reg['r10'] == 'S') or
							($reg['r11'] == 'S') or
							($reg['r12'] == 'S'))
						{
							$v_limite = $v_limite + 1;
						}

						$tpl->assign('titulo', $reg['texto']);
						if ($reg['r1'] == 'S') {
						
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '1');
							$tpl->assign('R1', $reg['r1']);
							if ($v_mostrar_valores == 'S') 
							{
								if ($reg['rotulo1'] != '') 
								{
									$tpl->assign('rotulo', $reg['rotulo1']);
								} 
								else 
								{
									$tpl->assign('rotulo', '1');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_1\',\''.$reg['r1_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r1_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_1" style="display:none;">'.(trim($reg['r1_complemento_rotulo']) != "" ? trim($reg['r1_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_1" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_1" 
								                                       cols="20" rows="2"></textarea></div>');
							}								
						}
						if ($reg['r2'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '2');
							$tpl->assign('R2', $reg['r2']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo2'] != '') {
									$tpl->assign('rotulo', $reg['rotulo2']);
								} else {
									$tpl->assign('rotulo', '2');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_2\',\''.$reg['r2_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);	
							
							if ($reg['r2_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_2" style="display:none;">'.(trim($reg['r2_complemento_rotulo']) != "" ? trim($reg['r2_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_2" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_2" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r3'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '3');
							$tpl->assign('R3', $reg['r3']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo3'] != '') {
									$tpl->assign('rotulo', $reg['rotulo3']);
								} else {
									$tpl->assign('rotulo', '3');
								}
							}

							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_3\',\''.$reg['r3_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r3_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_3" style="display:none;">'.(trim($reg['r3_complemento_rotulo']) != "" ? trim($reg['r3_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_3" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_3" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r4'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '4');
							$tpl->assign('R4', $reg['r4']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo4'] != '') {
									$tpl->assign('rotulo', $reg['rotulo4']);
								} else {
									$tpl->assign('rotulo', '4');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_4\',\''.$reg['r4_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r4_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_4" style="display:none;">'.(trim($reg['r4_complemento_rotulo']) != "" ? trim($reg['r4_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_4" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_4" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r5'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '5');
							$tpl->assign('R5', $reg['r5']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo5'] != '') {
									$tpl->assign('rotulo', $reg['rotulo5']);
								} else {
									$tpl->assign('rotulo', '5');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_5\',\''.$reg['r5_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r5_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_5" style="display:none;">'.(trim($reg['r5_complemento_rotulo']) != "" ? trim($reg['r5_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_5" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_5" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r6'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '6');
							$tpl->assign('R6', $reg['r6']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo6'] != '') {
									$tpl->assign('rotulo', $reg['rotulo6']);
								} else {
									$tpl->assign('rotulo', '6');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_6\',\''.$reg['r6_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r6_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_6" style="display:none;">'.(trim($reg['r6_complemento_rotulo']) != "" ? trim($reg['r6_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_6" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_6" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r7'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '7');
							$tpl->assign('R7', $reg['r7']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo7'] != '') {
									$tpl->assign('rotulo', $reg['rotulo7']);
								} else {
									$tpl->assign('rotulo', '7');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_7\',\''.$reg['r7_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r7_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_7" style="display:none;">'.(trim($reg['r7_complemento_rotulo']) != "" ? trim($reg['r7_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_7" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_7" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r8'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '8');
							$tpl->assign('R8', $reg['r8']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo8'] != '') {
									$tpl->assign('rotulo', $reg['rotulo8']);
								} else {
									$tpl->assign('rotulo', '8');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_8\',\''.$reg['r8_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r8_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_8" style="display:none;">'.(trim($reg['r8_complemento_rotulo']) != "" ? trim($reg['r8_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_8" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_8" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r9'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '9');
							$tpl->assign('R9', $reg['r9']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo9'] != '') {
									$tpl->assign('rotulo', $reg['rotulo9']);
								} else {
									$tpl->assign('rotulo', '9');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_9\',\''.$reg['r9_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r9_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_9" style="display:none;">'.(trim($reg['r9_complemento_rotulo']) != "" ? trim($reg['r9_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_9" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_9" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r10'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '10');
							$tpl->assign('R10', $reg['r10']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo10'] != '') {
									$tpl->assign('rotulo', $reg['rotulo10']);
								} else {
									$tpl->assign('rotulo', '10');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_10\',\''.$reg['r10_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r10_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_10" style="display:none;">'.(trim($reg['r10_complemento_rotulo']) != "" ? trim($reg['r10_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_10" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_10" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r11'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '11');
							$tpl->assign('R11', $reg['r11']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo11'] != '') {
									$tpl->assign('rotulo', $reg['rotulo11']);
								} else {
									$tpl->assign('rotulo', '11');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_11\',\''.$reg['r11_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r11_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_11" style="display:none;">'.(trim($reg['r11_complemento_rotulo']) != "" ? trim($reg['r11_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_11" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_11" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r12'] == 'S') {
							if ($disposicao == 'V') { $tpl->newBlock('resposta_vertical'); } else { $tpl->newBlock('resposta'); }				
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '12');
							$tpl->assign('R12', $reg['r12']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo12'] != '') {
									$tpl->assign('rotulo', $reg['rotulo12']);
								} else {
									$tpl->assign('rotulo', '12');
								}
							}
							
							$onclick_comp = 'onclick="checaComplemento('.$reg['cd_pergunta'].',\'CR_'.$reg['cd_pergunta'].'_complemento_12\',\''.$reg['r12_complemento'].'\');"';
							$tpl->assign('JS_RADIO',$onclick_comp);								
							
							if ($reg['r12_complemento'] == 'S')
							{
								$tpl->assign('complemento', '
								<div id="CR_'.$reg['cd_pergunta'].'_complemento_12" style="display:none;">'.(trim($reg['r12_complemento_rotulo']) != "" ? trim($reg['r12_complemento_rotulo']) : "").'<BR><textarea name="R_'.$reg['cd_pergunta'].'_complemento_12" 
								                                       id="R_'.$reg['cd_pergunta'].'_complemento_12" 
								                                       cols="20" rows="2"></textarea></div>');
							}							
						}
						if ($reg['r_diss'] == 'S') {
							$tpl->newBlock('resposta_dissertativa');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '0');
							$tpl->assign('R_diss', $reg['r_diss']);
							$tpl->assign('ncolsamp_diss', $v_ncolsamp_diss);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_dissertativa'] != '') {
									$tpl->assign('rotulo_dissertativa', $reg['rotulo_dissertativa']);
								} else {
									$tpl->assign('rotulo_dissertativa', '...');
								}
							}
						}
						if ($reg['r_justificativa'] == 'S') {
							$tpl->newBlock('justificativa');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_justificativa'] != '') {
									$tpl->assign('rotulo_justificativa', $reg['rotulo_justificativa']);
								} else {
									$tpl->assign('rotulo_justificativa', '...');
								}
							}
						}
						if ($reg['pergunta_texto'] != '') {
							$tpl->newBlock('resposta_texto');
							$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
							$tpl->assign('valor', '0');
							$tpl->assign('R_diss', $reg['r_diss']);
							if ($v_mostrar_valores == 'S') {
								if ($reg['rotulo_dissertativa'] != '') {
									$tpl->assign('rotulo_dissertativa', $reg['rotulo_dissertativa']);
								} else {
									$tpl->assign('rotulo', '...');
								}
							}
						}
						$tpl->assign('chk_site', ($reg['cd_site'] == $v_site ? ' selected' : ''));
					}			
				}
			}
// ----------------------------------------------------------------------- Fim do laço 2
			$tpl->newBlock('bola_voltar');
			$tpl->newBlock('nota_rodape');
			$tpl->assign('nota_rodape', str_replace(chr(10), '<br>', $v_nota_rodape));
			if ($v_limite != '') 
			{
				if($_REQUEST['c'] == 158) ## --> GAMBIARRA PARA DANI (GRI) 05/08/2009
				{
					$v_limite = 1;
				}
				
				$tpl->newBlock('informacoes');
				$tpl->assign('limt', $v_limite);
				$tpl->assign('obrigatorio', $obrigatoriedade);
				if ($v_limite != 1) 
				{
					$tpl->assign('s', 's');
				}
			}
		}
	}
	}
	else
	{
		header('location: resp_enquetes_capa.php?c='.$_REQUEST['c']);
	}
//--------------------------------------------------------------------
	
	pg_close($db);
	$tpl->printToScreen();	
?>