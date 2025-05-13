<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');

    class atendimento_protocolo_partial_cancel
    {
        private $db;
        private $entidade;
        private $_hasError;
        
        function atendimento_protocolo_partial_cancel( $_db )
        {
            $this->db = $_db;
            $this->entidade = new entity_projetos_atendimento_protocolo();
            $this->requestParams();
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            $this->entidade->setcd_atendimento_protocolo( utf8_decode($_POST["cd_comando_text"]) );
            $this->entidade->setmotivo_cancelamento( utf8_decode($_POST["motivo_cancelamento_text"]) );
        }

        public function cancel()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->correspondenciaGAP_cancel( $this->entidade );
            $this->_hasError = !$bRet;
            return $bRet;
        }

        public function hasError()
        {
            return $this->_hasError;
        }

    }

    $thisPage = new atendimento_protocolo_partial_cancel($db);
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