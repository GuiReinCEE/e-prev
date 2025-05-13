<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Enums.php');

include 'oo/start.php';
using( array( 'projetos.eventos_institucionais_inscricao' ) );

class eprev_evento_institucional_inscrito_save
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
		$this->campos['cd_eventos_institucionais'] = $_POST['cd_eventos_institucionais'];
		$this->campos['cd_empresa'] = $_POST['cd_empresa'];
		$this->campos['cd_registro_empregado'] = $_POST['cd_registro_empregado'];
		$this->campos['seq_dependencia'] = $_POST['seq_dependencia'];
		$this->campos['nome'] = $_POST['nome'];
		$this->campos['telefone'] = $_POST['telefone'];
		$this->campos['email'] = $_POST['email'];
		$this->campos['observacao'] = $_POST['observacao'];
		$this->campos['cadastro_por'] = $_SESSION['N'];
		$this->campos['tipo'] = $_POST['tipo'];
		$this->campos['tp_inscrito'] = $_POST['tp_inscrito'];
		$this->campos['endereco'] = $_POST['endereco'];
		$this->campos['cidade'] = $_POST['cidade'];
		$this->campos['cep'] = $_POST['cep'];
		$this->campos['uf'] = $_POST['uf'];
		$this->campos['fl_desclassificado'] = ($_POST['fl_desclassificado']=="S")?"S":"N";
		$this->campos['fl_selecionado'] = ($_POST['fl_selecionado']=="S")?"S":"N";
		$this->campos['ds_motivo'] = $_POST['ds_motivo'];
		$this->campos['empresa'] = $_POST['empresa'];
	}

	private function save()
	{
		if($this->campos['cd_eventos_institucionais_inscricao']=="")
		{
			$codigo = t_eventos_institucionais_inscricao::insert( $this->campos );
			$ret = $codigo;
			header('location:evento_institucional_inscrito.php');
		}
		else
		{
			$ret = t_eventos_institucionais_inscricao::update( $this->campos );
			$codigo = $this->campos['cd_eventos_institucionais_inscricao'];
			header('location:evento_institucional_inscrito.php?cd=' . $codigo);
		}
		if( $ret )
		{
			// header('location:evento_institucional_inscrito.php?cd=' . $codigo);
		}
	}
}
$eu = new eprev_evento_institucional_inscrito_save( $db );
?>