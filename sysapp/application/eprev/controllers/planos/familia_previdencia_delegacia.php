<?php
class familia_previdencia_delegacia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('familia_previdencia/Familia_previdencia_delegacia_model');
		$args=array();	
	
		if(gerencia_in(array('GRI')))
		{
			$data = Array();			
			$this->load->view('planos/familia_previdencia_delegacia/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
        $this->load->model('familia_previdencia/Familia_previdencia_delegacia_model');
		
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->Familia_previdencia_delegacia_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/familia_previdencia_delegacia/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function cadastro($cd_delegacia = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_model');
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$data['cd_delegacia'] = intval($cd_delegacia);
			
			if(intval($cd_delegacia) == 0)
			{
				$data['row'] = Array('cd_delegacia'  => intval($cd_delegacia) , 
					                 'nome'         => '',
					                 'endereco' => '',
					                 'cidade' => '',
					                 'uf' => '',
					                 'telefone' => '',
					                 'email' => '',
									 'dt_inclusao'  => '',
									 'dt_exclusao'  => ''
									);
			}
			else
			{
				$args['cd_delegacia'] = intval($cd_delegacia);
				$this->Familia_previdencia_delegacia_model->delegacia($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('planos/familia_previdencia_delegacia/cadastro.php',$data);
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
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_delegacia"] = $this->input->post("cd_delegacia", TRUE);
			$args["nome"]         = $this->input->post("nome", TRUE);
			$args["endereco"]     = $this->input->post("endereco", TRUE);
			$args["cidade"]       = $this->input->post("cidade", TRUE);
			$args["uf"]           = $this->input->post("uf", TRUE);
			$args["telefone"]     = $this->input->post("telefone", TRUE);
			$args["email"]        = $this->input->post("email", TRUE);
			
			$cd_new = $this->Familia_previdencia_delegacia_model->salvar($result, $args);
			redirect("planos/familia_previdencia_delegacia/cadastro/".$cd_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function excluir($cd_delegacia = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_delegacia"] = intval($cd_delegacia);
			$this->Familia_previdencia_delegacia_model->excluir($result, $args);
			redirect("planos/familia_previdencia_delegacia/cadastro/".$cd_delegacia, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

}
