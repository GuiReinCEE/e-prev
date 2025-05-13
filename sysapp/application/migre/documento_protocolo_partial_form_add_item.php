<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Entity.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

    class documento_protocolo_partial_add_item
    {
        private $db;
        private $entidade;

        function documento_protocolo_partial_add_item( $_db )
        {
            $this->db = $_db;
            
            $this->entidade = new entity_projetos_documento_protocolo_item();
            
            $this->requestParams();
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            // usar utf8_decode() para campos texto
            $this->entidade->set_cd_documento_protocolo( $_POST["cd_documento_protocolo_text"] );
            $this->entidade->set_cd_tipo_doc( $_POST["item_cd_tipo_doc_text"] );
            $this->entidade->set_cd_empresa( $_POST["item_cd_empresa_text"] );
            $this->entidade->set_cd_registro_empregado( $_POST["item_cd_registro_empregado_text"] );
            $this->entidade->set_seq_dependencia( $_POST["item_seq_dependencia_text"] );
            $this->entidade->set_observacao( utf8_decode( $_POST["item_observacao"] ) );
            $this->entidade->set_cd_usuario_cadastro( $_POST["cd_usuario_logado_text"] );
            $this->entidade->set_ds_processo( utf8_decode( $_POST["item_ds_processo"] ) );
            $this->entidade->set_nr_folha( ($_POST["item_nr_folha"]=="")?"1":$_POST["item_nr_folha"] );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $bRet = $service->documento_protocolo_item_Insert( $this->entidade );
        }

        public function getId()
        {
            return $this->entidade->get_cd_documento_protocolo_item();
        }
    }

    $thisPage = new documento_protocolo_partial_add_item($db);
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