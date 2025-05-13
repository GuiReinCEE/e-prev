<?php
class atendimento_agendamento extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function permissao()
	{
    	if(gerencia_in(array('GCM')))
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}
	}

	public function index()
	{
		if($this->permissao())
		{
			$this->load->model('projetos/atendimento_agendamento_model');

			$data = array();

			$this->load->view('ecrm/atendimento_agendamento/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		if($this->permissao())
		{	
			$this->load->model('projetos/atendimento_agendamento_model');

			$args = array(
	            'dt_agenda_ini'    		=> $this->input->post('dt_agenda_ini', TRUE),
	            'dt_agenda_fim'    		=> $this->input->post('dt_agenda_fim', TRUE),         
	            'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
	            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
	            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
	            'dt_inclusao_ini'    	=> $this->input->post('dt_inclusao_ini', TRUE),
	            'dt_inclusao_fim'    	=> $this->input->post('dt_inclusao_fim', TRUE),	
	            'dt_cancelamento_ini'  	=> $this->input->post('dt_cancelamento_ini', TRUE),
	            'dt_cancelamento_fim'   => $this->input->post('dt_cancelamento_fim', TRUE),
	            'fl_cancelado'         	=> $this->input->post('fl_cancelado', TRUE),
	            'fl_compareceu'    		=> $this->input->post('fl_compareceu', TRUE),
	            'nome'                  => $this->input->post('nome', TRUE)
	        );

	        manter_filtros($args);

			$data['collection'] = $this->atendimento_agendamento_model->listar($args);
			
			$this->load->view('ecrm/atendimento_agendamento/index_result', $data);
		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
	}

	public function justificar_cancelamento($cd_atendimento_agendamento)
	{
		if($this->permissao())
		{	
			$this->load->model('projetos/atendimento_agendamento_model');

			$data['row'] = $this->atendimento_agendamento_model->carrega_agendamento($cd_atendimento_agendamento);

			if($data['row']['dt_cancelado'] == '')
			{
				$this->load->view('ecrm/atendimento_agendamento/cancelar', $data);
			}
			else
			{
				redirect('ecrm/atendimento_agendamento');
			}
		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
	}

	public function cancelar_agendamento()
	{
		if($this->permissao())
		{	
			$this->load->model('projetos/atendimento_agendamento_model');

			$re_cripto = $this->atendimento_agendamento_model->get_re_cripto(
				$this->input->post('cd_empresa', TRUE),
	            $this->input->post('cd_registro_empregado', TRUE),
	            $this->input->post('seq_dependencia', TRUE)
			);

			$args = array(
	            'id_app'                            => '5385fa5e2ae966dfb007d75000ec8ed5',
	            're_cripto'                         => $re_cripto['re_cripto'],
	            'cd_atendimento_agendamento'        => $this->input->post('cd_atendimento_agendamento', TRUE),
	            'ds_justificativa_cancelamento'		=> $this->input->post('ds_justificativa_cancelamento', TRUE),
	            'cd_usuario'                        => $this->session->userdata('codigo')
	        );

	        if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
			{
				$url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/cancelar_agendamento';
			}
			else
			{
				$url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/cancelar_agendamento';
			}

	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        $retorno_json = curl_exec($ch);

	        $json = json_decode($retorno_json, true);

	        if($json['error']['status'] == 0)
	        {
	        	redirect('ecrm/atendimento_agendamento');
	        }
	        else
	        {
	        	exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao registrar o cancelamento.</h3>');
	        }	

		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_atendimento_agendamento = 0, $cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0, $cd_atendimento = 0)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/atendimento_agendamento_model');

			$data['tipo'] = $this->atendimento_agendamento_model->get_tipo();

			$data['data_agenda'] = $this->atendimento_agendamento_model->get_data_agenda();

			$data['row'] = array(
				'cd_atendimento_agendamento'     => $cd_atendimento_agendamento,
				'cd_empresa'    				 => $cd_empresa,
				'cd_registro_empregado'     	 => $cd_registro_empregado,
				'seq_dependencia'     			 => $seq_dependencia,
				'cd_atendimento'     			 => $cd_atendimento
			);

			$this->load->view('ecrm/atendimento_agendamento/cadastro',$data);
		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_cadastro()
	{	
		if($this->permissao())
		{
			$this->load->model('projetos/atendimento_agendamento_model');

			$re_cripto = $this->atendimento_agendamento_model->get_re_cripto(
				$this->input->post('cd_empresa', TRUE),
	            $this->input->post('cd_registro_empregado', TRUE),
	            $this->input->post('seq_dependencia', TRUE)
			);

			$args = array(
	            'id_app'                          => '5385fa5e2ae966dfb007d75000ec8ed5',
	            're_cripto'                       => $re_cripto['re_cripto'],
	            'cd_atendimento_agendamento_tipo' => $this->input->post('cd_atendimento_agendamento_tipo', TRUE),
	            'ds_tipo'                         => $this->input->post('ds_tipo', TRUE),
	            'dt_dia'                          => $this->input->post('dt_agenda', TRUE),
	            'email'                           => $this->input->post('email', TRUE),
	            'telefone_1'                      => $this->input->post('telefone_1', TRUE),
	            'telefone_2'                      => $this->input->post('telefone_2', TRUE),
	            'cd_atendimento'				  => $this->input->post('cd_atendimento'),	
	            'cd_usuario'                      => $this->session->userdata('codigo')
	        );

	        if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
			{
				$url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/set_agendamento';
			}
			else
			{
				$url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/set_agendamento';
			}

	        $ch = curl_init();

	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        $retorno_json = curl_exec($ch);

	        $json = json_decode($retorno_json, true);

	        if($this->session->userdata('codigo') == 251)
	        {
	        	echo '<pre>';
	        	print_r($retorno_json);
	        	exit;
	        }

	        if($json['error']['status'] == 0)
	        {
	        	redirect('ecrm/atendimento_agendamento');
	        }
	        else
	        {
	        	exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao registrar o agendamento.</h3>');
	        }
		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_compareceu()
	{
		$this->load->model('projetos/atendimento_agendamento_model');
		
		$args = array(
			'cd_atendimento_agendamento' => $this->input->post('cd_atendimento_agendamento', TRUE),
			'fl_compareceu'              => $this->input->post('fl_compareceu', TRUE)
		);

		$this->atendimento_agendamento_model->salvar_compareceu($args);
	}

	public function editar_agendamento($cd_atendimento_agendamento)
	{
		if($this->permissao())
		{	
			$this->load->model('projetos/atendimento_agendamento_model');

			$data['row'] = $this->atendimento_agendamento_model->carrega_agendamento($cd_atendimento_agendamento);

			if($data['row']['dt_cancelado'] == '')
			{
				$this->load->view('ecrm/atendimento_agendamento/editar_agendamento', $data);
			}
			else
			{
				redirect('ecrm/atendimento_agendamento');
			}
		}
		else 
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}	
	}

	Public function salvar_editar_agendamento()
	{
		$this->load->model('projetos/atendimento_agendamento_model');

		$cd_atendimento_agendamento = $this->input->post('cd_atendimento_agendamento', TRUE);
		
		$args = array(
			'cd_atendimento_agendamento' => $cd_atendimento_agendamento,
			'email'                      => $this->input->post('email', TRUE),
			'ds_link_zoom'				 => $this->input->post('ds_link_zoom', TRUE),	
			'ds_senha_zoom'				 => $this->input->post('ds_senha_zoom', TRUE)	
		);

		$this->atendimento_agendamento_model->salvar_editar_agendamento($args);

		redirect('ecrm/atendimento_agendamento/editar_agendamento/'.$cd_atendimento_agendamento);
	}

	Public function salvar_envio_email($cd_atendimento_agendamento)
	{
		$this->load->model(array('projetos/atendimento_agendamento_model', 'projetos/eventos_email_model'));

		$agendamento = $this->atendimento_agendamento_model->carrega_agendamento($cd_atendimento_agendamento);

		$cd_evento = 423;

        $email = $this->eventos_email_model->carrega($cd_evento);
		
		$args = array(
			'cd_atendimento_agendamento' => $cd_atendimento_agendamento,
			'cd_usuario_envio_email'     => $this->session->userdata('codigo')
		);

		$this->atendimento_agendamento_model->salvar_envio_email($args);

		$tags = array('[DS_NOME]', '[DT_AGENDA]',  '[LINK]');

        $subs = array(
            $agendamento['nome'],
            $agendamento['dt_agenda'],
            $agendamento['ds_link_zoom']
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Agendamento de Atendimento',
            'assunto' => $email['assunto'],
            'para'    => $agendamento['email'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args); 

		redirect('ecrm/atendimento_agendamento');
	}

}