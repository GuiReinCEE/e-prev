<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/ecrm/atendimento_recadastro');

    $tpl = new TemplatePower('tpl/tpl_atendimento_recadastro.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->assign( 'cd_usuario_criacao_text', $Z );

    //if ($D=='GAP' || $D=='GI')
    if( true or gerencia_in(array('GAP')) )
    {
		if((isset($_REQUEST['emp'])) and (isset($_REQUEST['re'])))
		{
			$filtro = "?filtrar_hidden=true&FiltroEmpresaText=".$_REQUEST['emp']."&FiltroREText=".$_REQUEST['re']."&FiltroDataGapText=01/01/2008&FiltroDataGap_final_Text=".date('d/m/Y')."&";
		}
		
		$tpl->assign('class_nova', '');
        $tpl->assign('class_movimento', 'abaSelecionada');
		$tpl->assign('url_inicial', 'atendimento_recadastro_partial_lista.php'.$filtro);
    }
    else
    {
        echo( "Sem permiss�o para acessar essa p�gina" );
        exit();
    }

    $tpl->printToscreen();
?>