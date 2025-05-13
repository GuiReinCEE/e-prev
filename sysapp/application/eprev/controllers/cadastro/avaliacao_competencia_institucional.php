<?php
class avaliacao_competencia_institucional extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/comp_inst_model');
    }

    function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$this->load->view('cadastro/avaliacao_competencia_institucional/index');
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

			$this->comp_inst_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_competencia_institucional/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	function cadastro($cd_comp_inst = 0)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_comp_inst'] = intval($cd_comp_inst);
			
			if($args['cd_comp_inst'] == 0)
            {
                $data['row'] = Array(
					'cd_comp_inst'   => $args['cd_comp_inst'],
                    'nome_comp_inst' => '',
                    'desc_comp_inst' => ''
				);
            }
            else
            {
                $this->comp_inst_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_competencia_institucional/cadastro', $data);
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
			
			$args["cd_comp_inst"]   = $this->input->post("cd_comp_inst",TRUE);
			$args["nome_comp_inst"] = $this->input->post("nome_comp_inst",TRUE);
			$args["desc_comp_inst"] = $this->input->post("desc_comp_inst",TRUE);
			
			$this->comp_inst_model->salvar($result, $args);
			
			redirect( "cadastro/avaliacao_competencia_institucional", "refresh" );
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
			
			$this->load->view('cadastro/avaliacao_competencia_institucional/escala');
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

			$this->comp_inst_model->listar_escala( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_competencia_institucional/escala_result', $data);
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
                $this->comp_inst_model->carrega_escala($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('cadastro/avaliacao_competencia_institucional/cadastro_escala', $data);
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
			
			$this->comp_inst_model->salvar_escala($result, $args);
			
			redirect( "cadastro/avaliacao_competencia_institucional/escala", "refresh" );
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
			
			$this->comp_inst_model->escluir_escala($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
}
?>