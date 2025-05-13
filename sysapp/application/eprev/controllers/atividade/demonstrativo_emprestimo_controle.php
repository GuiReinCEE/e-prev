<?php

class demonstrativo_emprestimo_controle extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/demonstrativo_emprestimo_controle_model');
    }
	
	function index()
    {
        if (gerencia_in(array('GAP')))
        {		
			$data = Array();
			$args = Array();
			$result = null;

			$this->load->view('atividade/demonstrativo_emprestimo_controle/index', $data);
		}
        else
        {
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
	
	function listar()
    {
        if (gerencia_in(array('GAP')))
        {	        
			$data = Array();
			$args = Array();
			$result = null;

			$args['dt_inicio']     = $this->input->post('dt_inicio', TRUE);
			$args['dt_final']      = $this->input->post('dt_final', TRUE);
			$args['fl_enviado']    = $this->input->post('fl_enviado', TRUE);
			$args['fl_email']      = $this->input->post('fl_email', TRUE);
			$args['fl_eletronico'] = $this->input->post("fl_eletronico", TRUE);

			manter_filtros($args);
			
			$this->demonstrativo_emprestimo_controle_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('atividade/demonstrativo_emprestimo_controle/index_result', $data);
		}
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }		
    }
	
	function enviar()
    {
        if (gerencia_in(array('GAP')))
        {	        
			$data = Array();
			$args = Array();
			$result = null;

			$args['dt_inicio']         = $this->input->post('dt_inicio', TRUE);
			$args['dt_final']          = $this->input->post('dt_final', TRUE);			
			$args['part_selecionado']  = $this->input->post('part_selecionado', TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$this->demonstrativo_emprestimo_controle_model->enviar($result, $args);
		}
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }		
    }	
	
	function emails()
	{
		if (gerencia_in(array('GAP')))
        {				
			$data = Array();
			$args = Array();
			$result = null;
			
			$this->load->view('atividade/demonstrativo_emprestimo_controle/emails', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	function emails_listar()
	{
		if (gerencia_in(array('GAP')))
        {				
			$data = Array();
			$args = Array();
			$result = null;
			
			$args['dt_email_ini']          = $this->input->post('dt_email_ini', TRUE);
			$args['dt_email_fim']          = $this->input->post('dt_email_fim', TRUE);
			$args['fl_retornou']           = $this->input->post('fl_retornou', TRUE);
			$args['cd_empresa']            = $this->input->post('cd_empresa', TRUE);
			$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
			$args['seq_dependencia']       = $this->input->post('seq_dependencia', TRUE);
			
			$this->demonstrativo_emprestimo_controle_model->emails_listar($result, $args);
			$data['collection'] = $result->result_array();	

			$this->load->view('atividade/demonstrativo_emprestimo_controle/emails_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}	
	
}
?>