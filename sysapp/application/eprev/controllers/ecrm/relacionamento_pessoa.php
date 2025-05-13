<?php
class relacionamento_pessoa extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('expansao/pessoa_model');
    }

    function index()
    {
		if (gerencia_in(array('GRI')))
        {			
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->pessoa_model->empresas($result, $args);
			$data['arr_empresa'] = $result->result_array();
			
			$this->pessoa_model->uf($result, $args);
			$data['arr_uf'] = $result->result_array();
			
			$this->pessoa_model->grupos($result, $args);
			$data['arr_grupo'] = $result->result_array();
			
			$this->pessoa_model->segmentos($result, $args);
			$data['arr_segmento'] = $result->result_array();
			
			$this->pessoa_model->departamentos($result, $args);
			$data['arr_departamento'] = $result->result_array();
			
			$this->pessoa_model->cargos($result, $args);
			$data['arr_cargo'] = $result->result_array();
			
			$this->load->view('ecrm/relacionamento_pessoa/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }
	
	function cidades()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['uf']        = $this->input->post('uf', true);
		$args['fl_filtro'] = $this->input->post('fl_filtro', true);
		
		$this->pessoa_model->cidades($result, $args);
        $arr = $result->result_array();
				
	    echo json_encode($arr);
	}	
	
	function listar()
    {
		$args = Array();
        $data = Array();
        $result = null;

		$args["ds_pessoa"]              = $this->input->post("ds_pessoa", TRUE);
		$args["cd_pessoa_empresa"]      = $this->input->post("cd_pessoa_empresa", TRUE);
		$args["cd_pessoa_departamento"] = $this->input->post("cd_pessoa_departamento", TRUE);
		$args["cd_pessoa_cargo"]        = $this->input->post("cd_pessoa_cargo", TRUE);
		$args["uf"]                     = $this->input->post("uf", TRUE);
		$args["cidade"]                 = $this->input->post("cidade", TRUE);
		$args["grupos"]                 = (is_array($this->input->post("grupos")) ? implode(",", $this->input->post("grupos")) : '');
		$args["segmentos"]              = (is_array($this->input->post("segmentos")) ? implode(",", $this->input->post("segmentos")) : '');
	
        $this->pessoa_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/relacionamento_pessoa/partial_result', $data);
    }
	
	function cadastro($cd_pessoa = 0, $cd_empresa_relacionamento = 0)
	{
		if (gerencia_in(array('GRI')))
        {			
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_pessoa'] = intval($cd_pessoa);
			
			$this->pessoa_model->empresas($result, $args);
			$data['arr_empresa'] = $result->result_array();
			
			$this->pessoa_model->uf($result, $args);
			$data['arr_uf'] = $result->result_array();
					
			$data['cd_empresa_relacionamento']= intval($cd_empresa_relacionamento);	
				
			if ($cd_pessoa == 0)
			{
				$data['row'] = Array(
				  'cd_pessoa'              => 0,
				  'ds_pessoa'              => '',
				  'cd_pessoa_empresa'      => intval($cd_empresa_relacionamento),
				  'cd_pessoa_departamento' => '',
				  'cd_pessoa_cargo'        => '',
				  'fax'                    => '',
				  'fax_ramal'              => '',
				  'telefone'               => '',
				  'telefone_ramal'         => '',
				  'celular'                => '',
				  'cep'                    => '',
				  'uf'                     => '',
				  'cidade'                 => '',
				  'logradouro'             => '',
				  'numero'                 => '',
				  'complemento'            => '',
				  'bairro'                 => '',
				  'site'                   => '',
				  'cd_empresa'             => '',
				  'cd_registro_empregado'  => '',
				  'seq_dependencia'        => ''
				);
			}
			else
			{
				$this->pessoa_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('ecrm/relacionamento_pessoa/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}
	
	function salvar()
	{
		$args = Array();
		$data = Array();
		$result = null;

		$cd_empresa_relacionamento      = $this->input->post("cd_empresa_relacionamento",TRUE);
		$args["cd_pessoa"]              = $this->input->post("cd_pessoa",TRUE);
		$args["ds_pessoa"]              = $this->input->post("ds_pessoa",TRUE);
		$args["cd_pessoa_empresa"]      = $this->input->post("cd_pessoa_empresa",TRUE);
		$args["cd_pessoa_departamento"] = $this->input->post("cd_pessoa_departamento",TRUE);
		$args["cd_pessoa_cargo"]        = $this->input->post("cd_pessoa_cargo",TRUE);
		$args["cd_empresa"]             = $this->input->post("cd_empresa",TRUE);
		$args["cd_registro_empregado"]  = $this->input->post("cd_registro_empregado",TRUE);
		$args["seq_dependencia"]        = $this->input->post("seq_dependencia",TRUE);
		$args["uf"]                     = $this->input->post("uf",TRUE);
		$args["cidade"]                 = $this->input->post("cidade",TRUE);
		$args["cep"]                    = $this->input->post("cep",TRUE);
		$args["logradouro"]             = $this->input->post("logradouro",TRUE);
		$args["numero"]                 = $this->input->post("numero",TRUE);
		$args["complemento"]            = $this->input->post("complemento",TRUE);
		$args["bairro"]                 = $this->input->post("bairro",TRUE);
		$args["telefone"]               = $this->input->post("telefone",TRUE);
		$args["telefone_ramal"]         = $this->input->post("telefone_ramal",TRUE);
		$args["fax"]                    = $this->input->post("fax",TRUE);
		$args["fax_ramal"]              = $this->input->post("fax_ramal",TRUE);
		$args["celular"]                = $this->input->post("celular",TRUE);
		$args["site"]                   = $this->input->post("site",TRUE);
		$args["cd_usuario"]             = $this->session->userdata('codigo');

		$cd_pessoa = $this->pessoa_model->salvar( $result, $args );

		redirect( "ecrm/relacionamento_pessoa/cadastro/".intval($cd_pessoa).'/'.$cd_empresa_relacionamento, "refresh" );
	}
	
	function salvar_email()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa"]  = $this->input->post("cd_pessoa",TRUE);
		$args["ds_email"]   = $this->input->post("ds_email",TRUE);
		$args["cd_usuario"] = $this->session->userdata('codigo');
		
		$this->pessoa_model->salvar_email( $result, $args );
	}
	
	function listar_emails()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa"] = $this->input->post("cd_pessoa",TRUE);
		
		$this->pessoa_model->listar_emails( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_pessoa/emails_result', $data);
	}
	
	function excluir_email()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa_email"] = $this->input->post("cd_pessoa_email",TRUE);
		$args["cd_usuario"]      = $this->session->userdata('codigo');
		
		$this->pessoa_model->excluir_email( $result, $args );
	}
	
	function excluir($cd_pessoa)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa"]  = $cd_pessoa;
		$args["cd_usuario"] = $this->session->userdata('codigo');
		
		$this->pessoa_model->excluir( $result, $args );
		
		redirect( "ecrm/relacionamento_pessoa", "refresh" );
	}
	
	function contato($cd_pessoa=0)
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$data['cd_pessoa'] = $cd_pessoa;
		
		$this->load->view("ecrm/relacionamento_pessoa/contato", $data);
	}
	
	function listar_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$args["cd_pessoa"] = $this->input->post("cd_pessoa",TRUE);
		
		$this->pessoa_model->listar_contato( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_pessoa/contatos_result', $data);
	}
	
	function salvar_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa"]  = $this->input->post("cd_pessoa",TRUE);
		$args["dt_contato"] = $this->input->post("dt_contato",TRUE);
		$args["ds_contato"] = $this->input->post("ds_contato",TRUE);
		$args["cd_usuario"] = $this->session->userdata('codigo');
		
		$this->pessoa_model->salvar_contato( $result, $args );
	}
	
	function excluir_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_pessoa_contato"] = $this->input->post("cd_pessoa_contato",TRUE);
		$args["cd_usuario"]        = $this->session->userdata('codigo');
		
		$this->pessoa_model->excluir_contato( $result, $args );
	}
}
?>