<?php
class Atividade_prioridade extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
	}
	
	private function get_permissao()
	{
		if($this->session->userdata("divisao") == "GTI")
		{
			return TRUE;
		}
		elseif(
			($this->session->userdata("tipo") == "G") OR 
			($this->session->userdata("indic_13") == "S") OR 
			($this->session->userdata("indic_10") == "S")
		)
		{
			return TRUE;
		}	
		else
		{
			return FALSE;
		}	
	}

	//$this->load->model('projetos/atividade_prioridade_model');

	public function index()
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/atividade_prioridade_model');

			if(($this->session->userdata('divisao') == 'GTI') AND ($this->session->userdata('tipo') != 'G'))
			{
				$data['fl_atendente_info']         = TRUE;
				$data['fl_atividade_prior_editar'] = 'N';

				#### ATENDENTE ####
				$data['cd_atendente']   = $this->session->userdata('codigo');
				$data['ar_atendente'][] = array(
					'value' => $this->session->userdata('codigo'), 
					'text'  => $this->session->userdata('nome')
				);
				
				#### AREA SOLICITANTE ####
				$data['ar_area_solicitante'] = $this->atividade_prioridade_model->get_gerencia_solicitante($this->session->userdata('codigo'));
				$data['cd_area_solicitante'] = '';
			}
			else
			{
				$data['fl_atendente_info']         = FALSE;
				$data['fl_atividade_prior_editar'] = (($this->get_permissao() == TRUE) ? 'S' : 'N');
				
				#### ATENDENTE ####
				$data['ar_atendente'] = $this->atividade_prioridade_model->get_atendente($this->session->userdata('divisao'));
				$data['cd_atendente'] = '';
				
				#### AREA SOLICITANTE ####
				$data['ar_area_solicitante'][] = array(
					'value' => $this->session->userdata('divisao'), 
					'text'  => $this->session->userdata('divisao')
				);

				$data['cd_area_solicitante'] = $this->session->userdata("divisao");
			}
			
			$this->load->view('atividade/atividade_prioridade/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function listar()
	{
		$this->load->model('projetos/atividade_prioridade_model');

		$args = array(
			'cd_atendente'              => $this->input->post('cd_atendente', TRUE),
			'cd_area_solicitante'       => $this->input->post('cd_area_solicitante', TRUE),
			'fl_atividade_prior_editar' => $this->input->post('fl_atividade_prior_editar', TRUE)
		);
		
		manter_filtros($args);

		$data = array(
			'collection'                => $this->atividade_prioridade_model->listar($args),
			'fl_atividade_prior_editar' => $this->input->post('fl_atividade_prior_editar', TRUE)
		);
		
		$this->load->view('atividade/atividade_prioridade/index_result', $data);
	}
	
	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/atividade_prioridade_model');

			$ar_atividade           = $this->input->post('ar_atividade', TRUE);
			$ar_prioridade          = $this->input->post('ar_prioridade', TRUE);
			$ar_prioridade_anterior = $this->input->post('ar_prioridade_anterior', TRUE);
					
			$nr_conta = 0;
			$nr_fim   = count($ar_atividade);
			$ar_prioridade_nova = array();

			while($nr_conta < $nr_fim)
			{
				if($ar_prioridade[$nr_conta] != $ar_prioridade_anterior[$nr_conta])
				{
					$ar_prioridade_nova[] = array(
						'cd_atividade'  => $ar_atividade[$nr_conta], 
						'nr_prioridade' => $ar_prioridade[$nr_conta], 
						'cd_usuario'    => $this->session->userdata('codigo')
					);
	            }

				$nr_conta++;
			}

			$args['ar_prioridade_nova'] = $ar_prioridade_nova;

			$this->atividade_prioridade_model->salvar($args);	

			$this->enviar_email($ar_prioridade_nova);

			redirect('atividade/atividade_prioridade', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function enviar_email($ar_atividade)
    {
    	$this->load->model(array(
    		'gestao/plano_acao_model',
    		'projetos/eventos_email_model'
		));

       	$cd_evento = 249;

        $email = $this->eventos_email_model->carrega($cd_evento);

        foreach ($ar_atividade as $key => $item) 
        {	 
        	$row = $this->atividade_prioridade_model->carrega($item['cd_atividade']);

            $para = $row['atendente'].'@eletroceee.com.br;'.$row['solicitante'].'@eletroceee.com.br;'.$row['usuario'].'@eletroceee.com.br';

            $tags = array('[NUMERO_ATIVIDADE]', '[SOLICITANTE]', '[ATENDENTE]', '[STATUS]', '[NR_PRIORIDADE]', '[LINK]');

            $subs = array(
                $row['numero'], 
                $row['ds_solicitante'],
                $row['ds_atendente'],
                $row['ds_status'],
                $row['nr_prioridade'],
                site_url('atividade/atividade_solicitacao/index/'.$row['area_solicitante'].'/'.$row['numero'])
            );
       
            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array( 
                'de'      => 'Atividade - Prioridade',
                'assunto' => str_replace('[NUMERO_ATIVIDADE]', $row['numero'], $email['assunto']),
                'para'    => $para,
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
		}

        redirect('atividade/atividade_prioridade', 'refresh');
    }
}
?>