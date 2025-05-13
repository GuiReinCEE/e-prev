<?php
class Sg_documento_recebido extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
		$this->load->view('cadastro/sg_documento_recebido/index');
    }

    public function listar()
    {
    	$this->load->model('projetos/docs_recebidos_model');

    	$args = array(
    		'ano'         => $this->input->post('ano', TRUE),
    		'numero'      => $this->input->post('numero', TRUE),
    		'data_ini'    => $this->input->post('data_ini', TRUE),
    		'data_fim'    => $this->input->post('data_fim', TRUE),
    		'remetente'   => $this->input->post('remetente', TRUE),
    		'destino'     => $this->input->post('destino', TRUE),
			'cd_gerencia' => $this->session->userdata('divisao')
    	);
		
		manter_filtros($args);

		$data['collection'] = $this->docs_recebidos_model->listar($args);

		$this->load->view('cadastro/sg_documento_recebido/index_result', $data);   	
	}

	public function cadastro($ano = 0, $numero = 0)
	{
		$this->load->model('projetos/docs_recebidos_model');
		
		$permissao = true;

		if(intval($ano) == 0 AND intval($numero) == 0)
		{
			$data['row'] = array(
				'ano'          => intval($ano),
				'numero'       => intval($numero),
				'ano_numero'   => '',
				'destino_emp'  => '',
				'destino_re'   => '',
				'destino_seq'  => '',
				'destino_nome' => '',
				'remetente'    => '',
				'assunto'      => '',
				'data'         => '',
				'hora'         => '',
				'fl_restrito'  => 'N'
			);
		}
		else
		{
			$data['row'] = $this->docs_recebidos_model->carrega($ano, $numero);

			if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
			{
				$permissao = false;
			}
		}

		if($permissao)
		{
			$this->load->view('cadastro/sg_documento_recebido/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		$this->load->model('projetos/docs_recebidos_model');

		$ano    = $this->input->post('ano', TRUE);
		$numero = $this->input->post('numero',TRUE);

		$args = array(
			'destino_emp'  => $this->input->post('destino_emp',TRUE),
			'destino_re'   => $this->input->post('destino_re',TRUE),
			'destino_seq'  => $this->input->post('destino_seq',TRUE),
			'destino_nome' => $this->input->post('destino_nome',TRUE),
			'remetente'    => $this->input->post('remetente',TRUE),
			'data'         => $this->input->post('data',TRUE),
			'hora'         => $this->input->post('hora',TRUE),
			'assunto'      => $this->input->post('assunto',TRUE),
			'fl_restrito'  => $this->input->post('fl_restrito',TRUE),
			'cd_usuario'   => $this->session->userdata('codigo')
		);

		if(intval($ano) == 0 AND intval($numero) == 0)
		{
			$this->docs_recebidos_model->salvar($args);
		}
		else
		{
			$this->docs_recebidos_model->atualizar($ano, $numero, $args);
		}

		redirect('cadastro/sg_documento_recebido', 'refresh');
	}

	public function excluir($ano, $numero)
	{
		if(gerencia_in(array('GC')))
        {
        	$this->load->model('projetos/docs_recebidos_model');

			$this->docs_recebidos_model->excluir($ano, $numero, $this->session->userdata('codigo'));
			
			redirect('cadastro/sg_documento_recebido', 'refresh' );
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function anexo($ano, $numero)
    {
    	CheckLogin();

		$this->load->model('projetos/docs_recebidos_model');

		$data['row'] = $this->docs_recebidos_model->carrega($ano, $numero);

		if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
		else
		{
			$data['collection'] = $this->docs_recebidos_model->listar_anexo($ano, $numero);

			$this->load->view('cadastro/sg_documento_recebido/anexo', $data);
		}
    }

    public function salvar_anexo()
    {
    	CheckLogin();

		$this->load->model('projetos/docs_recebidos_model');

		$ano    = $this->input->post('ano', TRUE);
		$numero = $this->input->post('numero', TRUE);

		$data['row'] = $this->docs_recebidos_model->carrega($ano, $numero);

		if(trim($data['row']['fl_restrito']) == 'S' AND $this->session->userdata('divisao') != trim($data['row']['cd_gerencia_inclusao']))
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
		else
		{
			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args = array();        
                    
                    $args['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                    $args['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                    $args['cd_usuario']   = $this->session->userdata('codigo');      
                    
                    $this->docs_recebidos_model->salvar_anexo(intval($ano), intval($numero), $args);
                    
                    $nr_conta++;
                }
            }

            redirect('cadastro/sg_documento_recebido/anexo/'.intval($ano).'/'.intval($numero), 'refresh');
		}
    }

    public function excluir_anexo($ano, $numero, $cd_correspondencia_anexo)
    {
    	CheckLogin();

        $this->load->model('projetos/docs_recebidos_model');

        $this->docs_recebidos_model->excluir_anexo(
            intval($cd_correspondencia_anexo),
            $this->session->userdata('codigo')
        );

        redirect('cadastro/sg_documento_recebido/anexo/'.intval($ano).'/'.intval($numero), 'refresh');
    }
}