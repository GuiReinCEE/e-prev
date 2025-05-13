<?php
class documento_digital_participante extends Controller
{

    function __construct()
    {
        parent::Controller();
		
		$this->load->model('clicksign/documento_digital_participante_model');
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

    function index()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();

            $this->load->view('ecrm/documento_digital_participante/index.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
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

			$args["fl_status"]       = $this->input->post("fl_status", TRUE);
			$args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
			$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);
			
			manter_filtros($args);

			$this->documento_digital_participante_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/documento_digital_participante/index_result', $data);		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
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
			exibir_mensagem('ACESSO NÃO PERMITIDO');
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
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
}
?>