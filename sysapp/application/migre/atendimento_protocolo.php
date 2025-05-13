<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');

    header( 'location:'.base_url().'index.php/ecrm/atendimento_protocolo');

    $tpl = new TemplatePower('tpl/tpl_atendimento_protocolo.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->assign( 'cd_usuario_criacao_text', $Z );

    if ($D=='GAD')
    {
        $tpl->assign('exibir_aba_nova', 'display:none');
        $tpl->assign('class_movimento', 'abaSelecionada');
		$tpl->assign('url_inicial', 'atendimento_protocolo_partial_lista.php');
	}
    elseif ($D=='GAP' || $D=='GI')
    {
		if((isset($_REQUEST['emp'])) and (isset($_REQUEST['re'])))
		{
			$filtro = "?filtrar_hidden=true&FiltroEmpresaText=".$_REQUEST['emp']."&FiltroREText=".$_REQUEST['re']."&FiltroDataGapText=01/01/2008&FiltroDataGap_final_Text=".date('d/m/Y')."&";
		}
        
		$tpl->assign('class_movimento', 'abaSelecionada');
		$tpl->assign('url_inicial', 'atendimento_protocolo_partial_lista.php'.$filtro);
    }
    else
    {
        echo( "Sem permissão para acessar essa página" );
        exit();
    }

    $tpl->printToscreen();
?>