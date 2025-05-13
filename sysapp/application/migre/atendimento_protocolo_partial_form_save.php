<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');

    class atendimento_protocolo_partial_save
    {
        private $db;
        private $entidade;

        function atendimento_protocolo_partial_save( $_db )
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
            $this->entidade->setcd_atendimento_protocolo( $_POST["cd_atendimento_protocolo_text"] );
            $this->entidade->set_nome( utf8_decode($_POST["nome_participante_text"]) );
            $this->entidade->setdestino( utf8_decode($_POST["destino_text"]) );
            $this->entidade->setcd_empresa( $_POST["cd_empresa_text"] );
            $this->entidade->setcd_registro_empregado( $_POST["cd_registro_empregado_text"] );
            $this->entidade->setseq_dependencia( $_POST["seq_dependencia_text"] );
            $this->entidade->settipo( utf8_decode($_POST["tipo_text"]) );
            $this->entidade->setidentificacao( utf8_decode($_POST["identificacao_text"]) );
            $this->entidade->setcd_usuario_criacao( $_POST["cd_usuario_criacao_text"] );
            $this->entidade->setdt_criacao( $_POST["dt_criacao_text"] );
            $this->entidade->setcd_atendimento_protocolo_tipo( $_POST["cd_atendimento_protocolo_tipo__select"] );
            $this->entidade->setcd_atendimento_protocolo_discriminacao( $_POST["cd_atendimento_protocolo_discriminacao__select"] );

            $pk_ate_enc = explode( ",", $_POST["cd_atendimento_encaminhamento__select"] );
            if( sizeof($pk_ate_enc) && $pk_ate_enc[0]!="" && $pk_ate_enc[1]!="" )
            {
	            $this->entidade->setcd_atendimento( $pk_ate_enc[0] );
	            $this->entidade->setcd_encaminhamento( $pk_ate_enc[1] );
            }
            else
            {
	            $this->entidade->setcd_atendimento( "null" );
	            $this->entidade->setcd_encaminhamento( "null" );
            }
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->correspondenciaGAP_insert( $this->entidade );
        }
        
        public function getId()
        {
            return $this->entidade->getcd_atendimento_protocolo();
        }

    }
    
    $thisPage = new atendimento_protocolo_partial_save($db);
    $thisPage->save();
        
    /** 
     * Mensagens de retorno para página que requisitou
     * String de de retorno no formato:   ID|MSG
     * ID : Código de mensagem 
     * MSG: Mensagem
     * --------------------------------------------------------- 
     * ID                                     MSG
     * 0   (operação finalizada sem falhas) | - 
     * 1   (Atualizado no banco de dados)   | [Sequence gerado]
     * 100 (falha)                          | [Mensagem de erro]
     */
    if ($thisPage->getId()=="0" || $thisPage->getId()=="") {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( "" );  // MSG
    }
    else
    {
        echo( "1" );                  // ID
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->getId() ); // MSG
    }
?>