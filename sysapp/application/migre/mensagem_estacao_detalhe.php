<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/class.TemplatePower.inc.php');

include 'oo/start.php';
using( array( 'projetos.mensagem_estacao', 'projetos.divisoes', 'projetos.mensagem_estacao_gerencia' ) );
header( 'location:'.base_url().'index.php/ecrm/mensagem_estacao/cadastro/');
class eprev_mensagem_estacao_detalhe
{
	private $db;
	private $filtro = array();
	private $comando = "";
	public $edicao = false;
	public $registro;

	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();
		
		if($this->comando=="data_existe")
		{
			$this->data_existe();
		}
		else
		{
			$this->load();
		}

		// comando preenchido quando página apenas deve executar algo e abandonar
		// próprio para chamadas de comandos em ajax
		if( $this->comando!="" ) exit;
	}

	private function requestParams()
	{
		if(isset($_POST['comando']))
		{
			$this->comando = $_POST['comando'];
		}
		if(isset($_REQUEST['cd']))
		{
			$this->filtro['cd_mensagem_estacao'] = $_REQUEST['cd'];
			$this->edicao = true;
		}
	}

	private function data_existe()
	{
		// uso para verificar se data já existe em chamada ajax
		if(isset($_POST['dt_inicial']))
		{
			$this->filtro['dt_inicial'] = $_POST['dt_inicial'];
		}
		if(isset($_POST['cd_mensagem_estacao']))
		{
			$this->filtro['cd_mensagem_estacao'] = $_POST['cd_mensagem_estacao'];
		}
		
		if($this->filtro['cd_mensagem_estacao']=="")
		{
			$this->filtro['cd_mensagem_estacao'] = 0;
		}
		
		$b = t_mensagem_estacao::existe_na_data($this->filtro);
		echo ($b)?"true":"false";
	}

	private function load()
	{
		$this->registro = t_mensagem_estacao::select_pk($this->filtro['cd_mensagem_estacao']);
	}
}

$eu = new eprev_mensagem_estacao_detalhe( $db );
$registro = $eu->registro;

#echo "<PRE>".print_r($registro,true)."</PRE>"; exit;


// -----------

    $tpl = new TemplatePower('tpl/tpl_mensagem_estacao_detalhe.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->assign('display_inclusao', ($eu->edicao)?'display:none;':'display:;');
    $tpl->assign('display_edicao', ($eu->edicao)?'display:;':'display:none;');
    if($eu->edicao)
    {
		$tpl->assign('cd_mensagem_estacao', $eu->registro['cd_mensagem_estacao']);
		$tpl->assign('nome', $eu->registro['nome']);
		$tpl->assign('dt_inicial', $eu->registro['dt_inicial']);
		$tpl->assign('arquivo', '<img src="'. str_replace( 'http://', 'https://', $eu->registro['arquivo']) .'" />');
		
		
    }
	
	$tpl->assign('url_link', $eu->registro['url']);
    
    // gerencias
    $gerencias_selecionadas = t_mensagem_estacao_gerencia::select_1( $eu->registro['cd_mensagem_estacao'] );
    
    if(sizeof($gerencias_selecionadas)==0)
    {
    	$tpl->assign( "checked_todas", " checked " );
    	$tpl->assign( "style_gerencias", " display:none; " );
    }
    else if(sizeof($gerencias_selecionadas)==1)
    {
    	if( $gerencias_selecionadas[0]['gerencia']=="ALL" )
    	{
    		$tpl->assign( "checked_todas", " checked " );
    		$tpl->assign( "style_gerencias", " display:none; " );
    	}
    	else
    	{
    		$tpl->assign( "checked_gerencias", " checked " );
    		$tpl->assign( "style_gerencias", " display:; " );
    	}
    }
    else
    {
    	$tpl->assign( "checked_gerencias", " checked " );
    	$tpl->assign( "style_gerencias", " display:; " );
    }

    $gerencias = divisoes::select_1();
    foreach($gerencias as $gerencia)
    {
	    $tpl->newBlock("gerencias");
	    $tpl->assign("gerencia_sigla", $gerencia['codigo']);
	    $tpl->assign("gerencia_nome", $gerencia['nome']);
	    foreach($gerencias_selecionadas as $gselecionada)
	    {
	    	if(trim($gselecionada['gerencia'])==trim($gerencia['codigo']))
	    	{
	    		$tpl->assign("checked_gerencia", " checked ");
	    	}
	    }
    }

    $tpl->printToscreen();
?>