<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_avaliacao_config.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    if( isset($_REQUEST['lbu']) )
    {	
		if($_REQUEST['lbu'] == 'relatorio')
		{
			header( 'location:'.base_url().'index.php/cadastro/matriz/avaliacao_relatorio');
		}
		else if($_REQUEST['lbu'] == 'matriz')
        {
            header( 'location:'.base_url().'index.php/cadastro/matriz/index');
        }
		else if($_REQUEST['lbu'] == 'manutencao')
        {
            header( 'location:'.base_url().'index.php/cadastro/avaliacao_manutencao');
        }
		else if($_REQUEST['lbu'] == 'conceito')
        {
            header( 'location:'.base_url().'index.php/cadastro/conceito/cadastro');
        }
		else if($_REQUEST['lbu'] == 'nomearcomite')
        {
            header( 'location:'.base_url().'index.php/cadastro/avaliacao_comite');
        }
		
        $tpl->assign('load_by_url', $_REQUEST['lbu']);
        $tpl->assign('cd_divisao', $_REQUEST['cd_divisao']);
    }
    else
    {
        $tpl->assign('load_by_url', '');
    }

    if ($INDIC_09!='*')
    {
        header( 'Location: acesso_restrito.php?IMG=' );
    }

    $tpl->printToscreen();
?>