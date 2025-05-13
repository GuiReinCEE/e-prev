<?php
class seminario_economico extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GRI','SG')))
		{
			$this->load->model('acs/Seminario_economico_model');
			$args = Array();	
			$data = Array();	

			$this->Seminario_economico_model->comboEdicao($result, $args);
			$data['ar_seminario_edicao'] = $result->result_array();
			
			$this->load->view('ecrm/seminario_economico/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
		if(gerencia_in(array('GRI','SG')))
		{		
			$this->load->model('acs/Seminario_economico_model');

			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_seminario_edicao"] = $this->input->post("cd_seminario_edicao", TRUE);
			$args["dt_inclusao_ini"]     = $this->input->post("dt_inclusao_ini", TRUE);
			$args["dt_inclusao_fim"]     = $this->input->post("dt_inclusao_fim", TRUE);
			$args["fl_presente"]         = $this->input->post("fl_presente", TRUE);
			$args["fl_email"]            = $this->input->post("fl_email", TRUE);
			
			manter_filtros($args);
			
			$this->Seminario_economico_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/seminario_economico/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		
		
	function detalhe($cd_inscricao = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI','SG')))
		{
			$this->load->model('acs/Seminario_economico_model');
			$args=array();	
			$data['cd_inscricao'] = intval($cd_inscricao);
			
			if(intval($cd_inscricao) == 0)
			{
				$data['row'] = Array('cd_inscricao'=>0,
									'nome' => '', 
									'cd_empresa' => '', 
									'cd_registro_empregado' => '', 
									'seq_dependencia' => '', 
									'cargo' => '',
									'empresa' => '',
									'endereco' => '',
									'cidade' => '',
									'uf' => '',
									'cep' => '',
									'telefone' => '',
									'telefone_ramal' => '',
									'fax' => '',
									'fax_ramal' => '',
									'telefone_ddd' => '',
									'fax_ddd' => '',
									'email' => '',
									'autoriza_mailing' => '', 
									'celular_ddd' => '', 
									'celular' => '', 
									'numero' => '',
									'complemento' => '',
									'dt_confirmacao' => '',
									'nome_sem_acento' => '',
									'cd_seminario_edicao' => '',
									'cd_barra' => '',
									'fl_presente' => '',
									'dt_inclusao' => '',
									'dt_exclusao' => '',
									'ds_seminario_edicao' => '',
									'dt_envio_certificado' => ''
				                     );
			}
			else
			{
				$args['cd_inscricao'] = intval($cd_inscricao);
				$this->Seminario_economico_model->inscricao($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/seminario_economico/detalhe.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function salvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Seminario_economico_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_inscricao"]          = $this->input->post("cd_inscricao", TRUE);
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["nome"]                  = $this->input->post("nome", TRUE);
			$args["email"]                 = $this->input->post("email", TRUE);
			$args["cargo"]                 = $this->input->post("cargo", TRUE);
			$args["empresa"]               = $this->input->post("empresa", TRUE);
			$args["cep"]                   = $this->input->post("cep", TRUE);
			$args["endereco"]              = $this->input->post("endereco", TRUE);
			$args["numero"]                = $this->input->post("numero", TRUE);
			$args["complemento"]           = $this->input->post("complemento", TRUE);
			$args["cidade"]                = $this->input->post("cidade", TRUE);
			$args["uf"]                    = $this->input->post("uf", TRUE);
			$args["telefone_ddd"]          = $this->input->post("telefone_ddd", TRUE);
			$args["telefone"]              = $this->input->post("telefone", TRUE);
			$args["telefone_ramal"]        = $this->input->post("telefone_ramal", TRUE);
			$args["celular_ddd"]           = $this->input->post("celular_ddd", TRUE);
			$args["celular"]               = $this->input->post("celular", TRUE);
			$args["fax_ddd"]               = $this->input->post("fax_ddd", TRUE);
			$args["fax"]                   = $this->input->post("fax", TRUE);
			$args["fax_ramal"]             = $this->input->post("fax_ramal", TRUE);
			$args["fl_presente"]           = $this->input->post("fl_presente", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');

			$cd_inscricao_new = $this->Seminario_economico_model->salvar($result, $args);
			redirect("ecrm/seminario_economico/detalhe/".$cd_inscricao_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function excluir( $cd_inscricao = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Seminario_economico_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_inscricao"] = intval($cd_inscricao);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			$this->Seminario_economico_model->excluir($result, $args);
			redirect("ecrm/seminario_economico/detalhe/".intval($cd_inscricao), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function certificado()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Seminario_economico_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_seminario_edicao"] = $this->input->post("cd_seminario_edicao", TRUE);
			$args["cd_inscricao"]        = $this->input->post("cd_inscricao", TRUE);
			$args["cd_usuario"]          = $this->session->userdata('codigo');
			
			echo $this->Seminario_economico_model->certificado($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
}
