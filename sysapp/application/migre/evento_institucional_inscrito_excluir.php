<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Enums.php');

include 'oo/start.php';
using( array( 'projetos.eventos_institucionais_inscricao' ) );

class eprev_evento_institucional_inscrito_excluir
{
	private $campos;

	function __construct()
	{
		$this->requestParams();
		
		$this->save();
	}

	private function requestParams()
	{
		if( isset($_POST['cd_eventos_institucionais_inscricao']) )
		{
			$this->campos['cd_eventos_institucionais_inscricao'] = $_POST['cd_eventos_institucionais_inscricao'];
		}
	}

	private function save()
	{
		if( $this->campos['cd_eventos_institucionais_inscricao']!="" )
		{
			$ret = t_eventos_institucionais_inscricao::delete( $this->campos['cd_eventos_institucionais_inscricao'] );
			$codigo = $this->campos['cd_eventos_institucionais_inscricao'];
			if( $ret )
			{
				header('location:evento_institucional_inscricao.php');
			}
		}
		
		echo $this->campos['cd_eventos_institucionais_inscricao'];
	}
}

$eu = new eprev_evento_institucional_inscrito_excluir();
?>