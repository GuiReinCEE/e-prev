<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Entity.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

    class documento_protocolo_partial_cancel
    {
        private $db;
        private $entidade;
        private $_hasError;
        
        function documento_protocolo_partial_cancel( $_db )
        {
            $this->db = $_db;
            $this->entidade = new entity_projetos_documento_protocolo();
            $this->requestParams();
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            $this->entidade->set_cd_documento_protocolo( utf8_decode($_POST["cd_comando_text"]) );
            $this->entidade->set_cd_usuario_exclusao( $_POST["cd_usuario_logado_text"] );
            $this->entidade->set_motivo_exclusao( utf8_decode($_POST["motivo_cancelamento_text"]) );
        }

        public function cancel()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->documento_protocolo_Cancel( $this->entidade );
            $this->_hasError = !$bRet;
            return $bRet;
        }

        public function hasError()
        {
            return $this->_hasError;
        }

    }

    $thisPage = new documento_protocolo_partial_cancel($db);
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
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( "" );  // MSG
    }
    else
    {
        echo( "1" );                      // ID
        echo( "|" );                     // SEPARADOR
        echo( "Cancelado com sucesso" ); // MSG
    }
?>