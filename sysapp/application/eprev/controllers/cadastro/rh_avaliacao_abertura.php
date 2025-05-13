<?php
class Rh_avaliacao_abertura extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
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
            $this->load->view('cadastro/rh_avaliacao_abertura/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            $args = array();
    
            manter_filtros($args);
    
            $data['collection'] = $this->avaliacao_model->listar($args);
    
            $this->load->view('cadastro/rh_avaliacao_abertura/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_avaliacao = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            if(intval($cd_avaliacao) == 0)
            {
            	$row = $this->avaliacao_model->carrega_anterior();

                $data['row'] = array(
                    'cd_avaliacao'               => intval($cd_avaliacao),
                    'nr_ano_avaliacao'           => (isset($row['nr_ano_avaliacao']) ? $row['nr_ano_avaliacao'] : date('Y')),
                    'dt_inicio'                  => '',
                    'dt_encerramento'            => '',
                    'ds_instrucao_preenchimento' => (isset($row['ds_instrucao_preenchimento']) ? $row['ds_instrucao_preenchimento'] : ''),
                    'dt_envio_email'             => '',
                    'cd_usuario_envio_email'     => '',
                    'fl_permissao'               => 1
                );
            }
            else
            {
                $data['row'] = $this->avaliacao_model->carrega($cd_avaliacao);
            }
    
            $this->load->view('cadastro/rh_avaliacao_abertura/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function encaminhar_email($cd_avaliacao)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'rh_avaliacao/avaliacao_model',
                'projetos/eventos_email_model'
            ));
        
            $cd_evento_avaliado = 373;

            $email_avaliado = $this->eventos_email_model->carrega($cd_evento_avaliado);   

            $cd_evento_avaliador = 374;

            $email_avaliador = $this->eventos_email_model->carrega($cd_evento_avaliador);   

            $row = $this->avaliacao_model->carrega($cd_avaliacao);

            $args = array(
                'ds_cargo'    => '',
                'cd_gerencia' => ''
            );

            foreach ($this->avaliacao_model->listar_avaliacao($cd_avaliacao, $args) as $key => $item) 
            {
                if(trim($item['dt_envio_email']) == '')
                {
                    $this->envia_email_avaliado($row, $item, $email_avaliado, $cd_evento_avaliado);

                    $this->envia_email_avaliador($row, $item, $email_avaliador, $cd_evento_avaliador);

                    $this->avaliacao_model->salvar_envio_email_avaliacao_usuario($item['cd_avaliacao_usuario'], $this->session->userdata('codigo'));
                }
            } 

            $this->avaliacao_model->salvar_envio_email($cd_avaliacao, $this->session->userdata('codigo'));

            redirect('cadastro/rh_avaliacao_abertura/cadastro/'.$cd_avaliacao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function envia_email_avaliado($avaliacao, $avaliado, $email, $cd_evento)
    {
        $tags = array('[NR_ANO]', '[DS_NOME]', '[LINK]');

        $subs = array(
            $avaliacao['nr_ano_avaliacao'],
            $avaliado['ds_nome'],
            site_url('cadastro/rh_avaliacao/formulario/'.intval($avaliado['cd_avaliacao_usuario']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $assunto = str_replace('[NR_ANO]', $avaliacao['nr_ano_avaliacao'], $email['assunto']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Sistema de Avaliação - Abertura',
            'assunto' => $assunto,
            'para'    => $avaliado['ds_usuario'].'@eletroceee.com.br',  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    private function envia_email_avaliador($avaliacao, $avaliado, $email, $cd_evento)
    {    
        $tags = array('[NR_ANO]', '[DS_NOME]', '[DS_NOME_AVALIADOR]', '[LINK]');

        $subs = array(
            $avaliacao['nr_ano_avaliacao'],
            $avaliado['ds_nome'],
            $avaliado['ds_avaliador'],
            site_url('cadastro/rh_avaliacao/formulario/'.intval($avaliado['cd_avaliacao_usuario']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $assunto = str_replace(array('[NR_ANO]', '[DS_NOME]'), array($avaliacao['nr_ano_avaliacao'], $avaliado['ds_nome']), $email['assunto']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Sistema de Avaliação - Autoavaliação',
            'assunto' => $assunto,
            'para'    => $avaliado['ds_usuario_avaliador'].'@eletroceee.com.br',  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            $cd_avaliacao = $this->input->post('cd_avaliacao', TRUE);        
    
            $args = array(
                'nr_ano_avaliacao'           => $this->input->post('nr_ano_avaliacao', TRUE),
                'dt_inicio'                  => $this->input->post('dt_inicio', TRUE),
                'dt_encerramento'            => $this->input->post('dt_encerramento', TRUE),
                'ds_instrucao_preenchimento' => $this->input->post('ds_instrucao_preenchimento', TRUE),
                'cd_usuario'                 => $this->session->userdata('codigo')
            );
    
            if(intval($cd_avaliacao) == 0)
            {
                $cd_avaliacao = $this->avaliacao_model->salvar($args);

     			$this->gerar($cd_avaliacao);
            }
            else
            {
                $this->avaliacao_model->atualizar($cd_avaliacao, $args);
            }
    
            redirect('cadastro/rh_avaliacao_abertura', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gerar($cd_avaliacao)
    {
        if($this->get_permissao())
        {
            ini_set('max_execution_time', 0);

            $this->load->model('rh_avaliacao/avaliacao_model');

            $usuarios = $this->gera_usuario($cd_avaliacao);

            echo 'Total a ser criado: '.count($usuarios).br();

            $this->gera_avaliacao($cd_avaliacao);

            $i = 0;

            foreach ($usuarios as $key => $item) 
            {
                $this->gera_formulario($cd_avaliacao, $item);

                echo 'Usuário : '.$item.' criado.'.br();

                $i++;
            }

            echo 'Total criado: '.$i.br();
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function gera_usuario($cd_avaliacao)
    {
    	$collection = $this->avaliacao_model->get_usuario_avaliacao($cd_avaliacao);

        $usuarios = array();

    	foreach ($collection as $key => $item) 
    	{
    		$row = $this->avaliacao_model->get_progresso_promocao($item['cd_usuario']);

    		if(count($row) > 0)
    		{
    			$args = array(
    				'cd_avaliacao'               => $cd_avaliacao,
                    'cd_usuario'                 => $item['cd_usuario'],
    				'cd_usuario_avaliador'       => $item['cd_avaliador'],
    				'ds_cargo'                   => $row['ds_cargo'],
    				'ds_cargo_area_atuacao'      => $row['ds_cargo_area_atuacao'],
    				'ds_classe'                  => $row['ds_classe'],
    				'ds_padrao'                  => $row['ds_padrao'],
                    'ds_conhecimento_generico'   => $row['ds_conhecimento_generico'],
                    'ds_conhecimento_especifico' => $row['ds_conhecimento_especifico'],
                    'ds_escolaridade_cargo'      => $row['ds_formacao'],
                    'ds_escolaridade_avaliado'   => $row['nome_escolaridade'],
    				'cd_usuario_inclusao'        => $this->session->userdata('codigo')
    			);

    			$cd_avaliacao_usuario = $this->avaliacao_model->salvar_avaliacao_usuario($args);

                $args = array(
                    'cd_avaliacao'         => $cd_avaliacao,
                    'cd_avaliacao_usuario' => $cd_avaliacao_usuario,
                    'cd_usuario'           => $item['cd_usuario'],
                    'tp_avaliacao'         => 'PRI',
                    'cd_usuario_inclusao'  => $this->session->userdata('codigo')
                );

                $this->avaliacao_model->salvar_avaliacao_usuario_avaliacao($args);

                $args = array(
                    'cd_avaliacao'         => $cd_avaliacao,
                    'cd_avaliacao_usuario' => $cd_avaliacao_usuario,
                    'cd_usuario'           => $item['cd_avaliador'],
                    'tp_avaliacao'         => 'SEG',
                    'cd_usuario_inclusao'  => $this->session->userdata('codigo')
                );

                $this->avaliacao_model->salvar_avaliacao_usuario_avaliacao($args);

                $usuarios[] = $item['cd_usuario'];
    		}
    	}

        return $usuarios;
    }

    private function gera_avaliacao($cd_avaliacao)
    {
        $this->avaliacao_model->salvar_avaliacao_performance($cd_avaliacao, $this->session->userdata('codigo'));
        $this->avaliacao_model->salvar_matriz_conceito($cd_avaliacao, $this->session->userdata('codigo'));
        $this->avaliacao_model->salvar_matriz_acao($cd_avaliacao, $this->session->userdata('codigo'));
        $this->avaliacao_model->salvar_matriz_quadro($cd_avaliacao, $this->session->userdata('codigo'));
    }

    private function gera_formulario($cd_avaliacao, $cd_usuario = 0)
    {
    	$collection = $this->avaliacao_model->get_avaliacao_usuario($cd_avaliacao, $cd_usuario);

    	foreach ($collection as $key => $item) 
    	{
    		$bloco = $this->avaliacao_model->get_bloco_classe($item['ds_classe']);

    		foreach ($bloco as $key2 => $item2) 
    		{
    			$args = array(
    				'cd_avaliacao'         => $cd_avaliacao,
    				'cd_avaliacao_usuario' => $item['cd_avaliacao_usuario'],
    				'tp_grupo'             => $item2['tp_grupo'],
    				'ds_bloco'             => $item2['ds_bloco'],
                    'ds_bloco_descricao'   => $item2['ds_bloco_descricao'],
    				'fl_conhecimento'      => $item2['fl_conhecimento'],
    				'cd_usuario'           => $this->session->userdata('codigo')
    			);

    			$cd_avaliacao_bloco = $this->avaliacao_model->gera_avaliacao_bloco($args);

                if(trim($item2['tp_grupo']) == 'C')
                {
                    $pergunta = $this->avaliacao_model->get_pergunta_bloco($item2['cd_bloco'], $item['ds_classe']);
                }
                else if(trim($item2['tp_grupo']) == 'FD')
                {
                    $pergunta = $this->avaliacao_model->get_pergunta_bloco_fator_desempenho($item2['cd_bloco']);
                }

    			foreach ($pergunta as $key3 => $item3) 
    			{
    				$args = array(
    					'cd_avaliacao'         => $cd_avaliacao,
    					'cd_avaliacao_usuario' => $item['cd_avaliacao_usuario'],
    					'cd_avaliacao_bloco'   => $cd_avaliacao_bloco,
    					'ds_pergunta'          => $item3['ds_pergunta'],
    					'cd_usuario'           => $this->session->userdata('codigo')
    				);

    				$this->avaliacao_model->gera_avaliacao_pergunta($args);
    			}
    		}
    	}
    }

    public function gera_usuario_avaliacao($cd_avaliacao)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('rh_avaliacao/avaliacao_model');

        	$this->gera_usuario($cd_avaliacao);

        	redirect('cadastro/rh_avaliacao_abertura', 'refresh');
        }
        else
        {
        	exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gera_formulario_avaliacao($cd_avaliacao, $cd_usuario = 0)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('rh_avaliacao/avaliacao_model');

        	$this->gera_formulario($cd_avaliacao, $cd_usuario);

        	redirect('cadastro/rh_avaliacao_abertura', 'refresh');
        }
        else
        {
        	exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function avaliacao($cd_avaliacao)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('rh_avaliacao/avaliacao_model');

            $data = array(
                'cd_avaliacao' => $cd_avaliacao,
                'row'          => $this->avaliacao_model->carrega($cd_avaliacao),
                'cargo'        => $this->avaliacao_model->get_avaliacao_usuario_cargo($cd_avaliacao),
                'gerencia'     => $this->avaliacao_model->get_avaliacao_usuario_gerencia($cd_avaliacao),
                'somatorio'    => $this->avaliacao_model->get_resultado_somado($cd_avaliacao)
            );

        	$this->load->view('cadastro/rh_avaliacao_abertura/avaliacao', $data);
        }
        else
        {
        	exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_avaliacao()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao = $this->input->post('cd_avaliacao', TRUE);

        $args = array(
            'ds_cargo'    => utf8_decode($this->input->post('ds_cargo', TRUE)),
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE)
        );

        $fl_progressao = $this->input->post('fl_progressao', TRUE);
        $fl_promocao   = $this->input->post('fl_promocao', TRUE);

        $data['collection'] = $this->avaliacao_model->listar_avaliacao($cd_avaliacao, $args);

        foreach ($data['collection'] as $key => $item)
        {
            $row       = $this->avaliacao_model->get_formulario_usuario($item['cd_avaliacao_usuario']);
            $resultado = $this->avaliacao_model->resultado($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);
            
            $data['collection'][$key]['cd_matriz']     = '';
            $data['collection'][$key]['nr_resultado']  = 0;
            $data['collection'][$key]['ds_progressao'] = '';
            $data['collection'][$key]['ds_promocao']   = '';
            $data['collection'][$key]['fl_progressao'] = '';
            $data['collection'][$key]['fl_promocao']   = '';
            $data['collection'][$key]['nr_ranking']    = '';

            if(count($resultado) > 0)
            {
                $data['collection'][$key]['cd_matriz']    = $resultado[0]['cd_matriz'].$resultado[1]['cd_matriz'];
                $data['collection'][$key]['nr_resultado'] = (($resultado[0]['nr_resultado']+$resultado[1]['nr_resultado']) / 2);

                $matriz_conceito = $this->avaliacao_model->get_matriz_conceito($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'], 'C');

                $cd_avaliacao_matriz_conceito_a = intval($matriz_conceito['cd_avaliacao_matriz_conceito']);

                $matriz_conceito = $this->avaliacao_model->get_matriz_conceito($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'], 'FD');

                $cd_avaliacao_matriz_conceito_b = intval($matriz_conceito['cd_avaliacao_matriz_conceito']);

                $promocao_progressao = $this->avaliacao_model->get_promocao_progressao($cd_avaliacao_matriz_conceito_a, $cd_avaliacao_matriz_conceito_b);

                $data['collection'][$key]['fl_progressao'] = $promocao_progressao['fl_progressao'];
                $data['collection'][$key]['fl_promocao']   = $promocao_progressao['fl_promocao'];
                $data['collection'][$key]['nr_ranking']    = $promocao_progressao['nr_ranking'];

                if(trim($promocao_progressao['fl_progressao']) == 'S')
                {
                    $data['collection'][$key]['ds_progressao'] = '<span class="label label-success">Sim</span>';
                }
                else if(trim($promocao_progressao['fl_progressao']) == 'N')
                {
                    $data['collection'][$key]['ds_progressao'] = '<span class="label label-important">Não</span>';
                }

                if(trim($promocao_progressao['fl_promocao']) == 'S')
                {
                    $data['collection'][$key]['ds_promocao'] = '<span class="label label-success">Sim</span>';
                }
                else if(trim($promocao_progressao['fl_promocao']) == 'N')
                {
                    $data['collection'][$key]['ds_promocao'] = '<span class="label label-important">Não</span>';
                }
            }

            if(trim($fl_progressao) != '')
            {
                if(trim($data['collection'][$key]['fl_progressao']) != trim($fl_progressao))
                {
                    unset($data['collection'][$key]);
                }
            }

            if(trim($fl_promocao) != '')
            {
                if(trim($data['collection'][$key]['fl_promocao']) != trim($fl_promocao))
                {
                    unset($data['collection'][$key]);
                }
            }
        }

        $this->load->view('cadastro/rh_avaliacao_abertura/avaliacao_result', $data);
    }

    public function treinamentos($cd_avaliacao_usuario)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        if($this->get_permissao())
        {
            $row = $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);

            $data = array(
                'row'        => $row,
                'collection' => $this->avaliacao_model->get_treinamento_avaliado($row['nr_ano_avaliacao'], $row['cd_usuario'])
            );

            $this->load->view('cadastro/rh_avaliacao_abertura/treinamentos', $data);
            
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function avaliado($cd_avaliacao_usuario)
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

		if($this->get_permissao())
        {
        	$row = $this->avaliacao_model->carrega_usuario_avaliacao($cd_avaliacao_usuario);

            $data = array(
                'row' 	    => $row,
                'avaliacao' => $this->avaliacao_model->carrega($row['cd_avaliacao']),
                'gerencia'  => $this->avaliacao_model->get_gerencia(),
                'usuario'   => $this->avaliacao_model->get_usuario($row['ds_area_avaliador'])
            );

            $this->load->view('cadastro/rh_avaliacao_abertura/avaliado', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_usuario()
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

    	$cd_gerencia = $this->input->post('cd_gerencia', TRUE);

    	foreach ($this->avaliacao_model->get_usuario($cd_gerencia) as $key => $item)
    	{
    		$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
    	}

    	echo json_encode($data);
    }

    public function salvar_avaliador()
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

    	$cd_avaliacao_usuario = $this->input->post('cd_avaliacao_usuario', TRUE);
    	$cd_usuario_avaliador = $this->input->post('cd_usuario_avaliador', TRUE);

    	$this->avaliacao_model->salvar_avaliador($cd_avaliacao_usuario, $cd_usuario_avaliador, $this->session->userdata('codigo'));
    	$this->avaliacao_model->salvar_avaliador_avaliacao($cd_avaliacao_usuario, $cd_usuario_avaliador, $this->session->userdata('codigo'));

    	redirect('cadastro/rh_avaliacao_abertura/avaliado/'.$cd_avaliacao_usuario, 'refresh');
    }

    public function relatorio($cd_avaliacao)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            $data = array(
                'cd_avaliacao' => $cd_avaliacao,
                'row'          => $this->avaliacao_model->carrega($cd_avaliacao),
                'cargo'        => $this->avaliacao_model->get_avaliacao_usuario_cargo($cd_avaliacao),
                'gerencia'     => $this->avaliacao_model->get_avaliacao_usuario_gerencia($cd_avaliacao),
                'somatorio'    => $this->avaliacao_model->get_resultado_somado($cd_avaliacao)
            );

            $this->load->view('cadastro/rh_avaliacao_abertura/relatorio', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_relatorio()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao = $this->input->post('cd_avaliacao', TRUE);

        $args = array(
            'ds_cargo'    => utf8_decode($this->input->post('ds_cargo', TRUE)),
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE),
            'diretoria'   => ($this->session->userdata('tipo') == 'D' ? $this->session->userdata('diretoria') : '')
        );

        $data['ordem_ranking'] = array();

        $data['matriz'] = $this->monta_matriz($cd_avaliacao, $args, $data['ordem_ranking'], $data['collection']);

        $this->load->view('cadastro/rh_avaliacao_abertura/relatorio_result', $data);
    }

    public function relatorio_gerencia()
    {
        if(trim($this->session->userdata('tipo')) == 'D' OR trim($this->session->userdata('tipo')) == 'G' OR trim($this->session->userdata('indic_01')) == 'S' OR $this->get_permissao())
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            $ultima_avaliacao = $this->avaliacao_model->get_ultima_avaliacao();

            $gerencia = array();

            if(trim($this->session->userdata('tipo')) == 'D')
            {
                $gerencia = $this->avaliacao_model->get_gerencia(trim($this->session->userdata('diretoria')));
            }

            $data = array(
                'avaliacao'    => $this->avaliacao_model->get_avaliacao_ano(),
                'cargo'        => $this->avaliacao_model->get_avaliacao_usuario_cargo($ultima_avaliacao['cd_avaliacao']),
                'gerencia'     => $gerencia,
                'cd_gerencia'  => $this->session->userdata('divisao'),
                'cd_avaliacao' => $ultima_avaliacao['cd_avaliacao']
            );

            $this->load->view('cadastro/rh_avaliacao_abertura/relatorio_gerencia', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function monta_matriz($cd_avaliacao, $args = array(), &$ordem_ranking, &$coll)
    {
        $matriz = array();

        $collection = $this->avaliacao_model->matriz($cd_avaliacao);

        $conceito_a = '';

        $i = 0;

        foreach ($collection as $key => $item) 
        {
            if(trim($conceito_a) != trim($item['ds_matriz_conceito_a']))
            {
                $i ++;
                $conceito_a = trim($item['ds_matriz_conceito_a']);
                $matriz[$i] = array();
            }

            $ordem_ranking[$key] = $item['nr_ranking'];

            $avaliacao = $this->avaliacao_model->get_avaliacao_matriz($cd_avaliacao, trim($item['ds_matriz_conceito_a']), trim($item['ds_matriz_conceito_b']), $args);

            $promocao_progressao = $this->avaliacao_model->get_promocao_progressao($item['cd_avaliacao_matriz_conceito_a'], $item['cd_avaliacao_matriz_conceito_b']);

            $ds_progressao = '';
            $ds_promocao   = '';

            if(trim($promocao_progressao['fl_progressao']) == 'S')
            {
                $ds_progressao = '<span class="label label-success">Sim</span>';
            }
            else if(trim($promocao_progressao['fl_progressao']) == 'N')
            {
                $ds_progressao = '<span class="label label-important">Não</span>';
            }

            if(trim($promocao_progressao['fl_promocao']) == 'S')
            {
                $ds_promocao = '<span class="label label-success">Sim</span>';
            }
            else if(trim($promocao_progressao['fl_promocao']) == 'N')
            {
                $ds_promocao = '<span class="label label-important">Não</span>';
            }

            $coll[$key] = array(
                'cd_matriz'     => trim($item['ds_matriz_conceito_a']).trim($item['ds_matriz_conceito_b']),
                'ds_matriz'     => trim($item['ds_matriz_acao']),
                'avaliacao'     => $avaliacao,
                'ds_progressao' => $ds_progressao,
                'ds_promocao'   => $ds_promocao
            );

            $matriz[$i][] = array(
                'cd_matriz'    => trim($item['ds_matriz_conceito_a']).trim($item['ds_matriz_conceito_b']),
                'ds_matriz'    => trim($item['ds_matriz_acao']),
                'cor_fundo'    => $item['cor_fundo'],
                'cor_texto'    => $item['cor_texto'],
                'nr_resultado' => count($avaliacao)
            );
        }

        asort($ordem_ranking);

        return $matriz;
    }

    public function pdf($cd_avaliacao_usuario)
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

    	$row 		= $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);
    	$row_status = $this->avaliacao_model->formulario_status($cd_avaliacao_usuario, $this->session->userdata('codigo'));
    	$resultado  = $this->avaliacao_model->resultado($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);
    	$collection = $this->avaliacao_model->get_formulario_grupo($row['cd_avaliacao']);
    	$avaliacao  = $this->avaliacao_model->get_avaliacao($row['cd_avaliacao_usuario']);

    	if(trim($row['dt_encerramento']) == '')
        {
            if(count($row_status) > 0)
            {
                $row['cd_avaliacao_usuario_avaliacao'] = $row_status['cd_avaliacao_usuario_avaliacao'];
                $row['ds_avaliacao']                   = $row_status['ds_status'];
                $row['tp_avaliacao']                   = $row_status['tp_avaliacao'];
            }
            else
            {
                $row['cd_avaliacao_usuario_avaliacao'] = 0;
                $row['ds_avaliacao']                   = 'Autoavaliação';
                $row['tp_avaliacao']                   = 'PRI';
            }
            
        }
        else
        {
            $row['cd_avaliacao_usuario_avaliacao'] = $row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'];
            $row['ds_avaliacao']                   = 'Finalizado';
            $row['tp_avaliacao']                   = 'QUA';
        }

        $this->load->plugin('fpdf');

        $fpdf = new PDF('P', 'mm', 'A4');
        $fpdf->AddFont('segoeuil');
        $fpdf->AddFont('segoeuib');       
        $fpdf->SetNrPag(true);
        $fpdf->SetMargins(10, 14, 5);
        $fpdf->header_exibe = true;
        $fpdf->header_logo = true;
        $fpdf->header_titulo = true;
        $fpdf->header_titulo_texto = 'Formulário Avaliação';
        $fpdf->AddPage();

        $altura_linha = 5;

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Período:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['nr_ano_avaliacao']);

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Avaliado:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['ds_avaliado']);  

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Admissão:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['dt_admissao']);  

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Cargo/Área de Atuação:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['ds_cargo_area_atuacao']);  

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Classe/Padrão:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['ds_classe'].(trim($row['ds_padrao']) != '' ? ' - '.$row['ds_padrao'] : ''));  

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Avaliador:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['ds_avaliador']);  

        $fpdf->SetY($fpdf->GetY() + 1);
        $fpdf->SetFont('segoeuib', '', 12);
        $fpdf->MultiCell(190, $altura_linha, 'Avaliação:');
        $fpdf->SetFont('segoeuil', '', 12);
        $fpdf->MultiCell(190, $altura_linha, $row['ds_avaliacao']); 

        if(count($resultado) > 0)
        {
        	$fpdf->SetY($fpdf->GetY() + 1);
	        $fpdf->SetFont('segoeuib', '', 12);
	        $fpdf->MultiCell(190, $altura_linha, 'Resultado:');
	        $fpdf->SetFont('segoeuil', '', 12);
	        $fpdf->MultiCell(190, $altura_linha, $resultado[0]['cd_matriz'].$resultado[1]['cd_matriz']); 
        }

        if(count($avaliacao) > 0 AND trim($avaliacao[0]['dt_encerramento']) != '')
        {
        	$fpdf->SetY($fpdf->GetY() + 1);
        	$fpdf->MultiCell(190, $altura_linha, ''); 

        	$resultado_avaliacao = array();

        	$fpdf->SetY($fpdf->GetY() + 1);
			$fpdf->SetFont('segoeuib', '', 10);
			$fpdf->SetAligns(array('C', 'C'));
			$fpdf->SetWidths(array(95, 95));
	        $fpdf->Row(array('Avaliação', 'Resultado'));
	        $fpdf->SetAligns(array('L', 'C'));
	        $fpdf->SetFont('segoeuil', '', 10);

        	foreach ($avaliacao as $key => $item) 
	        {
	        	if(trim($item['dt_encerramento']) != '')
	        	{
	        		$resultado_avaliacao = $this->avaliacao_model->resultado($item['cd_avaliacao_usuario_avaliacao']);

	        		$resultado = $resultado_avaliacao[0]['cd_matriz'].$resultado_avaliacao[1]['cd_matriz'];

	        		$fpdf->Row(array($item['ds_avaliacao_usuario'], $resultado));
	        	}
	        }

	        $fpdf->SetY($fpdf->GetY() + 1);
        	$fpdf->MultiCell(190, $altura_linha, ''); 
        }

        foreach ($collection as $key => $item)
        {
        	if(trim($item['tp_grupo']) == 'FD')
        	{
        		$fpdf->AddPage();
        	}

        	$fpdf->SetY($fpdf->GetY() + 1);
        	$fpdf->SetFont('segoeuib', '', 14);
        	$fpdf->MultiCell(190, $altura_linha + 5, $item['ds_grupo'], '0', 'C');

        	$peformance = $this->avaliacao_model->get_formulario_peformance($row['cd_avaliacao'], $item['tp_grupo']);

        	$fpdf->SetY($fpdf->GetY() + 1);
			$fpdf->SetFont('segoeuib', '', 10);
			$fpdf->SetAligns(array('C', 'C'));
			$fpdf->SetWidths(array(50, 140));
	        $fpdf->Row(array('Conceito', 'Descrição'));
	        $fpdf->SetAligns(array('L', 'L'));
	        $fpdf->SetFont('segoeuil', '', 10);

        	foreach ($peformance as $key => $item2)
        	{
		        $fpdf->Row(array($item2['ds_performance'], $item2['ds_performance_descricao']));
        	}

        	$blocos = $this->avaliacao_model->get_formulario_bloco_usuario($row['cd_avaliacao_usuario'], $item['tp_grupo']);

        	$nr_total_blocos = count($blocos);

        	$contador = 1;

        	foreach ($blocos as $key2 => $bloco)
            {
            	$fpdf->SetY($fpdf->GetY() + 1);
		        $fpdf->SetFont('segoeuib', '', 14);
		        $fpdf->MultiCell(190, $altura_linha + 5, $bloco['ds_bloco'], '0', 'C');

		        if(trim($bloco['ds_bloco_descricao']) != '')
		        {
		        	$fpdf->SetY($fpdf->GetY() + 1);
		        	$fpdf->SetFont('segoeuib', '', 12);
		        	$fpdf->MultiCell(190, $altura_linha, $bloco['ds_bloco_descricao'], '0', 'J');
		        }

		        $fpdf->SetY($fpdf->GetY() + 1);
				$fpdf->SetFont('segoeuil', '', 10);
				$fpdf->SetAligns(array('L', 'C', 'C', 'C', 'C'));
				$fpdf->SetWidths(array(130, 15, 15, 15, 15));
		        $fpdf->Row(array('', 'AUT', 'SUP', 'COM', 'CON'));

            	foreach($this->avaliacao_model->get_formulario_pergunta_usuario($row['cd_avaliacao_usuario'], $bloco['cd_avaliacao_bloco']) as $key3 => $pergunta)
            	{
            		$pergunta_resposta       = array();
            		$justificativa_avaliacao = array();

            		$pergunta_resposta[] = $pergunta['ds_pergunta'];

            		$pergunta_resposta[] = '';
					$pergunta_resposta[] = '';
					$pergunta_resposta[] = '';
					$pergunta_resposta[] = '';

            		foreach ($avaliacao as $key4 => $avaliacao_usuario_avaliacao) 
            		{
	                    $resposta = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_resposta(
	                    	$pergunta['cd_avaliacao_bloco_pergunta'], 
	                    	$avaliacao_usuario_avaliacao['cd_avaliacao_usuario_avaliacao']
	                    );

	                    if(count($resposta) > 0)
	                    {
	                    	$pergunta_resposta[ $avaliacao_usuario_avaliacao['nr_ordem'] ] = $resposta['tp_resposta'];
	                    }     

	                    $justificativa = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_justificativa(
	                    	$bloco['cd_avaliacao_bloco'],
	                    	$avaliacao_usuario_avaliacao['cd_avaliacao_usuario_avaliacao']
	                    );

				        if(count($justificativa) > 0)
				        {
				            $justificativa_avaliacao[$key4]['ds_justificativa'] = $justificativa['ds_justificativa'];
				            $justificativa_avaliacao[$key4]['ds_avaliacao_usuario'] = $avaliacao_usuario_avaliacao['ds_avaliacao_usuario'];
				        }      
                	}

            		$fpdf->Row($pergunta_resposta);
            	}

        	    foreach ($justificativa_avaliacao as $item3)
        		{
        			if(trim($item3['ds_justificativa']) != '')
        			{
        				$fpdf->SetY($fpdf->GetY() + 1);
				        $fpdf->SetFont('segoeuil', '', 12);
				        $fpdf->MultiCell(190, $altura_linha, 'Justificativa ('.$item3['ds_avaliacao_usuario'].')');
				        $fpdf->SetFont('segoeuil', '', 10);
			        	$fpdf->MultiCell(190, $altura_linha, $item3['ds_justificativa'], '0', 'J');
        			}
        		}

            	if($fpdf->GetY() >= 220 AND $contador < $nr_total_blocos)
            	{
					$fpdf->AddPage();
            	}

            	$contador++;
            }
        }

        $fpdf->Output();
        exit;
    }

    public function capacitacao($cd_usuario_avaliacao, $cd_avaliacao_usuario_capacitacao = 0)
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

    	$data['avaliado'] = $this->avaliacao_model->get_formulario_usuario($cd_usuario_avaliacao);

    	if(intval($cd_avaliacao_usuario_capacitacao) == 0)
    	{
    		$data['row'] = array(
    			'cd_avaliacao_usuario_capacitacao' 		=> 0,
    			'cd_avaliacao_usuario_capacitacao_tipo' => 0,
    			'nr_pontuacao' 							=> ''
    		);
    	}
    	else
    	{
    		$data['row'] = $this->avaliacao_model->carrega_capacitacao($cd_avaliacao_usuario_capacitacao);
    	}

    	$data['collection'] = $this->avaliacao_model->listar_capacitacao($cd_usuario_avaliacao);

    	$this->load->view('cadastro/rh_avaliacao_abertura/capacitacao', $data);
    }

    public function salvar_capacitacao()
    {
    	$this->load->model('rh_avaliacao/avaliacao_model');

    	$cd_avaliacao_usuario_capacitacao = $this->input->post('cd_avaliacao_usuario_capacitacao', TRUE);

    	$args = array(
    		'cd_avaliacao' 							=> $this->input->post('cd_avaliacao', TRUE),
    		'cd_avaliacao_usuario' 					=> $this->input->post('cd_avaliacao_usuario', TRUE),
    		'cd_avaliacao_usuario_capacitacao_tipo' => $this->input->post('cd_avaliacao_usuario_capacitacao_tipo', TRUE),
    		'nr_pontuacao' 							=> app_decimal_para_db($this->input->post('nr_pontuacao', TRUE)),
    		'cd_usuario' 							=> $this->session->userdata('codigo')
    	);

    	if(intval($cd_avaliacao_usuario_capacitacao) == 0)
    	{
    		$this->avaliacao_model->salvar_capacitacao($args);
    	}
    	else
    	{
    		$this->avaliacao_model->atualizar_capacitacao($cd_avaliacao_usuario_capacitacao, $args);
    	}

    	redirect('cadastro/rh_avaliacao_abertura/capacitacao/'.$args['cd_avaliacao_usuario'], 'refresh');
    }

    public function relatorio_pdi($cd_avaliacao)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $data = array(
            'cd_avaliacao' => $cd_avaliacao,
            'row'          => $this->avaliacao_model->carrega($cd_avaliacao),
            'cargo'        => $this->avaliacao_model->get_avaliacao_usuario_cargo($cd_avaliacao),
            'gerencia'     => $this->avaliacao_model->get_gerencia(),
            'usuarios'     => $this->avaliacao_model->get_usuarios_avaliacao($cd_avaliacao),
            'competencia'  => $this->avaliacao_model->get_competencia($cd_avaliacao),
        );

        $this->load->view('cadastro/rh_avaliacao_abertura/relatorio_pdi', $data);
    }

    public function listar_relatorio_pdi()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao = $this->input->post('cd_avaliacao', TRUE);

        $args = array(
        	'cd_gerencia' 								  => $this->input->post('cd_gerencia', TRUE),
        	'cd_usuario' 								  => $this->input->post('cd_usuario', TRUE),
        	'ds_cargo' 									  => utf8_decode($this->input->post('ds_cargo', TRUE)),
        	'ds_avaliacao_usuario_plando_desenvolvimento' => utf8_decode($this->input->post('ds_avaliacao_usuario_plando_desenvolvimento', TRUE))
        );

        manter_filtros($args);

        $data['collection'] = $this->avaliacao_model->listar_relatorio_pdi($cd_avaliacao, $args);

        $this->load->view('cadastro/rh_avaliacao_abertura/relatorio_pdi_result', $data);
    }
}