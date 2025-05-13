<?php
class Indisp_sistemas extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
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
			$this->load->model('informatica/indisp_sistemas_model');

            $data = array();
            
            //$data['tipo_dominio'] = $this->dominio_model->tipo_dominio();

            $this->load->view('servico/indisp_sistemas/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('informatica/indisp_sistemas_model');

    	$args = array(
            //'dt_dominio_renovacao_ini' => $this->input->post('dt_dominio_renovacao_ini', TRUE),
    		//'dt_dominio_renovacao_fim' => $this->input->post('dt_dominio_renovacao_fim', TRUE),
            //'cd_dominio_tipo'          => $this->input->post('cd_dominio_tipo', TRUE)
    	);
				
		manter_filtros($args);

		$data['collection'] = $this->indisp_sistemas_model->listar($args);

        $this->load->view('servico/indisp_sistemas/index_result', $data);
    }

    public function cadastro($cd_indisp_sistemas = 0, $cd_indisp_sistemas_ocorrencia = 0)
    {
    	if($this->get_permissao())
        {
            $this->load->model('informatica/indisp_sistemas_model');

	    	if(intval($cd_indisp_sistemas) == 0)
			{
				$data['row'] = array(
					'cd_indisp_sistemas' => intval($cd_indisp_sistemas),
                    'dt_indisp_sistemas' => '',
                    'nr_mes'             => '',
					'nr_ano'             => '',
					'ds_indisp_sistemas' => '',
					'nr_dias'            => ''
				);
			}
			else
			{
				$data['row'] = $this->indisp_sistemas_model->carrega($cd_indisp_sistemas);
                
                $data['ocorrencias_com_energia'] = $this->indisp_sistemas_model->listar_ocorrencia($cd_indisp_sistemas, 'S');
                $data['ocorrencias_sem_energia'] = $this->indisp_sistemas_model->listar_ocorrencia($cd_indisp_sistemas, 'N');

                $data['tipo'] = $this->indisp_sistemas_model->get_tipo();

                if(intval($cd_indisp_sistemas_ocorrencia) == 0)
                {
                    $data['ocorrencia'] = array(
                        'cd_indisp_sistemas_ocorrencia' => $cd_indisp_sistemas_ocorrencia,
                        'cd_indisp_sistemas_tipo'       => '',
                        'dt_indisp_sistemas_ocorrencia' => '',
                        'nr_minuto'                     => '',
                        'fl_energia'                    => '',
                        'ds_indisp_sistemas_ocorrencia' => ''
                    );
                }
                else
                {
                    $data['ocorrencia'] = $this->indisp_sistemas_model->carrega_ocorrencia($cd_indisp_sistemas_ocorrencia);
                }

                $data['resultado'] = $this->get_resultado($cd_indisp_sistemas);
			}

			$this->load->view('servico/indisp_sistemas/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
    	$this->load->model('informatica/indisp_sistemas_model');

    	$cd_indisp_sistemas = $this->input->post('cd_indisp_sistemas', TRUE);

    	$args = array(
    		'dt_indisp_sistemas' => '01/'.$this->input->post('nr_mes', TRUE).'/'.$this->input->post('nr_ano', TRUE),
    		'nr_dias'            => $this->input->post('nr_dias', TRUE),
    		'cd_usuario'         => $this->session->userdata('codigo')
    	);

    	if(intval($cd_indisp_sistemas) == 0)
		{
    		$cd_indisp_sistemas = $this->indisp_sistemas_model->salvar($args);
		} 
		else
		{
			$this->indisp_sistemas_model->atualizar(intval($cd_indisp_sistemas), $args);
		}

		redirect('servico/indisp_sistemas/cadastro/'.intval($cd_indisp_sistemas), 'refresh');
    }

    public function salvar_ocorrencia()
    {
        $this->load->model('informatica/indisp_sistemas_model');

        $cd_indisp_sistemas            = $this->input->post('cd_indisp_sistemas', TRUE);
        $cd_indisp_sistemas_ocorrencia = $this->input->post('cd_indisp_sistemas_ocorrencia', TRUE);

        $args = array(
            'cd_indisp_sistemas'            => $cd_indisp_sistemas,
            'dt_indisp_sistemas_ocorrencia' => $this->input->post('dt_indisp_sistemas_ocorrencia', TRUE),
            'cd_indisp_sistemas_tipo'       => $this->input->post('cd_indisp_sistemas_tipo', TRUE),
            'fl_energia'                    => $this->input->post('fl_energia', TRUE),
            'nr_minuto'                     => $this->input->post('nr_minuto', TRUE),
            'ds_indisp_sistemas_ocorrencia' => $this->input->post('ds_indisp_sistemas_ocorrencia', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );

        if(intval($cd_indisp_sistemas_ocorrencia) == 0)
        {
            $this->indisp_sistemas_model->salvar_ocorrencia($args);
        } 
        else
        {
            $this->indisp_sistemas_model->atualizar_ocorrencia(intval($cd_indisp_sistemas_ocorrencia), $args);
        }

        redirect('servico/indisp_sistemas/cadastro/'.intval($cd_indisp_sistemas), 'refresh');
    }

    public function excluir_ocorrencia($cd_indisp_sistemas, $cd_indisp_sistemas_ocorrencia)
    {
        $this->load->model('informatica/indisp_sistemas_model');

        $this->indisp_sistemas_model->excluir_ocorrencia(intval($cd_indisp_sistemas_ocorrencia), $this->session->userdata('codigo'));

        redirect('servico/indisp_sistemas/cadastro/'.intval($cd_indisp_sistemas), 'refresh');
    }

    public function resultado($cd_indisp_sistemas)
    {
        $this->load->model('informatica/indisp_sistemas_model');

        $data['row']       = $this->indisp_sistemas_model->carrega($cd_indisp_sistemas);
        $data['resultado'] = $this->get_resultado($cd_indisp_sistemas);

        $this->load->view('servico/indisp_sistemas/resultado', $data);
    }

    public function get_resultado($cd_indisp_sistemas)
    {
        $this->load->model('informatica/indisp_sistemas_model');

        $row = $this->indisp_sistemas_model->carrega($cd_indisp_sistemas);

        $ocorrencias_com_energia = $this->indisp_sistemas_model->get_tipo_mes($cd_indisp_sistemas, 'S');
        $ocorrencias_sem_energia = $this->indisp_sistemas_model->get_tipo_mes($cd_indisp_sistemas, 'N');

        $nr_minuto_mes = intval($row['nr_dias']) * 10 * 60;

        $resultado_final_com_energia = 0;

        foreach ($ocorrencias_com_energia as $key => $item) 
        {
            $percentual = ($item['tl_minuto'] / $nr_minuto_mes) * 100;
            $resultado  = $percentual * $item['nr_peso'];

            $resultado_final_com_energia += $resultado;

            $ocorrencias_com_energia[$key]['percentual'] = $percentual;
            $ocorrencias_com_energia[$key]['resultado']  = $resultado;
        }

        $resultado_final_com_energia = $resultado_final_com_energia / count($ocorrencias_com_energia);

        $resultado_final_sem_energia = 0;

        foreach ($ocorrencias_sem_energia as $key => $item) 
        {
            $percentual = ($item['tl_minuto'] / $nr_minuto_mes) * 100;
            $resultado  = $percentual * $item['nr_peso'];

            $resultado_final_sem_energia += $resultado;

            $ocorrencias_sem_energia[$key]['percentual'] = $percentual;
            $ocorrencias_sem_energia[$key]['resultado']  = $resultado;
        }

        $resultado_final_sem_energia = $resultado_final_sem_energia / count($ocorrencias_sem_energia);

        $data = array(
            'nr_dias'                     => intval($row['nr_dias']),
            'nr_minuto_mes'               => $nr_minuto_mes,
            'ocorrencias_com_energia'     => $ocorrencias_com_energia,
            'resultado_final_com_energia' => $resultado_final_com_energia,
            'ocorrencias_sem_energia'     => $ocorrencias_sem_energia,
            'resultado_final_sem_energia' => $resultado_final_sem_energia,
        );

       return $data;    
    }
}
?>