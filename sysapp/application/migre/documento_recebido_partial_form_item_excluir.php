<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.Util.String.php');

    include 'oo/start.php';
    using(array('projetos.documento_recebido_item'));

    class documento_recebido_partial_item_excluir
    {
        private $db;
        private $entidade;
        private $_hasError;

        function documento_recebido_partial_item_excluir( $_db )
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
            $this->entidade['cd_documento_recebido_item'] = intval( $_POST["cd_documento_recebido_item_selected"] );
            $this->entidade['cd_usuario_exclusao'] = intval( $_SESSION['Z'] );
        }

        public function cancel()
        {
        	$bRet = documento_recebido_item::excluir($this->entidade['cd_documento_recebido_item'], $this->entidade['cd_usuario_exclusao']);
        	
            $this->_hasError = !$bRet;
            return $bRet;
        }

        public function hasError()
        {
            return $this->_hasError;
        }
    }

    $thisPage = new documento_recebido_partial_item_excluir($db);
    $bRet = $thisPage->cancel();

    /** 
     * Mensagens de retorno para página que requisitou
     * String de de retorno no formato:   ID|MSG
     * ID : Código de mensagem 
     * MSG: Mensagem
     * --------------------------------------------------------- 
     * ID                                     MSG
     * 0   (operação finalizada sem falhas) | - 
     * 1   (Atualizado no banco de dados)   | "Excluído com sucesso"
     * 100 (falha)                          | [Mensagem de erro]
     */
    if ($thisPage->hasError()==true)
    {
        echo( "100" );  // ID
        echo( "|" );    // SEPARADOR
        echo( "" );     // MSG
    }
    else
    {
        echo( "1" );                     // ID
        echo( "|" );                     // SEPARADOR
        echo( "Cancelado com sucesso" ); // MSG
    }
?>