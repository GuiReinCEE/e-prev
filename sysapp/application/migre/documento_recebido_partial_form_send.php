<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.Util.String.php');
	include_once('inc/ePrev.Entity.php');
	include_once('inc/ePrev.Service.Projetos.php');
	include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');
	
	include_once('inc/ePrev.Service.EmailListas.php');

    include('oo/start.php');
    using( array('projetos.documento_recebido') );

    class documento_recebido_partial_send
    {
        private $db;
        private $dados = array();
        
        function documento_recebido_partial_send( $_db )
        {
            $this->db = $_db;
            
            $this->requestParams();
        }
        
        function __destruct()
        {
            $this->db = null;
        }
        
        function requestParams()
        {
        	if(isset($_POST["cd_comando_text"])) $this->dados['cd_documento_recebido'] = (int)$_POST["cd_comando_text"];
        	if(isset($_POST["cd_usuario_destino"])) $this->dados['cd_usuario_destino'] = (int)$_POST["cd_usuario_destino"];
        }

        public function save()
        {
        	$ret = documento_recebido::enviar_protocolo( (int)$this->dados['cd_documento_recebido'], (int)$_SESSION['Z'], (int)$this->dados['cd_usuario_destino'] );

            return $ret;
        }
    }

    $thisPage = new documento_recebido_partial_send($db);
    $bRet = $thisPage->save();

    /** 
     * Mensagens de retorno para página que requisitou
     * String de de retorno no formato:   ID|MSG
     * ID : Código de mensagem 
     * MSG: Mensagem
     * --------------------------------------------------------- 
     * ID                                     MSG
     * 0   (operação finalizada sem falhas) | - 
     * 1   (Atualizado no banco de dados)   | "Recebimento concluído com sucesso"
     * 100 (falha)                          | [Mensagem de erro]
     */
    if (!$bRet) {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( "Erro ao tentar salvar registro" );  // MSG
    }
    else
    {
        echo( "1" );                  // ID
        echo( "|" );                 // SEPARADOR
        echo( "Recebimento concluído com sucesso" ); // MSG
    }
?>