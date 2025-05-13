<?php

class pagamento_cheque extends Controller
{
	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('escritorio_juridico/pagamento_cheque_model');
    }
	
	public function index()
    {
		$result   = null;
		$data     = array();
		$args     = array();
		
		$this->pagamento_cheque_model->correspondentes($result, $args);
		$data['arr_correspondente'] = $result->result_array();
		
		$this->pagamento_cheque_model->processos($result, $args);
		$data['arr_processo'] = $result->result_array();
		
		$this->load->view('atividade/pagamento_cheque/index', $data);
    }
	
	public function listar()
    {
		$result   = null;
		$data     = array();
		$args     = array();
			
		$arr_ano_nr_processo = explode("/",$this->input->post("ano_nr_processo", TRUE));
		
		$args['nr_ano']                         = $this->input->post("nr_ano", TRUE);
		$args['nr_numero']                      = $this->input->post("nr_numero", TRUE);
		$args['cpf']                            = $this->input->post("cpf", TRUE);
		$args['nome']                           = $this->input->post("nome", TRUE);
		$args['cd_calculo_irrf_correspondente'] = $this->input->post("cd_calculo_irrf_correspondente", TRUE);
		$args['nr_processo_ano']                = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[0] : ''); 
		$args['nr_processo']                    = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[1] : ''); 
		$args['dt_pagamento_ini']               = $this->input->post("dt_pagamento_ini", TRUE);
		$args['dt_pagamento_fim']               = $this->input->post("dt_pagamento_fim", TRUE);
		$args['fl_status']                      = $this->input->post("fl_status", TRUE);
		$args['cd_empresa']                     = $this->input->post("cd_empresa", TRUE);
		$args['cd_registro_empregado']          = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']                = $this->input->post("seq_dependencia", TRUE);
		
		$this->pagamento_cheque_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('atividade/pagamento_cheque/index_result', $data);
    }
	
	public function rejeitar($cd_pagamento_cheque)
	{
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_pagamento_cheque'] = $cd_pagamento_cheque;
		
		$this->pagamento_cheque_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/pagamento_cheque/rejeitar', $data);
	}
	
	public function salvar_rejeitar()
	{
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_pagamento_cheque']           = $this->input->post("cd_pagamento_cheque", TRUE);
		$args['ds_pagamento_cheque_rejeitado'] = $this->input->post("ds_pagamento_cheque_rejeitado", TRUE);
		$args['cd_usuario']                    = $this->session->userdata("codigo");
		
		$this->pagamento_cheque_model->salvar_rejeitar($result, $args);
		
		redirect('atividade/pagamento_cheque', 'refresh');
	}
	
	public function cadastro($cd_pagamento_cheque)
    {
        if (gerencia_in(array('GP')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_pagamento_cheque'] = $cd_pagamento_cheque;
						
			$this->pagamento_cheque_model->carrega($result, $args);
			$data['row'] = $result->row_array();
						
			$this->pagamento_cheque_model->listar_anexo_calculo_irrf($result, $args);
			$data['collection_anexo_calculo'] = $result->result_array();
			
			$this->load->view('atividade/pagamento_cheque/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	
	public function liberar($cd_pagamento_cheque)
	{
		$result   = null;
		$data     = array();
        $args     = array();
	
		$args['cd_pagamento_cheque'] = $cd_pagamento_cheque;
		$args['cd_usuario']          = $this->session->userdata("codigo");
		
		$this->pagamento_cheque_model->liberar($result, $args);

		redirect("atividade/pagamento_cheque", "refresh");
	}
	
	public function anexo($cd_pagamento_cheque)
    {
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_pagamento_cheque'] = $cd_pagamento_cheque;
		
		$this->pagamento_cheque_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/pagamento_cheque/anexo', $data);
    }
	
	public function listar_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
			
		$args['cd_pagamento_cheque'] = $this->input->post("cd_pagamento_cheque", TRUE);
		
		$this->pagamento_cheque_model->listar_anexo($result, $args);
        $data['collection'] = $result->result_array();

		$this->load->view('atividade/pagamento_cheque/anexo_result', $data);
	}
	
	public function salvar_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['cd_calculo_irrf']     = $this->input->post("cd_calculo_irrf", TRUE);
		$args['cd_pagamento_cheque'] = $this->input->post("cd_pagamento_cheque", TRUE);
		$args['arquivo_nome']        = $this->input->post("arquivo_nome", TRUE);
		$args['arquivo']             = $this->input->post("arquivo", TRUE);
		$args['cd_usuario']          = $this->session->userdata("codigo");
		
		copy("./up/pagamento_cheque/".$args["arquivo"], "./../eletroceee/app/up/escritorio_juridico/".$args["arquivo"]);
		
		$this->pagamento_cheque_model->salvar_anexo($result, $args);
		
		redirect('atividade/pagamento_cheque/anexo/'.$args['cd_pagamento_cheque'], 'refresh');
	}
	
	public function excluir_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
			
		$args['cd_pagamento_cheque_anexo'] = $this->input->post("cd_pagamento_cheque_anexo", TRUE);
		$args['cd_usuario']                = $this->session->userdata("codigo");
		
		$this->pagamento_cheque_model->excluir_anexo($result, $args);
	}
}
?>