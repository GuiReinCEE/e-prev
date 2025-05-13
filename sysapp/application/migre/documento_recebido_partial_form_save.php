<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include 'inc/ePrev.Enums.php';
    include 'oo/start.php';

    using( array('projetos.documento_recebido') );

    class documento_recebido_partial_save
    {
        private $db;
        private $entidade;

        function documento_recebido_partial_save( $_db )
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
            $this->entidade['ano'] = intval( $_POST["ano_text"] );
            $this->entidade['contador'] = intval( $_POST["contador_text"] );
            $this->entidade['cd_usuario_cadastro'] = intval( $_POST["cd_usuario_logado_text"] );
            $this->entidade['cd_documento_recebido_tipo'] = intval( $_POST["cd_documento_recebido_tipo"] );
        }

        public function save()
        {
            $cd_documento_recebido_tipo = (int)$this->entidade['cd_documento_recebido_tipo'];
        	documento_recebido::inserir($cd_documento_recebido_tipo, $this->entidade);

        	// var_dump($this->entidade);exit;

            if ($bRet) 
            {
			}
        }

        public function getId()
        {
            return $this->entidade['cd_documento_recebido'];
        }

        public function getAno()
        {
            return $this->entidade['nr_ano'];
        }

        public function getContador()
        {
            return $this->entidade['nr_contador'];
        }
    }

    $thisPage = new documento_recebido_partial_save($db);
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
        echo( "Não foi possível salvar o protocolo, entre em contato com o administrador do sistema." );  // MSG
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