<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	#require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_tabelas_atualizar.html');

        header( 'location:'.base_url().'index.php/servico/tabelas_atualizar/index');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	if ($sel == '') { $sel = 'D'; }

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if ($_SESSION['D'] != 'GI') 
	{
   		header('location: acesso_restrito.php?IMG=banner_tabelas_atualizar');
	}	
	
	switch($sel) {
		case 'D': $flt = 'fl_diario'; break;
		case 'M': $flt = 'fl_mensal'; break;
		case 'S': $flt = 'fl_sincronizado'; break;
		case 'E': $flt = 'fl_eventual'; break;
		case 'I': $flt = 'fl_inativa'; break;
		default : $flt = 'fl_diario';
	}
	
	$tpl->assign($flt, 'class="abaSelecionada"');
//--------------------------------------------------------------	
	$tpl->newBlock('lista');
	$sql =        "  
	                 SELECT tabela, 
	                        TO_CHAR(dt_ult_atualizacao, 'DD/MM/YYYY HH24:MI:SS.MS') AS dt_atualizacao, 
							TO_CHAR(dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
							TO_CHAR(dt_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_final,
							TO_CHAR(dt_final - dt_inicio,'HH24:MI:SS') AS hr_tempo,
							CASE WHEN (dt_final - dt_inicio) > '00:00:00'::interval OR (dt_final - dt_inicio) IS NULL 
							     THEN ''
								 ELSE 'ERRO '
							END	AS fl_erro,
							CASE WHEN tipo_bd = 'O' THEN 'ORACLE'
							     WHEN tipo_bd = 'U' THEN 'URA TOI'
							     WHEN tipo_bd = 'T' THEN 'URA TELEDATA'
							     ELSE 'NÃO ESPEC'
							END AS bd_origem,							
	                        num_registros, 
							num_registros_atualizados,
							CASE 	when periodicidade = 'M' then 'Mensal'
									when periodicidade = 'D' then 'Diária'
									when periodicidade = 'E' then 'Eventual'
									when periodicidade = 'I' then 'Inativa'
									when periodicidade = 'S' then 'Sincronizada'
									else periodicidade
							END 	as periodicidade, 
 							truncar, 
							CASE WHEN TRIM(condicao) = '' OR condicao IS NULL 
							     THEN 'N'
								 ELSE 'S'
							END AS condicao,
							truncar,
							qt_total_registro
					   FROM projetos.tabelas_atualizar 
					  WHERE periodicidade = '" . $sel . "'
					    ".($_REQUEST["tp_bd"] == "" ? "" : ($_REQUEST["tp_bd"] == "A" ? "AND access_callcenter = 'L' " : "AND oracle = 'L'"))."
					  ORDER BY dt_inicio DESC
				 ";
	$rs=pg_query($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('tabela');


		$tpl->assign('tabela', $reg['tabela']);
		
		$tpl->assign('dt_inicio', $reg['dt_inicio']);
		$tpl->assign('dt_final', $reg['dt_final']);
		$tpl->assign('hr_tempo', "<font color='red'> ".$reg['fl_erro']." </font>".$reg['hr_tempo']);
		
		$tpl->assign('periodicidade', $reg['periodicidade']);//." (".$reg['truncar'].")");
		if ($reg['periodicidade'] == 'Inativa') {
			$tpl->assign('cor_fundop', '#DDAAAA');
			$tpl->assign('classe_link', 'links2vermelho');
		} elseif ($reg['periodicidade'] == 'Sincronizada') {
			$tpl->assign('cor_fundop', '#99FFCC');
			$tpl->assign('classe_link', 'links2verde');
		}
		
		$tpl->assign('condicao', $reg['truncar']." - ".$reg['condicao']);
		
		$tpl->assign('bd_origem', $reg['bd_origem']);
		
		$tpl->assign('access', $reg['access_callcenter']);
		$tpl->assign('postgres', $reg['postgres']);
		$tpl->assign('nregs', $reg['num_registros_atualizados']);
		$tpl->assign('qt_origem', $reg['num_registros']);
		
		$cor = "";
		if($reg['num_registros'] != $reg['qt_total_registro'])
		{
			$cor = "<B>";
		}
		if($reg['num_registros'] < $reg['qt_total_registro'])
		{
			$cor = "<font color='red'><b>";
		}
		
		$tpl->assign('qt_total', $cor.$reg['qt_total_registro']);
		$tpl->assign('oracle', $reg['oracle']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>