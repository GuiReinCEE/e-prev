<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Enums.php');

include 'oo/start.php';
using( array( 'projetos.mensagem_estacao' ) );

class eprev_mensagem_estacao_detalhe_save
{
	private $db;
	private $campos;
	
	// PRODUÇÃO
	private $UPLOAD_PATH = "/u/www/upload/mensagem_estacao/";
	private $UPLOAD_URL = "http://www.e-prev.com.br/upload/mensagem_estacao/";
	
	// DESENVOLVIMENTO
	//private $UPLOAD_PATH = "/u/www/upload/mensagem_estacao/";
	//private $UPLOAD_URL = "http://10.63.255.222/upload/mensagem_estacao/";
	
	// LOCAL
	//private $UPLOAD_PATH = "upload/mensagem_estacao/";
	//private $UPLOAD_URL = "http://10.63.255.94/controle_projetos/upload/mensagem_estacao/";

	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();
		$this->save();
	}

	private function requestParams()
	{
		$this->campos['cd_mensagem_estacao'] = $_POST['cd_mensagem_estacao'];
		$this->campos['nome'] = $_POST['nome'];
		$this->campos['dt_inicial'] = $_POST['dt_inicial'];
		$this->campos['url'] = $_POST['url_link'];
		$this->campos['cd_usuario'] = $_SESSION['Z'];

		//var_dump($_POST['publico']); exit;
		if($_POST['publico']=="ALL")
		{
			$this->campos['gerencias'] = array();
		}
		else
		{
			$this->campos['gerencias'] = $_POST['gerencias'];
		}
	}

	private function save()
	{
		if($this->campos['cd_mensagem_estacao']=="")
		{
			$anexo_enviado = $this->enviar_anexo();
			if( $anexo_enviado )
			{
				$this->campos['arquivo'] = $this->UPLOAD_URL . $this->upload_filename;
				$codigo = t_mensagem_estacao::insert( $this->campos );
				$ret = $codigo;
				header('location:mensagem_estacao_detalhe.php?cd='.$codigo);
			}
			else
			{
				if( sizeof($this->upload_error)!=0 )
				{
					echo "Erro ao tentar enviar o arquivo<br><br>" . $this->upload_error . "<br><br>Informe a equipe de informática sobre o problema.";
					var_dump($this->upload_error);
				}
			}
		}
		else
		{
			$anexo_enviado = $this->enviar_anexo();
			if( sizeof($this->upload_error)==0 )
			{
				if($this->upload_filename!="")
				{
					$this->campos['arquivo'] = $this->UPLOAD_URL . $this->upload_filename;
				}
				else
				{
					$this->campos['arquivo'] = "";
				}
				$ret = t_mensagem_estacao::update( $this->campos );
				$codigo = $this->campos['cd_mensagem_estacao'];
				header('location:mensagem_estacao_detalhe.php?cd=' . $codigo);
			}
			else
			{
				echo "Erro ao tentar enviar o arquivo<br><br>" . $this->upload_error . "<br><br>Informe a equipe de informática sobre o problema.";
			}
		}
	}

	private function enviar_anexo()
	{
		$config = array();
		
		$arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;
		
		// Tamanho máximo do arquivo (em bytes)
		$config["tamanho"] = 10485760;
		// Largura máxima (pixels)
		//$config["largura"] = 350;
		// Altura máxima (pixels)
		//$config["altura"]  = 180;
		
		if( $arquivo )
		{
			$UPLOAD_ERR_NO_FILE = 4;
			if( $arquivo['error'] == $UPLOAD_ERR_NO_FILE )
			{
				// se arquivo não informado, a operação deve ser abortada
				// sem que um erro seja anotado na propriedade $this->upload_error
				return false;
			}

			if( ! eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $arquivo["type"]) )
			{
				$this->upload_error[] =  "type{" . $arquivo["type"] . "}  Arquivo em formato inválido! A imagem deve ser jpg, jpeg, bmp, gif ou png. Envie outro arquivo.";
			}
			else
			{
				// Verifica tamanho do arquivo
				if($arquivo["size"] > $config["tamanho"])
				{
					$this->upload_error[] = "Arquivo em tamanho muito grande! A imagem enviada possui " . $arquivo["size"] . ". A imagem deve ser de no máximo " . $config["tamanho"] . ". Envie outro arquivo.";
				}
			}
			
			if(sizeof($this->upload_error))
			{
				return false;
			}
			else
			{
				// Pega extensão do arquivo
				preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["name"], $ext);
				
				// Gera um nome único para a imagem
				$imagem_nome = md5( uniqid(time()) ) . "." . $ext[1];
				$this->upload_filename = $imagem_nome;

				// Caminho onde a imagem ficará
				// $imagem_dir = "/u/www/upload/mensagem_estacao/" . $imagem_nome;
				$imagem_dir = $this->UPLOAD_PATH . $imagem_nome;

				// Realiza o upload
				if(move_uploaded_file($arquivo["tmp_name"], $imagem_dir))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}
}
$eu = new eprev_mensagem_estacao_detalhe_save( $db );
?>