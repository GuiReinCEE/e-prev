<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Service.Projetos.php');

    class atendimento_recadastro_partial_save
    {
        private $db;
        private $entidade;
        public $msg_err;
        
        function __construct( $_db )
        {
            $this->db = $_db;
            
            $this->entidade = new entity_projetos_atendimento_recadastro();
            
            $this->requestParams();
        }
        
        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            $this->entidade->cd_atendimento_recadastro = $_POST["cd_atendimento_recadastro_text"];
            $this->entidade->nome = utf8_decode($_POST["nome_participante_text"]);
            $this->entidade->cd_empresa = $_POST["cd_empresa_text"];
            $this->entidade->cd_registro_empregado = $_POST["cd_registro_empregado_text"];
            $this->entidade->seq_dependencia = $_POST["seq_dependencia_text"];
            $this->entidade->cd_usuario_criacao = $_POST["cd_usuario_criacao_text"];
            $this->entidade->observacao = utf8_decode($_POST["observacao_text"]);
            $this->entidade->dt_periodo = utf8_decode($_POST["dt_periodo_text"]);
            $this->entidade->servico_social = utf8_decode($_POST["servico_social_text"]);
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->atendimento_recadastro__insert( $this->entidade );
            $this->msg_err = '';
            if($bRet==2)
            {
            	echo 'teste' . ' - bret = ' . $bRet . ' - ';
            	$this->msg_err = "Não é possível incluir pois esse RE já foi cadastrado para o mesmo período.";
            	$bRet = false;
            }
        }
        
        public function getId()
        {
            return $this->entidade->cd_atendimento_recadastro;
        }

    }
    
    $thisPage = new atendimento_recadastro_partial_save($db);
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
    if($thisPage->getId()=="0" || $thisPage->getId()=="" || $thisPage->msg_err!='')
    {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( $thisPage->msg_err );  // MSG
    }
    else
    {
        echo( "1" );                  // ID
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->getId() ); // MSG
    }
?>