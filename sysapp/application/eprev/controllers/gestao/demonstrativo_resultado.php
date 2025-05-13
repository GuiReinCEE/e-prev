<?php
class Demonstrativo_resultado extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    var $caminho = 'up/demonstrativo_resultado/';

    private function get_mes()
    {
    	return array(
    		'01' => 'Janeiro',
    		'02' => 'Fevereiro',
    		'03' => 'Março',
    		'04' => 'Abril',
    		'05' => 'Maio',
    		'06' => 'Junho',
    		'07' => 'Julho',
    		'08' => 'Agosto',
    		'09' => 'Setembro',
    		'10' => 'Outubro',
    		'11' => 'Novembro',
    		'12' => 'Dezembro'
    	);
    }
    

    private function get_permissao()
    {
    	if(gerencia_in(array('GC')))
        {
        	return true;
        }
        else
        {
        	return false;
        }
    }

    private function get_estrutura_ordenada($collection, $cd_demonstrativo_resultado_mes = 0)
    {
        $array = array();

        foreach ($collection as $key => $item) 
        {   
            $array[count($array)] = $item;

            $this->get_ordem_filho(
                $item['cd_demonstrativo_resultado'],
                $item['cd_demonstrativo_resultado_estrutura'],
                $cd_demonstrativo_resultado_mes,
                $array,
                0
            );
        }  

        return $array;
    }

    private function get_ordem_filho($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura, $cd_demonstrativo_resultado_mes = 0, &$array = array(), $nivel = 0)
    {
        $nivel++;
        
        if(intval($cd_demonstrativo_resultado_mes) == 0)
        {
            $collection = $this->demonstrativo_resultado_model->listar_estrutura(
                $cd_demonstrativo_resultado,
                $cd_demonstrativo_resultado_estrutura
            );
        }
        else
        {
            $collection = $this->demonstrativo_resultado_model->listar_estrutura_mes(
                $cd_demonstrativo_resultado,
                $cd_demonstrativo_resultado_mes,
                $cd_demonstrativo_resultado_estrutura
            );
        }
        
        $i = count($array);
        
        foreach($collection as $key => $item)
        { 
            $item['nivel'] = $nivel;
            
            $array[$i] = $item;
            $i++;

            $i = $this->get_ordem_filho(
                $cd_demonstrativo_resultado,
                $item['cd_demonstrativo_resultado_estrutura'],
                $cd_demonstrativo_resultado_mes,
                $array,
                $nivel
            );
        } 
           
        return $i;
    }

    public function get_usuarios()
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        echo json_encode($this->demonstrativo_resultado_model->get_usuario($cd_gerencia));
    }

    public function index()
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/demonstrativo_resultado_model');

			$data['ano'] = $this->demonstrativo_resultado_model->get_ano();

    		$this->load->view('gestao/demonstrativo_resultado/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('gestao/demonstrativo_resultado_model');

    	$args['nr_ano'] = $this->input->post('nr_ano', TRUE);

    	manter_filtros($args);
    	
    	$data = array(
    		'collection'      => $this->demonstrativo_resultado_model->listar($args),
    		'fl_usuario_tipo' => $this->session->userdata('tipo')
    	);

    	$this->load->view('gestao/demonstrativo_resultado/index_result', $data);
    }

    public function cadastro($cd_demonstrativo_resultado = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/demonstrativo_resultado_model');

			$data = array();
			
			if(intval($cd_demonstrativo_resultado) == 0)
			{
				$data['row'] = array(
					'cd_demonstrativo_resultado' => $cd_demonstrativo_resultado,
					'nr_ano'					 => date('Y')
                );
			}
			else
			{
				$data['row'] = $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado);
			}

			$this->load->view('gestao/demonstrativo_resultado/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar()
	{
		if($this->get_permissao())
    	{
        	$this->load->model('gestao/demonstrativo_resultado_model');
			
			$cd_demonstrativo_resultado = $this->input->post('cd_demonstrativo_resultado', TRUE);

			$args = array(
				'nr_ano'     => $this->input->post('nr_ano', TRUE),
				'cd_usuario' => $this->session->userdata('codigo')
            );

            if(intval($cd_demonstrativo_resultado) == 0)
			{
                $ano_referencia = $this->demonstrativo_resultado_model->get_ano_anterior();  

				$cd_demonstrativo_resultado = $this->demonstrativo_resultado_model->salvar($args);

                if(count($ano_referencia) > 0)
                {
                    $collection = $this->demonstrativo_resultado_model->listar_estrutura($ano_referencia['cd_demonstrativo_resultado']);
                    
                    foreach ($collection as $key => $item) 
                    {
                        $estrutura = array(
                            'cd_demonstrativo_resultado'                      => $cd_demonstrativo_resultado,
                            'ds_demonstrativo_resultado_estrutura'            => $item['ds_demonstrativo_resultado_estrutura'],
                            'cd_demonstrativo_resultado_estrutura_referencia' => $item['cd_demonstrativo_resultado_estrutura'],
                            'cd_demonstrativo_resultado_estrutura_pai'        => '',
                            'nr_ordem'                                        => $item['nr_ordem'],
                            'cd_demonstrativo_resultado_estrutura_tipo'       => $item['cd_demonstrativo_resultado_estrutura_tipo'],
                            'cd_gerencia'                                     => $item['cd_gerencia'],
                            'cd_usuario_responsavel'                          => $item['cd_usuario_responsavel'],
                            'cd_usuario_substituto'                           => $item['cd_usuario_substituto'],
                            'cd_usuario_desativado'                           => $item['cd_usuario_desativado'],
                            'dt_desativado'                                   => $item['dt_desativado'],
                            'cd_usuario'                                      => $this->session->userdata('codigo')
                        );  

                        $cd_demonstrativo_resultado_estrutura = $this->demonstrativo_resultado_model->salvar_estrutura($estrutura);
                    }
                        
                    $collection = $this->demonstrativo_resultado_model->listar_estrutura($cd_demonstrativo_resultado);

                    foreach ($collection as $key => $item) 
                    {
                    	$row = $this->demonstrativo_resultado_model->get_referencia_pai($item['cd_referencia']);

                    	if(count($row) > 0)
                    	{
	                        $this->demonstrativo_resultado_model->atualizar_estrutura_pai(
	                            $item['cd_demonstrativo_resultado_estrutura'], 
	                            $this->session->userdata('codigo'),
	                            intval($row['cd_demonstrativo_resultado_estrutura'])
	                        );
                    	}
                    }
                }        
            }
			
			redirect('gestao/demonstrativo_resultado/estrutura/'.$cd_demonstrativo_resultado, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function excluir($cd_demonstrativo_resultado)
	{
		if($this->get_permissao())
    	{
        	$this->load->model('gestao/demonstrativo_resultado_model');
			
			$this->demonstrativo_resultado_model->excluir($cd_demonstrativo_resultado, $this->session->userdata('codigo'));
			
			redirect('gestao/demonstrativo_resultado/index', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function estrutura($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura = 0)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('gestao/demonstrativo_resultado_model');

        	$data = array(
                'demonstrativo' => $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado),
                'gerencia'      => $this->demonstrativo_resultado_model->get_gerencia(),
                'tipo'          => $this->demonstrativo_resultado_model->get_tipo()
            );

            $estrutura_pai = array();

            $collection = $this->demonstrativo_resultado_model->listar_estrutura_pai($cd_demonstrativo_resultado);

            $data['collection'] = array();

            foreach($this->get_estrutura_ordenada($collection) as $key => $item)
            {
            	$data['collection'][$key] = $item;

                $ordem = '';

                $cd_estrutura_escape = $item['cd_demonstrativo_resultado_estrutura_pai'];

                while(intval($cd_estrutura_escape) > 0)
                {
                    $row = $this->demonstrativo_resultado_model->get_estrutura_ordem($cd_estrutura_escape);

                    $cd_estrutura_escape = $row['cd_demonstrativo_resultado_estrutura_pai'];

                    $ordem = $row['nr_ordem'].'.'.$ordem; 
                }

                $ordem .= $item['nr_ordem'];

                $data['collection'][$key]['nr_ordem'] = $ordem;

                if(intval($item['cd_demonstrativo_resultado_estrutura']) != intval($cd_demonstrativo_resultado_estrutura))
                {
                    $estrutura_pai[count($estrutura_pai)] = array(
                        'value' => $item['cd_demonstrativo_resultado_estrutura'],
                        'text'  => $ordem.' - '.$item['ds_demonstrativo_resultado_estrutura']
                    );
                } 
            }

            $data['estrutura_pai'] = $estrutura_pai;

            if(intval($cd_demonstrativo_resultado_estrutura) == 0)
            {
                $data['row'] = array(
                    'cd_demonstrativo_resultado'                      => intval($cd_demonstrativo_resultado),
                    'cd_demonstrativo_resultado_estrutura'            => intval($cd_demonstrativo_resultado_estrutura),
                    'ds_demonstrativo_resultado_estrutura'            => '',
                    'cd_demonstrativo_resultado_estrutura_pai'        => '',
                    'cd_demonstrativo_resultado_estrutura_referencia' => '',
                    'nr_ordem'                                        => '',
                    'cd_demonstrativo_resultado_estrutura_tipo'       => '',
                    'cd_gerencia'                                     => '',
                    'cd_usuario_responsavel'                          => '',
                    'cd_usuario_substituto'                           => ''

                );

                $data['usuario'] = array();
            }
            else
            {
            	$data['row'] = $this->demonstrativo_resultado_model->carrega_estrutura($cd_demonstrativo_resultado_estrutura);

            	$data['usuario'] = $this->demonstrativo_resultado_model->get_usuario($data['row']['cd_gerencia']);
            }

            $this->load->view('gestao/demonstrativo_resultado/estrutura', $data);
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
            $this->load->model('gestao/demonstrativo_resultado_model');
            
            $cd_demonstrativo_resultado           = $this->input->post('cd_demonstrativo_resultado', TRUE);
            $cd_demonstrativo_resultado_estrutura = $this->input->post('cd_demonstrativo_resultado_estrutura', TRUE);
            
            $args = array(
                'cd_demonstrativo_resultado'                      => $cd_demonstrativo_resultado,
                'ds_demonstrativo_resultado_estrutura'            => $this->input->post('ds_demonstrativo_resultado_estrutura', TRUE),
                'cd_demonstrativo_resultado_estrutura_pai'        => $this->input->post('cd_demonstrativo_resultado_estrutura_pai', TRUE),
                'cd_demonstrativo_resultado_estrutura_referencia' => $this->input->post('cd_demonstrativo_resultado_estrutura_referencia', TRUE),
                'nr_ordem'                                        => $this->input->post('nr_ordem', TRUE),
                'cd_demonstrativo_resultado_estrutura_tipo'       => $this->input->post('cd_demonstrativo_resultado_estrutura_tipo', TRUE),
                'cd_gerencia'                                     => $this->input->post('cd_gerencia', TRUE),
                'cd_usuario_responsavel'                          => $this->input->post('cd_usuario_responsavel', TRUE),
                'cd_usuario_substituto'                           => $this->input->post('cd_usuario_substituto', TRUE),
                'cd_usuario_desativado'                           => '',
                'dt_desativado'                                   => '',
                'cd_usuario'                                      => $this->session->userdata('codigo')
            );

            if(intval($cd_demonstrativo_resultado_estrutura) == 0)
            {
                $cd_demonstrativo_resultado_estrutura = $this->demonstrativo_resultado_model->salvar_estrutura($args);
            }
            else
            {
                $this->demonstrativo_resultado_model->atualizar_estrutura($cd_demonstrativo_resultado_estrutura, $args);
            }
            
            redirect('gestao/demonstrativo_resultado/estrutura/'.$cd_demonstrativo_resultado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    } 

    public function excluir_estrutura($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura)
	{
		if($this->get_permissao())
    	{
        	$this->load->model('gestao/demonstrativo_resultado_model');
			
			$this->demonstrativo_resultado_model->excluir_estrutura($cd_demonstrativo_resultado_estrutura, $this->session->userdata('codigo'));
			
			redirect('gestao/demonstrativo_resultado/estrutura/'.$cd_demonstrativo_resultado, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function desativar_estrutura($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura)
	{
		if($this->get_permissao())
    	{
        	$this->load->model('gestao/demonstrativo_resultado_model');
			
			$this->demonstrativo_resultado_model->desativar_estrutura($cd_demonstrativo_resultado_estrutura, $this->session->userdata('codigo'));
			
			redirect('gestao/demonstrativo_resultado/estrutura/'.$cd_demonstrativo_resultado, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function ativar_estrutura($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura)
	{
		if($this->get_permissao())
    	{
        	$this->load->model('gestao/demonstrativo_resultado_model');
			
			$this->demonstrativo_resultado_model->ativar_estrutura($cd_demonstrativo_resultado_estrutura, $this->session->userdata('codigo'));
			
			redirect('gestao/demonstrativo_resultado/estrutura/'.$cd_demonstrativo_resultado, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function meses($cd_demonstrativo_resultado)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('gestao/demonstrativo_resultado_model');

        	$data['demonstrativo'] = $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado);

        	$data['collection'] = array();

        	foreach ($this->get_mes() as $key => $item) 
            {
                $row = $this->demonstrativo_resultado_model->carrega_meses($cd_demonstrativo_resultado, $key); 

            	$data['collection'][$key] = array(
                    'ds_mes'                         => $item,
                    'cd_demonstrativo_resultado_mes' => (count($row) > 0 ? $row['cd_demonstrativo_resultado_mes'] : ''),
                    'dt_inclusao'                    => (count($row) > 0 ? $row['dt_inclusao'] : ''),
                    'dt_fechamento'                  => (count($row) > 0 ? $row['dt_fechamento'] : ''),
                    'qt_item'                        => (count($row) > 0 ? $row['qt_item'] : ''),
                    'qt_anexo'                       => (count($row) > 0 ? $row['qt_anexo'] : ''),
                    'arquivo'                        => (count($row) > 0 ? $row['arquivo'] : '')
                );
            }

            $this->load->view('gestao/demonstrativo_resultado/meses', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function liberar_mes($cd_demonstrativo_resultado, $cd_mes, $ano)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/demonstrativo_resultado_model'
            ));

            $cd_evento = 268;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'cd_demonstrativo_resultado' => $cd_demonstrativo_resultado,
                'dt_referencia'              => '01/'.$cd_mes.'/'.$ano,    
                'cd_usuario'                 => $this->session->userdata('codigo')
            );

            $cd_demonstrativo_resultado_mes = $this->demonstrativo_resultado_model->salvar_mes($args);

            $this->demonstrativo_resultado_model->salvar_estrutura_mes(
                $cd_demonstrativo_resultado, 
                $cd_demonstrativo_resultado_mes, 
                $this->session->userdata('codigo')
            );

            foreach ($this->demonstrativo_resultado_model->listar_responsavel($cd_demonstrativo_resultado_mes) as $key => $item) 
            {
                $this->enviar_email_mes('R', $cd_demonstrativo_resultado_mes, $cd_mes.'/'.$ano, $cd_evento, $email, $item);
            }

            foreach ($this->demonstrativo_resultado_model->listar_substituto($cd_demonstrativo_resultado_mes) as $key => $item) 
            {
                $this->enviar_email_mes('S', $cd_demonstrativo_resultado_mes, $cd_mes.'/'.$ano, $cd_evento, $email, $item);
            }

            redirect('gestao/demonstrativo_resultado/meses/'.$cd_demonstrativo_resultado, 'refresh');

        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function enviar_email_mes($fl_tipo = 'R', $cd_demonstrativo_resultado_mes, $ds_referencia, $cd_evento, $email, $usuario)
    {
        if($this->get_permissao())
        {
            $collection = $this->demonstrativo_resultado_model->listar_estrutura_mes_usuario(
                $cd_demonstrativo_resultado_mes, 
                $usuario['cd_usuario'],
                $fl_tipo
            );

            $para = strtolower($usuario['ds_usuario']).'@eletroceee.com.br';

            $documento = '';

            foreach ($collection as $key => $item) 
            {                    
                $documento .= $item['ds_demonstrativo_resultado_estrutura'].br();  
            }

            $tags = array('[DOCUMENTOS]', '[MES_ANO]', '[LINK]');

            $subs = array(
                $documento, 
                $ds_referencia, 
                site_url('gestao/demonstrativo_resultado/minhas')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Demonstrativo de Resultados',
                'assunto' => $email['assunto'],
                'para'    => $para,
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function estrutura_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/demonstrativo_resultado_model');

            $data = array(
                'demonstrativo'     => $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado),
                'demonstrativo_mes' => $this->demonstrativo_resultado_model->carrega_resultado_mes($cd_demonstrativo_resultado_mes),
                'collection'        => array()
            );

            $collection = $this->demonstrativo_resultado_model->listar_estrutura_mes_pai($cd_demonstrativo_resultado_mes);

            foreach($this->get_estrutura_ordenada($collection, $cd_demonstrativo_resultado_mes) as $key => $item)
            {
                $data['collection'][$key] = $item;

                $ordem = '';

                $cd_estrutura_escape = $item['cd_demonstrativo_resultado_estrutura_pai'];

                while(intval($cd_estrutura_escape) > 0)
                {
                    $row = $this->demonstrativo_resultado_model->get_estrutura_mes_ordem($cd_estrutura_escape, $cd_demonstrativo_resultado_mes);

                    $cd_estrutura_escape = $row['cd_demonstrativo_resultado_estrutura_pai'];

                    $ordem = $row['nr_ordem'].'.'.$ordem; 
                }

                $ordem .= $item['nr_ordem'];

                $data['collection'][$key]['nr_ordem'] = $ordem;
            }

            $this->load->view('gestao/demonstrativo_resultado/estrutura_mes', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas()
    {
        $this->load->view('gestao/demonstrativo_resultado/minhas'); 
    }

    public function minhas_listar()
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $args = array(
            'nr_ano'        => $this->input->post('nr_ano', TRUE),
            'nr_mes'        => $this->input->post('nr_mes', TRUE),
            'fl_fechamento' => $this->input->post('fl_fechamento', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->demonstrativo_resultado_model->listar_minhas($this->session->userdata('codigo'), $args);

        $this->load->view('gestao/demonstrativo_resultado/minhas_result', $data);
    }

    public function anexo($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_demonstrativo_resultado_estrutura_mes = 0)
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $data = array(
            'demonstrativo'               => $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado),
            'demonstrativo_mes'           => $this->demonstrativo_resultado_model->carrega_resultado_mes($cd_demonstrativo_resultado_mes),
            'demonstrativo_resultado_mes' => array(),
            'nr_ordem'                    => 0,
            'estrutura'                   => array(),
            'collection'                  => array(),
            'fl_fechado'                  => FALSE,
            'arquivo_nome'                => ''
        );

        $collection = $this->demonstrativo_resultado_model->listar_estrutura_mes_pai($cd_demonstrativo_resultado_mes);

        foreach($this->get_estrutura_ordenada($collection, $cd_demonstrativo_resultado_mes) as $key => $item)
        {
            if(intval($item['cd_demonstrativo_resultado_estrutura_tipo']) != 2)
            {
                $ordem = '';

                $cd_estrutura_escape = $item['cd_demonstrativo_resultado_estrutura_pai'];

                while(intval($cd_estrutura_escape) > 0)
                {
                    $row = $this->demonstrativo_resultado_model->get_estrutura_mes_ordem($cd_estrutura_escape, $cd_demonstrativo_resultado_mes);

                    $cd_estrutura_escape = $row['cd_demonstrativo_resultado_estrutura_pai'];

                    $ordem = $row['nr_ordem'].'.'.$ordem; 
                }

                $ordem .= $item['nr_ordem'];

                $data['estrutura'][] = array(
                    'value' => $item['cd_demonstrativo_resultado_estrutura_mes'],
                    'text'  => $ordem.' - '.$item['ds_demonstrativo_resultado_estrutura']
                );

                if((intval($cd_demonstrativo_resultado_estrutura_mes) > 0) AND (intval($cd_demonstrativo_resultado_estrutura_mes) == intval($item['cd_demonstrativo_resultado_estrutura_mes'])))
                {
                    $data['arquivo_nome'] = $ordem.' - '.$item['ds_demonstrativo_resultado_estrutura'];
                }
            }
        }

        if(intval($cd_demonstrativo_resultado_estrutura_mes) > 0)
        {
            $data['demonstrativo_resultado_mes'] = $this->demonstrativo_resultado_model->carrega_estrutura_mes($cd_demonstrativo_resultado_estrutura_mes);

            if(trim($data['demonstrativo_resultado_mes']['dt_fechamento']) != '')
            {
                $data['fl_fechado'] = TRUE;
            }

            $estrutura_anexo = $this->demonstrativo_resultado_model->get_ordem_estrutura_anexo($cd_demonstrativo_resultado_estrutura_mes);

            if(count($estrutura_anexo) > 0)
            {
                $data['nr_ordem'] = $estrutura_anexo['nr_ordem'];
            }

            $data['collection'] = $this->demonstrativo_resultado_model->listar_estrutura_anexo($cd_demonstrativo_resultado_estrutura_mes);
        }
        else
        {
            $data['demonstrativo_resultado_mes']['cd_demonstrativo_resultado_estrutura_mes'] = 0;
        }

        $this->load->view('gestao/demonstrativo_resultado/anexo', $data);
    }

    public function salvar_anexo()
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
        $nr_ordem   = intval($this->input->post('nr_ordem', TRUE));

        $cd_demonstrativo_resultado               = $this->input->post('cd_demonstrativo_resultado', TRUE);
        $cd_demonstrativo_resultado_mes           = $this->input->post('cd_demonstrativo_resultado_mes', TRUE);
        $cd_demonstrativo_resultado_estrutura_mes = $this->input->post('cd_demonstrativo_resultado_estrutura_mes', TRUE);

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $nr_ordem ++;

                $args = array(
                    'cd_demonstrativo_resultado_estrutura_mes' => $cd_demonstrativo_resultado_estrutura_mes,
                    'nr_ordem'                                 => $nr_ordem,
                    'arquivo'                                  => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
                    'arquivo_nome'                             => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
                    'cd_usuario'                               => $this->session->userdata('codigo')
                );
          
                $this->demonstrativo_resultado_model->salvar_estrutura_anexo($args);
                
                $nr_conta++;
            }
        }   
        
        redirect('gestao/demonstrativo_resultado/anexo/'.intval($cd_demonstrativo_resultado).'/'.intval($cd_demonstrativo_resultado_mes).'/'.intval($cd_demonstrativo_resultado_estrutura_mes), 'refresh');
    }

    public function excluir_anexo($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_demonstrativo_resultado_estrutura_mes, $cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $this->demonstrativo_resultado_model->excluir_anexo($cd_demonstrativo_resultado_estrutura_mes_anexo, $this->session->userdata('codigo'));

        redirect('gestao/demonstrativo_resultado/anexo/'.intval($cd_demonstrativo_resultado).'/'.intval($cd_demonstrativo_resultado_mes).'/'.intval($cd_demonstrativo_resultado_estrutura_mes), 'refresh');
    }

    public function alterar_ordem($cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $this->demonstrativo_resultado_model->alterar_ordem(
            $cd_demonstrativo_resultado_estrutura_mes_anexo, 
            $this->input->post('nr_ordem', TRUE),
            $this->session->userdata('codigo')
        );
    }

    public function fechar_estrutura_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_demonstrativo_resultado_estrutura_mes)
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $collection = $this->demonstrativo_resultado_model->listar_estrutura_anexo($cd_demonstrativo_resultado_estrutura_mes);

        $demonstrativo               = $this->demonstrativo_resultado_model->carrega($cd_demonstrativo_resultado);
        $demonstrativo_mes           = $this->demonstrativo_resultado_model->carrega_resultado_mes($cd_demonstrativo_resultado_mes);
        $demonstrativo_resultado_mes = $this->demonstrativo_resultado_model->carrega_estrutura_mes($cd_demonstrativo_resultado_estrutura_mes);

        if(count($collection) > 1)
        {
            $this->load->plugin('PDFMerger');

            $pdf = new PDFMerger_pi;

            $arquivo = strtoupper(mes_format($demonstrativo_mes['cd_mes'], 'mmm')).$demonstrativo['nr_ano'].'_'.$cd_demonstrativo_resultado_estrutura_mes.'.pdf';
        
            foreach ($collection as $key => $item) 
            {
                $files[$key] = $this->caminho.$item['arquivo'];
            }

            $pdf->addPDFArray($files)->merge('file', $this->caminho.$arquivo);

            $args = array(
                'arquivo'      => $arquivo,
                'arquivo_nome' => $arquivo,
                'cd_usuario'   => $this->session->userdata('codigo')
            );
        }
        else
        {
            $args = array(
                'arquivo'      => $collection[0]['arquivo'],
                'arquivo_nome' => $collection[0]['arquivo_nome'],
                'cd_usuario'   => $this->session->userdata('codigo')
            );
        }

        $this->demonstrativo_resultado_model->fechar_estrutura_mes($cd_demonstrativo_resultado_estrutura_mes, $args);

        redirect('gestao/demonstrativo_resultado/anexo/'.intval($cd_demonstrativo_resultado).'/'.intval($cd_demonstrativo_resultado_mes).'/'.intval($cd_demonstrativo_resultado_estrutura_mes), 'refresh');
    }

    public function abrir_item($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_demonstrativo_resultado_estrutura_mes)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/demonstrativo_resultado_model');

            $this->demonstrativo_resultado_model->abrir_item($cd_demonstrativo_resultado_estrutura_mes, $this->session->userdata('codigo'));
            
            redirect('gestao/demonstrativo_resultado/estrutura_mes/'.intval($cd_demonstrativo_resultado).'/'.intval($cd_demonstrativo_resultado_mes), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function fechar_resultado_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/demonstrativo_resultado_model'
            ));

            $cd_evento = 273;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $arquivo = $this->pdf($cd_demonstrativo_resultado_mes);

            $args = array(
                'arquivo'      => $arquivo,
                'arquivo_nome' => $arquivo,
                'cd_usuario'   => $this->session->userdata('codigo')
            );

            $this->demonstrativo_resultado_model->fechar_resultado_mes($cd_demonstrativo_resultado_mes, $args);

            $row = $this->demonstrativo_resultado_model->carrega_resultado_mes($cd_demonstrativo_resultado_mes);

            $tags = array('[MES_ANO]', '[LINK]');

            $subs = array(
                $row['cd_mes'].'/'.$row['ano'], 
                site_url('gestao/demonstrativo_resultado/consulta')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Demonstrativo de Resultados',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('gestao/demonstrativo_resultado/meses/'.$cd_demonstrativo_resultado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function reabrir_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/demonstrativo_resultado_model');

            $this->demonstrativo_resultado_model->reabrir_mes($cd_demonstrativo_resultado_mes, $this->session->userdata('codigo'));

            redirect('gestao/demonstrativo_resultado/meses/'.$cd_demonstrativo_resultado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function pdf($cd_demonstrativo_resultado_mes)
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $this->load->plugin('PDFMerger');

        $row = $this->demonstrativo_resultado_model->carrega_resultado_mes($cd_demonstrativo_resultado_mes);

        $collection = $this->demonstrativo_resultado_model->listar_estrutura_mes_pai($cd_demonstrativo_resultado_mes);

        $array = $this->get_estrutura_ordenada($collection, $cd_demonstrativo_resultado_mes);
        
        $pagina = array();

        $pagina[0] = $this->caminho.$this->cria_capa($row);

        $pagina_deletar[] = $pagina[0];

        $i = 2;

        $nr_page = 2;

        $indice = array();

        foreach ($array as $key => $item) 
        {
            if(trim($item['cd_demonstrativo_resultado_estrutura_tipo']) == 2)
            {
                $nr_page = $nr_page + 1;

                $pagina[$i] = $this->caminho.$this->cria_sub_capa($item);

                $pagina_deletar[] = $pagina[$i];

                $indice[] = array(
                    'ds_estrutura' => $item['nr_ordem'].'. '.$item['ds_demonstrativo_resultado_estrutura'],
                    'nr_page'      => $nr_page
                );
            }
            else
            {
                $pdf = new PDFMerger_pi;

                $fpdi = $pdf->fpdi;

                $qt_page = $fpdi->setSourceFile($this->caminho.$item['arquivo']);

                $nr_page = $nr_page + $qt_page;

                $pagina[$i] = $this->caminho.$item['arquivo'];

                unset($pdf);
            }

            $i++;
        }

        $name_file = $this->cria_indice($indice, $cd_demonstrativo_resultado_mes);

        $pagina[1] = $this->caminho.$name_file;

        $pagina_deletar[] = $pagina[1];

        ksort($pagina);
  
        $pdf = new PDFMerger_pi;

        $demonstrativo = $this->caminho.strtoupper(mes_format($row['cd_mes'], 'mmm')).$row['ano'].'.pdf';

        $pdf->addPDFArray($pagina)->merge('file', $demonstrativo);

        foreach ($pagina_deletar as $key => $item) 
        {
            unlink($item);
        }

        $this->set_rodape($demonstrativo, 'Acompanhamento dos Resultados - '.$row['cd_mes'].'/'.$row['ano']);

        return strtoupper(mes_format($row['cd_mes'], 'mmm')).$row['ano'].'.pdf';

    }

    private function cria_capa($row)
    {
        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $fpdi->AddPage(); 

        $fpdi->AddFont('segoeuil');
        $fpdi->AddFont('segoeuib');
        $fpdi->SetMargins(10, 12, 5);

        $fpdi->SetY($fpdi->GetY() + 80);
        $fpdi->SetFont('segoeuib', '', 40);
        $fpdi->MultiCell(190, 20, 'Acompanhamento'."\n".'dos'."\n".'Resultados', '0','C');

        $fpdi->SetY($fpdi->GetY() + 40);
        $fpdi->SetFont('segoeuib', '', 14);
        $fpdi->MultiCell(190, 30, mes_extenso($row['cd_mes']).'/'.$row['ano'], '0','C');

        $fpdi->SetY($fpdi->GetY() + 20);
        $fpdi->SetFont('segoeuib', '', 12);
        
        $fpdi->MultiCell(190, 8, 'Consolidado pela Gerência de Controladoria', '0','L');
        $fpdi->SetFont('segoeuil', '', 12);
        $fpdi->MultiCell(190, 8, 'Tel: (051) 3027-3141 - gc@eletroceee.com.br', '0','L');

        $name_file = 'capa_'.$row['cd_demonstrativo_resultado_mes'].'.pdf';

        $fpdi->Output($this->caminho.$name_file , 'F');

        unset($pdf);

        return $name_file;
    }

    private function cria_sub_capa($row)
    {
        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $fpdi->AddPage(); 

        $fpdi->AddFont('segoeuil');
        $fpdi->AddFont('segoeuib');
        $fpdi->SetMargins(10, 12, 5);

        $fpdi->SetY($fpdi->GetY() + 80);
        $fpdi->SetFont('segoeuib', '', 40);
        $fpdi->MultiCell(190, 20, $row['ds_demonstrativo_resultado_estrutura'], '0','C');

        $name_file = 'capa_'.$row['cd_demonstrativo_resultado_estrutura_mes'].'_'.$row['cd_demonstrativo_resultado_estrutura'].'.pdf';

        $fpdi->Output($this->caminho.$name_file, 'F');

        unset($pdf);

        return $name_file;
    }

    private function cria_indice($indice, $cd_demonstrativo_resultado_mes)
    {
        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $fpdi->AddPage(); 

        $fpdi->AddFont('segoeuil');
        $fpdi->AddFont('segoeuib');
        $fpdi->SetMargins(10, 12, 5);

        $fpdi->SetY($fpdi->GetY() + 10);
        $fpdi->SetFont('segoeuib', '', 18);
        $fpdi->MultiCell(190, 20, 'Índice', '0','C');

        $fpdi->SetFont('segoeuib', '', 14);

        foreach ($indice as $key => $item) 
        {
           $fpdi->MultiCell(190, 10, $item['ds_estrutura'], '0', 'L');
           $fpdi->SetY($fpdi->GetY() - 8);
           $fpdi->MultiCell(190, 10, '___________________________________________________________________________________________', '0', 'L');
           $fpdi->SetY($fpdi->GetY() - 10);
           $fpdi->MultiCell(188, 10, $item['nr_page'], '0', 'R');
        }

        $name_file = 'indice_'.$cd_demonstrativo_resultado_mes.'.pdf';

        $fpdi->Output($this->caminho.$name_file, 'F');

        unset($pdf);

        return $name_file;
    }

    private function set_rodape($file, $texto)
    {
        $pdf = new PDFMerger_pi;

        $fpdi = $pdf->fpdi;

        $fpdi->SetMargins(10, 12, 5); 

        $pagecount = $fpdi->setSourceFile($file);

        for($i = 1; $i <= $pagecount; $i++)
        {
            $tplidx = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($tplidx);

            $fpdi->addPage($size['h'] > $size['w'] ? 'P' : 'L', array($size['w'], $size['h']));
            $fpdi->useTemplate($tplidx);

            $x = ((35 * $size['w']) / 100);
            $y = ((97.5 * $size['h']) / 100);

            $fpdi->SetTextColor(128,128,128);
            $fpdi->SetFont('Times','B',10);
            $fpdi->SetFont('Times','BI',8);
            $fpdi->Text($x, $y, $texto.' - '. $i.' de '.$pagecount);
        }

        $fpdi->Output($file , 'F');
    }

    public function consulta()
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $data['ano'] = $this->demonstrativo_resultado_model->get_ano_demostrativo_fechado();

        $this->load->view('gestao/demonstrativo_resultado/consulta', $data);
    }

    public function consulta_listar()
    {
        $this->load->model('gestao/demonstrativo_resultado_model');

        $args['nr_ano'] = $this->input->post('nr_ano', TRUE);

        manter_filtros($args);
        
        $data['collection'] = $this->demonstrativo_resultado_model->consulta_listar($args);

        $this->load->view('gestao/demonstrativo_resultado/consulta_result', $data);
    }
}