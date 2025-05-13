<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    
    include 'oo/start.php';
    using( array('projetos.documento_recebido_item') );

    class documento_recebido_partial_add_item
    {
        private $db;
        private $entidade;

        function documento_recebido_partial_add_item( $_db )
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
            // Usar utf8_decode() para campos texto
            $this->entidade['cd_documento_recebido'] = intval( $_POST["cd_documento_recebido_text"] );
            $this->entidade['cd_tipo_doc'] = intval( $_POST["item_cd_tipo_doc_text"] );
            $this->entidade['cd_empresa'] = intval( $_POST["item_cd_empresa_text"] );
            $this->entidade['cd_registro_empregado'] = intval( $_POST["item_cd_registro_empregado_text"] );
            $this->entidade['seq_dependencia'] = intval( $_POST["item_seq_dependencia_text"] );
            $this->entidade['ds_observacao'] = ( utf8_decode( $_POST["item_observacao"] ) );
            $this->entidade['cd_usuario_cadastro'] = intval( $_SESSION['Z'] );
            $this->entidade['nr_folha'] = intval( ($_POST["item_nr_folha"]=="")?"1":$_POST["item_nr_folha"] );
            $this->entidade['arquivo'] = utf8_decode($_POST["item_arquivo"]);
            $this->entidade['arquivo_nome'] = utf8_decode($_POST["item_arquivo_nome"]);
            $this->entidade['nome'] = utf8_decode($_POST["nome_participante_text"]);
        }

        public function save()
        {
        	documento_recebido_item::inserir( $this->entidade );
        }

        public function getId()
        {
            return $this->entidade['cd_documento_recebido_item'];
        }
    }

    $thisPage = new documento_recebido_partial_add_item($db);
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
    if ($thisPage->getId()=="0" || $thisPage->getId()=="")
    {
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