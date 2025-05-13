<?php
class Sumula_interventor extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('gestao/sumula_interventor_model');
    }

	public function get_usuarios()
    {		
		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);
		$cd_usuario  = $this->input->post('cd_usuario', TRUE);
		
		$usuarios = $this->sumula_interventor_model->get_usuarios($cd_gerencia, $cd_usuario);
				
		echo json_encode($usuarios);
    }
	
    public function index()
    {
		if (gerencia_in(array('GC')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$this->load->view('gestao/sumula_interventor/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar()
    {
		if (gerencia_in(array('GC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['nr_sumula_interventor']       = $this->input->post("nr_sumula_interventor", TRUE);   
			$args['dt_ini']          = $this->input->post("dt_ini", TRUE);   
			$args['dt_fim']          = $this->input->post("dt_fim", TRUE);   
			$args['dt_div_ini']      = $this->input->post("dt_div_ini", TRUE);   
			$args['dt_div_fim']      = $this->input->post("dt_div_fim", TRUE);   
			$args['fl_respondido']   = $this->input->post("fl_respondido", TRUE);   
			$args['cd_resposta']     = $this->input->post("cd_resposta", TRUE);   
			$args['dt_resposta_ini'] = $this->input->post("dt_resposta_ini", TRUE);   
			$args['dt_resposta_fim'] = $this->input->post("dt_resposta_fim", TRUE);   
			$args['descricao']		 = '';   
				
			manter_filtros($args);

			$this->sumula_interventor_model->listar($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula_interventor/partial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function cadastro($cd_sumula_interventor = 0)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_sumula_interventor'] = intval($cd_sumula_interventor);
            $args['cd_sumula_interventor'] = intval($cd_sumula_interventor);
			
			$this->sumula_interventor_model->total_itens($result, $args);
            $arr_total_itens = $result->row_array();

            $this->sumula_interventor_model->total_itens_enviados($result, $args);
            $arr_total_itens_enviados = $result->row_array();

            $data['fl_editar'] = true;

			if((intval($arr_total_itens['tl']) > 0) AND (intval($arr_total_itens_enviados['tl']) > 0))
			{
				$data['fl_editar'] = false;
			}

            if ($cd_sumula_interventor == 0)
            {
                $row = $this->sumula_interventor_model->carrega_numero_cadastro();

                $data['row'] = Array(
                  'cd_sumula_interventor'     => 0,
                  'nr_sumula_interventor'     => (count($row) > 0 ? $row['nr_sumula_interventor'] : ''),
                  'dt_sumula_interventor'     => '',
                  'dt_divulgacao' => '',
                  'arquivo'       => '',
                  'arquivo_nome'  => ''
                );
            }
            else
            {
                $this->sumula_interventor_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('gestao/sumula_interventor/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function validar_nr_sumula_interventor()
    {
        $nr_sumula_interventor = $this->input->post("nr_sumula_interventor", TRUE);

        $cd_sumula_interventor = $this->input->post("cd_sumula_interventor", TRUE);

        $data = $this->sumula_interventor_model->valida_numero_sumula_interventor($nr_sumula_interventor, $cd_sumula_interventor);

        echo json_encode($data);
    }

    function salvar()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $this->load->model("gestao/pauta_sg_model");

            $args['cd_sumula_interventor']     = $this->input->post("cd_sumula_interventor", TRUE);
            $args['nr_sumula_interventor']     = $this->input->post("nr_sumula_interventor", TRUE);
            $args['dt_sumula_interventor']     = $this->input->post("dt_sumula_interventor", TRUE);
            $args['dt_divulgacao'] = $this->input->post("dt_divulgacao", TRUE);
            $args['arquivo_nome']  = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo']       = $this->input->post("arquivo", TRUE);
            $args['cd_usuario']    = $this->session->userdata('codigo');

            $cd_sumula_interventor = $this->sumula_interventor_model->salvar($result, $args);

            $args['cd_sumula_interventor'] = $cd_sumula_interventor;

            $this->pauta_sg_model->assunto_sumula_in($result, $args);

            redirect("gestao/sumula_interventor/responsabilidade/".$cd_sumula_interventor, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function publicar()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_interventor']            = $this->input->post("cd_sumula_interventor", TRUE);
            $args['dt_publicacao_libera'] = $this->input->post("dt_publicacao_libera", TRUE);
            $args['cd_usuario']           = $this->session->userdata('codigo');

            $this->sumula_interventor_model->publicar($result, $args);
        }
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }
    }		

    function responsabilidade($cd_sumula_interventor, $cd_sumula_interventor_item = 0)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

			$data['enviados'] = array();
			$data['nao_enviados'] = array();
			$data['usuarios'] = array();
            
			$data['cd_sumula_interventor'] = intval($cd_sumula_interventor);
            $args['cd_sumula_interventor'] = intval($cd_sumula_interventor);
			$data['cd_sumula_interventor_item'] = intval($cd_sumula_interventor_item);
            $args['cd_sumula_interventor_item'] = intval($cd_sumula_interventor_item);
			
			$this->sumula_interventor_model->gericias_cadastradas($result, $args);
            $data['arr_gerencia_cad'] = $result->result_array();
			
            $this->sumula_interventor_model->total_itens_nao_enviados($result, $args);
            $data['total_enviados'] = $result->result_array();

            $this->sumula_interventor_model->carrega($result, $args);
            $data['row'] = $result->row_array();

			$data['nao_enviados'] = intval($data['total_enviados'][0]['tl']);
			
			if(isset($data['total_enviados'][1]))
			{
				$data['enviados'] = intval($data['total_enviados'][1]['tl']);
			}
			
			if(intval($cd_sumula_interventor_item > 0))
			{
				$this->sumula_interventor_model->carrega_sumula_interventor_item($result, $args);
				$data['row_item'] = $result->row_array();
			}
			else
			{
				$data['row_item'] = array(
					'cd_gerencia' 			 => '',
					'nr_sumula_interventor_item'		 => '',
					'descricao' 			 => '',
					'cd_responsavel' 		 => '',
					'cd_divisao_responsavel' => '',
					'cd_substituto'			 => '',
					'cd_divisao_substituto'  => ''				
				);
			}
			
            $this->load->view('gestao/sumula_interventor/responsabilidade', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar_responsabilidade()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_interventor']   = $this->input->post("cd_sumula_interventor", TRUE);
            $args['fl_recebido'] = $this->input->post("fl_recebido", TRUE);
            $args['cd_gerencia'] = $this->input->post("cd_gerencia", TRUE);
            $args['cd_resposta'] = $this->input->post("cd_resposta", TRUE);
			
            $this->sumula_interventor_model->lista_itens($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula_interventor/responsabilidade_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar_item()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_interventor']     			= $this->input->post("cd_sumula_interventor", TRUE);
            $args['cd_sumula_interventor_item'] 		= $this->input->post("cd_sumula_interventor_item", TRUE);
            $args['cd_gerencia']    		= $this->input->post("cd_gerencia", TRUE);
            $args['nr_sumula_interventor_item'] 		= $this->input->post('nr_sumula_interventor_item', TRUE);
            $args['descricao']      		= $this->input->post("descricao", TRUE);
            $args['cd_usuario_responsavel'] = $this->input->post("cd_usuario_responsavel", TRUE);
            $args['cd_usuario_substituto']  = $this->input->post("cd_usuario_substituto", TRUE);
            $args['cd_usuario']     		= $this->session->userdata('codigo');

            $this->sumula_interventor_model->salvar_item($result, $args);

            redirect("gestao/sumula_interventor/responsabilidade/" . $args['cd_sumula_interventor'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function carrega_sumula_interventor_item($cd_sumula_interventor_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_interventor_item'] = $this->input->post("cd_sumula_interventor_item", TRUE);

            $this->sumula_interventor_model->carrega_sumula_interventor_item($result, $args);
			$data['row'] = $result->row_array();

            $row = array_map("arrayToUTF8", $row);
            echo json_encode($row);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function excluir_sumula_interventor($cd_sumula_interventor, $cd_sumula_interventor_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_interventor_item'] = $cd_sumula_interventor_item;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->sumula_interventor_model->excluir_sumula_interventor_item($result, $args);

            redirect("gestao/sumula_interventor/responsabilidade/" . $cd_sumula_interventor, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function enviar_todos($cd_sumula_interventor)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_interventor'] = $cd_sumula_interventor;

            $this->sumula_interventor_model->enviar_todos($result, $args);

            redirect("gestao/sumula_interventor/cadastro/" . $cd_sumula_interventor, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function enviar($cd_sumula_interventor, $cd_sumula_interventor_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_interventor']      = $cd_sumula_interventor;
            $args['cd_sumula_interventor_item'] = $cd_sumula_interventor_item;

            $this->sumula_interventor_model->enviar($result, $args);

            redirect("gestao/sumula_interventor/responsabilidade/" . $cd_sumula_interventor, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function resposta($cd_sumula_interventor_item)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_sumula_interventor_item'] = $cd_sumula_interventor_item;

        $this->sumula_interventor_model->carrega_sumula_interventor_item_resposta($result, $args);
        $data['row'] = $result->row_array();
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_interventor_model->get_usuario_diretor($result, $args);
        $row = $result->row_array();

        if (	
			($this->session->userdata('codigo') == $data['row']['cd_responsavel'])
			OR 
			($this->session->userdata('codigo') == $data['row']['cd_substituto'])
			OR 
			(($this->session->userdata('tipo') == 'G') AND ($this->session->userdata('divisao') == $data['row']['cd_gerencia'])) #gerente
			OR
			(($this->session->userdata('indic_01') == 'S') AND ($this->session->userdata('divisao') == $data['row']['cd_gerencia'])) #substituto gerente
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_resposanvel'])
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_substituto'])
		   )
        {
            $this->load->view('gestao/sumula_interventor/resposta', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar_resposta()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_sumula_interventor_item'] = $this->input->post("cd_sumula_interventor_item", TRUE);
        $args['cd_resposta']    = $this->input->post("cd_resposta", TRUE);
        $args['complemento']    = $this->input->post("complemento", TRUE);
		$args['numero']         = $this->input->post("numero", TRUE);
        $args['ano']            = $this->input->post("ano", TRUE);
        $args['cd_usuario']     = $this->session->userdata('codigo');

        $this->sumula_interventor_model->salvar_resposta($result, $args);

        redirect("gestao/sumula_interventor/resposta/" . $args['cd_sumula_interventor_item'], "refresh");
    }
	
	function mudar_responsavel($cd_sumula_interventor_item, $cd_responsavel)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_sumula_interventor_item'] = $cd_sumula_interventor_item;
        $args['cd_responsavel'] = $cd_responsavel;
		
		$this->sumula_interventor_model->mudar_responsavel($result, $args);
		
		redirect("gestao/sumula_interventor/minhas", "refresh");
	}
	
	function acompanhamento($cd_sumula_interventor)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$data['arr_item'] = array();

            $data['cd_sumula_interventor'] = intval($cd_sumula_interventor);
            $args['cd_sumula_interventor'] = intval($cd_sumula_interventor);
			
			$this->sumula_interventor_model->item_acompanhamento($result, $args);
            $arr = $result->result_array();
			
			$i = 0;
			
			foreach($arr as $item)
			{
				$data['arr_item'][$i] = array('value' => $item['cd_sumula_interventor_item'], 'text' => $item['nr_sumula_interventor_item'].' - '.substr(trim($item['descricao']),0,100));
				
				$i++;
			}
			
            $this->load->view('gestao/sumula_interventor/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar_acompanhamento()
	{
		if (gerencia_in(array('GC')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_sumula_interventor']      = $this->input->post("cd_sumula_interventor", TRUE);
			$args['descricao']      = $this->input->post("descricao", TRUE);
			$args['cd_sumula_interventor_item'] = $this->input->post("cd_sumula_interventor_item", TRUE);
			$args['cd_usuario']     = $this->session->userdata('codigo');
			
			$this->sumula_interventor_model->salvar_acompanhamento($result, $args);
			
			redirect("gestao/sumula_interventor/acompanhamento/" . $args['cd_sumula_interventor'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function listar_acompanhamento()
	{
		if (gerencia_in(array('GC')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_sumula_interventor']   = $this->input->post("cd_sumula_interventor", TRUE);
			
            $this->sumula_interventor_model->lista_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula_interventor/acompanhamento_result', $data);
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_acompanhamento($cd_sumula_interventor ,$cd_sumula_interventor_acompanhamento)
	{
		if (gerencia_in(array('GC')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_sumula_interventor_acompanhamento'] = $cd_sumula_interventor_acompanhamento;
			$args['cd_usuario']               = $this->session->userdata('codigo');
			
            $this->sumula_interventor_model->excluir_acompanhamento($result, $args);

            redirect("gestao/sumula_interventor/acompanhamento/" . $cd_sumula_interventor, "refresh");
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

    function minhas()
    {
        $this->load->view('gestao/sumula_interventor/minhas');
    }

    function minhas_listar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_usuario']    = $this->session->userdata('codigo');
        $args['fl_respondido'] = $this->input->post("fl_respondido", TRUE);
        $args['dt_ini_envio']  = $this->input->post("dt_ini_envio", TRUE);
        $args['dt_fim_envio']  = $this->input->post("dt_fim_envio", TRUE);
        $args['dt_ini_resp']   = $this->input->post("dt_ini_resp", TRUE);
        $args['dt_fim_resp']   = $this->input->post("dt_fim_resp", TRUE);
        $args['nr_sumula_interventor']     = $this->input->post("nr_sumula_interventor", TRUE);

        manter_filtros($args);

        $this->sumula_interventor_model->carrega_minhas($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_interventor/minhas_result', $data);
    }
    
	function sumula_interventor_pdf($cd_sumula_interventor)
	{
        $args['cd_sumula_interventor']   = $cd_sumula_interventor;
        $args['fl_recebido'] = '';
		$args['cd_gerencia'] = '';
		$args['cd_resposta'] = '';		
		
        $this->sumula_interventor_model->carrega($result, $args);
        $ar_sumula_interventor = $result->row_array();		
		
		$ob_arq = './up/sumula_interventor/'.$ar_sumula_interventor['arquivo'];
		$ds_arq = $ar_sumula_interventor['arquivo_nome'];

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.$ds_arq.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($ob_arq));
		header('Accept-Ranges: bytes');	

		readfile($ob_arq);	
	}
	
    function pdf($cd_sumula_interventor)
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_sumula_interventor']   = $cd_sumula_interventor;
        $args['fl_recebido'] = '';
		$args['cd_gerencia'] = '';
		$args['cd_resposta'] = '';
        
        $this->sumula_interventor_model->carrega($result, $args);
        $row = $result->row_array();
        
        $this->sumula_interventor_model->lista_itens($result, $args);
        $arr = $result->result_array();
        
        $this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');		
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Súmula ". $row['nr_sumula_interventor'];
        
        $ob_pdf->AddPage();
        
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(0, 5, "Dt Súmula: ".$row['dt_sumula_interventor']."
        Dt Divulgação: ".$row['dt_divulgacao']."
        Link para a súmula: ", '0', 'L');
 
        $ob_pdf->SetFont('Arial', 'U', 10);
        $ob_pdf->SetTextColor(50,50,220);
        $ob_pdf->Write(5,base_url().'up/sumula_interventor/' . $row['arquivo_nome'], site_url('gestao/sumula_interventor/sumula_interventor_pdf')."/".$row['cd_sumula_interventor']);
        $ob_pdf->SetTextColor(0,0,0);
        $ob_pdf->SetY($ob_pdf->GetY()+10);
        
        foreach ($arr as $item)
        {
			$ob_pdf->SetWidths( array(45, 145) );
			$ob_pdf->SetAligns( array('L','L') );
		
			$args['cd_sumula_interventor_item'] =  $item['cd_sumula_interventor_item'];
			
			$this->sumula_interventor_model->acompanhamento_item($result, $args);
			$arr_acompanhamento = $result->result_array();
			
			$this->sumula_interventor_model->listar_anexo($result, $args);
			$arr_anexo = $result->result_array();
			
            $resposta = '';
    
            if($item['cd_resposta'] == 'AP')
            {
                $resposta = 'Ação Preventiva';
            }
            else if($item['cd_resposta'] == 'NC')
            {
                $resposta = 'Não Conforminadade';
            }
            else if($item['cd_resposta'] == 'SR')
            {
                $resposta = 'Sem Reflexo';
            }
            else if($item['cd_resposta'] == 'SP')
            {
                $resposta = 'Sem Reflexo - Plano de Ação';
            }

            if($item['complemento'] != '')
            {
                $resposta .= ': '.$item['complemento'];
            }
            
            $ob_pdf->SetFont('segoeuib', '', 13);
            
            $ob_pdf->MultiCell(0, 7, "Item: ". $item['nr_sumula_interventor_item'], '0', 'L');
            
            $ob_pdf->SetFont('segoeuil', '', 12);
            
            $ob_pdf->Row(array("Gerência: ", $item['gerencia']));	
            $ob_pdf->Row(array("Responsável: ", $item['responsavel']));
            $ob_pdf->Row(array("Substituto: ", $item['substituto']));
            $ob_pdf->Row(array("Descrição: ", $item['descricao']));
            $ob_pdf->Row(array("Resposta: ", $resposta));
			if((trim($item['numero']) != '') AND (trim($item['ano']) != ''))
			{
				$ob_pdf->Row(array("Ano/Número: ", $item['nr_ano_numero']));
			}
            $ob_pdf->Row(array("Respondido por: ", $item['nome']));
            $ob_pdf->Row(array("Dt Envio: ", $item['dt_envio']));
            $ob_pdf->Row(array("Dt Limite: ", $item['dt_limite']));
            $ob_pdf->Row(array("Dt Resposta: ", $item['dt_resposta']));
			
			if(count($arr_anexo) > 0)
			{
				$ob_pdf->MultiCell(0, 7, "Anexos: ", '0', 'L');
			}
			
			foreach($arr_anexo as $item_anexo)
			{				
				$ob_pdf->SetFont('Arial', 'U', 10);
				$ob_pdf->SetTextColor(50,50,220);
				$ob_pdf->Write(5,base_url().'up/sumula_interventor/' . $item_anexo['arquivo_nome'], base_url().'up/sumula_interventor/' . $item_anexo['arquivo']);
				$ob_pdf->SetTextColor(0,0,0);
				$ob_pdf->SetY($ob_pdf->GetY()+4);
			}
			
			if(count($arr_acompanhamento) > 0)
			{
				$ob_pdf->SetY($ob_pdf->GetY()+5);
				
				$ob_pdf->SetWidths(array(35,100,55));
				$ob_pdf->SetAligns(array('C', 'C', 'C'));
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->Row(array("Data ", "Acompanhamento", "Usuário"));
				$ob_pdf->SetAligns(array('C', 'L', 'L'));
								
				foreach ($arr_acompanhamento as $item2)
				{
					$ob_pdf->Row(array($item2['dt_inclusao'], $item2['descricao'], $item2['nome']));
				}
			}
			 
            $ob_pdf->SetY($ob_pdf->GetY()+5);
        }
		
		$this->sumula_interventor_model->acompanhamento_sem_item($result, $args);
		$arr = $result->result_array();
		
		if(count($arr) > 0)
		{		
			$ob_pdf->SetFont('segoeuib', '', 13);
			
			$ob_pdf->MultiCell(0, 7, "Acompanhamentos da Súmula", '0', 'L');
			
			$ob_pdf->SetFont('segoeuil', '', 10);
			
			$ob_pdf->SetWidths(array(35,100,55));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->Row(array("Data", "Acompanhamento", "Usuário"));
			$ob_pdf->SetAligns(array('C', 'L', 'L'));
							
			foreach ($arr as $item)
			{
				$ob_pdf->Row(array($item['dt_inclusao'], $item['descricao'], $item['nome']));
			}
		}
        
        $ob_pdf->Output();
    }
	
	public function consulta()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $this->load->view('gestao/sumula_interventor/consulta', $data);
    }

    public function consulta_listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula_interventor']       = $this->input->post("nr_sumula_interventor", TRUE);   
        $args['descricao']       = $this->input->post('descricao', TRUE);   
        $args['dt_ini']          = $this->input->post("dt_ini", TRUE);   
        $args['dt_fim']          = $this->input->post("dt_fim", TRUE);   
        $args['dt_div_ini']      = $this->input->post("dt_div_ini", TRUE);   
        $args['dt_div_fim']      = $this->input->post("dt_div_fim", TRUE);   
        $args['fl_respondido']   = '';   
        $args['cd_resposta']     = '';   
        $args['dt_resposta_ini'] = '';   
        $args['dt_resposta_fim'] = '';   
            
        manter_filtros($args);

        $this->sumula_interventor_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_interventor/consulta_result', $data);
    }
	
	function enviar_fundacao($cd_sumula_interventor)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_sumula_interventor']  = $cd_sumula_interventor;
            $args['cd_usuario'] = $this->session->userdata('codigo');
                    
			$this->sumula_interventor_model->carrega($result, $args);
			$row = $result->row_array();
			
			$args['assunto'] = 'Súmula INTERVENTOR nº '.$row['nr_sumula_interventor'];
			$args['texto']   = 'Está disponível a SÚMULA '.$row['nr_sumula_interventor'].' referente a reunião do INTERVENTOR ocorrida em '.$row['dt_sumula_interventor'].'.

            Clique no link abaixo para acessar:
            '.site_url('gestao/sumula_interventor/sumula_interventor_pdf')."/".$row['cd_sumula_interventor'].'

            ';

            $this->sumula_interventor_model->enviar_fundacao($result, $args);

            redirect("gestao/sumula_interventor/cadastro/".$cd_sumula_interventor, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function anexo($cd_sumula_interventor_item)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['arr_item'] = array();

		$args['cd_sumula_interventor_item'] = intval($cd_sumula_interventor_item);
		
		$this->sumula_interventor_model->carrega_sumula_interventor_item_resposta($result, $args);
        $data['row'] = $result->row_array();
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_interventor_model->get_usuario_diretor($result, $args);
        $row = $result->row_array();
		
		if (
			($this->session->userdata('codigo') == $data['row']['cd_responsavel'])
			OR 
			($this->session->userdata('codigo') == $data['row']['cd_substituto'])
			OR 
			(($this->session->userdata('tipo') == 'G') AND ($this->session->userdata('divisao') == $data['row']['cd_gerencia'])) #gerente
			OR
			(($this->session->userdata('indic_01') == 'S') AND ($this->session->userdata('divisao') == $data['row']['cd_gerencia'])) #substituto gerente
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_resposanvel'])
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_substituto'])
		   )
        {
            $this->load->view('gestao/sumula_interventor/anexo', $data);
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
		
		$args['cd_sumula_interventor_item'] = $this->input->post("cd_sumula_interventor_item", TRUE);
		
		$this->sumula_interventor_model->listar_anexo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('gestao/sumula_interventor/anexo_result', $data);
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
				
				$args['cd_sumula_interventor_item'] = $this->input->post("cd_sumula_interventor_item", TRUE);
				$args["cd_usuario"]     = $this->session->userdata('codigo');
				
				$this->sumula_interventor_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("gestao/sumula_interventor/anexo/".intval($args["cd_sumula_interventor_item"]), "refresh");
	}
	
	function excluir_anexo($cd_sumula_interventor_item, $cd_sumula_interventor_item_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_sumula_interventor_item']       = $cd_sumula_interventor_item;
		$args['cd_sumula_interventor_item_anexo'] = $cd_sumula_interventor_item_anexo;
		$args["cd_usuario"]           = $this->session->userdata('codigo');

		$this->sumula_interventor_model->excluir_anexo($result, $args);
		
		redirect("gestao/sumula_interventor/anexo/".intval($args["cd_sumula_interventor_item"]), "refresh");
	}
}
?>