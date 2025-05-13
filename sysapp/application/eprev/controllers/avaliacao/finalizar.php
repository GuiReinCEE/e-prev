<?php
class finalizar extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();	
			$data = Array();
	        $this->load->view('avaliacao/finalizar/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
		CheckLogin();
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{

	        $this->load->model('projetos/Avaliacao_capa_model');
	
	        $data['collection'] = array();
	        $result = null;
			$args   = array();
		
			manter_filtros($args);
	
	        $this->Avaliacao_capa_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	        $this->load->view('avaliacao/finalizar/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }

	function salvar($cd)
	{
		exibir_mensagem("UTILIZE A TELA AVALIACAO -> MANUTENCAO");
		#### MOVIDO PARA TELA AVALIACAO -> MANUTENCAO ####
		/*
		CheckLogin();
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$sql="
					SELECT capa.cd_avaliacao_capa, 
					       capa.tipo_promocao, 
						   capa.dt_periodo, 
						   avaliado.nome, 
						   avaliado.usuario, 
						   avaliado.guerra
			          FROM projetos.avaliacao_capa capa
			          JOIN projetos.usuarios_controledi avaliado 
					    ON avaliado.codigo=capa.cd_usuario_avaliado 
			         WHERE md5(capa.cd_avaliacao_capa::varchar||'-f1nal1zar')='".trim($cd)."'
			     ";
			$query = $this->db->query($sql);
			$avaliacao = $query->row_array();

			// ---------------------------------------
			
			$template_email=
"Prezado(a) {nome}.

A avaliação foi finalizada, o resultado já está disponível no ePrev.

{link}

Mensagem enviada pelo Controle de Avaliações.";
			
			$this->load->model('projetos/Avaliacao_capa_model');

			$args['cd_avaliacao_capa']=$avaliacao['cd_avaliacao_capa'];

			$msg=array();
			$retorno = $this->Avaliacao_capa_model->finalizar( $args, $msg );

			if($retorno)
			{
				// Email do evento 34
				if( $avaliacao['tipo_promocao']=='H' )
				{
					$texto = $template_email;
					$texto = str_replace("{nome}", $avaliacao['nome'], $texto);
					$texto = str_replace("{link}", "https://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/avaliacao.php?tipo=F&cd_capa=" . $avaliacao["cd_avaliacao_capa"], $texto);

	 				$args['de'] = 'Fundação CEEE';
	 				$args['para'] = $avaliacao['usuario'] . '@eletroceee.com.br';
	 				$args['assunto'] = "Avaliação de competências " . $avaliacao['dt_periodo'] . " - " . $avaliacao['guerra'];
	 				$args['mensagem'] = $texto;
	 				$args['cd_evento'] = enum_projetos_eventos::AVALIACAO_DE_DESEMPENHO;

					$enviado = enviar_email($args);

					if($enviado)
					{
						redirect( "avaliacao/finalizar", "refresh" );
					}
					else
					{
						exibir_mensagem( 'Não foi possível concluir a operação, avise o programador informando o código de avaliação: ' . $args['cd_avaliacao_capa'] );
					}
				}
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		*/
	}
}
?>