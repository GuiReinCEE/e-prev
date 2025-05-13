<?php
class Avaliacao_treinamento extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

	public function get_status()
	{
		return array(
    		array('value' => 'I', 'text' => 'Aguardando Início'),
    		array('value' => 'A', 'text' => 'Em Andamento'),
    		array('value' => 'F', 'text' => 'Finalizado')
    	);
	}
	
    public function index()
    {
		$this->load->model('projetos/avaliacao_treinamento_model');

		$data = array();
		
		$data['status'] = $this->get_status();
		
		$this->load->view('servico/avaliacao_treinamento/index', $data);
    }

    public function listar()
    {
    	$this->load->model('projetos/avaliacao_treinamento_model');

    	$args = array();
		$data = array();
		
		$args['nome']   = $this->input->post('nome', TRUE);
		$args['status'] = $this->input->post('status', TRUE);
		
		manter_filtros($args);
        
        $data['collection'] = $this->avaliacao_treinamento_model->listar($this->session->userdata('codigo'), $args);
		
		$this->load->view('servico/avaliacao_treinamento/index_result', $data);
    }
	
	public function cadastro($cd_treinamento_colaborador_resposta)
    {
		$this->load->model('projetos/avaliacao_treinamento_model');

        $row = $this->avaliacao_treinamento_model->carrega($cd_treinamento_colaborador_resposta);

		$data['treinamento'] = $this->avaliacao_treinamento_model->carrega($cd_treinamento_colaborador_resposta);

        if(count($row) == 0)
        {
            exibir_mensagem('AVALIAÇÃO NÃO FOI LOCALIZADA.');
        }
        else
        {
            if(
                (($this->session->userdata('codigo') == intval($row['cd_usuario'])) AND (trim($row['dt_finalizado'])) == '')
                OR
                ($this->session->userdata('codigo') == 251)

            )
            {
                if(trim($row['ds_formulario']) == '')
                {
                    $this->load->library('gera_avaliacao_treinamento');

                    $data['formulario'] = $this->gera_avaliacao_treinamento->monta_formulario($row['cd_treinamento_colaborador_formulario']);

                    $formulario = $data['formulario'];

                    $formulario['ds'] = utf8_encode($data['formulario']['ds']);

                    $formulario = json_encode($formulario);

                    $this->avaliacao_treinamento_model->atualizar_formulario(intval($cd_treinamento_colaborador_resposta), $formulario, $this->session->userdata('codigo'));

                    $data['respostas']  = array();

                    if(trim($formulario) == '')
                    {
                        $data['formulario'] = array();
                    }
                }
                else
                {
                    $data['formulario'] = json_decode($row['ds_formulario'], true);
                    $data['respostas']  = json_decode($row['ds_formulario_respondido'], true);
                }

                if(count($data['formulario']) > 0)
                {
                    $this->load->view('servico/avaliacao_treinamento/formulario', $data);
                }
                else
                {
                    exibir_mensagem('AVALIAÇÃO NÃO FOI LOCALIZADA.');
                }
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
        }
    }

    public function salvar()
    {
        $cd_treinamento_colaborador_resposta = $this->input->post('cd_treinamento_colaborador_resposta', TRUE);

        $this->atualizar_resposta($cd_treinamento_colaborador_resposta);

        redirect('servico/avaliacao_treinamento/cadastro/'.$cd_treinamento_colaborador_resposta);
    }

    public function finalizar()
    {
        $this->load->model(array(
            'projetos/avaliacao_treinamento_model',
            'projetos/eventos_email_model'
        ));

        $cd_treinamento_colaborador_resposta = $this->input->post('cd_treinamento_colaborador_resposta', TRUE);

        $cd_evento = 204;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $treinamento = $this->avaliacao_treinamento_model->carrega($cd_treinamento_colaborador_resposta);

        $tags = array('[NOME]', '[TREINAMENTO]', '[LINK]');
        $subs = array($treinamento['colaborador'], $treinamento['nome'], site_url('servico/avaliacao_treinamento/pdf/'.intval($cd_treinamento_colaborador_resposta)));

        $texto = str_replace($tags, $subs, $email['email']);

        $this->atualizar_resposta($cd_treinamento_colaborador_resposta);

        $this->avaliacao_treinamento_model->finalizar_avaliacao($cd_treinamento_colaborador_resposta, $this->session->userdata('codigo'));

        $args = array(
            'de'      => 'Avaliação de Treinamento',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);

        redirect('servico/avaliacao_treinamento');
    }

    public function pdf($cd_treinamento_colaborador_resposta)
    {
        $this->load->library('gera_avaliacao_treinamento');

        $this->gera_avaliacao_treinamento->pdf($cd_treinamento_colaborador_resposta);
    }

    private function atualizar_resposta($cd_treinamento_colaborador_resposta)
    {
        $this->load->model('projetos/avaliacao_treinamento_model');

        $treinamento = $this->avaliacao_treinamento_model->carrega($cd_treinamento_colaborador_resposta);

        $formulario = json_decode($treinamento['ds_formulario'], true);

        $respostas = array();

        foreach ($formulario['estrutura'] as $key => $item) 
        {
            if(trim($item['tp']) == 'D')
            {
                $respostas['estrutura_'.$item['cd']] = utf8_encode($this->input->post('estrutura_'.$item['cd'], TRUE));
            }
            else if(trim($item['tp']) == 'O')
            {
                if(count($item['sub']) == 0)
                {
                    $respostas['estrutura_'.$item['cd']] = $this->input->post('estrutura_'.$item['cd'], TRUE);
                }
                else
                {
                    foreach ($item['sub'] as $key2 => $item2) 
                    {
                        $respostas['estrutura_'.$item2['cd']] = $this->input->post('estrutura_'.$item2['cd'], TRUE);
                    }
                }
            }
            else if(trim($item['tp']) == 'S')
            {
                $resp = $this->input->post('estrutura_'.$item['cd'], TRUE);

                if(is_array($resp))
                {
                    foreach ($resp as $key2 => $item2) 
                    {
                        $respostas['estrutura_'.$item['cd']][] = $item2;
                    }
                }
                else
                {
                    $respostas['estrutura_'.$item['cd']] = array();
                }

                $fl_campo_adicional = false;

                foreach ($item['conf'] as $key2 => $item2) 
                {
                    if(trim($item2['obs']) == 'S')
                    {
                        $fl_campo_adicional = true;
                    }
                }

                if($fl_campo_adicional)
                {
                     $respostas['estrutura_obs_'.$item['cd']] = utf8_encode($this->input->post('estrutura_obs_'.$item['cd'], TRUE));
                }
            }
        }

        $ds_formulario_respondido = json_encode($respostas);

        $this->avaliacao_treinamento_model->atualizar_resposta(intval($cd_treinamento_colaborador_resposta), $ds_formulario_respondido, $this->session->userdata('codigo'));
    }
}