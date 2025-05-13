<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_enquetes_definicao.html');
    
	
	header('location:'.base_url().'index.php/ecrm/operacional_enquete/cadastro/'.$_REQUEST['c']);exit;
	
    //-----------------------------------------------
	$tpl->prepare();
	//$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
    //	if ($D == 'CC') {
    //		header('location: acesso_restrito.php?IMG=banner_enquetes');	
    //	} else {

    //-----------------------------------------------
		$tpl->newBlock('cadastro');
		$tpl->assign('cor_fundo1', $v_cor_fundo1);
		$tpl->assign('cor_fundo2', $v_cor_fundo2);

		if(!isset($c))
		{
			$tpl->assign("style_abas", "display:NONE");
		}
		else
		{
			#### PERMISSOES DAS ABAS ####
			$qr_sql = "
						SELECT COUNT(*) AS fl_responsavel
						  FROM projetos.enquetes  
					     WHERE cd_enquete     = ".$_REQUEST['c']."
						   AND (cd_responsavel = ".$_SESSION['Z']." OR cd_divisao_responsavel = '".$_SESSION['D']."')
			          ";
			$ob_resul = pg_query($db, $qr_sql);
			$ar_reg   = pg_fetch_array($ob_resul);
			if(($ar_reg['fl_responsavel'] < 1) and ($_SESSION['Z'] != 170) and ($_SESSION['Z'] != 251)  and $Z!=191)
			{
				$tpl->assign("style_abas", "display:NONE");
			}
		}
        

		if ( isset($c) )	
		{
			$sql = "
					SELECT cd_enquete, 
					       titulo, 
						   texto_abertura, 
						   texto_encerramento, 
						   TO_CHAR(dt_inicio, 'DD/MM/YYYY') AS dt_inicio, 
						   TO_CHAR(dt_inicio, 'HH24:MI') AS hr_inicio, 
						   cd_servico, 
						   tipo_enquete, 
						   tipo_layout, 
						   TO_CHAR(dt_fim, 'DD/MM/YYYY') AS dt_fim, 
						   TO_CHAR(dt_fim, 'HH24:MI') AS hr_fim, 
						   cd_site, 
						   cd_responsavel, 
						   cd_evento_institucional, 
						   cd_publicacao, 
						   imagem, 
						   controle_respostas, 
						   nr_publico_total,
						   cd_divisao_responsavel						   
					  FROM projetos.enquetes  
					 WHERE cd_enquete = ".$c ;

			$rs = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			$tpl->assign('codigo', $reg['cd_enquete']);
            $tpl->assign('titulo', $reg['titulo']);
			
			$tpl->assign('dt_inicio', $reg['dt_inicio']);
			$tpl->assign('hr_inicio', $reg['hr_inicio']);
			
			$tpl->assign('dt_fim', $reg['dt_fim']);
			$tpl->assign('hr_fim', $reg['hr_fim']);
			
			$tpl->assign('abertura', $reg['texto_abertura']);
			$tpl->assign('encerramento', $reg['texto_encerramento']);			
			// echo($Z);
            if ($reg['cd_responsavel'] != $Z and $Z!=191) {
				$tpl->assign('ro_responsavel', 'readonly');
				$tpl->assign('dis_responsavel', 'disabled');
			}
			if ($reg['imagem'] != '') {
				$tpl->assign('imagem', '<img src="' . $reg['imagem'] . '">');
			}
			if ($reg['controle_respostas'] == 'U') {
				$tpl->assign('chk_usuario', 'checked');
			} elseif ($reg['controle_respostas'] == 'I') {
				$tpl->assign('chk_ip', 'checked');
			} 
			elseif ($reg['controle_respostas'] == 'P') 
			{
				$tpl->assign('chk_participante', 'checked');
			} 
			elseif ($reg['controle_respostas'] == 'R') 
			{
				$tpl->assign('chk_re', 'checked');
			} 
			else 
			{
				$tpl->assign('chk_formulario', 'checked');
			}
			
			$tpl->assign('nr_publico_total', $reg['nr_publico_total']);
			
			$v_site = $reg['cd_site'];
			$v_evento = $reg['cd_evento_institucional'];
			$v_servico = $reg['cd_servico'];
			$v_publicacao = $reg['cd_publicacao'];
			$cd_divisao_responsavel = $reg['cd_divisao_responsavel'];
			$v_responsavel = $reg['cd_responsavel'];
			$v_tipo_enquete = $reg['tipo_enquete'];
			$v_tipo_layout = $reg['tipo_layout'];

            // Questão dissertativa
			$sql =        " select 	pergunta_texto, cd_agrupamento ";
			$sql = $sql . " from 	projetos.enquete_perguntas  ";
			$sql = $sql . " where 	cd_enquete = $c and texto is null and pergunta_texto <> ''";
			$rs = pg_query($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('pergunta_texto', $reg['pergunta_texto']);
			
			/*
            $agrup_diss = $reg['cd_agrupamento'];
			$tpl->assign('agrup_diss', $reg['cd_agrupamento']);

            // Lista de agrupamentos para a questão dissertativa
            $sql = "SELECT cd_agrupamento as cd_agrup_diss, nome as nome_agrup_diss FROM projetos.enquete_agrupamentos where cd_enquete = $c order by ordem, nome";
			$rs = pg_query($db, $sql);
    
			$tpl->newBlock('agrup_diss');
			$tpl->assign('cd_agrup_diss', '/');
			$tpl->assign('nome_agrup_diss', '');
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('agrup_diss');
				$tpl->assign('cd_agrup_diss', $reg['cd_agrup_diss']);
				$tpl->assign('nome_agrup_diss', $reg['nome_agrup_diss']);
				if ($agrup_diss == $reg['cd_agrup_diss']) {
					$tpl->assign('sel_agrup_diss', ' selected');
				}
			}
            */

			if (isset($c)) {
				//$tpl->newBlock('componentes_pesquisa');				
			}
			$tpl->assign('codigo', $c);

            // Lista de questões
            /*
			$sql = "SELECT cd_pergunta, texto, r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, ";
			$sql = $sql . " p.cd_agrupamento, a.nome as nome_agrupamento ";
			$sql = $sql . " FROM projetos.enquete_perguntas p, projetos.enquete_agrupamentos a ";
			$sql = $sql . " where p.cd_enquete = $c and p.cd_enquete = a.cd_enquete ";
			$sql = $sql . " and a.dt_exclusao is null and p.dt_exclusao is null ";
			$sql = $sql . " and a.cd_agrupamento = p.cd_agrupamento order by ordem, cd_pergunta ";
			$rs = pg_query($db, $sql);
            $v_agrup_ant="";
			while ($reg = pg_fetch_array($rs)) {
				if ($reg['cd_agrupamento'] != $v_agrup_ant ) {
					$tpl->newBlock('pergunta');
					$tpl->assign('cor_fundo', '#CDCDCD');
					$tpl->assign('grupo', $reg['nome_agrupamento']);
					$v_agrup_ant = $reg['cd_agrupamento'];
				}
				$tpl->newBlock('pergunta');
				if ($cor == 1) {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$cor = 2;
				}
				else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$cor = 1;
				}
				$tpl->assign('codigo', $c);
				$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
				$tpl->assign('titulo', $reg['texto']);
				$tpl->assign('R1', $reg['r1']);
				$tpl->assign('R2', $reg['r2']);
				$tpl->assign('R3', $reg['r3']);
				$tpl->assign('R4', $reg['r4']);
				$tpl->assign('R5', $reg['r5']);
				$tpl->assign('R6', $reg['r6']);
				$tpl->assign('R7', $reg['r7']);
				$tpl->assign('R8', $reg['r8']);
				$tpl->assign('R9', $reg['r9']);
				$tpl->assign('R10', $reg['r10']);
				$tpl->assign('R11', $reg['r11']);
				$tpl->assign('R12', $reg['r12']);
				$tpl->assign('chk_site', ($reg['cd_site'] == $v_site ? ' selected' : ''));
			}
            */

            // Lista de agrupamentos
            /*
			$sql = "SELECT cd_agrupamento, nome 
					FROM projetos.enquete_agrupamentos
					where cd_enquete = $c 
					and dt_exclusao is null
					order by ordem, nome";
			$rs = pg_query($db, $sql);
			$tpl->assign('codigo', $c);
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('agrupamento');
				if ($cor == 1) {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$cor = 2;
				}
				else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$cor = 1;
				}
				$tpl->assign('codigo', $c);
				$tpl->assign('cd_agrupamento', $reg['cd_agrupamento']);
				$tpl->assign('nome', $reg['nome']);
			}
            */

            // Lista de respostas
			$sql = "SELECT cd_resposta, nome, ordem FROM projetos.enquete_respostas where cd_enquete = $c order by ordem, nome";
			$rs = pg_query($db, $sql);
			$tpl->assign('codigo', $c);
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('resposta');
				if ($cor == 1) {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$cor = 2;
				}
				else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$cor = 1;
				}
				$tpl->assign('codigo', $c);
				$tpl->assign('cd_resposta', $reg['cd_resposta']);
				$tpl->assign('resposta', $reg['nome']);
				$tpl->assign('ordem', $reg['ordem']);
			}
            
            // Resultados obtidos
			/*
            $sql = "select count(distinct ip) as num_regs from projetos.enquete_resultados where cd_enquete = $c and ip not like ('%.%')";
			$rs = pg_query($db, $sql);
			$tpl->assign('codigo', $c);
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('resultados_obtidos');
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$tpl->assign('codigo', $c);
				$tpl->assign('respondentes', $reg['num_regs']);
			}
            */
            
            // Peso das questões com base <> 10
			$sql = "select count(*) as nregs from projetos.enquete_agrupamentos ";
			$sql = $sql . "where cd_enquete = $c and indic_escala = 'S'";
			$rs = pg_query($db, $sql);
			if($reg = pg_fetch_array($rs)) {
				$v_peso1 = $reg['nregs'];
			}

            // Média das questões com base <> 10
			$sql = "
			        select avg(r2.valor) as media 
			          from projetos.enquete_resultados r1, 
					       projetos.enquete_respostas r2, 
					       projetos.enquete_agrupamentos r3 
			         where r2.cd_resposta = r1.valor 
					   and r1.cd_enquete = $c 
					   and r2.cd_enquete = $c 
					   and r2.valor <> 0 
					   and ip not like ('%.%') 
					   --and r2.valor <> 6
			           and r3.cd_enquete = r2.cd_enquete 
					   and r1.cd_agrupamento = r3.cd_agrupamento 
					   and r1.cd_enquete = r3.cd_enquete 
					   and r3.indic_escala = 'S' ";
			$rs = pg_query($db, $sql);
			$tpl->assign('codigo', $c);
			if($reg = pg_fetch_array($rs)) {
				$tpl->assign('codigo', $c);
				$v_media1 = $reg['media'];
			}
// ----------------------------------------------------------------------------------------- Média das questões com base 10
			$sql = "select avg(r1.valor) as media from projetos.enquete_resultados r1, projetos.enquete_agrupamentos r3 ";
			$sql = $sql . "where r1.cd_enquete = $c and ip not like ('%.%') ";
			$sql = $sql . "and  r1.cd_agrupamento = r3.cd_agrupamento and r1.cd_enquete = r3.cd_enquete and r3.indic_escala = 'N' ";
			$rs = pg_query($db, $sql);
			$tpl->assign('codigo', $c);
			while ($reg = pg_fetch_array($rs)) {
				$tpl->assign('codigo', $c);
				$v_tot = (($v_media1 * $v_peso1) + $reg['media'] ) / ($v_peso1 + 1);
				$tpl->assign('cont_agrup', ($v_peso1 + 1));
				$tpl->assign('media', number_format($v_tot,2,',','.'));
				$v_tot = (($v_media1 + $reg['media'] ) / 2);
				$tpl->assign('med_agrup', number_format($v_media1,2,',','.'));
				$tpl->assign('percep', number_format($reg['media'],2,',','.'));
				$tpl->assign('media2', number_format($v_tot,2,',','.'));
			}
            
            // Resultados por agrupamento
            /*
			$sql1 = "SELECT cd_agrupamento, nome, indic_escala FROM projetos.enquete_agrupamentos where cd_enquete = $c order by nome";
			$rs1 = pg_query($db, $sql1);
			while ($reg1 = pg_fetch_array($rs1)) {
				$tpl->newBlock('resultado_agrupamento');
				$ag = $reg1['cd_agrupamento'];
				$tpl->assign('agrupamento', $reg1['nome']);
				if ($reg1['indic_escala'] == 'S') {
					$sql = "select avg(valor) as media from projetos.enquete_resultados where cd_enquete = $c and cd_agrupamento = $ag and ip not like ('%.%')  and valor <> 6	";
				}
				else {
					$sql = "select avg(valor) as media from projetos.enquete_resultados where cd_enquete = $c and cd_agrupamento = $ag and ip not like ('%.%') ";
				}
				$rs = pg_query($db, $sql);
				while ($reg = pg_fetch_array($rs)) {
					if ($cor == 1) {
						$tpl->assign('cor_fundo', $v_cor_fundo1);
						$cor = 2;
					}
					else {
						$tpl->assign('cor_fundo', $v_cor_fundo2);
						$cor = 1;
					}
					$tpl->assign('media_grupo', number_format($reg['media'],2,',','.'));
				}
			}
            */	

            // Questões optativas
			/*
            $sql = "select cd_pergunta, texto, soma, media from consultas.resultados_enquete where cd_enquete = $c";
			$rs = pg_query($db, $sql);
			$tpl->newBlock('questoes_optativas');
			$cont = 0;
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('texto_questao');
				$cont = $cont + 1;
				$tpl->assign('codigo', $c);
				$tpl->assign('cd_pergunta', $reg['cd_pergunta']);
				$tpl->assign('texto_questao', '<b>'.$cont.'</b>' .' - <i>'.$reg['texto'].'</i>');
				$tpl->assign('soma_questao', number_format($reg['soma'],0,',','.'));
				$tpl->assign('media_questao', number_format($reg['media'],2,',','.'));
				if ($cor == 1) {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$cor = 2;
				}
				else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$cor = 1;
				}
			}
            */

            // Questão dissertativa
            /*
			$sql = "SELECT descricao FROM projetos.enquete_resultados WHERE cd_enquete = $c AND questao = 'Texto'";
			$rs = pg_query($db, $sql);
			$tpl->newBlock('questoes_dissertativas');
			$cont = 0;
			while ($reg = pg_fetch_array($rs)) {
				$tpl->newBlock('texto_resp');
				$cont = $cont + 1;
				$tpl->assign('texto_resp', '<b>'.$cont.'</b>' .' - <i>'.$reg['descricao'].'</i>');
				if ($cor == 1) {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$cor = 2;
				}
				else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$cor = 1;
				}
			}
            */
		}
//------------------------------------------------------------------------------------------- Lista de servicos
		$sql = "SELECT cd_servico, nome_servico FROM projetos.servicos ORDER BY nome_servico";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('servico');
		$tpl->assign('nome_servico', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('servico');
			$tpl->assign('cd_servico', $reg['cd_servico']);
			$tpl->assign('nome_servico', $reg['nome_servico']);
			$tpl->assign('chk_servico', ($reg['cd_servico'] == $v_servico ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de sites
		$sql = "SELECT cd_site, tit_capa FROM projetos.root_site ORDER BY tit_capa";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('site');
		$tpl->assign('nome_site', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('site');
			$tpl->assign('cd_site', $reg['cd_site']);
			$tpl->assign('nome_site', $reg['tit_capa']);
			$tpl->assign('chk_site', ($reg['cd_site'] == $v_site ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de eventos
		$sql = "SELECT cd_evento, nome FROM projetos.eventos_institucionais ORDER BY nome";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('evento');
		$tpl->assign('nome_evento', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('evento');
			$tpl->assign('cd_evento', $reg['cd_evento']);
			$tpl->assign('nome_evento', $reg['nome']);
			$tpl->assign('chk_evento', ($reg['cd_evento'] == $v_evento ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de publicações
		$sql = "SELECT cd_publicacao, nome_publicacao FROM projetos.publicacoes ORDER BY nome_publicacao";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('publicacao');
		$tpl->assign('nome_publicacao', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('publicacao');
			$tpl->assign('cd_publicacao', $reg['cd_publicacao']);
			$tpl->assign('nome_publicacao', $reg['nome_publicacao']);
			$tpl->assign('chk_publicacao', ($reg['cd_publicacao'] == $v_publicacao ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de divisoes
		$sql = "
					SELECT codigo,
                           nome
					  FROM projetos.divisoes 
					 ORDER BY nome";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('divisao_responsavel');
			$tpl->assign('cd_divisao_responsavel', $reg['codigo']);
			$tpl->assign('ds_divisao_responsavel', $reg['nome']);
			if(trim($cd_divisao_responsavel) != "")
			{
				$tpl->assign('fl_divisao_responsavel', ($reg['codigo'] == $cd_divisao_responsavel ? ' selected' : ''));
			}
			else
			{
				$tpl->assign('fl_divisao_responsavel', ($reg['codigo'] == $_SESSION['D'] ? ' selected' : ''));
			}
		}		
//------------------------------------------------------------------------------------------- Lista de responsáveis
		$sql = "SELECT guerra, divisao, codigo FROM projetos.usuarios_controledi where tipo in ('U', 'N', 'G') ORDER BY divisao, guerra";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('responsavel');
		$tpl->assign('nome_responsavel', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('responsavel');
			$tpl->assign('cd_responsavel', $reg['codigo']);
			$tpl->assign('nome_responsavel', $reg['divisao'].' - '.$reg['guerra']);
			$tpl->assign('chk_responsavel', ($reg['codigo'] == $v_responsavel ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de layouts de enquete
		$sql = "SELECT codigo, descricao FROM listas where categoria = 'LEQT' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('layout');
		$tpl->assign('nome_tipo_layout', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('layout');
			$tpl->assign('cd_tipo_layout', $reg['codigo']);
			$tpl->assign('nome_tipo_layout', $reg['descricao']);
			$tpl->assign('chk_tipo_layout', ($reg['codigo'] == $v_tipo_layout ? ' selected' : ''));
		}
//------------------------------------------------------------------------------------------- Lista de tipos de enquete
		$sql = "SELECT codigo, descricao FROM listas where categoria = 'TEQT' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('tipo_enquete');
		$tpl->assign('nome_tipo_enquete', 'Selecione...');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('tipo_enquete');
			$tpl->assign('cd_tipo_enquete', $reg['codigo']);
			$tpl->assign('nome_tipo_enquete', $reg['descricao']);
			$tpl->assign('chk_tipo_enquete', ($reg['codigo'] == $v_tipo_enquete ? ' selected' : ''));
		}
//-----------------------------------------------
//	}
	pg_close($db);
	$tpl->printToScreen();	
?>