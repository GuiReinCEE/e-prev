<?php
class Responsavel extends Controller
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
			$this->load->model('indicador/indicador_administrador_model');

			$data = array(
				'gerencia' => $this->indicador_administrador_model->get_gerencia(),
				'grupo'    => $this->indicador_administrador_model->indicador_grupo(),
			);

			$this->load->view('indicador/responsavel/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('indicador/indicador_administrador_model');
		
		$args = array(
			'cd_gerencia'        => $this->input->post('cd_gerencia', TRUE),
			'cd_indicador_grupo' => $this->input->post('cd_indicador_grupo', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->indicador_administrador_model->listar($args);
		
		foreach($data['collection'] as $key => $item)
		{
			$grupo = $this->indicador_administrador_model->administrador_indicador_grupo($item['cd_indicador_administrador']);

			$data['collection'][$key]['grupo'] = array();

			foreach($grupo as $item2)
			{				
				$data['collection'][$key]['grupo'][] = $item2['ds_indicador_grupo'];
			}
		}
		
		$this->load->view('indicador/responsavel/index_result', $data);
    }

	public function cadastro($cd_indicador_administrador = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador/indicador_administrador_model');

			$data = array(
				'gerencia' => $this->indicador_administrador_model->get_gerencia(),
				'grupo'    => $this->indicador_administrador_model->indicador_grupo(),
			);

			if(intval($cd_indicador_administrador) == 0)
            {
            	$data['row'] = array(
            		'cd_indicador_administrador' => intval($cd_indicador_administrador),
            		'cd_gerencia'                => '',
            		'cd_usuario'                 => '',
            		'ds_tipo'                    => ''
            	);

            	$data['usuario'] = array();

            	$data['administrador_indicador_grupo'] = array();
            }
            else
            {
            	$data['row'] = $this->indicador_administrador_model->carrega(intval($cd_indicador_administrador));

            	$data['usuario'] = $this->indicador_administrador_model->get_usuarios($data['row']['cd_gerencia'], $data['row']['cd_usuario']);

            	$grupo = $this->indicador_administrador_model->administrador_indicador_grupo($cd_indicador_administrador);

				$data['administrador_indicador_grupo'] = array();

				foreach($grupo as $item)
				{				
					$data['administrador_indicador_grupo'][] = $item['cd_indicador_grupo'];
				}
            }

            $this->load->view('indicador/responsavel/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }	
	}

	public function get_usuarios()
	{
		$this->load->model('indicador/indicador_administrador_model');

		$data = array();

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        foreach($this->indicador_administrador_model->get_usuarios($cd_gerencia) as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
	    echo json_encode($data);
	}

	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador/indicador_administrador_model');
			
			$cd_indicador_administrador = $this->input->post('cd_indicador_administrador', TRUE);

			$args = array(
				'cd_usuario'          => $this->input->post('cd_usuario',TRUE),
				'cd_usuario_inclusao' => $this->session->userdata('codigo')
			);

			$grupo = $this->input->post('grupo', TRUE);

        	if(!is_array($grupo))
			{
				$args['grupo'] = array();
			}
			else
			{
				$args['grupo'] = $grupo;
			}

			if(intval($cd_indicador_administrador) == 0)
    		{
        		$this->indicador_administrador_model->salvar($args);
            } 
    		else
    		{
    			$this->indicador_administrador_model->atualizar(intval($cd_indicador_administrador), $args);
    		}
		
			redirect('indicador/responsavel', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }	
	}
	
	public function excluir($cd_indicador_administrador = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador/indicador_administrador_model');
		
			$this->indicador_administrador_model->excluir(intval($cd_indicador_administrador), $this->session->userdata('codigo'));
			
			redirect('indicador/responsavel', 'refresh');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}
?>