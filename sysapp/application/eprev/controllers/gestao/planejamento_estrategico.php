<?php
class Planejamento_estrategico extends Controller
{
	function __construct()
    {
        parent::Controller();

		CheckLogin();
    }

    public function intranet()
    {
        $this->load->model('gestao/planejamento_estrategico_model');

        $cd_planejamento_estrategico = $this->input->post('cd_planejamento_estrategico', TRUE);
        
        $nr_aba = $this->input->post('nr_aba', TRUE);

        $data['row'] = $this->planejamento_estrategico_model->carrega(intval($cd_planejamento_estrategico));

        if(intval($nr_aba) == 0)
        {
        	$collection = $this->planejamento_estrategico_model->listar_desdobramentos(intval($cd_planejamento_estrategico));

	        $ano = $data['row']['nr_ano_inicial']; 

			$ano_final = $data['row']['nr_ano_final']; 

			$data['ano'] = array();

			while ($ano <= $ano_final)  
			{
				$data['ano'][] = $ano;
				
				$ano++;
			}

	        foreach ($collection as $key => $item) 
	        {
	        	$collection[$key]['objetivo'] = array();
	        
	        	$objetivo = $this->planejamento_estrategico_model->listar_desdobramentos_objetivo($item['cd_planejamento_estrategico_desdobramento']);

	        	foreach ($objetivo as $key2 => $item2) 
	        	{
	        		$collection[$key]['objetivo'][] = $item2['ds_planejamento_estrategico_objetivo'];
	        	}

	        	$collection[$key]['programa_projeto'] = array();

	        	$programa_projeto = $this->planejamento_estrategico_model->listar_desdobramentos_programa_projeto($item['cd_planejamento_estrategico_desdobramento']);

	        	foreach ($programa_projeto as $key2 => $item2) 
	        	{
	        		$collection[$key]['programa_projeto'][$key2] = array(
	        			'ds_programa_projeto'     => $item2['ds_programa_projeto'],
	        			'cd_gerencia_responsavel' => $item2['cd_gerencia_responsavel']
	        		);

	        		foreach ($data['ano'] as $key3 => $item3) 
	        		{
	        			$cronograma = $this->planejamento_estrategico_model->get_cronograma($item2['cd_programa_projeto'], $item3);

	        			$collection[$key]['programa_projeto'][$key2][$item3] = '';

	        			if(count($cronograma) > 0)
	        			{
	        				if(trim($cronograma['arquivo']) != '')
	        				{
	        					$collection[$key]['programa_projeto'][$key2][$item3] = anchor(base_url().'up/planejamento_estrategico/'.$cronograma['arquivo'], $cronograma['ds_programa_projeto_arquivo'], array('target' => '_blank'));
		        			}
							elseif(intval($cronograma['cd_pendencia_gestao']) > 0)
							{
								$collection[$key]['programa_projeto'][$key2][$item3] = anchor(site_url('gestao/pendencia_gestao/cadastro/'.intval($cronograma['cd_pendencia_gestao'])), $cronograma['ds_programa_projeto_arquivo'], array('target' => '_blank'));
							}
		        			else
		        			{	
		        				$collection[$key]['programa_projeto'][$key2][$item3] = $cronograma['ds_programa_projeto_arquivo'];
		        			}
	        			}

	        			
	        		}
	        	}	
	        }

	        $data['collection'] = $collection;

	        $this->load->view('gestao/planejamento_estrategico/intranet', $data);
        }
        else if(intval($nr_aba) == 1)
        {
        	$this->load->view('gestao/planejamento_estrategico/intranet_acoes', $data);
        }

    }

	public function index()
	{
		$this->load->view('gestao/planejamento_estrategico/index');
	}

	public function listar()
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$args = array(
			'nr_ano_inicial' => $this->input->post('nr_ano_inicial', TRUE),
			'nr_ano_final'   => $this->input->post('nr_ano_final', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->planejamento_estrategico_model->listar($args);

		foreach($data['collection'] as $key => $item)
		{
			$data['collection'][$key]['desdobramento'] = array();

			$data['collection'][$key]['objetivo'] = array();

			foreach ($this->planejamento_estrategico_model->listar_desdobramentos(intval($item['cd_planejamento_estrategico'])) as $desdobramento) 
			{
				$data['collection'][$key]['desdobramento'][] = $desdobramento['text'];
			}

			foreach($this->planejamento_estrategico_model->listar_objetivo(intval($item['cd_planejamento_estrategico'])) as $objetivo)
			{
				$data['collection'][$key]['objetivo'][] = $objetivo['text'];
			}
		}

		$this->load->view('gestao/planejamento_estrategico/index_result', $data);
	}

	public function cadastro($cd_planejamento_estrategico = 0)
	{
		if(intval($cd_planejamento_estrategico) == 0)
        {
            $data['row'] = array(
                'cd_planejamento_estrategico' => intval($cd_planejamento_estrategico),
                'ds_diretriz_fundamental'     => '',     
                'nr_ano_inicial'              => '',
                'nr_ano_final'                => '',
                'arquivo'                     => '',
                'arquivo_nome'                => '',
                'arquivo_plano_execucao' 	  => '',
                'arquivo_plano_execucao_nome' => ''
            );
        }
        else
        {
            $this->load->model('gestao/planejamento_estrategico_model');

            $data['row'] = $this->planejamento_estrategico_model->carrega(intval($cd_planejamento_estrategico));
        }

        $this->load->view('gestao/planejamento_estrategico/cadastro', $data);
	}

	public function salvar()
	{
        $this->load->model('gestao/planejamento_estrategico_model');

        $cd_planejamento_estrategico = $this->input->post('cd_planejamento_estrategico', TRUE);

        $args = array( 
            'ds_diretriz_fundamental'       => $this->input->post('ds_diretriz_fundamental',TRUE),
            'nr_ano_inicial'                => $this->input->post('nr_ano_inicial', TRUE),            
            'nr_ano_final'                  => $this->input->post('nr_ano_final', TRUE),                     
            'arquivo'                       => $this->input->post('arquivo', TRUE),
            'arquivo_nome'                  => $this->input->post('arquivo_nome', TRUE),
            'arquivo_plano_execucao'        => $this->input->post('arquivo_plano_execucao', TRUE),
            'arquivo_plano_execucao_nome'   => $this->input->post('arquivo_plano_execucao_nome', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );

        if(intval($cd_planejamento_estrategico) == 0)
        {
            $cd_planejamento_estrategico = $this->planejamento_estrategico_model->salvar($args);
        }
        else
        {
            $this->planejamento_estrategico_model->atualizar($cd_planejamento_estrategico, $args);
        }

        redirect('gestao/planejamento_estrategico/desdobramento/'.intval($cd_planejamento_estrategico));		
	}

	public function desdobramento($cd_planejamento_estrategico, $cd_planejamento_estrategico_desdobramento = 0)
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$data = array(
			'planejamento'   => $this->planejamento_estrategico_model->carrega($cd_planejamento_estrategico),
			'collection'     => $this->planejamento_estrategico_model->listar_desdobramentos($cd_planejamento_estrategico)
		);

		$data['objetivo'] = $this->planejamento_estrategico_model->listar_objetivo(intval($cd_planejamento_estrategico));

		$data['objetivo_checked'] = array();	
		
		if(intval($cd_planejamento_estrategico_desdobramento) == 0)
		{
			$data['row'] = array(
				'cd_planejamento_estrategico_desdobramento' => intval($cd_planejamento_estrategico_desdobramento),
				'cd_planejamento_estrategico'               => intval($cd_planejamento_estrategico),
				'cd_planejamento_estrategico'				=> '',
				'ds_planejamento_estrategico_desdobramento' => '',		
				'nr_ordem'									=> '',
				'dt_inclusao' 								=> ''
			);

		}
		else
		{
			$data['row'] = $this->planejamento_estrategico_model->carrega_desdobramentos($cd_planejamento_estrategico_desdobramento);
		
			$objetivo_x = $this->planejamento_estrategico_model->listar_desdobramentos_objetivo($cd_planejamento_estrategico_desdobramento); 

			foreach($objetivo_x as $item)
			{
				$data['objetivo_checked'][] = $item['cd_planejamento_estrategico_objetivo'];
			}
		}

		$this->load->view('gestao/planejamento_estrategico/desdobramento', $data);
	}

	public function salvar_desdobramento()
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$cd_planejamento_estrategico = $this->input->post('cd_planejamento_estrategico', TRUE);

		$cd_planejamento_estrategico_desdobramento = $this->input->post('cd_planejamento_estrategico_desdobramento', TRUE);

		$cd_planejamento_estrategico_desobramento_objetivo = $this->input->post('cd_planejamento_estrategico_desobramento_objetivo', TRUE);

		$args = array(
			'cd_planejamento_estrategico'				=> intval($cd_planejamento_estrategico),
			'ds_planejamento_estrategico_desdobramento' => $this->input->post('ds_planejamento_estrategico_desdobramento', TRUE),
			'nr_ordem'								    => $this->input->post('nr_ordem', TRUE),
			'cd_usuario' 								=> $this->session->userdata('codigo')
		);

		$args['cd_planejamento_estrategico_objetivo']      = $this->input->post('cd_planejamento_estrategico_objetivo', TRUE);

		$args['objetivo'] = $this->input->post('objetivo', TRUE);

		if(!is_array($args['objetivo']))
		{
			$args['objetivo'] = array();
		}

		if(intval($cd_planejamento_estrategico_desdobramento) == 0)
		{
			$this->planejamento_estrategico_model->salvar_desdobramento($cd_planejamento_estrategico_desdobramento ,$args);
		}
		else
		{
			$this->planejamento_estrategico_model->atualizar_desdobramento($cd_planejamento_estrategico_desdobramento, $args);
		}

		redirect('gestao/planejamento_estrategico/desdobramento/'.intval($cd_planejamento_estrategico), 'refresh');
	}

	public function objetivo($cd_planejamento_estrategico, $cd_planejamento_estrategico_objetivo = 0)
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$data = array(
			'planejamento' => $this->planejamento_estrategico_model->carrega($cd_planejamento_estrategico),
			'collection'   => $this->planejamento_estrategico_model->listar_objetivo($cd_planejamento_estrategico)
		);

		if(intval($cd_planejamento_estrategico_objetivo) == 0)
		{
			$data['row'] = array(
				'cd_planejamento_estrategico_objetivo'  => intval($cd_planejamento_estrategico_objetivo),
				'cd_planejamento_estrategico'           => intval($cd_planejamento_estrategico),
				'cd_planejamento_estrategico'			=> '',
				'ds_planejamento_estrategico_objetivo' 	=> '',		
				'dt_inclusao' 							=> ''
			);
		}
		else
		{
			$data['row'] = $this->planejamento_estrategico_model->carrega_objetivos($cd_planejamento_estrategico_objetivo);
		}

		$this->load->view('gestao/planejamento_estrategico/objetivo', $data);
	}

	public function salvar_objetivo()
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$cd_planejamento_estrategico = $this->input->post('cd_planejamento_estrategico', TRUE);

		$cd_planejamento_estrategico_objetivo = $this->input->post('cd_planejamento_estrategico_objetivo', TRUE);

		$args = array(
			'cd_planejamento_estrategico'			=> intval($cd_planejamento_estrategico),
			'ds_planejamento_estrategico_objetivo' 	=> $this->input->post('ds_planejamento_estrategico_objetivo', TRUE),
			'cd_usuario' 							=> $this->session->userdata('codigo')
		);

		if(intval($cd_planejamento_estrategico_objetivo) == 0)
		{
			$this->planejamento_estrategico_model->salvar_objetivo($args);
		}
		else
		{
			$this->planejamento_estrategico_model->atualizar_objetivo($cd_planejamento_estrategico_objetivo, $args);
		}

		redirect('gestao/planejamento_estrategico/objetivo/'.intval($cd_planejamento_estrategico), 'refresh');
	}

	public function programa_projeto($cd_planejamento_estrategico, $cd_programa_projeto = 0)
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$data = array(
			'planejamento'  => $this->planejamento_estrategico_model->carrega($cd_planejamento_estrategico),
			'gerencia'	    => $this->planejamento_estrategico_model->get_gerencia(),
			'objetivo'    	=> $this->planejamento_estrategico_model->listar_objetivo($cd_planejamento_estrategico),
			'desdobramento' => $this->planejamento_estrategico_model->listar_desdobramentos($cd_planejamento_estrategico)
		);

		if(intval($cd_programa_projeto) == 0)
		{
			$data['row'] = array(
				'cd_programa_projeto' 		  				=> intval($cd_programa_projeto),
				'cd_planejamento_estrategico' 				=> intval($cd_planejamento_estrategico),
				'ds_programa_projeto'		  				=> '',
				'nr_ordem'									=> '',
				'cd_gerencia_responsavel'     				=> '',
				'cd_planejamento_estrategico_desdobramento' => '',
				'cd_planejamento_estrategico_objetivo'      => '',
				'dt_inclusao' 				  				=> ''
			);
		}
		else
		{
			$data['row'] = $this->planejamento_estrategico_model->carrega_programa(intval($cd_programa_projeto));
		}

		$data['collection'] = $this->planejamento_estrategico_model->listar_programa($cd_planejamento_estrategico);

		$this->load->view('gestao/planejamento_estrategico/programa_projeto', $data);		
	}

	public function salvar_projeto()
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$cd_planejamento_estrategico = $this->input->post('cd_planejamento_estrategico', TRUE);

		$cd_programa_projeto = $this->input->post('cd_programa_projeto', TRUE);

		$args = array(
			'cd_planejamento_estrategico'				 => intval($cd_planejamento_estrategico),
			'ds_programa_projeto'	      				 => $this->input->post('ds_programa_projeto', TRUE),
			'cd_gerencia_responsavel'    				 => $this->input->post('cd_gerencia_responsavel', TRUE),
			'cd_planejamento_estrategico_desdobramento'  => $this->input->post('cd_planejamento_estrategico_desdobramento', TRUE),
			'nr_ordem'							         => $this->input->post('nr_ordem', TRUE),
			'cd_usuario' 				 				 => $this->session->userdata('codigo')
		);

		if(intval($cd_programa_projeto) == 0)
		{
			$this->planejamento_estrategico_model->salvar_programa($args);
		}
		else
		{
			$this->planejamento_estrategico_model->atualizar_programa($cd_programa_projeto, $args);
		}

		redirect('gestao/planejamento_estrategico/programa_projeto/'.intval($cd_planejamento_estrategico), 'refresh');
	}

	public function cronograma($cd_programa_projeto, $cd_programa_projeto_arquivo = 0)
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$data = array(
			'collection' => $this->planejamento_estrategico_model->listar_cronograma($cd_programa_projeto),
			'programa'   => $this->planejamento_estrategico_model->carrega_programa($cd_programa_projeto)
		);

		$anos = array();

		foreach ($data['collection'] as $key => $item)
		{	
			$anos[] = $item['nr_ano'];
		}

		if(intval($cd_programa_projeto_arquivo) == 0)
        {
            $data['row'] = array(
                'cd_programa_projeto'         => intval($cd_programa_projeto),   
                'cd_programa_projeto_arquivo' => intval($cd_programa_projeto_arquivo),    
                'ds_programa_projeto_arquivo' => '', 
                'nr_ano'					  => '',
                'cd_pendencia_gestao'	      => '',
                'arquivo'                     => '',
                'arquivo_nome'                => ''
            );
        }
        else
        {        
        	$data['row'] = $this->planejamento_estrategico_model->carrega_cronograma(intval($cd_programa_projeto_arquivo));
		}		

		$ano_inicial = $data['programa']['nr_ano_inicial']; 

		$ano_final   = $data['programa']['nr_ano_final']; 

		$i = $data['programa']['nr_ano_inicial'];

		$data['drop'] = array();

		while ($i <= $ano_final)  
		{
			if(!in_array($i, $anos))
			{
				$data['drop'][] = array('value' => $i, 'text' => $i);
			}

			$i++;
		}

		$this->load->view('gestao/planejamento_estrategico/cronograma', $data);
	}

	public function salvar_cronograma()
	{
		$this->load->model('gestao/planejamento_estrategico_model');

		$cd_programa_projeto = $this->input->post('cd_programa_projeto', TRUE);

		$cd_programa_projeto_arquivo = $this->input->post('cd_programa_projeto_arquivo', TRUE);
		
		$args = array(
			'cd_programa_projeto_arquivo' => intval($cd_programa_projeto_arquivo),
			'cd_programa_projeto'		  => intval($cd_programa_projeto),
			'ds_programa_projeto_arquivo' => "Pendência: ".$this->input->post('cd_pendencia_gestao', TRUE), #$this->input->post('ds_programa_projeto_arquivo', TRUE),
			'nr_ano'                      => $this->input->post('nr_ano', TRUE),
			'cd_pendencia_gestao'         => $this->input->post('cd_pendencia_gestao', TRUE),
			'arquivo'					  => $this->input->post('arquivo', TRUE),
			'arquivo_nome'				  => $this->input->post('arquivo_nome', TRUE),
			'cd_usuario'				  => $this->session->userdata('codigo')
		);

		if($cd_programa_projeto_arquivo == 0)
		{
			$this->planejamento_estrategico_model->salvar_cronograma($args);
		}
		else
		{
			$this->planejamento_estrategico_model->atualizar_cronograma($cd_programa_projeto_arquivo, $args);
		}

		redirect('gestao/planejamento_estrategico/cronograma/'.intval($cd_programa_projeto), 'refresh');
	}
} 