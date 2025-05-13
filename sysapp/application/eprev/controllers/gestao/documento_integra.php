<?php
class Documento_integra extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->dir = '../eletroceee/pydio/data/';
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            #Adriana Espindola da Silva Reichmann
            if($this->session->userdata('codigo') == 3)
            {
                return TRUE;
            }
            #Renata Opitz
            else if($this->session->userdata('codigo') == 468)
            {
                return TRUE;
            }
			#Vanessa Silva Alves
			else if($this->session->userdata('codigo') == 424)
			{
				return true;
			}
            #Vitoria Vidal Medeiros da Silva
            else if($this->session->userdata('codigo') == 431)
            {
                return true;
            }
			#Luciano Machado Krause
            else if($this->session->userdata('codigo') == 480)
            {
                return true;
            }
            #Luciano Rodriguez
            else if($this->session->userdata('codigo') == 251)
            {
                return TRUE;
            }
            #Cristiano Jacobsen
            else if($this->session->userdata('codigo') == 170)
            {
                return TRUE;
            }			
        }
        else
        {
            return FALSE;
        }
    }

    public function tipo()
    {
        if($this->get_permissao())
        {
            $this->load->view('gestao/documento_integra/tipo_index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function tipo_listar()
    {
        $this->load->model('gestao/documento_integra_model');

        $args = array(
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->documento_integra_model->listar($args);

        $this->load->view('gestao/documento_integra/tipo_result', $data);
    }

    public function minhas()
    {
        $this->load->view('gestao/documento_integra/minhas_index');
    }

    public function minhas_listar()
    {
        $this->load->model('gestao/documento_integra_model');

        $args = array(
            'cd_documento_integra_doc_tipo' => $this->input->post('cd_documento_integra_doc_tipo', TRUE)
        );

        manter_filtros($args);

        $cd_responsavel = $this->session->userdata('codigo');

        $data['collection'] = $this->documento_integra_model->minhas_listar($cd_responsavel, $args);

        $this->load->view('gestao/documento_integra/minhas_result', $data);
    }

    public function minhas_cadastro($cd_documento_integra_doc_tipo, $cd_documento_integra = 0, $nr_mes = '', $nr_ano = '')
    {
    	$data = array();

        if(intval($cd_documento_integra) == 0)
        {
        	if(trim($nr_mes) == '')
        	{
        		$nr_mes = date('m');
        	}

        	if(trim($nr_ano) == '')
        	{
        		$nr_ano = date('Y');
        	}

            $data['row'] = array(
                'cd_documento_integra'          => intval($cd_documento_integra),
                'cd_documento_integra_doc_tipo' => intval($cd_documento_integra_doc_tipo),
                'ds_documento_integra_doc_tipo' => '',
                'nr_mes'                        => $nr_mes,
                'nr_ano'                        => $nr_ano,
                'dt_referencia'                 => '01/'.$nr_mes.'/'.$nr_ano,
                'dt_envio'                      => '',
                'ds_referencia'                 => ''
            );

            if(intval($cd_documento_integra_doc_tipo) > 0)
            {
            	$this->load->model('gestao/documento_integra_model');

            	$doc_tipo = $this->documento_integra_model->get_doc_tipo($cd_documento_integra_doc_tipo);

                $data['row']['ds_documento_integra_doc_tipo'] = $doc_tipo['ds_documento_integra_doc_tipo'];
            	$data['row']['tp_periodicidade']              = $doc_tipo['tp_periodicidade'];
            }

            $data['anexar_documento'] = array();
        }
        else
        {
            $this->load->model('gestao/documento_integra_model');

            $data['row'] = $this->documento_integra_model->carrega($cd_documento_integra);

            $data['anexar_documento'] = $this->documento_integra_model->listar_anexar_documento($cd_documento_integra);
        }

        $this->load->view('gestao/documento_integra/minhas_cadastro', $data);
    }

    public function minhas_salvar()
    {
        $this->load->model('gestao/documento_integra_model');

        $cd_documento_integra = $this->input->post('cd_documento_integra', TRUE);

        $nr_mes = $this->input->post('mes_referencia',TRUE);
        $nr_ano = $this->input->post('ano_referencia',TRUE);
        $ds_referencia = $this->input->post('ds_referencia',TRUE);

        $cd_documento_integra_doc_tipo = $this->input->post('cd_documento_integra_doc_tipo',TRUE);

        if(intval($cd_documento_integra) == 0)
        {
        	$doc_tipo = $this->documento_integra_model->get_doc_tipo($cd_documento_integra_doc_tipo);

            if(trim($doc_tipo['tp_periodicidade']) == 'M')
            {
                $ds_caminho = 'DOCUMENTOS_APROVADOS/'.$doc_tipo['ds_caminho'].'/'.$nr_ano.'/'.strtoupper(mes_extenso($nr_mes));
            }
            else if(trim($doc_tipo['tp_periodicidade']) == 'E')
            {
                $ds_caminho = 'DOCUMENTOS_APROVADOS/'.$doc_tipo['ds_caminho'].'/'.$doc_tipo['ds_caminho'].' '.$nr_ano.'/'.strtoupper($ds_referencia);
            }

            $args = array( 
                'dt_referencia'                 => '01/'.$nr_mes.'/'.$nr_ano,
                'ds_referencia'                 => $ds_referencia,
                'cd_documento_integra_doc_tipo' => $cd_documento_integra_doc_tipo,
                'ds_caminho'                    => $ds_caminho,
                'cd_usuario'                    => $this->session->userdata('codigo')
            );

            $cd_documento_integra = $this->documento_integra_model->minhas_salvar($args);
        }

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
                
                $this->documento_integra_model->anexar_documento(intval($cd_documento_integra), $args);
                
                $nr_conta++;
            }
        }

        redirect('gestao/documento_integra/minhas_cadastro/'.intval($cd_documento_integra_doc_tipo).'/'.intval($cd_documento_integra), 'refresh');
    }

    public function excluir_documento($cd_documento_integra, $cd_documento_integra_anexo)
    {
        $this->load->model('gestao/documento_integra_model');

        $this->documento_integra_model->excluir_documento(
            intval($cd_documento_integra_anexo), 
            $this->session->userdata('codigo')
        );

        redirect('gestao/documento_integra/minhas_cadastro/0/'.intval($cd_documento_integra), 'refresh');
    }

    public function enviar_documentacao($cd_documento_integra)
    {
    	$this->load->plugin('encoding_pi');

    	$this->load->model('gestao/documento_integra_model');

    	$row = $this->documento_integra_model->carrega($cd_documento_integra);

    	$documentacao = $this->documento_integra_model->listar_anexar_documento($cd_documento_integra);

    	$dir = $this->cria_diretorio($row['ds_caminho_completo']);

    	foreach ($documentacao as $key => $item) 
    	{
    		copy('../cieprev/up/documento_integra/'.$item['arquivo'], $dir.fixUTF8($item['arquivo_nome']));
    	}

    	$this->documento_integra_model->envia(
            intval($cd_documento_integra), 
            $this->session->userdata('codigo')
        );

    	redirect('gestao/documento_integra/minhas', 'refresh');
    }

    private function cria_diretorio($ds_caminho_completo)
    {
    	$arr = explode('/', $ds_caminho_completo);

    	$dir_tmp = $this->dir;

    	foreach ($arr as $key => $item) 
    	{
    		$dir_tmp .= $item;
			if(!is_dir($dir_tmp))
			{
				mkdir($dir_tmp, 0777);
			}

			$dir_tmp .= '/';
    	}

    	return $dir_tmp;
    }
}