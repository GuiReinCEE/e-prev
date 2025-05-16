<?php

class reuniao_sg extends Controller
{
    var $fl_acesso = false;
    var $fl_lista = false;

    function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$result = null;
		$data = Array();
		$args = Array();

        $this->load->model('projetos/reuniao_sg_model');

        if($this->session->userdata('codigo') == 26) #Adriana Nunes (GC)
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('codigo') == 415) #Lucio Daniel Sartori (GC)
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('codigo') == 411) #Regis Rodrigues da Silveira (GC)
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('codigo') == 170) #Cristiano Jacobsen(GI)
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('codigo') == 516) #GUILHERME REINHEIMER(GS-TI)
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('codigo') == 474) #Roberta Bittencourt da Costa (GC)
        {
            $this->fl_acesso = true;
        }
		
		$args['cd_usuario'] = $this->session->userdata('codigo');
		
		if(!$this->fl_acesso)
		{
			$this->reuniao_sg_model->verifica_reuniao_controle($result, $args);
			$ar = $result->row_array();
			
			if ($ar['fl_lista'] == "S")
			{
				$this->fl_acesso = true;
			}
		}
		
        $this->reuniao_sg_model->verifica_permissao_lista($result, $args);
        $ar_reg = $result->row_array();

        if ($ar_reg['fl_lista'] == "S")
        {
            $this->fl_lista = true;
        }
    }

    function index()
    {
        if (($this->fl_acesso) or ($this->fl_lista))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $this->reuniao_sg_model->get_usuarios_solicitante($result, $args);
            $data['solicitante'] = $result->result_array();
            
            $this->load->view('atividade/reuniao_sg/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
        if (($this->fl_acesso) or ($this->fl_lista))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
            $args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);
            $args["dt_reuniao_ini"]  = $this->input->post("dt_reuniao_ini", TRUE);
            $args["dt_reuniao_fim"]  = $this->input->post("dt_reuniao_fim", TRUE);
            $args["fl_parecer"]      = $this->input->post("fl_parecer", TRUE);
            $args["fl_encerrado"]    = $this->input->post("fl_encerrado", TRUE);
            $args["cd_usuario"]      = $this->input->post("cd_usuario", TRUE);

            manter_filtros($args);

            $this->reuniao_sg_model->listar($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->load->view('atividade/reuniao_sg/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function relatorio()
    {
        if ($this->fl_acesso) 
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $this->reuniao_sg_model->get_instituicoes($result, $args);
            $data['instituicoes'] = $result->result_array();

            $this->load->view('atividade/reuniao_sg/relatorio', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar_reuniao()
    {
        if ($this->fl_acesso) 
        {
            $result = null;
            $data = Array();
            $args = Array();
  
            $args["dt_reuniao_ini"]            = $this->input->post("dt_reuniao_ini", TRUE);
            $args["dt_reuniao_fim"]            = $this->input->post("dt_reuniao_fim", TRUE);
			$args["dt_ini_ini"]                = $this->input->post("dt_ini_ini", TRUE);
            $args["dt_ini_fim"]                = $this->input->post("dt_ini_fim", TRUE);
            $args["cd_reuniao_sg_instituicao"] = $this->input->post("cd_reuniao_sg_instituicao", TRUE);

            manter_filtros($args);

            $this->reuniao_sg_model->listar_relatorio($result, $args);
            $data['collection'] = $result->result_array();
			
            $this->load->view('atividade/reuniao_sg/relatorio_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function detalhe($cd_reuniao_sg = 0)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args['cd_usuario']    = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        $this->reuniao_sg_model->get_usuarios_gin($result, $args);
        $data['ar_res'] = $result->result_array();
		
		$this->reuniao_sg_model->get_usuarios_de($result, $args);
		$data['arr_diretoria'] = $result->result_array();
		
		$this->reuniao_sg_model->usuario_participante($result, $args);
		$data['arr_participante'] = $result->result_array();
		
		$data['arr_participante_checked'] = array();
        
        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            if (intval($cd_reuniao_sg) == 0)
            {
                $data['row'] = Array(
                  'cd_reuniao_sg'             => $args['cd_reuniao_sg'],
                  'cd_reuniao_sg_instituicao' => '',
                  'participantes'             => '',
                  'pauta'                     => '',
                  'contato'                   => '',
                  'dt_inclusao'               => '',
                  'dt_sugerida'               => '',
                  'hr_sugerida'               => '',
                  'dt_exclusao'               => '',
                  'dt_cancela'                => '',
                  'usuario_cadastro'          => '',
                  'dt_reuniao'                => '',
                  'hr_reuniao'                => '',
                  'dt_encerrado'              => '',
                  'cd_usuario_validacao'      => '',
                  'arquivo'                   => '',
                  'arquivo_nome'              => '',
                  'dt_encerrado'              => '',
				  'cd_usuario_inclusao'      => ''
                );
            }
            else
            {
                $this->reuniao_sg_model->cadastro($result, $args);
                $data['row'] = $result->row_array();
                
                $this->reuniao_sg_model->dt_encerrado($result, $args);
                $data['fl_encerrado'] = $result->row_array();
                
                $this->reuniao_sg_model->participantes($result, $args);
                $arr_participante = $result->result_array();
			
				foreach($arr_participante as $item)
				{
					$data['arr_participante_checked'][] = $item['codigo'];
				}

            }
            $this->load->view('atividade/reuniao_sg/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar()
    {
		$result = null;
		$data = Array();
		$args = Array();
	
        $args['cd_reuniao_sg'] = $this->input->post("cd_reuniao_sg", TRUE);
		
        $this->reuniao_sg_model->cadastro($result, $args);
        $row = $result->row_array();
        
        if (($this->fl_acesso) OR ($this->session->userdata('codigo') ==  $row['cd_usuario_validacao']))
        {
            $args["cd_reuniao_sg"]             = $this->input->post("cd_reuniao_sg", TRUE);
            $args["cd_reuniao_sg_instituicao"] = $this->input->post("cd_reuniao_sg_instituicao", TRUE);
            $args["participantes"]             = $this->input->post("participantes", TRUE);
            $args["pauta"]                     = $this->input->post("pauta", TRUE);
            $args["contato"]                   = $this->input->post("contato", TRUE);
            $args["dt_sugerida"]               = $this->input->post("dt_sugerida", TRUE);
            $args["hr_sugerida"] 			   = $this->input->post("hr_sugerida", TRUE);
            $args["dt_reuniao"] 			   = $this->input->post("dt_reuniao", TRUE);
            $args["hr_reuniao"] 			   = $this->input->post("hr_reuniao", TRUE);
            $args["fl_confirma"] 			   = $this->input->post("fl_confirma", TRUE);
            $args['cd_usuario_validacao'] 	   = $this->input->post("cd_usuario_validacao", TRUE);
            $args['arquivo_nome'] 			   = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo']                   = $this->input->post("arquivo", TRUE);
            $args["cd_usuario"]                = $this->input->post("cd_usuario_inclusao", TRUE);
            $args["arr_participante"]          = (is_array($this->input->post("arr_participante", TRUE)) ? $this->input->post("arr_participante", TRUE) : array());
			$args["cd_usuario_atualizacao"]    = $this->session->userdata('codigo');

            #echo "<PRE>".print_r($args,true)."</PRE>";
            $args["cd_reuniao_sg"] = $this->reuniao_sg_model->salvar($result, $args);

            if(count($args["arr_participante"]) > 0)
            {
                $this->reuniao_sg_model->salvar_usuario($result, $args);
            }
            
            redirect("atividade/reuniao_sg/detalhe/".$args["cd_reuniao_sg"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function excluir($cd_reuniao_sg = 0)
    {
        if ($this->fl_acesso)
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_reuniao_sg"] = intval($cd_reuniao_sg);
            $args["cd_usuario"]    = $this->session->userdata('codigo');

            $this->reuniao_sg_model->excluir($result, $args);
            redirect("atividade/reuniao_sg/detalhe/" . $cd_reuniao_sg, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function naoConfirmar($cd_reuniao_sg = 0)
    {
        if ($this->fl_acesso)
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_reuniao_sg"] = intval($cd_reuniao_sg);
            $args["cd_usuario"]    = $this->session->userdata('codigo');

            $this->reuniao_sg_model->naoConfirmar($result, $args);
            redirect("atividade/reuniao_sg/detalhe/" . $cd_reuniao_sg, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function parecer($cd_reuniao_sg)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args['cd_usuario']    = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $data['cd_reuniao_sg'] = $cd_reuniao_sg;
            $args['cd_reuniao_sg'] = $cd_reuniao_sg;

            $this->reuniao_sg_model->get_assuntos($result, $args);
            $data['assuntos'] = $result->result_array();

            $this->reuniao_sg_model->get_usuarios($result, $args);
            $data['usuarios'] = $result->result_array();
            
            $this->reuniao_sg_model->get_parecer($result, $args);
            $data['row'] = $result->row_array();
			
			$this->reuniao_sg_model->usuario($result, $args);
			$data['arr_participante'] = $result->result_array();
			
			$data['arr_participante_checked'] = array();

            foreach($data['usuarios'] as $item)
            {
                $data['arr_participante_checked'][] = $item['codigo'];
            }

            $this->reuniao_sg_model->usuario_participante_parecer($result, $args);
            $data['arr_participante_parecer'] = $result->result_array();

            $this->reuniao_sg_model->get_usuarios_parecer($result, $args);
            $usuarios = $result->result_array();
            
            $data['arr_participante_parecer_checked'] = array();

            foreach($usuarios as $item)
            {
                $data['arr_participante_parecer_checked'][] = $item['codigo'];
            }

            $this->load->view('atividade/reuniao_sg/parecer', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function salvar_parecer()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_reuniao_sg"] = $this->input->post("cd_reuniao_sg", TRUE);
        $args["cd_usuario"] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = array();

            $args["cd_reuniao_sg"]   = $this->input->post("cd_reuniao_sg", TRUE);
            $args["parecer"]         = $this->input->post("parecer", TRUE);
            $args["relato"]          = $this->input->post("relato", TRUE);
            $args["dt_reuniao_ini"]  = $this->input->post("dt_reuniao_ini", TRUE);
            $args["hr_reuniao_ini"]  = $this->input->post("hr_reuniao_ini", TRUE);
            $args["dt_reuniao_fim"]  = $this->input->post("dt_reuniao_fim", TRUE);
            $args["hr_reuniao_fim"]  = $this->input->post("hr_reuniao_fim", TRUE);
            $args["fl_qualificacao"] = $this->input->post("fl_qualificacao", TRUE);
            $args["cd_usuario"]      = $this->session->userdata('codigo');
			
            $this->reuniao_sg_model->salvar_parecer($result, $args);

            $args["arr_participante"] = (is_array($this->input->post("arr_participante", TRUE)) ? $this->input->post("arr_participante", TRUE) : array());

            $this->reuniao_sg_model->salvar_usuario($result, $args);

            $args["arr_participante_parecer"] = (is_array($this->input->post("arr_participante_parecer", TRUE)) ? $this->input->post("arr_participante_parecer", TRUE) : array());

            $this->reuniao_sg_model->salvar_participante_parecer($result, $args);

            redirect("atividade/reuniao_sg/parecer/".$args["cd_reuniao_sg"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function assunto($cd_reuniao_sg, $cd_reuniao_sg_assunto_parecer = 0)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args['cd_reuniao_sg_assunto_parecer'] = intval($cd_reuniao_sg_assunto_parecer);
        $args["cd_usuario"]    = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();

            $data['cd_reuniao_sg'] = intval($cd_reuniao_sg);
            $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);

            $this->reuniao_sg_model->get_sg_assunto($result, $args);
            $data['arr_assunto'] = $result->result_array();

            $this->reuniao_sg_model->get_assuntos($result, $args);
            $data['assuntos'] = $result->result_array();
			
			if(intval($args['cd_reuniao_sg_assunto_parecer']) == 0)
			{
				$data['row'] = array(
					'cd_reuniao_sg_assunto_parecer' => $args['cd_reuniao_sg_assunto_parecer'],
					'cd_reuniao_sg_assunto'         => '',
					'complemento'                   => ''
				);
			}
			else
			{
				$this->reuniao_sg_model->carrega_assunto($result, $args);
				$data['row'] = $result->row_array();
			}

            $this->load->view('atividade/reuniao_sg/assunto', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar_assunto()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_reuniao_sg"] = $this->input->post("cd_reuniao_sg", TRUE);
        $args["cd_usuario"] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();

            $args["cd_reuniao_sg"]                 = $this->input->post("cd_reuniao_sg", TRUE);
            $args["cd_reuniao_sg_assunto_parecer"] = $this->input->post("cd_reuniao_sg_assunto_parecer", TRUE);
            $args["cd_reuniao_sg_assunto"]         = $this->input->post("cd_reuniao_sg_assunto", TRUE);
            $args["complemento"]                   = $this->input->post("complemento", TRUE);
            $args["cd_usuario"]                    = $this->session->userdata('codigo');

            $this->reuniao_sg_model->salvar_assunto($result, $args);
			
            redirect("atividade/reuniao_sg/assunto/".$args["cd_reuniao_sg"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function excluir_assunto($cd_reuniao_sg, $cd_reuniao_sg_assunto_parecer)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args["cd_usuario"] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();

            $args["cd_reuniao_sg_assunto_parecer"] = intval($cd_reuniao_sg_assunto_parecer);
            $args["cd_usuario"] = $this->session->userdata('codigo');

            $this->reuniao_sg_model->excluir_assunto($result, $args);
            redirect("atividade/reuniao_sg/parecer/" . $cd_reuniao_sg, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function encerrar($cd_reuniao_sg)
    {        
        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_reuniao_sg"]   = intval($cd_reuniao_sg);
        $args["fl_qualificacao"] = $this->input->post("fl_qualificacao", TRUE);
        $args["relato"]          = $this->input->post("relato", TRUE);
        $args["parecer"]         = $this->input->post("parecer", TRUE);
        $args["cd_usuario"]      = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();
            
            $args["cd_usuario"] = $this->session->userdata('codigo');

            $this->reuniao_sg_model->encerrar($result, $args);
            redirect("atividade/reuniao_sg/parecer/" . $cd_reuniao_sg, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function confirma($cd_reuniao_sg_validacao)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg_validacao'] = intval($cd_reuniao_sg_validacao);
        $data['cd_reuniao_sg_validacao'] = intval($cd_reuniao_sg_validacao);
        $args["cd_usuario"] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_confirma($result, $args);
        $ar_reg = $result->row_array();

        if (count($ar_reg) > 0)
        {
            $data['cd_reuniao_sg'] = $ar_reg['cd_reuniao_sg'];
            $data['fl_validacao']  = $ar_reg['fl_validacao'];
            $this->load->view('atividade/reuniao_sg/confirma', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salva_confirmacao($cd_reuniao_sg_validacao, $fl_validacao)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg_validacao'] = intval($cd_reuniao_sg_validacao);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
        $args['fl_validacao']            = $fl_validacao;

        $this->reuniao_sg_model->salva_confirmacao($result, $args);
		
		$this->reuniao_sg_model->nao_validado($result, $args);
        $row = $result->row_array();
		
		if(intval($row['tl_nao_validado']) == 0)
		{
			$args['cd_reuniao_sg'] = $row['cd_reuniao_sg'];
			
			$this->reuniao_sg_model->auto_encerrar($result, $args);
		}
		
        redirect("atividade/reuniao_sg", "refresh");
    }

    function enviar($cd_reuniao_sg)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_reuniao_sg"] = intval($cd_reuniao_sg);
        $args["cd_usuario"]    = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_reuniao_sg"] = intval($cd_reuniao_sg);

            $this->reuniao_sg_model->enviar($result, $args);
            redirect("atividade/reuniao_sg/parecer/" . intval($cd_reuniao_sg), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
   
	function imprimir_detalhe($cd_reuniao_sg)
	{
		$result = null;
        $data = Array();
        $args = Array();
		
		$args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
		$args['cd_usuario'] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
			$this->reuniao_sg_model->cadastro($result, $args);
			$row = $result->row_array();
			
			$this->reuniao_sg_model->dt_encerrado($result, $args);
			$data['fl_encerrado'] = $result->row_array();
			
			$this->load->plugin('fpdf');
			
			$ob_pdf = new PDF();

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10, 14, 5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "";

            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0, 0, 0);

            $ob_pdf->AddPage();
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE", 0, "C");
            $ob_pdf->MultiCell(190, 4.5, "GERÊNCIA DE INVESTIMENTOS", 0, "C");
			
			$ob_pdf->SetY($ob_pdf->GetY() + 15);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Nº da Reunião :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+40,$ob_pdf->GetY(), $cd_reuniao_sg);
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Dt. Solicitação :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+45,$ob_pdf->GetY(), $row['dt_inclusao']);

			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Solicitante :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+35,$ob_pdf->GetY(), $row['usuario_cadastro']);
	
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Dt. Reunião :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+35,$ob_pdf->GetY(), $row['dt_reuniao']);
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Hr. Reunião :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+35,$ob_pdf->GetY(), $row['hr_reuniao']);
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Sugestão de data :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+48,$ob_pdf->GetY(), $row['dt_sugerida']);
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Sugestão de horário :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+55,$ob_pdf->GetY(), $row['hr_sugerida']);
			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Responsável :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+35,$ob_pdf->GetY(), $row['usuario_validacao']);

			
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
            
            $this->reuniao_sg_model->participantes($result, $args);
            $participantes = $result->result_array();

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "Participantes Fundação");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            
            $ob_pdf->SetWidths(array(89, 103));
            $ob_pdf->SetAligns(array('C', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->Row(array("Participante", "Gerência"));
            $ob_pdf->SetAligns(array('L', 'L'));
            
            foreach ($participantes as $item)
            {
                $ob_pdf->Row(array($item['nome'], $item['gerencia']));
            }
			
			$ob_pdf->SetY($ob_pdf->GetY() + 8);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->Text($ob_pdf->GetX(),$ob_pdf->GetY(), 'Nome da Instituição :');
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->Text($ob_pdf->GetX()+57,$ob_pdf->GetY(), $row['ds_reuniao_sg_instituicao']);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->MultiCell(190, 4.5, "Participantes com cargo: ");
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->MultiCell(190, 4.5, $row['participantes']);
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->MultiCell(190, 4.5, "Contato: ");
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->MultiCell(190, 4.5, $row['contato']);
			$ob_pdf->SetFont('Courier', 'B', 12);
			$ob_pdf->MultiCell(190, 4.5, "Pauta (Cfe IT 7.4.01.103): ");
			$ob_pdf->SetFont('Courier', '', 12);
			$ob_pdf->MultiCell(190, 4.5, $row['pauta']);
			
			$ob_pdf->Output();
            exit;
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function imprimir($cd_reuniao_sg)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args['cd_usuario'] = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $this->load->plugin('fpdf');

            $this->reuniao_sg_model->pdf($result, $args);
            $row = $result->row_array();

            $this->reuniao_sg_model->usuario_participante($result, $args);
            $participante_interno = $result->result_array();

            $ob_pdf = new PDF();

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10, 14, 5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "";

            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0, 0, 0);

            $ob_pdf->AddPage();
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE", 0, "C");
            $ob_pdf->MultiCell(190, 4.5, "GERÊNCIA DE INVESTIMENTOS", 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 10);
            $ob_pdf->MultiCell(190, 4.5, "DESCRIÇÃO DE REUNIÃO COM FORNECEDOR", 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 4);
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->MultiCell(190, 4.5, "        O presente relatório tem por finalidade descrever a reunião realizada com o fornecedor tratado no ponto 2 (dois), pautados pelos assuntos descritos no ponto 5 (cinco).");
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "1 - DATA E HORÁRIO");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, "Dt Reunião: ".$row['dt_reuniao']);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, "Dt Início da Reunião: ".$row['dt_reuniao_ini']);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, "Dt Fim da Reunião: ".$row['dt_reuniao_fim']);

            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "2 - FORNECEDOR");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, "Instituição: " . $row['ds_reuniao_sg_instituicao']);

            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "3.1 - CONVIDADOS INTERNOS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);

            $this->reuniao_sg_model->participantes($result, $args);
            $participantes = $result->result_array();
            
            $ob_pdf->SetWidths(array(89, 103));
            $ob_pdf->SetAligns(array('C', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->Row(array("Convidado", "Gerência"));
            $ob_pdf->SetAligns(array('L', 'L'));
            
            foreach ($participantes as $item)
            {
                $ob_pdf->Row(array($item['nome'], $item['gerencia']));
            }

            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "3.2 - CONVIDADOS EXTERNOS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, trim($row['participantes']));

            $ob_pdf->SetY($ob_pdf->GetY() + 4);
            
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "4.1 - PARTICIPANTES INTERNOS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);

            $this->reuniao_sg_model->get_usuarios_parecer($result, $args);
            $participantes_parecer = $result->result_array();
            
            $ob_pdf->SetWidths(array(89, 103));
            $ob_pdf->SetAligns(array('C', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->Row(array("Participante", "Gerência"));
            $ob_pdf->SetAligns(array('L', 'L'));
            
            foreach ($participantes_parecer as $item)
            {
                $ob_pdf->Row(array($item['nome'], $item['gerencia']));
            }

            $ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "4.2 - PARTICIPANTES EXTERNOS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, trim($row['relato']));
            
            
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $this->reuniao_sg_model->get_assuntos($result, $args);
            $assuntos = $result->result_array();

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "5 - ASSUNTOS TRATADOS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);

            $ob_pdf->SetWidths(array(89, 103));
            $ob_pdf->SetAligns(array('C', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->Row(array("Assunto", "Complemento"));
            $ob_pdf->SetAligns(array('L', 'L'));

            foreach ($assuntos as $item)
            {
                $ob_pdf->Row(array($item['ds_reuniao_sg_assunto'], $item['complemento']));
            }

            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "6 - PARECER");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, $row['parecer']);
            
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "7 - QUALIFICAÇÃO");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, $row['parecer_qualificacao']);

            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $this->reuniao_sg_model->get_usuarios($result, $args);
            $usuarios = $result->result_array();

            $ob_pdf->SetWidths(array(89, 73, 30));
            $ob_pdf->SetAligns(array('C', 'C', 'C'));
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->Row(array("Usuário", "Status", "Dt Aprovação"));
            $ob_pdf->SetAligns(array('L', 'L'));

            foreach ($usuarios as $item)
            {
                switch ($item['fl_validacao'])
                {
                    case 'S':
                        $validacao = 'Sim';
                        break;
                    case 'N':
                        $validacao = 'Não';
                        break;
                    default :
                        $validacao = 'Não Informado';
                        break;
                }

                $ob_pdf->Row(array($item['nome'], $validacao, $item['dt_validacao']));
            }

            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function imprimir_relatorio()
    {
        if ($this->fl_acesso) 
        {
            $result = null;
            $data = Array();
            $args = Array();
			
            $args["dt_reuniao_ini"]            = $this->input->post("dt_reuniao_ini", TRUE);
            $args["dt_reuniao_fim"]            = $this->input->post("dt_reuniao_fim", TRUE);
			$args["dt_ini_ini"]                = $this->input->post("dt_ini_ini", TRUE);
            $args["dt_ini_fim"]                = $this->input->post("dt_ini_fim", TRUE);
	
            $args["cd_reuniao_sg_instituicao"] = $this->input->post("cd_reuniao_sg_instituicao", TRUE);
            
            $this->reuniao_sg_model->listar_relatorio($result, $args);
            $collection = $result->result_array();

            $this->load->plugin('fpdf');
            
            $ob_pdf = new PDF();
            
            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10, 14, 10);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "";

            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0, 0, 0);
            
            $ob_pdf->AddPage();
            
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "RELATÓRIO SOBRE AS REUNIÕES COM FORNECEDORES", 0, "C");
            $ob_pdf->MultiCell(190, 4.5, 'RECEBIDOS PELA GERÊNCIA DE INVESTIMENTOS ("GIN")', 0, "C");
			
			if(trim($args["dt_reuniao_ini"]) != '' AND trim($args["dt_reuniao_fim"]) != '')
			{
				$ob_pdf->MultiCell(190, 4.5, 'PERÍODO: '. $args["dt_reuniao_ini"] .' até '. $args["dt_reuniao_fim"] , 0, "C");
			}
			else
			{
				$ob_pdf->MultiCell(190, 4.5, 'PERÍODO: '. $args["dt_ini_ini"] .' até '. $args["dt_ini_fim"] , 0, "C");
			}
            
            $ob_pdf->SetY($ob_pdf->GetY() + 4);
            
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "1. OBJETIVO");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, '      O presente relatório tem por finalidade apresentar as reuniões com fornecedores recebidos pela Gerência de Investimentos ("GIN") desta Entidade, de acordo com os diversos assuntos apresentados, e o respectivo parecer de tais reuniões. Todas elas foram realizadas durante o período de '. $args["dt_reuniao_ini"] .' até '. $args["dt_reuniao_fim"].'.');
            
            $ob_pdf->SetY($ob_pdf->GetY() + 4);
            
            $ob_pdf->SetFont('Courier', 'B', 12);
            $ob_pdf->MultiCell(190, 4.5, "2. REUNIÕES REALIZADAS");
            $ob_pdf->SetFont('Courier', '', 10);
            $ob_pdf->SetY($ob_pdf->GetY() + 1);
            $ob_pdf->MultiCell(190, 4.5, '      Segue abaixo um resumo das reuniões realizadas, detalhando os assuntos tratados e o respectivo resultado de cada uma delas:');
            
            $i = 1;
            foreach ($collection as $item)
            {
                $ob_pdf->SetY($ob_pdf->GetY() + 6);
            
                $ob_pdf->SetFont('Courier', 'B', 12);
                $ob_pdf->MultiCell(190, 4.5, "2.".$i." ".$item['ds_reuniao_sg_instituicao']." - ".$item['dt_reuniao']);
                $ob_pdf->SetY($ob_pdf->GetY() + 2);
                $ob_pdf->SetFont('Courier', '', 10);
                $ob_pdf->MultiCell(190, 4.5, "Dt. Início Reunião: ".$item['dt_reuniao_ini']." Dt. Fim Reunião: ".$item['dt_reuniao_fim']);
                $ob_pdf->SetY($ob_pdf->GetY() + 4);
                $i++;
                
                $ob_pdf->SetWidths(array(80, 111));
                $ob_pdf->SetAligns(array('C', 'C'));
                $ob_pdf->SetFont('Courier', '', 10);
                $ob_pdf->Row(array("Assunto", "Complemento"));
                $ob_pdf->SetAligns(array('L', 'L'));
                
                $args['cd_reuniao_sg'] = $item['cd_reuniao_sg'];
                $this->reuniao_sg_model->get_assuntos($result, $args);
                $assuntos = $result->result_array();
                
                foreach ($assuntos as $item2)
                {
                    $ob_pdf->Row(array($item2['ds_reuniao_sg_assunto'], $item2['complemento']));
                }
                
                $ob_pdf->SetFont('Courier', '', 10);
                $ob_pdf->SetY($ob_pdf->GetY() + 1);
                $ob_pdf->MultiCell(190, 4.5, 'Parecer: '.$item['parecer']);
                
                
                switch ($item["parecer_qualificacao"]) {
                    case "P":
                        $parecer = 'Positivo';
                        break;
                    case "N":
                        $parecer = 'Negativo';
                        break;
                    case "R":
                        $parecer = "Neutro";
                        break;
                    default :
                        $parecer = '';
                        break;
                }
                
                $ob_pdf->MultiCell(190, 4.5, 'Qualificação: '.$parecer);
            }
            
            $ob_pdf->SetY($ob_pdf->GetY() + 10);
            $linha1 = $ob_pdf->GetY();
            
            if($linha1 >= 260)
            {
                $ob_pdf->AddPage();
                $linha1 = $ob_pdf->GetY();
            }
            /*
            $args['cd_usuario'] = 463; #Bruno Luiz Ozelame
 
            $this->reuniao_sg_model->get_assinatura($result, $args);
            $assinatura = $result->row_array();
			
            list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
            
            $ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 0, $linha1 -20, $ob_pdf->ConvertSize($width/2.5), $ob_pdf->ConvertSize($height/2.5));    
            */
            $args['cd_usuario'] = 232; #Cristiano Viera dos Santos
 
            $this->reuniao_sg_model->get_assinatura($result, $args);
            $assinatura = $result->row_array();
            
            list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
            
            $ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 95, $linha1 -20, $ob_pdf->ConvertSize($width/2.5), $ob_pdf->ConvertSize($height/2.5)); 
            
            //$ob_pdf->Text(30,$linha1+14, trim('Bruno Luiz Ozelame'));
            //$ob_pdf->Text(25,$linha1+18, trim('Consultor de Investimentos'));
            
            $ob_pdf->Text(120,$linha1+14, trim('Cristiano Viera dos Santos'));
            $ob_pdf->Text(120,$linha1+18, trim('Consultor de Investimentos'));
            
            $ob_pdf->SetY($ob_pdf->GetY() + 25);
            $linha2 = $ob_pdf->GetY();
            
            if($linha2 >= 260)
            {
                $ob_pdf->AddPage();
                $linha2 = $ob_pdf->GetY();
            }
            
            $args['cd_usuario'] = 470; #Fernando Rea Amorim

            $this->reuniao_sg_model->get_assinatura($result, $args);
            $assinatura = $result->row_array();
            
			if((count($assinatura) > 0) and ($assinatura['assinatura']))
			{
				list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
				$ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 0, $linha2 -12, $ob_pdf->ConvertSize($width/2.5), $ob_pdf->ConvertSize($height/2.5));    
			}

            $ob_pdf->Text(30,$linha2+23, trim('Fernando Rea Amorim'));
            $ob_pdf->Text(25,$linha2+27, trim('Supervisor de Investimentos'));
            
            $ob_pdf->SetY($ob_pdf->GetY() + 30);
            $linha3 = $ob_pdf->GetY();
            
            if($linha3 >= 260)
            {
                $ob_pdf->AddPage();
                $linha3 = $ob_pdf->GetY();
            }
            
			/*
            $args['cd_usuario'] = 399; #Daniel Vieira Braga Abreu
 
            $this->reuniao_sg_model->get_assinatura($result, $args);
            $assinatura = $result->row_array();
            
			if((count($assinatura) > 0) and ($assinatura['assinatura']))
			{			
				list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
				$ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 95, $linha2 -10, $ob_pdf->ConvertSize($width/2.5), $ob_pdf->ConvertSize($height/2.5));    
			}

            $ob_pdf->Text(120,$linha2+23, trim('Daniel Vieira Braga Abreu'));
            $ob_pdf->Text(112,$linha2+27, trim('Assistente Técnico de Investimentos'));
            
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $linha3 = $ob_pdf->GetY();
            
            if($linha3 >= 260)
            {
                $ob_pdf->AddPage();
                $linha3 = $ob_pdf->GetY();
            }
            */

/*
			#### (90934) - DESATIVADO PELA AUSENCIA DE GERENTE - 16/05/2025 - REATIVAR AO CONTRATAR UM NOVO GERENTE ####
			
            $this->reuniao_sg_model->gerente_gin($result, $args);
            $row = $result->row_array();
            
            list($width, $height) = getimagesize('./img/assinatura/'.$row['assinatura']); 
            
            $ob_pdf->Image('./img/assinatura/'.$row['assinatura'], 95, $linha2-10, $ob_pdf->ConvertSize($width/2.5), $ob_pdf->ConvertSize($height/2.5));    
                      
            $ob_pdf->Text(125,$linha2+27, trim('Rafael Rocha Luzardo'));
            $ob_pdf->Text(120,$linha2+31, trim('Gerente de Investimentos'));
*/
  
            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function anexo($cd_reuniao_sg)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_reuniao_sg'] = intval($cd_reuniao_sg);
        $args['cd_usuario']    = $this->session->userdata('codigo');

        $this->reuniao_sg_model->verifica_permissao_consulta($result, $args);
        $ar_reg = $result->row_array();

        if (($this->fl_acesso) OR ($ar_reg['fl_consulta'] == "S"))
        {
            $this->reuniao_sg_model->cadastro($result, $args);
			$data['row'] = $result->row_array();

            $this->load->view('atividade/reuniao_sg/anexo', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function listar_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_reuniao_sg'] = $this->input->post("cd_reuniao_sg", TRUE);
		
		$this->reuniao_sg_model->listar_anexo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/reuniao_sg/anexo_result', $data);
	}
	
	function salvar_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				$args['cd_reuniao_sg'] = $this->input->post("cd_reuniao_sg", TRUE);
				$args["cd_usuario"]    = $this->session->userdata('codigo');
				
				$this->reuniao_sg_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("atividade/reuniao_sg/anexo/".intval($args["cd_reuniao_sg"]), "refresh");
	}
	
	function excluir_anexo($cd_reuniao_sg, $cd_reuniao_sg_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_reuniao_sg']       = $cd_reuniao_sg;
		$args['cd_reuniao_sg_anexo'] = $cd_reuniao_sg_anexo;
		$args["cd_usuario"]          = $this->session->userdata('codigo');

		$this->reuniao_sg_model->excluir_anexo($result, $args);
		
		redirect("atividade/reuniao_sg/anexo/".intval($cd_reuniao_sg), "refresh");
	}

}

?>