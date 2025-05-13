<?php
class contrato_avaliacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/contrato_avaliacao_model');
    }

    function index()
    {
		if(gerencia_in(array('GAD','GGS')))
		{        
			$this->load->view('cadastro/contrato_avaliacao/index');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }

    function listar()
    {
		$args = Array();
        $data = Array();
        $result = null;

		$args["ds_empresa"]    = $this->input->post("ds_empresa", TRUE);
		$args["ds_servico"]    = $this->input->post("ds_servico", TRUE);
		$args["dt_inicio_ini"] = $this->input->post("dt_inicio_ini", TRUE);
		$args["dt_inicio_fim"] = $this->input->post("dt_inicio_fim", TRUE);
		$args["dt_fim_ini"]    = $this->input->post("dt_fim_ini", TRUE);
		$args["dt_fim_fim"]    = $this->input->post("dt_fim_fim", TRUE);
		$args["dt_limite_ini"] = $this->input->post("dt_limite_ini", TRUE);
		$args["dt_limite_fim"] = $this->input->post("dt_limite_fim", TRUE);

        $this->contrato_avaliacao_model->listar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('cadastro/contrato_avaliacao/partial_result', $data);
    }

	function avaliacao($cd_contrato_avaliacao = 0)
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 			
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_avaliacao'] = intval($cd_contrato_avaliacao);

			if(intval($cd_contrato_avaliacao) == 0)
			{
				$data['row'] = Array(
					  'cd_contrato_avaliacao'  => 0,
					  'cd_contrato_formulario' => '',
					  'cd_contrato'            => '',
					  'dt_inicio_avaliacao'    => '',
					  'dt_fim_avaliacao'       => '',
					  'dt_limite_avaliacao'    => '',
					  'dt_envio_email'         => ''
					);
			}
			else
			{
				$this->contrato_avaliacao_model->carrega($result, $args);
				$data['row'] = $result->row_array();
				
				$args['cd_contrato_formulario'] = $data['row']['cd_contrato_formulario'];
				
				$this->contrato_avaliacao_model->grupos($result, $args);
				$grupos = $result->result_array();
				
				$i = 0;
				
				foreach($grupos as $item)
				{
					$data['arr_grupos'][$i]['value'] = $item['cd_contrato_formulario_grupo'];
					$data['arr_grupos'][$i]['text']  = $item['ds_contrato_formulario_grupo'];
					
					$i++;
				}
			}
			
			$this->load->view("cadastro/contrato_avaliacao/avaliacao", $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	
	}
	
	function listar_grupos()
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 
			$args = Array();
			$data = Array();
			$result = null;
		
			$args['cd_contrato_formulario'] = $this->input->post('cd_contrato_formulario', TRUE);
			$args['cd_contrato_avaliacao']  = $this->input->post('cd_contrato_avaliacao', TRUE);
				
			$this->contrato_avaliacao_model->grupos($result, $args);
			$grupos = $result->result_array();
			
			$i = 0;
			
			$data['fl_mudar_formulario'] = true;

			foreach($grupos as $item)
			{
				$args['cd_contrato_formulario_grupo'] = $item['cd_contrato_formulario_grupo'];
				
				$data['grupos'][$i]['cd_contrato_formulario_grupo'] = $item['cd_contrato_formulario_grupo'];
				$data['grupos'][$i]['ds_contrato_formulario_grupo'] = $item['ds_contrato_formulario_grupo'];
				
				$data['arr_grupos'][$i]['value'] = $item['cd_contrato_formulario_grupo'];
				$data['arr_grupos'][$i]['text']  = $item['ds_contrato_formulario_grupo'];
				
				$this->contrato_avaliacao_model->avaliadores($result, $args);
				$data['grupos'][$i]['avaliadores'] = $result->result_array();

				if(count($data['grupos'][$i]['avaliadores']) > 0)
				{
					$data['fl_mudar_formulario'] = false;
				}
				
				$i++;
			}
			
			$this->load->view("cadastro/contrato_avaliacao/avaliacao_result", $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}

	function salvar()
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_contrato_avaliacao"]  = $this->input->post('cd_contrato_avaliacao', TRUE);
			$args["cd_contrato"]            = $this->input->post('cd_contrato', TRUE);
			$args["cd_contrato_formulario"] = $this->input->post('cd_contrato_formulario', TRUE);
			$args["dt_inicio_avaliacao"]    = $this->input->post('dt_inicio_avaliacao', TRUE);
			$args["dt_fim_avaliacao"]       = $this->input->post('dt_fim_avaliacao', TRUE);
			$args["dt_limite_avaliacao"]    = $this->input->post('dt_limite_avaliacao', TRUE);
			$args["cd_usuario"]             = $this->session->userdata('codigo');

			$args["cd_contrato_avaliacao"] = $this->contrato_avaliacao_model->salvar($result, $args);
			
			redirect( 'cadastro/contrato_avaliacao/avaliacao/'.$args["cd_contrato_avaliacao"] , 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}
	
	function excluir($cd_contrato_avaliacao)
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->contrato_avaliacao_model->excluir($result, $args);
			 
			redirect( 'cadastro/contrato_avaliacao/', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}
	
	function salvar_avaliador()
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_contrato_avaliacao"]        = $this->input->post('cd_contrato_avaliacao', TRUE);
			$args["cd_divisao"]                   = $this->input->post('cd_divisao', TRUE);
			$args["cd_usuario_avaliador"]         = $this->input->post('cd_usuario_avaliador', TRUE);
			$args["cd_contrato_formulario_grupo"] = $this->input->post('cd_contrato_formulario_grupo', TRUE);
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$this->contrato_avaliacao_model->salvar_avaliador($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }				
	}
	
	function excluir_avaliador()
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_contrato_avaliacao_item"] = $this->input->post('cd_contrato_avaliacao_item', TRUE);
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			
			$this->contrato_avaliacao_model->excluir_avaliador($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}
	
	function enviar_email($cd_contrato_avaliacao)
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;
			
			$this->contrato_avaliacao_model->enviar_email($result, $args);
			
			redirect( "cadastro/contrato_avaliacao/avaliacao/".$cd_contrato_avaliacao, "refresh" );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }				
	}
	
	function resultado($cd_contrato_avaliacao)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;
		$data['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;

		$this->contrato_avaliacao_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->contrato_avaliacao_model->listar_avaliadores($result, $args);
		$data['avaliacao'] = $result->result_array();
		
		$this->contrato_avaliacao_model->listar_respostas($result, $args);
		$data['respostas'] = $result->result_array();
		
		$this->contrato_avaliacao_model->pontuacao_final($result, $args);
		$arr = $result->row_array();
		
		if(count($arr) > 0)
		{
			$data['resultado_final'] = $arr['vl_resultado'];
		}
		else
		{
			$data['resultado_final'] = 0;
		}
		
		$this->load->view("cadastro/contrato_avaliacao/resultado", $data);
	}
	
	function reabrir($cd_contrato_avaliacao)
	{
		if(gerencia_in(array('GAD','GGS')))
		{ 		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->contrato_avaliacao_model->reabrir($result, $args);
			
			redirect( "cadastro/contrato_avaliacao/avaliacao/".$cd_contrato_avaliacao, "refresh" );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }				
	}
}
?>