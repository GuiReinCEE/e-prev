<?php
class avisos_inadimplencia_emprestimo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
        $this->load->model('projetos/avisos_inadimplencia_emprestimo_model');
    }

    function index($nr_mes = "", $nr_ano = "")
    {
		$nr_mes = (((intval($nr_mes) >= 1) and (intval($nr_mes) <= 12)) ? intval($nr_mes) : date("m"));
		$nr_ano = ((intval($nr_ano) > 2000) ? intval($nr_ano) : date("Y"));
	
        if (gerencia_in(array('GP', 'GFC')))
        {		
			$data = Array();
			$args = Array();
			$result = null;

			$data['dt_referencia_aviso'] = "01/".str_pad($nr_mes, 2, "0", STR_PAD_LEFT) ."/".str_pad($nr_ano, 4, "0", STR_PAD_LEFT);
			
			$this->load->view('atividade/avisos_inadimplencia_emprestimo/index', $data);
		}
        else
        {
			exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }

    function listar()
    {
		if (gerencia_in(array('GFC')))
        {	        
			$data = Array();
			$args = Array();
			$result = null;

			$args['nr_mes'] = str_pad($this->input->post('nr_mes', TRUE), 2, "0", STR_PAD_LEFT);
			$args['nr_ano'] = str_pad($this->input->post('nr_ano', TRUE), 4, "0", STR_PAD_LEFT);
			
			manter_filtros($args);
			
			$this->avisos_inadimplencia_emprestimo_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('atividade/avisos_inadimplencia_emprestimo/index_result', $data);
		}
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }		
    }
	
    function enviar()
    {
        if (gerencia_in(array('GFC')))
        {	        
			$data = Array();
			$args = Array();
			$result = null;
			
			$args['nr_mes']            = str_pad($this->input->post('nr_mes', TRUE), 2, "0", STR_PAD_LEFT);
			$args['nr_ano']            = str_pad($this->input->post('nr_ano', TRUE), 4, "0", STR_PAD_LEFT);
			$args['part_selecionado']  = $this->input->post('part_selecionado', TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$this->avisos_inadimplencia_emprestimo_model->enviar($result, $args);
		}
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }		
    }	
	
	function emails()
	{
		if (gerencia_in(array('GP', 'GFC')))
        {				
			$data = Array();
			$args = Array();
			$result = null;
			
			$this->load->view('atividade/avisos_inadimplencia_emprestimo/emails', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	function emails_listar()
	{
		if (gerencia_in(array('GP')))
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
			
			$this->avisos_inadimplencia_emprestimo_model->emails_listar($result, $args);
			$data['collection'] = $result->result_array();	

			$this->load->view('atividade/avisos_inadimplencia_emprestimo/emails_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}	
}
?>