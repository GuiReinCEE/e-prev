<?php
class avaliacao_escolaridade extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
        $this->load->model('projetos/escolaridade_model');
    }

    function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$this->load->view('cadastro/avaliacao_escolaridade/index');
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

			$this->escolaridade_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_escolaridade/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	function cadastro($cd_escolaridade = 0)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_escolaridade'] = intval($cd_escolaridade);
			
			if($args['cd_escolaridade'] == 0)
            {
                $data['row'] = Array(
					'cd_escolaridade'   => $args['cd_escolaridade'],
                    'nome_escolaridade' => '',
                    'desc_escolaridade' => ''
				);
            }
            else
            {
                $this->escolaridade_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_escolaridade/cadastro', $data);
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
			
			$args["cd_escolaridade"]   = $this->input->post("cd_escolaridade",TRUE);
			$args["nome_escolaridade"] = $this->input->post("nome_escolaridade",TRUE);
			$args["desc_escolaridade"] = $this->input->post("desc_escolaridade",TRUE);
			
			$this->escolaridade_model->salvar($result, $args);
			
			redirect( "cadastro/avaliacao_escolaridade", "refresh" );
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
			
			$this->load->view('cadastro/avaliacao_escolaridade/escala');
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

			$this->escolaridade_model->listar_escala( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_escolaridade/escala_result', $data);
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
                $this->escolaridade_model->carrega_escala($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_escolaridade/cadastro_escala', $data);
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
			
			$this->escolaridade_model->salvar_escala($result, $args);
			
			redirect( "cadastro/avaliacao_escolaridade/escala", "refresh" );
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
			
			$this->escolaridade_model->escluir_escala($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
}
?>