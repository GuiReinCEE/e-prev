<?php
class Reclamacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao_atendimento($fl_responsavel, $fl_gerente, $reclamacao)
    {
    	if(gerencia_in(array('GRSC')))
		{
			return TRUE;
		}
		else if(intval($fl_responsavel) > 0)
		{
			return TRUE;
		}
		else if(intval($fl_gerente) > 0)
		{
			return TRUE;
		}
		else if(intval($reclamacao['cd_usuario_responsavel']) == 0)
		{
			if(intval($reclamacao['cd_usuario_inclusao']) == $this->session->userdata('codigo'))
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

    private function get_permissao_comite($reclamacao, $classificacao, $validacao_comite)
    {
    	if(
    		(count($classificacao) > 0) 
    		AND 
			(intval($classificacao['cd_reclamacao_comite']) > 0) 
			AND 
			(trim($this->session->userdata('indic_12')) == '*') 
			AND 
			(trim($reclamacao['dt_concorda']) == '') 
			AND 
			(trim($reclamacao['dt_encerramento']) == '')
			AND 
			(count($validacao_comite) > 0) 
			AND 
			(trim($validacao_comite['dt_confirma']) == '')
		)
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    private function get_permissao_acao($fl_responsavel, $fl_gerente)
    {
    	return TRUE;
    }

    private function get_permissao_cadastro($fl_encerramento, $fl_responsavel, $row)
    {
    	if(intval($fl_encerramento) == 0 AND trim($row['dt_cancela']) == '')
    	{
    		if(gerencia_in(array('GRSC')))
			{
				return TRUE;
			}
			else if(intval($fl_responsavel) > 0)
			{
				return TRUE;
			}
			else if(intval($row['cd_usuario_inclusao']) == $this->session->userdata('codigo'))
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

    private function get_permissoes($numero, $ano, $tipo, $row, $acao_retorno, $classificacao)
    {
    	$encerramento  = $this->reclamacao_model->get_verifica_encerramento(intval($numero), intval($ano), trim($tipo));
		$responsavel   = $this->reclamacao_model->verifica_responsavel(intval($numero), intval($ano), trim($tipo), intval($this->session->userdata('codigo'))); 
		$atendimento   = $this->reclamacao_model->verifica_atendimento(intval($numero), intval($ano), trim($tipo));
		$gerente       = $this->reclamacao_model->verifica_gerente(intval($numero), intval($ano), trim($tipo), intval($this->session->userdata('codigo')));
		$parecer_final = $this->reclamacao_model->get_parecer_final(intval($numero), intval($ano), trim($tipo));
		$acao          = $this->reclamacao_model->acao(intval($numero), intval($ano), trim($tipo));

		//ABA ATENDIMENTO (ATENDIMENTO E RESPONSÁVEL TEM ACESSO)
		$fl_aba_atendimento = $this->get_permissao_atendimento($responsavel['fl_responsavel'], $gerente['fl_gerente'], $row);
		//ABA ATENDIMENTO (ATENDIMENTO, RESPONSÁVEL, COMITÊ TEM ACESSO)
		$fl_aba_acao        = $this->get_permissao_acao($responsavel['fl_responsavel'], $gerente['fl_gerente']);
		//ABA PARA RESPOSTA DO COMITÊ (COMITÊ TEM ACESSO QUANDO NÃO TEM A RESPOSTA DO USUÁRIO)
		$fl_aba_comite = $this->get_permissao_comite(
			$acao_retorno,
			$classificacao, 
			$this->reclamacao_model->get_validacao_comite_confirmada(intval($numero), intval($ano), trim($tipo), intval($this->session->userdata('codigo')))
		);

		$fl_aba_prorrogacao = FALSE;

		if(isset($atendimento['fl_atendimento']) AND intval($atendimento['fl_atendimento']) > 0 AND $fl_aba_atendimento)
		{
			$fl_aba_prorrogacao = TRUE;
		}

		$fl_acao = $this->get_permissao_cadastro($encerramento['fl_encerramento'], $responsavel['fl_responsavel'], $row);

		$fl_acao_responsavel = FALSE;

		if(intval($encerramento['fl_encerramento']) == 0 AND intval($responsavel['fl_responsavel']) > 0)
		{
			$fl_acao_responsavel = TRUE;
		}
		else if(gerencia_in(array('GRSC')))
		{
			$fl_acao_responsavel = TRUE;
		}

		$fl_parecer_final = FALSE;

		if(intval($parecer_final['fl_parecer_final']) > 0 AND trim($this->session->userdata('indic_12')) == '*')
		{
			$fl_parecer_final = TRUE;
		}

		$fl_aba_retorno = TRUE;

		$fl_cadastro = FALSE;
		//vdornelles, lrodriguez
		if(in_array($this->session->userdata('codigo'), array(146, 251,35))) 
		{
			$fl_cadastro = TRUE;
		}

		return array(
			'fl_aba_atendimento'   => $fl_aba_atendimento,
			'fl_aba_prorrogacao'   => $fl_aba_prorrogacao,
			'fl_aba_acao'          => $fl_aba_acao,
			'fl_aba_comite'        => $fl_aba_comite,
			'fl_aba_retorno'       => $fl_aba_retorno,
			'fl_aba_parecer_final' => $fl_parecer_final,
			'fl_acao'              => $fl_acao,
			'fl_cadastro'          => $fl_cadastro,
			'fl_acao_responsavel'  => $fl_acao_responsavel,
			'fl_encerrado'         => (intval($encerramento['fl_encerramento']) == 0 ? FALSE : TRUE),
			'fl_responsavel'       => (intval($responsavel['fl_responsavel']) > 0 ? TRUE : FALSE)
		);
    }

    public function get_dropdown_sim_nao()
    {
    	return array(
			array('value' => 'S', 'text' => 'Sim'),
			array('value' => 'N', 'text' => 'Não')
		);
    }

	public function get_reclamacao_retorno_classificacao_tipo()
    {
        $this->load->model('projetos/reclamacao_model');
		
		$cd_reclamacao_retorno_classificacao_pai = $this->input->post('cd_reclamacao_retorno_classificacao_pai', TRUE);
		
		$data = $this->reclamacao_model->get_reclamacao_retorno_classificacao_tipo($cd_reclamacao_retorno_classificacao_pai);
		
		foreach ($data as $key => $item) 
	    {
			$data[$key]['text'] = utf8_encode($item['text']);           
	    }

		echo json_encode($data);
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
    	$this->load->model('projetos/reclamacao_model');

    	$participante = array(
    		'cd_empresa'            => $cd_empresa,
    		'cd_registro_empregado' => $cd_registro_empregado,
    		'seq_dependencia'       => $seq_dependencia
		);

    	$data = array(
    		'participante'          => $participante,
    		'empresa'               => $this->reclamacao_model->get_empresas(),
    		'planos'                => $this->reclamacao_model->get_planos(),
    		'retorno_classificacao' => $this->reclamacao_model->get_reclamacao_retorno_classificacao(),
    		'programa'              => $this->reclamacao_model->get_reclamacao_programa(),
    		'assunto'				=> $this->reclamacao_model->get_reclamacao_assunto(),
    		'usuario_inclusao'      => $this->reclamacao_model->get_usuario_inclusao(),
    		'dropdown_sim_nao'      => $this->get_dropdown_sim_nao()
    	);		
        
		$this->load->view('ecrm/reclamacao/index', $data);
    }	

    public function listar()
    {
		$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'                              => $this->input->post('numero', TRUE),
			'ano'               		          => $this->input->post('ano', TRUE),
			'tipo'                				  => $this->input->post('tipo', TRUE),
			'fl_situacao'         			      => $this->input->post('fl_situacao', TRUE),
			'fl_prorrogada'       			      => $this->input->post('fl_prorrogada', TRUE),
			'cd_empresa_patr'    			      => $this->input->post('cd_empresa_patr', TRUE),
			'fl_tipo_cliente'   			      => $this->input->post('fl_tipo_cliente', TRUE),
			'cd_empresa'        			      => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado'				  => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'       			  => $this->input->post('seq_dependencia', TRUE),
			'nome'                  			  => $this->input->post('nome', TRUE),
			'cd_plano'             				  => $this->input->post('cd_plano', TRUE),
			'fl_participante'       		  	  => $this->input->post('fl_participante', TRUE),
			'dt_inclusao_ini'      				  => $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'       			  => $this->input->post('dt_inclusao_fim', TRUE),
			'dt_atendimento_ini'  			      => $this->input->post('dt_atendimento_ini', TRUE),
			'dt_atendimento_fim'  			      => $this->input->post('dt_atendimento_fim', TRUE),
			'cd_divisao'           				  => $this->input->post('cd_divisao', TRUE),
			'cd_usuario_responsavel'			  => $this->input->post('cd_usuario_responsavel', TRUE),
			
			'dt_prazo_acao_ini'       			  => $this->input->post('dt_prazo_acao_ini', TRUE),
			'dt_prazo_acao_fim'         		  => $this->input->post('dt_prazo_acao_fim', TRUE),
			'dt_prazo_classificacao_ini'      	  => $this->input->post('dt_prazo_classificacao_ini', TRUE),
			'dt_prazo_classificacao_fim'    	  => $this->input->post('dt_prazo_classificacao_fim', TRUE),
			
			'dt_encerrado_ini'                    => $this->input->post('dt_encerrado_ini', TRUE),
			'dt_encerrado_fim'                    => $this->input->post('dt_encerrado_fim', TRUE),
			'cd_reclamacao_retorno_classificacao' => $this->input->post('cd_reclamacao_retorno_classificacao', TRUE),
			'cd_reclamacao_programa'              => $this->input->post('cd_reclamacao_programa', TRUE),
			'cd_reclamacao_assunto'			  	  => $this->input->post('cd_reclamacao_assunto', TRUE),
			'cd_usuario_inclusao'                 => $this->input->post('cd_usuario_inclusao', TRUE)
		);

        manter_filtros($args);
		
        $data['collection'] = $this->reclamacao_model->listar($args);
        $data['cd_usuario'] = $this->session->userdata('codigo');
				
        $this->load->view('ecrm/reclamacao/index_result', $data);
    }

    public function excluir($numero, $ano, $tipo)
    {
        $this->load->model('projetos/reclamacao_model');

        $this->reclamacao_model->excluir($numero, $ano, $tipo, $this->session->userdata('codigo'));

        redirect('ecrm/reclamacao', 'refresh');
    }

    public function dispensar_membro($numero, $ano, $tipo)
    {
    	if(trim($this->session->userdata('indic_12')) == '*' OR trim($this->session->userdata('codigo')) == '251')
		{
	    	$this->load->model('projetos/reclamacao_model');

	    	$data = array(
	    		'row'     => $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo)),
	    	 	'membros' => $this->reclamacao_model->get_membros($numero, $ano, $tipo)
	    	);

	    	$this->load->view('ecrm/reclamacao/dispensar_membro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

	public function atualizar_situacao_membro()
    {
    	$this->load->model('projetos/reclamacao_model');

    	$numero = $this->input->post('numero', TRUE);
    	$ano 	= $this->input->post('ano', TRUE);
    	$tipo 	= $this->input->post('tipo', TRUE);

		$args = array(
			'cd_usuario_comite'         => $this->input->post('cd_usuario_comite', TRUE),
			'ds_justificativa_confirma' => $this->input->post('ds_justificativa_confirma', TRUE),
			'dt_confirma'			    => $this->input->post('ds_confirma', TRUE),
			'fl_confirma'			    => $this->input->post('fl_confirma', TRUE),
			'cd_usuario'                => $this->session->userdata('codigo'),
		);

		$this->reclamacao_model->dispensar_membro($numero, $ano, $tipo, $args);

		redirect('ecrm/reclamacao/parecer_comite', 'refresh');
    }

    public function relatorio()
    {
    	$this->load->model('projetos/reclamacao_model');

    	$data['retorno_classificacao'] = $this->reclamacao_model->get_classificacao();
    	$data = array(
    		'retorno_classificacao' => $this->reclamacao_model->get_classificacao(),
    		'assunto'				=> $this->reclamacao_model->get_reclamacao_assunto()
    	);	


    	$this->load->view('ecrm/reclamacao/relatorio', $data);
    }

    public function relatorio_listar()
    {
    	$this->load->model('projetos/reclamacao_model');

		$args = array(
			'dt_inclusao_ini'      		          => $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'      		          => $this->input->post('dt_inclusao_fim', TRUE),
			'cd_reclamacao_retorno_classificacao' => $this->input->post('cd_reclamacao_retorno_classificacao', TRUE),
			'cd_reclamacao_assunto'			  	  => $this->input->post('cd_reclamacao_assunto', TRUE),
			'fl_situacao'                         => $this->input->post('fl_situacao', TRUE)
		);

		manter_filtros($args);
		
        $data['collection'] = $this->reclamacao_model->listar_relatorio($args);

        $this->load->view('ecrm/reclamacao/relatorio_result', $data);
    }

	public function cadastro($numero = 0, $ano = 0, $tipo = '', $cd_reclamacao_origem = 0, $cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0, $cd_atendimento = 0)
    {
		$this->load->model('projetos/reclamacao_model');

		$data = array(
			'origem'   => $this->reclamacao_model->get_reclamacao_origem(),
			'programa' => $this->reclamacao_model->get_reclamacao_programa(),
			'planos'   => $this->reclamacao_model->get_planos(),
			'assunto'  => $this->reclamacao_model->get_reclamacao_assunto()
		);

		$data['fl_acao_responsavel'] = FALSE;
				
		if((intval($numero) == 0) and (intval($ano) == 0))
		{
			$plano_participante = $this->reclamacao_model->get_plano_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia);
			
			$cd_plano = '';

			if(count($plano_participante) > 0)
			{
				$cd_plano = $plano_participante['cd_plano'];
			}
		
			$data['row'] = array(
				'cd_reclamacao'          => '',
                'numero'                 => 0,
                'ano'                    => 0,  
                'tipo'                   => $tipo,  
                'cd_empresa'             => intval($cd_empresa),  
                'cd_registro_empregado'  => intval($cd_registro_empregado),  
                'seq_dependencia'        => intval($seq_dependencia),  
                'nome'                   => '',  
                'descricao'              => '',  
                'cd_reclamacao_origem'   => intval($cd_reclamacao_origem),  
                'cd_atendimento'         => intval($cd_atendimento),  
                'cd_reclamacao_programa' => 0,
                'cd_reclamacao_assunto'  => 0,
                'cd_reclamacao_comite'   => 0,
                'dt_exclusao'            => '',
                'cd_plano'               => $cd_plano,
                'email_novo'             => '',
                'telefone_1'             => '',
                'telefone_2'             => ''
            );
		}
		else
		{
            $data['row'] = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));

            $data['atendimento'] = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));

            $data['programa'] = $this->reclamacao_model->get_reclamacao_programa();

            $data['assunto'] = $this->reclamacao_model->get_reclamacao_assunto();

            $data['usuarios'] = array();

            if(trim($data['atendimento']['cd_divisao']) != '')
            {
				$data['usuarios'] = $this->reclamacao_model->get_usuarios($data['atendimento']['cd_divisao']);
            }
            
            $data['cd_usuario'] = $this->session->userdata('codigo');

			$data['permissao'] = $this->get_permissoes(
				intval($numero), 
				intval($ano), 
				trim($tipo), 
				$data['row'], 
				$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
				$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
			);
		}

		$this->load->view('ecrm/reclamacao/cadastro',$data);
    }	

    public function listar_reclamacao_anterior()
    {
        $this->load->model('projetos/reclamacao_model');
		
		$args = array(
			'numero'                => $this->input->post('numero', TRUE),
			'ano'                   => $this->input->post('ano', TRUE),
			'tipo'                  => $this->input->post('tipo', TRUE),
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
			'dt_inclusao_ini'       => calcular_data('', '1 year'),
			'dt_inclusao_fim'       => date('d/m/Y')
		);
		
		$data = array(
			'collection'    => $this->reclamacao_model->listar_reclamacao_anterior($args),
			'cd_reclamacao' => $this->input->post('cd_reclamacao', TRUE)
		);
		
        $this->load->view('ecrm/reclamacao/cadastro_result', $data);
    }	

	public function salvar_reclamacao()
    {
    	$this->load->model('projetos/reclamacao_model');

    	$cd_reclamacao = $this->input->post('cd_reclamacao', TRUE);

		$args = array(
			'numero'                  => $this->input->post('numero', TRUE),
			'ano'                     => $this->input->post('ano', TRUE),
			'tipo'                    => $this->input->post('tipo', TRUE),
			'cd_empresa'              => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado'   => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'         => $this->input->post('seq_dependencia', TRUE),
			'nome'                    => $this->input->post('nome', TRUE),
			'cd_reclamacao_origem'    => $this->input->post('cd_reclamacao_origem', TRUE),
			'cd_reclamacao_programa'  => $this->input->post('cd_reclamacao_programa', TRUE),
			'cd_reclamacao_assunto'   => $this->input->post('cd_reclamacao_assunto', TRUE),
			'cd_atendimento'          => $this->input->post('cd_atendimento', TRUE),
			'descricao'               => $this->input->post('descricao', TRUE),
			'cd_plano'                => $this->input->post('cd_plano', TRUE),
			'email'                   => $this->input->post('email_novo', TRUE),
			'telefone_1'              => $this->input->post('telefone_1', TRUE),
			'telefone_2'              => $this->input->post('telefone_2', TRUE),
			'cd_usuario'              => $this->session->userdata('codigo'),
			'cd_gerencia_solicitante' => $this->session->userdata('divisao')
		);


		/*
		if((intval($args['cd_reclamacao_programa']) == 6) AND (trim($args['tipo']) == "R"))
		{
			$this->reclamacao_model->salvar_reclamacao_seguro($args);

		}
		else 
		*/
		if(trim($cd_reclamacao) == '')
		{
			$cd_reclamacao = $this->reclamacao_model->salvar_reclamacao($args);
		}
		else
		{	
			$cd_reclamacao = intval($args['numero']).'/'.intval($args['ano']).'/'.trim($args['tipo']);

			$this->reclamacao_model->atualizar_reclamacao(intval($args['numero']), intval($args['ano']), trim($args['tipo']), $args);
		}
		
		redirect('ecrm/reclamacao/cadastro/'.$cd_reclamacao, 'refresh');
    }

    public function cancelar_reclamacao($numero, $ano, $tipo)
    {
    	if(gerencia_in(array('GRSC')))
    	{
			$this->load->model('projetos/reclamacao_model');
			
			$this->reclamacao_model->cancelar_reclamacao($numero, $ano, $tipo, $this->session->userdata('codigo'));

			redirect('ecrm/reclamacao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
    /*
	public function atendimento($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');
		
		$data['programa'] = $this->reclamacao_model->get_reclamacao_programa();

		$data['row'] = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));

		if(intval($data['row']['cd_operacao']) == 0)
		{
			$data['row']['dt_prazo'] = calcular_data('',5,'+', TRUE);
		}

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
		);

		$data['usuarios'] = array();
		
		$this->load->view('ecrm/reclamacao/atendimento', $data);
	}	
	*/
	public function get_usuarios()
    {
    	$this->load->model('projetos/reclamacao_model');

		$cd_divisao = $this->input->post('cd_divisao', TRUE);
		
		$usuarios = $this->reclamacao_model->get_usuarios($cd_divisao);
		
		#$usuarios = array_map('arrayToUTF8', $usuarios);
		
		$ar_reg_json = Array();
		foreach ($usuarios as $item)
		{
			$ar_reg_json[] = array_map("arrayToUTF8", $item);		
		}
		
		echo json_encode($ar_reg_json);
    }

    public function get_ferias()
    {
    	$this->load->model('projetos/reclamacao_model');

		$cd_usuario = $this->input->post('cd_usuario', TRUE);
		
		$ferias = $this->reclamacao_model->get_ferias($cd_usuario);
		
		echo json_encode($ferias);
    }

    public function salvar_atendimento()
    {
		$this->load->model('projetos/reclamacao_model');

		$cd_operacao = $this->input->post('cd_operacao', TRUE);
		
		$args = array(
			'numero'                 => $this->input->post('numero', TRUE),
			'ano'                    => $this->input->post('ano', TRUE),
			'tipo'                   => $this->input->post('tipo', TRUE),
			'cd_divisao'             => $this->input->post('cd_divisao', TRUE),
			'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
			'dt_prazo_acao'          => calcular_data('', 2, '+', TRUE),
			'dt_prazo'               => calcular_data('', 5, '+', TRUE),
			'cd_usuario'             => $this->session->userdata('codigo')
		);

		$atendimento_reclamacao = $this->reclamacao_model->atendimento(intval($args['numero']), intval($args['ano']), trim($args['tipo']));
		
		if(intval($cd_operacao) == 0)
		{
			$this->reclamacao_model->salvar_atendimento($args);
        }

        $this->registra_responsavel($args, intval($args['numero']), intval($args['ano']), trim($args['tipo']));

		/*
		$this->reclamacao_model->atualizar_reclamacao_programa(intval($args['numero']), intval($args['ano']), trim($args['tipo']), $cd_reclamacao_programa);

		if(count($atendimento_reclamacao) > 0)
		{
			if(
				(intval($atendimento_reclamacao['cd_usuario_responsavel']) > 0)
				AND 
				(intval($atendimento_reclamacao['cd_usuario_responsavel']) != intval($args['cd_usuario_responsavel'])) 
				AND 
				(trim($args['tipo']) == 'R')
			)
			{
				$this->altera_responsavel($args['numero'], $args['ano'], $args['tipo'], $args['cd_usuario_responsavel']);
			}
		}
		*/

		redirect('ecrm/reclamacao/cadastro/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');					
	}

	private function registra_responsavel($args, $numero, $ano, $tipo)
	{
        $this->load->model(array(
            'projetos/reclamacao_model',
            'projetos/eventos_email_model'
        ));

        $ds_responsaveis_atendimento = $this->reclamacao_model->get_email_usuario_responsavel_atendimento($args['cd_usuario_responsavel']);

        if(trim($tipo) == 'R')
        {
            $ds_tipo_reclamacao = 'Reclamação';
        }
        else
        {
            $ds_tipo_reclamacao = 'Sugestão';
        }

        $cd_evento = 356;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array('[TIPO_RECLAMACAO]', '[LINK]');

        $subs = array(
            $ds_tipo_reclamacao,
            site_url('ecrm/reclamacao/cadastro/'.intval($numero).'/'.intval($ano).'/'.trim($tipo))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $ds_usuario_email = $this->session->userdata('usuario').'@eletroceee.com.br';
		
		$args = array(
			'de'      => 'Reclamação - Atendimento',
			'assunto' => $email['assunto'],
			'para'    => $ds_usuario_email.';'.strtolower(implode(';', $ds_responsaveis_atendimento)),
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $cd_usuario = $this->session->userdata('codigo');
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}

	private function altera_responsavel($numero, $ano, $tipo, $cd_usuario_responsavel)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 212;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$email_para = $this->reclamacao_model->get_email_atendimento($numero, $ano, $tipo, $cd_usuario_responsavel); 
		
		$tags = '[LINK]';
		$subs = site_url('ecrm/reclamacao/atendimento/'.intval($numero).'/'.intval($ano).'/'.trim($tipo));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$args = array(
			'de'      => 'Reclamação - Atendimento',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function prorrogacao($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');

    	$data['reclamacao'] = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));
		
		$data['row'] = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
		);

		$data['dt_prorrogacao_acao_default'] = calcular_data('', 2, '+', TRUE);
		$data['dt_prorrogacao_default']      = calcular_data('', 5, '+', TRUE);
		
		if(trim($data['row']['dt_prorrogacao_acao']) != '')
		{
			$data['dt_prorrogacao_acao_default'] = $data['row']['dt_prorrogacao_acao'];
		}

		if(trim($data['row']['dt_prorrogacao']) != '')
		{
			$data['dt_prorrogacao_default'] = $data['row']['dt_prorrogacao'];
		}		

		$this->load->view('ecrm/reclamacao/prorrogacao', $data);
    }

    public function salvar_atendimento_prorrogacao()
    {
		$this->load->model('projetos/reclamacao_model');

    	$numero = $this->input->post('numero', TRUE);
    	$ano    = $this->input->post('ano', TRUE);
        $tipo   = $this->input->post('tipo', TRUE);
        
        $atendimento_reclamacao = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));
    	$encerramento           = $this->reclamacao_model->get_verifica_encerramento(intval($numero), intval($ano), trim($tipo));
		$responsavel            = $this->reclamacao_model->verifica_responsavel(intval($numero), intval($ano), trim($tipo), intval($this->session->userdata('codigo')));
		
		if(intval($encerramento['fl_encerramento']) > 0 OR intval($responsavel['fl_responsavel']) > 0 OR gerencia_in(array('GRSC')))
		{		
			$tp_prorrogacao = $this->input->post('tp_prorrogacao', TRUE);

			if(intval($tp_prorrogacao) == 2)
			{
				$dt_prorrogacao_acao = calcular_data('', 2, '+', TRUE);
				$dt_prorrogacao      = $this->input->post('dt_prorrogacao', TRUE);
			}
			else if(intval($tp_prorrogacao) == 3)
			{
				$dt_prorrogacao_acao = $this->input->post('dt_prorrogacao_acao', TRUE);
				$dt_prorrogacao      = $this->input->post('dt_prorrogacao', TRUE);
			}
			else
			{
				$dt_prorrogacao_acao = calcular_data('', 2, '+', TRUE);
				$dt_prorrogacao      = calcular_data('', 5, '+', TRUE);
			}

			$args = array(
				'numero'                       => $numero,
				'ano'                          => $ano,
				'tipo'                         => $tipo,
				'tp_prorrogacao'               => $tp_prorrogacao,
				'dt_prorrogacao_acao'          => $dt_prorrogacao_acao,
				'dt_prorrogacao'               => $dt_prorrogacao,
				'ds_justificativa_prorrogacao' => $this->input->post('ds_justificativa_prorrogacao', TRUE),
				'arquivo'                      => $this->input->post('arquivo', TRUE),
				'arquivo_nome'                 => $this->input->post('arquivo_nome', TRUE),
				'cd_usuario'                   => $this->session->userdata('codigo')
			);

            $this->reclamacao_model->salvar_atendimento_prorrogacao($args);
            
            $this->prorroga_prazo($atendimento_reclamacao);
			
			redirect('ecrm/reclamacao/prorrogacao/'.$numero.'/'.$ano.'/'.$tipo, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    private function prorroga_prazo($atendimento_reclamacao)
	{
        $this->load->model(array(
            'projetos/reclamacao_model',
            'projetos/eventos_email_model'
        ));

        $ds_responsaveis_atendimento = $this->reclamacao_model->get_email_usuario_responsavel_atendimento($atendimento_reclamacao['cd_usuario_responsavel']);

        if(trim($atendimento_reclamacao['tipo']) == 'R')
        {
            $ds_tipo_reclamacao = 'Reclamação';
        }
        else
        {
            $ds_tipo_reclamacao = 'Sugestão';
        }

        $cd_evento = 357;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array('[TIPO_RECLAMACAO]', '[LINK]');

        $subs = array(
            $ds_tipo_reclamacao,
            site_url('ecrm/reclamacao/cadastro/'.intval($atendimento_reclamacao['numero']).'/'.intval($atendimento_reclamacao['ano']).'/'.trim($atendimento_reclamacao['tipo']))
        );

        $texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Atendimento',
			'assunto' => $email['assunto'],
			'para'    => strtolower(implode(';', $ds_responsaveis_atendimento)),
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $cd_usuario = $this->session->userdata('codigo');
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}

    public function reencaminhamento($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');

    	$data['reclamacao'] = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));
		
		$data['row'] = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));

		$data['collection'] = $this->reclamacao_model->reencaminhamento(intval($numero), intval($ano), trim($tipo));

		$data['usuarios'] = $this->reclamacao_model->atendimento_get_usuario($data['row']['cd_divisao'], $data['row']['cd_usuario_responsavel']);

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
		);
		
		$this->load->view('ecrm/reclamacao/reencaminhamento', $data);
    }

	public function salvar_atendimento_reencaminhamento()
    {
    	$this->load->model('projetos/reclamacao_model');

    	$numero = $this->input->post('numero', TRUE);
    	$ano    = $this->input->post('ano', TRUE);
    	$tipo   = $this->input->post('tipo', TRUE);

    	$encerramento = $this->reclamacao_model->get_verifica_encerramento(intval($numero), intval($ano), trim($tipo));
		$responsavel  = $this->reclamacao_model->verifica_responsavel(intval($numero), intval($ano), trim($tipo), intval($this->session->userdata('codigo')));
		
		if(intval($encerramento['fl_encerramento']) > 0 OR intval($responsavel['fl_responsavel']) > 0 OR gerencia_in(array('GRSC')))
		{		
			$args = array(
				'numero'                                  => $numero,
				'ano'                                     => $ano,
				'tipo'                                    => $tipo,
				'dt_prazo_acao'                           => calcular_data('', 2, '+', TRUE),
				'dt_prazo'                                => calcular_data('', 5, '+', TRUE),
				'cd_divisao_reencaminhamento'             => $this->input->post('cd_divisao_reencaminhamento', TRUE),
				'cd_usuario_responsavel_reencaminhamento' => $this->input->post('cd_usuario_responsavel_reencaminhamento', TRUE),
				'ds_justificativa_reencaminhamento'       => $this->input->post('ds_justificativa_reencaminhamento', TRUE),
				'cd_usuario'                              => $this->session->userdata('codigo')
			);

			$this->reclamacao_model->salvar_atendimento_reencaminhamento($args);
		
			$this->altera_responsavel($args['numero'], $args['ano'], $args['tipo'], $args['cd_usuario_responsavel_reencaminhamento']);
			
			redirect('ecrm/reclamacao/reencaminhamento/'.$numero.'/'.$ano.'/'.$tipo, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function acompanhamento($numero, $ano, $tipo)
    {
		$this->load->model('projetos/reclamacao_model');

		$data['row'] = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));	
		
        $data['acompanhamento'] = $this->reclamacao_model->listar_acompanhamento(intval($numero), intval($ano), trim($tipo));	

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
		);

		$this->load->view('ecrm/reclamacao/acompanhamento', $data);
    }

    public function salvar_acompanhamento()
    {
		$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'            => $this->input->post('numero', TRUE),
			'ano'               => $this->input->post('ano', TRUE),
			'tipo'              => $this->input->post('tipo', TRUE),
			'ds_acompanhamento' => $this->input->post('ds_acompanhamento', TRUE),
			'cd_usuario'        => $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_acompanhamento($args);
		
		redirect('ecrm/reclamacao/acompanhamento/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }

    public function anexo($numero, $ano, $tipo)
    {
		$this->load->model('projetos/reclamacao_model');
		
		$data['row'] = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));	
		
        $data['anexo'] = $this->reclamacao_model->listar_anexo(intval($numero), intval($ano), trim($tipo));	

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			$this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo))
		);

		$this->load->view('ecrm/reclamacao/anexo', $data);
    }

    public function salvar_anexo()
    {
		$this->load->model('projetos/reclamacao_model');

		$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

		$args = array(
			'numero'     => $this->input->post('numero', TRUE),
			'ano'        => $this->input->post('ano', TRUE),
			'tipo'       => $this->input->post('tipo', TRUE),
			'cd_usuario' => $this->session->userdata('codigo')
		);

		if($qt_arquivo > 0)
		{
			$nr_conta = 0;

			while($nr_conta < $qt_arquivo)
			{	
				$args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
				$args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
				
				$this->reclamacao_model->salvar_anexo($args);
				
				$nr_conta++;
			}
		}
		
		redirect('ecrm/reclamacao/anexo/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }

    public function acao($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');

		$data = array(
			'row'                   => $this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			'reclamacao'            => $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo)),
			'retorno_classificacao' => $this->reclamacao_model->get_reclamacao_retorno_classificacao(),
			'usuario_callcenter'    => $this->reclamacao_model->get_usuario_callcenter($this->session->userdata('codigo')),
			'validacao_comite'      => $this->reclamacao_model->get_validacao_comite(intval($numero), intval($ano), trim($tipo)),
			'parecer_final'         => $this->reclamacao_model->carrega_parecer_final(intval($numero), intval($ano), trim($tipo)),
			'atendimento'           => $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo))
		);

		$data['acao'] = $this->reclamacao_model->acao(intval($numero), intval($ano), trim($tipo));
		
		if(count($data['acao']) == 0)
		{
			$atendimento = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));	
			
			$data['acao'] = array(
				'cd_reclamacao_andamento' => 0,
                'numero'                  => intval($numero),
                'ano'                     => intval($ano),  
                'tipo'                    => $tipo,  
                'descricao'               => '',  
                'dt_inclusao'             => '',  
                'ds_usuario_inclusao'     => '',  
				'cd_reclamacao_comite'    => 0,
                'dt_prazo'                => (trim($atendimento['dt_prazo']) != '' ? $atendimento['dt_prazo'] : ''),
                'dt_prorrogacao'          => (trim($atendimento['dt_prorrogacao']) != '' ? $atendimento['dt_prorrogacao'] : ''),
            );
		}
		
		$data['classificacao'] = $this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo));

		if(count($data['classificacao']) == 0)
		{
			$data['classificacao'] = array(
				'cd_reclamacao_andamento'                 => 0,
                'cd_reclamacao_retorno' 				  => '',
                'cd_reclamacao_comite' 				 	  => '',
                'cd_reclamacao_retorno_classificacao_pai' => '',
                'cd_reclamacao_retorno_classificacao' 	  => '',
                'fl_encaminhar_comite' 	 				  => '',
                'nr_nc'                 				  => '',
                'nr_ano_nc'            					  => '',
                'ds_justificativa'            			  => '',
                'descricao'             				  => '',
                'dt_inclusao'                             => ''
            );
			
			$data['retorno_classificacao_filho'] = array();
		}
		else
		{			
			$data['retorno_classificacao_filho'] = $this->reclamacao_model->retorno_carrega($data['classificacao']['cd_reclamacao_retorno_classificacao_pai']);
		}

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$data['row'],
			$data['classificacao']
		);

		$data['fl_concorda'] = $this->get_dropdown_sim_nao();

		$data['fl_encaminhar_comite'] = $this->get_dropdown_sim_nao();

		$this->load->view('ecrm/reclamacao/acao', $data);
    }

    public function salvar_acao()
    {
		$this->load->model('projetos/reclamacao_model');

		$cd_reclamacao_andamento = $this->input->post('cd_reclamacao_andamento', TRUE);

		$args = array(
			'numero'         => $this->input->post('numero', TRUE),
			'ano'            => $this->input->post('ano', TRUE),
			'tipo'           => $this->input->post('tipo', TRUE),
			'descricao'      => $this->input->post('descricao', TRUE),
			'cd_usuario'     => $this->session->userdata('codigo')
		);

		if(intval($cd_reclamacao_andamento) == 0)
		{
			$this->reclamacao_model->salvar_acao($args);
		}
		else
		{
			$this->reclamacao_model->atualizar_acao($cd_reclamacao_andamento, $args);
		}

		if(trim($args['tipo']) == 'S')
		{
			$this->reclamacao_model->encerra_reclamacao($args['numero'], $args['ano'], $args['tipo']);
		}
	
		redirect('ecrm/reclamacao/acao/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }		

	public function salvar_classificacao()
    {
    	$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'                                  => $this->input->post('numero', TRUE),
			'ano'                                     => $this->input->post('ano', TRUE), 
			'tipo'                                    => $this->input->post('tipo', TRUE),
			'cd_reclamacao_retorno_classificacao'     => $this->input->post('cd_reclamacao_retorno_classificacao', TRUE),
			'cd_reclamacao_retorno_classificacao_pai' => $this->input->post('cd_reclamacao_retorno_classificacao_pai', TRUE),
			'nr_nc'                                   => $this->input->post('nr_nc', TRUE),
			'nr_ano_nc'                               => $this->input->post('nr_ano_nc', TRUE),
			'ds_justificativa'                        => $this->input->post('ds_justificativa', TRUE),
			'fl_encaminhar_comite'                    => $this->input->post('fl_encaminhar_comite', TRUE),
			'cd_usuario'                              => $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_classificacao($args, TRUE);

		if(
			trim($args['tipo']) == 'R' 
			AND 
			trim($args['fl_encaminhar_comite']) == 'S' 
			AND 
			intval($args['nr_ano_nc']) == 0
			AND
			intval($args['nr_nc']) == 0
		)
		{	
			$this->enviar_reclamacao_comite($args);
			
			$this->reclamacao_model->salvar_reclamacao_comite_retorno($args);
			
			foreach($this->reclamacao_model->get_usuarios_comite() as $item)
			{
				$this->reclamacao_model->salvar_reclamacao_comite($args, $item['codigo']);
			}
		}
			
		redirect('ecrm/reclamacao/acao/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }

	public function enviar_reclamacao_comite($args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 213;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$tags = '[LINK]';
		$subs = site_url('ecrm/reclamacao/validacao_comite/'.intval($args['numero']).'/'.intval($args['ano']).'/'.trim($args['tipo']));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$args = array(
			'de'      => 'Reclamação - Ação',
			'assunto' => $email['assunto'],
			'para'    => $email['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function retorno($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');

    	$data = array(
			'row'                 => $this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
			'reclamacao'          => $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo)),
			'classificacao'       => $this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo)),		
			'retorno_atendimento' => $this->reclamacao_model->reclamacao_retorno_atendimento(intval($numero), intval($ano), trim($tipo)),
			'reclamacao_retorno'  => $this->reclamacao_model->reclamacao_retorno()
		);

		if(count($data['retorno_atendimento']) == 0)
		{
			$data['retorno_atendimento'] = array(
				'dt_retorno'            => '',
                'cd_reclamacao_retorno' => '',
                'ds_observacao_retorno' => ''
            );
		}

		$data['permissao'] = $this->get_permissoes(
			intval($numero), 
			intval($ano), 
			trim($tipo), 
			$data['row'], 
			$data['row'],
			$data['classificacao']
		);

		$data['permissao']['fl_acao_retorno'] = false;

		if(gerencia_in(array('GRSC')))
		{
			$data['permissao']['fl_acao_retorno'] = true;
		}

		$this->load->view('ecrm/reclamacao/retorno', $data);
    }

    public function salvar_retorno()
    {
    	$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'                => $this->input->post('numero', TRUE),
			'ano'                   => $this->input->post('ano', TRUE),
			'tipo'                  => $this->input->post('tipo', TRUE),
			'dt_retorno'            => $this->input->post('dt_retorno', TRUE),
			'cd_reclamacao_retorno' => $this->input->post('cd_reclamacao_retorno', TRUE),
			'ds_observacao_retorno' => $this->input->post('ds_observacao_retorno', TRUE),
			'cd_usuario'            => $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_retorno($args);

		redirect('ecrm/reclamacao/retorno/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }

	public function validacao_comite($numero, $ano, $tipo)
	{
		if(trim($this->session->userdata('indic_12')) == '*')
		{
			$this->load->model('projetos/reclamacao_model');

			$validaca_comite = $this->reclamacao_model->get_validacao_comite_confirmada($numero, $ano, $tipo, intval($this->session->userdata('codigo')));

			if(count($validaca_comite) > 0 AND trim($validaca_comite['dt_confirma']) == '')
			{
				$reclamacao = array(		
					'numero'     => $numero,
					'ano'        => $ano,
					'tipo'       => $tipo,
					'cd_usuario' => $this->session->userdata('codigo')
				);

				$data = array(
					'row'              => $this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
					'reclamacao'       => $reclamacao,
					'validacao_comite' => $this->reclamacao_model->get_validacao_comite($numero, $ano, $tipo, intval($this->session->userdata('codigo')))
				);

				$data['status'] = array( 
					array('value' => 'S', 'text' => 'Confirma'),
					array('value' => 'N', 'text' => 'Não Confirma')
				);

				$classificacao = $this->reclamacao_model->classificacao($numero, $ano, $tipo);

				$data['fl_opcao_nc'] = false;

				if(intval($classificacao['cd_reclamacao_retorno_classificacao']) != 6)
				{
					$data['fl_opcao_nc'] = true;
				}
				
				$this->load->view('ecrm/reclamacao/validacao_comite', $data);
			}
			else
			{
				exibir_mensagem('RECLAMAÇÃO ENCERRADA');
			}	
		}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
	}

	public function salvar_validacao_comite()
	{
		$this->load->model('projetos/reclamacao_model');

		$numero = $this->input->post('numero', TRUE);
		$ano    = $this->input->post('ano', TRUE);
		$tipo   = $this->input->post('tipo', TRUE);
		
		$args = array(	
			'fl_confirma' 				=> $this->input->post('fl_confirma', TRUE),
			'fl_abrir_nc'               => $this->input->post('fl_abrir_nc', TRUE),
			'ds_justificativa_confirma' => $this->input->post('ds_justificativa_confirma', TRUE),
			'cd_usuario'  				=> $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_validacao_comite(intval($numero), intval($ano), trim($tipo), $args);
		
		$confirma = $this->reclamacao_model->get_reclamacao_confirmada(intval($numero), intval($ano), trim($tipo));

		if(count($confirma) > 0 AND trim($confirma['fl_encerrado']) == 'S') 
		{
			if(trim($confirma['fl_abrir_nc']) == 'N')
			{
				$this->load->model('projetos/eventos_email_model');
		
				$cd_evento = 279;
				
				$email = $this->eventos_email_model->carrega($cd_evento);
				
				$tags = '[LINK]';
				$subs = site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo));
				
				$texto = str_replace($tags, $subs, $email['email']);
				
				$cd_usuario = $this->session->userdata('codigo');
				
				$args = array(
					'de'      => 'Reclamação - Aguardando Avaliação Final',
					'assunto' => $email['assunto'],
					'para'    => $email['para'],
					'cc'      => $email['cc'],
					'cco'     => $email['cco'],
					'texto'   => $texto
				);
				
				$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
			}
			else
			{
				$args = array(	
					'fl_retorno' 				=> 'S',
					'ds_justificativa_confirma' => 'A maioria do membros do Comitê decide por abrir NC.',
					'cd_usuario'  				=> $this->session->userdata('codigo')
				);

				$this->reclamacao_model->salvar_parecer_comite_avaliacao(intval($numero), intval($ano), trim($tipo), $args);

				$this->reclamacao_model->exlcuir_classificao(intval($numero), intval($ano), trim($tipo), $args);

				if(trim($args['fl_retorno']) == 'S')
				{
					$this->enviar_validacao_comite_nao_confirma($numero, $ano, $tipo, $args, 'S');
				}
				/*
				else
				{
					$this->enviar_validacao_comite_confirma($numero, $ano, $tipo, $args);

					$this->reclamacao_model->encerra_reclamacao($numero, $ano, $tipo);
				}
				*/
			}
		}
		
		redirect('ecrm/reclamacao/acao/'.$numero.'/'.$ano.'/'.$tipo, 'refresh');
	}

	public function parecer_comite()
	{
		if(trim($this->session->userdata('indic_12')) == '*' OR trim($this->session->userdata('codigo')) == '251')
		{
			$data['status'] = array(
				array('value' => 'AM', 'text' => 'Aguardando Avaliação de Membro(s)'),
				array('value' => 'AF', 'text' => 'Aguardando Avaliação Final'),
				array('value' => 'EN', 'text' => 'Encerrada'),
				array('value' => 'AR', 'text' => 'Aguardando Retorno'),
				array('value' => 'AC', 'text' => 'Aguardando Reclassificação')

			);

			$this->load->view('ecrm/reclamacao/parecer_comite', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function parecer_comite_listar()
	{
		$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'               => $this->input->post('numero', TRUE),
			'ano'                  => $this->input->post('ano', TRUE),
			'tipo'                 => $this->input->post('tipo', TRUE),
			'fl_status'            => $this->input->post('fl_status', TRUE),
			'dt_parecer_final_ini' => $this->input->post('dt_parecer_final_ini', TRUE),
			'dt_parecer_final_fim' => $this->input->post('dt_parecer_final_fim', TRUE)
		);

		manter_filtros($args);
		
        $data['collection'] = $this->reclamacao_model->parecer_comite_listar($args);

        foreach($data['collection'] as $key => $reclamacao)
        {
        	$data['collection'][$key]['membros'] = array();

        	$membros = $this->reclamacao_model->menbros_comite_sem_resposta($reclamacao['numero'], $reclamacao['ano'], $reclamacao['tipo']);

        	foreach ($membros as $key2 => $item) 
        	{
        		$data['collection'][$key]['membros'][] = $item['ds_usuario'];
        	}
        }
				
        $this->load->view('ecrm/reclamacao/parecer_comite_result', $data);
	}

	public function parecer_comite_avaliacao($numero, $ano, $tipo)
	{
		if(trim($this->session->userdata('indic_12')) == '*')
		{
			$this->load->model('projetos/reclamacao_model');

			$parecer_final = $this->reclamacao_model->get_parecer_final($numero, $ano, $tipo);

			if(intval($parecer_final['fl_parecer_final']) > 0)
			{
				$data = array(
					'row'              => $this->reclamacao_model->acao_retorno(intval($numero), intval($ano), trim($tipo)),
					'validacao_comite' => $this->reclamacao_model->get_validacao_comite($numero, $ano, $tipo)
				);

				$data['status'] = array( 
					array('value' => 'N', 'text' => 'Confirma'),
					array('value' => 'S', 'text' => 'Não Confirma')
				);

				$this->load->view('ecrm/reclamacao/parecer_comite_avaliacao', $data);
			}
			else
			{
				exibir_mensagem('ACESSO NÃO PERMITIDO');
			}
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_parecer_comite_avaliacao()
	{
		$this->load->model('projetos/reclamacao_model');

		$numero = $this->input->post('numero', TRUE);
		$ano    = $this->input->post('ano', TRUE);
		$tipo   = $this->input->post('tipo', TRUE);
		
		$args = array(	
			'fl_retorno' 				=> $this->input->post('fl_retorno', TRUE),
			'ds_justificativa_confirma' => $this->input->post('ds_justificativa_confirma', TRUE),
			'cd_usuario'  				=> $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_parecer_comite_avaliacao(intval($numero), intval($ano), trim($tipo), $args);

		if(trim($args['fl_retorno']) == 'S')
		{
			$this->enviar_validacao_comite_nao_confirma($numero, $ano, $tipo, $args);

			## RETORNO COMO CONCORDA ## 
			$args = array(	
				'fl_concorda' 				=> 'S',
				'ds_justificativa_concorda' => '',
				'cd_usuario'  				=> $this->session->userdata('codigo')
			);
			
			$this->reclamacao_model->salvar_retorno_responsavel(intval($numero), intval($ano), trim($tipo), $args);
			
			$classificacao = $this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo));

			$ds_acompanhamento = 'Reclassificação em  '.date('d/m/Y H:i:s')."\n";

			$ds_acompanhamento .= $classificacao['ds_reclamacao_retorno_classificacao']."\n";

			if(intval($classificacao['cd_reclamacao_retorno_classificacao_pai']) == 1)
			{
				if(intval($classificacao['cd_reclamacao_retorno_classificacao']) == 8)
				{
					$ds_acompanhamento .= $classificacao['ds_justificativa'];
				}
				else
				{
					$ds_acompanhamento .= 'NC : '.$classificacao['nr_ano_nc'].'/'.$classificacao['nr_nc'];
				}
			}

			$acompanhamento = array(
				'numero'            => $numero,
				'ano'               => $ano,
				'tipo'              => $tipo,
				'ds_acompanhamento' => $ds_acompanhamento,
				'cd_usuario'        => $this->session->userdata('codigo')
			);

			$this->reclamacao_model->salvar_acompanhamento($acompanhamento);

			$this->reclamacao_model->exlcuir_classificao(intval($numero), intval($ano), trim($tipo), $args);
			
			//$this->enviar_validacao_comite_concorda($numero, $ano, $tipo, $args);
		}
		else
		{
			$this->enviar_validacao_comite_confirma($numero, $ano, $tipo, $args);

			$this->reclamacao_model->encerra_reclamacao($numero, $ano, $tipo);
		}

		redirect('ecrm/reclamacao/parecer_comite', 'refresh');
	}

	private function enviar_validacao_comite_confirma($numero, $ano, $tipo, $args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 214;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$email_para = $this->reclamacao_model->get_emails($numero, $ano, $tipo);
		
		$tags = '[LINK]';

		$subs = site_url('ecrm/reclamacao/acao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Encerrada',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
	
	private function enviar_validacao_comite_nao_confirma($numero, $ano, $tipo, $args, $fl_nc = 'N')
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 215;
		
		$email = $this->eventos_email_model->carrega($cd_evento);

		$cd_usuario = $this->session->userdata('codigo');
	
		$email_para = $this->reclamacao_model->get_emails($numero, $ano, $tipo);
		
		$tags = array('[JUSTIFICATIVA]', '[LINK]');
		$subs = array($args['ds_justificativa_confirma'], site_url('ecrm/reclamacao/acao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo)));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Não Confirmada',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'].(trim($fl_nc) == 'S' ? ';'.$email_para['para_gerencia'] : ';'.$email_para['para_gerente'].';'.$email_para['para_gerente_substituto']),
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function salvar_retorno_responsavel()
	{
		$this->load->model('projetos/reclamacao_model');

		$numero = $this->input->post('numero', TRUE);
		$ano    = $this->input->post('ano', TRUE);
		$tipo   = $this->input->post('tipo', TRUE);
		
		$args = array(	
			'fl_concorda' 				=> $this->input->post('fl_concorda', TRUE),
			'ds_justificativa_concorda' => $this->input->post('ds_justificativa_concorda', TRUE),
			'cd_usuario'  				=> $this->session->userdata('codigo')
		);
		
		$this->reclamacao_model->salvar_retorno_responsavel(intval($numero), intval($ano), trim($tipo), $args);
		
		if(trim($args['fl_concorda']) == 'S')
		{
			$classificacao = $this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo));

			$ds_acompanhamento = 'Reclassificação em  '.date('d/m/Y H:i:s')."\n";

			$ds_acompanhamento .= $classificacao['ds_reclamacao_retorno_classificacao']."\n";

			if(intval($classificacao['cd_reclamacao_retorno_classificacao_pai']) == 1)
			{
				if(intval($classificacao['cd_reclamacao_retorno_classificacao']) == 8)
				{
					$ds_acompanhamento .= $classificacao['ds_justificativa'];
				}
				else
				{
					$ds_acompanhamento .= 'NC : '.$classificacao['nr_ano_nc'].'/'.$classificacao['nr_nc'];
				}
			}

			$acompanhamento = array(
				'numero'            => $numero,
				'ano'               => $ano,
				'tipo'              => $tipo,
				'ds_acompanhamento' => $ds_acompanhamento,
				'cd_usuario'        => $this->session->userdata('codigo')
			);

			$this->reclamacao_model->salvar_acompanhamento($acompanhamento);

			$this->reclamacao_model->exlcuir_classificao(intval($numero), intval($ano), trim($tipo), $args);
			
			$this->enviar_validacao_comite_concorda($numero, $ano, $tipo, $args);
		}
		else
		{
			$this->enviar_reclamacao_comite_nao_concorda($numero, $ano, $tipo, $args);

			$this->reclamacao_model->encerra_reclamacao($numero, $ano, $tipo);
		}
		
		redirect('ecrm/reclamacao/acao/'.$numero.'/'.$ano.'/'.$tipo, 'refresh');
	}

	private function enviar_reclamacao_comite_nao_concorda($numero, $ano, $tipo, $args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 218;
		
		$email = $this->eventos_email_model->carrega($cd_evento);

		$cd_usuario = $this->session->userdata('codigo');
		
		$email_para = $this->reclamacao_model->get_emails($numero, $ano, $tipo);
		
		$tags = array('[JUSTIFICATIVA]', '[LINK]');
		$subs = array($args['ds_justificativa_concorda'],site_url('ecrm/reclamacao/acao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo)));
				
		$texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Encerrada - Não Concorda',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    private function enviar_validacao_comite_concorda($numero, $ano, $tipo, $args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 217;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$email_para = $this->reclamacao_model->get_emails($numero, $ano, $tipo);
		
		$tags = '[LINK]';
		$subs = site_url('ecrm/reclamacao/acao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Concorda',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
	
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function salvar_reclassificacao()
    {
    	$this->load->model('projetos/reclamacao_model');

		$args = array(
			'numero'                                  => $this->input->post('numero', TRUE),
			'ano'                                     => $this->input->post('ano', TRUE), 
			'tipo'                                    => $this->input->post('tipo', TRUE),
			'cd_reclamacao_retorno_classificacao'     => $this->input->post('cd_reclamacao_retorno_classificacao', TRUE),
			'cd_reclamacao_retorno_classificacao_pai' => $this->input->post('cd_reclamacao_retorno_classificacao_pai', TRUE),
			'nr_nc'                                   => $this->input->post('nr_nc', TRUE),
			'nr_ano_nc'                               => $this->input->post('nr_ano_nc', TRUE),
			'ds_justificativa'                        => $this->input->post('ds_justificativa', TRUE),
			'fl_encaminhar_comite'                    => 'N',
			'cd_usuario'                              => $this->session->userdata('codigo')
		);

		$this->reclamacao_model->salvar_classificacao($args);

		$this->enviar_email_reclassificacao($args['numero'], $args['ano'], $args['tipo']);

		$this->reclamacao_model->encerra_reclamacao($args['numero'], $args['ano'], $args['tipo']);
			
		redirect('ecrm/reclamacao/acao/'.$args['numero'].'/'.$args['ano'].'/'.$args['tipo'], 'refresh');
    }

    private function enviar_email_reclassificacao($numero, $ano, $tipo)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 280;
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$email_para = $this->reclamacao_model->get_emails($numero, $ano, $tipo);
		
		$tags = '[LINK]';
		$subs = site_url('ecrm/reclamacao/acao/'.intval($numero).'/'.intval($ano).'/'.trim($tipo));
		
		$texto = str_replace($tags, $subs, $email['email']);
		
		$args = array(
			'de'      => 'Reclamação - Reclassificada',
			'assunto' => $email['assunto'],
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
	
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function pdf($numero, $ano, $tipo)
    {
    	$this->load->model('projetos/reclamacao_model');

    	$row                 = $this->reclamacao_model->reclamacao(intval($numero), intval($ano), trim($tipo));
    	$atendimento         = $this->reclamacao_model->atendimento(intval($numero), intval($ano), trim($tipo));
    	$acompanhamento      = $this->reclamacao_model->listar_acompanhamento(intval($numero), intval($ano), trim($tipo));
    	$acao                = $this->reclamacao_model->acao(intval($numero), intval($ano), trim($tipo));
    	$classificacao       = $this->reclamacao_model->classificacao(intval($numero), intval($ano), trim($tipo));
    	$validacao_comite    = $this->reclamacao_model->get_validacao_comite(intval($numero), intval($ano), trim($tipo));
		$parecer_final       = $this->reclamacao_model->carrega_parecer_final(intval($numero), intval($ano), trim($tipo));
		$retorno_atendimento = $this->reclamacao_model->reclamacao_retorno_atendimento(intval($numero), intval($ano), trim($tipo));

    	$this->load->plugin('fpdf');

		$ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = 'Reclamação '.$row['cd_reclamacao'];

		$ob_pdf->AddPage();

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '1 - Cadastro', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuil', '', 10);

		$ob_pdf->MultiCell(190, 5.5, 'RE: '.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Nome: '.$row['nome'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Descrição: '.$row['descricao'], 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '2 - Atendimento', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuil', '', 10);

		$ob_pdf->MultiCell(190, 5.5, 'Responsável: '.$atendimento['ds_usuario_reponsavel'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Programa: '.$atendimento['ds_reclamacao_origem'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Dt. Prazo: '.$atendimento['dt_prazo'], 0, 'L');

		if(trim($atendimento['dt_prazo']) != '')
		{
			$ob_pdf->MultiCell(190, 5.5, 'Dt. Prorrogação: '.$atendimento['dt_prorrogacao'], 0, 'L');
			$ob_pdf->MultiCell(190, 5.5, 'Justificativa Prorrogação: '.$atendimento['ds_justificativa_prorrogacao'], 0, 'L');
		}

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '3 - Acompanhamento', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 10);

		$ob_pdf->SetWidths(array(35, 54, 103));
        $ob_pdf->SetAligns(array('C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->Row(array('Dt. Inclusão', 'Usuário', 'Descrição'));
        $ob_pdf->SetAligns(array('C', 'L', 'L'));
        $ob_pdf->SetFont('segoeuil', '', 10);

        foreach ($acompanhamento as $key => $item) 
        {
        	$ob_pdf->Row(array(
        		$item['dt_inclusao'], 
        		$item['ds_usuario_inclusao'],
        		$item['ds_acompanhamento']
    		));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '4 - Ação', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuil', '', 10);

		$ob_pdf->MultiCell(190, 5.5, 'Dt. Ação: '.$acao['dt_inclusao'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Descrição: '.$acao['descricao'], 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '5 - Classificação', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuil', '', 10);

		$ob_pdf->MultiCell(190, 5.5, 'Dt. Classificação: '.$classificacao['dt_inclusao'], 0, 'L');
		$ob_pdf->MultiCell(190, 5.5, 'Classificação: '.$classificacao['ds_reclamacao_retorno_classificacao'], 0, 'L');

		if(intval($classificacao['cd_reclamacao_retorno_classificacao']) == 6)
		{
			$ob_pdf->MultiCell(190, 5.5, 'NC: '.$classificacao['nr_ano_nc'].'/'.$classificacao['nr_nc'], 0, 'L');
		}
		else if(intval($classificacao['cd_reclamacao_retorno_classificacao']) == 8)
		{
			$ob_pdf->MultiCell(190, 5.5, 'Justificativa: '.$classificacao['ds_justificativa'], 0, 'L');
		}

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '6 - Retorno', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuil', '', 10);

		if(count($retorno_atendimento) > 0)
		{
			$ob_pdf->MultiCell(190, 5.5, 'Dt. Retorno: '.$retorno_atendimento['dt_retorno'], 0, 'L');
			$ob_pdf->MultiCell(190, 5.5, 'Forma: '.$retorno_atendimento['ds_reclamacao_retorno'], 0, 'L');

			if(trim($retorno_atendimento['ds_observacao_retorno']) != '')
			{
				$ob_pdf->MultiCell(190, 5.5, 'Observações: '.$retorno_atendimento['ds_observacao_retorno'], 0, 'L');
			}
		}
		else
		{	
			$ob_pdf->MultiCell(190, 5.5, 'Sem Retorno.', 0, 'L');
		}
		

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetFont('segoeuib', '', 12);

		$ob_pdf->MultiCell(190, 5.5, '7 - Parecer do Comitê', 0, 'L');

		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$ob_pdf->SetWidths(array(54, 30, 72, 35));
        $ob_pdf->SetAligns(array('C', 'C', 'C', 'C'));
        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->Row(array('Nome', 'Status', 'Justificativa', 'Dt. Parecer'));
        $ob_pdf->SetAligns(array('L', 'C', 'L', 'C'));
        $ob_pdf->SetFont('segoeuil', '', 10);

        foreach($validacao_comite as $item)
		{
			$ob_pdf->Row(array(
        		$item['ds_usuario_comite'], 
        		$item['ds_confirma'],
        		$item['ds_justificativa_confirma'],
        		$item['dt_confirma']
    		));
		}	

		if(count($parecer_final) > 0 AND trim($parecer_final['dt_parecer_final']) != '')
		{
			$ob_pdf->SetFont('segoeuib', '', 10);
			
			$ob_pdf->Row(array(
        		$parecer_final['ds_usuario_comite'], 
        		$parecer_final['ds_confirma'],
        		$parecer_final['ds_justificativa_confirma'],
        		$parecer_final['dt_parecer_final']
    		));
		}
		

		/*
		echo form_default_integer_ano('nr_ano_nc', 'nr_nc', 'Não Conformidade (Ano/Número): (*)', $classificacao['nr_ano_nc'], $classificacao['nr_nc']);		
		echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $classificacao['ds_justificativa'], 'style="width:500px; height: 100px;"');	

		*/

		$ob_pdf->Output();
    }
}
?>