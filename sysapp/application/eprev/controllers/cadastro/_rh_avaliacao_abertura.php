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
            $data['collection'][$key]['ds_progressao'] = '';
            $data['collection'][$key]['ds_promocao']   = '';
            $data['collection'][$key]['fl_progressao'] = '';
            $data['collection'][$key]['fl_promocao']   = '';

            if(count($resultado) > 0)
            {
                $data['collection'][$key]['cd_matriz'] = $resultado[0]['cd_matriz'].$resultado[1]['cd_matriz'];

                $matriz_conceito = $this->avaliacao_model->get_matriz_conceito($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'], 'C');

                $cd_avaliacao_matriz_conceito_a = intval($matriz_conceito['cd_avaliacao_matriz_conceito']);

                $matriz_conceito = $this->avaliacao_model->get_matriz_conceito($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'], 'FD');

                $cd_avaliacao_matriz_conceito_b = intval($matriz_conceito['cd_avaliacao_matriz_conceito']);

                $promocao_progressao = $this->avaliacao_model->get_promocao_progressao($cd_avaliacao_matriz_conceito_a, $cd_avaliacao_matriz_conceito_b);

                $data['collection'][$key]['fl_progressao'] = $promocao_progressao['fl_progressao'];
                $data['collection'][$key]['fl_promocao']   = $promocao_progressao['fl_promocao'];

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
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE)
        );

        $data['matriz'] = $this->monta_matriz($cd_avaliacao, $args);

        $this->load->view('cadastro/rh_avaliacao_abertura/relatorio_result', $data);
    }

    private function monta_matriz($cd_avaliacao, $args = array())
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

            $avaliacao = $this->avaliacao_model->get_avaliacao_matriz($cd_avaliacao, trim($item['ds_matriz_conceito_a']), trim($item['ds_matriz_conceito_b']), $args);

            $matriz[$i][] = array(
                'cd_matriz'    => trim($item['ds_matriz_conceito_a']).trim($item['ds_matriz_conceito_b']),
                'ds_matriz'    => trim($item['ds_matriz_acao']),
                'cor_fundo'    => $item['cor_fundo'],
                'cor_texto'    => $item['cor_texto'],
                'nr_resultado' => count($avaliacao),
                'avaliacao'    => $avaliacao
            );
        }

        return $matriz;
    }
}