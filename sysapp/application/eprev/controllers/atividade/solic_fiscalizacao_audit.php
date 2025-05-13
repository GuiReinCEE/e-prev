<?php
class Solic_fiscalizacao_audit extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC', 'AI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_acao($row)
    {
        if(trim($row['cd_gerencia']) == $this->session->userdata('divisao'))
        {
            return TRUE;
        }
        elseif(in_array($this->session->userdata('divisao'), $row['gerencia_opcional']))
        {
           return  TRUE;
        }
        elseif(in_array($this->session->userdata('divisao'), $row['gestao']))
        {
            return  TRUE;
        }
        else
        {
            return  $this->get_permissao();
        }
    }

    private function get_cadastro($row)
    {
        $row['gerencia_opcional'] = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional(intval($row['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $row['gerencia_opcional'][] = $gerencia['cd_gerencia'];
        }

        $row['gestao'] = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gestao(intval($row['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $row['gestao'][] = $gerencia['cd_gerencia'];
        }

        $row['grupo_opcional'] = array();

        foreach($this->solic_fiscalizacao_audit_model->get_grupo(intval($row['cd_solic_fiscalizacao_audit'])) as $grupo)
        {               
            $row['grupo_opcional'][] = $grupo['cd_solic_fiscalizacao_audit_grupo'];
        }

        return $row;
    }

    private function get_permissao_area_consolidadora($cd_gerencia)
    {
        if(trim($cd_gerencia) == trim($this->session->userdata('divisao')))
        {
            $row = $this->solic_fiscalizacao_audit_model->get_resp_area_consolidadora(
                trim($this->session->userdata('divisao')),
                trim($this->session->userdata('codigo'))
            );

            if(intval($row['tl_permissao']) > 0)
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            } 
        }
        else
        {
            return FALSE;
        } 
    }

    private function integracao_liquid($nome_documento, $pasta, $codigo_ficha, $campos_ficha, $nome_arquivo, $arquivo_base_64)
    {
        $ob_cliente_soap = new SoapClient('http://10.63.255.55/wsliquidweb/Default.asmx?wsdl');

        $args['loginUsuario']  = 'protocolo_eprev';
        $args['senha']         = 'c8ml09';
        $args['nomeDocumento'] = $nome_documento;
        $args['pasta']         = $pasta;
        $args['codigoFicha']   = $codigo_ficha;
        $args['camposFicha']   = $campos_ficha;
        $args['nomeArquivo']   = $nome_arquivo;
        $args['arquivoBase64'] = $arquivo_base_64;

        $resultado = $ob_cliente_soap->ImportarArquivo($args);

        $txt = '<?xml version="1.0" encoding="ISO-8859-1"?>'.$resultado->ImportarArquivoResult->any;

        $xml = new SimpleXMLElement($txt);

        $array = (array) $xml;

        return $array['@attributes']['codigoDocumento'];
    }

    private function gera_arquivo_liquid($nr_ano, $nr_numero, $arquivo, $arquivo_nome, $dir_item = '')
    {
        if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') OR ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
        {
            $codigo_ficha = 11;

            $pasta = utf8_encode('FISCALIZAÇÕES E AUDITORIAS\\'.$nr_ano.'-'.$nr_numero.'\\');

            if(trim($dir_item) != '')
            {
                $pasta .= utf8_encode($dir_item.'\\');
            }

            $dir = '../cieprev/up/solic_fiscalizacao_audit';

            $campos_ficha = 
                intval($nr_ano).';'.
                intval($nr_numero).';'.
                trim(utf8_encode($arquivo_nome)).';'.
                date('d/m/Y');

            $file = $dir.'/'.utf8_encode($arquivo);

/*
            if(pathinfo($arquivo, PATHINFO_EXTENSION) == 'pdf')
            {
                $this->load->plugin('PDFMerger');

                $pdf = new PDFMerger_pi;                

                $pdf->addPDFArray(array($file))->merge('file', $arquivo);

                $file = $dir.'/_'.utf8_encode($arquivo);

                $pdf->fpdi->Output($file, 'F');
            }
*/
            $cd_liquid =  $this->integracao_liquid(
                utf8_encode($arquivo_nome), 
                $pasta, 
                $codigo_ficha, 
                $campos_ficha, 
                utf8_encode($arquivo_nome), 
                base64_encode(file_get_contents($file))
            );

            if(intval($cd_liquid) > 0)
            {
                //@unlink($dir.'/'.$arquivo);
            }

            return $cd_liquid;
        }
        else
        {
            return 0;
        }
    }

    public function abrir_documento_liquid($cd_liquid)
    {
        /*
        $ob_cliente_soap = new SoapClient('http://10.63.255.55/wsliquidweb/Default.asmx?wsdl');

        $args['loginUsuario']    = 'protocolo_eprev';
        $args['senha']           = 'c8ml09';
        $args['codigoDocumento'] = $cd_liquid;

        $resultado = $ob_cliente_soap->ObterDocumentoPDF($args);

        $arquivo = '<?xml version="1.0" encoding="ISO-8859-1"?>'.$resultado->ObterDocumentoPDFResult->any;

        $xml = new SimpleXMLElement($arquivo);

        (array) $xml;

        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate');
        header('Pragma: hack');
        header('Content-Disposition: inline; filename="doc.pdf"');
        header('Content-Transfer-Encoding: binary');        
        echo base64_decode($xml[0]);
        */

        $this->abrir_documento($cd_liquid, 'pdf');
    }

    public function abrir_documento($cd_liquid, $ext)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $liquid = $this->solic_fiscalizacao_audit_model->get_caminho_liquid($cd_liquid);

        $args = array(
            'token'      => 'c1656f543fa6bc16aae79d1f128933f5',
            'ds_caminho' => trim($liquid['ds_caminho'])
        );

        $ch = curl_init();

        $url = 'http://10.63.255.16:1111/getDocumentoLiquid.php';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

        if(intval($json['error']['status']) == 0)
        {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="file.'.$ext.'"');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Expires: 0');

            echo base64_decode($json['result']);
        }
        else
        {
            echo trim($json['error']['mensagem']);
        }
    }

    public function abrir_documento_web($cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $row = $this->solic_fiscalizacao_audit_model->get_documentacao_anexo($cd_solic_fiscalizacao_audit_documentacao_anexo);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$row['arquivo_nome'].'"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        readfile('./up/solic_fiscalizacao_audit/'.$row['arquivo']);
    }

    private function get_base64_liquid($cd_liquid, $ext = 'pdf')
    {
        $base64 = '';

        if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') OR ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
        {
            if(trim($ext) == 'pdf')
            {
                $ob_cliente_soap = new SoapClient('http://10.63.255.55/wsliquidweb/Default.asmx?wsdl');

                $args['loginUsuario']    = 'protocolo_eprev';
                $args['senha']           = 'c8ml09';
                $args['codigoDocumento'] = $cd_liquid;

                $resultado = $ob_cliente_soap->ObterDocumentoPDF($args);

                $arquivo = '<?xml version="1.0" encoding="ISO-8859-1"?>'.$resultado->ObterDocumentoPDFResult->any;

                $xml = new SimpleXMLElement($arquivo);

                (array) $xml;

                $base64 = $xml[0];
            }
            else
            {
                $this->load->model('projetos/solic_fiscalizacao_audit_model');

                $liquid = $this->solic_fiscalizacao_audit_model->get_caminho_liquid($cd_liquid);

                $ob_cliente_soap = new SoapClient('http://10.63.255.16:1111/server.php?wsdl');

                $base64 = $ob_cliente_soap->geraDocumentoLiquid($liquid['ds_caminho']);
            }
        }

        return $base64;
    }

    public function os79188csv()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $args = array(
            'cd_solic_fiscalizacao_audit_origem' => '',
            'dt_recebimento_ini'                 => '',
            'dt_recebimento_fim'                 => '',
            'cd_solic_fiscalizacao_audit_tipo'   => 30,
            'ds_solic_fiscalizacao_audit_tipo'   => '',
            'cd_gerencia'                        => '',
            'cd_gestao'                          => '',
            'ds_documento'                       => '',
            'ds_teor'                            => '',
            'fl_enviado'                         => '',
            'dt_prazo_ini'                       => '',
            'dt_prazo_fim'                       => '',
            'dt_envio_ini'                       => '',
            'dt_envio_fim'                       => '',
            'dt_atendimento_ini'                 => '',
            'dt_atendimento_fim'                 => '',
            'cd_gerencia_usuario'                => $this->session->userdata('divisao')
        );

        $collection = $this->solic_fiscalizacao_audit_model->listar($this->get_permissao(), $args);

        //$linha = 'Ano/Nº;Dt. Recebimento;Documento;Item;Descrição Resumida;Gerência;Responsável;Dt. Atendimento Resp.'.chr(13).chr(10);

        $data['collection'] = array();

        foreach($collection as $key => $item)
        {
            $documentacao = $this->solic_fiscalizacao_audit_model->listar_documentacao(intval($item['cd_solic_fiscalizacao_audit']));

            foreach ($documentacao as $key3 => $item3) 
            {
                $usuario = $this->solic_fiscalizacao_audit_model->get_usuario_responsavel(intval($item3['cd_solic_fiscalizacao_audit_documentacao']));

                if(count($usuario) > 0)
                {
                    foreach ($usuario as $key2 => $item2) 
                    {
                        $documentacao[$key3]['responsavel'][] = $item2['ds_usuario'];
                    }
                }
                else
                {
                    $documentacao[$key3]['responsavel'][] = $item3['cd_gerencia'];
                }
            }

            foreach ($documentacao as $key3 => $item3) 
            {
                $data['collection'][] = array(
                    'ds_ano_numero'                            => $item['ds_ano_numero'],
                    'dt_recebimento'                           => $item['dt_recebimento'],
                    'ds_documento'                             => $item['ds_documento'],
                    'nr_item'                                  => $item3['nr_item'],
                    'ds_solic_fiscalizacao_audit_documentacao' => $item3['ds_solic_fiscalizacao_audit_documentacao'],
                    'cd_gerencia'                              => $item3['cd_gerencia'],
                    'responsavel'                              => $item3['responsavel'],
                    'dt_atendimento_responsavel'               => $item3['dt_atendimento_responsavel'],
                );
            }
        }

        $this->load->view('atividade/solic_fiscalizacao_audit/os79188csv', $data);
    }


    public function index()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
           'gerencia'     => $this->solic_fiscalizacao_audit_model->get_gerencia(),
           'gestao'       => $this->solic_fiscalizacao_audit_model->get_gerencia(array('DIV', 'CON')),
           'origem'       => $this->solic_fiscalizacao_audit_model->get_origem(),
           'tipo'         => array(),
           'fl_permissao' => $this->get_permissao()
        );

        $agrupamento = $this->solic_fiscalizacao_audit_model->get_agrupamento();

        foreach ($agrupamento as $key => $item) 
        {
            $tipo = $this->solic_fiscalizacao_audit_model->get_tipo(0, $item['cd_solic_fiscalizacao_audit_tipo_agrupamento']);

            $data['tipo'][] = array(
                'value' => $item['value'],
                'text'  => $tipo
            );
        }       

        $this->load->view('atividade/solic_fiscalizacao_audit/index', $data);
    }

    public function listar()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $args = array(
            'cd_solic_fiscalizacao_audit_origem' => $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE),
            'dt_recebimento_ini'                 => $this->input->post('dt_recebimento_ini', TRUE),
            'dt_recebimento_fim'                 => $this->input->post('dt_recebimento_fim', TRUE),
            'cd_solic_fiscalizacao_audit_tipo'   => $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE),
            'ds_solic_fiscalizacao_audit_tipo'   => $this->input->post('ds_solic_fiscalizacao_audit_tipo', TRUE),
            'cd_gerencia'                        => $this->input->post('cd_gerencia', TRUE),
            'cd_gestao'                          => $this->input->post('cd_gestao', TRUE),
            'ds_documento'                       => $this->input->post('ds_documento', TRUE),
            'ds_teor'                            => $this->input->post('ds_teor', TRUE),
            'fl_enviado'                         => $this->input->post('fl_enviado', TRUE),
            'dt_prazo_ini'                       => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'                       => $this->input->post('dt_prazo_fim', TRUE),
            'dt_envio_ini'                       => $this->input->post('dt_envio_ini', TRUE),
            'dt_envio_fim'                       => $this->input->post('dt_envio_fim', TRUE),
            'dt_atendimento_ini'                 => $this->input->post('dt_atendimento_ini', TRUE),
            'dt_atendimento_fim'                 => $this->input->post('dt_atendimento_fim', TRUE),
            'cd_gerencia_usuario'                => $this->session->userdata('divisao')
        );

        manter_filtros($args);

        $data['collection'] = $this->solic_fiscalizacao_audit_model->listar($this->get_permissao(), $args);
      
        foreach($data['collection'] as $key => $tipo)
        {
            $data['collection'][$key]['gerencia'] = array();
                
            foreach($this->solic_fiscalizacao_audit_model->get_gestao(intval($tipo['cd_solic_fiscalizacao_audit'])) as $gerencia)
            {               
               $data['collection'][$key]['gerencia'][] = $gerencia['cd_gerencia'];
            }
        }

        $this->load->view('atividade/solic_fiscalizacao_audit/index_result', $data);
    }

    public function cadastro($cd_solic_fiscalizacao_audit = 0, $cd_correspondencia_recebida_item = 0)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
            'origem'            => $this->solic_fiscalizacao_audit_model->get_origem(),
            'gerencia'          => $this->solic_fiscalizacao_audit_model->get_gerencia(),
            'gestao'            => $this->solic_fiscalizacao_audit_model->get_gerencia(array('DIV', 'CON')),
            'grupos'            => $this->solic_fiscalizacao_audit_model->get_grupos(),
            'tipo'              => array(),
            'gerencia_opcional' => array(),
        );

        $agrupamento = $this->solic_fiscalizacao_audit_model->get_agrupamento();

        foreach ($agrupamento as $key => $item) 
        {
            $tipo = $this->solic_fiscalizacao_audit_model->get_tipo(0, $item['cd_solic_fiscalizacao_audit_tipo_agrupamento']);

            $data['tipo'][] = array(
                'value' => $item['value'],
                'text'  => $tipo
            );
        }

        $data['cd_correspondencia_recebida_item'] = $cd_correspondencia_recebida_item;

        $data['fl_atendimento'] = FALSE;

        if(intval($cd_solic_fiscalizacao_audit) == 0)
        {
            $data['row'] = array(
                'cd_solic_fiscalizacao_audit'        => intval($cd_solic_fiscalizacao_audit),
                'fl_especificar_origem'              => 'N',
                'fl_especificar_tipo'                => 'N',
                'tl_gestao'                          => 0,
                'cd_solic_fiscalizacao_audit_origem' => '',
                'ds_origem'                          => '',
                'dt_recebimento'                     => '',
                'cd_solic_fiscalizacao_audit_tipo'   => '',
                'ds_tipo'                            => '',
                'cd_gerencia'                        => '',
                'ds_documento'                       => '',
                'ds_teor'                            => '',
                'fl_prazo'                           => '',
                'nr_dias_prazo'                      => '',
                'dt_prazo'                           => '',
                'arquivo'                            => '',
                'arquivo_nome'                       => '',
                'dt_envio'                           => '',
                'gestao'                             => array(),
                'gerencia_opcional'                  => array(),
                'grupo_opcional'                     => array(),
                'ds_link_acesso'                     => ''
            );

            $fl_permissao = $this->get_permissao();
        }
        else
        {
            $data['row'] = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

            $fl_permissao = $this->get_permissao_acao($data['row']);

            $data['cd_correspondencia_recebida_item'] = $data['row']['cd_correspondencia_recebida_item'];

            if(intval($data['row']['tl_documento_encerramento']) == 0 AND intval($data['row']['tl_documento']) > 0)
            {
                $data['fl_atendimento'] = TRUE;
            }
        }

        if(intval($data['cd_correspondencia_recebida_item']) > 0)
        {
            $data['correspondencia_recebida'] = $this->solic_fiscalizacao_audit_model->carrega_correspondencia_recebida(
                $data['cd_correspondencia_recebida_item']
            );
        }

        if($fl_permissao)
        {
		   $this->load->view('atividade/solic_fiscalizacao_audit/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_origem()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit_origem = $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE);

        $origem = $this->solic_fiscalizacao_audit_model->get_origem($cd_solic_fiscalizacao_audit_origem);

        echo json_encode($origem);
    }

    public function get_tipo()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit_tipo = $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE);

        $tipo = $this->solic_fiscalizacao_audit_model->get_tipo($cd_solic_fiscalizacao_audit_tipo);

        $tipo_gerencia = $this->solic_fiscalizacao_audit_model->get_tipo_gerencia(intval($tipo['value']));

        $gestao = array();

        foreach($tipo_gerencia as $gerencia)
        {               
            $gestao[] = $gerencia['cd_gerencia'];
        }

        $tipo = array( 
            'fl_especificar' => $tipo['fl_especificar'],
            'cd_gerencia'    => $tipo['cd_gerencia'],
            'gestao'         => $gestao
        );

        echo json_encode($tipo);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solic_fiscalizacao_audit_model');

            $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);

            $args = array(
                'cd_solic_fiscalizacao_audit_origem' => $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE),
                'ds_origem'                          => $this->input->post('ds_origem', TRUE),
                'dt_recebimento'                     => $this->input->post('dt_recebimento', TRUE),
                'cd_solic_fiscalizacao_audit_tipo'   => $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE),
                'ds_tipo'                            => $this->input->post('ds_tipo', TRUE),
                'cd_gerencia'                        => $this->input->post('cd_gerencia', TRUE),
                'ds_documento'                       => $this->input->post('ds_documento', TRUE),
                'ds_teor'                            => $this->input->post('ds_teor', TRUE),
                'fl_prazo'                           => $this->input->post('fl_prazo', TRUE),
                'nr_dias_prazo'                      => $this->input->post('nr_dias_prazo', TRUE),
                'dt_prazo'                           => $this->input->post('dt_prazo', TRUE),
                'arquivo'                            => $this->input->post('arquivo', TRUE),
                'arquivo_nome'                       => $this->input->post('arquivo_nome', TRUE),
                'cd_correspondencia_recebida_item'   => $this->input->post('cd_correspondencia_recebida_item', TRUE),
                'cd_usuario'                         => $this->session->userdata('codigo')
            );

            $gerencia_opcional = $this->input->post('gerencia_opcional', TRUE);

            if(!is_array($gerencia_opcional))
            {
                $args['gerencia_opcional'] = array();
            }
            else
            {
                $args['gerencia_opcional'] = $gerencia_opcional;
            }

            $gestao = $this->input->post('gestao_item', TRUE);

            if(!is_array($gestao))
            {
                $args['gestao'] = array();
            }
            else
            {
                $args['gestao'] = $gestao;
            }

            $grupo_opcional = $this->input->post('grupo_opcional', TRUE);

            if(!is_array($grupo_opcional))
            {
                $args['grupo_opcional'] = array();
            }
            else
            {
                $args['grupo_opcional'] = $grupo_opcional;
            }

            if(intval($cd_solic_fiscalizacao_audit) == 0)
            {
                $cd_solic_fiscalizacao_audit = $this->solic_fiscalizacao_audit_model->salvar($args);
            }
            else
            {
                #echo "<PRE>"; print_r($args);exit;
				$this->solic_fiscalizacao_audit_model->atualizar($cd_solic_fiscalizacao_audit, $args);
            }

            redirect('atividade/solic_fiscalizacao_audit/cadastro/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_atendimento_correspondencia()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solic_fiscalizacao_audit_model');

            $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
            $fl_enviar_email = $this->input->post('fl_enviar_email', TRUE);

            $args = array(
                'dt_envio_atendimento'         => $this->input->post('dt_envio_atendimento', TRUE),
                'nr_correspondencia_ano'       => $this->input->post('nr_correspondencia_ano', TRUE),
                'nr_correspondencia_numero'    => $this->input->post('nr_correspondencia_numero', TRUE),
                'ds_justificativa_atendimento' => $this->input->post('ds_justificativa_atendimento', TRUE),
                'arquivo_atendimento'          => $this->input->post('arquivo_atendimento', TRUE),
                'arquivo_atendimento_nome'     => $this->input->post('arquivo_atendimento_nome', TRUE),
                'cd_usuario'                   => $this->session->userdata('codigo')
            );

            if($fl_enviar_email == 'S')
            {
                $this->solic_fiscalizacao_audit_model->salvar_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args);

                $this->email_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args);
                $this->email_atendimento_correspondencia_gestao($cd_solic_fiscalizacao_audit);
            }
            else if($fl_enviar_email == 'N')
            {
                $this->solic_fiscalizacao_audit_model->atualizar_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args);
            }

            redirect('atividade/solic_fiscalizacao_audit/cadastro/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function email_atendimento_correspondencia_gestao($cd_solic_fiscalizacao_audit)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $gestao = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional(intval($cd_solic_fiscalizacao_audit)) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }    
   
        foreach($this->solic_fiscalizacao_audit_model->get_gestao(intval($cd_solic_fiscalizacao_audit)) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo(intval($cd_solic_fiscalizacao_audit)) as $grupo)
        {               
            if(trim($grupo['ds_email_grupo']) == '')
            {
                foreach ($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo_integrante($cd_solic_fiscalizacao_audit) as $grupo2) 
                {
                    $gestao[] = strtolower($grupo2['ds_usuario']).'@eletroceee.com.br';
                }
            }
            else
            {
                $gestao[] = strtolower($grupo['ds_email_grupo']);
            }
        }

        if(count($gestao) > 0)
        {
            $cd_evento = 432;

            $email = $this->eventos_email_model->carrega($cd_evento);
            $row   = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

            $tags = array('[DS_NOME_DOCUMENTO]', '[DS_REMETENTE]', '[DS_TIPO]', '[LINK]');

            $subs = array(
                $row['ds_documento'],
                $row['ds_solic_fiscalizacao_audit_origem'],
                $row['ds_solic_fiscalizacao_audit_tipo'],
                str_replace('http:', 'https:', base_url('up/solic_fiscalizacao_audit/'.$row['arquivo_atendimento']))
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');
            
            $args = array( 
                'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
                'assunto' => $email['assunto'],
                'para'    => '',  
                'cc'      => implode(';', $gestao),
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args); 

            $this->envio_pydio($cd_solic_fiscalizacao_audit); 
        }
    }

    public function envio_pydio($cd_solic_fiscalizacao_audit)
    {
        $this->load->plugin('encoding_pi');

        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $row   = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        $caminho_solic_fiscalizacao_audit = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/REGISTRO-SOLICITACOES/'.$row['ds_ano_edicao'];

        if(!is_dir($caminho_solic_fiscalizacao_audit))
        {
            mkdir($caminho_solic_fiscalizacao_audit, 0777);
        }

        $caminho_solic_fiscalizacao_audit .= '/'.$row['ds_mes_edicao'].' - '.fixUTF8(mes_extenso($row['ds_mes_edicao']));

        if(!is_dir($caminho_solic_fiscalizacao_audit))
        {
            mkdir($caminho_solic_fiscalizacao_audit, 0777);
        }

        copy('../cieprev/up/solic_fiscalizacao_audit/'.$row['arquivo_atendimento'], $caminho_solic_fiscalizacao_audit.'/'.str_replace('/','-',$row['ds_ano_numero']).'.pdf');

        $arq_solic_fiscalizacao_audit = $cd_solic_fiscalizacao_audit.'_'.date('dmyHis').'.pdf';

        $this->load->plugin('fpdf');
        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;

        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY()+2);

        $ob_pdf->SetFont('segoeuib','',20);
        $ob_pdf->MultiCell(190, 5, "Documentos ".$row['ds_ano_numero'], '0', 'C');

        $ob_pdf->SetY($ob_pdf->GetY()+6);
        $ob_pdf->SetFont('segoeuib','',12);
        $ob_pdf->MultiCell(190, 5, "Link:", '0', 'L');
        $ob_pdf->SetFont('segoeuil','',11);
        $ob_pdf->MultiCell(190, 5, $row['ds_link_acesso'], '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY()+6);
        $ob_pdf->SetFont('segoeuib','',12);
        $ob_pdf->MultiCell(190, 5, "Chave de Acesso:", '0', 'L');
        $ob_pdf->SetFont('segoeuil','',11);
        $ob_pdf->MultiCell(190, 5, $row['ds_chave_acesso'], '0', 'L');

        $ob_pdf->Output('up/solic_fiscalizacao_audit/'.$arq_solic_fiscalizacao_audit, 'F');

        copy('../cieprev/up/solic_fiscalizacao_audit/'.$arq_solic_fiscalizacao_audit, $caminho_solic_fiscalizacao_audit.'/'.str_replace('/','-',$row['ds_ano_numero']).' - Documentos.pdf');

    }

    private function email_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 369;

        $email = $this->eventos_email_model->carrega($cd_evento);
        $row   = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        $nr_correspondecia = $args['nr_correspondencia_ano'].'/'.$args['nr_correspondencia_numero'];

        if(intval($args['nr_correspondencia_ano']) > 0 AND intval($args['nr_correspondencia_numero']) > 0)
        {
            $ds_correspondecia = ' através da correspondência '.$nr_correspondecia;
        }
        else
        {
            $ds_correspondecia = '';
        }

        if(trim($args['ds_justificativa_atendimento']) != '')
        {
            $ds_justificativa = 'Justificativa: '.nl2br($args['ds_justificativa_atendimento']);
        }
        else
        {
            $ds_justificativa = '';
        }
      
        $tags = array('[DS_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[DT_ATENDIMENTO]', '[DS_CORRESPONDECIA]', '[DS_JUSTIFICATIVA]', '[LINK]');

        $subs = array(
            $row['ds_documento'],
            $row['dt_recebimento'],
            $row['ds_solic_fiscalizacao_audit_origem'],
            $row['ds_solic_fiscalizacao_audit_tipo'],
            $args['dt_envio_atendimento'],
            $ds_correspondecia,
            $ds_justificativa,
            site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($cd_solic_fiscalizacao_audit))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower($row['cd_gerencia']).'@eletroceee.com.br',
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);        
    }

    public function enviar($cd_solic_fiscalizacao_audit)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/solic_fiscalizacao_audit_model',
                'projetos/eventos_email_model'
            ));

            $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

            $row['ext'] = pathinfo($row['arquivo'], PATHINFO_EXTENSION);

            //$this->gera_pdf($cd_solic_fiscalizacao_audit);

           // $cd_liquid = $this->gera_arquivo_liquid($row['nr_ano'], $row['nr_numero'], $row['arquivo'], $row['arquivo_nome']);

            //$row['cd_liquid'] = $cd_liquid;

            $row['cd_liquid'] = 0; 
            $this->enviar_email_gestao($row);

            if(trim($row['cd_gerencia']) != '')
            {
                $this->enviar_email_area_consolidadora($row);
            }

            $this->solic_fiscalizacao_audit_model->enviar(
                intval($cd_solic_fiscalizacao_audit), 
                0,
                $this->session->userdata('codigo')
            );
        
            redirect('atividade/solic_fiscalizacao_audit/cadastro/'.$cd_solic_fiscalizacao_audit, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

     public function enviar_email_gestao_teste($cd_solic_fiscalizacao_audit)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        $this->enviar_email_gestao($row);
    }


    public function enviar_email_gestao($solic_fiscalizacao_audit)
    {
        $gestao = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }    
   
        foreach($this->solic_fiscalizacao_audit_model->get_gestao(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $grupo)
        {               
            if(trim($grupo['ds_email_grupo']) == '')
            {
            	foreach ($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo_integrante($grupo['cd_solic_fiscalizacao_audit_grupo']) as $grupo2) 
            	{
            		$gestao[] = strtolower($grupo2['ds_usuario']).'@eletroceee.com.br';
            	}
            }
            else
            {
            	$gestao[] = strtolower($grupo['ds_email_grupo']);
            }
        } 

        if(count($gestao) > 0)
        {
            $cd_evento = 299;

            $email = $this->eventos_email_model->carrega($cd_evento);
          
            $tags = array('[DS_NOME_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_REMETENTE]', '[DS_TEOR]', '[DT_PRAZO]');

            $subs = array(
                $solic_fiscalizacao_audit['ds_documento'],
                $solic_fiscalizacao_audit['dt_recebimento'],
                $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_origem'],
                $solic_fiscalizacao_audit['ds_teor'],
                $solic_fiscalizacao_audit['dt_prazo']
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array( 
                'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
                'assunto' => $email['assunto'],
                'para'    => '',
                'cc'      => implode(';', $gestao),  
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $dir = '../cieprev/up/solic_fiscalizacao_audit';

            $file = $dir.'/'.$solic_fiscalizacao_audit['arquivo'];

            $anexo[0] = array(
                //'arquivo'      => $this->get_base64_liquid($solic_fiscalizacao_audit['cd_liquid'], $solic_fiscalizacao_audit['ext']),
                'arquivo'      => base64_encode(file_get_contents($file)),
                'arquivo_nome' => $solic_fiscalizacao_audit['arquivo_nome']
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args, $anexo);  
        }          
    }

    private function enviar_email_area_consolidadora($solic_fiscalizacao_audit)
    {
        $cd_evento = 300;

        $email = $this->eventos_email_model->carrega($cd_evento);        

        $tags = array('[DS_NOME_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_REMETENTE]', '[DS_TEOR]', '[DT_PRAZO]', '[LINK]');

        $subs = array(
            $solic_fiscalizacao_audit['ds_documento'],
            $solic_fiscalizacao_audit['dt_recebimento'],
            $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_origem'],
            $solic_fiscalizacao_audit['ds_teor'],
            $solic_fiscalizacao_audit['dt_prazo'],
            site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower($solic_fiscalizacao_audit['cd_gerencia']).'@eletroceee.com.br',  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);   
    }

    public function pdf($cd_solic_fiscalizacao_audit)
    {
    }

    private function gera_pdf($cd_solic_fiscalizacao_audit)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $this->load->plugin('fpdf');
        
        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);
        
        $gerencia_opcional = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional(intval($cd_solic_fiscalizacao_audit)) as $gerencia)
        {
            $gerencia_opcional[] = $gerencia['cd_gerencia']; 
        }

        $ds_gerencia_opcional = implode(',', $gerencia_opcional);

        $gerencia_opcional_grupo = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo(intval($cd_solic_fiscalizacao_audit)) as $grupo)
        {
            $gerencia_opcional_grupo[] = $grupo['ds_grupo']; 
        }

        $ds_gerencia_opcional_grupo = implode(',', $gerencia_opcional_grupo);

        $gestao = array();

        foreach ($this->solic_fiscalizacao_audit_model->get_tipo_gerencia(intval($row['cd_solic_fiscalizacao_audit_tipo'])) as $gerencia)
        {
            $gestao[] = $gerencia['cd_gerencia'];
        }

        $ds_gestao = implode(',', $gestao);
                        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');               
        $ob_pdf->SetNrPagDe(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = 'Registro de Solicitações, Fiscalizações e Auditorias';
      
        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 13);

        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Ano/Nº: ', '0','L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_ano_numero'], '0', '');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        //origem
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Origem: ', '0','L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_solic_fiscalizacao_audit_origem'], '0', '');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        //especificar origem
        if(trim($row['ds_origem']) != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Especificar origem: ', '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 6);

            $ob_pdf->SetFont('segoeuil', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_origem']);

            $ob_pdf->SetY($ob_pdf->GetY() + 6);    
        }
      
        //data recebimento
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Data Recebimento: ', '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['dt_recebimento']);

        $ob_pdf->SetY($ob_pdf->GetY() + 6);
      
        //tipo
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Tipo: ', '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_solic_fiscalizacao_audit_tipo']);

        $ob_pdf->SetY($ob_pdf->GetY() + 6); 
       
        //tipo especificado
        if(trim($row['ds_tipo']) != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Especificar tipo: ', '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 6);

            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_tipo']);

            $ob_pdf->SetY($ob_pdf->GetY() + 6);
        } 
      
        //area consolidadora
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Área Consolidadora: ', '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['cd_gerencia']);

        $ob_pdf->SetY($ob_pdf->GetY() + 6); 
     
        //gestao
        if($ds_gestao != '')
        {      
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Gestão: ', '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 6);

            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), $ds_gestao);

            $ob_pdf->SetY($ob_pdf->GetY() + 6); 
        }
        /*
        //envio opcional
        if($ds_gerencia_opcional != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Envio opcional: ', '0', 'L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->Text(45, $ob_pdf->GetY(), $ds_gerencia_opcional);
            $ob_pdf->SetY($ob_pdf->GetY() + 6);
        }

        //envio opcional grupo
        if($ds_gerencia_opcional_grupo != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Envio opcional(Grupo): ', '0', 'L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->Text(45, $ob_pdf->GetY(), $ds_gerencia_opcional_grupo);
            $ob_pdf->SetY($ob_pdf->GetY() + 6);
        }
        */
        //nome
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Documento: ', '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_documento']);

        $ob_pdf->SetY($ob_pdf->GetY() + 6);        
      
        //teor
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Teor: ', '0', 'L');

        $ob_pdf->SetY($ob_pdf->GetY() + 6);

        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), $row['ds_teor']);

        $ob_pdf->SetY($ob_pdf->GetY() + 6); 
      
        //prazo para providencias
        if(trim($row['dt_prazo']) != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Prazo: ', '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 6);

            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), $row['dt_prazo']);

            $ob_pdf->SetY($ob_pdf->GetY() + 6);                 
        }

        if(trim($row['arquivo']) != '' AND trim($row['arquivo_nome']) != '')
        {
            $ob_pdf->SetFont('segoeuib', '', 13);
            $ob_pdf->Text(10, $ob_pdf->GetY(), 'Arquivo: ', '0', 'L');


            $ob_pdf->SetY($ob_pdf->GetY() + 6);

            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->SetTextColor(50,50,220);
            $ob_pdf->Write(-3, $row['arquivo_nome'], base_url().'up/solic_fiscalizacao_audit/'.$row['arquivo']);
            $ob_pdf->SetY($ob_pdf->GetY() + 6);   
        }

        $ob_pdf->SetTextColor(0, 86, 0);

        /*
        //data inclusao
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Data inclusão: ', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(43, $ob_pdf->GetY(), $row['dt_inclusao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 6); 

        //usuario
        $ob_pdf->SetFont('segoeuib', '', 13);
        $ob_pdf->Text(10, $ob_pdf->GetY(), 'Usuário: ', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',13);
        $ob_pdf->Text(29, $ob_pdf->GetY(), $row['ds_usuario_inclusao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 6);
        */

        $ob_pdf->Output('./up/solic_fiscalizacao_audit/'.str_replace('/', '_', $row['ds_ano_numero']).'.pdf');
    }

    public function prorrogacao($cd_solic_fiscalizacao_audit)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['row'] = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        $data['fl_permissao_prorrogacao'] = FALSE;
        $data['fl_permissao_confirmacao'] = $this->get_permissao();

        if(trim($data['row']['cd_gerencia']) == $this->session->userdata('divisao'))
        {
            $data['fl_permissao_prorrogacao'] = TRUE;
        }

        if($this->get_permissao_acao($data['row']))
        {
            $this->load->view('atividade/solic_fiscalizacao_audit/prorrogacao', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_prorrogacao()
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);

        $args = array(
            'dt_solicitacao_prorrogacao' => $this->input->post('dt_solicitacao_prorrogacao', TRUE),
            'ds_solicitacao_prorrogacao' => $this->input->post('ds_solicitacao_prorrogacao', TRUE),
            'arquivo_minuta'             => $this->input->post('arquivo_minuta', TRUE),
            'arquivo_minuta_nome'        => $this->input->post('arquivo_minuta_nome', TRUE),
            'cd_usuario'                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->solicitar_prorrogacao($cd_solic_fiscalizacao_audit, $args);

        $ds_solic_fiscalizacao_audit_acompanhamento = 'Solicitação de Prorrogação do Prazo para : '.$this->input->post('dt_solicitacao_prorrogacao', TRUE)."\n\n";

        if($this->input->post('ds_solicitacao_prorrogacao', TRUE) != '')
        {
            $ds_solic_fiscalizacao_audit_acompanhamento .= 'Descrição da Prorrogação : '.$this->input->post('ds_solicitacao_prorrogacao', TRUE);
        }

        $args = array(
            'cd_solic_fiscalizacao_audit'                => $cd_solic_fiscalizacao_audit,
            'ds_solic_fiscalizacao_audit_acompanhamento' => $ds_solic_fiscalizacao_audit_acompanhamento,
            'cd_usuario'                                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_acompanhamento($args);

        $cd_evento = 306;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);  

        $tags = array('[DS_NOME_DOCUMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[LINK]');

        $subs = array(
            $row['ds_documento'],
            $row['ds_solic_fiscalizacao_audit_origem'],
            $row['ds_solic_fiscalizacao_audit_tipo'],
            site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($cd_solic_fiscalizacao_audit))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
    
        redirect('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    public function confirma_prorrogacao()
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
        $fl_confirmacao              = $this->input->post('fl_confirmacao', TRUE);

        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        if(trim($fl_confirmacao) == 'S')
        {
            $args = array(
                'dt_prazo_porrogado'  => $this->input->post('dt_prazo_porrogado', TRUE),
                'arquivo_pedido'      => $this->input->post('arquivo_pedido', TRUE),
                'arquivo_pedido_nome' => $this->input->post('arquivo_pedido_nome', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );
            /*
            $cd_liquid = $this->gera_arquivo_liquid(
                $row['nr_ano'], 
                $row['nr_numero'], 
                $args['arquivo_pedido'], 
                $args['arquivo_pedido_nome']
            );
            */

            //$args['cd_liquid'] = $cd_liquid;
            $args['cd_liquid'] = 0;

            $this->solic_fiscalizacao_audit_model->confirma_prorrogacao($cd_solic_fiscalizacao_audit, $args);

            $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

            $ds_solic_fiscalizacao_audit_acompanhamento = 'Confirmada a prorrogação do prazo de '.$row['dt_prazo_antes'].' para '.$row['dt_prazo_depois'];

            $this->envia_email_confirma_prorrogacao($row);
        }
        else
        {
            $this->solic_fiscalizacao_audit_model->nao_confirma_prorrogacao($cd_solic_fiscalizacao_audit, $this->session->userdata('codigo'));

            $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

            $ds_solic_fiscalizacao_audit_acompanhamento = 'Não Confirmada a prorrogação do prazo.'."\n\n";
            $ds_solic_fiscalizacao_audit_acompanhamento .= 'Descrição : '.$this->input->post('ds_confirmacao_prorrogacao', TRUE);

            $this->envia_email_nao_confirma_prorrogacao($row);
        }

        $args = array(
            'cd_solic_fiscalizacao_audit'                => $cd_solic_fiscalizacao_audit,
            'ds_solic_fiscalizacao_audit_acompanhamento' => $ds_solic_fiscalizacao_audit_acompanhamento,
            'cd_usuario'                                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_acompanhamento($args);

        redirect('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    private function envia_email_confirma_prorrogacao($solic_fiscalizacao_audit)
    {
        $cd_evento = 317;

        $email = $this->eventos_email_model->carrega($cd_evento);      

        $tags = array('[DT_PRAZO]', '[DS_NOME_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[LINK]');

        $subs = array(

            $solic_fiscalizacao_audit['dt_prazo_porrogado'],
            $solic_fiscalizacao_audit['ds_documento'],
            $solic_fiscalizacao_audit['dt_recebimento'],
            $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_origem'],
            $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_tipo'],
            site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower($solic_fiscalizacao_audit['cd_gerencia']).'@eletroceee.com.br',  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    private function envia_email_nao_confirma_prorrogacao($solic_fiscalizacao_audit)
    {
        $cd_evento = 318;

        $email = $this->eventos_email_model->carrega($cd_evento);        

        $tags = array('[DS_NOME_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[LINK]');

        $subs = array(
            $solic_fiscalizacao_audit['ds_documento'],
            $solic_fiscalizacao_audit['dt_recebimento'],
            $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_origem'],
            $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_tipo'],
            site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower($solic_fiscalizacao_audit['cd_gerencia']).'@eletroceee.com.br',  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function acompanhamento($cd_solic_fiscalizacao_audit)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['row'] = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_acao($data['row']))
        {
            $data['collection'] = $this->solic_fiscalizacao_audit_model->listar_acompanhamento(intval($cd_solic_fiscalizacao_audit));

            $this->load->view('atividade/solic_fiscalizacao_audit/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_acompanahmento()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);

        $args = array(
            'cd_solic_fiscalizacao_audit'                => $cd_solic_fiscalizacao_audit,
            'ds_solic_fiscalizacao_audit_acompanhamento' => $this->input->post('ds_solic_fiscalizacao_audit_acompanhamento', TRUE),
            'cd_usuario'                                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_acompanhamento($args);

        redirect('atividade/solic_fiscalizacao_audit/acompanhamento/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    public function documentacao($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao = 0)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['row'] = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_acao($data['row']) AND trim($data['row']['dt_envio']) != '')
        {
            $data['gerencia'] = $this->solic_fiscalizacao_audit_model->get_gerencia();

            $data['collection'] = $this->solic_fiscalizacao_audit_model->listar_documentacao(intval($cd_solic_fiscalizacao_audit));

            foreach ($data['collection'] as $key => $item) 
            {
                $usuario = $this->solic_fiscalizacao_audit_model->get_usuario_responsavel(intval($item['cd_solic_fiscalizacao_audit_documentacao']));

                if(count($usuario) > 0)
                {
                    foreach ($usuario as $key2 => $item2) 
                    {
                        $data['collection'][$key]['responsavel'][] = $item2['ds_usuario'];
                    }
                }
                else
                {
                    $data['collection'][$key]['responsavel'][] = $item['cd_gerencia'];
                }

                $data['collection'][$key]['gerencia_apoio'] = array();
                $gerencia_apoio = $this->solic_fiscalizacao_audit_model->get_gerencia_apoio($item['cd_solic_fiscalizacao_audit_documentacao']);

                foreach ($gerencia_apoio as $key2 => $item2) 
                {
                    $data['collection'][$key]['gerencia_apoio'][] = $item2['cd_gerencia'];
                }
            }

            $data['usuario_responsavel'] = array();

            if(intval($cd_solic_fiscalizacao_audit_documentacao) == 0)
            {
                $item = $this->solic_fiscalizacao_audit_model->get_next_documentacao($cd_solic_fiscalizacao_audit);

                $data['documentacao'] = array(
                    'cd_solic_fiscalizacao_audit_documentacao' => intval($cd_solic_fiscalizacao_audit_documentacao),
                    'ds_solic_fiscalizacao_audit_documentacao' => '',
                    'nr_item'                                  => $item['nr_item'],
                    'cd_gerencia'                              => '',
                    'dt_prazo_retorno'                         => $item['dt_prazo_retorno']
                ); 

                $data['usuario'] = array();
            }
            else
            {
                $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

                $data['usuario'] = $this->solic_fiscalizacao_audit_model->get_usuario($data['documentacao']['cd_gerencia']);

                foreach ($this->solic_fiscalizacao_audit_model->get_usuario_responsavel(intval($cd_solic_fiscalizacao_audit_documentacao)) as $key => $item) 
                {
                    $data['usuario_responsavel'][] = $item['cd_usuario'];
                }
            }

            $data['fl_atendimento'] = FALSE;

            if(
                intval($data['row']['tl_documento']) > 0 
                AND 
                intval($data['row']['tl_documento_atendido']) == intval($data['row']['tl_documento'])
                AND
                trim($data['row']['cd_gerencia']) == trim($this->session->userdata('divisao'))
            )
            {
                $data['fl_atendimento'] = TRUE;
            }

            $this->load->view('atividade/solic_fiscalizacao_audit/documentacao', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_documentacao()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_area_consolidadora(trim($row['cd_gerencia'])))
        {
            $cd_solic_fiscalizacao_audit_documentacao = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

            $args = array(
                'cd_solic_fiscalizacao_audit'              => $cd_solic_fiscalizacao_audit,
                'ds_solic_fiscalizacao_audit_documentacao' => $this->input->post('ds_solic_fiscalizacao_audit_documentacao', TRUE),
                'nr_item'                                  => $this->input->post('nr_item', TRUE),
                'cd_gerencia'                              => $this->input->post('cd_gerencia', TRUE),
                'dt_prazo_retorno'                         => $this->input->post('dt_prazo_retorno', TRUE),
                'usuario'                                  => (is_array($this->input->post('usuario', TRUE)) ? $this->input->post('usuario', TRUE) : array()),
                'cd_usuario'                               => $this->session->userdata('codigo'),
                'fl_verificar_gerencia'                    => 'N'
            );

            if(intval($cd_solic_fiscalizacao_audit_documentacao) == 0)
            {
                if(trim($row['cd_gerencia']) == 'GC' AND in_array($row['cd_solic_fiscalizacao_audit_origem'], array(1,2,4)))
                {
                    $args['fl_verificar_gerencia'] = 'S';
                }

                $cd_solic_fiscalizacao_audit_documentacao = $this->solic_fiscalizacao_audit_model->salvar_documentacao($args);
            }
            else
            {
                $documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

                if(trim($documentacao['fl_verificar_gerencia']) == 'S' AND trim($documentacao['fl_confirma_gerencia']) == 'N')
                {
                    $args['fl_confirma_gerencia'] = '';
                }
                else
                {
                    $args['fl_confirma_gerencia'] = $documentacao['fl_confirma_gerencia'];
                }

                $this->solic_fiscalizacao_audit_model->atualizar_documentacao($cd_solic_fiscalizacao_audit_documentacao, $args);
            }

            redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit));
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_documentacao($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $this->solic_fiscalizacao_audit_model->excluir_documentacao($cd_solic_fiscalizacao_audit_documentacao, $this->session->userdata('codigo'));

        redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit));
    }

    public function get_usuario()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array();

        foreach($this->solic_fiscalizacao_audit_model->get_usuario($this->input->post('cd_gerencia', TRUE)) as $item)
        {
            $data[] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }
    
        echo json_encode($data);
    }

    public function enviar_solicitacao()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit           = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
        $solic_fiscalizacao_audit_documentacao = $this->input->post('solic_fiscalizacao_audit_documentacao', TRUE);

        $solic_fiscalizacao_audit_documentacao = explode(",", $solic_fiscalizacao_audit_documentacao);

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_area_consolidadora(trim($row['cd_gerencia'])))
        {
            $this->solic_fiscalizacao_audit_model->enviar_solicitacao(
                $cd_solic_fiscalizacao_audit, 
                $this->session->userdata('codigo')
            );

            foreach ($solic_fiscalizacao_audit_documentacao as $item)
            {
                $this->solic_fiscalizacao_audit_model->salvar_envio_solicitacao(
                    $item,
                    $this->session->userdata('codigo')
                );

                $this->email_solicitacao_documento($cd_solic_fiscalizacao_audit, $item);
            }

            redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit));
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_solicitacao_documento($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 347;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao(intval($cd_solic_fiscalizacao_audit_documentacao));

        $responsavel = array();

        $usuario = $this->solic_fiscalizacao_audit_model->get_usuario_responsavel(intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']));

        if(count($usuario) > 0)
        {
            foreach ($usuario as $key2 => $item2) 
            {
                $responsavel[] = $item2['ds_usuario_email'];
            }
        }
        else
        {
            $responsavel[] = $documentacao['cd_gerencia'].'@eletroceee.com.br';
        }

        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        $row['ext'] = pathinfo($row['arquivo'], PATHINFO_EXTENSION);

        $ds_info_email = 'para providências';
        $ds_assunto = 'Solicitação de Documentação';

        if(trim($documentacao['fl_verificar_gerencia']) == 'S')
        {
            $ds_info_email = 'para validação de competência desta Gerência';
            $ds_assunto = 'Validar Competência';
        }

        $tags = array('[DS_INFO_EMAIL]','[DS_DOCUMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[DS_DESCRICAO_ITEM]', '[DT_PRAZO]', '[LINK]');

        $subs = array(
            $ds_info_email,
            $row['ds_documento'],
            $row['ds_solic_fiscalizacao_audit_origem'],
            $row['ds_solic_fiscalizacao_audit_tipo'],
            $documentacao['ds_solic_fiscalizacao_audit_documentacao'],
            $documentacao['dt_prazo_retorno'],
            site_url('atividade/solic_fiscalizacao_audit/responder/'.intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => str_replace('[DS_ASSUNTO]', $ds_assunto, $email['assunto']),
            'para'    => strtolower(implode(';', $responsavel)),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $dir = '../cieprev/up/solic_fiscalizacao_audit';

        $file = $dir.'/'.$row['arquivo'];

        $anexo[0] = array(
            //'arquivo'      => $this->get_base64_liquid($solic_fiscalizacao_audit['cd_liquid'], $solic_fiscalizacao_audit['ext']),
            'arquivo'      => base64_encode(file_get_contents($file)),
            'arquivo_nome' => $row['arquivo_nome']
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args, $anexo);   
    }

    public function atendeu($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_area_consolidadora(trim($row['cd_gerencia'])))
        {
            $this->solic_fiscalizacao_audit_model->atendeu(
                intval($cd_solic_fiscalizacao_audit_documentacao), 
                $this->session->userdata('codigo')
            );

            redirect('atividade/solic_fiscalizacao_audit/documentos/'.intval($cd_solic_fiscalizacao_audit).'/'.intval($cd_solic_fiscalizacao_audit_documentacao));
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function reabrir_atendimento($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        if($this->get_permissao_area_consolidadora(trim($row['cd_gerencia'])))
        {
            $this->solic_fiscalizacao_audit_model->reabrir_atendimento(
                intval($cd_solic_fiscalizacao_audit_documentacao)
            );

            redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit));
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }


    public function minhas()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
           'origem' => $this->solic_fiscalizacao_audit_model->get_origem(),
           'tipo'   => array()
        );

        $agrupamento = $this->solic_fiscalizacao_audit_model->get_agrupamento();

        foreach ($agrupamento as $key => $item) 
        {
            $tipo = $this->solic_fiscalizacao_audit_model->get_tipo(0, $item['cd_solic_fiscalizacao_audit_tipo_agrupamento']);

            $data['tipo'][] = array(
                'value' => $item['value'],
                'text'  => $tipo
            );
        } 

        $this->load->view('atividade/solic_fiscalizacao_audit/minhas', $data);
    }

    public function minhas_listar()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $args = array(
            'nr_ano'                             => $this->input->post('nr_ano', TRUE),
            'nr_numero'                          => $this->input->post('nr_numero', TRUE),
            'cd_solic_fiscalizacao_audit_origem' => $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE),
            'cd_solic_fiscalizacao_audit_tipo'   => $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE),
            'dt_prazo_ini'                       => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'                       => $this->input->post('dt_prazo_fim', TRUE),
            'cd_gerencia_usuario'                => $this->session->userdata('divisao'), 
            'status'                             => $this->input->post('status', TRUE) 
        );

        manter_filtros($args);

        $data['collection'] = $this->solic_fiscalizacao_audit_model->minhas_listar($this->session->userdata('codigo'), $this->session->userdata('divisao'), $args);

        $this->load->view('atividade/solic_fiscalizacao_audit/minhas_listar', $data);
    }

    private function permissao_responder_documento($documentacao)
    {
        $fl_permissao = FALSE;

        $usuario = $this->solic_fiscalizacao_audit_model->get_usuario_responsavel(
            intval($documentacao['cd_solic_fiscalizacao_audit_documentacao'])
        );

        if(count($usuario) > 0)
        {
            $usuario_responsavel = array();

            foreach ($usuario as $key => $item) 
            {
                $usuario_responsavel[] = $item['cd_usuario'];
            }

            if(in_array($this->session->userdata('codigo'), $usuario_responsavel))
            {
                $fl_permissao = TRUE;
            }
        }
        else if($this->session->userdata('divisao') == trim($documentacao['cd_gerencia']))
        {
            $fl_permissao = TRUE;
        }

        return $fl_permissao;
    }

    public function responder($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->permissao_responder_documento($data['documentacao']))
        {
            $cd_solic_fiscalizacao_audit = $data['documentacao']['cd_solic_fiscalizacao_audit'];

            $data['row']        = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

            if(trim($data['documentacao']['fl_verificar_gerencia']) == 'N' OR trim($data['documentacao']['fl_confirma_gerencia']) == 'S')
            {
                $data['collection'] = $this->solic_fiscalizacao_audit_model->listar_documento($cd_solic_fiscalizacao_audit_documentacao);

                if($this->session->userdata('tipo') == 'G' OR $this->session->userdata('divisao') == 'AI')
                {
                    $data['usuario_conferente'] = $this->solic_fiscalizacao_audit_model->get_usuario(
                        $data['documentacao']['cd_gerencia'], 'S', 'N'
                    );

                    $data['cd_usuario_conferente'] = '';
                }
                else
                {
                    $data['usuario_conferente'] = $this->solic_fiscalizacao_audit_model->get_usuario(
                        $data['documentacao']['cd_gerencia']
                    );

                    $data['cd_usuario_conferente'] = $data['documentacao']['cd_usuario_gerente'];
                }

                if($this->session->userdata('indic_01') == 'S' OR $this->session->userdata('divisao') == 'AI')
                {
                    $data['usuario_sub_conferente'] = $this->solic_fiscalizacao_audit_model->get_usuario(
                        $data['documentacao']['cd_gerencia'], 'N', 'S'
                    );

                    $data['cd_usuario_sub_conferente'] = '';
                }
                else
                {
                    $data['usuario_sub_conferente'] = $this->solic_fiscalizacao_audit_model->get_usuario(
                        $data['documentacao']['cd_gerencia']
                    );
                
                    $data['cd_usuario_sub_conferente'] = $data['documentacao']['cd_usuario_substituto'];
                }

                $this->load->view('atividade/solic_fiscalizacao_audit/responder', $data);
            }
            else if(trim($data['documentacao']['fl_verificar_gerencia']) == 'S' AND trim($data['documentacao']['fl_confirma_gerencia']) == '')
            {
                $data['gerencia'] = $this->solic_fiscalizacao_audit_model->get_gerencia();

                $this->load->view('atividade/solic_fiscalizacao_audit/validar_gerencia', $data);
            }
            else if(trim($data['documentacao']['fl_verificar_gerencia']) == 'S' AND trim($data['documentacao']['fl_confirma_gerencia']) == 'N')
            {
                exibir_mensagem('AGUARDANDO A ÁREA CONSOLIDADORA');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function validar_gerencia()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit              = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
        $cd_solic_fiscalizacao_audit_documentacao = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

        $documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->permissao_responder_documento($documentacao))
        {
            $fl_confirma_gerencia = $this->input->post('fl_competencia', TRUE);
            $fl_apoio = '';
            $cd_gerencia = '';

            if(trim($fl_confirma_gerencia) == 'S')
            {
                $fl_apoio = $this->input->post('fl_apoio', TRUE);

                if(trim($fl_apoio) == 'S')
                {
                    $gerencia_apoio = (is_array($this->input->post('gerencia_apoio', TRUE)) ? $this->input->post('gerencia_apoio', TRUE) : array());

                    $this->solic_fiscalizacao_audit_model->set_gerencia_apoio($cd_solic_fiscalizacao_audit_documentacao, $gerencia_apoio, $this->session->userdata('codigo'));
                }
            }
            else
            {
                $this->load->model('projetos/eventos_email_model');
                
                $cd_gerencia = $this->input->post('cd_gerencia', TRUE);
            
                $cd_evento = 453;

                $email = $this->eventos_email_model->carrega($cd_evento);   
        
                $tags = array(
                    '[GERENCIA]',
                    '[DS_DESCRICAO_ITEM]',
                    '[NOVA_GERENCIA]', 
                    '[LINK]'
                );

                $subs = array(
                    $documentacao['cd_gerencia'],
                    $documentacao['ds_solic_fiscalizacao_audit_documentacao'],
                    $cd_gerencia,
                    site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit).'/'.intval($cd_solic_fiscalizacao_audit_documentacao))
                );

                $texto = str_replace($tags, $subs, $email['email']);

                $cd_usuario = $this->session->userdata('codigo');

                $args = array( 
                    'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
                    'assunto' => $email['assunto'],
                    'para'    => $email['para'],
                    'cc'      => $email['cc'],
                    'cco'     => $email['cco'],
                    'texto'   => $texto
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
            }

            $this->solic_fiscalizacao_audit_model->validar_gerencia($cd_solic_fiscalizacao_audit_documentacao, $fl_confirma_gerencia, $cd_gerencia, $this->session->userdata('codigo'));

            redirect('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }

    }

    public function anexar_documento()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit_documentacao = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

        $documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->permissao_responder_documento($documentacao))
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
                    
                    $this->solic_fiscalizacao_audit_model->anexar_documento(intval($cd_solic_fiscalizacao_audit_documentacao), $args);
                    
                    $nr_conta++;
                }
            }

            redirect('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_documento($cd_solic_fiscalizacao_audit_documentacao, $cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $this->solic_fiscalizacao_audit_model->excluir_documento(
            intval($cd_solic_fiscalizacao_audit_documentacao_anexo), 
            $this->session->userdata('codigo')
        );

        redirect('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao), 'refresh');
    }

    public function encaminhar_conferencia()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit_documentacao  = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

        $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->permissao_responder_documento($data['documentacao']))
        {
            $cd_solic_fiscalizacao_audit                 = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
            $solic_fiscalizacao_audit_documentacao_anexo = $this->input->post('solic_fiscalizacao_audit_documentacao_anexo', TRUE);

            $solic_fiscalizacao_audit_documentacao_anexo = explode(",", $solic_fiscalizacao_audit_documentacao_anexo);

            $args = array(
                'cd_usuario_conferente'     => $this->input->post('cd_usuario_conferente', TRUE), 
                'cd_usuario_sub_conferente' => $this->input->post('cd_usuario_sub_conferente', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo'),
            );
            
            $this->solic_fiscalizacao_audit_model->encaminhar_conferencia(
                $cd_solic_fiscalizacao_audit_documentacao, 
                $solic_fiscalizacao_audit_documentacao_anexo,
                $args,
                $data['documentacao']['fl_atendeu_conferencia']
            );
           
           $this->email_encaminhar_conferencia($cd_solic_fiscalizacao_audit_documentacao);

            redirect('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_encaminhar_conferencia($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 441;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $row = $this->solic_fiscalizacao_audit_model->carrega_documentacao(intval($cd_solic_fiscalizacao_audit_documentacao));

        $tags = array(
            '[DS_DOCUMENTO]',
            '[DS_DESCRICAO_ITEM]',
            '[DS_ORIGEM]', 
            '[DS_TIPO]', 
            '[NR_ITEM_DOCUMENTO]', 
            '[DS_ITEM_DOCUMENTO]', 
            '[DS_MOTIVO]', 
            '[DT_PRAZO]', 
            '[LINK]'
        );

        $subs = array(
            $row['ds_documento'],
            $row['ds_solic_fiscalizacao_audit_documentacao'],
            $row['ds_origem'],
            $row['ds_tipo'],
            $row['nr_item'],
            $row['ds_solic_fiscalizacao_audit_documentacao'],
            $row['ds_motivo_atendeu_conferencia'],
            $row['dt_prazo_retorno'],
            site_url('atividade/solic_fiscalizacao_audit/conferencia/'.intval($cd_solic_fiscalizacao_audit_documentacao))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => $row['ds_usuario_conferente'].'@eletroceee.com.br;'.$row['ds_usuario_sub_conferente'].'@eletroceee.com.br',
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
    }

    public function minhas_conferencia()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
           'origem' => $this->solic_fiscalizacao_audit_model->get_origem(),
           'tipo'   => array()
        );

        $agrupamento = $this->solic_fiscalizacao_audit_model->get_agrupamento();

        foreach ($agrupamento as $key => $item) 
        {
            $tipo = $this->solic_fiscalizacao_audit_model->get_tipo(0, $item['cd_solic_fiscalizacao_audit_tipo_agrupamento']);

            $data['tipo'][] = array(
                'value' => $item['value'],
                'text'  => $tipo
            );
        } 

        $this->load->view('atividade/solic_fiscalizacao_audit/minhas_conferencia', $data);
    }

    public function minhas_conferencia_listar()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $args = array(
            'nr_ano'                             => $this->input->post('nr_ano', TRUE),
            'nr_numero'                          => $this->input->post('nr_numero', TRUE),
            'cd_solic_fiscalizacao_audit_origem' => $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE),
            'cd_solic_fiscalizacao_audit_tipo'   => $this->input->post('cd_solic_fiscalizacao_audit_tipo', TRUE),
            'dt_prazo_ini'                       => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'                       => $this->input->post('dt_prazo_fim', TRUE),
            'cd_gerencia_usuario'                => $this->session->userdata('divisao'), 
            'status'                             => $this->input->post('status', TRUE) 
        );

        manter_filtros($args);

        $data['collection'] = $this->solic_fiscalizacao_audit_model->minhas_conferencia_listar($this->session->userdata('codigo'), $args);

        $this->load->view('atividade/solic_fiscalizacao_audit/minhas_conferencia_listar', $data);
    }

    public function conferencia($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->session->userdata('codigo') == intval($data['documentacao']['cd_usuario_conferente']) OR $this->session->userdata('codigo') == intval($data['documentacao']['cd_usuario_sub_conferente']))
        {
            $cd_solic_fiscalizacao_audit = $data['documentacao']['cd_solic_fiscalizacao_audit'];

            $data['row']        = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

            $data['collection'] = $this->solic_fiscalizacao_audit_model->listar_documento($cd_solic_fiscalizacao_audit_documentacao);


            $this->load->view('atividade/solic_fiscalizacao_audit/conferencia', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function atendimento_conferencia($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
            'row'          => $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit)),
            'documentacao' => $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao)
        );

        $this->load->view('atividade/solic_fiscalizacao_audit/atendimento_conferencia', $data);
    }

    public function salvar_atendimento_conferencia()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit              = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
        $cd_solic_fiscalizacao_audit_documentacao = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

        $args = array(
            'cd_solic_fiscalizacao_audit_documentacao' => $cd_solic_fiscalizacao_audit_documentacao,
            'ds_motivo_atendeu_conferencia'            => $this->input->post('ds_motivo_atendeu_conferencia', TRUE),
            'fl_atendeu_conferencia'                   => 'N',
            'cd_usuario'                               => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_atendimento_conferencia($cd_solic_fiscalizacao_audit_documentacao, $args);

        $ds_documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        $acompanhamento = array(
            'cd_solic_fiscalizacao_audit'                => $cd_solic_fiscalizacao_audit,
            'ds_solic_fiscalizacao_audit_acompanhamento' => 'Conferência da Documentação '.$ds_documentacao['nr_item'].' - '.$ds_documentacao['ds_solic_fiscalizacao_audit_documentacao'].' não foi atendida : '.$args['ds_motivo_atendeu_conferencia'],
            'cd_usuario'                                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_acompanhamento($acompanhamento);

        $this->envia_email_atendimento_conferencia($cd_solic_fiscalizacao_audit_documentacao);

        redirect('atividade/solic_fiscalizacao_audit/minhas_conferencia', 'refresh');
    }

    private function envia_email_atendimento_conferencia($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 440;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $row = $this->solic_fiscalizacao_audit_model->carrega_documentacao(intval($cd_solic_fiscalizacao_audit_documentacao));

        $responsavel = array();

        foreach ($this->solic_fiscalizacao_audit_model->get_usuario_responsavel($cd_solic_fiscalizacao_audit_documentacao) as $key => $item)
        {
            $responsavel[] = $item['ds_usuario_email'];
        }

        if(count($responsavel) == 0)
        {
            $responsavel[] = $row['cd_gerencia'].'@eletroceee.com.br';
        }

        $tags = array(
            '[DS_DOCUMENTO]', 
            '[DS_ORIGEM]', 
            '[DS_TIPO]', 
            '[NR_ITEM_DOCUMENTO]', 
            '[DS_ITEM_DOCUMENTO]', 
            '[DS_MOTIVO]', 
            '[DT_PRAZO]', 
            '[LINK]'
        );

        $subs = array(
            $row['ds_documento'],
            $row['ds_origem'],
            $row['ds_tipo'],
            $row['nr_item'],
            $row['ds_solic_fiscalizacao_audit_documentacao'],
            $row['ds_motivo_atendeu_conferencia'],
            $row['dt_prazo_retorno'],
            site_url('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower(implode(';', $responsavel)),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
    }

    public function encerrar_solicitacao_conferencia($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->session->userdata('codigo') == intval($data['documentacao']['cd_usuario_conferente']) OR $this->session->userdata('codigo') == intval($data['documentacao']['cd_usuario_sub_conferente']))
        {
             $this->solic_fiscalizacao_audit_model->encerrar_solicitacao(
                intval($cd_solic_fiscalizacao_audit_documentacao), 
                $this->session->userdata('codigo'),
                $data['documentacao']['fl_atendeu']
            );

            $this->solic_fiscalizacao_audit_model->encerrar_solicitacao_conferencia(
                intval($cd_solic_fiscalizacao_audit_documentacao), 
                $this->session->userdata('codigo')
            );

            $this->email_encerrar_solicitacao($data['documentacao']);

            redirect('atividade/solic_fiscalizacao_audit/minhas_conferencia', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function encerrar_solicitacao($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data['documentacao'] = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        if($this->permissao_responder_documento($data['documentacao']))
        {
            $this->solic_fiscalizacao_audit_model->encerrar_solicitacao(
                intval($cd_solic_fiscalizacao_audit_documentacao), 
                $this->session->userdata('codigo'),
                $data['documentacao']['fl_atendeu']
            );

            $this->email_encerrar_solicitacao($data['documentacao']);

            redirect('atividade/solic_fiscalizacao_audit/minhas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_encerrar_solicitacao($documentacao)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));
        
        $cd_evento = 354;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $resp_area_consolidadora = $this->solic_fiscalizacao_audit_model->resp_area_consolidadora($documentacao['cd_area_consolidadora']); 
        
        foreach ($resp_area_consolidadora as $key => $item)
        {
            $ds_usuario_email[] = $item['ds_usuario_email'];
        }

        $tags = array('[DS_DESCRICAO_ITEM]', '[DS_DOCUMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[LINK]');

        $subs = array(
            $documentacao['nr_item'].' - '.$documentacao['ds_solic_fiscalizacao_audit_documentacao'],
            $documentacao['ds_documento'],
            $documentacao['ds_origem'],
            $documentacao['ds_tipo'],
            site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($documentacao['cd_solic_fiscalizacao_audit']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => implode(";", $ds_usuario_email),  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
    }

    public function documentos($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
            'collection'   => $this->solic_fiscalizacao_audit_model->listar_documentos($cd_solic_fiscalizacao_audit_documentacao),
            'row'          => $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit)),
            'documentacao' => $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao)
        );

        $this->load->view('atividade/solic_fiscalizacao_audit/documentos', $data);
    }

    public function salvar_encaminhar_documento()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit_documentacao_anexo = $this->input->post('cd_solic_fiscalizacao_audit_documentacao_anexo', TRUE);
        $fl_salvar                                      = $this->input->post('fl_salvar', TRUE);

        $this->solic_fiscalizacao_audit_model->salvar_encaminhar_documento($cd_solic_fiscalizacao_audit_documentacao_anexo, $fl_salvar, $this->session->userdata('codigo'));
    }

    public function encerra_documentacao($cd_solic_fiscalizacao_audit)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        $anexos = $this->solic_fiscalizacao_audit_model->get_documentos_encerrados($cd_solic_fiscalizacao_audit);

        foreach ($anexos as $key => $item) 
        {
            //echo $item['nr_item'].' '.$item['arquivo_nome'].br();
            /*
            $cd_liquid = $this->gera_arquivo_liquid(
                intval($row['nr_ano']), 
                intval($row['nr_numero']), 
                $item['arquivo'], 
                $item['arquivo_nome'], 
                $item['nr_item']
            );
            */

            $this->solic_fiscalizacao_audit_model->encerra_documentacao($cd_solic_fiscalizacao_audit, $this->session->userdata('codigo'));
            /*
            if(intval($cd_liquid) > 0)
            {
                $this->solic_fiscalizacao_audit_model->atualiza_documentacao_anexo_liquid($item['cd_solic_fiscalizacao_audit_documentacao_anexo'], $cd_liquid);
            }
            */
        }

        $this->email_encerra_solicitacao_area_consolidadora($cd_solic_fiscalizacao_audit);

        redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    public function reabrir_documentacao($cd_solic_fiscalizacao_audit)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

         $this->solic_fiscalizacao_audit_model->reabrir_documentacao($cd_solic_fiscalizacao_audit, $this->session->userdata('codigo'));

        redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    private function email_encerra_solicitacao_area_consolidadora($cd_solic_fiscalizacao_audit)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 366;

        $email = $this->eventos_email_model->carrega($cd_evento);   

        $row = $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit));

        $ds_email_area_consolidadora = '';

        if(trim($row['cd_gerencia']) != '')
        {
        	$ds_email_area_consolidadora = ';'.strtolower($row['cd_gerencia']).'@eletroceee.com.br';
        }

        $tags = array('[DS_DOCUMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[DT_PRAZO]', '[LINK]');

        $subs = array(
            $row['ds_documento'],
            $row['ds_solic_fiscalizacao_audit_origem'],
            $row['ds_solic_fiscalizacao_audit_tipo'],
            $row['dt_prazo'],
            site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($cd_solic_fiscalizacao_audit))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => $email['para'].$ds_email_area_consolidadora,
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);   
    }

    public function remover_documento($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao, $cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $this->solic_fiscalizacao_audit_model->remover_documento(
            $this->session->userdata('codigo'), 
            $cd_solic_fiscalizacao_audit_documentacao_anexo
        );

        redirect('atividade/solic_fiscalizacao_audit/documentos/'.intval($cd_solic_fiscalizacao_audit).'/'.$cd_solic_fiscalizacao_audit_documentacao, 'refresh');
    }

    public function atendimento($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $data = array(
            'row'          => $this->get_cadastro($this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit)),
            'documentacao' => $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao)
        );

        $this->load->view('atividade/solic_fiscalizacao_audit/atendimento', $data);
    }

    public function salvar_atendimento()
    {
        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $cd_solic_fiscalizacao_audit              = $this->input->post('cd_solic_fiscalizacao_audit', TRUE);
        $cd_solic_fiscalizacao_audit_documentacao = $this->input->post('cd_solic_fiscalizacao_audit_documentacao', TRUE);

        $args = array(
            'cd_solic_fiscalizacao_audit_documentacao' => $cd_solic_fiscalizacao_audit_documentacao,
            'ds_motivo_atendeu'                        => $this->input->post('ds_motivo_atendeu', TRUE),
            'dt_prorrogacao_prazo_retorno'             => $this->input->post('dt_prorrogacao_prazo_retorno', TRUE),
            'fl_atendeu'                               => 'N',
            'cd_usuario'                               => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_atendimento($cd_solic_fiscalizacao_audit_documentacao, $args);

        $ds_documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao);

        $acompanhamento = array(
            'cd_solic_fiscalizacao_audit'                => $cd_solic_fiscalizacao_audit,
            'ds_solic_fiscalizacao_audit_acompanhamento' => 'Solicitação da Documentação '.$ds_documentacao['nr_item'].' - '.$ds_documentacao['ds_solic_fiscalizacao_audit_documentacao'].' não foi atendida : '.$args['ds_motivo_atendeu'],
            'cd_usuario'                                 => $this->session->userdata('codigo')
        );

        $this->solic_fiscalizacao_audit_model->salvar_acompanhamento($acompanhamento);

        $this->envia_email_atendimento($cd_solic_fiscalizacao_audit_documentacao);

        redirect('atividade/solic_fiscalizacao_audit/documentacao/'.intval($cd_solic_fiscalizacao_audit), 'refresh');
    }

    private function envia_email_atendimento($cd_solic_fiscalizacao_audit_documentacao)
    {
        $this->load->model(array(
            'projetos/solic_fiscalizacao_audit_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 365;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $row = $this->solic_fiscalizacao_audit_model->carrega_documentacao(intval($cd_solic_fiscalizacao_audit_documentacao));

        $responsavel = array();

        foreach ($this->solic_fiscalizacao_audit_model->get_usuario_responsavel($cd_solic_fiscalizacao_audit_documentacao) as $key => $item)
        {
            $responsavel[] = $item['ds_usuario_email'];
        }

        if(count($responsavel) == 0)
        {
            $responsavel[] = $row['cd_gerencia'].'@eletroceee.com.br';
        }

        $tags = array(
            '[DS_DOCUMENTO]', 
            '[DS_ORIGEM]', 
            '[DS_TIPO]', 
            '[NR_ITEM_DOCUMENTO]', 
            '[DS_ITEM_DOCUMENTO]', 
            '[DS_MOTIVO]', 
            '[DT_PRAZO]', 
            '[LINK]'
        );

        $subs = array(
            $row['ds_documento'],
            $row['ds_origem'],
            $row['ds_tipo'],
            $row['nr_item'],
            $row['ds_solic_fiscalizacao_audit_documentacao'],
            $row['ds_motivo_atendeu'],
            $row['dt_prazo_retorno'],
            site_url('atividade/solic_fiscalizacao_audit/responder/'.intval($cd_solic_fiscalizacao_audit_documentacao))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => strtolower(implode(';', $responsavel)),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
    }


    public function zip($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        ini_set('max_execution_time', 0);

        $this->load->library('zip');

        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $dir = '../cieprev/up/solic_fiscalizacao_audit/';

        $dir_tmp = '../cieprev/up/solic_fiscalizacao_audit/';

        $collection = $this->solic_fiscalizacao_audit_model->get_documentos($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao);

        foreach ($collection as $key => $doc) 
        {
            $arquivo = $doc['arquivo'];

            $dir_tmp = '../cieprev/up/solic_fiscalizacao_audit/'.$doc['nr_item'].'/';
            if(!is_dir($dir_tmp))
            {
                mkdir($dir_tmp);
            }   

            copy($dir.'/'.$arquivo, $dir_tmp.'/'.$doc['arquivo_nome_zip']);

            $this->zip->read_file($dir_tmp.'/'.$doc['arquivo_nome_zip']);
            @unlink($dir_tmp.'/'.$doc['arquivo_nome_zip']);
        }

        if(is_dir($dir_tmp))
        {
            @rmdir($dir_tmp);
        }

        $this->zip->download($cd_solic_fiscalizacao_audit.'.zip');
    }

    public function zip_multiplo($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
        ini_set('max_execution_time', 0);

        $this->load->library('zip');

        $this->load->model('projetos/solic_fiscalizacao_audit_model');

        $dir = '../cieprev/up/solic_fiscalizacao_audit/';

        $dir_tmp = '../cieprev/up/solic_fiscalizacao_audit/';

        $collection = $this->solic_fiscalizacao_audit_model->get_documentos($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao);

        $tl_documentos = count($collection);

        $arr_zip = array();

        //$div_documentos = invtal($tl_documentos / 20);

        $i = 1;
        $x = 1;

        foreach ($collection as $key => $doc) 
        {
            $arquivo = $doc['arquivo'];
                
            $dir_tmp = '../cieprev/up/solic_fiscalizacao_audit/'.$cd_solic_fiscalizacao_audit.'_'.$doc['nr_item'];
            if(!is_dir($dir_tmp))
            {
                mkdir($dir_tmp);
            }   

            copy($dir.'/'.$arquivo, $dir_tmp.'/'.$doc['arquivo_nome_zip']);

            $this->zip->read_file($dir_tmp.'/'.$doc['arquivo_nome_zip']);
            @unlink($dir_tmp.'/'.$doc['arquivo_nome_zip']);

            if($x >= 20 OR count($collection) == intval($key)+1)
            {
                $arr_zip[] = $dir_tmp.'/'.$cd_solic_fiscalizacao_audit.'_'.$i.'.zip';
                $this->zip->zip_save($dir_tmp.'/', $cd_solic_fiscalizacao_audit.'_'.$i.'.zip');
                $this->zip->clear_data();
                $i++;
                $x = 1;
            }
            $x++;
        }

        $this->zip->clear_data();

        $ii = 1;

        foreach ($arr_zip as $key => $zip) 
        {
            $zip = str_replace('cieprev/', '', $zip);
            echo '<a href="'.site_url($zip).'" target="_blank">zip: '.$ii.'</a><br/>';
            $this->zip->read_file($zip);

            $ii++;
           ## @unlink($zip);
        }

        /*
        if(is_dir($dir_tmp))
        {
            @rmdir($dir_tmp);
        }

        $this->zip->download($cd_solic_fiscalizacao_audit.'.zip');
        */

    }
}