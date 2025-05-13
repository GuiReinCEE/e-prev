<?php

class Solicita_kit extends Controller
{

	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('eleicoes/solicita_kit_model');
    }
	
	public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        if (gerencia_in(array('GAP', 'GGS')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$this->solicita_kit_model->solicitantes($result, $args);
            $data['arr_solicitante'] = $result->result_array();
			
			$this->solicita_kit_model->tipo($result, $args);
            $data['arr_tipo'] = $result->result_array();
			
			$this->solicita_kit_model->enviados($result, $args);
            $data['arr_enviados'] = $result->result_array();
			
			$data['cd_empresa'] = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia'] = $seq_dependencia;

            $this->load->view('gestao/solicita_kit/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
        if (gerencia_in(array('GAP', 'GGS')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["nome"]                  = $this->input->post("nome", TRUE);
			$args["cd_usuario_inclusao"]   = $this->input->post("cd_usuario_inclusao", TRUE);
			$args["cd_usuario_envio"]      = $this->input->post("cd_usuario_envio", TRUE);
			$args["dt_solicitacao_ini"]    = $this->input->post("dt_solicitacao_ini", TRUE);
			$args["dt_solicitacao_fim"]    = $this->input->post("dt_solicitacao_fim", TRUE);
			$args["dt_envio_ini"]          = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]          = $this->input->post("dt_envio_fim", TRUE);
			$args["cd_solicita_kit_tipo"]  = $this->input->post("cd_solicita_kit_tipo", TRUE);
			$args["fl_enviado"]            = $this->input->post("fl_enviado", TRUE);

            $this->solicita_kit_model->listar($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('gestao/solicita_kit/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function enviar($cd_solicita_kit = 0)
    {
        if (gerencia_in(array('GAP', 'GGS')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["cd_solicita_kit"] = $cd_solicita_kit;
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->solicita_kit_model->enviar($result, $args);

			redirect("gestao/solicita_kit", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function enviar_todos()
	{
		if (gerencia_in(array('GAP', 'GGS')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$solicita_kit = $this->input->post("solicita_kit", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			foreach($solicita_kit as $item)
			{
				$args["cd_solicita_kit"] = $item;
				
				$this->solicita_kit_model->enviar($result, $args);
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
		
	}
	
	public function cadastro($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        if (gerencia_in(array('GAP', 'GGS')))
        {
            $args = Array();
            $data = Array();
            $result = null;
						
			$this->solicita_kit_model->tipo($result, $args);
            $data['arr_tipo'] = $result->result_array();
			
			$this->solicita_kit_model->eleicao($result, $args);
            $data['arr_eleicao'] = $result->result_array();
			
			$data['row'] = Array(
			  'cd_solicita_kit' => 0,
			  'cd_empresa' => $cd_empresa,
			  'cd_registro_empregado' => $cd_registro_empregado,
			  'seq_dependencia' => $seq_dependencia,
			  'nome' => '',
			  'endereco' => '',
			  'complemento' => '',
			  'cd_solicita_kit_tipo' => '',
			  'fl_endereco_atualizado' => '',
			  'cd_eleicao' => '4'
			);

            $this->load->view('gestao/solicita_kit/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
	{
		if (gerencia_in(array('GAP', 'GGS')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["cd_solicita_kit"]        = $this->input->post("cd_solicita_kit", TRUE);
			$args["cd_empresa"]             = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]  = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]        = $this->input->post("seq_dependencia", TRUE);
			$args["cd_solicita_kit_tipo"]   = $this->input->post("cd_solicita_kit_tipo", TRUE);
			$args["fl_endereco_atualizado"] = $this->input->post("fl_endereco_atualizado", TRUE);
			$args["cd_eleicao"]             = $this->input->post("cd_eleicao", TRUE);
			$args["cd_usuario"]             = $this->session->userdata('codigo');
			
			$this->solicita_kit_model->salvar($result, $args);
			
			redirect("gestao/solicita_kit", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function verifica_cadastro()
	{
		if (gerencia_in(array('GAP', 'GGS')))
        {
			$args["cd_empresa"]             = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]  = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]        = $this->input->post("seq_dependencia", TRUE);
			$args["cd_eleicao"]             = $this->input->post("cd_eleicao", TRUE);
			
			$this->solicita_kit_model->verifica_cadatro($result, $args);
			$tl = $result->row_array();
			
			echo $tl['total'];
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
}
?>