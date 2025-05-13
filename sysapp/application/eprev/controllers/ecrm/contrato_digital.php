<?php
class contrato_digital extends Controller
{

    function __construct()
    {
        parent::Controller();
		
		$this->load->model('clicksign/contrato_digital_model');
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GCM')))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    function index($fl_pendente = 'S', $cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();

			#### PENDENTE PARTICIPANTE OU FUNDAวรO ####
			$data['ar_pendente'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_pendente'][] = array('value' => 'N', 'text' => 'Nใo');
			
			#### PENDENTE PARTICIPANTE ####
			$data['ar_pendente_participante'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_pendente_participante'][] = array('value' => 'N', 'text' => 'Nใo');	

			#### CONTRATO CONCLUIDO (ASSINADO) ####
			$data['ar_concluido'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_concluido'][] = array('value' => 'N', 'text' => 'Nใo');	

			#### FINALIZADO OU CANCELADO ####
			$data['ar_encerrado'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_encerrado'][] = array('value' => 'N', 'text' => 'Nใo');	

			$data['fl_pendente']           = $fl_pendente;			
			$data['cd_empresa']            = $cd_empresa;			
			$data['cd_registro_empregado'] = $cd_registro_empregado;			
			$data['seq_dependencia']       = $seq_dependencia;			
			
            $this->load->view('ecrm/contrato_digital/index.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NรO PERMITIDO');
        }
    }

    function listar()
    {
        CheckLogin();
        if($this->get_permissao())
        {       
			$args = Array();
			$data = Array();
			$result = null;

			$args["cpf"]                      = $this->input->post("cpf", TRUE);
			$args["cd_empresa"]               = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]    = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]          = $this->input->post("seq_dependencia", TRUE);
			$args["nome"]                     = $this->input->post("nome", TRUE);
			$args["fl_pendente"]              = $this->input->post("fl_pendente", TRUE);
			$args["fl_pendente_participante"] = $this->input->post("fl_pendente_participante", TRUE);
			$args["fl_concluido"]             = $this->input->post("fl_concluido", TRUE);
			$args["fl_encerrado"]             = $this->input->post("fl_encerrado", TRUE);
			$args["dt_inclusao_ini"]          = $this->input->post("dt_inclusao_ini", TRUE);
			$args["dt_inclusao_fim"]          = $this->input->post("dt_inclusao_fim", TRUE);
			$args["dt_limite_ini"]            = $this->input->post("dt_limite_ini", TRUE);
			$args["dt_limite_fim"]            = $this->input->post("dt_limite_fim", TRUE);
			$args["dt_concluido_ini"]         = $this->input->post("dt_concluido_ini", TRUE);
			$args["dt_concluido_fim"]         = $this->input->post("dt_concluido_fim", TRUE);
			$args["dt_cancelado_ini"]         = $this->input->post("dt_cancelado_ini", TRUE);
			$args["dt_cancelado_fim"]         = $this->input->post("dt_cancelado_fim", TRUE);
			$args["dt_finalizado_ini"]        = $this->input->post("dt_finalizado_ini", TRUE);
			$args["dt_finalizado_fim"]        = $this->input->post("dt_finalizado_fim", TRUE);
			
			manter_filtros($args);

			$this->contrato_digital_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/contrato_digital/index_result', $data);		
		}
		else
		{
			exibir_mensagem('ACESSO NรO PERMITIDO');
		}
    }
	
	public function cadastro($cd_contrato_digital = 0)
	{
        CheckLogin();
        if($this->get_permissao())
		{

			if(intval($cd_contrato_digital) == 0)
			{
				redirect('ecrm/contrato_digital/', 'refresh');
			}
			else
			{
				$data['row'] = $this->contrato_digital_model->carrega(intval($cd_contrato_digital));
			}
			
			$this->load->view('ecrm/contrato_digital/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NรO PERMITIDO');
		} 		
	}	
	
    function listarAssinadores()
    {
        CheckLogin();
        if($this->get_permissao())
        {       
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_contrato_digital"] = $this->input->post("cd_contrato_digital", TRUE);
			
			manter_filtros($args);

			$this->contrato_digital_model->listarAssinadores($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/contrato_digital/cadastro_result', $data);		
		}
		else
		{
			exibir_mensagem('ACESSO NรO PERMITIDO');
		}
    }	
}
?>