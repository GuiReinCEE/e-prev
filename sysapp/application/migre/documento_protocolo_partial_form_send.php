<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.Util.String.php');
	include_once('inc/ePrev.Entity.php');
	include_once('inc/ePrev.Service.Projetos.php');
	include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');
	
	include_once('inc/ePrev.Service.EmailListas.php');
    
    class documento_protocolo_partial_send
    {
        private $db;
        private $entidade;
        
        function documento_protocolo_partial_send( $_db )
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
            $this->entidade->set_cd_usuario_envio( $_POST["cd_usuario_logado_text"] );
            $this->entidade->set_dt_envio( date("Y-m-d") );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->documento_protocolo_Send( $this->entidade );
            return $bRet;
        }

        public function envia_email()
        {
        	// TODO: documento_protocolo - definir texto do email
        	// Enviar email para GAD
			$emailListas = new EmailListas($this->db);
			$emails = $emailListas->getEmailsToString( "digitalizacao_envio_gad" );

			$email = new entity_projetos_envia_emails_extended();
			$email->assunto = "Protocolo de documentos para digitaliza��o enviado";
			$email->texto = "A GAP acabou de enviar um protocolo de documentos para digitaliza��o

J� � poss�vel fazer a confer�ncia no ePrev.

			----
			link: http://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/documento_protocolo.php" . "
			----
			";
			$email->para = $emails;

			$send = new ADO_projetos_envia_emails($this->db);
			$send->insert($email);
        }
    }

    $thisPage = new documento_protocolo_partial_send($db);
    $bRet = $thisPage->save();

    $thisPage->envia_email();

    /** 
     * Mensagens de retorno para p�gina que requisitou
     * String de de retorno no formato:   ID|MSG
     * ID : C�digo de mensagem 
     * MSG: Mensagem
     * --------------------------------------------------------- 
     * ID                                     MSG
     * 0   (opera��o finalizada sem falhas) | - 
     * 1   (Atualizado no banco de dados)   | "Recebimento conclu�do com sucesso"
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
        echo( "Recebimento conclu�do com sucesso" ); // MSG
    }
?>