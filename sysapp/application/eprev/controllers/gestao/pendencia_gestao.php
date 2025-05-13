<?php
class Pendencia_gestao extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_gerencia_responsavel($cd_pendencia_gestao)
    {
        $responsavel = $this->pendencia_gestao_model->get_gerencia_pendencia_gestao_gerencia(intval($cd_pendencia_gestao));

        $responsavel_array = array();

        foreach ($responsavel as $item) 
        {
            $responsavel_array[] = $item['cd_gerencia'];
        }

        return $responsavel_array;
    }

    private function get_permissao_encerrar($cd_reuniao_sistema_gestao_tipo)
    {
        if($cd_reuniao_sistema_gestao_tipo == 49)
        {
            return $this->get_usuario_comite_apuracao_resp();
        }
        else
        {
            //RCOLIVEIRA
            if(($this->session->userdata('codigo')) == 78)
            {
                return 'S';
            }
            //ANUNES
            else if(($this->session->userdata('codigo')) == 26)
            {
                return 'S';
            }
            //RAQUELR
            else if(($this->session->userdata('codigo')) == 515)
            {
                return 'S';
            }			
            //JSEIDLER
            else if(($this->session->userdata('codigo')) == 298)
            {
                return 'S';
            }
            else if(intval($cd_reuniao_sistema_gestao_tipo) == 46)
            {
                //JFETTER
                if(($this->session->userdata('codigo')) == 132)
                {
                    return 'S';
                }
                //CGONCALVES
                else if(($this->session->userdata('codigo')) == 118)
                {
                    return 'S';
                }
                //LUCIOS
                else if(($this->session->userdata('codigo')) == 415)
                {
                    return 'S';
                }
                else
                {
                    return 'N';
                }
            }
            else if(gerencia_in(array('GC')))
            {
                if(in_array(intval($cd_reuniao_sistema_gestao_tipo), array(11, 12, 13, 14)))
                {
                    return 'S';
                }
                else
                {
                    return 'N';
                }
            }
            else if(gerencia_in(array('AI')))
            {
                if(intval($cd_reuniao_sistema_gestao_tipo) == 24)
                {
                    return 'S';
                }
                else
                {
                    return 'N';
                }
            }
            else
            {
                return 'N';
            }
        }
    }

    private function get_usuario_comite_apuracao_resp()
    {
        $usuario = array(
            3, ##Adriana Espindola da Silva Reichmann##
            362, ##Carla Gomes da Silva##
            424, ##Vanessa Silva Alves##
        );

        if(in_array(intval($this->session->userdata('codigo')), $usuario))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function get_perm_comite_apuracao_resp($cd_reuniao_sistema_gestao_tipo)
    {
        if(intval($cd_reuniao_sistema_gestao_tipo) == 49)
        {
            return $this->get_usuario_comite_apuracao_resp();
        }
        else
        {
            return TRUE;
        }
    }

    private function get_permissao_cadastro_tipo()
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
	
	public function get_usuarios()
	{       
		$this->load->model('gestao/pendencia_gestao_model');

		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);

		foreach($this->pendencia_gestao_model->get_usuarios($cd_gerencia) as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
		echo json_encode($data);
	}	

    public function index($cd_reuniao_sistema_gestao_grupo = 0)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data = array(
            'reuniao'     					  => $this->pendencia_gestao_model->get_tipo_reuniao(),
            'grupo_tipo'  					  => $this->pendencia_gestao_model->get_grupo_tipo_reuniao(),
            'superior'    					  => $this->pendencia_gestao_model->get_pendencia_gestao_superior(),
            'responsavel' 					  => $this->pendencia_gestao_model->get_pendencia_gestao_gerencia(),
            'usuario'     					  => $this->pendencia_gestao_model->get_pendencia_gestao_usuario(),
            'cd_reuniao_sistema_gestao_grupo' => $cd_reuniao_sistema_gestao_grupo
        );

        $filtros['situacao'] = array(
            array('id' => 'tp_atrasado',     'value' => 'T', 'text' => 'Atrasado',     'checked' => TRUE),
            array('id' => 'tp_aberto',       'value' => 'A', 'text' => 'Aberto',       'checked' => TRUE),
            array('id' => 'tp_execuntado',   'value' => 'X', 'text' => utf8_decode('Em Andamento'),  'checked' => TRUE),
			array('id' => 'tp_implementado', 'value' => 'I', 'text' => 'Implementado', 'checked' => TRUE),
            array('id' => 'tp_encerrado',    'value' => 'E', 'text' => 'Encerrado',    'checked' => FALSE)
        );

        $data['filtros'] = $filtros;

        $this->load->view('gestao/pendencia_gestao/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $args = array(
            'cd_reuniao_sistema_gestao_grupo' => $this->input->post('cd_reuniao_sistema_gestao_grupo', TRUE),
            'cd_reuniao_sistema_gestao_tipo'  => $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE),
            'dt_inicial'                      => $this->input->post('dt_inicial', TRUE),
            'dt_final'                        => $this->input->post('dt_final', TRUE),
            'dt_prazo_inicial'                => $this->input->post('dt_prazo_inicial', TRUE),
            'dt_prazo_final'                  => $this->input->post('dt_prazo_final', TRUE),
            'cd_superior'                     => $this->input->post('cd_superior', TRUE),
            'fl_situacao'                     => $this->input->post('fl_situacao', TRUE),
            'tp_atrasado'                     => $this->input->post('tp_atrasado', TRUE),
            'tp_aberto'                       => $this->input->post('tp_aberto', TRUE),
            'tp_execuntado'                   => $this->input->post('tp_execuntado', TRUE),
            'tp_implementado'                 => $this->input->post('tp_implementado', TRUE),
            'tp_encerrado'                    => $this->input->post('tp_encerrado', TRUE),
            'cd_responsavel'                  => $this->input->post('cd_responsavel', TRUE),
            'cd_usuario_responsavel'          => $this->input->post('cd_usuario_responsavel', TRUE)
        );

        manter_filtros($args);

		$data['collection'] = $this->listarExec($args);

        $this->load->view('gestao/pendencia_gestao/index_result', $data);
    }
	
	private function listarExec($ar_param = Array())
	{
        $this->load->model('gestao/pendencia_gestao_model');
		
		$args = array(
		    'cd_pendencia_gestao'             => ((array_key_exists("cd_pendencia_gestao", $ar_param)) ? $ar_param['cd_pendencia_gestao'] : ""),
            'cd_reuniao_sistema_gestao_grupo' => ((array_key_exists("cd_reuniao_sistema_gestao_grupo", $ar_param)) ? $ar_param['cd_reuniao_sistema_gestao_grupo'] : ""),
            'cd_reuniao_sistema_gestao_tipo'  => ((array_key_exists("cd_reuniao_sistema_gestao_tipo", $ar_param)) ? $ar_param['cd_reuniao_sistema_gestao_tipo'] : ""),
            'dt_inicial'                      => ((array_key_exists("dt_inicial", $ar_param)) ? $ar_param['dt_inicial'] : ""),
            'dt_final'                        => ((array_key_exists("dt_final", $ar_param)) ? $ar_param['dt_final'] : ""),
            'dt_prazo_inicial'                => ((array_key_exists("dt_prazo_inicial", $ar_param)) ? $ar_param['dt_prazo_inicial'] : ""),
            'dt_prazo_final'                  => ((array_key_exists("dt_prazo_final", $ar_param)) ? $ar_param['dt_prazo_final'] : ""),
            'cd_superior'                     => ((array_key_exists("cd_superior", $ar_param)) ? $ar_param['cd_superior'] : ""),
            'fl_situacao'                     => ((array_key_exists("fl_situacao", $ar_param)) ? $ar_param['fl_situacao'] : ""),
            'tp_atrasado'                     => ((array_key_exists("tp_atrasado", $ar_param)) ? $ar_param['tp_atrasado'] : ""),
            'tp_aberto'                       => ((array_key_exists("tp_aberto", $ar_param)) ? $ar_param['tp_aberto'] : ""),
            'tp_execuntado'                   => ((array_key_exists("tp_execuntado", $ar_param)) ? $ar_param['tp_execuntado'] : ""),
            'tp_implementado'                 => ((array_key_exists("tp_implementado", $ar_param)) ? $ar_param['tp_implementado'] : ""),
            'tp_encerrado'                    => ((array_key_exists("tp_encerrado", $ar_param)) ? $ar_param['tp_encerrado'] : ""),
            'cd_responsavel'                  => ((array_key_exists("cd_responsavel", $ar_param)) ? $ar_param['cd_responsavel'] : ""),
            'cd_usuario_responsavel'          => ((array_key_exists("cd_usuario_responsavel", $ar_param)) ? $ar_param['cd_usuario_responsavel'] : ""),
            'fl_dashboard'                    => ((array_key_exists("fl_dashboard", $ar_param)) ? $ar_param['fl_dashboard'] : "")
        );        
				
		$data = Array();
		$data = $this->pendencia_gestao_model->listar($args, $this->get_usuario_comite_apuracao_resp());

        foreach ($data as $key => $item)
        {
            $data[$key]['responsavel'] = $this->get_gerencia_responsavel(intval($item['cd_pendencia_gestao']));

            if(count($data[$key]['responsavel']) == 0)
            {
                $data[$key]['responsavel'][] = $item['ds_usuario_responsavel'];
            }
        }
		return $data;
	}

    public function cadastro($cd_pendencia_gestao = 0, $cd_reuniao_sistema_gestao = 0)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data['reuniao']  = $this->pendencia_gestao_model->get_tipo_reuniao();
       
        if(intval($cd_pendencia_gestao) == 0)
        {
            $cd_reuniao_sistema_gestao_tipo = '';
            $dt_reuniao                     = '';

            if(intval($cd_reuniao_sistema_gestao) > 0)
            {
                $reuniao_sistema_gestao = $this->pendencia_gestao_model->get_reuniao_sistema_gestao_reuniao($cd_reuniao_sistema_gestao);

                if(count($reuniao_sistema_gestao) > 0)
                {
                    $cd_reuniao_sistema_gestao_tipo = $reuniao_sistema_gestao['cd_reuniao_sistema_gestao_tipo'];
                    $dt_reuniao                     = $reuniao_sistema_gestao['dt_reuniao'];
                }
            }

            $data['row'] = array(
                'cd_pendencia_gestao'            => 0,
                'cd_reuniao_sistema_gestao_tipo' => $cd_reuniao_sistema_gestao_tipo,
                'dt_reuniao'                     => $dt_reuniao,
                'cd_superior'                    => '',
                'dt_prazo'                       => '',
                'ds_item'                        => '',
                'dt_encerrada'                   => '',
                'arquivo'                        => '',
                'cd_gerencia'                    => '',
                'cd_usuario_responsavel'         => '',
                'cd_gerencia_responsavel'        => '',
                'responsavel_n'                  => '',
                'arquivo_nome'                   => '',
                'dt_executando'                  => '',
                'ds_usuario_executando'          => '',
                'ds_implementado'                => '',
                'ds_usuario_implementado'        => '',
                'dt_inicio'                      => '',
                'dt_implementado'                => '',
                'dt_verificacao_eficacia'        => ''
            );

            $responsavel = array();

            $data['historico'] = array();

            $data['usuarios'] = array();

            $data['fl_permissao_encerrar'] = 'N';
        }
        else
        {
            $data['row'] = $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao));

            $data['usuarios'] = $this->pendencia_gestao_model->get_usuarios($data['row']['cd_gerencia']);

            $responsavel = $this->get_gerencia_responsavel(intval($cd_pendencia_gestao));
            
            $data['historico'] = $this->pendencia_gestao_model->get_historico_prorrogacao(intval($cd_pendencia_gestao));

            $data['fl_permissao_encerrar'] = $this->get_permissao_encerrar($data['row']['cd_reuniao_sistema_gestao_tipo']);
        }

        if($this->get_perm_comite_apuracao_resp($data['row']['cd_reuniao_sistema_gestao_tipo']))
        {
            $data['superior']    = $this->pendencia_gestao_model->get_responsavel_pendencia(array($data['row']['cd_superior']));
            $data['responsavel'] = $this->pendencia_gestao_model->get_responsavel_pendencia($responsavel);

            $data['responsavel_checked'] = $responsavel;

            if($this->session->userdata('indic_12') == '*')
            {
                $data['fl_permissao'] = 'S';
            }
            else  if(in_array('TOD', $responsavel))
            {
                $data['fl_permissao'] = 'S';
            }
            else if(intval($data['row']['cd_usuario_responsavel']) > 0)
            {
                if(
                    (intval($data['row']['cd_usuario_responsavel']) == $this->session->userdata('codigo'))
                    OR
                    (trim($data['row']['cd_gerencia']) == $this->session->userdata('divisao') 
                        AND 
                        (
                            $this->session->userdata('tipo') == 'G' OR  $this->session->userdata('indic_01') == 'S'
                        )

                    )

                ) {
                    $data['fl_permissao'] = 'S';
                }
                else
                {
                    $data['fl_permissao'] = 'N';
                }
            }
            else
            {
                $row = $this->pendencia_gestao_model->get_permissao_usuario_responsavel($this->session->userdata('codigo'), $responsavel);

                $data['fl_permissao'] = $row['fl_permissao'];
            }

            $this->load->view('gestao/pendencia_gestao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $cd_pendencia_gestao = $this->input->post('cd_pendencia_gestao', TRUE);

        $args = array(
            'cd_reuniao_sistema_gestao_tipo' => $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE),
            'dt_reuniao'                     => $this->input->post('dt_reuniao', TRUE),
            'ds_item'                        => $this->input->post('ds_item', TRUE),
            'cd_superior'                    => $this->input->post('cd_superior', TRUE),
            'dt_prazo'                       => $this->input->post('dt_prazo', TRUE),
            'dt_prazo_prorroga'              => $this->input->post('dt_prazo_prorroga', TRUE),
            'cd_usuario_responsavel'         => $this->input->post('cd_usuario_responsavel', TRUE),
            'cd_reuniao_sistema_gestao'      => '',
            'dt_implementado'                => '',
            'cd_usuario_implementado'        => '', 
            'arquivo'                        => $this->input->post('arquivo', TRUE),
            'arquivo_nome'                   => $this->input->post('arquivo_nome', TRUE),
            'cd_usuario'                     => $this->session->userdata('codigo')
        );

        $responsavel = $this->input->post('responsavel', TRUE);

        $args['responsavel'] = array();

        if(is_array($responsavel))
        {
            $args['responsavel'] = $responsavel;         
        }

        if((intval($args['cd_reuniao_sistema_gestao_tipo']) > 0) AND (trim($args['dt_reuniao']) != ''))
        {
            $reuniao_sistema_gestao = $this->pendencia_gestao_model->get_reuniao_sistema_gestao(intval($args['cd_reuniao_sistema_gestao_tipo']), $args['dt_reuniao']);

            if(count($reuniao_sistema_gestao) > 0)
            {
                $args['cd_reuniao_sistema_gestao'] = intval($reuniao_sistema_gestao['cd_reuniao_sistema_gestao']);
            }
        }

        if(intval($cd_pendencia_gestao) == 0)
        {
            $cd_pendencia_gestao = $this->pendencia_gestao_model->salvar($args);
        }
        else
        {
            $this->pendencia_gestao_model->atualizar($cd_pendencia_gestao, $args);
        }

        if((trim($args['dt_prazo_prorroga']) != '') AND ((trim($args['dt_prazo_prorroga']) != $this->input->post('dt_prazo_prorroga_old', TRUE))))
        {
            $args = array(
                'cd_pendencia_gestao'             => intval($cd_pendencia_gestao),
                'dt_pendencia_gestao_prorrogacao' => $args['dt_prazo_prorroga'],
                'cd_usuario'                      => $this->session->userdata('codigo')
            ); 

            $this->pendencia_gestao_model->salvar_pendencia_gestao_prorrogacao($args);
        }
        
        redirect('gestao/pendencia_gestao/cadastro/'.$cd_pendencia_gestao, 'refresh');
    }

    public function encerrar($cd_pendencia_gestao)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $this->pendencia_gestao_model->encerrar($cd_pendencia_gestao, $this->session->userdata('codigo'));

        redirect('gestao/pendencia_gestao', 'refresh');
    }

    public function acompanhamento($cd_pendencia_gestao)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data = array(
            'row'        => $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao)),
            'collection' => $this->pendencia_gestao_model->lista_acompanhamento(intval($cd_pendencia_gestao))
        );
        
        if($this->get_perm_comite_apuracao_resp($data['row']['cd_reuniao_sistema_gestao_tipo']))
        {
            $data['implementado'] = array(
                array('value' => 'S', 'text' => 'Sim'),
                array('value' => 'N', 'text' => ('Não'))
            );
    		
            $data['executando'] = array(
                array('value' => 'S', 'text' => 'Sim'),
                array('value' => 'N', 'text' => ('Não'))
            );		

            $data['verificado'] = array(
                array('value' => 'S', 'text' => 'Sim'),
                array('value' => 'N', 'text' => ('Não'))
            );      

            $this->load->view('gestao/pendencia_gestao/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salva_acompanhamento()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $fl_implementado = $this->input->post('fl_implementado', TRUE);
        $fl_executando   = $this->input->post('fl_executando', TRUE);
        $fl_verificado   = $this->input->post('fl_verificado', TRUE);
        
        if(trim($this->input->post('dt_verificado_eficacia', TRUE)) != '')
        {
            $fl_verificado = 'S';
        }
        else
        {
            $fl_verificado = 'N';
        }

        $args = array(
            'cd_pendencia_gestao'                => $this->input->post('cd_pendencia_gestao', TRUE),
            'ds_pendencia_gestao_acompanhamento' => $this->input->post('ds_pendencia_gestao_acompanhamento', TRUE),
            'dt_verificado_eficacia'             => $this->input->post('dt_verificado_eficacia', TRUE),
            'cd_usuario'                         => $this->session->userdata('codigo')
        );

        $this->pendencia_gestao_model->salva_acompanhamento($args, $fl_implementado, $fl_executando, $fl_verificado);

        $row = $this->pendencia_gestao_model->carrega(intval($args['cd_pendencia_gestao']));

        if(trim($fl_implementado) == 'S')
        {
            $this->enviar_email_implementado($this->input->post('cd_pendencia_gestao', TRUE));

            if(intval($row['cd_reuniao_sistema_gestao_tipo']) == 24)
            {
                $this->pendencia_gestao_model->atualiza_implementacao_cenario_legal($row['cd_atividade'], $row['cd_cenario']);
            }
        }

        if(trim($this->input->post('dt_verificado_eficacia', TRUE)) != '' AND intval($row['cd_reuniao_sistema_gestao_tipo']) == 24)
        {
        	$this->envia_email_verificado(intval($args['cd_pendencia_gestao']));
        }

        redirect('gestao/pendencia_gestao/acompanhamento/'.$args['cd_pendencia_gestao'], 'refresh');
    }

    private function enviar_email_implementado($cd_pendencia_gestao)
    {
        $this->load->model(array(
            'gestao/pendencia_gestao_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 367;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $pendencia_gestao = $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao));

        //ENVIO PARA GRC
        if(in_array(intval($pendencia_gestao['cd_reuniao_sistema_gestao_tipo']), array(11, 12, 13, 14)))
        {
            $texto = str_replace('[LINK]', site_url('gestao/pendencia_gestao/cadastro/'.intval($cd_pendencia_gestao)), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array( 
                'de'      => 'Pendências Gestão',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);   
        }
    }

    private function envia_email_verificado($cd_pendencia_gestao)
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 378;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $texto = str_replace('[LINK]', site_url('gestao/pendencia_gestao/cadastro/'.intval($cd_pendencia_gestao)), $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Pendências Gestão',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);   
    }
	
    public function cronograma($cd_pendencia_gestao)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data = array(
            'row'        => $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao)),
            'collection' => $this->pendencia_gestao_model->lista_cronograma(intval($cd_pendencia_gestao))
        );
		
		$responsavel = $this->get_gerencia_responsavel(intval($cd_pendencia_gestao));
        $data['superior']    = $this->pendencia_gestao_model->get_responsavel_pendencia(array($data['row']['cd_superior']));
        $data['responsavel'] = $this->pendencia_gestao_model->get_responsavel_pendencia($responsavel);		
		
        if($this->session->userdata('indic_12') == '*')
        {
            $data['fl_permissao'] = 'S';
        }
        else  if(in_array('TOD', $responsavel))
        {
            $data['fl_permissao'] = 'S';
        }
        else if(intval($data['row']['cd_usuario_responsavel']) > 0)
        {
            if(
                (intval($data['row']['cd_usuario_responsavel']) == $this->session->userdata('codigo'))
                OR
                (trim($data['row']['cd_gerencia']) == $this->session->userdata('divisao') 
                    AND 
                    (
                        $this->session->userdata('tipo') == 'G' OR  $this->session->userdata('indic_01') == 'S'
                    )

                )

            ) {
                $data['fl_permissao'] = 'S';
            }
            else
            {
                $data['fl_permissao'] = 'N';
            }
        }
        else
        {
            $row = $this->pendencia_gestao_model->get_permissao_usuario_responsavel($this->session->userdata('codigo'), $responsavel);

            $data['fl_permissao'] = $row['fl_permissao'];
        }		

        $this->load->view('gestao/pendencia_gestao/cronograma', $data);
    }	
	
    public function salva_cronograma()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $args = array(
                    'cd_pendencia_gestao' => $this->input->post('cd_pendencia_gestao', TRUE),
                    'arquivo'             => $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE),
                    'arquivo_nome'        => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
                    'cd_usuario'          => $this->session->userdata('codigo')
                );
          
                $this->pendencia_gestao_model->salvar_cronograma($args);
                
                $nr_conta++;
            }
        }   
        
        redirect('gestao/pendencia_gestao/cronograma/'.$args['cd_pendencia_gestao'], 'refresh');
    }	
	
    
    public function anexo($cd_pendencia_gestao)
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data = array(
            'row'        => $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao)),
            'collection' => $this->pendencia_gestao_model->lista_anexo(intval($cd_pendencia_gestao))
        );

        if($this->get_perm_comite_apuracao_resp($data['row']['cd_reuniao_sistema_gestao_tipo']))
        {
            $this->load->view('gestao/pendencia_gestao/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function salva_anexo()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

        $cd_pendencia_gestao = $this->input->post('cd_pendencia_gestao', TRUE);

        $pendencia_gestao = $this->pendencia_gestao_model->carrega(intval($cd_pendencia_gestao));

        $atividade = array();

        if(intval($pendencia_gestao['cd_atividade']) > 0) 
        {
            $this->load->model('projetos/atividade_atendimento_model');
            
            $args['cd_atividade'] = $pendencia_gestao['cd_atividade'];

            $this->atividade_atendimento_model->atividade($result, $args);
            $atividade = $result->row_array();
        }

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $args = array(
                    'cd_pendencia_gestao' => $cd_pendencia_gestao,
                    'arquivo'             => $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE),
                    'arquivo_nome'        => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
                    'cd_usuario'          => $this->session->userdata('codigo')
                );
          
                $this->pendencia_gestao_model->salvar_anexo($args);
                /*
                if(count($atividade) > 0 AND trim($atividade['tipo_ativ']) == 'L' AND (intval($args['pertinencia']) == 1 OR intval($args['pertinencia']) == 2))
                {
                    $this->load->plugin('encoding_pi');

                    $this->load->model('projetos/cenario_model');

                    $cenario = $this->cenario_model->carrega_conteudo(
                        intval($atividade['cd_cenario'])
                    );

                    $caminho_cenario = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CENARIO-LEGAL/'.$cenario['ds_ano_edicao'];

                    if(!is_dir($caminho_cenario))
                    {
                        mkdir($caminho_cenario, 0777);
                    }

                    $caminho_cenario .= '/'.$cenario['cd_edicao'].'_'.$cenario['cd_cenario'].'_'.str_replace(' ', '-', $cenario['tit_capa']);;

                    if(!is_dir($caminho_cenario))
                    {
                        mkdir($caminho_cenario, 0777);
                    }

                    if(trim($cenario['arquivo']) != '')
                    {
                        copy('../cieprev/up/cenario/'.$cenario['arquivo'], $caminho_cenario.'/'.fixUTF8($cenario['arquivo_nome']));
                    }
                    copy('../cieprev/up/pendencia_gestao/'.$args['arquivo'], $caminho_cenario.'/'.fixUTF8($args['arquivo_nome']));
                }
                */
                
                $nr_conta++;
            }
        }   
        exit;
        redirect('gestao/pendencia_gestao/anexo/'.$args['cd_pendencia_gestao'], 'refresh');
    }
    
    public function excluir_anexo($cd_pendencia_gestao, $cd_pendencia_gestao_anexo)
    {
        $this->load->model('gestao/pendencia_gestao_model');
        
        $this->pendencia_gestao_model->excluir_anexo($cd_pendencia_gestao_anexo, $this->session->userdata('codigo'));
        
        redirect('gestao/pendencia_gestao/anexo/'.$cd_pendencia_gestao, 'refresh');
    }

    public function excluir_acompanhamento($cd_pendencia_gestao, $cd_pendencia_gestao_acompanhamento)
    {
        $this->load->model('gestao/pendencia_gestao_model');
        
        $this->pendencia_gestao_model->excluir_acompanhamento($cd_pendencia_gestao_acompanhamento, $this->session->userdata('codigo'));
        
        redirect('gestao/pendencia_gestao/acompanhamento/'.$cd_pendencia_gestao, 'refresh');
    }

    public function tipo()
    {
        if($this->get_permissao_cadastro_tipo())
        {
            $this->load->view('gestao/pendencia_gestao/tipo');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function tipo_listar()
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $args = array(
            'ds_reuniao_sistema_gestao_tipo' => $this->input->post('ds_reuniao_sistema_gestao_tipo', TRUE)
        );
                
        manter_filtros($args);

        $data['collection'] = $this->reuniao_sistema_gestao_tipo_model->listar($args, 'N');

        $this->load->view('gestao/pendencia_gestao/tipo_result', $data);
    }

    public function tipo_cadastro($cd_reuniao_sistema_gestao_tipo = 0)
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        if(intval($cd_reuniao_sistema_gestao_tipo) == 0)
        {
            $data['row'] = array(
                'cd_reuniao_sistema_gestao_tipo' => intval($cd_reuniao_sistema_gestao_tipo),
                'ds_reuniao_sistema_gestao_tipo' => ''
            );
        }
        else
        {
            $data['row'] = $this->reuniao_sistema_gestao_tipo_model->carrega($cd_reuniao_sistema_gestao_tipo);      
        }

        $this->load->view('gestao/pendencia_gestao/tipo_cadastro', $data);
    }

    public function tipo_salvar()
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $cd_reuniao_sistema_gestao_tipo = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);

        $args = array(
            'cd_reuniao_sistema_gestao_tipo' => $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE),
            'ds_reuniao_sistema_gestao_tipo' => $this->input->post('ds_reuniao_sistema_gestao_tipo', TRUE),
            'cd_usuario'                     => $this->session->userdata('codigo')
        );

        if(intval($cd_reuniao_sistema_gestao_tipo) == 0)
        {
            $this->reuniao_sistema_gestao_tipo_model->salvar_tipo_pendencia($args);
        } 
        else
        {
            $this->reuniao_sistema_gestao_tipo_model->atualizar_tipo_pendencia(intval($cd_reuniao_sistema_gestao_tipo), $args);
        }

        redirect('gestao/pendencia_gestao/tipo/'.$cd_reuniao_sistema_gestao_tipo, 'refresh');
    }
	
    public function dashboard()
    {
        $this->load->model('gestao/pendencia_gestao_model');

        $data = array();

        $this->load->view('gestao/pendencia_gestao/dashboard', $data);
    }	
	
    public function dashboardItem()
    {
        $this->load->model('gestao/pendencia_gestao_model');
		
		$fl_situacao = $this->input->post('fl_situacao', TRUE);
		
		$args['fl_dashboard']    = "S";
		$args['tp_atrasado']     = ($fl_situacao == 'T' ? "T" : "");
		$args['tp_aberto']       = ($fl_situacao == 'A' ? "A" : "");
		$args['tp_execuntado']   = ($fl_situacao == 'X' ? "X" : "");
		$args['tp_implementado'] = ($fl_situacao == 'I' ? "I" : "");
		$args['tp_encerrado']    = ($fl_situacao == 'E' ? "E" : "");
		
		$data = $this->listarExec($args);
		
		#print_r($data); exit;
		
		$ar_ret = array();
		foreach($data as $item)
		{
			$ar_ret[] = array(
								'cd_pendencia_gestao'     => $item['cd_pendencia_gestao'],
								'dt_prazo'                => (trim($item['dt_prazo']) == "" ? "#" : $item['dt_prazo']),
								'ds_item'                 => utf8_encode($item['ds_item']),
								'ds_gerencia_responsavel' => utf8_encode(($item['ds_gerencia_responsavel'] == "" ? implode(', ', $item['responsavel']) : $item['ds_gerencia_responsavel'])),
								'ds_acompanhamento'       => utf8_encode(nl2br($item['ds_acompanhamento'])),
								'ds_arquivo'              => (trim($item['arquivo']) == "" ? "#" : trim($item['arquivo'])),
								'arquivo_cronograma'      => trim($item['arquivo_cronograma']),
								'qt_cronograma'           => intval($item['qt_cronograma']),
								'qt_anexo'                => intval($item['qt_anexo'])
							);
		}
		
		echo json_encode($ar_ret);
    }
	
    public function dashboardQTItem()
    {
		$ar_ret = array();
		
		$ar_ret['qt_atrasado']     = intval($this->dashboardGetQTItem("T"));
		$ar_ret['qt_aberto']       = intval($this->dashboardGetQTItem("A"));
		$ar_ret['qt_execuntado']   = intval($this->dashboardGetQTItem("X"));
		$ar_ret['qt_implementado'] = intval($this->dashboardGetQTItem("I"));
		$ar_ret['qt_encerrado']    = intval($this->dashboardGetQTItem("E"));
		
		echo json_encode($ar_ret);
    }		
	
    private function dashboardGetQTItem($fl_situacao = "")
    {
        $this->load->model('gestao/pendencia_gestao_model');
		
		$args['fl_dashboard']    = "S";
		$args['tp_atrasado']     = ($fl_situacao == 'T' ? "T" : "");
		$args['tp_aberto']       = ($fl_situacao == 'A' ? "A" : "");
		$args['tp_execuntado']   = ($fl_situacao == 'X' ? "X" : "");
		$args['tp_implementado'] = ($fl_situacao == 'I' ? "I" : "");
		$args['tp_encerrado']    = ($fl_situacao == 'E' ? "E" : "");
		
		$data = $this->listarExec($args);
		
		return count($data);
    }	
}
?>
