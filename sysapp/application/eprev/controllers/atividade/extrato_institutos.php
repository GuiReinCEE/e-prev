<?php
class Extrato_institutos extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GP')))
        {
        	return TRUE;
        }
        else
        {
        	return FALSE;
        }
    }
	
	public function index()
    {
        if($this->get_permissao())
        {		
			$this->load->view('atividade/extrato_institutos/index');
		}
        else
        {
			exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }
	
	public function listar()
    {      
    	$this->load->model('projetos/extrato_institutos_model');

    	$args = array(
    		'dt_emissao_extrato_ini' => $this->input->post('dt_emissao_extrato_ini', TRUE),
    		'dt_emissao_extrato_fim' => $this->input->post('dt_emissao_extrato_fim', TRUE),
    		'fl_recebido_extrato'    => $this->input->post('fl_recebido_extrato', TRUE),
    		'fl_enviado'             => $this->input->post('fl_enviado', TRUE),
    		'fl_email'               => $this->input->post('fl_email', TRUE),
    		'fl_eletronico'          => $this->input->post('fl_eletronico', TRUE)
    	);

		manter_filtros($args);
		
		$data['collection'] = $this->extrato_institutos_model->listar($args);

		$this->load->view('atividade/extrato_institutos/index_result', $data);
    }
	
	public function enviar()
    {
        if($this->get_permissao())
        {
        	$this->load->model('projetos/extrato_institutos_model');

			$args = array(
				'arr_re_cripto'          => $this->input->post('arr_re_cripto', TRUE),
				'dt_emissao_extrato_ini' => $this->input->post('dt_emissao_extrato_ini', TRUE),
				'dt_emissao_extrato_fim' => $this->input->post('dt_emissao_extrato_fim', TRUE),
                'fl_envio'               => 'E',
				'cd_usuario'             => $this->session->userdata('codigo')
			);

			$this->extrato_institutos_model->enviar($args);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }	

    public function enviar_correio()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/extrato_institutos_model');

            $args = array(
                'arr_re_cripto'          => $this->input->post('arr_re_cripto', TRUE),
                'dt_emissao_extrato_ini' => $this->input->post('dt_emissao_extrato_ini', TRUE),
                'dt_emissao_extrato_fim' => $this->input->post('dt_emissao_extrato_fim', TRUE),
                'fl_envio'               => 'C',
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            $this->extrato_institutos_model->enviar($args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }       
    }   

    public function enviar_manual()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/extrato_institutos_model');

            $args = array(
                'arr_re_cripto'          => $this->input->post('arr_re_cripto', TRUE),
                'dt_emissao_extrato_ini' => $this->input->post('dt_emissao_extrato_ini', TRUE),
                'dt_emissao_extrato_fim' => $this->input->post('dt_emissao_extrato_fim', TRUE),
                'fl_envio'               => 'M',
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            $this->extrato_institutos_model->enviar($args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }       
    }   
	
	public function emails()
	{
		if($this->get_permissao())
        {				
			$this->load->view('atividade/extrato_institutos/emails');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
	}
	
	public function emails_listar()
	{
		$this->load->model('projetos/extrato_institutos_model');

		$args = array(
			'dt_email_ini'          => $this->input->post('dt_email_ini', TRUE),
			'dt_email_fim'          => $this->input->post('dt_email_fim', TRUE),
			'fl_retornou'           => $this->input->post('fl_retornou', TRUE),
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE)
		);
		
		$data['collection'] = $this->extrato_institutos_model->emails_listar($args);

		$this->load->view('atividade/extrato_institutos/emails_result', $data);
	}

    public function atualizar_documento($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('projetos/extrato_institutos_model');

        $this->extrato_institutos_model->atualizar_documento($cd_empresa, $cd_registro_empregado, $seq_dependencia);

        redirect('atividade/extrato_institutos', 'refresh');
    }
}
?>