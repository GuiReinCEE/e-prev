<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_partial_avaliacao_avaliado_save_by_avaliador
{
    private $service;
    private $command = '';
    private $grau_escolaridade = '0';
    private $capa;
    private $avaliacao;
    private $cd_avaliacao;
    private $db;
    private $media_geral;
    
    public $tudo_ok = true;
    
    function __construct($db)
    {
        $this->db = $db;
        $this->capa = new entity_projetos_avaliacao_capa_extended();
        $this->avaliacao = new entity_projetos_avaliacao_extended();
        $this->service = new service_projetos($db);
        
        $this->requestParams();
    }
    function __destruct()
    {
        $this->service = null;
    }

    private function requestParams()
    {
        if( isset($_POST["ajax_command_hidden"]) )
        {
            $this->command = $_POST["ajax_command_hidden"];
        }

        // capa
        if( isset($_POST["grau_escolaridade"]) )
        {
            $this->capa->set_grau_escolaridade($_POST["grau_escolaridade"]);
        }
        if( isset($_POST["cd_avaliacao_selected_hidden"]) )
        {
            $this->capa->set_cd_avaliacao_capa(utf8_decode($_POST["cd_avaliacao_selected_hidden"]));
        }
        if( isset($_POST["tipo_promocao_hidden"]) )
        {
            $this->capa->set_tipo_promocao(utf8_decode($_POST["tipo_promocao_hidden"]));
        }

        // avaliação
        if( isset($_POST["cd_avaliacao_selected_hidden"]) )
        {
            $this->avaliacao->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );
        }
    }

    private function capa_update()
    {
        if( $this->command=="save_and_send" || $this->command=="insert_and_send" )
        {
            $this->capa->set_status("S"); // fechado pelo superior
        }
        else
        {
            $this->capa->set_status("F"); // continua com o superior
        }
        
        $this->service->avaliacao_capa_Update( $this->capa );
        return true;
    }
    
    private function avaliacao_insert()
    {
        if($this->command=="insert_and_continue" || $this->command=="insert_and_send")
        {
            $this->avaliacao->set_cd_avaliacao( "0" );
            $this->avaliacao->set_cd_usuario_avaliador( $_SESSION["Z"] );
            $this->avaliacao->set_tipo( "S" );
            $this->avaliacao->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );

            $rs = $this->service->avaliacao_Insert( $this->avaliacao );
            
            // clonar expectativas para avaliação do avaliador
            $avaliacao_origem = $_POST["cd_avaliacao_hidden"];
            $this->service->avaliacao_aspecto__clone( $avaliacao_origem, $this->avaliacao->get_cd_avaliacao() );
        }
        else if($this->command=="save_and_continue" || $this->command=="save_and_send")
        {
            $this->avaliacao->set_cd_avaliacao( $_POST["cd_avaliacao_hidden"] );
            $rs = $this->service->avaliacao_Update( $this->avaliacao );
        }

        $this->cd_avaliacao = $this->avaliacao->get_cd_avaliacao();

        $competencias_institucionais = array();
        $competencias_especificas = array();
        $responsabilidades = array();
        $v_comp_inst_informada = 0;
        
        while( list($key, $value) = each($_POST) ) 
        { 
            $v_str = $key;

            if (strpos($v_str, "omp_inst") > 0)
            {
                if (is_numeric($value))
                {
                    $v_comp_inst_informada = $v_comp_inst_informada + 1;
                }
                $comp_inst = new entity_projetos_avaliacoes_comp_inst();
                $comp_inst->set_cd_avaliacao( $this->cd_avaliacao );
                $comp_inst->set_cd_comp_inst( str_replace('comp_inst', '', $v_str) );
                $comp_inst->set_grau( $value );
                $competencias_institucionais[ sizeof($competencias_institucionais) ] = $comp_inst;
            }
            if (strpos($v_str, "omp_espec") > 0)
            {
                $comp_espec = new entity_projetos_avaliacoes_comp_espec();
                $comp_espec->set_cd_avaliacao( $this->cd_avaliacao );
                $comp_espec->set_cd_comp_espec( str_replace('comp_espec', '', $v_str) );
                $comp_espec->set_grau( $value );
                $competencias_especificas[ sizeof($competencias_especificas) ] = $comp_espec;
            }
            if (strpos($v_str, "esponsabilidade") > 0)
            {
                $resp = new entity_projetos_avaliacoes_responsabilidades();
                $resp->set_cd_avaliacao( $this->cd_avaliacao );
                $resp->set_cd_responsabilidade( str_replace('responsabilidade', '', $v_str) );
                $resp->set_grau( $value );
                $responsabilidades[ sizeof($responsabilidades) ] = $resp;
            }
        }
        
        $ret = $this->service->avaliacao_generate_queries_to_transaction( $this->cd_avaliacao, $competencias_institucionais, $competencias_especificas, $responsabilidades);
        
        return $ret;
    }

    private function verifica_promocao_horizontal()
    {
        // Testes para aprovação de promoção horizontal
        $pode_promover = $this->pode_promover_horizontalmente();

        if( ! $pode_promover )
        {
            // Encerra o processo de avaliação divulgando o resultado
            // Será implementado junto com a promoção horizontal
        	$this->capa->set_media_geral( $this->media_geral );
            $retorno = $this->service->avaliacao_capa_Publicar( $this->capa );
        }
        else
        {
            // encaminha ao administrador do sistema (atualmente o gilberto 28-5-2008)
            // o processo de envio de emails ao comitê será realizado pelo gilberto (administrador)
            /*
             * O relatório pro gilberto será formado lista de avaliaçoes com status E:
             * */
            $retorno = $this->service->avaliacao_capa__encaminhar_ao_administrador( $this->capa->get_cd_avaliacao_capa() );

            // $this->service->avaliacao_capa_envia_email_evento_34($this->capa, "COMITE");
        }

        return $retorno;
    }
    
    private function verifica_promocao_vertical()
    {
        
        // Testes para aprovação de promoção horizontal
        $pode_promover = $this->pode_promover_verticalmente();

        if( ! $pode_promover )
        {
        	// echo 'não pode pode promover';exit;
            // encerra o processo de avaliação divulgando o resultado
            $this->capa->set_media_geral( $this->media_geral );
            $retorno = $this->service->avaliacao_capa_Publicar( $this->capa );
        }
        else
        {
        	//echo 'pode promover';exit;
            // encaminha ao administrador do sistema (atualmente o gilberto 28-5-2008)
            // o processo de envio de emails ao comitê será realizado pelo gilberto (administrador)
            /*
             * O relatório pro gilberto será formado lista de avaliaçoes com status E:
             * 
             * */
            $retorno = $this->service->avaliacao_capa__encaminhar_ao_administrador( $this->capa->get_cd_avaliacao_capa() );

            // $this->service->avaliacao_capa_envia_email_evento_34($this->capa, "COMITE");
        }

        return true;
    }
    
    public function start()
    {
        if($this->tudo_ok) $this->tudo_ok = $this->capa_update();
        
        if($this->tudo_ok) $this->tudo_ok = $this->avaliacao_insert();

        if( $this->command=="save_and_send" || $this->command=="insert_and_send" )
        {
            if( $this->capa->get_tipo_promocao()=='H' )
            {
                if($this->tudo_ok) $this->tudo_ok = $this->verifica_promocao_horizontal();
            }
            elseif( $this->capa->get_tipo_promocao()=='V' )
            {
                if($this->tudo_ok) $this->tudo_ok = $this->verifica_promocao_vertical();
            }
        }
    }
    
    public function pode_promover_horizontalmente()
    {
		$return = false;
		
		$_capas = array();
		$_capa = new entity_projetos_avaliacao_capa_extended();
		$avaliado = new entity_projetos_usuarios_controledi_extended();
		$usuario_matriz = new entity_projetos_usuario_matriz_extended();
		$matriz_salarial = new entity_projetos_matriz_salarial_extended();
		
		// echo $this->capa->get_cd_avaliacao_capa();
		$_capas = $this->service->avaliacao_capa_FetchAll($this->capa);
		$_capa = $_capas[0];
		$avaliado = $_capa->avaliado;
		$usuario_matriz = $avaliado->usuario_matriz;
		
		if( $this->service->avaliacao__processo_completo($_capa) )
		{
			$matriz_salarial = $usuario_matriz->matriz_salarial;
			
			$GRAU_MINIMO = $matriz_salarial->valor_final;
			
	        $helper = new helper_avaliacao_resultado( $this->db, $this->capa->get_cd_avaliacao_capa(), $_SESSION['Z'] );
	        $helper->load();
	
	        $GRAU = 0;
	        foreach( $helper->capa->avaliacoes as $avaliacao )
	        {
	            if( $avaliacao->get_tipo()=='S' )
	            {
					$helper->load_valores( $avaliacao->get_cd_avaliacao() );
	                $GRAU = $helper->get_grau_final();
	                break;
				}
	        }
	        $this->media_geral = $GRAU;
	        
	        return ( floatval($this->media_geral)>=floatval($GRAU_MINIMO) );
		}
		else
		{
			$this->media_geral = 0;
			return false;
		}
    }

    public function pode_promover_verticalmente()
    {
        $return = false;
        $GRAU_MINIMO = 70.0;

        $capa = new entity_projetos_avaliacao_capa_extended();
        $capa->set_cd_avaliacao_capa( $this->capa->get_cd_avaliacao_capa() );
        $capas = $this->service->avaliacao_capa_FetchAll($capa);
        $capa = $capas[0];

        $a_data = explode("/", $capa->avaliado->usuario_matriz->dt_promocao);
        $dt_promocao = $a_data[2] . '/' . $a_data[1] . '/' . $a_data[0];
        $dt_um_ano_atras = strftime("%Y/%m/%d", mktime ( 0, 0, 0, date('m'), date('d'), date('Y')-1 ));

        // Se a última promoção do usuário foi Horizontal ou a data da promoção foi V mas superior a um ano
        // lembrando que para uma promoção horizontal ocorrer a ultima vertical deve ter sido dois anos antes.
        if( $capa->avaliado->usuario_matriz->tipo_promocao=='H' OR $dt_promocao<$dt_um_ano_atras )
        {
            $helper = new helper_avaliacao_resultado( $this->db, $capa->get_cd_avaliacao_capa(), $_SESSION['Z'] );
            $helper->load();
            
            $GRAU = 0;
            foreach( $helper->capa->avaliacoes as $avaliacao )
            {
                if( $avaliacao->get_tipo()=='S' )
                {
                    $helper->load_valores( $avaliacao->get_cd_avaliacao() );
                    $GRAU = $helper->get_grau_final();
                    // $this->service->avaliacao_capa__media_geral__set( $this->capa->get_cd_avaliacao_capa(), $GRAU );
                    break;
                }
            }
            $this->media_geral = $GRAU;
            $return = (  floatval($GRAU)>floatval($GRAU_MINIMO)  );
        }
        else
        {
        	$this->media_geral = 0;
        }
        return $return;
    }
}

$esta = new controle_projetos_avaliacao_partial_avaliacao_avaliado_save_by_avaliador($db);
$esta->start();

if ( !$esta->tudo_ok )
{
    $return_id = 100;
    $return_message = "Ocorreu um erro ao salvar esta avaliação.";
}
else
{
    $return_id = 1;
    $return_message = "Avaliação atualizada.\n";
}

/**
 * RETORNO DA PÁGINA:
 * 
 * $return_id
 *  1 - Todas as tabelas atualizadas
 *  2 - Algum erro ocorreu, mas alguma(s) tabela(s) foi(ram) atualizada(s)
 *  100 - Erro ao atualizar tabela projetos.avaliacao.
 * 
 * $return_message
 *  Mensagem de retorno, contém o andamento das atualizações ou erros que ocorreram.
 */
echo( $return_id );
echo( "|" );
echo( $return_message );
exit();
?>