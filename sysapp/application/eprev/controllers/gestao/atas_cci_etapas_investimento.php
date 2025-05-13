<?php

class atas_cci_etapas_investimento extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('gestao/atas_cci_etapas_investimento_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GIN')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('gestao/atas_cci_etapas_investimento/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		if (gerencia_in(array('GIN')))
        {
			$args = Array();
			$data = Array();
			$result = null;
								
			$args['ds_atas_cci_etapas_investimento'] = $this->input->post("ds_atas_cci_etapas_investimento", TRUE);
			$args['email']  				         = $this->input->post("nr_numero", TRUE);						
							
			manter_filtros($args);

			$this->atas_cci_etapas_investimento_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/atas_cci_etapas_investimento/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function cadastro($cd_atas_cci_etapas_investimento = 0)
	{
		if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_atas_cci_etapas_investimento'] = $cd_atas_cci_etapas_investimento;
			
			if(intval($args['cd_atas_cci_etapas_investimento']) == 0)
			{
				$data['row'] = array(
					'cd_atas_cci_etapas_investimento' => intval($args['cd_atas_cci_etapas_investimento']),
					'ds_atas_cci_etapas_investimento' => '',
					'qt_dias'                         => '',
					'fl_dia_util'                     => '',
					'ds_assunto'                      => '',
					'ds_texto'                        => '',
					'fl_responsavel'                  => '',
					'email'                           => ''
				);
			}
			else
			{
				$this->atas_cci_etapas_investimento_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/atas_cci_etapas_investimento/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar()
	{
		if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_atas_cci_etapas_investimento'] = $this->input->post("cd_atas_cci_etapas_investimento", TRUE);
			$args['ds_atas_cci_etapas_investimento'] = $this->input->post("ds_atas_cci_etapas_investimento", TRUE);
			$args['qt_dias']                         = $this->input->post("qt_dias", TRUE);
			$args['fl_dia_util']                     = $this->input->post("fl_dia_util", TRUE);
			$args['ds_assunto']                      = $this->input->post("ds_assunto", TRUE);
			$args['ds_texto']                        = $this->input->post("ds_texto", TRUE);
			$args['fl_responsavel']                  = $this->input->post("fl_responsavel", TRUE);
			$args['email']                           = str_replace (" ", "", $this->input->post("email", TRUE));	
			$args['cd_usuario']                      = $this->session->userdata("codigo");
			
			$this->atas_cci_etapas_investimento_model->salvar($result, $args);
			
			redirect("gestao/atas_cci_etapas_investimento", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}
?>