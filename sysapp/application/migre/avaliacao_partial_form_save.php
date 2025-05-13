<?header("Content-Type: text/html; charset=iso-8859-1");?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');

    include_once('inc/ePrev.Service.Projetos.php');

    class avaliacao_partial_save
    {
        private $db;
        private $entidade;
        private $error_flag = false;
        private $error_message = "";
        public $tipo = '';
        
        function avaliacao_partial_save( $_db )
        {
            $this->db = $_db;
            
            $this->entidade = new entity_projetos_avaliacao_capa_extended();
            
            $this->requestParams();
        }
        
        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            $this->entidade->set_cd_avaliacao_capa( "0" );
            if(isset($_POST['dt_periodo_text']))
            {
                $this->entidade->set_dt_periodo( $_POST['dt_periodo_text'] );
            }
            if( isset( $_POST['tipo_promocao_hidden'] ) )
            {
                $this->entidade->set_tipo_promocao($_POST['tipo_promocao_hidden']);
            }

            // O avaliador só pode ser escolhido quando o tipo de promoção é Horizontal
            // caso contrário, abaixo o usuário logado assume como avaliador
            if($this->entidade->get_tipo_promocao()=='H')
            {
                if( isset($_POST['cd_usuario_avaliador_select']) )
                {
                    $this->entidade->set_cd_usuario_avaliador( $_POST['cd_usuario_avaliador_select'] );
                }
            }
            
            // O usuário avaliado só pode ser escolhido quando o tipo de promoção é Vertical
            // caso contrário, abaixo o usuário logado assume como avaliado
            if($this->entidade->get_tipo_promocao()=='V')
            {
                if( isset($_POST['cd_usuario_avaliador_select']) )
                {
                    $this->entidade->set_cd_usuario_avaliador( $_POST['cd_usuario_avaliador_select'] );
                }
                if( isset($_POST['cd_usuario_avaliado_select']) )
                {
                    $this->entidade->set_cd_usuario_avaliado( $_POST['cd_usuario_avaliado_select'] );
                }
            }
            
            if(isset($_SESSION['Z']))
            {
                // Se o tipo for horizontal o usuário logado deve ser gravado como avaliado
                if($this->entidade->get_tipo_promocao()=='H')
                {
                    $this->entidade->set_cd_usuario_avaliado( $_SESSION['Z'] );
                }
            }
            
            // ----------------------------------------------------------------------------------------
            // lembrando que tipo = H indica que a promoção é horizontal e foi aberta pelo 
            // avaliado enquanto a tipo = V é vertical e foi aberta pelo superior
            // ----------------------------------------------------------------------------------------
            // se avaliação do tipo vertical seta status indicando que está com o Superior (avaliador) 
            // onde F = Fechado pelo avaliado
            
            if($this->entidade->get_tipo_promocao()=='V')
            {
                $this->entidade->set_status( 'F' );
            }
            
            // se avaliação do tipo horizontal seta status indicando que está com o Avaliado
            // onde A = Aberto pelo avaliado
            
            elseif($this->entidade->get_tipo_promocao()=='H')
            {
                $this->entidade->set_status( 'A' );
            }
            
            // ----------------------------------------------------------------------------------------
            
            $this->entidade->set_grau_escolaridade( 'null' );
        }

        public function save()
        {
            $service = new service_projetos($this->db);
            $controle = new entity_projetos_avaliacao_controle();

			$usuario_bloqueado=true;
			
			$usuario_bloqueado = $service->usuario_bloqueado_para_avaliacao( intval( $this->entidade->get_cd_usuario_avaliado() ) );
			
			if( $usuario_bloqueado )
        	{
        		$this->error_flag=true;
        		$this->error_message = "Sua avaliação está temporariamente bloqueada por falta de definição das competências e responsabilidades do seu novo cargo.";
        	}
            elseif( $this->entidade->get_tipo_promocao()=='H' && !$service->avaliacao__is_open($controle) )
            {
            	$this->error_flag = true;
            	$this->error_message = "O Processo de avaliação do período de " . date('Y') . " não está aberto!";

            	$dh_abertura = explode(" ", $controle->dt_abertura);
            	$adt_abertura = explode( "/", $dh_abertura[0] );
            	$dt_abertura = $adt_abertura[2] . '-' . $adt_abertura[1] . '-' . $adt_abertura[0];
            	if( $dt_abertura >= date('Y-m-d') )
            	{
            		$this->error_message .= "\nAbertura prevista para " . $controle->dt_abertura;
            	}
            }
            else
            {
	            try
	            {
	                $bRet = $service->avaliacao_capa_Insert( $this->entidade );
	                if($bRet)
	                {
	                	if($this->entidade->get_tipo_promocao()=='V')
	                	{
	                		// Enviar email ao superior avisando que existe uma nova avaliação
	                		$filtro = new entity_projetos_avaliacao_capa_extended();
	                		$filtro->set_cd_avaliacao_capa( $this->entidade->get_cd_avaliacao_capa() );
	                		//$capas = $service->avaliacao_capa_FetchAll( $filtro );
	                		//$capa = $capas[0];
	                		$service->avaliacao_capa_envia_email_evento_34( $filtro, 'SUPERIOR' );
	                	}
	                }
	            }
	            catch(Exception $e)
	            {
	                $bRet = false;
	                if( strpos( $e->getMessage(), "UN_aval_capa_periodo" ) )
	                {
	                    $this->error_message = "Já existe uma avaliação para este usuário avaliado no mesmo período!" ;
	                }
	                else
	                {
	                    echo $e->getMessage();
	                }
	            }
	            $service = null;
	            $this->error_flag = !$bRet;
            }
        }

        public function get_id()
        {
            return $this->entidade->get_cd_avaliacao_capa();
        }
        public function has_error()
        {
            return $this->error_flag;
        }
        public function get_error_message()
        {
            return $this->error_message;
        }

    }
    
    $thisPage = new avaliacao_partial_save($db);
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
    if($thisPage->has_error())
    {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( $thisPage->get_error_message() );  // MSG
    }
    else if ( $thisPage->get_id()=="0" || $thisPage->get_id()=="") 
    {
        echo( "100" ); // ID
        echo( "|" );  // SEPARADOR
        echo( "" );  // MSG
    }
    else
    {
        echo( "1" );                  // ID
        echo( "|" );                 // SEPARADOR
        echo( $thisPage->get_id() );// MSG
    }
?>