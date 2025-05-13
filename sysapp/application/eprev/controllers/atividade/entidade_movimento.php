<?php
class entidade_movimento extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('entidades/movimento_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GP', 'AI')))
		{							
			$this->load->view('atividade/entidade_movimento/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {		
		if(gerencia_in(array('GP', 'AI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['nr_ano']          = $this->input->post("nr_ano", TRUE);
			$args['nr_numero']       = $this->input->post("nr_numero", TRUE);
			$args['nr_mes_ref']      = $this->input->post("nr_mes_ref", TRUE);
			$args['nr_ano_ref']      = $this->input->post("nr_ano_ref", TRUE);
			$args['dt_envio_ini']    = $this->input->post("dt_envio_ini", TRUE);
			$args['dt_envio_fim']    = $this->input->post("dt_envio_fim", TRUE);
			$args['dt_recebido_ini'] = $this->input->post("dt_recebido_ini", TRUE);
			$args['dt_recebido_fim'] = $this->input->post("dt_recebido_fim", TRUE);
			$args['dt_retorno_ini']  = $this->input->post("dt_retorno_ini", TRUE);
			$args['dt_retorno_fim']  = $this->input->post("dt_retorno_fim", TRUE);
			
			manter_filtros($args);
			
			$this->movimento_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/entidade_movimento/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function receber($cd_movimento)
	{
		if(gerencia_in(array('GP', 'AI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento'] = $cd_movimento;
			
			$this->movimento_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->movimento_model->anexo_entidade($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/entidade_movimento/receber', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_recebimento($cd_movimento)
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento'] = $cd_movimento;
			$args['cd_usuario']   = $this->session->userdata('codigo');
			
			$this->movimento_model->receber($result, $args);
			
			redirect('atividade/entidade_movimento/receber/'.$cd_movimento, 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function retorno($cd_movimento)
	{
		if(gerencia_in(array('GP', 'AI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento'] = $cd_movimento;
			
			$this->movimento_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->movimento_model->retorno_tipo($result, $args);
			$data['arr_retorno'] = $result->result_array();
			
			$this->load->view('atividade/entidade_movimento/retorno', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_anexo()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento']              = $this->input->post("cd_movimento", TRUE);
			$args['cd_movimento_retorno_tipo'] = $this->input->post("cd_movimento_retorno_tipo", TRUE);
			$args['arquivo_nome']              = $this->input->post("arquivo_nome", TRUE);
			$args['arquivo']                   = $this->input->post("arquivo", TRUE);
			$args['cd_usuario']                = $this->session->userdata("codigo");
			
			copy("./up/entidade_movimento/".$args["arquivo"], "./../eletroceee/app/up/entidade/".$args["arquivo"]);
			
			$this->movimento_model->salvar_anexo($result, $args);
			
			redirect('atividade/entidade_movimento/retorno/'.$args['cd_movimento'], 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function listar_anexo()
	{
		if(gerencia_in(array('GP', 'AI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento'] = $this->input->post("cd_movimento", TRUE);
			
			$this->movimento_model->listar_anexo($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/entidade_movimento/anexo_result', $data);
			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir_anexo($cd_movimento, $cd_movimento_anexo)
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento_anexo'] = $cd_movimento_anexo;
			$args['cd_usuario']         = $this->session->userdata("codigo");
			
			$this->movimento_model->excluir_anexo($result, $args);
			
			redirect('atividade/entidade_movimento/retorno/'.$cd_movimento, 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_retorno($cd_movimento)
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_movimento'] = $cd_movimento;
			$args['cd_usuario']   = $this->session->userdata("codigo");
			
			$this->movimento_model->salvar_retorno($result, $args);
			
			redirect('atividade/entidade_movimento/retorno/'.$cd_movimento, 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}