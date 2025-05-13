<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/atividade/tarefa_historico/index/'.$os.'/'.$c);
	
	$tpl = new TemplatePower('tpl/tpl_frm_hist_tarefa.html');
// ----------------------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
   	$tpl->assign('os', $os);
	$tpl->assign('origem', $os);
	$tpl->assign( "site_url", site_url());
	$tpl->assign('fl_tipo_grava', $f);
// ----------------------------------------------------------------------------
	if ($c != '')	{  // Tarefa criada para programador
		$sql =   " ";
		$sql = $sql . " select t.cd_atividade	as cd_atividade,   	";
		$sql = $sql . "	t.cd_tarefa	as cd_tarefa, ";
		$sql = $sql . "	t.cd_recurso as cd_recurso, ";
		$sql = $sql . " t.programa as programa,	";
		$sql = $sql . " to_char(t.dt_inicio_prev,'dd/mm/yyyy') as dt_inicio_prev, ";
		$sql = $sql . " to_char(t.dt_fim_prev,'dd/mm/yyyy') as dt_fim_prev,	";
		$sql = $sql . " to_char(t.dt_hr_inicio,'dd/mm/yyyy hh:mi:ss') as dt_hora_inicio, ";
		$sql = $sql . " to_char(t.dt_hr_fim,'dd/mm/yyyy hh:mi:ss') as dt_hora_fim, ";
		$sql = $sql . " t.duracao as duracao,	";
		$sql = $sql . "	t.descricao as descricao, ";
		$sql = $sql . "	t.observacoes as observacoes, ";
		$sql = $sql . "	t.casos_testes as casos_testes, ";
		$sql = $sql . "	t.tabs_envolv as tabs_envolv, ";
		$sql = $sql . "	t.imagem		as imagem,			";
		$sql = $sql . " to_char(t.hr_inicio,'hh:mi:ss') as hr_inicio,	";
		$sql = $sql . " to_char(t.dt_fim,'dd/mm/yyyy') as dt_fim,	";
		$sql = $sql . " to_char(t.hr_fim,'hh:mi:ss') as hr_fim,		";		
		$sql = $sql . " t.cd_mandante as cd_mandante,		";
		$sql = $sql . " to_char(t.dt_inicio_prog,'dd/mm/yyyy hh24:mi:ss') as dt_inicio_prog, ";
		$sql = $sql . " to_char(t.dt_fim_prog,'dd/mm/yyyy hh24:mi:ss') as dt_fim_prog, ";
		$sql = $sql . " to_char(t.dt_ok_anal,'dd/mm/yyyy hh24:mi:ss') as dt_ok_anal, ";
		$sql = $sql . " t.fl_checklist ";
		$sql = $sql . "  from 	projetos.tarefas t	";
		$sql = $sql . "  where t.cd_atividade = $os	";
		$sql = $sql . "  and t.cd_tarefa = $c	";

        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $c);
		$tpl->assign('cd_tarefa', $reg['cd_tarefa']);
		$tpl->assign('dt_inicio', $reg['dt_inicio_prev']);
		$tpl->assign('dt_fim', $reg['dt_fim_prev']);
		$tpl->assign('tp_tarefa', $reg['tipo']);
		$tpl->assign('cd_habil', $reg['cd_habilidade']);
		$tpl->assign('descricao', str_replace(chr(13).chr(10), "<br>", $reg['descricao']));
		$tpl->assign('obs', $reg['observacoes']);
		$tpl->assign('dur_ant', $reg['duracao']);
		$tpl->assign('casos_testes', str_replace(chr(13).chr(10), "<br>", $reg['casos_testes']));
		$tpl->assign('tabs_envolv', str_replace(chr(13).chr(10), "<br>", $reg['tabs_envolv']));
        $tpl->assign('dt_cadastro',  $reg['dt_cadastro']);
        $tpl->assign('hr_inicio_real',  $reg['hr_inicio']);
        $tpl->assign('dt_fim_real',  $reg['dt_fim']);
        $tpl->assign('hr_fim_real',  $reg['hr_fim']);
		$tpl->assign('dt_ok_anal',  $reg['dt_ok_anal']);
		$tpl->assign('dt_fim_prog',  $reg['dt_fim_prog']);
		$tpl->assign('dt_inicio_prog',  $reg['dt_inicio_prog']);
		$tpl->assign('imagem',  $reg['imagem']);
		
		$tpl->assign('ver_checklist', ( $reg['fl_checklist']=='S' )?'display:;':'display:none;' );
		
		$cd_atividade = $reg['cd_atividade'];
		$v_cd_tarefa = $reg['cd_tarefa'];
		$v_cd_recurso = $reg['cd_recurso'];
		$v_cd_mandante = $reg['cd_mandante'];
		$v_programa = $reg['programa'];
	}
	
		$tthis->load->model('projetos/Tarefa_historico_model');
		
		$tthis->Tarefa_historico_model->lista(intval($c), intval($os), $col);
		$head = array('Evento','Responsável','Data','Status','Complemento', 'Motivo');
		$num=0;
		foreach($col as $item)
		{
			$num++;
		    $body[] = array($num, $item['responsavel'], $item['data'], $item['status_atual'], $item['complemento'], $item['motivo']);
		}
		$tthis->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;

		$tpl->assignGlobal('GRID_HISTORICO',$grid->render());
	
	/*$sql = " " ;
	$sql  = " SELECT "; 
	$sql .= " 	h.cd_atividade, "; 
	$sql .= "  	h.cd_tarefa, "; 
	$sql .= "  	h.cd_recurso, "; 
	$sql .= "  	TO_CHAR(h.timestamp_alteracao,'dd/mm/yyyy - hh24:mi:ss') as data, "; 
	$sql .= "  	h.descricao, ";    
	$sql .= "  	u.guerra as responsavel, ";    
	$sql .= "  	CASE WHEN (status_atual='AMAN') THEN 'Aguardando Manutenção' "; 
	$sql .= "  	     WHEN (status_atual='EMAN') THEN 'Em Manutenção' "; 
	$sql .= "  	     WHEN (status_atual='LIBE') THEN 'Liberada' "; 
	$sql .= "  	     WHEN (status_atual='CONC') THEN 'Concluída' "; 
	$sql .= " 		 WHEN (h.status_atual='CANC') THEN 'Cancelada'
					 WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
					 WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = h.cd_atividade)='SUSP') THEN 'Atividade Suspensa'
					 WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)'"; 	
	$sql .= "    END as status_atual,
                h.ds_obs	";
	$sql .= " FROM "; 
	$sql .= " 	projetos.tarefa_historico h, ";
	$sql .= " 	projetos.usuarios_controledi u	 "; 
	$sql .= " WHERE ";
	$sql .= " 	h.cd_recurso 	= u.codigo AND ";
	$sql .= "  	h.cd_tarefa 	= $c AND";
	$sql .= "  	h.cd_atividade 	= $os ";
	$sql .= " ORDER BY ";
	$sql .= " 	timestamp_alteracao ";
	$sql = utf8_encode($sql);	
	 
		$rs = pg_exec($db, $sql);
		$num = 1;
		while ($reg = pg_fetch_array($rs))
		{
			$tpl->newBlock('historico');
			$tpl->assign('evento', $num);
			$tpl->assign('cod_os', $n);
			$tpl->assign('codtarefa', $reg['cd_tarefa']);
			$tpl->assign('responsavel', $reg['responsavel']);
			$tpl->assign('situacao', utf8_decode($reg['status_atual']));
			$tpl->assign('data', $reg['data']);
			$tpl->assign('descricao', $reg['descricao']);
			$tpl->assign('motivo', $reg['ds_obs']);
			$num = ($num + 1);
		}	*/


	pg_close($db);
	$tpl->printToScreen();	
?>