<?php
class contracheque_participante extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
        $this->load->model('projetos/contracheque_controle_model');
    }

    function index()
    {
        if (gerencia_in(array('GB', 'GAP')))
        {		
			$data = Array();
			$args = Array();
			$result = null;

			$this->load->view('atividade/contracheque_participante/index', $data);
		}
        else
        {
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }

    function listar()
    {
        if (gerencia_in(array('GB')))
        {	        
			$data = Array();
			$args = Array();
			$result = null;

			$args['dt_pagamento']    = $this->input->post('dt_pagamento', TRUE);
			$args['tp_contracheque'] = $this->input->post('tp_contracheque', TRUE);
			
			$data['dt_pagamento']    = $this->input->post('dt_pagamento', TRUE);

			manter_filtros($args);
			
			$this->contracheque_controle_model->verifica_liberacao($result, $args);
			$data["ar_libera"] = $result->row_array();
			
			$this->contracheque_controle_model->resumo_controle($result, $args);
			$ar_controle = $result->result_array();		
			
			$data['bt_gerar']       = false;
			$data['bt_envia_email'] = false;
			$data['dt_envio_email'] = "";
			$data['ds_envio_email'] = "";
			
			if(count($ar_controle) == 0)
			{
				$this->contracheque_controle_model->resumo_folha($result, $args);
				$data['ar_resumo'] = $result->result_array();
				
				$data['bt_gerar'] = true;
			}
			else
			{
				$data['ar_resumo'] = $ar_controle;
				
				$this->contracheque_controle_model->verifica_envio($result, $args);
				$ar_envio = $result->row_array();
				
				if($ar_envio["fl_envio"] == "N")
				{
					$data['bt_gerar'] = false;
					$data['bt_envia_email'] = true;
				}
				else
				{
					$this->contracheque_controle_model->get_data_envio($result, $args);
					$ar_envio = $result->row_array();	
					
					$data['dt_envio_email']	= $ar_envio['dt_envio_email'];
					$data['ds_envio_email']	= $ar_envio['ds_envio_email'];
				}
			}

			$this->load->view('atividade/contracheque_participante/index_result', $data);
		}
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }		
    }
	
	function gerar()
	{
        if (gerencia_in(array('GB')))
        {				
			$result = null;
			$data = Array();
			$args = Array();

			$args['dt_pagamento'] = $this->input->post('dt_pagamento', TRUE);
			$args['cd_usuario']   = $this->session->userdata('codigo');	
			
			$this->contracheque_controle_model->gerar($result, $args);
			$this->listar();	
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}
	
	function enviarEmail()
	{
        if (gerencia_in(array('GB')))
        {				
			$result = null;
			$data = Array();
			$args = Array();

			$args['dt_pagamento'] = $this->input->post('dt_pagamento', TRUE);
			$args['cd_usuario']   = $this->session->userdata('codigo');	
			
			$this->contracheque_controle_model->enviar_email($result, $args);
			$this->listar();	
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
	
	function emails()
	{
		if (gerencia_in(array('GAP', 'GB')))
        {				
			$data = Array();
			$args = Array();
			$result = null;
			
			$this->load->view('atividade/contracheque_participante/emails', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	function emails_listar()
	{
		if (gerencia_in(array('GAP', 'GB')))
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
			
			$this->contracheque_controle_model->emails_listar($result, $args);
			$data['collection'] = $result->result_array();	

			$this->load->view('atividade/contracheque_participante/emails_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
}
?>