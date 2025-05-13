<?php
class ri_cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		
		if(gerencia_in(array('GRI')))
		{
			$data = array();
			$args = array();
			$this->load->model('acs/Cadastro_Origem_model');
			$this->Cadastro_Origem_model->combo( $result, $args );
			$data['ar_cadastro_origem'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_cadastro/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listar()
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Cadastro_model');
			$data['collection'] = array();
			$result = null;
			$args=array();

			$args["cd_cadastro_origem"] = $this->input->post('cd_cadastro_origem', TRUE);
			$args["nome"]               = $this->input->post('nome', TRUE);
			$args["empresa"]            = $this->input->post('empresa', TRUE);

			manter_filtros($args);
			$this->Cadastro_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/ri_cadastro/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function cadastro($cd_cadastro = 0)
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$data = array();
			$args = array();
			
			$this->load->model('acs/Cadastro_model');
			$this->load->model('acs/Cadastro_Origem_model');

			$this->Cadastro_Origem_model->combo( $result, $args );

			$data['ar_cadastro_origem'] = $result->result_array();	
			$data['cd_cadastro'] = intval($cd_cadastro);
			$args['cd_cadastro'] = intval($cd_cadastro);
			
			if(intval($cd_cadastro) == 0)
			{
				$data['row'] = Array('cd_cadastro'=> intval($cd_cadastro),  
									 'cd_cadastro_origem'=> 0,  
									 'nome'=>'',  
									 'cargo'=>'',  
									 'empresa'=>'',  
									 'endereco'=>'',
								     'cep'=>'',
								     'cidade'=>'',
								     'uf'=>'',
								     'pais'=>'',
								     'telefone_ddd'=>'',
								     'telefone'=>'',
								     'celular_ddd'=>'',
								     'celular'=>'',
								     'email'=>'',
								     'dt_inclusao'=>'',
								     'cd_usuario_inclusao'=>'',
								     'dt_exclusao'=>'',
								     'cd_usuario_exclusao'=>''
									 );
			}
			else
			{

				$this->Cadastro_model->cadastro($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/ri_cadastro/cadastro.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }

    function cadastroSalvar()
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Cadastro_model');

			$data   = Array();
			$result = null;
			$args   = Array();

			$args["cd_cadastro"]        = $this->input->post("cd_cadastro", TRUE);
			$args["cd_cadastro_origem"] = $this->input->post("cd_cadastro_origem", TRUE);
			$args["nome"]               = $this->input->post("nome", TRUE);
			$args["cargo"]              = $this->input->post("cargo", TRUE);
			$args["empresa"]            = $this->input->post("empresa", TRUE);
			$args["cep"]                = $this->input->post("cep", TRUE);
			$args["cidade"]             = $this->input->post("cidade", TRUE);
			$args["uf"]                 = $this->input->post("uf", TRUE);
			$args["pais"]               = $this->input->post("pais", TRUE);
			$args["telefone_ddd"]       = $this->input->post("telefone_ddd", TRUE);
			$args["telefone"]           = $this->input->post("telefone", TRUE);
			$args["celular_ddd"]        = $this->input->post("celular_ddd", TRUE);
			$args["celular"]            = $this->input->post("celular", TRUE);
			$args["email"]              = $this->input->post("email", TRUE);
			
			$retorno = $this->Cadastro_model->cadastroSalvar($result, $args);
			
			redirect("ecrm/ri_cadastro/cadastro/".$retorno, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

	function excluir($cd_cadastro)
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('acs/Cadastro_model');
			
			$args['cd_cadastro'] = intval($cd_cadastro);
			$args["cd_usuario"]  = $this->session->userdata('codigo');
			
			$this->Cadastro_model->excluir($result, $args);
			
			redirect("ecrm/ri_cadastro/cadastro/".intval($cd_cadastro), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
}
