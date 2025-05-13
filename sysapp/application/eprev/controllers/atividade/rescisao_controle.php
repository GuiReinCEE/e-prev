<?php

class rescisao_controle extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/rescisao_controle_model');
    }
	
	public function index()
    {
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('atividade/rescisao_controle/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function listar()
    {
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_empresa']             = $this->input->post("cd_empresa", TRUE);    
			$args['dt_digita_demissao_ini'] = $this->input->post("dt_digita_demissao_ini", TRUE);    
			$args['dt_digita_demissao_fim'] = $this->input->post("dt_digita_demissao_fim", TRUE);   
			$args['fl_status']              = $this->input->post("fl_status", TRUE);   
			$args['fl_email']               = $this->input->post("fl_email", TRUE);   
						
			manter_filtros($args);

			$this->rescisao_controle_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('atividade/rescisao_controle/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function adicionar()
    {
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$check = $this->input->post("check", TRUE);  
			
			$args['cd_usuario'] = $this->session->userdata('codigo');
			
			foreach($check as $item)
			{
				$arr = explode("_", $item);
				
				if(count($arr) == 3)
				{
					$args['cd_empresa']            = $arr[0];
					$args['cd_registro_empregado'] = $arr[1];
					$args['seq_dependencia']       = $arr[2];
					
					$this->rescisao_controle_model->adicionar($result, $args);
				}
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function remover()
    {
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$re = $this->input->post("re", TRUE);  
			
			$args['cd_usuario'] = $this->session->userdata('codigo');
			
			$arr = explode("_", $re);
				
			if(count($arr) == 3)
			{
				$args['cd_empresa']            = $arr[0];
				$args['cd_registro_empregado'] = $arr[1];
				$args['seq_dependencia']       = $arr[2];
				
				$this->rescisao_controle_model->remover($result, $args);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function enviar()
    {
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;  
			
			$args['cd_usuario'] = $this->session->userdata('codigo');

			$this->rescisao_controle_model->enviar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function acompanhamento($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;  
			
			$args['cd_empresa']            = $cd_empresa;
			$args['cd_registro_empregado'] = $cd_registro_empregado;
			$args['seq_dependencia']       = $seq_dependencia;
			
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;
			
			$this->rescisao_controle_model->listar_acompanhamento($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/rescisao_controle/acompanhamento', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function salvar_acompanhamento()
	{
		if(gerencia_in(array('GAP')))
		{
			$args = Array();
			$data = Array();
			$result = null;  
		
			$args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);    
			$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);    
			$args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);   
			$args['descricao']             = $this->input->post("descricao", TRUE);  
			$args['cd_usuario']            = $this->session->userdata('codigo');
			
			$this->rescisao_controle_model->salvar_acompanhamento($result, $args);
			
			redirect("atividade/rescisao_controle/acompanhamento/".$args['cd_empresa'].'/'.$args['cd_registro_empregado'].'/'.$args['seq_dependencia'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}

?>