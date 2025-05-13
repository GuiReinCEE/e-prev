<?php
class avaliacao_responsabilidade extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
        $this->load->model('projetos/avaliacao_responsabilidade_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$this->load->view('cadastro/avaliacao_responsabilidade/index');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["nome"] = $this->input->post("nome", TRUE);

			manter_filtros($args);

			$this->avaliacao_responsabilidade_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_responsabilidade/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_responsabilidade = 0)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_responsabilidade'] = intval($cd_responsabilidade);
			
			if($args['cd_responsabilidade'] == 0)
            {
                $data['row'] = Array(
					'cd_responsabilidade'   => $args['cd_responsabilidade'],
                    'nome_responsabilidade' => '',
                    'desc_responsabilidade' => ''
				);
            }
            else
            {
                $this->avaliacao_responsabilidade_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_responsabilidade/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}
	
	function salvar()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_responsabilidade"]   = $this->input->post("cd_responsabilidade",TRUE);
			$args["nome_responsabilidade"] = $this->input->post("nome_responsabilidade",TRUE);
			$args["desc_responsabilidade"] = $this->input->post("desc_responsabilidade",TRUE);
			
			$this->avaliacao_responsabilidade_model->salvar($result, $args);
			
			redirect( "cadastro/avaliacao_responsabilidade", "refresh" );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}

	function escala()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('cadastro/avaliacao_responsabilidade/escala');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	function listar_escala()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["descricao"] = $this->input->post("descricao", TRUE);
		
			manter_filtros($args);

			$this->avaliacao_responsabilidade_model->listar_escala( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_responsabilidade/escala_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro_escala($cd_escala = '')
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_escala'] = trim($cd_escala);
			
			if($args['cd_escala'] == '')
            {
                $data['row'] = Array(
					'cd_escala' => $args['cd_escala'],
                    'descricao' => ''
				);
            }
            else
            {
                $this->avaliacao_responsabilidade_model->carrega_escala($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_responsabilidade/cadastro_escala', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}
	
	function salvar_escala()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["insert"]    = $this->input->post("insert",TRUE);
			$args["cd_escala"] = $this->input->post("cd_escala",TRUE);
			$args["descricao"] = $this->input->post("descricao",TRUE);
			
			$this->avaliacao_responsabilidade_model->salvar_escala($result, $args);
			
			redirect( "cadastro/avaliacao_responsabilidade/escala", "refresh" );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	function excluir_escala()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_escala"] = $this->input->post("cd_escala",TRUE);
			
			$this->avaliacao_responsabilidade_model->escluir_escala($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
}
?>