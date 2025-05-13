<?php
class Solic_fiscalizacao_audit_tipo extends Controller
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

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solic_fiscalizacao_audit_tipo_model');

            $data['agrupamento'] = $this->solic_fiscalizacao_audit_tipo_model->get_agrupamento();

            $this->load->view('atividade/solic_fiscalizacao_audit_tipo/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_tipo_model');

        $args = array(
            'ds_solic_fiscalizacao_audit_tipo'             => $this->input->post('ds_solic_fiscalizacao_audit_tipo', TRUE),
            'cd_solic_fiscalizacao_audit_tipo_agrupamento' => $this->input->post('cd_solic_fiscalizacao_audit_tipo_agrupamento', TRUE),
            'fl_especificar'                               => $this->input->post('fl_especificar', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->solic_fiscalizacao_audit_tipo_model->listar($args);

        foreach($data['collection'] as $key => $item)
		{
			$data['collection'][$key]['gerencia'] = array();

			$tipo_gerencia = $this->solic_fiscalizacao_audit_tipo_model->get_tipo_gerencia(intval($item['cd_solic_fiscalizacao_audit_tipo']));

			foreach($tipo_gerencia as $gerencia)
			{				
				$data['collection'][$key]['gerencia'][] = $gerencia['cd_gerencia'];
			}
		}

        $this->load->view('atividade/solic_fiscalizacao_audit_tipo/index_result', $data);
    }

    public function cadastro($cd_solic_fiscalizacao_audit_tipo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solic_fiscalizacao_audit_tipo_model');

            $data = array(
                'gerencia'           => $this->solic_fiscalizacao_audit_tipo_model->get_gerencia(array('DIV', 'CON')),
                'agrupamento'        => $this->solic_fiscalizacao_audit_tipo_model->get_agrupamento(),
                'area_consolidadora' => $this->solic_fiscalizacao_audit_tipo_model->get_gerencia(),
                'tipo_gerencia'      => array()
            );

            if(intval($cd_solic_fiscalizacao_audit_tipo) == 0)
            {
                $data['row'] = array(
                    'cd_solic_fiscalizacao_audit_tipo'             => intval($cd_solic_fiscalizacao_audit_tipo),
                    'ds_solic_fiscalizacao_audit_tipo'             => '',
                    'cd_solic_fiscalizacao_audit_tipo_agrupamento' => '',
                    'cd_gerencia'                                  => '',
                    'fl_especificar'                               => 'N'
                );
            }
            else
            {
                $data['row'] = $this->solic_fiscalizacao_audit_tipo_model->carrega($cd_solic_fiscalizacao_audit_tipo);

                $tipo_gerencia = $this->solic_fiscalizacao_audit_tipo_model->get_tipo_gerencia(intval($cd_solic_fiscalizacao_audit_tipo));

				foreach($tipo_gerencia as $gerencia)
				{				
					$data['tipo_gerencia'][] = $gerencia['cd_gerencia'];
				}
            }

            $this->load->view('atividade/solic_fiscalizacao_audit_tipo/cadastro', $data);
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
            $this->load->model('projetos/solic_fiscalizacao_audit_tipo_model');

            $cd_solic_fiscalizacao_audit_tipo = $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE);

            $args = array( 
                'ds_solic_fiscalizacao_audit_tipo'             => $this->input->post('ds_solic_fiscalizacao_audit_tipo', TRUE),
                'cd_solic_fiscalizacao_audit_tipo_agrupamento' => $this->input->post('cd_solic_fiscalizacao_audit_tipo_agrupamento', TRUE),
                'cd_gerencia'                                  => $this->input->post('cd_gerencia', TRUE),
                'fl_especificar'                               => $this->input->post('fl_especificar', TRUE), 
                'cd_usuario'                                   => $this->session->userdata('codigo')
            );

            $tipo_gerencia = $this->input->post('tipo_gerencia', TRUE);

            if(!is_array($tipo_gerencia))
			{
				$args['tipo_gerencia'] = array();
			}
			else
			{
				$args['tipo_gerencia'] = $tipo_gerencia;
			}

            if(intval($cd_solic_fiscalizacao_audit_tipo) == 0)
            {
                $this->solic_fiscalizacao_audit_tipo_model->salvar($args);
            }
            else
            {
                $this->solic_fiscalizacao_audit_tipo_model->atualizar($cd_solic_fiscalizacao_audit_tipo, $args);
            }

            redirect('atividade/solic_fiscalizacao_audit_tipo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}