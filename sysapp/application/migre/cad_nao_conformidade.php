<?php
	require('inc/conexao.php');
	require('inc/sessao.php');
	header("Location: ".site_url("gestao/nc/cadastro/".$_REQUEST['c']));

exit;
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

	if ($tr == 'I')
	{
		$tpl = new TemplatePower('tpl/tpl_inclusao_nao_conf.html');

		// ABAS - BEGIN
		$abas[] = array('aba_lista', 'LISTA', false, 'aba_lista_click()');
		$abas[] = array('aba_cadastro', 'NÃO CONFORMIDADE', true, 'void(0);');
		$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
		$tpl->assignGlobal( 'ABA_END', aba_end('') );
		$tpl->assignGlobal( 'link_lista', site_url("gestao/nc") );
		// ABAS - END
	}
	else
	{
		$tpl = new TemplatePower('tpl/tpl_cad_nao_conf.html');
	}

   	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');

   	$tpl->prepare();
   	$tpl->assign('n', $n);
   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
   	$tpl->assign('divsao', $D);
   	$cod_processo = "";
   	$tpl->newBlock('cadastro');
//-------------------------------------------------------------------------------------------
   	if ($msg == 'E') 
	{
    	echo "<script language='JavaScript'>;alert('Não é possível cadastrar uma data de encerramento se não for informada Ação Corretiva.');</script>";
		$msg='';
	}
//------------------------------------------------------------------------------------------- 
	if (isset($c))	
	{
		$sql = "
				SELECT TO_CHAR(nc.dt_cadastro,'dd/mm/yyyy') AS dt_inclusao, 
				       nc.cd_processo AS processo,                  
				       pp.desc_proc AS desc_processo,             
				       nc.cd_nao_conformidade AS cod_nao_conf,              
				       nc.descricao AS descricao,                 
				       nc.disposicao AS disposicao,                
				       nc.evidencias AS evidencias,				  
				       nc.acao_corretiva AS acao_corretiva,			  
				       nc.causa AS causa,                     
				       TO_CHAR(nc.data_fechamento,'dd/mm/yyyy') AS dt_encerramento,   
				       TO_CHAR(nc.dt_implementacao,'dd/mm/yyyy') AS dt_implementacao,	
				       nc.cd_responsavel AS cod_responsavel,           
				       nc.cd_gerente AS cod_gerente,				  
				       nc.aberto_por AS aberto_por,           
				       nc.numero_cad_nc AS numero_cad_nc,   	      
				       pp.envolvidos AS envolvidos,   			  
				       (SELECT guc.nome                                     
				          FROM projetos.processos gp,                      
				               projetos.usuarios_controledi guc,           
				               projetos.nao_conformidade gnc               
				         WHERE gnc.cd_gerente = guc.codigo                
				           AND gnc.cd_processo = gp.cd_processo       
				           AND gnc.cd_nao_conformidade = nc.cd_nao_conformidade) AS gerente, 
				       (SELECT puc1.nome                                    
				          FROM projetos.processos pp1,                     
				               projetos.usuarios_controledi puc1,          
				               projetos.nao_conformidade pnc               
				         WHERE pnc.aberto_por = puc1.codigo           
				           AND pnc.cd_processo = pp1.cd_processo      
				           AND pnc.cd_nao_conformidade = nc.cd_nao_conformidade ) AS responsavel 
				          FROM projetos.nao_conformidade    nc,                       
				               projetos.processos           pp                       
				 WHERE nc.cd_processo             = pp.cd_processo           
				   AND nc.cd_nao_conformidade = ".$c;
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$cod_nao_conf = $reg['cod_nao_conf'];
		$tpl->assign('dt_cadastro',     $reg['dt_inclusao']);
		$tpl->assign('num_nao_conf',    $reg['cod_nao_conf']);
		$tpl->assign('nac',    		 $cod_nao_conf);
		$tpl->assign('pro',    		 $reg['processo']);
		$tpl->assign('cod_nao_conf',    $cod_nao_conf);
		$tpl->assign('processo',        $reg['processo']);
        $tpl->assign('responsavel',     $reg['responsavel']);
		$tpl->assign('gerente',		 $reg['gerente']);
		$tpl->assign('causa',           $reg['causa']);
		$tpl->assign('causa_print',       nl2br(htmlentities($reg['causa'])));
        $tpl->assign('dt_encerramento', $reg['dt_encerramento']);
        $tpl->assign('descricao',       $reg['descricao']);
        $tpl->assign('descricao_print',       nl2br(htmlentities($reg['descricao'])));
		$tpl->assign('disposicao',		 $reg['disposicao']); 
		$tpl->assign('disposicao_print',       nl2br(htmlentities($reg['disposicao'])));
		$tpl->assign('evidencias',      $reg['evidencias']);
		$tpl->assign('evidencias_print',       nl2br(htmlentities($reg['evidencias'])));
		$tpl->assign('acao_corretiva',  $reg['acao_corretiva']);
		$tpl->assign('cod_gerente',  	 $reg['cod_gerente']);
        $tpl->assign('dt_implementacao', $reg['dt_implementacao']);
 		$tpl->assign('numero_cad',  	conv_num_nc($reg['cod_nao_conf']));
  		$tpl->assign('envolvidos',  	$reg['envolvidos']);
        $tpl->assign('data_cad',        $reg['data_cad']);
		///if ($Z != 100) { $tpl->assign('ro_data_encerr', 'readonly'); }
		$cod_processo = $reg['processo'];
		$responsavel = $reg['cod_responsavel'];
		$cod_aberto_por = $reg['aberto_por'];
		$cod_gerente = $reg['cod_gerente'];
		$cod_nao_conf = $reg['cod_nao_conf'];
//---------------------------------------------------- Busca informações da Ação Corretiva:
		$sql = "SELECT TO_CHAR(dt_efe_verif,'dd/mm/yyyy') AS dt_efe_verif 
		          FROM projetos.acao_corretiva 
				 WHERE cd_acao = ".$c;				
		$rs2 = pg_exec($db, $sql);
		$reg2 = pg_fetch_array($rs2);
		$tpl->assign('dt_verif_eficacia', $reg2['dt_efe_verif']);
	}
	else {
		$sql = "SELECT MAX(cd_nao_conformidade) as num 
		          FROM projetos.nao_conformidade";				
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$date = date("d/m/Y");
		$cod_nao_conf = ($reg['num'] + 1);
		if (substr($cod_nao_conf, 0, 4) != date('Y')) { $cod_nao_conf = date('Y') . '001'; }
		if ($cod_nao_conf <= 2004099) { $cod_nao_conf = 2005001; }
		if ($cod_nao_conf == 1) { $cod_nao_conf = 2004078; }
		$tpl->assign('num_nao_conf', $reg['num']);
		$tpl->assign('cod_nao_conf', $cod_nao_conf);
		$tpl->assign('numero_cad',   conv_num_nc($cod_nao_conf));
		$tpl->assign('nac',    		 $cod_nao_conf);					// garcia - 06/04/2004
		$tpl->assign('dt_cadastro',  $date);
	}
//---------------------------------------------------------- Limita alguns campos ao responsável:
	if ($Z == $responsavel) {
	}
	else {
		$tpl->assign('ro_resp', 'readonly');
	}
//----------------------------------------------------------
	if ($tr == 'U') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	$tpl->assign('insere', $n);
//---------------------------------------------------------- Processo que esta ocorrendo a nao conformidade
// garcia - 09/02/2004
	$sql = "SELECT cd_processo AS processo, 
	               procedimento AS nome_processo 
              FROM projetos.processos 
			 ORDER BY nome_processo ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('processo');
		$tpl->assign('codigo_processo', $reg['processo']);
		$tpl->assign('nome_processo', $reg['nome_processo']);
		if (($reg['processo'] == $cp) or ($reg['processo'] == $cod_processo))  { $tpl->assign('sel_processo', 'selected'); }
	}
//---------------------------------------------------- Quem abriu a NC
	$sql = "SELECT u.codigo AS codigo_aberto_por, 
	               u.nome AS nome 
		      FROM projetos.usuarios_controledi u ";
	if (isset($cp))  
	{
		$sql.= " WHERE u.codigo = ".$cp."  
		           AND u.tipo NOT IN ('X', 'P')";
	} 
	else 
	{
		$sql.= " WHERE u.tipo NOT IN ('X', 'P') ";
	}
	$sql.= " ORDER BY nome ";
	$rs = pg_exec($db, $sql);
// --------------------------------------------------- lista de usuarios habilitados a abrirem não conformidade(todos):
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('aberto_por');
		$tpl->assign('codigo_aberto_por', $reg['codigo_aberto_por']);
		$tpl->assign('aberto_por', $reg['nome']);
		if (isset($cod_aberto_por)) {
			if ($reg['codigo_aberto_por'] == $cod_aberto_por) { $tpl->assign('sel_aberto_por', ' selected'); }
		}
		else {
			if ($reg['codigo_aberto_por'] == $Z) { 
				$tpl->assign('sel_aberto_por', ' selected'); 
			}
		}
	}
// ---------------------------------------------------- Gerente da área da NC
	$sql = " SELECT u.codigo AS codigo_ger, 
	                u.nome AS nome 
			   FROM projetos.usuarios_controledi u 
			  WHERE u.tipo = 'G'  
 		      ORDER BY nome ";
	$rs = pg_exec($db, $sql);
// --------------------------------------------------- lista de gerentes
	$tpl->newBlock('gerente');
	$tpl->assign('cod_gerente', 'NULL');
	$tpl->assign('gerente', '&nbsp;');
	if ($tr == 'I') { $tpl->assign('sel_gerente', ' selected'); }
	while ($reg=pg_fetch_array($rs))
	{				
		$tpl->newBlock('gerente');
		$tpl->assign('cod_gerente', $reg['codigo_ger']);
		$tpl->assign('gerente', $reg['nome']);
		if ($reg['codigo_ger'] == $cod_gerente) { $tpl->assign('sel_gerente', ' selected'); }
	}
// --------------------------------------------------- Responsável pela nao conformidade
	$tpl->newBlock('responsavel');
	$sql = " SELECT codigo AS codigo_responsavel, 
	                nome AS nome_responsavel 
	           FROM projetos.usuarios_controledi  
			  WHERE tipo not in ('X', 'P')
			  ORDER BY nome ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('responsavel');
		$tpl->assign('cod_responsavel', $reg['codigo_responsavel']);
		$tpl->assign('responsavel', $reg['nome_responsavel']);
		if ($reg['codigo_responsavel'] == $responsavel) { $tpl->assign('sel_responsavel', ' selected'); }
	}	 
// --------------------------------------------------- Lista os acompanhamentos das nao conformidades
	
    $sql = " SELECT cd_acompanhamento, 
	                TO_CHAR(a.data,'dd/mm/yyyy') AS data, 
					a.situacao,                          
					u.nome AS auditor                    
			   FROM projetos.usuarios_controledi u,
			        projetos.acompanhamento a
			  WHERE a.auditor = u.codigo ";
    if ((isset($tr)) and ($tr != 'I')) 
	{
      	$sql.= " AND a.cd_nao_conformidade = ".$cod_nao_conf;
    }
	if ((! isset($tr)) or ($tr == 'I')) 
	{
      	$sql.= " AND a.cd_nao_conformidade = 0  ";
	}
    $sql.= " ORDER BY a.data desc ";
	$rs = pg_exec($db, $sql);	  
    while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('acompanhamento');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		
		$tpl->assign('dt_acomp', $reg['data']);
        $tpl->assign('situacao', nl2br($reg['situacao']));
		$tpl->assign('auditor', $reg['auditor']);
	}	
	
	$tpl->newBlock('codigo');
	$tpl->assign('cod_processo', $cod_processo);
    $tpl->assign('cod_nao_conf', $cod_nao_conf); 
//---------------------------------------------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
//---------------------------------------------------------------------------------------------
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
//---------------------------------------------------------------------------------------------
	function conv_num_nc($n) {
// Pressupõe que o num esteja no formato AAAANNN
		$aaaa = substr($n, 0, 4);
		$nc = substr($n, 4, 3);
		return $nc.'/'.$aaaa;
	}
?>