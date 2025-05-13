<?php
class registro_pendencia_documento extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function permissao()
    {
        if(gerencia_in(array('GCM')))
    	{
    		return true;
    	}
        else
        {
            return false;
        }
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $data = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );

            $this->load->view('ecrm/registro_pendencia_documento/index', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('autoatendimento/registro_pendencia_documento_model');

        $args = array(
            'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
            'dt_inclusao_ini'       => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'       => $this->input->post('dt_inclusao_fim', TRUE),
            'fl_cancelamento'       => $this->input->post('fl_cancelamento', TRUE),
            'fl_confirmacao'        => $this->input->post('fl_confirmacao', TRUE),
            'fl_envio_participante' => $this->input->post('fl_envio_participante', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->registro_pendencia_documento_model->listar($args);

        $this->load->view('ecrm/registro_pendencia_documento/index_result', $data);
    }

    public function cadastro($cd_registro_pendencia_documento)
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $data['row']                    = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);
            $data['collection']             = $this->registro_pendencia_documento_model->listar_registro_pendencia_documento_arquivo($cd_registro_pendencia_documento);
            $data['tipo_protocolo_interno'] = $this->registro_pendencia_documento_model->tipo_solicitacao_protocolo_interno();

            $this->load->view('ecrm/registro_pendencia_documento/cadastro', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function confirmar($cd_registro_pendencia_documento)
    {
        if($this->permissao())
        {
            if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
            {
                $url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_confirmar';
            }
            else
            {
                $url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_confirmar';
            }

            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $row = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);

            $args = array(
                'id_app'       => '4ad123d34cec7fa81fa62c864325cd26',
                'id_pendencia' => $row['id_pendencia'],
                're_cripto'    => $row['re_cripto']
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            if($json['error']['status'] == 0)
            {
                $this->load->model('projetos/eventos_email_model');

                $this->registro_pendencia_documento_model->confirmar($cd_registro_pendencia_documento, $this->session->userdata('codigo'));

                $this->registro_pendencia_documento_model->enviar($cd_registro_pendencia_documento, $this->session->userdata('codigo'));

                $participante = $this->registro_pendencia_documento_model->participante_email($cd_registro_pendencia_documento);

                if(count($participante) > 0)
                {
                    $cd_evento  = 436;
                    $cd_usuario = $this->session->userdata('codigo');

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $args = array(
                        'de'                    => 'Pendência de Documento',
                        'assunto'               => $email['assunto'],
                        'para'                  => $participante['email'],
                        'cc'                    => $participante['email_profissional'],
                        'cco'                   => $email['cco'],
                        'texto'                 => $email['email'],
                        'cd_empresa'            => $participante['cd_empresa'],
                        'cd_registro_empregado' => $participante['cd_registro_empregado'],
                        'seq_dependencia'       => $participante['seq_dependencia'],
                        'tp_email'              => 'A',
                        'cd_divulgacao'         => ''
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }

                redirect('ecrm/registro_pendencia_documento');
            }
            else
            {
                exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao registrar a confirmação.</h3>');
            }

        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cancelamento($cd_registro_pendencia_documento)
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $data['row']        = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);
            $data['collection'] = $this->registro_pendencia_documento_model->listar_registro_pendencia_documento_arquivo($cd_registro_pendencia_documento);

            $this->load->view('ecrm/registro_pendencia_documento/cancelar', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cancelar()
    {
        if($this->permissao())
        {
            if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
            {
                $url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_cancelar';
            }
            else
            {
                $url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_cancelar';
            }
            
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $cd_registro_pendencia_documento = $this->input->post('cd_registro_pendencia_documento', TRUE);

            $row = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);

            $args = array(
                'id_app'       => '4ad123d34cec7fa81fa62c864325cd26',
                'id_pendencia' => $row['id_pendencia'],
                're_cripto'    => $row['re_cripto']
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            if($json['error']['status'] == 0)
            {
                $args = array(
                    'ds_justificativa' => $this->input->post('ds_justificativa', TRUE),
                    'cd_usuario'       => $this->session->userdata('codigo')
                );

                $this->registro_pendencia_documento_model->cancelar($cd_registro_pendencia_documento, $args);

                redirect('ecrm/registro_pendencia_documento/cancelamento/'.$cd_registro_pendencia_documento);
            }
            else
            {
                exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao registrar o cancelamento.</h3>');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function andamento($cd_registro_pendencia_documento)
    {
        if($this->permissao())
        {
            if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
            {
                $url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_analise';
            }
            else
            {
                $url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/set_pendencia_analise';
            }

            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $row = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);

            $args = array(
                'id_app'       => '4ad123d34cec7fa81fa62c864325cd26',
                'id_pendencia' => $row['id_pendencia'],
                're_cripto'    => $row['re_cripto']
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            if($json['error']['status'] == 0)
            {
                $this->registro_pendencia_documento_model->andamento($cd_registro_pendencia_documento, $this->session->userdata('codigo'));

                redirect('ecrm/registro_pendencia_documento/cadastro/'.$cd_registro_pendencia_documento);
            }
            else
            {
                exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao registrar o status para em análise.</h3>');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_registro_pendencia_documento, $fl_enviar_email = 'S')
    {
        if($this->permissao())
        {
            $this->load->model(array(
                'autoatendimento/registro_pendencia_documento_model',
                'projetos/eventos_email_model'
            ));

            $this->registro_pendencia_documento_model->enviar($cd_registro_pendencia_documento, $this->session->userdata('codigo'));

            $participante = $this->registro_pendencia_documento_model->participante_email($cd_registro_pendencia_documento);
            
            if(count($participante) > 0 AND trim($fl_enviar_email) == 'S')
            {
                $cd_evento  = 435;
                $cd_usuario = $this->session->userdata('codigo');

                $email = $this->eventos_email_model->carrega($cd_evento);

                $tags = array('[OBS]');
                
                $subs = array(nl2br(trim($participante['ds_justificativa'])));

                $texto = str_replace($tags, $subs, $email['email']);

                $args = array(
                    'de'                    => 'Pendência de Documento',
                    'assunto'               => $email['assunto'],
                    'para'                  => $participante['email'],
                    'cc'                    => $participante['email_profissional'],
                    'cco'                   => $email['cco'],
                    'texto'                 => $texto,
                    'cd_empresa'            => $participante['cd_empresa'],
                    'cd_registro_empregado' => $participante['cd_registro_empregado'],
                    'seq_dependencia'       => $participante['seq_dependencia'],
                    'tp_email'              => 'A',
                    'cd_divulgacao'         => ''
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            }

            redirect('ecrm/registro_pendencia_documento');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gerar_protocolo_interno()
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $cd_registro_pendencia_documento = $this->input->post('cd_registro_pendencia_documento', TRUE);
            $registro_pendencia_documento    = $this->input->post('registro_pendencia_documento', TRUE);

            $row = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);

            $args = array(
                'cd_documento_recebido_tipo'       => 1,
                'cd_documento_recebido_tipo_solic' => $this->input->post('cd_documento_recebido_tipo_solic', TRUE),
                'cd_usuario'                       => $this->session->userdata('codigo')
            );

            $cd_documento_recebido = $this->registro_pendencia_documento_model->protocolo_interno($args);

            foreach ($registro_pendencia_documento as $key => $item) 
            {
                $documento = $this->registro_pendencia_documento_model->get_registro_pendencia_documento_arquivo($item);

                $nr_folha_pdf = 1;

                if(trim(pathinfo($documento['ds_arquivo'], PATHINFO_EXTENSION)) == 'pdf')
                {
                    $pdftext = file_get_contents(base_url().'up/registro_pendencia_documento/'.$documento['ds_arquivo']);
                    $nr_folha_pdf = preg_match_all("/\/Page\W/", $pdftext, $dummy);
                }

                copy('./up/registro_pendencia_documento/'.$documento['ds_arquivo'], './up/documento_recebido/'.$documento['ds_arquivo']);

                $args = array(
                    'cd_documento_recebido' => $cd_documento_recebido,
                    'cd_empresa'            => $row['cd_empresa'], 
                    'cd_registro_empregado' => $row['cd_registro_empregado'],
                    'seq_dependencia'       => $row['seq_dependencia'],
                    'nome'                  => $row['nome'],
                    'cd_tipo_doc'           => $this->input->post('registro_pendencia_'.$item, TRUE),
                    'arquivo'               => $documento['ds_arquivo'],
                    'arquivo_nome'          => $documento['ds_arquivo'],
                    'nr_folha'              => 1,
                    'nr_folha_pdf'          => $nr_folha_pdf,
                    'cd_usuario'            => $this->session->userdata('codigo')
                );

                $this->registro_pendencia_documento_model->protocolo_interno_documento($item, $args);
            }

            redirect('ecrm/cadastro_protocolo_interno/detalhe/'.$cd_documento_recebido);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar_documento_liquid()
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/registro_pendencia_documento_model');

            $cd_registro_pendencia_documento = $this->input->post('cd_registro_pendencia_documento', TRUE);
            $registro_pendencia_documento    = $this->input->post('registro_pendencia_documento', TRUE);

            $row = $this->registro_pendencia_documento_model->carrega($cd_registro_pendencia_documento);

            $pasta        = 'IMAGENS PREVIDENCIARIAS - FCEEE\ANO DE '.date('Y').'\\'.date('m').' - '.strtoupper(mes_extenso(intval(date('m')))).'\\'.date('dmY').'\\';
            $codigo_ficha = 1;
            $dir = '../cieprev/up/registro_pendencia_documento/';

            foreach ($registro_pendencia_documento as $key => $item) 
            {
                $cd_tipo_doc = $this->input->post('registro_pendencia_'.$item, TRUE);

                $documento = $this->registro_pendencia_documento_model->get_registro_pendencia_documento_arquivo($item);

                $tipo_doc = $this->registro_pendencia_documento_model->get_documento_nome($cd_tipo_doc);

                $nome_arquivo = 
                    $row['cd_empresa'].'_'.
                    $row['cd_registro_empregado'].'_'.
                    $row['seq_dependencia'].'_'.
                    $cd_tipo_doc.'_'.
                    uniqid();

                $campos_ficha = 
                    $row['cd_empresa'].';'.
                    $row['cd_registro_empregado'].';'.
                    $row['seq_dependencia'].';'.
                    $cd_tipo_doc.';'.
                    $tipo_doc['nome_documento'].';'.
                    date('d/m/Y').';'.
                    date('d/m/Y').';'.
                    ';'.
                    ';'.
                    ';'.
                    ';'.
                    $nome_arquivo.';'.
                    $nome_arquivo.';'.
                    ';'.
                    ';'.
                    ';'.
                    $this->session->userdata('usuario').' - '.$this->session->userdata('divisao').';'.
                    'Pendência de Documento (e-prev)';

                $ob_cliente_soap = new SoapClient('http://10.63.255.55/wsliquidweb/Default.asmx?wsdl');

                $args['loginUsuario']  = 'protocolo_eprev';
                $args['senha']         = 'c8ml09';
                $args['nomeDocumento'] = utf8_encode($nome_arquivo.'.pdf');
                $args['pasta']         = utf8_encode($pasta);
                $args['codigoFicha']   = $codigo_ficha;
                $args['camposFicha']   = utf8_encode($campos_ficha);
                $args['nomeArquivo']   = utf8_encode($nome_arquivo.'.pdf');
                $args['arquivoBase64'] = base64_encode(file_get_contents($dir.$documento['ds_arquivo']));

                $resultado = $ob_cliente_soap->ImportarArquivo($args);

                $txt = '<?xml version="1.0" encoding="ISO-8859-1"?>'.$resultado->ImportarArquivoResult->any;

                $xml = new SimpleXMLElement($txt);

                $array = (array) $xml;

                $id_liquid = $array['@attributes']['codigoDocumento'];

                $args = array(
                    'id_liquid'   => $id_liquid,
                    'cd_tipo_doc' => $this->input->post('registro_pendencia_'.$item, TRUE)
                );

                $this->registro_pendencia_documento_model->liquid_documento($item, $args);

                redirect('ecrm/registro_pendencia_documento/cadastro/'.$cd_registro_pendencia_documento);
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}