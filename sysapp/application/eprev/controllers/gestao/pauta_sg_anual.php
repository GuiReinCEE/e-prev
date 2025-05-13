<?php
class Pauta_sg_anual extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_colegiado()
    {
    	return array(
    		array('value' => 'DE', 'text' => 'Diretoria Executiva'),
    		array('value' => 'CF', 'text' => 'Conselho Fiscal'),
    		array('value' => 'CD', 'text' => 'Conselho Deliberativo')
    	);
    }

    private function get_mes()
    {
    	return array(
    		array('value' => '',   'text' => 'Selecione'),
    		array('value' => '01', 'text' => 'Janeiro'),
    		array('value' => '02', 'text' => 'Fevereiro'),
    		array('value' => '03', 'text' => 'Março'),
    		array('value' => '04', 'text' => 'Abril'),
    		array('value' => '05', 'text' => 'Maio'),
    		array('value' => '06', 'text' => 'Junho'),
    		array('value' => '07', 'text' => 'Julho'),
    		array('value' => '08', 'text' => 'Agosto'),
    		array('value' => '09', 'text' => 'Setembro'),
    		array('value' => '10', 'text' => 'Outubro'),
    		array('value' => '11', 'text' => 'Novembro'),
    		array('value' => '12', 'text' => 'Dezembro')
    	);
    }

    public function index()
    {
    	if($this->get_permissao())
        {
	    	$data['colegiado'] = $this->get_colegiado();

	    	$this->load->view('gestao/pauta_sg_anual/index', $data);
    	}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('gestao/pauta_sg_anual_model');

    	$args = array(
            'fl_colegiado' => $this->input->post('fl_colegiado', TRUE),
            'nr_ano'       => $this->input->post('nr_ano', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->pauta_sg_anual_model->listar($args);

        $this->load->view('gestao/pauta_sg_anual/index_result', $data);
    }

    public function cadastro($cd_pauta_sg_anual = 0)
    { 
    	if($this->get_permissao())
        {
	    	$data['colegiado'] = $this->get_colegiado();

	    	if(intval($cd_pauta_sg_anual) == 0)
        	{
        		$data['row'] = array(
	                'cd_pauta_sg_anual' => intval($cd_pauta_sg_anual),
	                'nr_ano'            => '',
	                'fl_colegiado'      => '',
                    'dt_limite'         => ''
	            );
        	}
        	else
        	{
        		$this->load->model('gestao/pauta_sg_anual_model');

        		$data['row'] = $this->pauta_sg_anual_model->carrega($cd_pauta_sg_anual);
        	}

	    	$this->load->view('gestao/pauta_sg_anual/cadastro', $data);
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
            $this->load->model('gestao/pauta_sg_anual_model');

            $cd_pauta_sg_anual = $this->input->post('cd_pauta_sg_anual', TRUE);

            $args = array( 
                'nr_ano'         => $this->input->post('nr_ano',TRUE),
                'fl_colegiado'   => $this->input->post('fl_colegiado', TRUE),
                'dt_limite'      => $this->input->post('dt_limite', TRUE),
                'dt_confirmacao' => $this->input->post('dt_confirmacao', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')
            );

            if(intval($cd_pauta_sg_anual) == 0)
            {
                $cd_pauta_sg_anual = $this->pauta_sg_anual_model->salvar($args);
            }
            else
            {
                $this->pauta_sg_anual_model->atualizar($cd_pauta_sg_anual, $args);
            }

            redirect('gestao/pauta_sg_anual/cadastro/'.$cd_pauta_sg_anual, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_pauta_sg_anual)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'gestao/pauta_sg_anual_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 284;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $pauta = $this->pauta_sg_anual_model->carrega($cd_pauta_sg_anual);

            $tags = array('[NR_ANO]', '[DT_LIMITE]', '[LINK]');
            $subs = array(
                $pauta['nr_ano'], 
                $pauta['dt_limite'], 
                site_url('gestao/pauta_sg_anual/responder/'.$cd_pauta_sg_anual)
            );
            
            $texto = str_replace($tags, $subs, $email['email']);

            $collection = $this->pauta_sg_anual_model->get_email_reponsaveis($cd_pauta_sg_anual);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Pauta Anual',
                'assunto' => $email['assunto'],
                'para'    => '',
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            foreach ($collection as $key => $item) 
            {
                $args['para'] = trim($item['ds_email_responsavel']).';'.trim($item['ds_email_substituto']);

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

                $this->pauta_sg_anual_model->salvar_gerencia($cd_pauta_sg_anual, $item['cd_gerencia'], $cd_usuario);
            }

            $this->pauta_sg_anual_model->enviar($cd_pauta_sg_anual, $cd_usuario);

            redirect('gestao/pauta_sg_anual/cadastro/'.$cd_pauta_sg_anual, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function assunto($cd_pauta_sg_anual, $cd_pauta_sg_anual_assunto = 0)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('gestao/pauta_sg_anual_model');

        	$data = array(
        		'pauta_sg_anual' => $this->pauta_sg_anual_model->carrega($cd_pauta_sg_anual),
        		'mes'            => $this->get_mes(),
        		'gerencia'       => $this->pauta_sg_anual_model->get_gerencia(),
                'objetivo'       => $this->pauta_sg_anual_model->get_objetivo(),
                'justificativa'  => $this->pauta_sg_anual_model->get_justificativa(),
        		'collection'     => $this->pauta_sg_anual_model->listar_assunto($cd_pauta_sg_anual),
                'pauta_gerencia' => $this->pauta_sg_anual_model->listar_pauta_gerencia($cd_pauta_sg_anual)
        	);

        	if(intval($cd_pauta_sg_anual_assunto) == 0)
        	{
        		$data['row'] = array(
	                'cd_pauta_sg_anual'          => intval($cd_pauta_sg_anual),
	                'cd_pauta_sg_anual_assunto'  => intval($cd_pauta_sg_anual_assunto),
	                'mes'                        => '',
	                'ds_assunto'                 => '',
	                'cd_gerencia_responsavel'    => '',
                    'cd_pauta_sg_objetivo'       => '',
                    'cd_pauta_sg_justificativa'  => ''
	            );
        	}
        	else
        	{
        		$data['row'] = $this->pauta_sg_anual_model->carrega_assunto($cd_pauta_sg_anual_assunto);
        	}

        	$this->load->view('gestao/pauta_sg_anual/assunto', $data);
        }
        else
        {
        	exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_assunto()
    {
    	if($this->get_permissao())
        {
            $this->load->model('gestao/pauta_sg_anual_model');

            $cd_pauta_sg_anual         = $this->input->post('cd_pauta_sg_anual', TRUE);
            $cd_pauta_sg_anual_assunto = $this->input->post('cd_pauta_sg_anual_assunto', TRUE);

            $args = array( 
            	'cd_pauta_sg_anual'         => intval($cd_pauta_sg_anual),
                'dt_referencia'             => '01/'.$this->input->post('mes',TRUE).'/'.$this->input->post('nr_ano',TRUE),
                'ds_assunto'                => $this->input->post('ds_assunto', TRUE),
                'cd_gerencia_responsavel'   => $this->input->post('cd_gerencia_responsavel', TRUE),
                'cd_pauta_sg_objetivo'      => $this->input->post('cd_pauta_sg_objetivo', TRUE),
                'cd_pauta_sg_justificativa' => $this->input->post('cd_pauta_sg_justificativa', TRUE),
                'nr_tempo'                  => $this->input->post('nr_tempo', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );

            if(intval($cd_pauta_sg_anual_assunto) == 0)
            {
                $this->pauta_sg_anual_model->salvar_assunto($args);
            }
            else
            {
                $this->pauta_sg_anual_model->atualizar_assunto($cd_pauta_sg_anual_assunto, $args);
            }

            redirect('gestao/pauta_sg_anual/assunto/'.$cd_pauta_sg_anual, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas()
    {
    	if($this->session->userdata('tipo') == 'G' OR $this->session->userdata('indic_01') == 'S')
    	{
    		$data['colegiado'] = $this->get_colegiado();

    		$this->load->view('gestao/pauta_sg_anual/minhas', $data);
    	}
    	else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas_listar()
    {
    	$this->load->model('gestao/pauta_sg_anual_model');

    	$args = array(
            'fl_colegiado' => $this->input->post('fl_colegiado', TRUE),
            'nr_ano'       => $this->input->post('nr_ano', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->pauta_sg_anual_model->minhas_listar($this->session->userdata('divisao'), $args);

        $this->load->view('gestao/pauta_sg_anual/minhas_result', $data);
    }

    public function responder($cd_pauta_sg_anual, $cd_pauta_sg_anual_assunto = 0)
    {
    	if($this->session->userdata('tipo') == 'G' OR $this->session->userdata('indic_01') == 'S')
    	{
    		$this->load->model('gestao/pauta_sg_anual_model');

    		$data = array(
        		'pauta_sg_anual' => $this->pauta_sg_anual_model->carrega($cd_pauta_sg_anual, $this->session->userdata('divisao')),
                'mes'            => $this->get_mes(),
                'objetivo'       => $this->pauta_sg_anual_model->get_objetivo(),
                'justificativa'  => $this->pauta_sg_anual_model->get_justificativa(),
                'collection'     => $this->pauta_sg_anual_model->meus_assuntos($cd_pauta_sg_anual)
        	);

            if(intval($cd_pauta_sg_anual_assunto) == 0)
            {
                $data['row'] = array(
                    'cd_pauta_sg_anual'          => intval($cd_pauta_sg_anual),
                    'cd_pauta_sg_anual_assunto'  => intval($cd_pauta_sg_anual_assunto),
                    'mes'                        => '',
                    'ds_assunto'                 => '',
                    'cd_pauta_sg_objetivo'       => '',
                    'cd_pauta_sg_justificativa'  => ''
                );
            }
            else
            {
                $data['row'] = $this->pauta_sg_anual_model->carrega_assunto($cd_pauta_sg_anual_assunto);
            }

        	$this->load->view('gestao/pauta_sg_anual/responder', $data);
    	}
    	else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_responder_assunto()
    {
        if($this->session->userdata('tipo') == 'G' OR $this->session->userdata('indic_01') == 'S')
        {
            $this->load->model('gestao/pauta_sg_anual_model');

            $cd_pauta_sg_anual         = $this->input->post('cd_pauta_sg_anual', TRUE);
            $cd_pauta_sg_anual_assunto = $this->input->post('cd_pauta_sg_anual_assunto', TRUE);

            $args = array( 
                'cd_pauta_sg_anual'         => intval($cd_pauta_sg_anual),
                'dt_referencia'             => '01/'.$this->input->post('mes',TRUE).'/'.$this->input->post('nr_ano',TRUE),
                'ds_assunto'                => $this->input->post('ds_assunto', TRUE),
                'cd_gerencia_responsavel'   => $this->session->userdata('divisao'),
                'cd_pauta_sg_objetivo'      => $this->input->post('cd_pauta_sg_objetivo', TRUE),
                'cd_pauta_sg_justificativa' => $this->input->post('cd_pauta_sg_justificativa', TRUE),
                'nr_tempo'                  => $this->input->post('nr_tempo', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );

            if(intval($cd_pauta_sg_anual_assunto) == 0)
            {
                $this->pauta_sg_anual_model->salvar_assunto($args);
            }
            else
            {
                $this->pauta_sg_anual_model->atualizar_assunto($cd_pauta_sg_anual_assunto, $args);
            }

            redirect('gestao/pauta_sg_anual/responder/'.$cd_pauta_sg_anual, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function encerrar($cd_pauta_sg_anual)
    {
    	if($this->session->userdata('tipo') == 'G' OR $this->session->userdata('indic_01') == 'S')
    	{
    		$this->load->model(array(
            	'gestao/pauta_sg_anual_model',
            	'projetos/eventos_email_model'
            ));

            $cd_evento = 285;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $pauta_sg_anual = $this->pauta_sg_anual_model->carrega($cd_pauta_sg_anual);

            $tags = array('[CD_GERENCIA]', '[FL_COLEGIADO]', '[LINK]');
			$subs = array(
				$this->session->userdata('divisao'),
				$pauta_sg_anual['fl_colegiado'],
				site_url('gestao/pauta_sg_anual/assunto/'.intval($cd_pauta_sg_anual))
			);
			
			$texto = str_replace($tags, $subs, $email['email']);

			$cd_usuario = $this->session->userdata('codigo');

            $args = array(
				'de'      => 'Pauta Anual',
				'assunto' => $email['assunto'],
				'para'    => $email['para'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);

			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

    		$this->pauta_sg_anual_model->encerrar(
    			intval($cd_pauta_sg_anual),
    			$this->session->userdata('divisao'),
    			$cd_usuario
    		);

    		redirect('gestao/pauta_sg_anual/minhas', 'refresh');
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function excluir_assunto($cd_pauta_sg_anual, $cd_pauta_sg_anual_assunto, $fl_grc = 'N')
    {
        if(($this->session->userdata('tipo') == 'G' OR $this->session->userdata('indic_01') == 'S') OR (gerencia_in(array('GC'))))
        {
            $this->load->model('gestao/pauta_sg_anual_model');

            $this->pauta_sg_anual_model->excluir_assunto(
                intval($cd_pauta_sg_anual_assunto),
                $this->session->userdata('codigo')
            );

            if(trim($fl_grc) == 'S')
            {
                redirect('gestao/pauta_sg_anual/assunto/'.intval($cd_pauta_sg_anual), 'refresh');
            }
            else
            {
                redirect('gestao/pauta_sg_anual/responder/'.intval($cd_pauta_sg_anual), 'refresh');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}