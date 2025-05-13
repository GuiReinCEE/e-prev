<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Enums.php');

include 'oo/start.php';
using( array( 'projetos.eventos_institucionais_inscricao' ) );

class eprev_evento_institucional_inscrito_selecionar
{
	private $db;
	private $campos;

	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();
		$this->save();
	}

	private function requestParams()
	{
		$this->campos['cd_eventos_institucionais_inscricao'] = $_POST['cd_eventos_institucionais_inscricao'];
		$this->campos['fl_selecionado'] = ($_POST['fl_selecionado']=="S")?"S":"N";
	}

	private function save()
	{
		if($this->campos['cd_eventos_institucionais_inscricao']=="")
		{
			echo "false";
		}
		else
		{
			$ret = t_eventos_institucionais_inscricao::selecionar( 
				$this->campos["cd_eventos_institucionais_inscricao"]
				, $this->campos["fl_selecionado"] 
			);
			echo "true";
		}
	}
}
$eu = new eprev_evento_institucional_inscrito_selecionar( $db );
?>