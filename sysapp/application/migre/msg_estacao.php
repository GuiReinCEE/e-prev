<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');

header( 'location:'.base_url().'index.php/ecrm/mensagem_estacao/estacao/'.$_REQUEST['u']);
exit;

include 'oo/start.php';

using( array('projetos.usuarios_controledi', 'projetos.mensagem_estacao') );

class msg_estacao
{
	private $usuario = 0;

	function __construct()
	{
		$this->requestParams();
	}

	function start()
	{
		// echo "usuário: " . $this->usuario . "<br />";
		$u = usuarios_controledi::select( array('usuario'=>$this->usuario) );
		$mensagem = t_mensagem_estacao::select_1( $u->items[0]->divisao );
		
		
		
		if( sizeof($mensagem)>0 )
		{
			if(trim($mensagem[0]["url"]) != "")
			{
				$url = str_replace("[USUARIO]",$this->usuario,$mensagem[0]["url"]);
				
				echo '<center><a href="'.$url.'" title="Clique para abrir"><img src="'.$mensagem[0]["arquivo"].'" border="0"></a></center>';
			}
			else
			{
				echo '<center><img src="'.$mensagem[0]["arquivo"].'" border="0"></center>';
			}
		}
		else
		{
			// echo " - NÃO Existe";
		}
	}

	function requestParams()
	{
		$this->usuario = $_GET['u'];
	}
}

$o = new msg_estacao();
$o->start();
?>