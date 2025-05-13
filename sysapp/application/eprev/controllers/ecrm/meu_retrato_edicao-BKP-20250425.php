<?php
class Meu_retrato_edicao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function permissao()
    {
    	if(gerencia_in(array('GNR', 'GAP.')))
    	{
    		return true;
    	}
    	else if($this->session->userdata('indic_05') == 'S')
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    private function get_tipo_participante()
    {
        return array(
            array('value' => 'ATIV', 'text' => 'Ativo'),
            array('value' => 'APOS', 'text' => 'Aposentado'),
            array('value' => 'APOSM', 'text' => 'Aposentado Migrado'),
            array('value' => 'EXAU', 'text' => 'Ex-Autárquico')
        );
    }

    public function participante_dados_instituidor($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->get_dados_instituidor($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_aposentado($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->get_dados_aposentado($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_ieabprev($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_ieabprev($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_municipios($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_municipios($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_familia_corporativo($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_familia_corporativo($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_ceeeprev($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_ceeeprev($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_ceranprev($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_ceranprev($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_crmprev($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_crmprev($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }

    public function participante_dados_foz($cd_edicao, $cd_plano, $cd_empresa)
    {
        $this->load->model('meu_retrato/edicao_model');

        $collection = $this->edicao_model->participante_dados_foz($cd_edicao);

        $filename = $cd_plano.'_'.$cd_empresa.'_'.date("Y-m-d").".csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
     
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
     
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
     
        $df = fopen("php://output", 'w');

        fputcsv($df, array_keys(reset($collection)), ';');

        foreach ($collection as $row) 
        {
            fputcsv($df, $row, ';');
        }

        fclose($df);

        die();    
    }
	
    public function participante_dados($cd_edicao, $cd_edicao_participante)
    {
		$this->load->model('meu_retrato/edicao_model');

		$data = array();
		
		$data['row']['cd_edicao'] = intval($cd_edicao);
		
		$args['cd_edicao_participante'] = intval($cd_edicao_participante);
		
		$data['collection'] = $this->edicao_model->participante_dados($args);

		$this->load->view('ecrm/meu_retrato_edicao/participante_dados', $data);
    }	

    public function index()
    {
		$this->load->model('meu_retrato/edicao_model');

		$data = array();

		$data['data_base'] = $this->edicao_model->get_data_base();

        $data['tipo_participante'] = $this->get_tipo_participante();

		$data['cd_gerencia'] = $this->session->userdata('divisao');

		$this->load->view('ecrm/meu_retrato_edicao/index', $data);
    }

    public function listar()
    {
		$this->load->model('meu_retrato/edicao_model');

		$args = array();
		$data = array();

        $args = array(
            'cd_empresa'      => $this->input->post('cd_plano_empresa', TRUE),
            'cd_plano'        => $this->input->post('cd_plano', TRUE),
            'nr_extrato'      => $this->input->post('nr_extrato', TRUE),
            'dt_base_extrato' => $this->input->post('dt_base_extrato', TRUE),
            'tp_participante' => $this->input->post('tp_participante', TRUE),
            'cd_gerencia'     => ($this->session->userdata('codigo') == 1 ? 'GTI' : $this->session->userdata('divisao'))
        ); 
		
		manter_filtros($args);

		$data['collection'] = $this->edicao_model->listar($args);

		$this->load->view('ecrm/meu_retrato_edicao/index_result', $data);
    }

    public function cadastro($cd_edicao = 0)
    {
        $data['tipo_participante'] = $this->get_tipo_participante();

        if(gerencia_in(array('GS')))
        {
            if($cd_edicao == 0)
            {
                $data['row'] = array(
                    'cd_edicao'                => 0,
                    'cd_empresa'               => '',
                    'cd_plano'                 => '',
                    'nr_extrato'               => '',
                    'dt_base_extrato'          => '',
                    'dt_liberacao_informatica' => '',
                    'tp_participante'          => '',
					
                    'vl_plano'     => 0,
                    'vl_cdi'       => 0,
                    'vl_poupanca'  => 0,
                    'vl_inpc'      => 0,
                    'vl_igpm'      => 0,
                    'vl_ipca_ibge' => 0
                );
            }
            else
            {
                $this->load->model('meu_retrato/edicao_model');

                $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));
            }

            $this->load->view('ecrm/meu_retrato_edicao/cadastro', $data);
        }
        else if(gerencia_in(array('GAP.')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));

            $this->load->view('ecrm/meu_retrato_edicao/atuarial', $data);
        }
        else if(gerencia_in(array('GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));

            $this->load->view('ecrm/meu_retrato_edicao/comunicacao', $data);
        }
        else if(gerencia_in(array('GNR')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));

            $this->load->view('ecrm/meu_retrato_edicao/expansao', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $cd_edicao = $this->input->post('cd_edicao', TRUE);

            $args = array(
                'cd_empresa'                       => $this->input->post('cd_plano_empresa', TRUE),
                'cd_plano'                         => $this->input->post('cd_plano', TRUE),
                'tp_participante'                  => $this->input->post('tp_participante', TRUE),
                'dt_base_extrato'                  => $this->input->post('dt_base_extrato', TRUE),
                'nr_extrato'                       => $this->input->post('nr_extrato', TRUE),
                'ficaadica'                        => $this->input->post('ficaadica', TRUE),
                'dt_equilibrio'                    => $this->input->post('dt_equilibrio', TRUE),
                'ds_equilibrio'                    => $this->input->post('ds_equilibrio', TRUE),
                'comentario_rentabilidade'         => $this->input->post('comentario_rentabilidade', TRUE),
                'arquivo_comparativo'              => $this->input->post('arquivo_comparativo', TRUE),
                'arquivo_comparativo_nome'         => $this->input->post('arquivo_comparativo_nome', TRUE),
                'arquivo_premissas_atuariais'      => $this->input->post('arquivo_premissas_atuariais', TRUE),
                'arquivo_premissas_atuariais_nome' => $this->input->post('arquivo_premissas_atuariais_nome', TRUE),

                'cd_edicao_comparativo'            => $this->input->post('cd_edicao_comparativo', TRUE),
                'dt_inicial_comparativo'           => $this->input->post('dt_inicial_comparativo', TRUE),
                'dt_final_comparativo'             => $this->input->post('dt_final_comparativo', TRUE),
				
				'comparativo_vl_plano'             => app_decimal_para_db($this->input->post('comparativo_vl_plano', TRUE)),
                'comparativo_vl_cdi'               => app_decimal_para_db($this->input->post('comparativo_vl_cdi', TRUE)),
                'comparativo_vl_poupanca'          => app_decimal_para_db($this->input->post('comparativo_vl_poupanca', TRUE)),
                'comparativo_vl_inpc'              => app_decimal_para_db($this->input->post('comparativo_vl_inpc', TRUE)),
                'comparativo_vl_igpm'              => app_decimal_para_db($this->input->post('comparativo_vl_igpm', TRUE)),
                'comparativo_vl_ipca_ibge'         => app_decimal_para_db($this->input->post('comparativo_vl_ipca_ibge', TRUE)),

                'cd_usuario'                       => $this->session->userdata('codigo')
            );

            if(intval($cd_edicao) == 0)
            {
                if(intval($args['cd_plano']) == 1)
                {
                    $row = $this->edicao_model->salvar_plano_unico($args);

                    if(trim($args['tp_participante']) != 'APOS')
                    {
                        $this->edicao_model->gerar_plano_unico($row['cd_edicao'], $args['cd_empresa'], $args['cd_usuario']);
                    }
                }
                else
                {
                    $row = $this->edicao_model->salvar($args);
                }

                $cd_edicao = $row['cd_edicao'];
            }
            else
            {
                $this->edicao_model->atualizar(intval($cd_edicao), $args);

                if(trim($args['arquivo_comparativo']) != '')
                {
                    copy('./up/meu_retrato/'.$args['arquivo_comparativo'], './../eletroceee/meu_retrato/img/'.$args['arquivo_comparativo']);
                }

                if(trim($args['arquivo_premissas_atuariais']) != '')
                {
                    copy('./up/meu_retrato/'.$args['arquivo_premissas_atuariais'], './../eletroceee/meu_retrato/img/'.$args['arquivo_premissas_atuariais']);
                }
            }

            redirect('ecrm/meu_retrato_edicao/cadastro/'.$cd_edicao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gerar_instituidor_aposentado()
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $row = $this->edicao_model->gerar_instituidor_aposentado($this->session->userdata('codigo'));

            redirect('ecrm/meu_retrato_edicao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gerar($cd_edicao)
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $cd_usuario = $this->session->userdata('codigo');

            $this->edicao_model->gerar(intval($cd_edicao), $cd_usuario);

            redirect('ecrm/meu_retrato_edicao/cadastro/'.intval($cd_edicao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function libera_informatica($cd_edicao)
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model(array(
                'meu_retrato/edicao_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 194;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $edicao = $this->edicao_model->carrega($cd_edicao);

            $tags = array('[EMPRESA]', '[PLANO]', '[NR_EXTRATO]', '[DT_BASE]', '[LINK]');
            $subs = array($edicao['sigla'], $edicao['plano'], $edicao['nr_extrato'], $edicao['dt_base_extrato'], site_url('ecrm/meu_retrato_edicao/cadastro/'.intval($cd_edicao)));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $this->edicao_model->libera_informatica(intval($cd_edicao), $cd_usuario);

            $args = array(
                'de'      => 'Meu Retrato Edição',
                'assunto' => str_replace('[EMPRESA]', $edicao['plano'], $email['assunto']),
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('ecrm/meu_retrato_edicao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function libera_informatica_beneficio($cd_edicao)
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model(array(
                'meu_retrato/edicao_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 195;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $edicao = $this->edicao_model->carrega($cd_edicao);

            $tags = array('[EMPRESA]', '[PLANO]', '[DT_BASE]', '[LINK]');
            $subs = array($edicao['sigla'], $edicao['plano'], $edicao['dt_base_extrato'], site_url('ecrm/meu_retrato_edicao/cadastro/'.intval($cd_edicao)));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $this->edicao_model->libera_informatica(intval($cd_edicao), $cd_usuario);

            $args = array(
                'de'      => 'Meu Retrato Edição',
                'assunto' => str_replace('[EMPRESA]', $edicao['plano'], $email['assunto']),
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('ecrm/meu_retrato_edicao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function libera_atuarial($cd_edicao)
    {
        if(gerencia_in(array('GP')))
        {
           $this->load->model(array(
                'meu_retrato/edicao_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 196;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $edicao = $this->edicao_model->carrega($cd_edicao);

            $tags = array('[EMPRESA]', '[PLANO]', '[NR_EXTRATO]', '[DT_BASE]', '[LINK]');
            $subs = array($edicao['sigla'], $edicao['plano'], $edicao['nr_extrato'], $edicao['dt_base_extrato'], site_url('ecrm/meu_retrato_edicao/cadastro/'.intval($cd_edicao)));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $this->edicao_model->libera_atuarial(intval($cd_edicao), $cd_usuario);

            $assunto = $email['assunto'];

            $fl_benef = FALSE;

            if(intval($edicao['cd_plano']) == 1 OR (intval($edicao['cd_plano']) == 2 AND trim($edicao['tp_participante']) == 'APOSM'))
            {
                $fl_benef = TRUE;
            }

            if($fl_benef)
            {
                $assunto .= ' GP - Benefício';
            }
            else
            {
                $assunto .= ' GP - Atuarial';
            }

            $args = array(
                'de'      => 'Meu Retrato Edição',
                'assunto' => $assunto,
                'para'    => $email['para'].(!$fl_benef ? ';atuarial@eletroceee.com.br' : ''),
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('ecrm/meu_retrato_edicao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_comunicacao()
    {
        if(gerencia_in(array('GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $cd_edicao = $this->input->post('cd_edicao', TRUE);

            $args = array(
                'ficaadica'                        => $this->input->post('ficaadica', TRUE),
                'dt_equilibrio'                    => $this->input->post('dt_equilibrio', TRUE),
                'ds_equilibrio'                    => $this->input->post('ds_equilibrio', TRUE),				
                'comentario_rentabilidade'         => $this->input->post('comentario_rentabilidade', TRUE),
                'arquivo_comparativo'              => $this->input->post('arquivo_comparativo', TRUE),
                'arquivo_comparativo_nome'         => $this->input->post('arquivo_comparativo_nome', TRUE),
                'arquivo_premissas_atuariais'      => $this->input->post('arquivo_premissas_atuariais', TRUE),
                'arquivo_premissas_atuariais_nome' => $this->input->post('arquivo_premissas_atuariais_nome', TRUE),
				
                'cd_edicao_comparativo'            => $this->input->post('cd_edicao_comparativo', TRUE),
                'dt_inicial_comparativo'           => $this->input->post('dt_inicial_comparativo', TRUE),
                'dt_final_comparativo'             => $this->input->post('dt_final_comparativo', TRUE),
				
				'comparativo_vl_plano'             => app_decimal_para_db($this->input->post('comparativo_vl_plano', TRUE)),
                'comparativo_vl_cdi'               => app_decimal_para_db($this->input->post('comparativo_vl_cdi', TRUE)),
                'comparativo_vl_poupanca'          => app_decimal_para_db($this->input->post('comparativo_vl_poupanca', TRUE)),
                'comparativo_vl_inpc'              => app_decimal_para_db($this->input->post('comparativo_vl_inpc', TRUE)),
                'comparativo_vl_igpm'              => app_decimal_para_db($this->input->post('comparativo_vl_igpm', TRUE)),
                'comparativo_vl_ipca_ibge'         => app_decimal_para_db($this->input->post('comparativo_vl_ipca_ibge', TRUE)),				
				
                'cd_usuario'                       => $this->session->userdata('codigo')
            );

            $this->edicao_model->atualizar_comunicacao(intval($cd_edicao), $args);

            if(trim($args['arquivo_comparativo']) != '')
            {
                copy('./up/meu_retrato/'.$args['arquivo_comparativo'], './../eletroceee/meu_retrato/img/'.$args['arquivo_comparativo']);
            }

            if(trim($args['arquivo_premissas_atuariais']) != '')
            {
                copy('./up/meu_retrato/'.$args['arquivo_premissas_atuariais'], './../eletroceee/meu_retrato/img/'.$args['arquivo_premissas_atuariais']);
            }

            redirect('ecrm/meu_retrato_edicao/cadastro/'.$cd_edicao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function duplicar_comunicao($cd_edicao, $fl_tipo = 'S')
    {
        if(gerencia_in(array('GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $edicao = $this->edicao_model->carrega($cd_edicao);

            $edicao_replicar = $this->edicao_model->get_edicao_replica($cd_edicao, $edicao['cd_plano'], ($fl_tipo == 'S' ? $edicao['tp_participante'] : ''));

            foreach ($edicao_replicar as $key => $item) 
            {
                $row = $this->edicao_model->carrega($item['cd_edicao']);

                $args = array(
                    'ficaadica'                        => $edicao['ficaadica'],
                    'dt_equilibrio'                    => $edicao['dt_equilibrio'],
                    'ds_equilibrio'                    => $edicao['ds_equilibrio'],             
                    'comentario_rentabilidade'         => $edicao['comentario_rentabilidade'],
                    'arquivo_comparativo'              => $edicao['arquivo_comparativo'],
                    'arquivo_comparativo_nome'         => $edicao['arquivo_comparativo_nome'],
                    'arquivo_premissas_atuariais'      => $edicao['arquivo_premissas_atuariais'],
                    'arquivo_premissas_atuariais_nome' => $edicao['arquivo_premissas_atuariais_nome'],
                    
                    'cd_edicao_comparativo'            => $row['cd_edicao_comparativo'],
                    'dt_inicial_comparativo'           => $edicao['dt_inicial_comparativo'],
                    'dt_final_comparativo'             => $edicao['dt_final_comparativo'],
                    
                    'comparativo_vl_plano'             => $edicao['vl_plano'],
                    'comparativo_vl_cdi'               => $edicao['vl_cdi'],
                    'comparativo_vl_poupanca'          => $edicao['vl_poupanca'],
                    'comparativo_vl_inpc'              => $edicao['vl_inpc'],
                    'comparativo_vl_igpm'              => $edicao['vl_igpm'],
                    'comparativo_vl_ipca_ibge'         => $edicao['vl_ipca_ibge'],             
                    
                    'cd_usuario'                       => $this->session->userdata('codigo')
                );


                $this->edicao_model->atualizar_comunicacao(intval($row['cd_edicao']), $args);
            }

            redirect('ecrm/meu_retrato_edicao/cadastro/'.$cd_edicao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function libera($cd_edicao)
    {
        if(gerencia_in(array('GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $cd_usuario = $this->session->userdata('codigo');

            $this->edicao_model->libera(intval($cd_edicao), $cd_usuario);

            $row = $this->edicao_model->carrega(intval($cd_edicao));

            redirect('ecrm/divulgacao/cadastro/'.$row['cd_divulgacao'], 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function participante($cd_edicao)
    {
        if($this->permissao())
        {
            $this->load->model('meu_retrato/edicao_model');

            $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));

            $this->load->view('ecrm/meu_retrato_edicao/participante', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function participante_listar()
    {
        $this->load->model('meu_retrato/edicao_model');

        $args = array();
        $data = array();

        $cd_edicao = $this->input->post('cd_edicao', TRUE);

        $args = array(
            'qt_pagina'             => $this->input->post('qt_pagina', TRUE),
            'nr_pagina'             => $this->input->post('nr_pagina', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'desligado'             => $this->input->post('desligado', TRUE)
        ); 
        
        manter_filtros($args);

        $nr_pagina = intval($args['nr_pagina']) - 1;

        if($nr_pagina > 0)
        {
            $nr_pagina = ($nr_pagina * intval($args['qt_pagina']));
        }
        else
        {
            $nr_pagina = 0;
        }

        $data['collection'] = $this->edicao_model->edicao_listar(intval($cd_edicao), $nr_pagina, $args['qt_pagina'], $args);

        $this->load->view('ecrm/meu_retrato_edicao/participante_result', $data);
    }
	
    public function verificar($cd_edicao)
    {
        if(gerencia_in(array('GS')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $data['row'] = $this->edicao_model->carrega(intval($cd_edicao));

            $this->load->view('ecrm/meu_retrato_edicao/verificar', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }	
	
    public function verificar_listar()
    {
        $this->load->model('meu_retrato/edicao_model');

		$result = null;
		$data   = array();
		$args   = array();

        $args['cd_edicao']  = $this->input->post('cd_edicao', TRUE);
        $args['cd_item']    = $this->input->post('cd_item', TRUE);
        $args['qt_amostra'] = $this->input->post('qt_amostra', TRUE);
        
        manter_filtros($args);

        $this->edicao_model->verificar_listar_maior($result, $args);
		$data['ar_maior'] = $result->result_array();
		
        $this->edicao_model->verificar_listar_menor($result, $args);
		$data['ar_menor'] = $result->result_array();		
		
        $this->load->view('ecrm/meu_retrato_edicao/verificar_result.php', $data);		
    }	
	
    public function equilibrio_listar()
    {
        $this->load->model('meu_retrato/edicao_model');

		$result = null;
		$data   = array();
		$args   = array();

        $args['cd_edicao']  = $this->input->post('cd_edicao', TRUE);
        
        manter_filtros($args);

        $this->edicao_model->equilibrio_listar($result, $args);
		$data['collection'] = $result->result_array();		
		
        $this->load->view('ecrm/meu_retrato_edicao/cadastro_equilibrio_result.php', $data);		
    }	
	
    public function equilibrio_add()
    {
        if(gerencia_in(array('GS','GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $args = array(
                'cd_edicao'    => $this->input->post('cd_edicao', TRUE),
                'nr_ano'       => $this->input->post('nr_ano', TRUE),
                'vl_provisao'  => app_decimal_para_db($this->input->post('vl_provisao', TRUE)),
                'vl_cobertura' => app_decimal_para_db($this->input->post('vl_cobertura', TRUE)),
                'cd_usuario'   => $this->session->userdata('codigo')
            );

            $this->edicao_model->equilibrio_add($args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function equilibrio_del()
    {
        if(gerencia_in(array('GS','GNR-COM')))
        {
            $this->load->model('meu_retrato/edicao_model');

            $args = array(
                'cd_edicao_equilibrio' => $this->input->post('cd_edicao_equilibrio', TRUE),
				'cd_usuario'           => $this->session->userdata('codigo')
            );

            $this->edicao_model->equilibrio_del($args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function getIndice()
    {
        $this->load->model('meu_retrato/edicao_model');

        $meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$args = array();
		$data = array();

        $args = array(
            'cd_indice'       => $this->input->post('cd_indice', TRUE),
            'ds_titulo'       => $this->input->post('ds_titulo', TRUE),
            'dt_ini'          => $this->input->post('dt_ini', TRUE),
            'dt_fim'          => $this->input->post('dt_fim', TRUE)
        ); 
		
		
		$ar_reg = $this->edicao_model->getIndice($args);
        
        #echo "<PRE>";
        #print_r($args);
        #echo "<HR>";
        #print_r($ar_reg);


		$ar_dados = Array();
		
        foreach($ar_reg as $item)
		{
			$ar_dados[] = $item;
        }    
        
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_conta_acumulada = $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			else
			{
				$nr_conta_acumulada = $nr_conta_acumulada_anterior * $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
        $ar_ret['titulo']        = $args['ds_titulo']; 
        $ar_ret['dt_ini']        = $args['dt_ini']; 
        $ar_ret['dt_fim']        = $args['dt_fim']; 
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['mes_maximo']    = $ar_dt_mes[count($ar_dt_mes)-1];
        $ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
        $ar_ret['pr_acumulado_formatado']  = number_format(floatval($ar_cota_acumulada[count($ar_cota_acumulada)-1]),2,",",".");
		$ar_ret['dt_referencia'] = $dt_referencia;
		
        #echo "<HR>";
        #print_r($ar_ret);       
        
        echo json_encode($ar_ret);
    }

    function getRentabilidade()
    {
        $this->load->model('meu_retrato/edicao_model');

        $meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$args = array();
		$data = array();

        $args = array(

            'cd_plano'        => $this->input->post('cd_plano', TRUE),
            'cd_empresa'       => $this->input->post('cd_empresa', TRUE),
            'dt_ini'          => $this->input->post('dt_ini', TRUE),
            'dt_fim'          => $this->input->post('dt_fim', TRUE)
        ); 
		
		
		$ar_ret = $this->edicao_model->getRentabilidade($args);
        

        $ar_ret['nr_cota_mes_formatado']       = number_format(floatval($ar_ret['nr_cota_mes']),2,",",".");
        $ar_ret['nr_cota_acumulada_formatado'] = number_format(floatval($ar_ret['nr_cota_acumulada']),2,",",".");
        #echo "<PRE>";
        #print_r($args);
        #echo "<HR>";
        #print_r($ar_reg);
    
        
        echo json_encode($ar_ret);
    }  
    
    
    function getPoupanca()
    {
        $this->load->model('meu_retrato/edicao_model');

        $meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$args = array();
		$data = array();

        $args = array(
            'dt_ini'          => $this->input->post('dt_ini', TRUE),
            'dt_fim'          => $this->input->post('dt_fim', TRUE)
        ); 
		
		
		$ar_reg = $this->edicao_model->getPoupanca($args);
        
        #echo "<PRE>";
        #print_r($args);
        #echo "<HR>";
        #print_r($ar_reg);


		$ar_dados = Array();
		
        foreach($ar_reg as $item)
		{
			$ar_dados[] = $item;
        }    
        
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_conta_acumulada = $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			else
			{
				$nr_conta_acumulada = $nr_conta_acumulada_anterior * $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
        $ar_ret['titulo']        = utf8_encode("POUPANÇA"); 
        $ar_ret['dt_ini']        = $args['dt_ini']; 
        $ar_ret['dt_fim']        = $args['dt_fim']; 
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['mes_maximo']    = $ar_dt_mes[count($ar_dt_mes)-1];
        $ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
        $ar_ret['pr_acumulado_formatado']  = number_format(floatval($ar_cota_acumulada[count($ar_cota_acumulada)-1]),2,",",".");
		$ar_ret['dt_referencia'] = $dt_referencia;
		
        #echo "<HR>";
        #print_r($ar_ret);       
        
        echo json_encode($ar_ret);
    }    
}