<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_frm_tarefa_checklist.html');

	include('inc/ePrev.Enums.php');
	include('oo/start.php');
	using(array('projetos.tarefa_checklist', 'projetos.tarefas'));
	header( 'location:'.base_url().'index.php/atividade/tarefa_checklist/index/'.$os.'/'.$_REQUEST['c']);
	class eprev_frm_tarefa_checklist
	{
		static function listar_perguntas($fl_tarefa_tipo, $cd_tarefa_checklist_grupo)
		{
			if( $fl_tarefa_tipo=="A" || $fl_tarefa_tipo=="F" || $fl_tarefa_tipo=="R" )
			{
				$tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_ORACLE;
			}
			else
			{
				$tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_WEB;
			}
			$collection = tarefa_checklist::select_1( $tipo, $cd_tarefa_checklist_grupo );
			return $collection;
		}
		
		static function listar_grupos($fl_tarefa_tipo)
		{
			if( $fl_tarefa_tipo=="A" || $fl_tarefa_tipo=="F" || $fl_tarefa_tipo=="R" )
			{
				$tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_ORACLE;
			}
			else
			{
				$tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_WEB;
			}
			$collection = tarefa_checklist::select_3( $tipo );
			return $collection;
		}
		
		static function carregar_tarefa()
		{
			$cd_atividade = $_REQUEST['os'];
			$cd_tarefa = $_REQUEST['c'];
			
			$collection = tarefas::select_1($cd_atividade, $cd_tarefa);
			return $collection;
		}
		
		static function carregar_resposta($cd_tarefas, $cd_tarefa_checklist_pergunta)
		{
			$collection = tarefa_checklist::select_2($cd_tarefas, $cd_tarefa_checklist_pergunta);
			
			if(sizeof($collection)==0)
			{
				$row['fl_resposta']='';
				$row['fl_especialista']='';
			}
			else
			{
				$row = $collection[0];
			}
			
			return $row;
		}
	}

	$tarefa = eprev_frm_tarefa_checklist::carregar_tarefa();
	
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
	$tpl->assign('fl_tipo_grava', strtolower($tarefa[0]['fl_tarefa_tipo']));
	$tpl->assign('cd_tarefa', $_REQUEST['c']);
	$tpl->assign('historico', site_url('atividade/tarefa_historico/index/'.$os.'/'.$_REQUEST['c']));
	$tpl->assign('anexo', site_url('atividade/tarefa_anexo/index/'.$os.'/'.$_REQUEST['c']));
	$tpl->assign('execucao', site_url('atividade/tarefa_execucao/index/'.$os.'/'.$c));

	$cd_tarefas = $tarefa[0]['codigo'];
	$tpl->assign('cd_tarefas', $cd_tarefas);

	$exibir_objetos = true;
	
	// Tarefa liberada não exibe os Options de Verificado/Não verificado
	if($tarefa[0]['status_atual']=="LIBE")
	{
		$exibir_objetos = false;
	}
	// Tarefa concluída não exibe os Options
	elseif($tarefa[0]['status_atual']=="CONC")
	{
		$exibir_objetos = false;
	}
	// Tarefa onde o programador NÃO é o usuário logado não exibe os Options
	elseif($tarefa[0]['cd_recurso']!=$_SESSION["Z"])
	{
		$exibir_objetos = false;
	}
	
	// Tarefa liberada e aberta pelo analista logado permite salvar
	if( $tarefa[0]['status_atual']=="LIBE" && $_SESSION['Z']==$tarefa[0]['cd_mandante'] )
	{
		$tpl->assignGlobal('display_salvar', "");
		$tpl->assignGlobal('display_mensagem_salvar', "none");
	}
	// Tarefa liberada onde o usuário logado não é o analista proibe o botão salvar
	else if( $tarefa[0]['status_atual']=="LIBE" && $_SESSION['Z']!=$tarefa[0]['cd_mandante'] )
	{
		$tpl->assignGlobal('display_salvar', "none");
		$tpl->assignGlobal('display_mensagem_salvar', "");
	}
	// Tarefa concluída proibe o botão salvar
	else if( $tarefa[0]['status_atual']=="CONC" )
	{
		$tpl->assignGlobal('display_salvar', "none");
		$tpl->assignGlobal('display_mensagem_salvar', "");
	}
	else
	{
		$tpl->assignGlobal('display_salvar', "");
		$tpl->assignGlobal('display_mensagem_salvar', "none");
	}

	$grupos = eprev_frm_tarefa_checklist::listar_grupos($tarefa[0]['fl_tarefa_tipo']);

	$zebra = "#C9D0C8";
	foreach( $grupos as $grupo )
	{
		$tpl->newBlock('grupos');
		$tpl->assign( 'zebra', "#ADB5AC" );
		$tpl->assign( 'nome_grupo', $grupo['ds_grupo'] );

		$perguntas = eprev_frm_tarefa_checklist::listar_perguntas($tarefa[0]['fl_tarefa_tipo'] , $grupo["cd_tarefa_checklist_grupo"] );

		foreach( $perguntas as $item )
		{
			$zebra = ($zebra=="#F4F4F4")?"#C9D0C8":"#F4F4F4";
			
			$tpl->newBlock('perguntas');
			$tpl->assign( 'zebra', $zebra );
			$tpl->assign( 'cd_tarefa_checklist_pergunta', $item['cd_tarefa_checklist_pergunta'] );
			$tpl->assign( 'pergunta', $item['ds_pergunta'] );

			$resposta = eprev_frm_tarefa_checklist::carregar_resposta( $cd_tarefas, $item['cd_tarefa_checklist_pergunta'] );
			$tpl->assign( 'checked_resposta_s', ($resposta['fl_resposta']=="S")?"checked":"" );
			$tpl->assign( 'checked_resposta_n', ($resposta['fl_resposta']=="N")?"checked":"" );
			$tpl->assign( 'checked_especialista', ($resposta['fl_especialista']=="S")?"checked":"" );
			$tpl->assign( 'checked_especialista_2', ($resposta['fl_especialista']=="S")?"X":"" );

			if( !$exibir_objetos )
			{
				$tpl->assign( 'exibir_mensagem_verificado', ($resposta['fl_resposta']=="S")?"X":"" );
				$tpl->assign( 'exibir_mensagem_nao_verificado', ($resposta['fl_resposta']=="N")?"X":"" );
				$tpl->assign( 'exibir_objeto', "style='display:none;'" );
	
			}
			else
			{
			}
			$tpl->assign( 'visible_especialista', ($_SESSION['Z']!=$tarefa[0]['cd_mandante'] || $tarefa[0]['status_atual']=="CONC")?"none":"" );
			$tpl->assign( 'visible_especialista_2', ($_SESSION['Z']!=$tarefa[0]['cd_mandante'] || $tarefa[0]['status_atual']=="CONC")?"":"none" );

		}
	}

	// ----------------------------------------------------------------------------

	pg_close($db);
	$tpl->printToScreen();	
?>