<?php
class Regulamento_alteracao extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    var $caminho = 'up/regulamento_alteracao/';

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
            $this->load->view('planos/regulamento_alteracao/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $args = array();

        manter_filtros($args);

        $data['collection'] = array();

        $regulamento_tipo = $this->regulamento_alteracao_model->lista_regulamento_tipo($args);   

        foreach ($regulamento_tipo as $item) 
        {
            $row = $this->regulamento_alteracao_model->listar($item['cd_regulamento_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
        }

        $this->load->view('planos/regulamento_alteracao/index_result', $data);
	}

	public function cadastro($cd_regulamento_alteracao)
    {
        if($this->get_permissao())
        {
        	$this->load->model('gestao/regulamento_alteracao_model');
            
            $data['row'] = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao); 

        	$this->load->view('planos/regulamento_alteracao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario = 0)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	    	$data = array(
	            'regulamento_alteracao' => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	            'collection'            => $this->regulamento_alteracao_model->listar_glossario($cd_regulamento_alteracao)
	        ); 

	        if(intval($cd_regulamento_alteracao_glossario) == 0)
	        {
	            $row = $this->regulamento_alteracao_model->get_next_ordem_glossario($cd_regulamento_alteracao);

	            $data['row'] = array(
	                'cd_regulamento_alteracao_glossario' => '',
	                'nr_ordem'                           => (isset($row['nr_ordem']) ? $row['nr_ordem'] : 1),
	                'ds_regulamento_alteracao_glossario' => ''
	            );
	        }
	        else
	        {
	            $data['row'] = $this->regulamento_alteracao_model->carrega_glossario(
	                $cd_regulamento_alteracao, 
	                $cd_regulamento_alteracao_glossario
	            );
	        }

	        $this->load->view('planos/regulamento_alteracao/glossario', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_glossario()
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $cd_regulamento_alteracao           = $this->input->post('cd_regulamento_alteracao', TRUE);
	        $cd_regulamento_alteracao_glossario = $this->input->post('cd_regulamento_alteracao_glossario', TRUE);

	        $args = array(
	            'cd_regulamento_alteracao_glossario_referencia' => '',
	            'nr_ordem'                                      => $this->input->post('nr_ordem', TRUE), 
	            'ds_regulamento_alteracao_glossario'            => $this->input->post('ds_regulamento_alteracao_glossario', TRUE),
	            'cd_usuario'                                    => $this->session->userdata('codigo')
	        );

	        if(intval($cd_regulamento_alteracao_glossario) == 0)
	        {
	            $cd_regulamento_alteracao_glossario = $this->regulamento_alteracao_model->salvar_glossario($cd_regulamento_alteracao, $args);

	            $fl_renumeracao = $this->input->post('fl_renumeracao', TRUE);

	            if(trim($fl_renumeracao) != '')
	            {
	                $this->regulamento_alteracao_model->atualizar_renumeracao_glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario, $args);
	            }
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->atualizar_glossario($cd_regulamento_alteracao_glossario, $args);
	        }

	        redirect('planos/regulamento_alteracao/glossario/'.$cd_regulamento_alteracao, 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function remover_glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario, $nr_ordem)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->remover_glossario($cd_regulamento_alteracao_glossario, $this->session->userdata('codigo'));

	        $args = array(
	            'nr_ordem'                                => $nr_ordem,
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atualizar_renumeracao_glossario($cd_regulamento_alteracao, $cd_regulamento_alteracao_glossario, $args, '-');

	        redirect('planos/regulamento_alteracao/glossario/'.$cd_regulamento_alteracao, 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function verifica_ordem_glossario()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $fl_verifica = $this->input->post('fl_verifica', TRUE);

        $row = $this->regulamento_alteracao_model->verifica_ordem_glossario(
            $this->input->post('cd_regulamento_alteracao', TRUE), 
            $this->input->post('nr_ordem', TRUE),
            (trim($fl_verifica) == 'R') ? '=' : '>'
        );

        echo json_encode($row);
    }
    
	public function salvar()
	{
        if($this->get_permissao())
        {
    		$this->load->model('gestao/regulamento_alteracao_model');

    		$cd_regulamento_alteracao = $this->input->post('cd_regulamento_alteracao', TRUE);

        	$args = array(
                'dt_envio_previc'     => $this->input->post('dt_envio_previc', TRUE),
                'ds_aprovacao_previc' => $this->input->post('ds_aprovacao_previc', TRUE),
                'dt_aprovacao_previc' => $this->input->post('dt_aprovacao_previc', TRUE),
                'arquivo'             => $this->input->post('arquivo', TRUE),
                'arquivo_nome'        => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

        	$this->regulamento_alteracao_model->atualizar(intval($cd_regulamento_alteracao), $args);

            $this->pdf_regulamento($cd_regulamento_alteracao);

        	redirect('planos/regulamento_alteracao/cadastro/'.intval($cd_regulamento_alteracao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_regualamento_alteracao($cd_regulamento_alteracao_referencia)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        ### CADASTRO DO REGULAMENTO ###
	        
	        $row = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao_referencia);

	        $args = array( 
	            'cd_regulamento_alteracao_referencia' => $cd_regulamento_alteracao_referencia,
	            'cd_regulamento_tipo'                 => $row['cd_regulamento_tipo'],
	            'ds_aprovacao_previc'                 => '',
	            'dt_aprovacao_previc'                 => '',
	            'cd_usuario'                          => $this->session->userdata('codigo')
	        );    

	        $cd_regulamento_alteracao = $this->regulamento_alteracao_model->salvar($args);

	        ### CADASTRO DO REGULAMENTO ###

	        ### CADASTRO DA ESTRUTURA ###

	        $estrutura = array();

	        $this->monta_estrutura($cd_regulamento_alteracao_referencia, $estrutura);

	        foreach ($estrutura as $key => $item) 
	        {
	            $args = array(
	                'cd_regulamento_alteracao'                      => $cd_regulamento_alteracao,
	                'nr_ordem'                                      => $item['nr_ordem'],
	                'cd_regulamento_alteracao_estrutura_tipo'       => $item['cd_regulamento_alteracao_estrutura_tipo'],
	                'ds_regulamento_alteracao_estrutura'            => $item['ds_regulamento_alteracao_estrutura'],
	                'cd_regulamento_alteracao_estrutura_pai'        => $item['cd_regulamento_alteracao_estrutura_pai'],
	                'cd_regulamento_alteracao_estrutura_referencia' => $item['cd_regulamento_alteracao_estrutura'],
	                'cd_usuario'                                    => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_estrutura($args);
	        }
	        
	        $this->regulamento_alteracao_model->atualizar_estrutura_pai($cd_regulamento_alteracao);

	        ### CADASTRO DA ESTRUTURA ###

	        ### CADASTRO DOS ARTIGOS ###

	        $artigo = $this->regulamento_alteracao_model->get_estrutura_artigo($cd_regulamento_alteracao_referencia);

	        foreach ($artigo as $key => $item) 
	        {
	            $item['ds_regulamento_alteracao_unidade_basica'] = $this->remover_tags($item['ds_regulamento_alteracao_unidade_basica']);

	            $args = array(
	                'cd_regulamento_alteracao'                           => $cd_regulamento_alteracao,
	                'nr_ordem'                                           => $item['nr_ordem'],
	                'cd_regulamento_alteracao_estrutura_tipo'            => $item['cd_regulamento_alteracao_estrutura_tipo'],
	                'ds_regulamento_alteracao_unidade_basica'            => $item['ds_regulamento_alteracao_unidade_basica'],
	                'cd_regulamento_alteracao_estrutura'                 => $item['cd_regulamento_alteracao_estrutura'],
	                'cd_regulamento_alteracao_unidade_basica_pai'        => $item['cd_regulamento_alteracao_unidade_basica_pai'],
	                'cd_regulamento_alteracao_unidade_basica_referencia' => $item['cd_regulamento_alteracao_unidade_basica'],
	                'cd_usuario'                                         => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_estrutura_unidade_basica($args);

	            ### CADASTRO DAS UNIDADES BÁSICAS ###
	            $unidade_basica = array();

	            $this->monta_unidade_basica($cd_regulamento_alteracao_referencia, $item['cd_regulamento_alteracao_unidade_basica'], $unidade_basica, 0, 0);

	            foreach ($unidade_basica as $key2 => $item2) 
	            {
	                $item2['ds_regulamento_alteracao_unidade_basica'] = $this->remover_tags($item2['ds_regulamento_alteracao_unidade_basica']);

	                $args = array(
	                    'cd_regulamento_alteracao'                           => $cd_regulamento_alteracao,
	                    'nr_ordem'                                           => $item2['nr_ordem'],
	                    'cd_regulamento_alteracao_estrutura_tipo'            => $item2['cd_regulamento_alteracao_estrutura_tipo'],
	                    'ds_regulamento_alteracao_unidade_basica'            => $item2['ds_regulamento_alteracao_unidade_basica'],
	                    'cd_regulamento_alteracao_estrutura'                 => $item2['cd_regulamento_alteracao_estrutura'],
	                    'cd_regulamento_alteracao_unidade_basica_pai'        => $item2['cd_regulamento_alteracao_unidade_basica_pai'],
	                    'cd_regulamento_alteracao_unidade_basica_referencia' => $item2['cd_regulamento_alteracao_unidade_basica'],
	                    'cd_usuario'                                         => $this->session->userdata('codigo')
	                );

	                $this->regulamento_alteracao_model->salvar_estrutura_unidade_basica($args);
	            }

	            ### CADASTRO DAS UNIDADES BÁSICAS ###
	        }

	        $this->regulamento_alteracao_model->atualizar_unidade_basica_estrutura($cd_regulamento_alteracao);

	        $this->regulamento_alteracao_model->atualizar_unidade_basica_pai($cd_regulamento_alteracao);

	        ### CADASTRO DOS ARTIGOS ###

	        ### CADASTRO DAS REFERÊNCIAS ###

	        $ref = $this->regulamento_alteracao_model->carrega_referencia($cd_regulamento_alteracao_referencia);

	        foreach ($ref as $key => $item) 
	        {
	            $args = array(
	                'cd_regulamento_alteracao'                               => $cd_regulamento_alteracao, 
	                'cd_regulamento_alteracao_unidade_basica_ref_referencia' => $item['cd_regulamento_alteracao_unidade_basica_ref'], 
	                'cd_regulamento_alteracao_unidade_basica'                => $item['cd_regulamento_alteracao_unidade_basica'], 
	                'cd_regulamento_alteracao_unidade_basica_referenciado'   => $item['cd_regulamento_alteracao_unidade_basica_referenciado'],
	                'cd_usuario'                                             => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_estrutura_unidade_basica_referencia($args);
	        }

	        $this->regulamento_alteracao_model->atualizar_unidade_basica_ref($cd_regulamento_alteracao);

	        ### CADASTRO DAS REFERÊNCIAS ###

	        ### CADASTRO DAS REFERÊNCIAS DA ESTRUTURA ###

	        $ref_estrutura = $this->regulamento_alteracao_model->carrega_referencia_estrutura($cd_regulamento_alteracao_referencia);

	        foreach ($ref_estrutura as $key => $item) 
	        {
	            $args = array(
	                'cd_regulamento_alteracao'                                        => $cd_regulamento_alteracao, 
	                'cd_regulamento_alteracao_unidade_basica_estrutura_ref_referenci' => $item['cd_regulamento_alteracao_unidade_basica_estrutura_ref'], 
	                'cd_regulamento_alteracao_unidade_basica'                         => $item['cd_regulamento_alteracao_unidade_basica'], 
	                'cd_regulamento_alteracao_estrutura_referenciado'                 => $item['cd_regulamento_alteracao_estrutura_referenciado'],
	                'cd_usuario'                                                      => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_referencia_estrutura($args);
	        }

	        $this->regulamento_alteracao_model->atualizar_estrutura_ref($cd_regulamento_alteracao);

	        ### CADASTRO DAS REFERÊNCIAS DA ESTRUTURA###

	        ### CADASTRO DA REVISÃO ###

	        $revisao = $this->regulamento_alteracao_model->lista_revisao(intval($row['cd_regulamento_tipo']));

	        foreach ($revisao as $key => $item) 
	        {
	            $args = array(
	                'cd_regulamento_revisao'               => $item['cd_regulamento_revisao'],
	                'ds_regulamento_alteracao_revisao'     => $item['ds_regulamento_revisao'],
	                'ds_descricao'                         => $item['ds_descricao'],
	                'nr_ordem'                             => $item['nr_ordem'],
	                'cd_etapa_automatica'                  => $item['cd_etapa_automatica'],
	                'cd_usuario'                           => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_alteracao_revisao($cd_regulamento_alteracao, $args);
	        }

	        $this->regulamento_alteracao_model->atualizar_revisao_ref($cd_regulamento_alteracao);

	        ### CADASTRO DA REVISÃO ###

	        ### CADASTRO GLOSSARIO###

	        $glossario = $this->regulamento_alteracao_model->listar_glossario($cd_regulamento_alteracao_referencia);

	        foreach ($glossario as $key => $item)
	        {
	            $item['ds_regulamento_alteracao_glossario'] = $this->remover_tags($item['ds_regulamento_alteracao_glossario']);

	            $args = array(
	                'cd_regulamento_alteracao_glossario_referencia' => $item['cd_regulamento_alteracao_glossario'],
	                'ds_regulamento_alteracao_glossario'            => $item['ds_regulamento_alteracao_glossario'],
	                'nr_ordem'                                      => $item['nr_ordem'],
	                'cd_usuario'                                    => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_glossario($cd_regulamento_alteracao, $args);
	        }

	        ### CADASTRO GLOSSARIO###

	        redirect('planos/regulamento_alteracao/cadastro/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

	private function monta_estrutura($cd_regulamento_alteracao, &$estrutura = array(), $cd_regulamento_alteracao_estrutura = 0, $fl_subsecao = TRUE, $cd_regulamento_alteracao_estrutura_pai = 0, $ds_ordem = '', $nr_nivel = 0, $fl_removido = 'N')
	{
		$collection = $this->regulamento_alteracao_model->get_estrutura(
			$cd_regulamento_alteracao, 
			$cd_regulamento_alteracao_estrutura,
			$cd_regulamento_alteracao_estrutura_pai,
			($fl_subsecao ? array(1, 2, 3) : array(1, 2)),
            $fl_removido
		);

		$nr_nivel ++;

		$i = count($estrutura);

		foreach ($collection as $key => $item) 
		{
			$item['ds_ordem'] = (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'];

			$item['nr_nivel'] = $nr_nivel;

			$item['text'] = str_repeat('&nbsp', ($nr_nivel-1)*4).$item['text'];

			$estrutura[$i] = $item;
	
			$i++;

			$i = $this->monta_estrutura(
				$cd_regulamento_alteracao,
				$estrutura,
				$cd_regulamento_alteracao_estrutura,
				$fl_subsecao,
				$item['cd_regulamento_alteracao_estrutura'],
				(trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'],
				$nr_nivel,
                $fl_removido
			);
		}

		return $i;
	}

	public function estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura = 0)
    {
    	if($this->get_permissao())
        {
	    	$this->load->model('gestao/regulamento_alteracao_model');

	    	$collection    = array();
	    	$estrutura_pai = array();

	    	$this->monta_estrutura($cd_regulamento_alteracao, $collection);
	    	$this->monta_estrutura($cd_regulamento_alteracao, $estrutura_pai, $cd_regulamento_alteracao_estrutura, FALSE);

	    	$data = array(
	    		'regulamento_alteracao' => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	    		'estrutura_tipo'        => $this->regulamento_alteracao_model->get_estrutura_tipo('E'),
	    		'collection'            => $collection,
	    		'estrutura_pai'         => $estrutura_pai
	    	); 

	    	if(intval($cd_regulamento_alteracao_estrutura) == 0)
	    	{
	    		$nr_ordem = '';

	    		$row = $this->regulamento_alteracao_model->get_next_ordem($cd_regulamento_alteracao);

				if(isset($row['nr_ordem']))
				{
					$nr_ordem = intval($row['nr_ordem']);
				}

	    		$data['row'] = array(
	    			'cd_regulamento_alteracao_estrutura'      => $cd_regulamento_alteracao_estrutura,  
	    			'nr_ordem'                                => $nr_ordem,
	    			'cd_regulamento_alteracao_estrutura_tipo' => 1,
	    			'ds_regulamento_alteracao_estrutura'      => '',
	    			'cd_regulamento_alteracao_estrutura_pai'  => ''
	    		);
	    	}
	    	else
	    	{
	            $data['row'] = $this->regulamento_alteracao_model->carrega_estrutura($cd_regulamento_alteracao_estrutura);
	    	}

	    	$this->load->view('planos/regulamento_alteracao/estrutura', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
	}

	public function set_estrutura_pai()
    {
    	$this->load->model('gestao/regulamento_alteracao_model');

    	$cd_regulamento_alteracao               = $this->input->post('cd_regulamento_alteracao', TRUE);
    	$cd_regulamento_alteracao_estrutura_pai = $this->input->post('cd_regulamento_alteracao_estrutura_pai', TRUE);

    	$data = array(
    		'nr_ordem'                                => 1,
    		'cd_regulamento_alteracao_estrutura_tipo' => 1
    	);

    	$data['nr_ordem'] = 1;

    	$row = $this->regulamento_alteracao_model->get_next_ordem($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura_pai);

		if(isset($row['nr_ordem']))
		{
			$data['nr_ordem'] = intval($row['nr_ordem']);
		}

		$row = $this->regulamento_alteracao_model->carrega_estrutura($cd_regulamento_alteracao_estrutura_pai);

		if(count($row) > 0)
		{
			$data['cd_regulamento_alteracao_estrutura_tipo'] = intval($row['cd_regulamento_alteracao_estrutura_tipo_filho']);
		}

		echo json_encode($data);
    }

    public function verifica_ordem_estrutura()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $fl_verifica = $this->input->post('fl_verifica', TRUE);

        $row = $this->regulamento_alteracao_model->verifica_ordem_estrutura(
            $this->input->post('cd_regulamento_alteracao', TRUE), 
            $this->input->post('cd_regulamento_alteracao_estrutura_tipo', TRUE),
            $this->input->post('nr_ordem', TRUE), 
            $this->input->post('cd_regulamento_alteracao_estrutura_pai', TRUE),
            (trim($fl_verifica) == 'R') ? '=' : '>'
        );

        echo json_encode($row);
    }

    public function remover_estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $row = $this->regulamento_alteracao_model->carrega_estrutura($cd_regulamento_alteracao_estrutura);

	        $collection[] = $row;

	        $this->monta_estrutura($cd_regulamento_alteracao, $collection, 0, TRUE, $row['cd_regulamento_alteracao_estrutura'], $row['nr_ordem'], 1);

	        foreach ($collection as $key => $item) 
	        {
	            $this->regulamento_alteracao_model->remover_estrutura(
	                $item['cd_regulamento_alteracao_estrutura'],
	                $this->session->userdata('codigo')
	            );
	            
	            $artigo = $this->regulamento_alteracao_model->get_estrutura_artigo(
	                $cd_regulamento_alteracao, 
	                $item['cd_regulamento_alteracao_estrutura'],
	                'S'
	            );

	            foreach($artigo as $key2 => $item2)
	            {
	                $this->regulamento_alteracao_model->remover_unidade_basica(
	                    $item2['cd_regulamento_alteracao_unidade_basica'],
	                    $this->session->userdata('codigo')
	                );

	                $args = array(
	                    'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	                    'cd_regulamento_alteracao_estrutura_tipo' => 4,
	                    'nr_ordem'                                => $item2['nr_ordem'],
	                    'cd_usuario'                              => $this->session->userdata('codigo')
	                );

	                $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao(
	                    $item2['cd_regulamento_alteracao_unidade_basica'],
	                    $args,
	                    '-'
	                );
	            }
	        }
	        
	        $args = array(
	            'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_estrutura_tipo' => $row['cd_regulamento_alteracao_estrutura_tipo'],
	            'nr_ordem'                                => $row['nr_ordem'],
	            'cd_regulamento_alteracao_estrutura_pai'  => $row['cd_regulamento_alteracao_estrutura_pai'],
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atuliza_estrutura_renumeracao($cd_regulamento_alteracao_estrutura, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

public function excluir_estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_estrutura)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $row = $this->regulamento_alteracao_model->carrega_estrutura($cd_regulamento_alteracao_estrutura);

	        $collection[] = $row;

	        $this->monta_estrutura($cd_regulamento_alteracao, $collection, 0, TRUE, $row['cd_regulamento_alteracao_estrutura'], $row['nr_ordem'], 1);

	        foreach ($collection as $key => $item) 
	        {
	            $this->regulamento_alteracao_model->excluir_estrutura(
	                $item['cd_regulamento_alteracao_estrutura'],
	                $this->session->userdata('codigo')
	            );
	            
	            $artigo = $this->regulamento_alteracao_model->get_estrutura_artigo(
	                $cd_regulamento_alteracao, 
	                $item['cd_regulamento_alteracao_estrutura'],
	                'S'
	            );

	            foreach($artigo as $key2 => $item2)
	            {
	                $this->regulamento_alteracao_model->excluir_unidade_basica(
	                    $item2['cd_regulamento_alteracao_unidade_basica'],
	                    $this->session->userdata('codigo')
	                );

	                $args = array(
	                    'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	                    'cd_regulamento_alteracao_estrutura_tipo' => 4,
	                    'nr_ordem'                                => $item2['nr_ordem'],
	                    'cd_usuario'                              => $this->session->userdata('codigo')
	                );

	                $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao(
	                    $item2['cd_regulamento_alteracao_unidade_basica'],
	                    $args,
	                    '-'
	                );
	            }
	        }
	        
	        $args = array(
	            'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_estrutura_tipo' => $row['cd_regulamento_alteracao_estrutura_tipo'],
	            'nr_ordem'                                => $row['nr_ordem'],
	            'cd_regulamento_alteracao_estrutura_pai'  => $row['cd_regulamento_alteracao_estrutura_pai'],
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atuliza_estrutura_renumeracao($cd_regulamento_alteracao_estrutura, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

	public function salvar_estrutura()
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$cd_regulamento_alteracao           = $this->input->post('cd_regulamento_alteracao', TRUE);
			$cd_regulamento_alteracao_estrutura = $this->input->post('cd_regulamento_alteracao_estrutura', TRUE);

	        $args = array( 
	        	'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	            'nr_ordem'                                => $this->input->post('nr_ordem', TRUE),
	            'cd_regulamento_alteracao_estrutura_tipo' => $this->input->post('cd_regulamento_alteracao_estrutura_tipo', TRUE),
	            'ds_regulamento_alteracao_estrutura'      => $this->input->post('ds_regulamento_alteracao_estrutura', TRUE),
	            'cd_regulamento_alteracao_estrutura_pai'  => $this->input->post('cd_regulamento_alteracao_estrutura_pai', TRUE),
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        if(intval($cd_regulamento_alteracao_estrutura) == 0)
	        {
	            $cd_regulamento_alteracao_estrutura = $this->regulamento_alteracao_model->salvar_estrutura($args);

	            $fl_renumeracao =  $this->input->post('fl_renumeracao', TRUE);

	            if(trim($fl_renumeracao) == 'S')
	            {
	                $this->regulamento_alteracao_model->atuliza_estrutura_renumeracao($cd_regulamento_alteracao_estrutura, $args);
	            }
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->atualizar_estrutura($cd_regulamento_alteracao_estrutura, $args);
	        }

	        redirect('planos/regulamento_alteracao/estrutura/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
	}

	public function estrutura_artigo($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica = 0, $cd_regulamento_alteracao_estrutura = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$estrutura = array();

	    	$this->monta_estrutura($cd_regulamento_alteracao, $estrutura);

	    	$data = array(
	    		'regulamento_alteracao' => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	    		'estrutura'             => $estrutura
	    	); 

	        $collection = $this->regulamento_alteracao_model->get_estrutura_artigo($cd_regulamento_alteracao);

	        foreach ($collection as $key => $item) 
	        {
	            $collection[$key]['qt_unidade_basica'] = 0;

	            $unidade_basica = array();

	            $this->monta_unidade_basica($cd_regulamento_alteracao, $item['cd_regulamento_alteracao_unidade_basica'], $unidade_basica, 0, 0);

	            $collection[$key]['qt_unidade_basica'] = count($unidade_basica);
	        }

	        $data['collection'] = $collection;

	    	if(intval($cd_regulamento_alteracao_unidade_basica) == 0)
	    	{
	    		$nr_ordem = 1;

	    		$row = $this->regulamento_alteracao_model->get_next_ordem_artigo($cd_regulamento_alteracao);

	    		if(isset($row['nr_ordem']))
				{
					$nr_ordem = intval($row['nr_ordem']);
				}

	    		$data['row'] = array(
	    			'cd_regulamento_alteracao_unidade_basica' => $cd_regulamento_alteracao_unidade_basica,  
	    			'nr_ordem'                                => $nr_ordem,
	    			'ds_regulamento_alteracao_unidade_basica' => '',
	    			'cd_regulamento_alteracao_estrutura'      => $cd_regulamento_alteracao_estrutura
	    		);
	    	}
	    	else
	    	{
	            $data['row'] = $this->regulamento_alteracao_model->carrega_estrutura_artigo($cd_regulamento_alteracao_unidade_basica);
	        }

	    	$this->load->view('planos/regulamento_alteracao/estrutura_artigo', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }
    
    public function verifica_ordem_unidade_basica()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $fl_verifica = $this->input->post('fl_verifica', TRUE);

        $row = $this->regulamento_alteracao_model->verifica_ordem_unidade_basica(
            $this->input->post('cd_regulamento_alteracao', TRUE), 
            4,
            $this->input->post('nr_ordem', TRUE),
            (trim($fl_verifica) == 'R') ? '=' : '>'
        );

        echo json_encode($row);
    }

    public function remover_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $nr_ordem)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->remover_unidade_basica(
	            $cd_regulamento_alteracao_unidade_basica,
	            $this->session->userdata('codigo')
	        );
	        
	        $args = array(
	            'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_estrutura_tipo' => 4,
	            'nr_ordem'                                => $nr_ordem,
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao($cd_regulamento_alteracao_unidade_basica, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura_artigo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function excluir_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $nr_ordem)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->excluir_unidade_basica(
	            $cd_regulamento_alteracao_unidade_basica,
	            $this->session->userdata('codigo')
	        );
	        
	        $args = array(
	            'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_estrutura_tipo' => 4,
	            'nr_ordem'                                => $nr_ordem,
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao($cd_regulamento_alteracao_unidade_basica, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura_artigo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

	public function salvar_estrutura_artigo()
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$cd_regulamento_alteracao                = $this->input->post('cd_regulamento_alteracao', TRUE);
			$cd_regulamento_alteracao_unidade_basica = $this->input->post('cd_regulamento_alteracao_unidade_basica', TRUE);
			$cd_regulamento_alteracao_estrutura      = $this->input->post('cd_regulamento_alteracao_estrutura', TRUE);

			$args = array( 
	        	'cd_regulamento_alteracao'                    => $cd_regulamento_alteracao,
	            'nr_ordem'                                    => $this->input->post('nr_ordem',TRUE),
	            'cd_regulamento_alteracao_estrutura_tipo'     => 4,
	            'cd_regulamento_alteracao_unidade_basica_pai' => '',
	            'ds_regulamento_alteracao_unidade_basica'     => $this->input->post('ds_regulamento_alteracao_unidade_basica', TRUE),
	            'cd_regulamento_alteracao_estrutura'          => $cd_regulamento_alteracao_estrutura,
	            'cd_usuario'                                  => $this->session->userdata('codigo')
	        );       

			if(intval($cd_regulamento_alteracao_unidade_basica) == 0)
	        {
	            $cd_regulamento_alteracao_unidade_basica = $this->regulamento_alteracao_model->salvar_estrutura_unidade_basica($args);

	            $fl_renumeracao =  $this->input->post('fl_renumeracao', TRUE);

	            if(trim($fl_renumeracao) == 'S')
	            {
	                $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao($cd_regulamento_alteracao_unidade_basica, $args);
	            }
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->atualizar_estrutura_unidade_basica($cd_regulamento_alteracao_unidade_basica, $args);
	        }

	        redirect('planos/regulamento_alteracao/estrutura_artigo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
	}

	private function monta_unidade_basica(
        $cd_regulamento_alteracao, 
        $cd_regulamento_alteracao_unidade_basica_pai, 
        &$unidade_basica = array(), 
        $cd_regulamento_alteracao_unidade_basica = 0,
        $nr_nivel = 1, 
        $ds_ordem = '', 
        $fl_removido = 'N'
    )
	{
		$collection = $this->regulamento_alteracao_model->get_unidade_basica(
			$cd_regulamento_alteracao,
			$cd_regulamento_alteracao_unidade_basica_pai,
			$cd_regulamento_alteracao_unidade_basica,
            $fl_removido
		);

		$nr_nivel ++;

		$i = count($unidade_basica);

		foreach ($collection as $key => $item) 
		{
			$item['ds_ordem'] = (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'];

			$item['text'] = str_repeat('&nbsp', ($nr_nivel-1)*4).(strlen($item['text']) > 100 ? substr($item['text'], 0, 100).'...' : $item['text']);

			$item['nr_nivel'] = $nr_nivel;

			$unidade_basica[$i] = $item;

			$i++;

			$i = $this->monta_unidade_basica(
				$cd_regulamento_alteracao,
				$item['cd_regulamento_alteracao_unidade_basica'],
				$unidade_basica,
				$item['cd_regulamento_alteracao_unidade_basica'],
				$nr_nivel,
				(trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'],
                $fl_removido
			);
		}

		return $i;
	}

	private function ajusta_tags($ds_texto)
	{
		$tags = array('<bi>', '</bi>', '<bu>', '</bu>', '<biu>', '</biu>', '<iu>', '</iu>');
    	$subs = array('<b><i>', '</i></b>', '<b><u>', '</u></b>', '<b><i><u>', '</u></i></b>', '<i><u>', '</u></i>');

    	return str_replace($tags, $subs, $ds_texto);
	}

    private function remover_tags($ds_texto)
    {
        $tags = array('<b>', '</b>', '<bi>', '</bi>', '<bu>', '</bu>', '<biu>', '</biu>', '<iu>', '</iu>');

        return str_replace($tags, '', $ds_texto);
    }

	public function estrutura_unidade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_artigo, $cd_regulamento_alteracao_unidade_basica = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

	        $artigo = $this->regulamento_alteracao_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_artigo);

			$unidade_basica[] = array(
				'value' => $cd_regulamento_alteracao_unidade_basica_artigo,
				'text'  => (strlen($artigo['ds_artigo']) > 100 ? substr($artigo['ds_artigo'], 0, 100).'...' : $artigo['ds_artigo'])
			);

			$collection = array();

			$this->monta_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_artigo, $unidade_basica);
			$this->monta_unidade_basica($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_artigo, $collection, 0, 0);

			$data = array(
	    		'regulamento_alteracao' => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	    		'estrutura_tipo'        => $this->regulamento_alteracao_model->get_estrutura_tipo('U'),
	    		'artigo'                => $artigo,
	    		'unidade_basica'        => $unidade_basica,
	    		'collection'            => $collection
	        );

	    	$data['artigo']['ds_artigo'] = $this->ajusta_tags($data['artigo']['ds_artigo']);

	    	if(intval($cd_regulamento_alteracao_unidade_basica) == 0)
	    	{
	    		$nr_ordem = 1;

	    		$row = $this->regulamento_alteracao_model->get_next_ordem_unidade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_artigo);

	    		if(isset($row['nr_ordem']))
				{
					$nr_ordem = intval($row['nr_ordem']);
				}

	    		$data['row'] = array(
	    			'cd_regulamento_alteracao_unidade_basica'     => $cd_regulamento_alteracao_unidade_basica,  
	    			'nr_ordem'                                    => $nr_ordem,
	    			'ds_regulamento_alteracao_unidade_basica'     => '',
	    			'cd_regulamento_alteracao_estrutura'          => $data['artigo']['cd_regulamento_alteracao_estrutura'],
	    			'cd_regulamento_alteracao_unidade_basica_pai' => $cd_regulamento_alteracao_unidade_basica_artigo,
	    			'cd_regulamento_alteracao_estrutura_tipo'     => ''
	    		);
	    	}
	    	else
	    	{
	            $data['row'] = $this->regulamento_alteracao_model->carrega_estrutura_unidade_artigo($cd_regulamento_alteracao_unidade_basica);
	        }

	    	$this->load->view('planos/regulamento_alteracao/estrutura_unidade', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
	}

	public function salvar_estrutura_unidade()
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$cd_regulamento_alteracao                       = $this->input->post('cd_regulamento_alteracao', TRUE);
			$cd_regulamento_alteracao_unidade_basica        = $this->input->post('cd_regulamento_alteracao_unidade_basica', TRUE);
			$cd_regulamento_alteracao_estrutura             = $this->input->post('cd_regulamento_alteracao_estrutura', TRUE);
			$cd_regulamento_alteracao_unidade_basica_artigo = $this->input->post('cd_regulamento_alteracao_unidade_basica_artigo', TRUE);

			$args = array( 
	        	'cd_regulamento_alteracao'                     => $cd_regulamento_alteracao,
	            'nr_ordem'                                     => $this->input->post('nr_ordem',TRUE),
	            'cd_regulamento_alteracao_unidade_basica_pai'  => $this->input->post('cd_regulamento_alteracao_unidade_basica_pai', TRUE),
	            'cd_regulamento_alteracao_estrutura_tipo'      => $this->input->post('cd_regulamento_alteracao_estrutura_tipo', TRUE),
	            'ds_regulamento_alteracao_unidade_basica'      => $this->input->post('ds_regulamento_alteracao_unidade_basica', TRUE),
	            'cd_regulamento_alteracao_estrutura'           => $cd_regulamento_alteracao_estrutura,
	            'cd_usuario'                                   => $this->session->userdata('codigo')
	        );

			if(intval($cd_regulamento_alteracao_unidade_basica) == 0)
	        {
	            $cd_regulamento_alteracao_unidade_basica = $this->regulamento_alteracao_model->salvar_estrutura_unidade_basica($args);

	            $fl_renumeracao =  $this->input->post('fl_renumeracao', TRUE);

	            if(trim($fl_renumeracao) == 'S')
	            {
	                $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao_filho($cd_regulamento_alteracao_unidade_basica, $args);
	            }
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->atualizar_estrutura_unidade_basica($cd_regulamento_alteracao_unidade_basica, $args);
	        }

	        redirect('planos/regulamento_alteracao/estrutura_unidade/'.intval($cd_regulamento_alteracao).'/'.intval($cd_regulamento_alteracao_unidade_basica_artigo), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }
    
    public function verifica_ordem_unidade_basica_filho()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $fl_verifica = $this->input->post('fl_verifica', TRUE);

        $row = $this->regulamento_alteracao_model->verifica_ordem_unidade_basica_filho(
            $this->input->post('cd_regulamento_alteracao', TRUE), 
            $this->input->post('cd_regulamento_alteracao_estrutura_tipo', TRUE), 
            $this->input->post('cd_regulamento_alteracao_unidade_basica_pai', TRUE),
            $this->input->post('nr_ordem', TRUE),
            (trim($fl_verifica) == 'R') ? '=' : '>'
        );

        echo json_encode($row);
    }

    public function remover_unidade_basica_filho($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $nr_ordem, $cd_regulamento_alteracao_unidade_basica_pai, $cd_regulamento_alteracao_unidade_basica_artigo, $cd_regulamento_alteracao_estrutura_tipo)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->remover_unidade_basica(
	                $cd_regulamento_alteracao_unidade_basica,
	                $this->session->userdata('codigo')
	        );
	        
	        $args = array(
	            'cd_regulamento_alteracao'                    => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_unidade_basica_pai' => $cd_regulamento_alteracao_unidade_basica_pai,
	            'cd_regulamento_alteracao_estrutura_tipo'     => $cd_regulamento_alteracao_estrutura_tipo,
	            'nr_ordem'                                    => $nr_ordem,
	            'cd_usuario'                                  => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao_filho($cd_regulamento_alteracao_unidade_basica, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura_unidade/'.intval($cd_regulamento_alteracao).'/'.intval($cd_regulamento_alteracao_unidade_basica_artigo), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function excluir_unidade_basica_filho($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $nr_ordem, $cd_regulamento_alteracao_unidade_basica_pai, $cd_regulamento_alteracao_unidade_basica_artigo, $cd_regulamento_alteracao_estrutura_tipo)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->excluir_unidade_basica(
	                $cd_regulamento_alteracao_unidade_basica,
	                $this->session->userdata('codigo')
	        );
	        
	        $args = array(
	            'cd_regulamento_alteracao'                    => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_unidade_basica_pai' => $cd_regulamento_alteracao_unidade_basica_pai,
	            'cd_regulamento_alteracao_estrutura_tipo'     => $cd_regulamento_alteracao_estrutura_tipo,
	            'nr_ordem'                                    => $nr_ordem,
	            'cd_usuario'                                  => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atualiza_unidade_basica_renumeracao_filho($cd_regulamento_alteracao_unidade_basica, $args, '-');

	        redirect('planos/regulamento_alteracao/estrutura_unidade/'.intval($cd_regulamento_alteracao).'/'.intval($cd_regulamento_alteracao_unidade_basica_artigo), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

	public function set_unidade_basica_pai()
	{
		$this->load->model('gestao/regulamento_alteracao_model');

    	$cd_regulamento_alteracao                    = $this->input->post('cd_regulamento_alteracao', TRUE);
    	$cd_regulamento_alteracao_unidade_basica_pai = $this->input->post('cd_regulamento_alteracao_unidade_basica_pai', TRUE);

    	$data = array(
    		'nr_ordem'                                => 1,
    		'cd_regulamento_alteracao_estrutura_tipo' => 1
    	);

    	$row = $this->regulamento_alteracao_model->get_next_ordem_unidade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica_pai);

		if(isset($row['nr_ordem']))
		{
			$data['nr_ordem'] = intval($row['nr_ordem']);
		}

		$row = $this->regulamento_alteracao_model->get_ultimo_tipo_unidade($cd_regulamento_alteracao_unidade_basica_pai);

		if(count($row) > 0)
		{
			$data['cd_regulamento_alteracao_estrutura_tipo'] = trim($row['cd_regulamento_alteracao_estrutura_tipo']);
		}

		echo json_encode($data);
	}

    public function set_unidade_basica_tipo()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $cd_regulamento_alteracao                    = $this->input->post('cd_regulamento_alteracao', TRUE);
        $cd_regulamento_alteracao_unidade_basica_pai = $this->input->post('cd_regulamento_alteracao_unidade_basica_pai', TRUE);
        $cd_regulamento_alteracao_estrutura_tipo     = $this->input->post('cd_regulamento_alteracao_estrutura_tipo', TRUE);

        $data['nr_ordem'] = 1;

        $row = $this->regulamento_alteracao_model->get_next_ordem_unidade_tipo(
            $cd_regulamento_alteracao, 
            $cd_regulamento_alteracao_unidade_basica_pai,
            $cd_regulamento_alteracao_estrutura_tipo
        );

        if(isset($row['nr_ordem']))
        {
            $data['nr_ordem'] = intval($row['nr_ordem']);
        }
          
        echo json_encode($data); 
    }

    public function referencia($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $cd_regulamento_alteracao_artigo = 0)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $data = array(
	            'cd_regulamento_alteracao_artigo' => $cd_regulamento_alteracao_artigo,
	            'regulamento_alteracao'           => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	            'artigo'                          => $this->regulamento_alteracao_model->carrega_unidade_basica(
	                $cd_regulamento_alteracao_unidade_basica
	            )
	        );

	        $artigo = $this->regulamento_alteracao_model->get_estrutura_artigo($cd_regulamento_alteracao);

	        $collection = array();

	        foreach ($artigo as $key => $item) 
	        {
	            $item['ds_unidade_basica'] = $item['ds_artigo'];
	            $item['ds_ordem']          = $item['nr_ordem'];

	            if(intval($cd_regulamento_alteracao_unidade_basica) != intval($item['cd_regulamento_alteracao_unidade_basica']))
	            {
	                $collection[] = $item;
	            }            

	            $unidade_basica = array();

	            $this->monta_unidade_basica($cd_regulamento_alteracao, $item['cd_regulamento_alteracao_unidade_basica'], $unidade_basica, 0, 0);

	            foreach ($unidade_basica as $key2 => $item2) 
	            {
	                $item2['ds_ordem'] = $item['ds_ordem'].'.'.$item2['ds_ordem'];

	                if(intval($cd_regulamento_alteracao_unidade_basica) != intval($item2['cd_regulamento_alteracao_unidade_basica']))
	                {
	                    $collection[] = $item2;
	                }            

	            }
	        }

	        $data['collection'] = $collection;

	        $referenciado = $this->regulamento_alteracao_model->get_unidade_basica_referenciado($cd_regulamento_alteracao_unidade_basica);

	        $data['referenciado'] = array();

	        foreach ($referenciado as $key => $item) 
	        {
	            $data['referenciado'][] = $item['cd_regulamento_alteracao_unidade_basica_referenciado'];
	        }

	        $this->load->view('planos/regulamento_alteracao/referencia', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function referencia_estrutura($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $cd_regulamento_alteracao_artigo = 0)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $collection = array();

	        $this->monta_estrutura($cd_regulamento_alteracao, $collection);

	        $data = array(
	            'regulamento_alteracao'                   => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	            'artigo'                                  => $this->regulamento_alteracao_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica),
	            'collection'                              => $collection,
	            'cd_regulamento_alteracao_unidade_basica' => $cd_regulamento_alteracao_unidade_basica,
	            'cd_regulamento_alteracao_artigo'         => $cd_regulamento_alteracao_artigo     
	        );

	        $referenciado = $this->regulamento_alteracao_model->get_estrutura_unidade_basica_referenciado(
	            $cd_regulamento_alteracao_unidade_basica
	        );

	        $data['referenciado'] = array();

	        foreach ($referenciado as $key => $item) 
	        {
	            $data['referenciado'][] = $item['cd_regulamento_alteracao_estrutura_referenciado'];
	        }

	        $this->load->view('planos/regulamento_alteracao/referencia_estrutura', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_referencia_estrutura()
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $cd_regulamento_alteracao                             = $this->input->post('cd_regulamento_alteracao', TRUE);
	        $cd_regulamento_alteracao_unidade_basica              = $this->input->post('cd_regulamento_alteracao_unidade_basica', TRUE);
	        $cd_regulamento_alteracao_estrutura_referenciado      = $this->input->post('cd_regulamento_alteracao_estrutura_referenciado', TRUE);
	        $fl_salvar                                            = $this->input->post('fl_salvar', TRUE);

	        if(trim($fl_salvar) == 'S')
	        {
	            $this->regulamento_alteracao_model->salvar_referenciado_estrutura(
	                $cd_regulamento_alteracao,
	                $cd_regulamento_alteracao_unidade_basica,
	                $cd_regulamento_alteracao_estrutura_referenciado,
	                $this->session->userdata('codigo')
	            );
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->exclui_referenciado_estrutura(
	                $cd_regulamento_alteracao_unidade_basica,
	                $cd_regulamento_alteracao_estrutura_referenciado,
	                $this->session->userdata('codigo')
	            );
	        }
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_referencia()
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $cd_regulamento_alteracao                             = $this->input->post('cd_regulamento_alteracao', TRUE);
	        $cd_regulamento_alteracao_unidade_basica              = $this->input->post('cd_regulamento_alteracao_unidade_basica', TRUE);
	        $cd_regulamento_alteracao_unidade_basica_referenciado = $this->input->post('cd_regulamento_alteracao_unidade_basica_referenciado', TRUE);
	        $fl_salvar                                            = $this->input->post('fl_salvar', TRUE);

	        if(trim($fl_salvar) == 'S')
	        {
	            $this->regulamento_alteracao_model->salvar_referenciado(
	                $cd_regulamento_alteracao,
	                $cd_regulamento_alteracao_unidade_basica,
	                $cd_regulamento_alteracao_unidade_basica_referenciado,
	                $this->session->userdata('codigo')
	            );
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->exclui_referenciado(
	                $cd_regulamento_alteracao_unidade_basica,
	                $cd_regulamento_alteracao_unidade_basica_referenciado,
	                $this->session->userdata('codigo')
	            );
	        }
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    private function get_artigo_pai($cd_regulamento_alteracao_unidade_basica_pai)
    {
        $row = $this->regulamento_alteracao_model->carrega_unidade_basica(
            $cd_regulamento_alteracao_unidade_basica_pai
        );

        if(trim($row['fl_artigo']) == 'N')
        {
            return $this->get_artigo_pai($row['cd_regulamento_alteracao_unidade_basica_pai']);
        }
        else
        {
            return $row;
        }
    }

    private function get_estrutura_arvore($cd_regulamento_alteracao_estrutura_pai, &$estrutura = array())
    {
        $row = $this->regulamento_alteracao_model->carrega_estrutura(intval($cd_regulamento_alteracao_estrutura_pai));

        $estrutura[] = $row;

        if(intval($row['cd_regulamento_alteracao_estrutura_pai']) > 0)
        {
            $this->get_estrutura_arvore(intval($row['cd_regulamento_alteracao_estrutura_pai']), $estrutura);
        }
    }

    private function get_unidade_basica_arvore($cd_regulamento_alteracao_unidade_basica_pai, &$unidade_basica = array())
    {
        $row = $this->regulamento_alteracao_model->carrega_unidade_basica(intval($cd_regulamento_alteracao_unidade_basica_pai));

        $row['ds_alteracao_referencia'] = '';

        if(intval($row['cd_regulamento_alteracao_unidade_basica_referencia']) > 0)
        {
            $row_referencia = $this->regulamento_alteracao_model->carrega_unidade_basica(
                $row['cd_regulamento_alteracao_unidade_basica_referencia']
            );

            $row['ds_alteracao_referencia'] = $row_referencia['ds_ordem'];
        }

        $unidade_basica[] = $row;

        if(intval($row['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
        {
            $this->get_unidade_basica_arvore(intval($row['cd_regulamento_alteracao_unidade_basica_pai']), $unidade_basica);
        }
    }

    public function remissao($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $regulamento_alteracao = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

	        $unidade_basica_referencia = $this->regulamento_alteracao_model->get_unidade_basica_ref($cd_regulamento_alteracao);

	        $collection = array();

	        foreach ($unidade_basica_referencia as $key => $item) 
	        {
	            $row = $this->regulamento_alteracao_model->carrega_unidade_basica(
	                $item['cd_regulamento_alteracao_unidade_basica']
	            );

	            if(count($row) > 0)
	            {
	                $collection[$key] = $row;

	                $collection[$key]['cd_ref']                                               = intval($item['cd_ref']);
	                $collection[$key]['fl_tipo']                                              = trim($item['fl_tipo']);
	                $collection[$key]['dt_verificado']                                        = trim($item['dt_verificado']);
	                $collection[$key]['cd_regulamento_alteracao_unidade_basica_referenciado'] = intval($item['cd_regulamento_alteracao_unidade_basica_referenciado']);

	                $collection[$key]['ds_artigo_pai'] = '';

	                if(trim($collection[$key]['fl_artigo']) == 'N')
	                {
	                    $artigo_pai = $this->get_artigo_pai(
	                        $collection[$key]['cd_regulamento_alteracao_unidade_basica_pai']
	                    );

	                    $collection[$key]['nr_ordem_artigo'] = $artigo_pai['nr_ordem'];
	                    $collection[$key]['ds_artigo_pai']   = $artigo_pai['ds_artigo'];

	                    $collection[$key]['cd_regulamento_alteracao_unidade_basica_pai'] = $artigo_pai['cd_regulamento_alteracao_unidade_basica'];
	                }
	                else
	                {
	                    $collection[$key]['nr_ordem_artigo'] = $collection[$key]['nr_ordem'];
	                }

	                $estrutura_ref      = array();
	                $unidade_basica_ref = array();

	                $collection[$key]['estrutura']      = array();
	                $collection[$key]['unidade_basica'] = array();

	                if(intval($item['cd_regulamento_alteracao_unidade_basica_referenciado']) > 0)
	                {
	                    $unidade_basica = $this->regulamento_alteracao_model->carrega_unidade_basica(
	                        $item['cd_regulamento_alteracao_unidade_basica_referenciado']
	                    );

	                    $unidade_basica['ds_alteracao_referencia'] = '';

	                    if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_referencia']) > 0)
	                    {
	                        $unidade_basica_referencia = $this->regulamento_alteracao_model->carrega_unidade_basica(
	                            $unidade_basica['cd_regulamento_alteracao_unidade_basica_referencia']
	                        );

	                        $unidade_basica['ds_alteracao_referencia'] = $unidade_basica_referencia['ds_ordem'];
	                    }

	                    $unidade_basica_ref[] = $unidade_basica;

	                    if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
	                    {
	                        $this->get_unidade_basica_arvore(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_pai']), $unidade_basica_ref);
	                    }

	                    $cd_regulamento_alteracao_estrutura = $unidade_basica['cd_regulamento_alteracao_estrutura'];
	                }
	                else
	                {
	                    $cd_regulamento_alteracao_estrutura = $item['cd_regulamento_alteracao_estrutura_referenciado'];
	                }

	                $estrutura = $this->regulamento_alteracao_model->carrega_estrutura($cd_regulamento_alteracao_estrutura);

	                $estrutura_ref[] = $estrutura;

	                if(intval($estrutura['cd_regulamento_alteracao_estrutura_pai']) > 0)
	                {
	                    $this->get_estrutura_arvore(intval($estrutura['cd_regulamento_alteracao_estrutura_pai']), $estrutura_ref);
	                }

	                krsort($estrutura_ref);

	                $collection[$key]['estrutura'] = $estrutura_ref;

	                krsort($unidade_basica_ref);

	                $collection[$key]['unidade_basica'] = $unidade_basica_ref;
	            }
	        }

	        array_sort_by_column($collection, 'nr_ordem_artigo');

	        $data = array(
	            'regulamento_alteracao' => $regulamento_alteracao,
	            'collection'            => $collection
	        );

	        $this->load->view('planos/regulamento_alteracao/remissao', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_verificado_remissao()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $cd_regulamento_alteracao = $this->input->post('cd_regulamento_alteracao', TRUE);
        $cd_ref                   = $this->input->post('cd_ref', TRUE);
        $fl_tipo                  = $this->input->post('fl_tipo', TRUE);
        $fl_verificado            = $this->input->post('fl_verificado', TRUE);

        $row['dt_verificado'] = '';

        if(trim($fl_tipo) == 'U')
        {
            $row = $this->verifica_unidade_basica_ref($cd_ref, $fl_verificado);
        }
        else
        {
            $row = $this->verifica_estrutura_ref($cd_ref, $fl_verificado);
        }

        echo json_encode($row);
    }

    private function verifica_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref, $fl_verificado)
    {
        if(trim($fl_verificado) == 'S')
        {
            $this->regulamento_alteracao_model->atualiza_alteracao_unidade_basica_ref(
                $cd_regulamento_alteracao_unidade_basica_ref,
                $this->session->userdata('codigo')
            );
        }
        else
        {
            $this->regulamento_alteracao_model->excluir_alteracao_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref);
        }

        $row = $this->regulamento_alteracao_model->carrega_alteracao_unidade_basica_ref($cd_regulamento_alteracao_unidade_basica_ref);

        return $row['dt_verificado'];
    }

    private function verifica_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref, $fl_verificado)
    {
        if(trim($fl_verificado) == 'S')
        {
            $this->regulamento_alteracao_model->atualiza_alteracao_estrutura_ref(
                $cd_regulamento_alteracao_unidade_basica_estrutura_ref,
                $this->session->userdata('codigo')
            );
        }
        else
        {
            $this->regulamento_alteracao_model->excluir_alteracao_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref);
        }

        $row = $this->regulamento_alteracao_model->carrega_alteracao_estrutura_ref($cd_regulamento_alteracao_unidade_basica_estrutura_ref);

        return $row['dt_verificado'];
    }

    private function monta_revisao($cd_regulamento_alteracao, &$revisao = array(), $cd_regulamento_alteracao_revisao_pai = 0, $ds_ordem = '', $nr_nivel = 0)
    {
        $collection = $this->regulamento_alteracao_model->get_alteracao_revisao(
            $cd_regulamento_alteracao,
            $cd_regulamento_alteracao_revisao_pai
        );

        $nr_nivel ++;

        $i = count($revisao);

        foreach ($collection as $key => $item) 
        {
            $item['ds_ordem'] = (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'];

            $item['nr_nivel'] = $nr_nivel;

            if(trim($item['ds_descricao']) != '')
            {
                $item['ds_regulamento_alteracao_revisao'] = $item['ds_regulamento_alteracao_revisao'].br().'<i>'.trim($item['ds_descricao']).'</i>';
            }

            $revisao[$i] = $item;

            $i++;

            $i = $this->monta_revisao(
                $cd_regulamento_alteracao,
                $revisao,
                $item['cd_regulamento_alteracao_revisao'],
                (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'],
                $nr_nivel
            );
        }

        return $i;
    }

    public function revisao($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $regulamento_alteracao = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

	        $revisao = array();

	        $this->monta_revisao($cd_regulamento_alteracao, $revisao);

	        $data = array(
	            'regulamento_alteracao' => $regulamento_alteracao,
	            'collection'            => $revisao
	        );

	        $this->load->view('planos/regulamento_alteracao/revisao', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_verificado()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $cd_regulamento_alteracao         = $this->input->post('cd_regulamento_alteracao', TRUE);
        $cd_regulamento_alteracao_revisao = $this->input->post('cd_regulamento_alteracao_revisao', TRUE);
        $fl_verificado                    = $this->input->post('fl_verificado', TRUE);

        if(trim($fl_verificado) == 'S')
        {
            $this->regulamento_alteracao_model->atualiza_alteracao_revisao(
                $cd_regulamento_alteracao_revisao,
                $this->session->userdata('codigo')
            );

            $this->regulamento_alteracao_model->atualiza_alteracao_revisao_pai(
                $cd_regulamento_alteracao,
                $cd_regulamento_alteracao_revisao,
                $this->session->userdata('codigo')
            );
        }
        else
        {
            $this->regulamento_alteracao_model->excluir_alteracao_revisao($cd_regulamento_alteracao_revisao);
        }

        $row = $this->regulamento_alteracao_model->carrega_alteracao_revisao($cd_regulamento_alteracao_revisao);

        $data[$row['cd_regulamento_alteracao_revisao']]     = $row['dt_verificado'];
        $data[$row['cd_regulamento_alteracao_revisao_pai']] = $row['dt_verificado_pai'];

        echo json_encode($data);
    }

    public function quadro_comparativo($cd_regulamento_alteracao, $cd_regulamento_alteracao_quadro_comparativo = 0)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');  

	        $regulamento_alteracao = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

	        $ds_rodape_anterior = '';

	        $row_anterior = $this->regulamento_alteracao_model->carrega($regulamento_alteracao['cd_regulamento_alteracao_referencia']);

	        if(isset($row_anterior['ds_rodape']))
	        {
	            $ds_rodape_anterior = $row_anterior['ds_rodape'];
	        }

	        $row = array();

	        if(trim($regulamento_alteracao['dt_inicio_quadro_comparativo']) == '')
	        {
	            $collection = $this->monta_quadro_comparativo(
	                $cd_regulamento_alteracao, 
	                $regulamento_alteracao['cd_regulamento_alteracao_referencia']
	            );
	        }
	        else
	        {
	            if(intval($cd_regulamento_alteracao_quadro_comparativo) == 0)
	            {
	                $nr_ordem = '';

	                $row_quadro_comparativo = $this->regulamento_alteracao_model->get_next_ordem_quadro_comparativo(
	                    $cd_regulamento_alteracao
	                );

	                if(isset($row_quadro_comparativo['nr_ordem']))
	                {
	                    $nr_ordem = intval($row_quadro_comparativo['nr_ordem']);
	                }

	                $row = array(
	                    'cd_regulamento_alteracao_quadro_comparativo' => $cd_regulamento_alteracao_quadro_comparativo,
	                    'nr_ordem'                                    => $nr_ordem,
	                    'ds_texto_anterior'                           => '',
	                    'ds_texto_atual'                              => '',
	                    'ds_justificativa'                            => '',
	                    'tp_align_anterior'                           => '',
	                    'tp_align_atual'                              => ''
	                );
	            }
	            else
	            {
	                $row = $this->regulamento_alteracao_model->carrega_quadro_comparativo($cd_regulamento_alteracao_quadro_comparativo);
	            }

	            $collection = $this->regulamento_alteracao_model->listar_quadro_comparativo($cd_regulamento_alteracao);
	        }

	        $data = array(
	            'regulamento_alteracao' => $regulamento_alteracao,
	            'collection'            => $collection,
	            'row'                   => $row,
	            'ds_rodape_anterior'    => $ds_rodape_anterior,
	            'atividade' => $this->regulamento_alteracao_model->get_atividade_quadro_comparativo($cd_regulamento_alteracao)
	        );

	        $this->load->view('planos/regulamento_alteracao/quadro_comparativo', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    private function monta_quadro_comparativo($cd_regulamento_alteracao, $cd_regulamento_alteracao_referencia = 0)
    {
        $collection = array();

        $i = 1;

        $glossario_anterior = $this->regulamento_alteracao_model->get_glossario_referencia(
            $cd_regulamento_alteracao_referencia
        );

        if(count($glossario_anterior) > 0 AND intval($glossario_anterior['qt_glossario']) > 0)
        {
            $ds_texto_anterior = 'GLOSSÁRIO';
            $ds_texto_atual    = 'GLOSSÁRIO';
            $ds_justificativa  = '';
        }
        else
        {
            $ds_texto_anterior = '';
            $ds_texto_atual    = '<b>GLOSSÁRIO</b>';
            $ds_justificativa  = 'Incluído';
        }

        $collection[] = array(
            'cd_regulamento_alteracao_quadro_comparativo' => 0,
            'nr_ordem'                                    => $i,
            'cd_regulamento_alteracao_unidade_basica'     => 0,
            'tp_align_anterior'                           => 'C',
            'ds_texto_anterior'                           => $ds_texto_anterior,
            'tp_align_atual'                              => 'C',
            'ds_texto_atual'                              => $ds_texto_atual,
            'ds_justificativa'                            => $ds_justificativa,
            'dt_envio' 								      => ''
        );

        $i++;

        $glossario = $this->regulamento_alteracao_model->listar_glossario($cd_regulamento_alteracao, 'S');

        foreach ($glossario as $key => $item) 
        {
            $glossario_referencia = $this->regulamento_alteracao_model->carrega_glossario_referencia(
                $item['cd_regulamento_alteracao_glossario_referencia']
            );

            $ds_texto_anterior = '';
            $ds_justificativa  = '';
            $ds_texto_atual    = $item['ds_regulamento_alteracao_glossario'];
            $tp_align_atual     = 'J';

            if(intval($item['cd_regulamento_alteracao_glossario_referencia']) == 0)
            {
                $ds_justificativa = 'Incluído';
            }
            else if(trim($item['dt_removido']) != '')
            {
                $ds_texto_anterior = $glossario_referencia['ds_regulamento_alteracao_glossario'];
                $ds_texto_atual    = '(Glossário excluído)';
                $ds_justificativa  = 'Excluído';
                $tp_align_atual    = 'L';
            }
            else if(trim($item['fl_alteracao_texto']) == 'S')
            {
                $ds_texto_anterior = $glossario_referencia['ds_regulamento_alteracao_glossario'];
                $ds_justificativa = 'Alterado';
            }

            $collection[] = array(
                'cd_regulamento_alteracao_quadro_comparativo' => 0,
                'nr_ordem'                                    => $i,
                'cd_regulamento_alteracao_unidade_basica'     => 0,
                'tp_align_anterior'                           => 'J',
                'ds_texto_anterior'                           => $ds_texto_anterior,
                'tp_align_atual'                              => $tp_align_atual,
                'ds_texto_atual'                              => $ds_texto_atual,
                'ds_justificativa'                            => $ds_justificativa,
                'dt_envio' 								      => ''
            );

            $i++;
        }

        $estrutura = array();

        $this->monta_estrutura($cd_regulamento_alteracao, $estrutura, 0, TRUE, 0, '', 0, 'S');

        foreach ($estrutura as $key => $item) 
        {
            $data = $this->quadro_comparativo_estrutura($item);

            $data['tipo']['cd_regulamento_alteracao_quadro_comparativo'] = 0;
            $data['tipo']['nr_ordem'] = $i;
            $data['tipo']['dt_envio'] = '';

            $collection[] = $data['tipo'];

            $i++;

            $data['estrutura']['cd_regulamento_alteracao_quadro_comparativo'] = 0;
            $data['estrutura']['nr_ordem']                                    = $i;
            $data['estrutura']['cd_regulamento_alteracao_unidade_basica']     = 0;
            $data['estrutura']['dt_envio']                                    = '';

            $collection[] = $data['estrutura'];

            $i++;

            $artigo = $this->regulamento_alteracao_model->get_estrutura_artigo(
                $cd_regulamento_alteracao, 
                $item['cd_regulamento_alteracao_estrutura'],
                'S'
            );

            foreach ($artigo as $key2 => $item2) 
            {
                $data = $this->quadro_comparativo_artigo($item2);

                $data['artigo']['cd_regulamento_alteracao_quadro_comparativo'] = 0;
                $data['artigo']['nr_ordem']                                    = $i;
                $data['artigo']['cd_regulamento_alteracao_unidade_basica']     = $item2['cd_regulamento_alteracao_unidade_basica'];
                $data['artigo']['dt_envio']                                    = '';

                $collection[] = $data['artigo'];

                $i++;

                $unidade_basica = array();

                $this->monta_unidade_basica(
                    $cd_regulamento_alteracao, 
                    $item2['cd_regulamento_alteracao_unidade_basica'], 
                    $unidade_basica, 
                    0, 
                    0,
                    '',
                    'S'
                );

                foreach ($unidade_basica as $key3 => $item3) 
                {
                    $data = $this->quadro_comparativo_unidade_basica($item3);

                    $data['unidade_basica']['cd_regulamento_alteracao_quadro_comparativo'] = 0;
                    $data['unidade_basica']['nr_ordem']                                    = $i;
                    $data['unidade_basica']['cd_regulamento_alteracao_unidade_basica']     = $item3['cd_regulamento_alteracao_unidade_basica'];
                    $data['unidade_basica']['dt_envio']                                    = '';

                    $collection[] = $data['unidade_basica'];

                    $i++;
                }
            }
        }

        return $collection;
    }

    private function quadro_comparativo_estrutura($estrutura)
    {
        $estrutura_referencia = $this->regulamento_alteracao_model->get_estrutura_referencia(
            $estrutura['cd_regulamento_alteracao_estrutura_referencia']
        );

        $data['tipo'] = array(
            'nr_ordem'          => 0,
            'ds_texto_anterior' => '',
            'ds_texto_atual'    => '',
            'ds_justificativa'  => '',
            'tp_align_anterior' => 'C',
            'tp_align_atual'    => 'C'
        );

        $data['estrutura'] = array(
            'nr_ordem'          => 0,
            'ds_texto_anterior' => '',
            'ds_texto_atual'    => '',
            'ds_justificativa'  => '',
            'tp_align_anterior' => 'C',
            'tp_align_atual'    => 'C'
        );

        if(intval($estrutura['cd_regulamento_alteracao_estrutura_referencia']) == 0)
        {
            $data['tipo']['ds_texto_atual']   = '<b>'.$estrutura['ds_tipo'].'</b>';
            $data['tipo']['ds_justificativa'] = 'Incluído';

            $data['estrutura']['ds_texto_atual']   = '<b>'.$estrutura['ds_regulamento_alteracao_estrutura'].'</b>';
            $data['estrutura']['ds_justificativa'] = 'Incluído';
        }
        else if(trim($estrutura['dt_removido']) != '')
        {
            $data['tipo']['ds_texto_anterior'] = $estrutura_referencia['ds_tipo'];
            $data['tipo']['ds_texto_atual']    = '('.$estrutura['ds_regulamento_alteracao_estrutura_tipo'].' excluído)';
            $data['tipo']['ds_justificativa']  = 'Excluído';
            $data['tipo']['tp_align_atual']    = 'L';

            $data['estrutura']['ds_texto_anterior'] = $estrutura_referencia['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_texto_atual']    = '('.$estrutura['ds_regulamento_alteracao_estrutura_tipo'].' excluído)';
            $data['estrutura']['ds_justificativa']  = 'Excluído';
            $data['estrutura']['tp_align_atual']    = 'L';
        }
        else if(trim($estrutura['fl_alteracao_ordem']) == 'S' AND trim($estrutura['fl_alteracao_texto']) == 'S')
        {
            $data['tipo']['ds_texto_anterior'] = $estrutura_referencia['ds_tipo'];
            $data['tipo']['ds_texto_atual']    = '<b>'.$estrutura['ds_tipo'].'</b>';
            $data['tipo']['ds_justificativa']  = 'Alterado e Renumerado';

            $data['estrutura']['ds_texto_anterior'] = $estrutura_referencia['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_texto_atual']    = '<b>'.$estrutura['ds_regulamento_alteracao_estrutura'].'</b>';
            $data['estrutura']['ds_justificativa']  = 'Alterado e Renumerado';
        }
        else if(trim($estrutura['fl_alteracao_ordem']) == 'S')
        {
            $data['tipo']['ds_texto_anterior'] = $estrutura_referencia['ds_tipo'];
            $data['tipo']['ds_texto_atual']    = '<b>'.$estrutura['ds_tipo'].'</b>';
            $data['tipo']['ds_justificativa']  = 'Renumerado';

            $data['estrutura']['ds_texto_anterior'] = $estrutura_referencia['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_texto_atual']    = $estrutura['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_justificativa']  = '';
        }
        else if(trim($estrutura['fl_alteracao_texto']) == 'S')
        {
            $data['tipo']['ds_texto_anterior'] = $estrutura_referencia['ds_tipo'];
            $data['tipo']['ds_texto_atual']    = $estrutura['ds_tipo'];
            $data['tipo']['ds_justificativa']  = '';

            $data['estrutura']['ds_texto_anterior'] = $estrutura_referencia['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_texto_atual']    = '<b>'.$estrutura['ds_regulamento_alteracao_estrutura'].'</b>';
            $data['estrutura']['ds_justificativa']  = 'Alterado';
        }
        else
        {
            $data['tipo']['ds_texto_anterior'] = $estrutura['ds_tipo'];
            $data['tipo']['ds_texto_atual']    = $estrutura['ds_tipo'];
            $data['tipo']['ds_justificativa']  = '';

            $data['estrutura']['ds_texto_anterior'] = $estrutura['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_texto_atual']    = $estrutura['ds_regulamento_alteracao_estrutura'];
            $data['estrutura']['ds_justificativa']  = '';
        }

        return $data;
    }

    private function quadro_comparativo_artigo($artigo)
    {
        $artigo_referencia = $this->regulamento_alteracao_model->get_unida_basica_referencia(
            $artigo['cd_regulamento_alteracao_unidade_basica_referencia']
        );

        $data['artigo'] = array(
            'nr_ordem'          => 0,
            'ds_texto_anterior' => '',
            'ds_texto_atual'    => '',
            'ds_justificativa'  => '',
            'tp_align_anterior' => 'J',
            'tp_align_atual'    => 'J'
        );

        if(intval($artigo['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
        {
            $data['artigo']['ds_texto_atual']    = nl2br('<b>'.$artigo['ds_sigla_artigo'].$artigo['ds_numeracao_sigla_artigo'].'</b>'.$artigo['ds_regulamento_alteracao_unidade_basica']);

            $data['artigo']['ds_justificativa']  = 'Incluído';
        }
        else if(trim($artigo['dt_removido']) != '')
        {
            $data['artigo']['ds_texto_anterior'] = nl2br($this->remover_tags($artigo['ds_artigo']));

            $data['artigo']['ds_texto_atual']    = '('.$artigo['ds_tipo_unidade_basica'].' excluído)';

            $data['artigo']['ds_justificativa']  = 'Excluído';
        }
        else if(trim($artigo['fl_alteracao_ordem']) == 'S' AND trim($artigo['fl_alteracao_texto']) == 'S')
        {
            $data['artigo']['ds_texto_anterior'] = nl2br($this->remover_tags($artigo_referencia['ds_unidade_basica']));

            $data['artigo']['ds_texto_atual']    = nl2br($artigo['ds_sigla_artigo'].'<b>'.$artigo['ds_numeracao_sigla_artigo'].'</b>'.$artigo['ds_regulamento_alteracao_unidade_basica']);;

            $data['artigo']['ds_justificativa']  = 'Alterado e Renumerado';
        }
        else if(trim($artigo['fl_alteracao_ordem']) == 'S')
        {
            $data['artigo']['ds_texto_anterior'] = nl2br($this->remover_tags($artigo_referencia['ds_unidade_basica']));

            $data['artigo']['ds_texto_atual']    = nl2br($artigo['ds_sigla_artigo'].'<b>'.$artigo['ds_numeracao_sigla_artigo'].'</b>'.$artigo['ds_regulamento_alteracao_unidade_basica']);;

            $data['artigo']['ds_justificativa']  = 'Renumerado';
        }
        else if(trim($artigo['fl_alteracao_texto']) == 'S')
        {
            $data['artigo']['ds_texto_anterior'] = nl2br($this->remover_tags($artigo_referencia['ds_unidade_basica']));

            $data['artigo']['ds_texto_atual']    = nl2br($artigo['ds_artigo']);

            $data['artigo']['ds_justificativa']  = 'Alterado';
        }
        else
        {
            $data['artigo']['ds_texto_anterior'] = nl2br($this->remover_tags($artigo['ds_artigo']));

            $data['artigo']['ds_texto_atual']    = nl2br($this->remover_tags($artigo['ds_artigo']));
        }

        return $data;
    }

    private function quadro_comparativo_unidade_basica($unidade_basica)
    {
        $unidade_basica_referencia = $this->regulamento_alteracao_model->get_unida_basica_referencia(
            $unidade_basica['cd_regulamento_alteracao_unidade_basica_referencia']
        );

        $data['unidade_basica'] = array(
            'nr_ordem'          => 0,
            'ds_texto_anterior' => '',
            'ds_texto_atual'    => '',
            'ds_justificativa'  => '',
            'tp_align_anterior' => 'J',
            'tp_align_atual'    => 'J'
        );

        if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
        {
            if(intval($unidade_basica['cd_regulamento_alteracao_estrutura_tipo']) == 5 AND trim($unidade_basica['ds_simbolo_texto']) != '§')
            {
                $data['unidade_basica']['ds_texto_atual']    = nl2br('<b>'.$unidade_basica['ds_simbolo_texto'].'</b>'.$unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }
            else
            {
                $data['unidade_basica']['ds_texto_atual']    = nl2br('<b>'.$unidade_basica['ds_simbolo_texto'].$unidade_basica['ds_numeracao_sigla_unidade_basica'].'</b>'.$unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }

            $data['unidade_basica']['ds_justificativa']  = 'Incluído';
        }
        else if(trim($unidade_basica['dt_removido']) != '')
        {
            $data['unidade_basica']['ds_texto_anterior'] = nl2br($this->remover_tags($unidade_basica_referencia['ds_unidade_basica']));

            $data['unidade_basica']['ds_texto_atual']    = '('.$unidade_basica['ds_regulamento_alteracao_estrutura_tipo'].' excluído)';

            $data['unidade_basica']['ds_justificativa']  = 'Excluído';
        }
        else if(trim($unidade_basica['fl_alteracao_ordem']) == 'S' AND trim($unidade_basica['fl_alteracao_texto']) == 'S')
        {
            $data['unidade_basica']['ds_texto_anterior'] = nl2br($this->remover_tags($unidade_basica_referencia['ds_unidade_basica']));

            $data['unidade_basica']['ds_texto_atual']    = nl2br('<b>'.$unidade_basica['ds_simbolo_texto'].$unidade_basica['ds_numeracao_sigla_unidade_basica'].'</b>'.$unidade_basica['ds_regulamento_alteracao_unidade_basica']);

            $data['unidade_basica']['ds_justificativa']  = 'Alterado e Renumerado';
        }
        else if(trim($unidade_basica['fl_alteracao_ordem']) == 'S')
        {
            $data['unidade_basica']['ds_texto_anterior'] = nl2br($this->remover_tags($unidade_basica_referencia['ds_unidade_basica']));
            
            if(intval($unidade_basica['cd_regulamento_alteracao_estrutura_tipo']) == 5 AND trim($unidade_basica['ds_simbolo_texto']) != '§')
            {
                $data['unidade_basica']['ds_texto_atual']    = nl2br('<b>'.$unidade_basica['ds_simbolo_texto'].'</b>'.$unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }
            else
            {
                $data['unidade_basica']['ds_texto_atual']    = nl2br($unidade_basica['ds_simbolo_texto'].'<b>'.$unidade_basica['ds_numeracao_sigla_unidade_basica'].'</b>'.$unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }

            $data['unidade_basica']['ds_justificativa']  = 'Renumerado';
        }
        else if(trim($unidade_basica['fl_alteracao_texto']) == 'S')
        {
            $data['unidade_basica']['ds_texto_anterior'] = nl2br($this->remover_tags($unidade_basica_referencia['ds_unidade_basica']));

            $data['unidade_basica']['ds_texto_atual']    = nl2br($unidade_basica['ds_unidade_basica']);

            $data['unidade_basica']['ds_justificativa']  = 'Alterado';
        }
        else
        {
            $data['unidade_basica']['ds_texto_anterior'] = nl2br($this->remover_tags($unidade_basica_referencia['ds_unidade_basica']));

            $data['unidade_basica']['ds_texto_atual']    = nl2br($this->remover_tags($unidade_basica['ds_unidade_basica']));
        }

        return $data;
    }

    public function iniciar_quadro_comparativo($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->iniciar_quadro_comparativo(
	            $cd_regulamento_alteracao,
	            $this->session->userdata('codigo')
	        );

	        $collection = $this->monta_quadro_comparativo($cd_regulamento_alteracao);

	        foreach ($collection as $key => $item) 
	        {
	            if(trim($item['ds_justificativa']) != '')
	            {
	                $item['ds_justificativa'] = $item['ds_justificativa']."\n\n".'Motivo: ';
	            }

	            $args = array(
	                'cd_regulamento_alteracao'                => $cd_regulamento_alteracao,
	                'cd_regulamento_alteracao_unidade_basica' => (isset($item['cd_regulamento_alteracao_unidade_basica']) ? $item['cd_regulamento_alteracao_unidade_basica'] : 0),
	                'nr_ordem'                                => $item['nr_ordem'],
	                'ds_texto_anterior'                       => $item['ds_texto_anterior'],
	                'ds_texto_atual'                          => $item['ds_texto_atual'],
	                'ds_justificativa'                        => $item['ds_justificativa'],
	                'tp_align_anterior'                       => $item['tp_align_anterior'],
	                'tp_align_atual'                          => $item['tp_align_atual'],
	                'cd_usuario'                              => $this->session->userdata('codigo')
	            );

	            $this->regulamento_alteracao_model->salvar_quadro_comparativo($args);
	        }

	        redirect('planos/regulamento_alteracao/quadro_comparativo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function verifica_ordem_quadro_comparativo()
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $fl_verifica = $this->input->post('fl_verifica', TRUE);

        $row = $this->regulamento_alteracao_model->verifica_ordem_quadro_comparativo(
            $this->input->post('cd_regulamento_alteracao', TRUE), 
            $this->input->post('nr_ordem', TRUE),
            (trim($fl_verifica) == 'R') ? '=' : '>'
        );

        echo json_encode($row);
    }

    public function salvar_quadro_comparativo()
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $cd_regulamento_alteracao                    = $this->input->post('cd_regulamento_alteracao', TRUE);
	        $cd_regulamento_alteracao_quadro_comparativo = $this->input->post('cd_regulamento_alteracao_quadro_comparativo', TRUE);

	        $args = array(
	            'cd_regulamento_alteracao' => $cd_regulamento_alteracao,
	            'nr_ordem'                 => $this->input->post('nr_ordem', TRUE),
	            'ds_texto_anterior'        => $this->input->post('ds_texto_anterior', TRUE),
	            'ds_texto_atual'           => $this->input->post('ds_texto_atual', TRUE),
	            'ds_justificativa'         => $this->input->post('ds_justificativa', TRUE),
	            'tp_align_anterior'        => $this->input->post('tp_align_anterior', TRUE),
	            'tp_align_atual'           => $this->input->post('tp_align_atual', TRUE),
	            'cd_usuario'               => $this->session->userdata('codigo')
	        );

	        if(intval($cd_regulamento_alteracao_quadro_comparativo) == 0)
	        {
	            $cd_regulamento_alteracao_quadro_comparativo = $this->regulamento_alteracao_model->salvar_quadro_comparativo($args);

	            $fl_renumeracao =  $this->input->post('fl_renumeracao', TRUE);

	            if(trim($fl_renumeracao) == 'S')
	            {
	                $this->regulamento_alteracao_model->atuliza_quadro_comparativo_renumeracao($cd_regulamento_alteracao_quadro_comparativo, $args);
	            }
	        }
	        else
	        {
	            $this->regulamento_alteracao_model->atualizar_quadro_comparativo($cd_regulamento_alteracao_quadro_comparativo, $args);
	        }

	        redirect('planos/regulamento_alteracao/quadro_comparativo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function excluir_quadro_comparativo($cd_regulamento_alteracao, $cd_regulamento_alteracao_quadro_comparativo, $nr_ordem)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->excluir_quadro_comparativo(
	            $cd_regulamento_alteracao_quadro_comparativo,
	            $this->session->userdata('codigo')
	        );
	        
	        $args = array(
	            'cd_regulamento_alteracao' => $cd_regulamento_alteracao,
	            'nr_ordem'                 => $nr_ordem,
	            'cd_usuario'               => $this->session->userdata('codigo')
	        );

	        $this->regulamento_alteracao_model->atuliza_quadro_comparativo_renumeracao($cd_regulamento_alteracao_quadro_comparativo, $args, '-');

	        redirect('planos/regulamento_alteracao/quadro_comparativo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function finalizar_quadro_comparativo($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->finalizar_quadro_comparativo(
	            $cd_regulamento_alteracao,
	            $this->session->userdata('codigo')
	        );

	        redirect('planos/regulamento_alteracao/quadro_comparativo/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function versao_anterior($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');  
	        
	        $data['regulamento_alteracao'] = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao); 
	        
	        $data['collection'] = $this->regulamento_alteracao_model->listar_versao(
	            $data['regulamento_alteracao']['cd_regulamento_tipo'], 
	            $cd_regulamento_alteracao
	        );

	        $this->load->view('planos/regulamento_alteracao/versao_anterior', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function finalizar_alteracoes($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->finalizar_alteracoes(
	            intval($cd_regulamento_alteracao), 
	            $this->session->userdata('codigo')
	        );

	        $this->pdf_regulamento($cd_regulamento_alteracao);

	        redirect('planos/regulamento_alteracao/cadastro/'.intval($cd_regulamento_alteracao), 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function atividade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $unidade_basica = $this->regulamento_alteracao_model->carrega_estrutura_unidade_artigo($cd_regulamento_alteracao_unidade_basica);

	        $cd_regulamento_alteracao_unidade_basica_artigo = $cd_regulamento_alteracao_unidade_basica;

	        if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
	        {
	            $cd_regulamento_alteracao_unidade_basica_artigo = $unidade_basica['cd_regulamento_alteracao_unidade_basica_pai'];
	        }

	        $data = array(
	            'cd_regulamento_alteracao'                       => $cd_regulamento_alteracao,
	            'cd_regulamento_alteracao_unidade_basica'        => $cd_regulamento_alteracao_unidade_basica,
	            'unidade_basica'                                 => $unidade_basica,
	            'gerencia'                                       => $this->regulamento_alteracao_model->get_gerencia(),
	            'artigo'                                         => $this->regulamento_alteracao_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_artigo),
	            'regulamento_alteracao'                          => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
	        );

	        $data['row'] = $this->regulamento_alteracao_model->carrega_atividade_unidade_basica($cd_regulamento_alteracao_unidade_basica);

	        if(count($data['row']) == 0)
	        {
	            $data['row'] = array(
	                'cd_regulamento_alteracao_atividade' => 0,
	                'dt_envio'                           => ''
	            );
	        }

	        $data['gerencia_atividade'] = array();

	        foreach ($this->regulamento_alteracao_model->listar_gerencia_atividade_unidade_basica($data['row']['cd_regulamento_alteracao_atividade']) as $key => $item)
	        {
	            $data['gerencia_atividade'][] = $item['cd_gerencia'];
	        }

	        $this->load->view('planos/regulamento_alteracao/atividade', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function salvar_atividade_unidade_basica()
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $cd_regulamento_alteracao                       = $this->input->post('cd_regulamento_alteracao', TRUE);
	        $cd_regulamento_alteracao_unidade_basica        = $this->input->post('cd_regulamento_alteracao_unidade_basica', TRUE);
	        $cd_regulamento_alteracao_atividade             = $this->input->post('cd_regulamento_alteracao_atividade', TRUE);

	        $args = array(
	            'cd_regulamento_alteracao_unidade_basica' => $cd_regulamento_alteracao_unidade_basica,
	            'cd_gerencia'                             => (is_array($this->input->post('cd_gerencia', TRUE)) ? $this->input->post('cd_gerencia', TRUE): array()),
	            'cd_usuario'                              => $this->session->userdata('codigo')
	        );

	        if(intval($cd_regulamento_alteracao_atividade) == 0)
	        {
	            $cd_regulamento_alteracao_atividade = $this->regulamento_alteracao_model->salvar_atividade_unidade_basica($args);
	        }

	        $this->regulamento_alteracao_model->salvar_gerencia_atividade($cd_regulamento_alteracao_atividade, $args);

	        redirect('planos/regulamento_alteracao/atividade/'.$cd_regulamento_alteracao.'/'.$cd_regulamento_alteracao_unidade_basica, 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function encaminhar_atividade($cd_regulamento_alteracao, $cd_regulamento_alteracao_unidade_basica, $cd_regulamento_alteracao_atividade)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        $this->regulamento_alteracao_model->encaminhar_atividade($cd_regulamento_alteracao_atividade, $this->session->userdata('codigo'));

	        $this->envia_email_atividade($cd_regulamento_alteracao, $cd_regulamento_alteracao_atividade);

	        redirect('planos/regulamento_alteracao/atividade/'.$cd_regulamento_alteracao.'/'.$cd_regulamento_alteracao_unidade_basica, 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function encaminhar_atividades($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');

	        foreach ($this->regulamento_alteracao_model->listar_atividades_nao_encaminhadas($cd_regulamento_alteracao) as $key => $item) 
	        {
		        $this->regulamento_alteracao_model->encaminhar_atividade($item['cd_regulamento_alteracao_atividade'], $this->session->userdata('codigo'));

		        $this->envia_email_atividade($cd_regulamento_alteracao, $item['cd_regulamento_alteracao_atividade']);	
	        }

	        redirect('planos/regulamento_alteracao/quadro_comparativo/'.$cd_regulamento_alteracao, 'refresh');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    private function envia_email_atividade($cd_regulamento_alteracao, $cd_regulamento_alteracao_atividade)
    {
    	$this->load->model('projetos/eventos_email_model');

    	$row = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

    	foreach ($this->regulamento_alteracao_model->listar_gerencia_atividade_unidade_basica($cd_regulamento_alteracao_atividade) as $key => $item) 
    	{
    		foreach ($this->regulamento_alteracao_model->get_responsavel($item['cd_gerencia']) as $key2 => $item2) 
    		{
    			$responsavel[] = $item2['ds_usuario'];
    		}
    	}

        $cd_evento = 385;

        $email = $this->eventos_email_model->carrega($cd_evento);        

        $tags = array('[DS_REGULAMENTO]', '[LINK]');

        $subs = array($row['ds_regulamento_tipo'], site_url('atividade/regulamento_alteracao_atividade/index/'.intval($cd_regulamento_alteracao_atividade)));

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Regulamento de Plano',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => strtolower(implode(';', $responsavel)),
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function pdf($cd_regulamento_alteracao, $fl_gerar = 'N')
    {
    	if($this->get_permissao())
        {
	        if(trim($fl_gerar) == 'S')
	        {
	            $this->pdf_regulamento($cd_regulamento_alteracao);
	        }

	        $this->load->model('gestao/regulamento_alteracao_model');

	        $row = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);
	        
	        header('Location: '.base_url().'up/regulamento_alteracao/regulamento_'.$row['cd_plano'].'_'.$row['cd_regulamento_alteracao'].'.pdf');
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function pdf_regulamento($cd_regulamento_alteracao)
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $row = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);
    
        $collection = array();

        $this->monta_estrutura($cd_regulamento_alteracao, $collection);

        $glossario = $this->regulamento_alteracao_model->listar_glossario($cd_regulamento_alteracao);

        $pagina = array();

        $indice = array();

        $pagina[0] = $this->caminho.$this->cria_capa($row);
        $pagina[2] = $this->caminho.$this->cria_glossario($row, $glossario);
        
        $nr_page_glossario = 3;

        $indice[] = array(
            'nr_page'    => $nr_page_glossario,
            'ds_sumario' => 'GLOSSÁRIO',
            'nr_nivel'   => 1
        );

        $this->load->plugin('PDFMerger');

        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $qt_page = $fpdi->setSourceFile($pagina[2]);

        $nr_page_conteudo = $qt_page + $nr_page_glossario;

        $pagina[3] = $this->caminho.$this->cria_conteudo($row, $collection, $indice, $nr_page_conteudo);

        $pagina[1] = $this->caminho.$this->cria_sumario($row, $indice);

        $qt_page = $fpdi->setSourceFile($pagina[1]);

        if($qt_page > 1)
        {
            $indice = array();

            $nr_page_glossario = $qt_page + 2;

            $indice[] = array(
                'nr_page'    => $nr_page_glossario,
                'ds_sumario' => 'GLOSSÁRIO',
                'nr_nivel'   => 1
            );

            $qt_page = $fpdi->setSourceFile($pagina[2]);

            $nr_page_conteudo = $qt_page + $nr_page_glossario;

            $pagina[3] = $this->caminho.$this->cria_conteudo($row, $collection, $indice, $nr_page_conteudo);
            $pagina[1] = $this->caminho.$this->cria_sumario($row, $indice);
        }

        unset($fpdi);

        ksort($pagina);

        $regulamento = $this->merge_pdf($row, $pagina);

        $this->set_rodape($this->caminho.$regulamento, (trim($row['ds_rodape']) != '' ? $row['ds_rodape'] : ''));
    }

	private function cria_capa($row)
	{
		$this->load->plugin('fpdf');

		$name_file = 'capa_'.$row['cd_regulamento_alteracao'].'.pdf';

		$ob_pdf = new PDF();
        $ob_pdf->SetMargins(25, 15, 25);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = '';

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 80);
        $ob_pdf->SetFont('Times', '', 20);

        $ob_pdf->MultiCell(160, 4.5, 'REGULAMENTO', 0, 'C');
        $ob_pdf->MultiCell(160, 15, $row['ds_regulamento_tipo'], 0, 'C');

        $ob_pdf->SetFont('Times', '', 14);
        $ob_pdf->MultiCell(160, 20, 'CNPB '.$row['ds_cnpb'], 0, 'C');

        $ob_pdf->Output($this->caminho.$name_file , 'F');

        unset($ob_pdf);

        return $name_file;
	}

	private function cria_glossario($row, $glossario = array())
	{
		$this->load->plugin('fpdf');

		$name_file = 'glossario_'.$row['cd_regulamento_alteracao'].'.pdf';

		$ob_pdf = new PDF();
        $ob_pdf->SetMargins(25, 15, 25);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = '';

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->WriteTagSetStyle();

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont('Times', 'B', 12);

        $ob_pdf->MultiCell(160, 4.5, 'GLOSSÁRIO', 0, 'C');

        $ob_pdf->SetY($ob_pdf->GetY() + 5);

        foreach ($glossario as $key => $item) 
        {
            $ob_pdf->WriteTag(160, 5, '<p>'.$item['ds_regulamento_alteracao_glossario'].'</p>', 0, 'J');

            $ob_pdf->SetY($ob_pdf->GetY() + 5);
        }

        $ob_pdf->Output($this->caminho.$name_file , 'F');

        unset($ob_pdf);

        return $name_file;
	}

	private function cria_sumario($row, $indice)
	{
		$this->load->plugin('fpdf');

		$name_file = 'sumario_'.$row['cd_regulamento_alteracao'].'.pdf';

		$ob_pdf = new PDF();
        $ob_pdf->SetMargins(25, 15, 25);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = '';

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->WriteTagSetStyle();

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont('Times', 'B', 12);

        $ob_pdf->MultiCell(160, 4.5, 'SUMÁRIO', 0, 'C');

        $ob_pdf->SetFont('Times', '', 12);

        foreach ($indice as $key => $item) 
        {
        	if(intval($item['nr_nivel']) == 1)
        	{
        		$espaco = 0;

        		$ob_pdf->SetY($ob_pdf->GetY() + 6);
        	}
        	else
        	{
        		$espaco = (5 * $item['nr_nivel']);
        	}

			$ob_pdf->SetX($ob_pdf->GetX()+$espaco);

        	$ob_pdf->MultiCell(160-$espaco, 6, $item['ds_sumario'], '0', 'L');
			$ob_pdf->SetY($ob_pdf->GetY() - 6);
			//$ob_pdf->MultiCell(160, 10, '_______________________________________________________________________', '0', 'L');
			//$ob_pdf->SetY($ob_pdf->GetY() - 10);
			$ob_pdf->MultiCell(158, 6, $item['nr_page'], '0', 'R');
        }

        $ob_pdf->Output($this->caminho.$name_file , 'F');

        unset($ob_pdf);

        return $name_file;
	}

	private function cria_conteudo($row, $collection, &$indice, $nr_page)
	{
		$this->load->plugin('fpdf');

		$name_file = 'conteudo_'.$row['cd_regulamento_alteracao'].'.pdf';

        $ob_pdf = new PDF();				
        $ob_pdf->SetMargins(25, 15, 25);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = '';

        $ob_pdf->WriteTagSetStyle();

        $ob_pdf->SetStyle('tab', 'times', 'N', 12, '0,0,0', 15);

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 1);

        $nr_page_atual = 1;
        $nr_page_new   = $nr_page;

        foreach ($collection as $key => $item) 
        {
            if($ob_pdf->GetY() >= 240)
            {
                $ob_pdf->AddPage();
            }

        	$ob_pdf->SetFont('Times', (trim($item['fl_alteracao_ordem']) == 'S' ? 'B' : ''), 12);

        	$ob_pdf->MultiCell(160, 4.5, $item['ds_tipo'], 0, 'C');

            $ob_pdf->SetFont('Times', (trim($item['fl_alteracao_texto']) == 'S' ? 'B' : ''), 12);

        	$ob_pdf->MultiCell(160, 4.5, $item['ds_regulamento_alteracao_estrutura'], 0, 'C');

        	$ob_pdf->SetY($ob_pdf->GetY() + 5);

        	if($ob_pdf->PageNo() > $nr_page_atual)
        	{
        		$nr_page_new = $nr_page + ($ob_pdf->PageNo()-1);

        		$nr_page_atual = $ob_pdf->PageNo();
        	}

        	$indice[] = array(
	        	'nr_page'    => $nr_page_new,
	        	'ds_sumario' => $item['ds_tipo'].' - '.$item['ds_regulamento_alteracao_estrutura'],
	        	'nr_nivel'   => $item['nr_nivel']
	        );

        	$artigo = $this->regulamento_alteracao_model->get_estrutura_artigo($row['cd_regulamento_alteracao'], $item['cd_regulamento_alteracao_estrutura']);

        	foreach ($artigo as $key2 => $item2) 
        	{
                if($ob_pdf->GetY() >= 255)
                {
                    $ob_pdf->AddPage();
                }

                $ds_artigo = '<p>';

                if(intval($item2['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
                {
                    $ds_artigo .= '<b>';
                }

                $ds_artigo .= $item2['ds_sigla_artigo'];

                if(intval($item2['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
                {
                    $ds_artigo .= '</b>';
                }

                if(trim($item2['fl_alteracao_ordem']) == 'S')
                {
                    $ds_artigo .= '<b>';
                }

                $ds_artigo .= $item2['ds_numeracao_sigla_artigo'];

                if(trim($item2['fl_alteracao_ordem']) == 'S')
                {
                    $ds_artigo .= '</b>';
                }

                $ds_artigo .= $item2['ds_regulamento_alteracao_unidade_basica'].'</p>';

        		$ob_pdf->WriteTag(160, 5,$ds_artigo, 0, 'J');

        		$ob_pdf->SetY($ob_pdf->GetY() + 5);

        		$unidade_basica = array();

                $this->monta_unidade_basica($row['cd_regulamento_alteracao'], $item2['cd_regulamento_alteracao_unidade_basica'], $unidade_basica, 0, 0);

        		foreach ($unidade_basica as $key3 => $item3) 
        		{
                    if(intval($item3['cd_regulamento_alteracao_estrutura_tipo']) == 5 AND $ob_pdf->GetY() >= 255)
                    {
                        $ob_pdf->AddPage();
                    }

        			$espaco = 0;

        			$ds_unidade_basica = nl2br($item3['ds_unidade_basica']);

                    $unidade_basica_br = explode('<br />', $ds_unidade_basica);
                    
        			if(count($unidade_basica_br) <= 1)
        			{
                        $fl_altera_paragrafo_unico = FALSE;

                        $ds_unidade_basica = '<p>';

                        if(intval($item3['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
                        {
                            $ds_unidade_basica .= '<b>'.$item3['ds_simbolo_texto'].'</b>';

                            if(intval($item3['cd_regulamento_alteracao_estrutura_tipo']) == 5 AND trim($item3['ds_simbolo_texto']) != '§')
                            {
                                $fl_altera_paragrafo_unico = TRUE;
                            }
                        }
                        else if(intval($item3['cd_regulamento_alteracao_estrutura_tipo']) == 5 AND trim($item3['ds_simbolo_texto']) != '§' AND trim($item3['fl_alteracao_ordem']) == 'S')
                        {
                            $fl_altera_paragrafo_unico = TRUE;

                            $ds_unidade_basica .= '<b>'.$item3['ds_simbolo_texto'].'</b>';
                        }
                        else
                        {
                            $ds_unidade_basica .= $item3['ds_simbolo_texto'];
                        }
        
                        if(!$fl_altera_paragrafo_unico)
                        {
                            if(trim($item3['fl_alteracao_ordem']) == 'S')
                            {
                                $ds_unidade_basica .= '<b>'.$item3['ds_numeracao_sigla_unidade_basica'].'</b>';
                            }
                            else
                            {
                                $ds_unidade_basica .= $item3['ds_numeracao_sigla_unidade_basica'];
                            }
                        }
        
                        $ds_unidade_basica .= $item3['ds_regulamento_alteracao_unidade_basica'].'</p>';
                        
        				$tab = substr_count($item3['ds_unidade_basica'], '<tab>');

        				$espaco = (5 * ($item3['nr_nivel'] + $tab));

        				$ob_pdf->SetX($ob_pdf->GetX() + $espaco);

        				$ob_pdf->WriteTag(160-$espaco, 5, $ds_unidade_basica, 0, 'J');
        			}
        			else
        			{
        				foreach ($unidade_basica_br as $key4 => $item4) 
        				{
                            $ds_unidade_basica = $item4;

                            if(intval($key4) == 0)
                            {
                                $ds_unidade_basica = '';

                                if(intval($item3['cd_regulamento_alteracao_unidade_basica_referencia']) == 0)
                                {
                                    $ds_unidade_basica .= '<b>'.$item3['ds_simbolo_texto'].'</b>';
                                }
                                else
                                {
                                    $ds_unidade_basica .= $item3['ds_simbolo_texto'];
                                }
                
                                if(trim($item3['fl_alteracao_ordem']) == 'S')
                                {
                                    $ds_unidade_basica .= '<b>'.$item3['ds_numeracao_sigla_unidade_basica'].'</b>';
                                }
                                else
                                {
                                    $ds_unidade_basica .= $item3['ds_numeracao_sigla_unidade_basica'];
                                }

                                $regulamento_unidade_basica_br = explode('<br />', nl2br($item3['ds_regulamento_alteracao_unidade_basica']));
    
                                $ds_unidade_basica .= $regulamento_unidade_basica_br[0];
                            }

        					$tab = substr_count($item4, '<tab>');

        					$espaco = (5 * ($item3['nr_nivel'] + $tab));

        					$ob_pdf->SetX($ob_pdf->GetX() + $espaco);

        					$ob_pdf->WriteTag(160 - $espaco, 5,'<p>'.str_replace('<tab>', '', $ds_unidade_basica).'</p>', 0, 'J');

        					$ob_pdf->SetY($ob_pdf->GetY() + 1);
        				}
        			}

        			$ob_pdf->SetY($ob_pdf->GetY() + 5);
                }	
        	}

        	$ob_pdf->SetY($ob_pdf->GetY() + 5);
        }

        $ob_pdf->Output($this->caminho.$name_file , 'F');

        unset($ob_pdf);

        return $name_file;
	}

	private function merge_pdf($row, $pagina)
	{
		$this->load->plugin('PDFMerger');

		$ob_pdf = new PDFMerger_pi;

        $name_file = 'regulamento_'.$row['cd_plano'].'_'.$row['cd_regulamento_alteracao'].'.pdf';

        $ob_pdf->addPDFArray($pagina)->merge('file', $this->caminho.$name_file);
        /*
        foreach ($pagina as $key => $item) 
        {
            unlink($item);
        }
		*/
        unset($ob_pdf);

        return $name_file;
	}

	private function set_rodape($file, $texto = '')
    {
    	$this->load->plugin('PDFMerger');

        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $pagecount = $fpdi->setSourceFile($file);

        for($i = 1; $i <= $pagecount; $i++)
        {
            $tplidx = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($tplidx);

            $fpdi->addPage($size['h'] > $size['w'] ? 'P' : 'L', array($size['w']+50, $size['h']+100));
            $fpdi->useTemplate($tplidx);

            $x = ((35 * $size['w']) / 100);
            $y = ((97.5 * $size['h']) / 100);

            if($i == 1)
            {
            	$fpdi->SetFont('Times', '', 12);
	            $fpdi->SetY($y-35);
	  
	            $fpdi->MultiCell(0, 6, 'Fundação CEEE de Seguridade Social - ELETROCEEE', 0, 'C');
	            $fpdi->SetFont('Times', '', 9);
	            $fpdi->MultiCell(0, 6, 'Rua dos Andradas, 702 . Porto Alegre/RS, 90020-004 . Tel 51 3027 3100 . Fax 51 3228 5325 . eletro@eletroceee.com.br', 0, 'C');
	            $fpdi->MultiCell(0, 6, 'www.fundacaoceee.com.br', 0, 'C');
            }
            else
            {
	            $fpdi->SetFont('Times', '', 12);

	           	$fpdi->Text(27, $y, $texto);
	           	$fpdi->Text(185, $y, $i);
            }
        }

        $fpdi->Output($file , 'F');
    }

    public function pdf_quadro_comparativo($cd_regulamento_alteracao)
    {
        $this->load->model('gestao/regulamento_alteracao_model');

        $row = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

        $ds_rodape_anterior = '';

        $row_anterior = $this->regulamento_alteracao_model->carrega($row['cd_regulamento_alteracao_referencia']);

        if(isset($row_anterior['ds_rodape']))
        {
            $ds_rodape_anterior = $row_anterior['ds_rodape'];
        }

        $collection = $this->regulamento_alteracao_model->listar_quadro_comparativo($cd_regulamento_alteracao);

        $ds_texto_atual    = '<p>Texto Proposto</p>';
        $ds_texto_anterior = 'Texto Atual';

        if(trim($row['dt_aprovacao_previc']) != '')
        {
            $ds_texto_atual    = '<p>Texto Atual<br />('.$row['ds_rodape'].')</p>';
            $ds_texto_anterior = 'Texto Anterior';
        }

        $head = array('<p>'.$ds_texto_anterior.'<br />('.$ds_rodape_anterior.')</p>', $ds_texto_atual, '<p>Justificativas</p>');

        $head_align = array('C','C','C');

        $this->load->plugin('fpdf');

        $ob_pdf = new PDF('L','mm','A4');

        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10,14,5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo_font = 1;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = $row['ds_regulamento_tipo'].' - CNPB '.$row['ds_cnpb'];

        $ob_pdf->WriteTagSetStyle();

        $ob_pdf->SetStyle('tab', 'times', 'N', 11, '0,0,0', 5);
        $ob_pdf->SetStyle('p',   'times', 'N', 11, '0,0,0');

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 1);

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0,0,0);
        $ob_pdf->SetWidths(array(100, 100, 75));  
        $ob_pdf->SetAligns($head_align);
        $ob_pdf->RowTag($head);

        foreach($collection as $item)
        {
            $ob_pdf->SetWidths(array(100, 100, 75));  
            $ob_pdf->SetAligns(array($item['tp_align_anterior'], $item['tp_align_atual'], 'J'));

            $ob_pdf->RowTag(array(
                '<p>'.$item['ds_texto_anterior'].'</p>',
                '<p>'.$item['ds_texto_atual'].'</p>',
                '<p>'.nl2br($item['ds_justificativa']).'</p>'
            ), $head, $head_align);
        }    

        $ob_pdf->Output();
        exit;
    }

    public function atividades($cd_regulamento_alteracao)
    {
    	if($this->get_permissao())
        {
	        $this->load->model('gestao/regulamento_alteracao_model');  

	        $data['regulamento_alteracao'] = $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao);

	        $collection = $this->regulamento_alteracao_model->listar_atividades($cd_regulamento_alteracao);

	        foreach ($collection as $key => $item) 
	        {
	        	$collection[$key]['atividade'] = $this->regulamento_alteracao_model->get_atividades($item['cd_regulamento_alteracao_atividade']);
	        }

	        $data['collection'] = $collection;

	        $this->load->view('planos/regulamento_alteracao/atividades', $data);
	    }
	    else
	    {
	    	exibir_mensagem('ACESSO NÃO PERMITIDO');
	    }
    }

    public function acompanhamento($cd_regulamento_alteracao, $cd_regulamento_alteracao_atividade_gerencia, $cd_regulamento_alteracao_atividade_acompanhamento = 0)
    {
    	if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$atividade_gerencia = $this->regulamento_alteracao_model->carrega_atividade_gerencia($cd_regulamento_alteracao_atividade_gerencia);

			$data = array(
				'atividade_gerencia' 			=> $atividade_gerencia,
				'collection' 			=> $this->regulamento_alteracao_model->listar_acompanhamento($atividade_gerencia['cd_regulamento_alteracao_atividade_gerencia']),
				'regulamento_alteracao' => $this->regulamento_alteracao_model->carrega($cd_regulamento_alteracao),
				'unidade_basica' 		=> $this->regulamento_alteracao_model->carrega_unidade_basica($atividade_gerencia['cd_regulamento_alteracao_unidade_basica']),
				'cd_usuario' 			=> $this->session->userdata('codigo')
			);

			if(intval($data['unidade_basica']['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
			{
				$data['unidade_basica_pai'] = $this->regulamento_alteracao_model->carrega_unidade_basica($data['unidade_basica']['cd_regulamento_alteracao_unidade_basica_pai']);
			}

			if(intval($cd_regulamento_alteracao_atividade_acompanhamento) == 0)
			{
				$data['row'] = array(
					'cd_regulamento_alteracao_atividade_acompanhamento' => '',
					'ds_regulamento_alteracao_atividade_acompanhamento' => ''
				);
			}
			else
			{
				$data['row'] = $this->regulamento_alteracao_model->carrega_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento);
			}

			$this->load->view('planos/regulamento_alteracao/acompanhamento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

	public function salvar_acompanhamento()
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/regulamento_alteracao_model');

			$cd_regulamento_alteracao 			   			   = $this->input->post('cd_regulamento_alteracao', TRUE);
			$cd_regulamento_alteracao_atividade_acompanhamento = $this->input->post('cd_regulamento_alteracao_atividade_acompanhamento', TRUE);

			$args = array(
				'cd_regulamento_alteracao_atividade_gerencia' 		=> $this->input->post('cd_regulamento_alteracao_atividade_gerencia', TRUE),
				'ds_regulamento_alteracao_atividade_acompanhamento' => $this->input->post('ds_regulamento_alteracao_atividade_acompanhamento', TRUE),
				'cd_usuario' 										=> $this->session->userdata('codigo')
			);

			if(intval($cd_regulamento_alteracao_atividade_acompanhamento) == 0)
			{
				$this->regulamento_alteracao_model->salvar_acompanhamento($args);
			}
			else
			{
				$this->regulamento_alteracao_model->atualizar_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento, $args);
			}

			redirect('planos/regulamento_alteracao/acompanhamento/'.$cd_regulamento_alteracao.'/'.$args['cd_regulamento_alteracao_atividade_gerencia'], 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}