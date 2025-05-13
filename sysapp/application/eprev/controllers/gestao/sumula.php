<?php
class Sumula extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('gestao/sumula_model');
    }

    public function verifica_sumula()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula']       = $this->input->post("nr_sumula", TRUE);   
        $args['dt_ini']          = $this->input->post("dt_ini", TRUE);   
        $args['dt_fim']          = $this->input->post("dt_fim", TRUE);   
        $args['dt_div_ini']      = $this->input->post("dt_div_ini", TRUE);   
        $args['dt_div_fim']      = $this->input->post("dt_div_fim", TRUE);   
        $args['fl_respondido']   = $this->input->post("fl_respondido", TRUE);   
        $args['cd_resposta']     = $this->input->post("cd_resposta", TRUE);   
        $args['dt_resposta_ini'] = $this->input->post("dt_resposta_ini", TRUE);   
        $args['dt_resposta_fim'] = $this->input->post("dt_resposta_fim", TRUE);   
        $args['descricao']       = '';   

        $this->sumula_model->listar($result, $args);
        $collection = $result->result_array();

        foreach ($collection as $key => $item) 
        {
            if (file_exists('./up/sumula/'.$item['arquivo'])) 
            {
            } 
            else 
            {
                echo $item['cd_sumula'].' - '.$item['nr_sumula'].' - '.$item['arquivo_nome'].' - '.$item['arquivo'].' - NÃO FOI ENCONTRADO'.br();
				
				if (file_exists('../_a/sumula/sumula '.$item['nr_sumula'].'.pdf'))
				{
					#echo "achei".br();
					#copy('../_a/sumula/sumula '.$item['nr_sumula'].'.pdf', './up/sumula/'.$item['arquivo']);
				}
				
            }
        }
    }

    public function intranet()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula']       = '';   
        $args['descricao']       = '';   
        $args['dt_ini']          = '';   
        $args['dt_fim']          = '';   
        $args['dt_div_ini']      = '';   
        $args['dt_div_fim']      = '';   
        $args['fl_respondido']   = '';   
        $args['cd_resposta']     = '';   
        $args['dt_resposta_ini'] = '';   
        $args['dt_resposta_fim'] = '';   
            
        manter_filtros($args);

        $this->sumula_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula/intranet', $data);
    }

	public function get_usuarios()
    {		
		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);
		$cd_usuario  = $this->input->post('cd_usuario', TRUE);
		
        foreach($this->sumula_model->get_usuarios($cd_gerencia, $cd_usuario) as $item)
        {
            $data[] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }
        
        echo json_encode($data);
    }
	
    public function index()
    {
		if (gerencia_in(array('GC', 'DE')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$this->load->view('gestao/sumula/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar()
    {
		if (gerencia_in(array('GC', 'DE')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['nr_sumula']       = $this->input->post("nr_sumula", TRUE);   
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

			$this->sumula_model->listar($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula/partial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function cadastro($cd_sumula = 0)
    {
        if (gerencia_in(array('GC', 'DE')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_sumula'] = intval($cd_sumula);
            $args['cd_sumula'] = intval($cd_sumula);
			
			$this->sumula_model->total_itens($result, $args);
            $arr_total_itens = $result->row_array();

            $this->sumula_model->total_itens_enviados($result, $args);
            $arr_total_itens_enviados = $result->row_array();

            $data['fl_editar'] = true;

			if((intval($arr_total_itens['tl']) > 0) AND (intval($arr_total_itens_enviados['tl']) > 0))
			{
				$data['fl_editar'] = false;
			}

            if ($cd_sumula == 0)
            {
                $row = $this->sumula_model->carrega_numero_cadastro();

                $data['row'] = Array(
                  'cd_sumula'     => 0,
                  'nr_sumula'     => (count($row) > 0 ? $row['nr_sumula'] : ''),
                  'dt_sumula'     => '',
                  'dt_divulgacao' => '',
                  'arquivo'       => '',
                  'arquivo_nome'  => ''
                );
            }
            else
            {
                $this->sumula_model->carrega($result, $args);
                $data['row'] = $result->row_array();

                if(trim($data['row']['arquivo_nome']) == '')
                {
                    $data['fl_editar'] = true;
                }
            }

            $this->load->view('gestao/sumula/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function validar_nr_sumula()
    {
        $nr_sumula = $this->input->post("nr_sumula", TRUE);

        $cd_sumula = $this->input->post("cd_sumula", TRUE);

        $data = $this->sumula_model->valida_numero_sumula($nr_sumula, $cd_sumula);

        if(intval($cd_sumula) == 0 AND intval($data['valida']) == 0)
        {
            $row = $this->sumula_model->valida_pauta($nr_sumula);

            if(intval($row['tl_valida_pauta']) == 0)
            {
                $data['valida'] = 2;
            }
        }

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
            
            $cd_sumula_new = $this->input->post("cd_sumula", TRUE);

            $args['cd_sumula']     = $this->input->post("cd_sumula", TRUE);
            $args['nr_sumula']     = $this->input->post("nr_sumula", TRUE);
            $args['dt_sumula']     = $this->input->post("dt_sumula", TRUE);
            $args['dt_divulgacao'] = $this->input->post("dt_divulgacao", TRUE);
            $args['arquivo_nome']  = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo']       = $this->input->post("arquivo", TRUE);
            $args['cd_usuario']    = $this->session->userdata('codigo');

            $cd_sumula = $this->sumula_model->salvar($result, $args);

            $args['cd_sumula'] = $cd_sumula;

            if(intval($cd_sumula_new) == 0)
            {
                $this->pauta_sg_model->assunto_sumula_de($result, $args);
            }

            redirect("gestao/sumula/responsabilidade/".$cd_sumula, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvarAta()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula']        = $this->input->post("cd_sumula_ata", TRUE);
            $args['arquivo_ata_nome'] = $this->input->post("arquivo_ata_nome", TRUE);
            $args['arquivo_ata']      = $this->input->post("arquivo_ata", TRUE);
            $args['cd_usuario']       = $this->session->userdata('codigo');

            $this->sumula_model->salvarAta($result, $args);
			
			#### INTEGRACAO COM PYDIO ####
			$this->sumula_model->carrega($result, $args);
			$row = $result->row_array();

			$dir_aprovado = str_replace("DIRETORIA","DOCUMENTOS_APROVADOS",$row['integracao_arq']);

			copy('../cieprev/up/sumula/'.$row['arquivo_ata'], $row['integracao_arq'].'/ade-'.$row['nr_sumula'].'.pdf');
			copy('../cieprev/up/sumula/'.$row['arquivo_ata'], $dir_aprovado.'/ade-'.$row['nr_sumula'].'.pdf');

            list($d, $m, $a) = explode('/', $row['dt_sumula']);
/*
            $caminho_previc = '../eletroceee/pydio/data/PREVIC/Atas DE/'.$a;

            if(!is_dir($caminho_previc))
            {
                mkdir($caminho_previc, 0777);
            }

            $caminho_previc .= '/'.$row['nr_sumula'];

            if(!is_dir($caminho_previc))
            {
                mkdir($caminho_previc, 0777);
            }

            copy('../cieprev/up/sumula/'.$row['arquivo_ata'], $caminho_previc.'/ade-'.$row['nr_sumula'].'.pdf');
			*/
			redirect("gestao/sumula/cadastro/".$args['cd_sumula'], "refresh");
        }
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }
    }		
	
	function publicar()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula']            = $this->input->post("cd_sumula", TRUE);
            $args['dt_publicacao_libera'] = $this->input->post("dt_publicacao_libera", TRUE);
            $args['cd_usuario']           = $this->session->userdata('codigo');

            $this->sumula_model->publicar($result, $args);

            $this->assunto_aprovado($args['cd_sumula']);
        }
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }
    }		

    public function assunto_aprovado($cd_sumula)
    {
        $this->load->plugin('encoding_pi');

        $result = null;
        $args['cd_sumula'] = $cd_sumula;

        $this->sumula_model->carrega($result, $args);
        $row = $result->row_array();
		
		$dir_aprovado = str_replace("DIRETORIA","DOCUMENTOS_APROVADOS",$row['integracao_arq']);

        copy('../cieprev/up/sumula/'.$row['arquivo'], $row['integracao_arq'].'/Sumula_'.$row['nr_sumula'].'.pdf');
        copy('../cieprev/up/sumula/'.$row['arquivo'], $dir_aprovado.'/Sumula_'.$row['nr_sumula'].'.pdf');

        $pauta_sg_assunto = $this->sumula_model->assunto_aprovado($cd_sumula);

        foreach ($pauta_sg_assunto as $key => $item) 
        {
            foreach ($this->sumula_model->assunto_aprovado_anexo($item['cd_pauta_sg_assunto']) as $key2 => $item2) 
            {
                copy('../cieprev/up/pauta/'.$item2['arquivo'], $dir_aprovado.'/documentos/'.$item['nr_item_sumula'].' - '.fixUTF8($item2['arquivo_nome']));
            }
        }
    }

    function responsabilidade($cd_sumula, $cd_sumula_item = 0)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

			$data['enviados'] = array();
			$data['nao_enviados'] = array();
			$data['usuarios'] = array();
            
			$data['cd_sumula'] = intval($cd_sumula);
            $args['cd_sumula'] = intval($cd_sumula);
			$data['cd_sumula_item'] = intval($cd_sumula_item);
            $args['cd_sumula_item'] = intval($cd_sumula_item);
			
			$this->sumula_model->gericias_cadastradas($result, $args);
            $data['arr_gerencia_cad'] = $result->result_array();
			
            $this->sumula_model->total_itens_nao_enviados($result, $args);
            $data['total_enviados'] = $result->result_array();

            $this->sumula_model->carrega($result, $args);
            $data['row'] = $result->row_array();

			$data['nao_enviados'] = intval($data['total_enviados'][0]['tl']);
			
			if(isset($data['total_enviados'][1]))
			{
				$data['enviados'] = intval($data['total_enviados'][1]['tl']);
			}
			
			if(intval($cd_sumula_item > 0))
			{
				$this->sumula_model->carrega_sumula_item($result, $args);
				$data['row_item'] = $result->row_array();
			}
			else
			{
				$data['row_item'] = array(
					'cd_gerencia' 			 => '',
					'nr_sumula_item'		 => '',
					'descricao' 			 => '',
					'cd_responsavel' 		 => '',
					'cd_divisao_responsavel' => '',
					'cd_substituto'			 => '',
					'cd_divisao_substituto'  => ''				
				);
			}
			
            $this->load->view('gestao/sumula/responsabilidade', $data);
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

            $args['cd_sumula']   = $this->input->post("cd_sumula", TRUE);
            $args['fl_recebido'] = $this->input->post("fl_recebido", TRUE);
            $args['cd_gerencia'] = $this->input->post("cd_gerencia", TRUE);
            $args['cd_resposta'] = $this->input->post("cd_resposta", TRUE);
			
            $this->sumula_model->lista_itens($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula/responsabilidade_result', $data);
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

            $args['cd_sumula']     			= $this->input->post("cd_sumula", TRUE);
            $args['cd_sumula_item'] 		= $this->input->post("cd_sumula_item", TRUE);
            $args['cd_gerencia']    		= $this->input->post("cd_gerencia", TRUE);
            $args['nr_sumula_item'] 		= $this->input->post('nr_sumula_item', TRUE);
            $args['descricao']      		= $this->input->post("descricao", TRUE);
            $args['cd_usuario_responsavel'] = $this->input->post("cd_usuario_responsavel", TRUE);
            $args['cd_usuario_substituto']  = $this->input->post("cd_usuario_substituto", TRUE);
            $args['cd_usuario']     		= $this->session->userdata('codigo');

            $this->sumula_model->salvar_item($result, $args);

            redirect("gestao/sumula/responsabilidade/" . $args['cd_sumula'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function carrega_sumula_item($cd_sumula_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_item'] = $this->input->post("cd_sumula_item", TRUE);

            $this->sumula_model->carrega_sumula_item($result, $args);
			$data['row'] = $result->row_array();

            $row = array_map("arrayToUTF8", $row);
            echo json_encode($row);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function excluir_sumula($cd_sumula, $cd_sumula_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_item'] = $cd_sumula_item;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->sumula_model->excluir_sumula_item($result, $args);

            redirect("gestao/sumula/responsabilidade/" . $cd_sumula, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function enviar_todos($cd_sumula)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula'] = $cd_sumula;

            $this->sumula_model->enviar_todos($result, $args);

            redirect("gestao/sumula/cadastro/" . $cd_sumula, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function enviar($cd_sumula, $cd_sumula_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula']      = $cd_sumula;
            $args['cd_sumula_item'] = $cd_sumula_item;

            $this->sumula_model->enviar($result, $args);

            redirect("gestao/sumula/responsabilidade/" . $cd_sumula, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function resposta($cd_sumula_item)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_sumula_item'] = $cd_sumula_item;

        $this->sumula_model->carrega_sumula_item_resposta($result, $args);
        $data['row'] = $result->row_array();
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_model->get_usuario_diretor($result, $args);
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
            $this->load->view('gestao/sumula/resposta', $data);
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

        $args['cd_sumula_item'] = $this->input->post("cd_sumula_item", TRUE);
        $args['cd_resposta']    = $this->input->post("cd_resposta", TRUE);
        $args['complemento']    = $this->input->post("complemento", TRUE);
		$args['numero']         = $this->input->post("numero", TRUE);
        $args['ano']            = $this->input->post("ano", TRUE);
        $args['cd_usuario']     = $this->session->userdata('codigo');

        $this->sumula_model->salvar_resposta($result, $args);

        redirect("gestao/sumula/resposta/" . $args['cd_sumula_item'], "refresh");
    }
	
	function mudar_responsavel($cd_sumula_item, $cd_responsavel)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_sumula_item'] = $cd_sumula_item;
        $args['cd_responsavel'] = $cd_responsavel;
		
		$this->sumula_model->mudar_responsavel($result, $args);
		
		redirect("gestao/sumula/minhas", "refresh");
	}
	
	function acompanhamento($cd_sumula)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$data['arr_item'] = array();

            $data['cd_sumula'] = intval($cd_sumula);
            $args['cd_sumula'] = intval($cd_sumula);
			
			$this->sumula_model->item_acompanhamento($result, $args);
            $arr = $result->result_array();
			
			$i = 0;
			
			foreach($arr as $item)
			{
				$data['arr_item'][$i] = array('value' => $item['cd_sumula_item'], 'text' => $item['nr_sumula_item'].' - '.substr(trim($item['descricao']),0,100));
				
				$i++;
			}
			
            $this->load->view('gestao/sumula/acompanhamento', $data);
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
			
			$args['cd_sumula']      = $this->input->post("cd_sumula", TRUE);
			$args['descricao']      = $this->input->post("descricao", TRUE);
			$args['cd_sumula_item'] = $this->input->post("cd_sumula_item", TRUE);
			$args['cd_usuario']     = $this->session->userdata('codigo');
			
			$this->sumula_model->salvar_acompanhamento($result, $args);
			
			redirect("gestao/sumula/acompanhamento/" . $args['cd_sumula'], "refresh");
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
			
			$args['cd_sumula']   = $this->input->post("cd_sumula", TRUE);
			
            $this->sumula_model->lista_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula/acompanhamento_result', $data);
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_acompanhamento($cd_sumula ,$cd_sumula_acompanhamento)
	{
		if (gerencia_in(array('GC')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_sumula_acompanhamento'] = $cd_sumula_acompanhamento;
			$args['cd_usuario']               = $this->session->userdata('codigo');
			
            $this->sumula_model->excluir_acompanhamento($result, $args);

            redirect("gestao/sumula/acompanhamento/" . $cd_sumula, "refresh");
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

    function minhas()
    {
        $this->load->view('gestao/sumula/minhas');
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
        $args['nr_sumula']     = $this->input->post("nr_sumula", TRUE);

        manter_filtros($args);

        $this->sumula_model->carrega_minhas($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula/minhas_result', $data);
    }
    
	function sumula_pdf($cd_sumula)
	{
        $args['cd_sumula']   = $cd_sumula;
        $args['fl_recebido'] = '';
		$args['cd_gerencia'] = '';
		$args['cd_resposta'] = '';		
		
        $this->sumula_model->carrega($result, $args);
        $ar_sumula = $result->row_array();		
		
		$ob_arq = './up/sumula/'.$ar_sumula['arquivo'];
		$ds_arq = $ar_sumula['arquivo_nome'];

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.$ds_arq.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($ob_arq));
		header('Accept-Ranges: bytes');	

		readfile($ob_arq);	
	}
	
    function pdf($cd_sumula)
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_sumula']   = $cd_sumula;
        $args['fl_recebido'] = '';
		$args['cd_gerencia'] = '';
		$args['cd_resposta'] = '';
        
        $this->sumula_model->carrega($result, $args);
        $row = $result->row_array();
        
        $this->sumula_model->lista_itens($result, $args);
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
        $ob_pdf->header_titulo_texto = "Súmula ". $row['nr_sumula'];
        
        $ob_pdf->AddPage();
        
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(0, 5, "Dt Súmula: ".$row['dt_sumula']."
        Dt Divulgação: ".$row['dt_divulgacao']."
        Link para a súmula: ", '0', 'L');
 
        $ob_pdf->SetFont('Arial', 'U', 10);
        $ob_pdf->SetTextColor(50,50,220);
        $ob_pdf->Write(5,base_url().'up/sumula/' . $row['arquivo_nome'], site_url('gestao/sumula/sumula_pdf')."/".$row['cd_sumula']);
        $ob_pdf->SetTextColor(0,0,0);
        $ob_pdf->SetY($ob_pdf->GetY()+10);
        
        foreach ($arr as $item)
        {
			$ob_pdf->SetWidths( array(45, 145) );
			$ob_pdf->SetAligns( array('L','L') );
		
			$args['cd_sumula_item'] =  $item['cd_sumula_item'];
			
			$this->sumula_model->acompanhamento_item($result, $args);
			$arr_acompanhamento = $result->result_array();
			
			$this->sumula_model->listar_anexo($result, $args);
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
            
            $ob_pdf->MultiCell(0, 7, "Item: ". $item['nr_sumula_item'], '0', 'L');
            
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
				$ob_pdf->Write(5,base_url().'up/sumula/' . $item_anexo['arquivo_nome'], base_url().'up/sumula/' . $item_anexo['arquivo']);
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
		
		$this->sumula_model->acompanhamento_sem_item($result, $args);
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

        $this->load->view('gestao/sumula/consulta', $data);
    }

    public function consulta_listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula']       = $this->input->post("nr_sumula", TRUE);   
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

        $this->sumula_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula/consulta_result', $data);
    }
	
	function enviar_fundacao($cd_sumula)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_sumula']  = $cd_sumula;
            $args['cd_usuario'] = $this->session->userdata('codigo');
                    
			$this->sumula_model->carrega($result, $args);
			$row = $result->row_array();
			
			$args['assunto'] = 'Súmula Diretoria nº '.$row['nr_sumula'];
			$args['texto']   = 'Está disponível a SÚMULA '.$row['nr_sumula'].' referente a reunião de Diretoria Executiva ocorrida em '.$row['dt_sumula'].'.

            Clique no link abaixo para acessar:
            '.site_url('gestao/sumula/sumula_pdf')."/".$row['cd_sumula'].'

            ';

            if(trim($row['dt_envio_todos']) == '')
            {
                $this->sumula_model->enviar_fundacao($result, $args);
            }

            redirect("gestao/sumula/cadastro/".$cd_sumula, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function anexo($cd_sumula_item)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['arr_item'] = array();

		$args['cd_sumula_item'] = intval($cd_sumula_item);
		
		$this->sumula_model->carrega_sumula_item_resposta($result, $args);
        $data['row'] = $result->row_array();
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_model->get_usuario_diretor($result, $args);
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
            $this->load->view('gestao/sumula/anexo', $data);
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
		
		$args['cd_sumula_item'] = $this->input->post("cd_sumula_item", TRUE);
		
		$this->sumula_model->listar_anexo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('gestao/sumula/anexo_result', $data);
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
				
				$args['cd_sumula_item'] = $this->input->post("cd_sumula_item", TRUE);
				$args["cd_usuario"]     = $this->session->userdata('codigo');
				
				$this->sumula_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("gestao/sumula/anexo/".intval($args["cd_sumula_item"]), "refresh");
	}
	
	function excluir_anexo($cd_sumula_item, $cd_sumula_item_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_sumula_item']       = $cd_sumula_item;
		$args['cd_sumula_item_anexo'] = $cd_sumula_item_anexo;
		$args["cd_usuario"]           = $this->session->userdata('codigo');

		$this->sumula_model->excluir_anexo($result, $args);
		
		redirect("gestao/sumula/anexo/".intval($args["cd_sumula_item"]), "refresh");
	}

    function resolucao_diretoria()
    {
        $result = null;
        $data = Array();
        $args = Array();
        $resolucao = Array();

        $this->load->model('gestao/resolucao_diretoria_model');

        $args["cd_sumula"] = $this->input->post("cd_sumula", TRUE);

        $sumula_item = $this->input->post("sumula_item", TRUE);

        $this->sumula_model->carrega($result, $args);
        $row = $result->row_array();

        $resolucao["dt_resolucao_diretoria"]             = $row["dt_sumula"];
        $resolucao["nr_ata"]                             = $row["nr_sumula"];
        $resolucao["fl_situacao"]                        = "N";
        $resolucao["cd_resolucao_diretoria_abrangencia"] = 1;
        $resolucao["nr_ano"]                             = date('Y');
        $resolucao["cd_usuario"]                         = $this->session->userdata('codigo');

        $this->load->plugin('fpdf');

        $this->sumula_model->assinatura_presidente($result, $args);
        $assinatura = $result->row_array();

        foreach($sumula_item as $key => $item)
        {
            $args["cd_sumula_item"]      = $item;
            $resolucao["cd_sumula_item"] = $item;

            $this->sumula_model->carrega_sumula_item($result, $args);
            $row = $result->row_array();

            $resolucao["ds_resolucao_diretoria"] = $row["ds_pauta_sg_assunto"];
            $resolucao["area"]                   = $row["cd_gerencia"];

            $args["cd_resolucao_diretoria"] = $this->sumula_model->resolucao_diretoria($result, $resolucao);

            $this->resolucao_diretoria_model->carrega($result, $args);
            $row_resolucao = $result->row_array();
                
            $ob_pdf = new PDF();
            $ob_pdf->AddFont('segoeuil');
            $ob_pdf->AddFont('segoeuib');   
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
            $ob_pdf->SetFont('segoeuib', '', 12);
            $ob_pdf->MultiCell(190, 4.5, "RESOLUÇÃO DE DIRETORIA EXECUTIVA", 0, "C");

            $ob_pdf->MultiCell(190, 5.5, "Data: ".$row_resolucao["dt_resolucao_diretoria"], 0, "L");
            $ob_pdf->MultiCell(190, 5.5, "Nº: ".$row_resolucao["ano_numero"], 0, "L");
            $ob_pdf->MultiCell(190, 5.5, "Ata: ".$row_resolucao["nr_ata"], 0, "L");

            $ob_pdf->SetY($ob_pdf->GetY() + 5);

            $ob_pdf->MultiCell(190, 5.5, "Título: ".$row_resolucao["ds_resolucao_diretoria"], 0, "L");
            $ob_pdf->MultiCell(190, 5.5, "Abrangência: ".$row_resolucao["ds_resolucao_diretoria_abrangencia"], 0, "L");

            $ob_pdf->SetY($ob_pdf->GetY() + 10);

            $ob_pdf->MultiCell(190, 5.5, "1) Resolução:", 0, "L");
            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, 5.5, $row["ds_decisao"], 0, "L");

            $ob_pdf->SetY($ob_pdf->GetY() + 5);

            $ob_pdf->SetFont('segoeuib', '', 12);
            $ob_pdf->MultiCell(190, 5.5, "2) Vigência:", 0, "L");
            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, 5.5, $row_resolucao["dt_resolucao_diretoria"], 0, "L");

            $ob_pdf->SetY($ob_pdf->GetY() + 5);

            $ob_pdf->SetFont('segoeuib', '', 12);
            $ob_pdf->MultiCell(190, 5.5, "3) RDE:", 0, "L");
            $ob_pdf->SetY($ob_pdf->GetY() + 3);
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, 5.5, "ATA ".$row_resolucao["nr_ata"]." -  Diretoria Executiva", 0, "L");

            $ob_pdf->SetY($ob_pdf->GetY() + 30);

            #### PRESINDENTE ####
            $row['usuario_presidente']    = $assinatura["nome"];
            $row['assinatura_presidente'] = $assinatura["assinatura"];

            list($width, $height) = getimagesize('./img/assinatura/'.$row['assinatura_presidente']);   

            $nr_pe = $ob_pdf->GetStringWidth($row['usuario_presidente']);
            $nr_c_pe = $ob_pdf->GetStringWidth('Presidente');
            $coluna_pe    = abs(($nr_pe-$nr_c_pe)/2);
            $coluna_nm_pe = abs(($nr_pe-$nr_c_pe)/2);
            
            if($nr_pe > $nr_c_pe)
            {
                $coluna_nm_pe = 0;
                $nr_x = $nr_pe;
            }
            else
            {
                $coluna_pe = 0;
                $nr_x = $nr_c_pe;
            }
            
            $ob_pdf->SetX(25);
            
            $ob_pdf->Image('./img/assinatura/'.$row['assinatura_presidente'], 0, $ob_pdf->GetY() - 26, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
                      
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_pe, $ob_pdf->GetY(), $row['usuario_presidente']);
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_pe , $ob_pdf->GetY()+4, "Presidente");

            $args["arquivo"]      = md5(uniqid(rand(), true)).".pdf";
            $args["arquivo_nome"] = str_replace("/", "-", $row_resolucao["ano_numero"]).".pdf";

            $this->sumula_model->resolucao_diretoria_arquivo($result, $args);

            $ob_pdf->Output("up/resolucao_diretoria/".$args["arquivo"], 'F');
        }
    }


    public function getSumulaAssinatura()
    {
		if (gerencia_in(array('GC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_sumula'] = $this->input->post("cd_sumula", TRUE); 

			$this->sumula_model->getSumulaAssinatura($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula/cadastro_assinatura_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function getAtaAssinatura()
    {
		if (gerencia_in(array('GC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_sumula'] = $this->input->post("cd_sumula", TRUE); 

			$this->sumula_model->getAtaAssinatura($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula/cadastro_assinatura_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>