<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');

    class atendimento_protocolo_partial_receive
    {
        private $db;
        private $entidade;
        
        function atendimento_protocolo_partial_receive( $_db )
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
            $this->entidade->setcd_atendimento_protocolo( $_POST["cd_comando_text"] );
            $this->entidade->setcd_usuario_recebimento( $_POST["cd_usuario_criacao_text"] );
            $this->entidade->setdt_recebimento( date("Y-m-d") );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->correspondenciaGAP_receive( $this->entidade );
            return $bRet;
        }

    }

    $thisPage = new atendimento_protocolo_partial_receive($db);
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