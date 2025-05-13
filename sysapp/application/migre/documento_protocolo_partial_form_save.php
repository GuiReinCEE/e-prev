<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Entity.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');
    include_once( 'inc/ePrev.ADO.Projetos.envia_emails.php' );

    class documento_protocolo_partial_save
    {
        private $db;
        private $entidade;

        function documento_protocolo_partial_save( $_db )
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
            $this->entidade->set_ano( $_POST["ano_text"] );
            $this->entidade->set_contador( $_POST["contador_text"] );
            $this->entidade->set_cd_usuario_cadastro( $_POST["cd_usuario_logado_text"] );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->documento_protocolo_Insert( $this->entidade );
            
            if ($bRet) 
            {
				// SBNUNES, SOLIVEIRA + CRIADOR
                $email = new entity_projetos_envia_emails();
                $email->set_assunto( "Novo protocolo de documentos" );
                $email->set_de( "Funda��o CEEE" );
                $email->set_para( "sbnunes@eletroceee.com.br, soliveira@eletroceee.com.br" );
                $email->set_cc( $_SESSION["U"]."@eletroceee.com.br" );
                $email->set_texto( "

                    CORPO DO EMAIL
                    CORPO DO EMAIL
                    CORPO DO EMAIL
                    CORPO DO EMAIL
                    CORPO DO EMAIL

                " );
                //$service->envia_emails_Send( $email );
			}
        }

        public function getId()
        {
            return $this->entidade->get_cd_documento_protocolo();
        }

        public function getAno()
        {
            return $this->entidade->get_ano();
        }

        public function getContador()
        {
            return $this->entidade->get_contador();
        }
    }

    $thisPage = new documento_protocolo_partial_save($db);
    $thisPage->save();

    /** 
     * Mensagens de retorno para p�gina que requisitou
     * String de de retorno no formato:   ID|MSG
     * ID : C�digo de mensagem
     * MSG: Mensagem
     * ---------------------------------------------------------
     * ID                                     MSG
     * 0   (opera��o finalizada sem falhas) | - 
     * 1   (Atualizado no banco de dados)   | [Sequence gerado]
     * 100 (falha)                          | [Mensagem de erro]
     */
    if ($thisPage->getId()=="0" || $thisPage->getId()=="") {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( "N�o foi poss�vel salvar o protocolo, entre em contato com o administrador do sistema." );  // MSG
    }
    else
    {
        echo( "1" );                  // ID
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->getId() );
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->getAno() );
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->getContador() );
    }
?>