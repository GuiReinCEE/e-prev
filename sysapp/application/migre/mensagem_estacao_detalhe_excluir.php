<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Enums.php');

include 'oo/start.php';
using( array( 'projetos.mensagem_estacao' ) );

class eprev_mensagem_estacao_excluir
{
	private $campos;

	function __construct()
	{
		$this->requestParams();
		
		$this->save();
	}

	private function requestParams()
	{
		if( isset($_POST['cd_mensagem_estacao']) )
		{
			$this->campos['cd_mensagem_estacao'] = $_POST['cd_mensagem_estacao'];
		}
	}

	private function save()
	{
		if( $this->campos['cd_mensagem_estacao']!="" )
		{
			$ret = t_mensagem_estacao::delete( $this->campos['cd_mensagem_estacao'] );
			if( $ret )
			{
				header('location:mensagem_estacao.php');
			}
		}
	}
}

$eu = new eprev_mensagem_estacao_excluir();
?>