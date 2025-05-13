<?php
class acompanhamento extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/acompanhamento_projetos_model');
    }

    function index()
    {
		if(gerencia_in(array('GI')))
		{
			$this->load->view('atividade/acompanhamento/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}		
    }

    function listar()
    {
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args["dt_acompanhamento_ini"] = $this->input->post("dt_acompanhamento_ini", TRUE);
			$args["dt_acompanhamento_fim"] = $this->input->post("dt_acompanhamento_fim", TRUE);
			$args["dt_encerramento_ini"]   = $this->input->post("dt_encerramento_ini", TRUE);
			$args["dt_encerramento_fim"]   = $this->input->post("dt_encerramento_fim", TRUE);		
			
			manter_filtros($args);

			$this->acompanhamento_projetos_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$data['ar_analista'] = Array();
			
			foreach($data['collection'] as $acompanhamento)
			{				
				$args['cd_acomp']   = $acompanhamento['cd_acomp'];
				$args['cd_projeto'] = $acompanhamento['cd_projeto'];
				
				$this->acompanhamento_projetos_model->analista($result, $args);
				
				$data['ar_analista'][$acompanhamento['cd_acomp']."-".$acompanhamento['cd_projeto']] = $result->result_array();
			}

			$this->load->view('atividade/acompanhamento/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}       
    }
	
    function cadastro($cd_acomp = 0)
    {	
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = intval($cd_acomp);

			$this->acompanhamento_projetos_model->projeto($result, $args);
			$data['arr_projeto'] = $result->result_array();	
			
			$data['arr_analista_checked'] = Array();		
			
			if(intval($args['cd_acomp']) == 0)
			{
				$data['row'] = Array(
					'cd_acomp'        => $args['cd_acomp'],
				    'cd_projeto'      => '',
					'dt_encerramento' => '',
					'dt_cancelamento' => '',
					'dt_email'        => ''
				);
				
				$args['cd_projeto'] = 0;
				
				$this->acompanhamento_projetos_model->responsavel($result, $args);
				$data['arr_analista'] = $result->result_array();	
			}
			else
			{

				$this->acompanhamento_projetos_model->cadastro($result, $args);
				$data['row'] = $result->row_array();	

				$args['cd_projeto'] = $data['row']['cd_projeto'];
				
				$this->acompanhamento_projetos_model->responsavel($result, $args);
				$data['arr_analista'] = $result->result_array();		
			
				$this->acompanhamento_projetos_model->analista($result, $args);
				$ar_analista = $result->result_array();
				
				$data['arr_analista_checked'] = Array();
				
				foreach($ar_analista as $item)
				{				
					$data['arr_analista_checked'][] = $item['cd_analista'];
				}
			}
			
			$this->load->view('atividade/acompanhamento/cadastro',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}		
    }
	
	function salvar()
	{
		if(gerencia_in(array('GI')))
		{			
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_acomp"]     = $this->input->post("cd_acomp", TRUE);
			$args["cd_projeto"]   = $this->input->post("cd_projeto", TRUE);
			$args["arr_analista"] = $this->input->post("arr_analista", TRUE);
			$args["cd_usuario"]   = $this->session->userdata('codigo');
		
			$cd_acomp = $this->acompanhamento_projetos_model->salvar( $result, $args );
			
			redirect("atividade/acompanhamento/cadastro/".$cd_acomp, "refresh");	
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}	
	}
	
	function envia_email()
	{
		if(gerencia_in(array('GI')))
		{			
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_acomp"] = $this->input->post("cd_acomp", TRUE);
		
			$this->acompanhamento_projetos_model->envia_email( $result, $args );
			$row = $result->row_array();
			
			echo json_encode($row);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function encerra($cd_acomp)
	{
		if(gerencia_in(array('GI')))
		{			
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_acomp"]   = $cd_acomp;
			$args["cd_usuario"] = $this->session->userdata('codigo');
		
			$this->acompanhamento_projetos_model->encerra( $result, $args );
			
			redirect("atividade/acompanhamento/cadastro/".$cd_acomp, "refresh");	
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function cancela($cd_acomp)
	{
		if(gerencia_in(array('GI')))
		{			
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_acomp"]   = $cd_acomp;
			$args["cd_usuario"] = $this->session->userdata('codigo');
		
			$this->acompanhamento_projetos_model->cancela( $result, $args );
			
			redirect("atividade/acompanhamento/cadastro/".$cd_acomp, "refresh");	
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}

    function reuniao($cd_acomp)
    {	
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = intval($cd_acomp);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->acompanhamento_projetos_model->reuniao($result, $args);
			$data['arr_reuniao'] = $result->result_array();

			$this->acompanhamento_projetos_model->envolvido($result, $args);
			$ar_reuniao_envolvido = $result->result_array();	
			
			$data['arr_reuniao_envolvido'] = Array();
			
			foreach($ar_reuniao_envolvido as $item)
			{				
				$data['arr_reuniao_envolvido'][$item['cd_reuniao']][] = $item['nome'];
			}
			
			$this->load->view('atividade/acompanhamento/reuniao',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}	
    }	
	
	function cadastro_reuniao($cd_acomp, $cd_reuniao = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args['cd_acomp']   = intval($cd_acomp);
			$args['cd_reuniao'] = intval($cd_reuniao);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->acompanhamento_projetos_model->presentes($result, $args);
			$data['arr_presente'] = $result->result_array();
			
			$data['arr_presente_checked'] = array();
			
			if($args['cd_reuniao'] == 0)
			{
				$data['row_reunicao'] = array(
					'cd_reuniao'        => $args['cd_reuniao'],
					'dt_reuniao'        => '',
					'descricao'         => '',
					'motivo'            => '',
					'assunto'           => '',
					'ds_arquivo'        => '',
					'ds_arquivo_fisico' => ''
				);
			}
			else
			{
				$this->acompanhamento_projetos_model->cadastro_reuniao($result, $args);
				$data['row_reunicao'] = $result->row_array();	
				
				$this->acompanhamento_projetos_model->presentes_reuniao($result, $args);
				$arr = $result->result_array();
				
				foreach($arr as $item)
				{
					$data['arr_presente_checked'][] = $item['cd_usuario'];
				}
			}
			
			$this->load->view('atividade/acompanhamento/cadastro_reuniao',$data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_reuniao()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args["cd_acomp"]            = $this->input->post("cd_acomp", TRUE);
			$args["cd_reuniao"]          = $this->input->post("cd_reuniao", TRUE);
			$args["dt_reuniao"]          = $this->input->post("dt_reuniao", TRUE);
			$args["descricao"]           = $this->input->post("descricao", TRUE);
			$args["motivo"]              = $this->input->post("motivo", TRUE);
			$args["assunto"]             = $this->input->post("assunto", TRUE);
			$args["cd_usuario"]          = $this->session->userdata('codigo');
			$args['cd_usuario_presente'] = array();
			
			$arr_presentes = array();
		
			$arr_presentes = $this->input->post("arr_presentes", TRUE); 

			if(is_array($arr_presentes))
			{
				foreach($arr_presentes as $key => $item)
				{
					$args['cd_usuario_presente'][] = $item;
				}
			}

			$cd_reuniao = $this->acompanhamento_projetos_model->salvar_reuniao($result, $args);
		
			redirect("atividade/acompanhamento/cadastro_reuniao/".$args["cd_acomp"]."/".$cd_reuniao, "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_reuniao_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]      = $this->input->post("cd_acomp", TRUE);
			$args["cd_reuniao"]    = $this->input->post("cd_reuniao", TRUE);
			$args['arquivo_nome']  = $this->input->post("arquivo_nome", TRUE);
			$args['arquivo']       = $this->input->post("arquivo", TRUE);
			$args["cd_usuario"]    = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->salvar_reuniao_anexo($result, $args);
			
			redirect("atividade/acompanhamento/cadastro_reuniao/".$args["cd_acomp"]."/".$args["cd_reuniao"], "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function listar_reuniao_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_reuniao"] = $this->input->post("cd_reuniao", TRUE);
			
			$this->acompanhamento_projetos_model->listar_reuniao_anexo($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/acompanhamento/reuniao_anexo_result', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function excluir_reuniao_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_reuniao_anexo"] = $this->input->post("cd_reuniao_anexo", TRUE);
			$args["cd_usuario"]       = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->excluir_reuniao_anexo($result, $args);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function imprimir_reuniao($cd_acomp, $cd_reuniao = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]   = $cd_acomp;
			$args["cd_reuniao"] = $cd_reuniao;
			
			$this->load->plugin('fpdf');

			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Registro de Reuniѕes ";
			
			$this->acompanhamento_projetos_model->carrega_projeto($result, $args);
			$row = $result->row_array();	
			
			$projeto = $row['nome'];
			
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Arial', 'B', 13);
			$ob_pdf->MultiCell(190, 7, "Projeto: ". $projeto, '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			if(intval($args["cd_reuniao"]) == 0)
			{
				$this->acompanhamento_projetos_model->roteiro($result, $args);
				$arr_roteiro = $result->result_array();
				
				$ob_pdf->SetFont('Arial', 'B', 12);
				$ob_pdf->MultiCell(190, 7, "Roteiro: ", '0', 'L');
				
				$ob_pdf->SetFont('Arial', '', 10);
				
				foreach($arr_roteiro as $item)
				{
					$ob_pdf->MultiCell(190, 7, $item['nr_ordem'].') '.$item['ds_reunioes_projetos_roteiro'], '0', 'L');
				}
				
				$ob_pdf->setY($ob_pdf->getY() + 5);
			}
			
			if(intval($args["cd_reuniao"]) > 0)
			{
				$this->acompanhamento_projetos_model->cadastro_reuniao($result, $args);
				$arr_reuniao = $result->result_array();	
			}
			else
			{
				$this->acompanhamento_projetos_model->reuniao($result, $args);
			    $arr_reuniao = $result->result_array();
			}
			
			foreach($arr_reuniao as $item)
			{
				$args['cd_reuniao'] = $item['cd_reuniao'];
				
				$this->acompanhamento_projetos_model->presentes_reuniao($result, $args);
				$arr = $result->result_array();
				
				$this->acompanhamento_projetos_model->listar_reuniao_anexo($result, $args);
				$arr_anexo = $result->result_array();
				
				$ob_pdf->SetFont('Arial', 'B', 12);
				$ob_pdf->MultiCell(190, 7, "Data: ". $item['dt_reuniao'], '0', 'L');
				$ob_pdf->SetFont('Arial', '', 10);
				$presentes = '';
				
				foreach($arr as $item2)
				{
					if(trim($presentes) == '')
					{
						$presentes .= $item2['nome'];
					}
					else
					{
						$presentes .= ', '.$item2['nome'];
					}
				}
				
				$ob_pdf->MultiCell(190, 7, "Presentes: ".$presentes, '0', 'L');
				
				$ob_pdf->MultiCell(190, 7, "Assuntos Tratados: ", '0', 'L');
				
				if(trim($item['assunto']) != '')
				{
					$ob_pdf->MultiCell(190, 7, $item['assunto'], '0', 'L');
				}
				
				if(trim($item['ds_arquivo_fisico']) != '' OR count($arr_anexo) > 0)
				{
					$ob_pdf->MultiCell(190, 7, "Anexo: ", '0', 'L');
					
					if(trim($item['ds_arquivo_fisico']) != '' )
					{
						$ob_pdf->MultiCell(190, 7, $item['ds_arquivo_fisico'], '0', 'L');
					}
					
					if(count($arr_anexo) > 0)
					{
						foreach($arr_anexo as $item3)
						{
							$ob_pdf->MultiCell(190, 7, $item3['arquivo_nome'], '0', 'L');
						}
					}
				}

				$ob_pdf->setY($ob_pdf->getY() + 5);
			}
		
			$ob_pdf->Output();
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function email_reuniao($cd_acomp, $cd_reuniao)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]   = $cd_acomp;
			$args["cd_reuniao"] = $cd_reuniao;
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->email_reuniao($result, $args);
			
			redirect("atividade/acompanhamento/reuniao/".$args["cd_acomp"], "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function etapa($cd_acomp)
    {
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = intval($cd_acomp);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->acompanhamento_projetos_model->status_etapa($result, $args);
			$data['arr_status'] = $result->result_array();	
			
			$this->acompanhamento_projetos_model->registro_operacional($result, $args);
			$data['arr_reg_operacional'] = $result->result_array();		

			$this->acompanhamento_projetos_model->escopo($result, $args);
			$data['arr_escopo'] = $result->result_array();		

			$this->acompanhamento_projetos_model->wbs($result, $args);
			$data['arr_wbs'] = $result->result_array();			

			$this->acompanhamento_projetos_model->mudanca_escopo($result, $args);
			$data['arr_mudanca_escopo'] = $result->result_array();				
			
			$this->load->view('atividade/acompanhamento/etapa',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}		
    }
	
	function registro_operacional($cd_acomp, $cd_acompanhamento_registro_operacional = '')
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']                               = intval($cd_acomp);
			$args['cd_acompanhamento_registro_operacional'] = intval($cd_acompanhamento_registro_operacional);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			$data['fl_salvar']    = true;
			$data['fl_finalizar'] = false;
			$data['fl_reiniciar'] = false;
			$data['fl_analista']  = false;

			if($args['cd_acompanhamento_registro_operacional'] == 0)
			{
				$data['row_registro'] = array(
					'cd_acompanhamento_registro_operacional' => $args['cd_acompanhamento_registro_operacional'],
					'cd_acomp'                               => $args['cd_acomp'],
					'ds_nome'                                => '',
					'ds_processo_faz'                        => '',
					'ds_processo_executado'                  => '',
					'ds_calculo'                             => '',
					'ds_responsaveis'                        => '',
					'ds_requesito'                           => '',
					'ds_necessario'                          => '',
					'ds_integridade'                         => '',
					'ds_resultado'                           => '',
					'ds_local'                               => '',
					'dt_finalizado'                          => ''
				);
			}
			else
			{
				$this->acompanhamento_projetos_model->cadastro_registro_operacional($result, $args);
				$data['row_registro'] = $result->row_array();
				
				$row = $data['row_registro'];
				
				$cd_usuario = $this->session->userdata('codigo');
				
				$data['fl_salvar']    = false;
				$data['fl_finalizar'] = false;
				$data['fl_reiniciar'] = false;
				
				if(intval($cd_usuario) == $row['cd_usuario'] and trim($row['dt_finalizado']) == '')
				{
					$data['fl_salvar']    = true;
					$data['fl_finalizar'] = true;
				}
				else
				{
					$args['cd_usuario'] = $cd_usuario;
					
					$this->acompanhamento_projetos_model->permissao_analista($result, $args);
					$row_analista = $result->row_array();
					
					if(intval($row_analista['fl_analista']) > 0 and trim($row['dt_finalizado']) != '')
					{
						$data['fl_salvar']    = true;
						$data['fl_reiniciar'] = true;
						$data['fl_analista']  = true;
					}
				}
			}
			
			$this->load->view('atividade/acompanhamento/registro_operacional',$data);			
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function salvar_registro_operacional()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acompanhamento_registro_operacional'] = $this->input->post("cd_acompanhamento_registro_operacional", TRUE); 
			$args['cd_acomp']                               = $this->input->post("cd_acomp", TRUE); 
			$args['ds_nome']                                = $this->input->post("ds_nome", TRUE); 
			$args['ds_processo_faz']                        = $this->input->post("ds_processo_faz", TRUE); 
			$args['ds_processo_executado']                  = $this->input->post("ds_processo_executado", TRUE); 
			$args['ds_calculo']                             = $this->input->post("ds_calculo", TRUE); 
			$args['ds_responsaveis']                        = $this->input->post("ds_responsaveis", TRUE); 
			$args['ds_requesito']                           = $this->input->post("ds_requesito", TRUE); 
			$args['ds_necessario']                          = $this->input->post("ds_necessario", TRUE); 
			$args['ds_integridade']                         = $this->input->post("ds_integridade", TRUE); 
			$args['ds_resultado']                           = $this->input->post("ds_resultado", TRUE); 
			$args['ds_local']                               = $this->input->post("ds_local", TRUE); 
			$args['ds_processo_faz_complemento']            = $this->input->post("ds_processo_faz_complemento", TRUE); 
			$args['ds_processo_executado_complemento']      = $this->input->post("ds_processo_executado_complemento", TRUE); 
			$args['ds_calculo_complemento']                 = $this->input->post("ds_calculo_complemento", TRUE); 
			$args['ds_requesito_complemento']               = $this->input->post("ds_requesito_complemento", TRUE); 
			$args['ds_necessario_complemento']              = $this->input->post("ds_necessario_complemento", TRUE); 
			$args['ds_integridade_complemento']             = $this->input->post("ds_integridade_complemento", TRUE); 
			$args['ds_resultado_complemento']               = $this->input->post("ds_resultado_complemento", TRUE); 
			$args['cd_usuario']                             = $this->session->userdata('codigo');
			
			$cd_acompanhamento_registro_operacional = $this->acompanhamento_projetos_model->salvar_registro_operacional($result, $args);
		
			redirect("atividade/acompanhamento/registro_operacional/".$args["cd_acomp"]."/".$cd_acompanhamento_registro_operacional, "refresh");	
			
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function finalizar_registro_operacional($cd_acomp, $cd_acompanhamento_registro_operacional)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $cd_acompanhamento_registro_operacional; 
		$args['cd_usuario']                             = $this->session->userdata('codigo');
		
		$this->acompanhamento_projetos_model->finalizar_registro_operacional( $result, $args );
		
		redirect("atividade/acompanhamento/registro_operacional/".intval($cd_acomp)."/".$args['cd_acompanhamento_registro_operacional'], "refresh");
	}
	
	function reiniciar_registro_operacional($cd_acomp, $cd_acompanhamento_registro_operacional)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_acompanhamento_registro_operacional'] = $cd_acompanhamento_registro_operacional; 
		$args['cd_usuario']                             = $this->session->userdata('codigo');
		
		$this->acompanhamento_projetos_model->reiniciar_registro_operacional( $result, $args );
		
		redirect("atividade/acompanhamento/registro_operacional/".intval($cd_acomp)."/".$args['cd_acompanhamento_registro_operacional'], "refresh");
	}
	
	function salvar_registro_operacional_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]                                  = $this->input->post("cd_acomp", TRUE);
			$args["cd_acompanhamento_registro_operacional"]    = $this->input->post("cd_acompanhamento_registro_operacional", TRUE);
			$args['arquivo_nome']                              = $this->input->post("arquivo_nome", TRUE);
			$args['arquivo']                                   = $this->input->post("arquivo", TRUE);
			$args["cd_usuario"]                                = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->salvar_registro_operacional_anexo($result, $args);
			
			redirect("atividade/acompanhamento/registro_operacional/".$args["cd_acomp"]."/".$args["cd_acompanhamento_registro_operacional"], "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function listar_registro_operacional_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acompanhamento_registro_operacional"] = $this->input->post("cd_acompanhamento_registro_operacional", TRUE);
			
			$this->acompanhamento_projetos_model->listar_registro_operacional_anexo($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/acompanhamento/registro_operacional_anexo_result', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function excluir_registro_operacional_anexo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acompanhamento_registro_operacional_anexo"] = $this->input->post("cd_acompanhamento_registro_operacional_anexo", TRUE);
			$args["cd_usuario"]                                   = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->excluir_registro_operacional_anexo($result, $args);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function escopo($cd_acomp, $cd_acompanhamento_escopos = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']                  = intval($cd_acomp);
			$args['cd_acompanhamento_escopos'] = intval($cd_acompanhamento_escopos);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			if(intval($cd_acompanhamento_escopos) == 0)
			{
				$data['row_escopo'] = array(
					'cd_acompanhamento_escopos' => $args['cd_acompanhamento_escopos'],
					'ds_objetivos'              => '',
					'ds_regras'                 => '',
					'ds_impacto'                => '',
					'ds_responsaveis'           => '',
					'ds_solucao'                => '',
					'ds_recurso'                => '',
					'ds_viabilidade'            => '',
					'ds_modelagem'              => '',
					'ds_produtos'               => ''
				);
			}
			else
			{
				$this->acompanhamento_projetos_model->cadastro_escopo($result, $args);
				$data['row_escopo'] = $result->row_array();
			}
			
			$this->load->view('atividade/acompanhamento/escopo', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_escopo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']                  = $this->input->post("cd_acomp", TRUE); 
			$args['cd_acompanhamento_escopos'] = $this->input->post("cd_acompanhamento_escopos", TRUE); 
			$args['ds_objetivos']              = $this->input->post("ds_objetivos", TRUE); 
			$args['ds_regras']                 = $this->input->post("ds_regras", TRUE); 
			$args['ds_impacto']                = $this->input->post("ds_impacto", TRUE); 
			$args['ds_responsaveis']           = $this->input->post("ds_responsaveis", TRUE); 
			$args['ds_solucao']                = $this->input->post("ds_solucao", TRUE); 
			$args['ds_recurso']                = $this->input->post("ds_recurso", TRUE); 
			$args['ds_viabilidade']            = $this->input->post("ds_viabilidade", TRUE); 
			$args['ds_modelagem']              = $this->input->post("ds_modelagem", TRUE); 
			$args['ds_produtos']               = $this->input->post("ds_produtos", TRUE); 
			$args['cd_usuario']                = $this->session->userdata('codigo');
			
			$cd_acompanhamento_escopos = $this->acompanhamento_projetos_model->salvar_escopo($result, $args);
		
			redirect("atividade/acompanhamento/escopo/".$args["cd_acomp"]."/".$cd_acompanhamento_escopos, "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function imprimir_escopo($cd_acompanhamento_escopos)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acompanhamento_escopos"] = $cd_acompanhamento_escopos;
			
			$this->load->plugin('fpdf');

			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Registro de Reuniѕes";
			
			$this->acompanhamento_projetos_model->cadastro_escopo($result, $args);
			$row = $result->row_array();	
			
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Arial', 'B', 13);
			$ob_pdf->MultiCell(190, 7, "Projeto: ". $row['projeto'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 4);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "1) Objetivo: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_objetivos'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "2) Regras de Negѓcio/Funcionalidas: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_regras'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "3) Impacto: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_impacto'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "4) Responsсveis: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_responsaveis'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "5) Soluчуo Imediata (opcional): ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_solucao'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "6) Recurso/Custo: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_recurso'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "7) Viabilidade/Sugestуo (opcional): ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_viabilidade'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "8) Modelagem de Dados: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_modelagem'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "9) Produtos: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_produtos'], '0', 'L');
			
			$ob_pdf->Output();
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function wbs($cd_acomp)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = $cd_acomp;
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('atividade/acompanhamento/wbs', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function listar_wbs()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = $this->input->post("cd_acomp", TRUE); 
			
			$this->acompanhamento_projetos_model->wbs($result, $args);
			$data['collection'] = $result->result_array();		
			
			$this->load->view('atividade/acompanhamento/wbs_result', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_wbs()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]          = $this->input->post("cd_acomp", TRUE);
			$args['ds_arquivo']        = $this->input->post("arquivo_nome", TRUE);
			$args['ds_arquivo_fisico'] = $this->input->post("arquivo", TRUE);
			$args["cd_usuario"]        = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->salvar_wbs($result, $args);
			
			redirect("atividade/acompanhamento/wbs/".$args["cd_acomp"], "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function excluir_wbs()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acompanhamento_wbs"] = $this->input->post("cd_acompanhamento_wbs", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->excluir_wbs($result, $args);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}

	function mudanca_escopo($cd_acomp, $cd_acompanhamento_mudanca_escopo = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acomp"]                         = $cd_acomp;
			$args["cd_acompanhamento_mudanca_escopo"] = $cd_acompanhamento_mudanca_escopo;
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			$this->acompanhamento_projetos_model->solicitante($result, $args);
			$data['arr_solicitante'] = $result->result_array();
			
			if(intval($args["cd_acompanhamento_mudanca_escopo"]) == 0)
			{
				$data['row_mudanca'] = array(
					'cd_acompanhamento_mudanca_escopo' => $cd_acompanhamento_mudanca_escopo,
					'nr_numero'                        => '',
					'cd_solicitante'                   => '',
					'cd_analista'                      => '',
					'cd_etapa'                         => '',
					'dt_mudanca'                       => '',
					'dt_aprovacao'                     => '',
					'nr_dias'                          => '',
					'ds_descricao'                     => '',
					'ds_regras'                        => '',
					'ds_impacto'                       => '',
					'ds_responsaveis'                  => '',
					'ds_solucao'                       => '',
					'ds_recurso'                       => '',
					'ds_viabilidade'                   => '',
					'ds_modelagem'                     => '',
					'ds_produtos'                      => ''
				);
			}
			else
			{
				$this->acompanhamento_projetos_model->cadastro_mudanca_escopo($result, $args);
				$data['row_mudanca'] = $result->row_array();
			}
			
			$this->load->view('atividade/acompanhamento/mudanca_escopo', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_mudanca_escopo()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']                         = $this->input->post("cd_acomp", TRUE); 
			$args['cd_acompanhamento_mudanca_escopo'] = $this->input->post("cd_acompanhamento_mudanca_escopo", TRUE); 
			$args['nr_numero']                        = $this->input->post("nr_numero", TRUE); 
			$args['cd_solicitante']                   = $this->input->post("cd_solicitante", TRUE); 
			$args['cd_analista']                      = $this->input->post("cd_analista", TRUE); 
			$args['cd_etapa']                         = $this->input->post("cd_etapa", TRUE); 
			$args['dt_mudanca']                       = $this->input->post("dt_mudanca", TRUE); 
			$args['dt_aprovacao']                     = $this->input->post("dt_aprovacao", TRUE); 
			$args['nr_dias']                          = $this->input->post("nr_dias", TRUE); 
			$args['ds_descricao']                     = $this->input->post("ds_descricao", TRUE); 
			$args['ds_regras']                        = $this->input->post("ds_regras", TRUE); 
			$args['ds_impacto']                       = $this->input->post("ds_impacto", TRUE); 
			$args['ds_responsaveis']                  = $this->input->post("ds_responsaveis", TRUE); 
			$args['ds_solucao']                       = $this->input->post("ds_solucao", TRUE); 
			$args['ds_recurso']                       = $this->input->post("ds_recurso", TRUE); 
			$args['ds_viabilidade']                   = $this->input->post("ds_viabilidade", TRUE); 
			$args['ds_modelagem']                     = $this->input->post("ds_modelagem", TRUE); 
			$args['ds_produtos']                      = $this->input->post("ds_produtos", TRUE); 
			$args['cd_usuario']                       = $this->session->userdata('codigo');
			
			$cd_acompanhamento_mudanca_escopo = $this->acompanhamento_projetos_model->salvar_mudanca_escopo($result, $args);
		
			redirect("atividade/acompanhamento/mudanca_escopo/".$args["cd_acomp"]."/".$cd_acompanhamento_mudanca_escopo, "refresh");	
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function imprimir_mudanca_escopo($cd_acompanhamento_mudanca_escopo)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_acompanhamento_mudanca_escopo"] = $cd_acompanhamento_mudanca_escopo;
			
			$this->load->plugin('fpdf');

			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Requisiчуo de Mudanчa de Escopo";
			
			$this->acompanhamento_projetos_model->cadastro_mudanca_escopo($result, $args);
			$row = $result->row_array();	
			
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Arial', 'B', 13);
			$ob_pdf->MultiCell(190, 7, "Projeto: ". $row['projeto'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 4);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "1) Descriчуo da Mudanчa de Escopo: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_descricao'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "2) Regras de Negѓcio/Funcionalidas: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_regras'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "3) Impacto: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_impacto'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "4) Responsсveis: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_responsaveis'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "5) Soluчуo Imediata (opcional): ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_solucao'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "6) Recurso/Custo: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_recurso'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "7) Viabilidade/Sugestуo (opcional): ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_viabilidade'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "8) Modelagem de Dados: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_modelagem'], '0', 'L');
			
			$ob_pdf->setY($ob_pdf->getY() + 3);
			
			$ob_pdf->SetFont('Arial', 'B', 12);
			$ob_pdf->MultiCell(190, 7, "9) Produtos: ", '0', 'L');
			$ob_pdf->SetFont('Arial', '', 12);
			$ob_pdf->MultiCell(190, 7, $row['ds_produtos'], '0', 'L');
			
			$ob_pdf->Output();
		}
		else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
	}
	
	function salvar_etapa()
	{	
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_acomp"]   = $this->input->post("cd_acomp", TRUE);
			$args["status_ar"]  = $this->input->post("status_ar", TRUE);
			$args["status_es"]  = $this->input->post("status_es", TRUE);
			$args["status_au"]  = $this->input->post("status_au", TRUE);
			$args["status_de"]  = $this->input->post("status_de", TRUE);
			$args["status_me"]  = $this->input->post("status_me", TRUE);
			$args["desc_ar"]    = $this->input->post("desc_ar", TRUE);
			$args["desc_es"]    = $this->input->post("desc_es", TRUE);
			$args["desc_au"]    = $this->input->post("desc_au", TRUE);
			$args["desc_de"]    = $this->input->post("desc_de", TRUE);
			$args["desc_me"]    = $this->input->post("desc_me", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->acompanhamento_projetos_model->salvar_etapa( $result, $args );
			
			redirect("atividade/acompanhamento/etapa/".$args["cd_acomp"], "refresh");			

		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}	
	}
	
    function previsao($cd_acomp)
    {	
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp'] = intval($cd_acomp);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->acompanhamento_projetos_model->previsao($result, $args);
			$data['arr_previsao'] = $result->result_array();
			
			$this->load->view('atividade/acompanhamento/previsao',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}		
    }
	
	function cadastro_previsao($cd_acomp, $cd_previsao = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']    = intval($cd_acomp);
			$args['cd_previsao'] = intval($cd_previsao);
			
			$this->acompanhamento_projetos_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
			
			if(intval($args["cd_previsao"]) == 0)
			{
				$data['row_previsao'] = array(
					'cd_previsao' => $cd_previsao,
					'mes_ano'     => date('d/m/Y'),
					'descricao'   => '',
					'obs'         => ''
				);
			}
			else
			{
				$this->acompanhamento_projetos_model->cadastro_previsao($result, $args);
				$data['row_previsao'] = $result->row_array();
			}
			
			$this->load->view('atividade/acompanhamento/cadastro_previsao',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function salvar_previsao()
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_previsao"] = $this->input->post("cd_previsao", TRUE);
			$args["cd_acomp"]    = $this->input->post("cd_acomp", TRUE);
			$args["mes"]         = $this->input->post("mes", TRUE);
			$args["ano"]         = $this->input->post("ano", TRUE);
			$args["descricao"]   = $this->input->post("descricao", TRUE);
			$args["obs"]         = $this->input->post("obs", TRUE);
			$args["cd_usuario"]  = $this->session->userdata('codigo');
			
			$mes = mes_format($args['mes'], 'mmmm'); 
			
			$args['mes_extenso'] = strtoupper($mes);
			
			$cd_previsao = $this->acompanhamento_projetos_model->salvar_previsao( $result, $args );
			
			redirect("atividade/acompanhamento/cadastro_previsao/".$args["cd_acomp"]."/".$cd_previsao, "refresh");	
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}	
	
	function imprimir_previsao($cd_acomp, $cd_previsao = 0)
	{
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_acomp']    = intval($cd_acomp);
			$args['cd_previsao'] = intval($cd_previsao);
			
			$this->load->plugin('fpdf');

			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Previsуo";
			
			$this->acompanhamento_projetos_model->carrega_projeto($result, $args);
			$row = $result->row_array();	
			
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Arial', 'B', 13);
			$ob_pdf->MultiCell(190, 7, "Projeto: ". $row['nome'], '0', 'L');
			
			$this->acompanhamento_projetos_model->previsao_pdf($result, $args);
			$arr = $result->result_array();
			
			foreach($arr as $item)
			{
				$ob_pdf->setY($ob_pdf->getY() + 5);
				
				$ob_pdf->SetFont('Arial', 'B', 12);
				$ob_pdf->MultiCell(190, 7, "Mъs/Ano:", '0', 'L');
				$ob_pdf->SetFont('Arial', '', 12);
				$ob_pdf->MultiCell(190, 7, $item['mes'].'/'.$item['ano'], '0', 'L');
				
				$ob_pdf->setY($ob_pdf->getY() + 3);
				
				$ob_pdf->SetFont('Arial', 'B', 12);
				$ob_pdf->MultiCell(190, 7, "Previsуo:", '0', 'L');
				$ob_pdf->SetFont('Arial', '', 12);
				$ob_pdf->MultiCell(190, 7, $item['descricao'], '0', 'L');
				
				$ob_pdf->setY($ob_pdf->getY() + 3);
				
				$ob_pdf->SetFont('Arial', 'B', 12);
				$ob_pdf->MultiCell(190, 7, "Observaчуo: ", '0', 'L');
				$ob_pdf->SetFont('Arial', '', 12);
				$ob_pdf->MultiCell(190, 7, $item['obs'], '0', 'L');

			}
			
			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NУO PERMITIDO");
		}
	}
	
	function previsao_valida_mes()
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$args["mes"]         = $this->input->post("mes", TRUE);
		$args["ano"]         = $this->input->post("ano", TRUE);
		$args["cd_acomp"]    = $this->input->post("cd_acomp", TRUE);
		$args["cd_previsao"] = $this->input->post("cd_previsao", TRUE);
		
		$this->acompanhamento_projetos_model->previsao_valida_mes($result, $args);
		$row = $result->row_array();
		
		echo intval($row['tl']);
	}
}
?>