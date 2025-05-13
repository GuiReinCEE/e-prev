<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Entity.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');
    include_once('inc/ePrev.ADO.Projetos.envia_emails.php');

    class documento_protocolo_partial_receive
    {
        private $db;
        private $entidade;
        
        function documento_protocolo_partial_receive( $_db )
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
            $this->entidade->set_cd_documento_protocolo( $_POST["cd_comando_text"] );
            $this->entidade->set_cd_usuario_ok( $_POST["cd_usuario_logado_text"] );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->documento_protocolo_Receive( $this->entidade );
            $protocolo = new entity_projetos_documento_protocolo();
            $protocolo->set_cd_documento_protocolo( $this->entidade->get_cd_documento_protocolo() );
            $service->documento_protocolo_LoadById( $protocolo );

            $usuario_cadastro = $protocolo->get_usuario_cadastro()->get_usuario();
            
            // Para quem confirmou e para quem criou o protocolo
            $email = new entity_projetos_envia_emails();
            $email->set_assunto( "Protocolo de documentos recebido" );
            $email->set_de( "Fundação CEEE" );
            $email->set_para( $usuario_cadastro."@eletroceee.com.br" );
            $email->set_cc( $_SESSION["U"]."@eletroceee.com.br" );
            $email->set_texto( "

                CORPO DO EMAIL
                CORPO DO EMAIL
                CORPO DO EMAIL
                CORPO DO EMAIL
                CORPO DO EMAIL

            " );
            //$service->envia_emails_Send( $email );
            
            $email = null;
            $protocolo = null;
            $service = null;
            
            return $bRet;
        }

    }

    $thisPage = new documento_protocolo_partial_receive($db);
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