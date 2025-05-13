<?php
class Regulamento_revisao extends Controller
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

    private function monta_estrutura(&$estrutura = array(), $cd_regulamento_revisao = 0, $cd_regulamento_revisao_pai = 0, $ds_ordem = '', $nr_nivel = 0)
    {
        $collection = $this->regulamento_revisao_model->get_revisao(
            $cd_regulamento_revisao,
            $cd_regulamento_revisao_pai
        );

        $nr_nivel ++;

        $i = count($estrutura);

        foreach ($collection as $key => $item) 
        {
            $item['ds_ordem'] = (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'];

            $item['nr_nivel'] = $nr_nivel;

            $item['text'] = str_repeat('&nbsp', ($nr_nivel-1)*4).$item['ds_ordem'].' - '.$item['text'];

            $estrutura[$i] = $item;
    
            $i++;

            $i = $this->monta_estrutura(
                $estrutura,
                $cd_regulamento_revisao,
                $item['cd_regulamento_revisao'],                
                (trim($ds_ordem) != '' ? $ds_ordem.'.' : '').$item['nr_ordem'],
                $nr_nivel
            );
        }

        return $i;
    }

    public function index($cd_regulamento_revisao = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/regulamento_revisao_model');

            $this->monta_estrutura($data['collection']);

            $this->monta_estrutura($data['regulamento_revisao_pai'], $cd_regulamento_revisao);

            if(intval($cd_regulamento_revisao) == 0)
            {
                $row = $this->regulamento_revisao_model->get_next_ordem();

                $data['row'] = array(
                    'cd_regulamento_revisao'     => '',
                    'cd_regulamento_revisao_pai' => '',
                    'nr_ordem'                   => $row['nr_ordem'],
                    'ds_regulamento_revisao'     => '',
                    'ds_descricao'               => '',
                    'cd_usuario'                 => ''
                );
            }
            else
            {
                $data['row'] = $this->regulamento_revisao_model->carrega(intval($cd_regulamento_revisao));
            }

            $data['regulamento_revisao_tipo'] = $this->regulamento_revisao_model->get_regulamento_tipo();

            $data['regulamento_alteracao_revisao_tipo'] = array();

            foreach($this->regulamento_revisao_model->get_regulamento($cd_regulamento_revisao) as $item)
            {               
                $data['regulamento_alteracao_revisao_tipo'][] = $item['cd_regulamento_tipo'];
            }

            foreach ($data['collection'] as $key => $item) 
            {
                $data['collection'][$key]['regulamento_alteracao_revisao_tipo'] = array();

                foreach ($this->regulamento_revisao_model->get_regulamento($item['cd_regulamento_revisao']) as $key2 => $item2) 
                {
                    $data['collection'][$key]['regulamento_alteracao_revisao_tipo'][] = $item2['ds_regulamento_tipo'];
                }
            }

            $this->load->view('planos/regulamento_revisao/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function set_pai()
    {
    	$this->load->model('gestao/regulamento_revisao_model');

    	$cd_regulamento_revisao_pai = $this->input->post('cd_regulamento_revisao_pai', TRUE);   

    	$data['nr_ordem'] = 1;

    	$row = $this->regulamento_revisao_model->get_next_ordem($cd_regulamento_revisao_pai);

		if(isset($row['nr_ordem']))
		{
			$data['nr_ordem'] = intval($row['nr_ordem']);
		}

		echo json_encode($data);
    }

    public function verifica_ordem()
    {
        $this->load->model('gestao/regulamento_revisao_model');

        $row = $this->regulamento_revisao_model->verifica_ordem($this->input->post('nr_ordem', TRUE), $this->input->post('cd_regulamento_revisao_pai', TRUE));

        echo json_encode($row);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/regulamento_revisao_model');

            $cd_regulamento_revisao = $this->input->post('cd_regulamento_revisao', true);
            $cd_regulamento_tipo    = $this->input->post('cd_regulamento_tipo', true);

            $args = array(
                'cd_regulamento_revisao_pai' => $this->input->post('cd_regulamento_revisao_pai', true),
                'nr_ordem'                   => $this->input->post('nr_ordem', true),
                'ds_regulamento_revisao'     => $this->input->post('ds_regulamento_revisao', true),
                'ds_descricao'               => $this->input->post('ds_descricao', true),
                'cd_usuario'                 => $this->session->userdata('codigo')
            );

            if(!is_array($cd_regulamento_tipo))
            {
                $args['cd_regulamento_tipo'] = array();
            }
            else
            {
                $args['cd_regulamento_tipo'] = $cd_regulamento_tipo;
            }

            if(intval($cd_regulamento_revisao) == 0)
            {
                $cd_regulamento_revisao = $this->regulamento_revisao_model->salvar($args);

                $fl_renumeracao = $this->input->post('fl_renumeracao', TRUE);

                if(trim($fl_renumeracao) == 'S')
                {
                    $this->regulamento_revisao_model->atualiza_nr_ordem($cd_regulamento_revisao, $args);
                }
            }
            else
            {
                $this->regulamento_revisao_model->atualizar($cd_regulamento_revisao, $args);
            }

            redirect('planos/regulamento_revisao/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function remover($cd_regulamento_revisao, $nr_ordem, $cd_regulamento_revisao_pai)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/regulamento_revisao_model');

            $this->regulamento_revisao_model->remover($cd_regulamento_revisao, $this->session->userdata('codigo'));
            
            $args = array(
                'cd_regulamento_revisao_pai' => $cd_regulamento_revisao_pai,
                'nr_ordem'                   => $nr_ordem,
                'cd_usuario'                 => $this->session->userdata('codigo')
            );

            $this->regulamento_revisao_model->atualiza_nr_ordem($cd_regulamento_revisao, $args, '-');

            redirect('planos/regulamento_revisao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

}