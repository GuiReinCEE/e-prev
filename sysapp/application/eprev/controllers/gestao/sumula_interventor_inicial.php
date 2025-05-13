<?php
class Sumula_interventor_inicial extends Controller {

	function __construct()
    {
        parent::Controller();
        
		CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('SG')))
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
		$this->load->view('gestao/sumula_interventor_inicial/index');
    }

    public function listar()
    {
    	$this->load->model('gestao/sumula_interventor_inicial_model');

		$args = array(
			'nr_sumula_interventor' => $this->input->post('nr_sumula_interventor', TRUE),
			'dt_sumula_ini'         => $this->input->post('dt_sumula_ini', TRUE),
			'dt_sumula_fim'         => $this->input->post('dt_sumula_ini', TRUE),
			'dt_divulgacao_ini'     => $this->input->post('dt_divulgacao_ini', TRUE),
			'dt_divulgacao_fim'     => $this->input->post('dt_divulgacao_fim', TRUE),
		);
			
		manter_filtros($args);

		$data['collection'] = $this->sumula_interventor_inicial_model->listar($args);

		$this->load->view('gestao/sumula_interventor_inicial/index_result', $data);
	}

	public function cadastro($cd_sumula_interventor_inicial = 0)
    {
    	if($this->get_permissao())
        {
	    	$this->load->model('gestao/sumula_interventor_inicial_model');

	    	if(intval($cd_sumula_interventor_inicial) == 0)
	        {
	        	$row = $this->sumula_interventor_inicial_model->get_proximo_numero();

	        	$data['row'] = array(
					'cd_sumula_interventor'  => intval($cd_sumula_interventor_inicial),
					'nr_sumula_interventor'  => (count($row) > 0 ? $row['nr_sumula_interventor'] : 1),
					'dt_sumula_interventor'  => '',
					'dt_divulgacao'          => '',
					'arquivo_pauta'          => '',
					'arquivo_pauta_nome'     => '',
					'arquivo_sumula'         => '',
					'arquivo_sumula_nome'    => ''
	            );
	        }
	        else
	        {
	        	$data['row'] = $this->sumula_interventor_inicial_model->carrega($cd_sumula_interventor_inicial);
	        }

	        $this->load->view('gestao/sumula_interventor_inicial/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function valida_numero_sumula()
    {
    	$this->load->model('gestao/sumula_interventor_inicial_model');

        $data = $this->sumula_interventor_inicial_model->valida_numero_sumula(
        	$this->input->post('cd_sumula_interventor', TRUE),
        	$this->input->post('nr_sumula_interventor', TRUE)
    	);

        echo json_encode($data);
    }

    public function salvar()
    {
		if($this->get_permissao())
        {
        	$this->load->model('gestao/sumula_interventor_inicial_model');

        	$cd_sumula_interventor_inicial = $this->input->post('cd_sumula_interventor_inicial', TRUE);

        	$args = array(
				'nr_sumula_interventor'  => $this->input->post('nr_sumula_interventor', TRUE),
				'dt_sumula_interventor'  => $this->input->post('dt_sumula_interventor', TRUE),
				'dt_divulgacao'          => $this->input->post('dt_divulgacao', TRUE),
				'arquivo_pauta'          => $this->input->post('arquivo_pauta', TRUE),
				'arquivo_pauta_nome'     => $this->input->post('arquivo_pauta_nome', TRUE),
				'arquivo_sumula'         => $this->input->post('arquivo_sumula', TRUE),
				'arquivo_sumula_nome'    => $this->input->post('arquivo_sumula_nome', TRUE),
				'cd_usuario'             => $this->session->userdata('codigo')
            );

            if(intval($cd_sumula_interventor_inicial) == 0)
            {
            	$cd_sumula_interventor_inicial = $this->sumula_interventor_inicial_model->salvar($args);
            }
            else
            {
            	$this->sumula_interventor_inicial_model->atualizar($cd_sumula_interventor_inicial, $args);
            }

            redirect('gestao/sumula_interventor_inicial/cadastro/'.$cd_sumula_interventor_inicial, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
		
	public function abrir_pdf($cd_sumula_interventor_inicial, $tl_tipo = 'P')
	{
		$this->load->model('gestao/sumula_interventor_inicial_model');
	        
        $row = $this->sumula_interventor_inicial_model->carrega($cd_sumula_interventor_inicial);
		
		if(trim($tl_tipo) == 'P')
		{
			$ob_arq = './up/sumula_interventor_inicial/'.$row['arquivo_pauta'];
			$ds_arq = $row['arquivo_pauta_nome'];
		}
		else
		{
			$ob_arq = './up/sumula_interventor_inicial/'.$row['arquivo_sumula'];
			$ds_arq = $row['arquivo_sumula_nome'];
		}

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.$ds_arq.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($ob_arq));
		header('Accept-Ranges: bytes');	

		readfile($ob_arq);
	}

	public function publicar()
    {
    	$this->load->model('gestao/sumula_interventor_inicial_model');

        $this->sumula_interventor_inicial_model->publicar(
        	$this->input->post('cd_sumula_interventor_inicial', TRUE),
        	$this->session->userdata('codigo'),
        	$this->input->post('dt_publicacao_libera', TRUE)
    	);
    }	

    public function enviar_fundacao($cd_sumula_interventor_inicial)
    {
		if($this->get_permissao())
        {
        	$this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/sumula_interventor_inicial_model'
            ));
        	
        	$cd_evento = 267;
            
            $email = $this->eventos_email_model->carrega($cd_evento);

            $sumula = $this->sumula_interventor_inicial_model->carrega($cd_sumula_interventor_inicial);

            $tags = array('[NR_SUMULA]', '[DATA]', '[LINK]');

            $subs = array(
                $sumula['nr_sumula_interventor'], 
                $sumula['dt_sumula_interventor'], 
                site_url('gestao/sumula_interventor_inicial')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');
            
            $args = array(
                'de'      => 'Súmulas - Pauta de reunião',
                'assunto' => str_replace('[NR_SUMULA]', $sumula['nr_sumula_interventor_inicial'], $email['assunto']),
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );
        	
        	$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('gestao/sumula_interventor_inicial/cadastro/'.$cd_sumula_interventor_inicial, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}