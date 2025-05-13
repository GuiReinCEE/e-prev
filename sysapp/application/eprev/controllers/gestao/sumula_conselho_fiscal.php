<?php

class sumula_conselho_fiscal extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('gestao/sumula_conselho_fiscal_model');
    }

    public function intranet()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula_conselho_fiscal'] = '';   
        $args['descricao']                 = '';   
        $args['dt_ini']                    = '';   
        $args['dt_fim']                    = '';   
        $args['dt_div_ini']                = '';   
        $args['dt_div_fim']                = '';   
        $args['fl_respondido']             = '';   
        $args['cd_resposta']               = '';   
        $args['dt_resposta_ini']           = '';   
        $args['dt_resposta_fim']           = '';   
            
        manter_filtros($args);

        $this->sumula_conselho_fiscal_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_conselho_fiscal/intranet', $data);
    }
	
	public function get_usuarios()
    {		
		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);
		$cd_usuario  = $this->input->post('cd_usuario', TRUE);
		
		$usuarios = $this->sumula_conselho_fiscal_model->get_usuarios($cd_gerencia, $cd_usuario);
		
		echo json_encode($usuarios);
    }

    public function validar_nr_sumula()
    {
        $nr_sumula_conselho_fiscal = $this->input->post("nr_sumula_conselho_fiscal", TRUE);

        $cd_sumula_conselho_fiscal = $this->input->post("cd_sumula_conselho_fiscal", TRUE);

        $data = $this->sumula_conselho_fiscal_model->valida_numero_sumula($cd_sumula_conselho_fiscal, $nr_sumula_conselho_fiscal);

        if(intval($cd_sumula_conselho_fiscal) == 0 AND intval($data['valida']) == 0)
        {
            $row = $this->sumula_conselho_fiscal_model->valida_pauta($nr_sumula_conselho_fiscal);

            if(intval($row['tl_valida_pauta']) == 0)
            {
                $data['valida'] = 2;
            }
        }

        echo json_encode($data);
    }
	
	public function index()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $this->load->view('gestao/sumula_conselho_fiscal/index.php', $data);
    }
	
	public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_sumula_conselho_fiscal'] = $this->input->post("nr_sumula_conselho_fiscal", TRUE);   
        $args['dt_ini']                    = $this->input->post("dt_ini", TRUE);   
        $args['dt_fim']                    = $this->input->post("dt_fim", TRUE);   
        $args['dt_div_ini']                = $this->input->post("dt_div_ini", TRUE);   
        $args['dt_div_fim']                = $this->input->post("dt_div_fim", TRUE);   
        $args['fl_respondido']             = $this->input->post("fl_respondido", TRUE);   
        $args['dt_resposta_ini']           = $this->input->post("dt_resposta_ini", TRUE);   
        $args['dt_resposta_fim']           = $this->input->post("dt_resposta_fim", TRUE); 
        $args['descricao']                 = '';     
            
        manter_filtros($args);

        $this->sumula_conselho_fiscal_model->listar($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_conselho_fiscal/partial_result', $data);
    }
	
	function cadastro($cd_sumula_conselho_fiscal = 0)
    {
        if (gerencia_in(array('GC', 'DE')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
            $args['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);

            $this->sumula_conselho_fiscal_model->total_enviados($result, $args);

            $data['total_enviados'] = $result->row_array();

            $data['fl_editar'] = true;

            if(intval( $data['total_enviados']['tl']) > 0)
            {
                $data['fl_editar'] = false;
            }

            if ($cd_sumula_conselho_fiscal == 0)
            {
                $row = $this->sumula_conselho_fiscal_model->carrega_numero_cadastro();
                
                $data['row'] = Array(
                  'cd_sumula_conselho_fiscal' => 0,
                  'nr_sumula_conselho_fiscal' => (count($row) > 0 ? $row['nr_sumula'] : ''),
                  'dt_sumula_conselho_fiscal' => '',
                  'dt_divulgacao'             => '',
                  'arquivo'                   => '',
                  'arquivo_nome'              => ''
                );
            }
            else
            {
                $this->sumula_conselho_fiscal_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('gestao/sumula_conselho_fiscal/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $this->load->model("gestao/pauta_sg_model");

            $args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
            $args['nr_sumula_conselho_fiscal'] = $this->input->post("nr_sumula_conselho_fiscal", TRUE);
            $args['dt_sumula_conselho_fiscal'] = $this->input->post("dt_sumula_conselho_fiscal", TRUE);
            $args['dt_divulgacao']             = $this->input->post("dt_divulgacao", TRUE);
            $args['arquivo_nome']              = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo']                   = $this->input->post("arquivo", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $cd_sumula_conselho_fiscal = $this->sumula_conselho_fiscal_model->salvar($result, $args);

            $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;

            $this->pauta_sg_model->assunto_sumula_cf($result, $args);

            redirect("gestao/sumula_conselho_fiscal/responsabilidade/" . $cd_sumula_conselho_fiscal, "refresh");
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

            $args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_ata", TRUE);
            $args['arquivo_ata_nome']   = $this->input->post("arquivo_ata_nome", TRUE);
            $args['arquivo_ata']        = $this->input->post("arquivo_ata", TRUE);
            $args['cd_usuario']         = $this->session->userdata('codigo');
			
            $this->sumula_conselho_fiscal_model->salvarAta($result, $args);

			#### INTEGRACAO COM PYDIO ####
			$this->sumula_conselho_fiscal_model->carrega($result, $args);
			$row = $result->row_array();

			$dir_aprovado = str_replace("FISCAL","DOCUMENTOS_APROVADOS",$row['integracao_arq']);

			copy('../cieprev/up/sumula_conselho_fiscal/'.$row['arquivo_ata'], $row['integracao_arq'].'/acf-'.$row['nr_sumula_conselho_fiscal'].'.pdf');
			copy('../cieprev/up/sumula_conselho_fiscal/'.$row['arquivo_ata'], $dir_aprovado.'/acf-'.$row['nr_sumula_conselho_fiscal'].'.pdf');

            list($d, $m, $a) = explode('/', $row['dt_sumula_conselho_fiscal']);
/*
            $caminho_previc = '../eletroceee/pydio/data/PREVIC/Atas CF/'.$a;

            if(!is_dir($caminho_previc))
            {
                mkdir($caminho_previc, 0777);
            }

            $caminho_previc .= '/'.$row['nr_sumula_conselho_fiscal'];

            if(!is_dir($caminho_previc))
            {
                mkdir($caminho_previc, 0777);
            }

            copy('../cieprev/up/sumula_conselho_fiscal/'.$row['arquivo_ata'], $caminho_previc.'/ade-'.$row['nr_sumula_conselho_fiscal'].'.pdf');
*/
			redirect("gestao/sumula_conselho_fiscal/cadastro/".$args['cd_sumula_conselho_fiscal'], "refresh");
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

            $args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
            $args['dt_publicacao_libera']      = $this->input->post("dt_publicacao_libera", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->sumula_conselho_fiscal_model->publicar($result, $args);
            $this->assunto_aprovado($args['cd_sumula_conselho_fiscal']);
        }
        else
        {
            echo "ACESSO NÃO PERMITIDO";
        }
    } 

    public function assunto_aprovado($cd_sumula_conselho_fiscal)
    {
        $this->load->plugin('encoding_pi');
        
        $result = null;
        $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;

        $this->sumula_conselho_fiscal_model->carrega($result, $args);
        $row = $result->row_array();
		
		$dir_aprovado = str_replace("FISCAL","DOCUMENTOS_APROVADOS",$row['integracao_arq']);

        copy('../cieprev/up/sumula_conselho_fiscal/'.$row['arquivo'], $row['integracao_arq'].'/Sumula_'.$row['nr_sumula_conselho_fiscal'].'.pdf');
        copy('../cieprev/up/sumula_conselho_fiscal/'.$row['arquivo'], $dir_aprovado.'/Sumula_'.$row['nr_sumula_conselho_fiscal'].'.pdf');

        $pauta_sg_assunto = $this->sumula_conselho_fiscal_model->assunto_aprovado($cd_sumula_conselho_fiscal);

        foreach ($pauta_sg_assunto as $key => $item) 
        {
            foreach ($this->sumula_conselho_fiscal_model->assunto_aprovado_anexo($item['cd_pauta_sg_assunto']) as $key2 => $item2) 
            {
                copy('../cieprev/up/pauta/'.$item2['arquivo'], $dir_aprovado.'/documentos/'.$item['nr_item_sumula'].' - '.fixUTF8($item2['arquivo_nome']));
            }
        }
    }       
	
	function responsabilidade($cd_sumula_conselho_fiscal, $cd_sumula_conselho_fiscal_item = 0)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

			$data['enviados'] = array();
			$data['nao_enviados'] = array();
			
			$data['usuarios'] = array();
            
            $data['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
            $args['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
			$data['cd_sumula_conselho_fiscal_item'] = intval($cd_sumula_conselho_fiscal_item);
            $args['cd_sumula_conselho_fiscal_item'] = intval($cd_sumula_conselho_fiscal_item);

			$this->sumula_conselho_fiscal_model->gericias_cadastradas($result, $args);
            $data['arr_gerencia_cad'] = $result->result_array();
			
            $this->sumula_conselho_fiscal_model->total_enviados($result, $args);
            $data['total_enviados'] = $result->result_array();

            $this->sumula_conselho_fiscal_model->carrega($result, $args);
            $data['row'] = $result->row_array();
			
			$data['nao_enviados'] = intval($data['total_enviados'][0]['tl']);
			
			if(isset($data['total_enviados'][1]))
			{
				$data['enviados'] = intval($data['total_enviados'][1]['tl']);
			}
			
			if(intval($cd_sumula_conselho_fiscal_item > 0))
			{
				$this->sumula_conselho_fiscal_model->carrega_sumula_item($result, $args);
				$data['row_item'] = $result->row_array();
			}
			else
			{
				$data['row_item'] = array(
					'cd_diretoria' 				     => '',
					'cd_gerencia' 				     => '',
					'nr_sumula_conselho_fiscal_item' => '',
					'descricao' 					 => '',
					'cd_responsavel' 				 => '',
					'cd_divisao_responsavel'		 => '',
					'cd_substituto'					 => '',
					'cd_divisao_substituto' 		 => ''				
				);
			}
			
            $this->load->view('gestao/sumula_conselho_fiscal/responsabilidade', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function email_gerentes($cd_sumula_conselho_fiscal = 0)
	{
		if (gerencia_in(array('GC')))
        {
			$args = Array();
            $data = Array();
            $result = null;
		
			$args['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
			
			$this->sumula_conselho_fiscal_model->carrega($result, $args);
            $row = $result->row_array();
			
			$args['nr_sumula_conselho_fiscal'] = $row['nr_sumula_conselho_fiscal'];
			$args['arquivo']                   = $row['arquivo'];
			
			$this->sumula_conselho_fiscal_model->email_gerentes($result, $args);
			
			redirect("gestao/sumula_conselho_fiscal/cadastro/" . $args['cd_sumula_conselho_fiscal'], "refresh");
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

            $args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
            $args['fl_recebido']               = $this->input->post("fl_recebido", TRUE);
            $args['cd_gerencia']               = $this->input->post("cd_gerencia", TRUE);
            $args['cd_diretoria']              = $this->input->post("cd_diretoria", TRUE);
			
            $this->sumula_conselho_fiscal_model->lista_itens($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula_conselho_fiscal/responsabilidade_result', $data);
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

            $args['cd_sumula_conselho_fiscal']      = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
            $args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);
            $args['cd_gerencia']                    = $this->input->post("cd_gerencia", TRUE);
            $args['cd_diretoria']                   = $this->input->post("cd_diretoria", TRUE);
            $args['nr_sumula_conselho_fiscal_item'] = $this->input->post("nr_sumula_conselho_fiscal_item", TRUE);
            $args['descricao']                      = $this->input->post("descricao", TRUE);
            $args['cd_usuario_responsavel']         = $this->input->post("cd_usuario_responsavel", TRUE);
            $args['cd_usuario_substituto']          = $this->input->post("cd_usuario_substituto", TRUE);
            $args['cd_usuario']                     = $this->session->userdata('codigo');

            $this->sumula_conselho_fiscal_model->salvar_item($result, $args);

            redirect("gestao/sumula_conselho_fiscal/responsabilidade/" . $args['cd_sumula_conselho_fiscal'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function carrega_sumula_item()
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);

            $this->sumula_conselho_fiscal_model->carrega_sumula_item($result, $args);
            $row = $result->row_array();

            $row = array_map("arrayToUTF8", $row);
            echo json_encode($row);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function excluir_sumula($cd_sumula_conselho_fiscal, $cd_sumula_conselho_fiscal_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_conselho_fiscal_item'] = $cd_sumula_conselho_fiscal_item;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->sumula_conselho_fiscal_model->excluir_sumula_item($result, $args);

            redirect("gestao/sumula_conselho_fiscal/responsabilidade/" . $cd_sumula_conselho_fiscal, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function enviar_todos($cd_sumula_conselho_fiscal)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;

            $this->sumula_conselho_fiscal_model->enviar_todos($result, $args);

            redirect("gestao/sumula_conselho_fiscal/cadastro/" . $cd_sumula_conselho_fiscal, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function enviar($cd_sumula_conselho_fiscal, $cd_sumula_conselho_fiscal_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_sumula_conselho_fiscal']      = $cd_sumula_conselho_fiscal;
            $args['cd_sumula_conselho_fiscal_item'] = $cd_sumula_conselho_fiscal_item;

            $this->sumula_conselho_fiscal_model->enviar($result, $args);

            redirect("gestao/sumula_conselho_fiscal/responsabilidade/" . $cd_sumula_conselho_fiscal, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function acompanhamento($cd_sumula_conselho_fiscal)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			$data['arr_item'] = array();
			
            $data['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
            $args['cd_sumula_conselho_fiscal'] = intval($cd_sumula_conselho_fiscal);
			
			$this->sumula_conselho_fiscal_model->item_acompanhamento($result, $args);
            $arr = $result->result_array();
			
			$i = 0;
			
			foreach($arr as $item)
			{
				$data['arr_item'][$i] = array('value' => $item['cd_sumula_conselho_fiscal_item'], 'text' => $item['nr_sumula_conselho_fiscal_item'].' - '.substr(trim($item['descricao']),0,100));
				
				$i++;
			}
			
            $this->load->view('gestao/sumula_conselho_fiscal/acompanhamento', $data);
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
			
			$args['cd_sumula_conselho_fiscal']   = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
			
            $this->sumula_conselho_fiscal_model->lista_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/sumula_conselho_fiscal/acompanhamento_result', $data);
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
			
			$args['cd_sumula_conselho_fiscal']      = $this->input->post("cd_sumula_conselho_fiscal", TRUE);
			$args['descricao']                      = $this->input->post("descricao", TRUE);
			$args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);
			$args['cd_usuario']                     = $this->session->userdata('codigo');
			
			$this->sumula_conselho_fiscal_model->salvar_acompanhamento($result, $args);
			
			redirect("gestao/sumula_conselho_fiscal/acompanhamento/" . $args['cd_sumula_conselho_fiscal'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function resposta($cd_sumula_conselho_fiscal_item)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_sumula_conselho_fiscal_item'] = $cd_sumula_conselho_fiscal_item;

        $this->sumula_conselho_fiscal_model->carrega_sumula_item_resposta($result, $args);
        $data['row'] = $result->row_array();
		
		$cd_usuario  = intval($this->session->userdata('codigo'));
		$diretoria   = $this->session->userdata('diretoria');
		$cd_gerencia = trim($this->session->userdata('divisao'));
		$gerente     = trim($this->session->userdata('tipo'));
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_conselho_fiscal_model->get_usuario_diretor($result, $args);
        $row = $result->row_array();
        
		# Habilitado para Gerente da Gerência destino entrar na resposta.
        if (
            ($cd_usuario == $data['row']['cd_responsavel']) 
            OR 
            ($cd_usuario == $data['row']['cd_substituto']) 
            OR ($diretoria == $data['row']['cd_diretoria']) 
            OR (
                ($cd_gerencia == $data['row']['cd_gerencia']) AND ($gerente == 'G')
               )
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_resposanvel'])
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_substituto'])
           )
        {
            $this->load->view('gestao/sumula_conselho_fiscal/resposta', $data);
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

        $args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);
        $args['descricao']                      = $this->input->post("descricao", TRUE);
        $args['cd_usuario']                     = $this->session->userdata('codigo');

        $this->sumula_conselho_fiscal_model->salvar_resposta($result, $args);

        redirect("gestao/sumula_conselho_fiscal/resposta/" . $args['cd_sumula_conselho_fiscal_item'], "refresh");
    }
	
	function mudar_responsavel($cd_sumula_conselho_fiscal_item, $cd_responsavel)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_sumula_conselho_fiscal_item'] = $cd_sumula_conselho_fiscal_item;
        $args['cd_responsavel']                 = $cd_responsavel;
		
		$this->sumula_conselho_fiscal_model->mudar_responsavel($result, $args);
		
		redirect("gestao/sumula_conselho_fiscal/minhas", "refresh");
	}
	
	function minhas()
    {
        $this->load->view('gestao/sumula_conselho_fiscal/minhas.php');
    }
	
	function minhas_listar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_usuario']                = $this->session->userdata('codigo');
		$args['diretoria']                 = $this->session->userdata('diretoria');
        $args['fl_respondido']             = $this->input->post("fl_respondido", TRUE);
        $args['dt_ini_envio']              = $this->input->post("dt_ini_envio", TRUE);
        $args['dt_fim_envio']              = $this->input->post("dt_fim_envio", TRUE);
        $args['dt_ini_resp']               = $this->input->post("dt_ini_resp", TRUE);
        $args['dt_fim_resp']               = $this->input->post("dt_fim_resp", TRUE);
        $args['nr_sumula_conselho_fiscal'] = $this->input->post("nr_sumula_conselho_fiscal", TRUE);

        manter_filtros($args);

        $this->sumula_conselho_fiscal_model->carrega_minhas($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_conselho_fiscal/minhas_result', $data);
    }
	
	function pdf($cd_sumula_conselho_fiscal)
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;
        $args['fl_recebido']               = '';
		$args['cd_gerencia']               = '';
		$args['cd_resposta']               = '';
		$args['cd_diretoria']              = '';
        
        $this->sumula_conselho_fiscal_model->carrega($result, $args);
        $row = $result->row_array();
        
        $this->sumula_conselho_fiscal_model->lista_itens($result, $args);
        $arr = $result->result_array();
        
        $this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Súmula do Conselho - ". $row['nr_sumula_conselho_fiscal'];
        
        $ob_pdf->AddPage();
        
        $ob_pdf->SetFont('Arial', '', 12);
        $ob_pdf->MultiCell(0, 5, "Dt Súmula: ".$row['dt_sumula_conselho_fiscal']."
Dt Divulgação: ".$row['dt_divulgacao']."
Link para a súmula: ", '0', 'L');
        
        $ob_pdf->SetFont('Arial', 'U', 12);
        $ob_pdf->SetTextColor(50,50,220);
        $ob_pdf->Write(5,base_url().'up/sumula_conselho_fiscal/' . $row['arquivo_nome'], base_url().'up/sumula_conselho_fiscal/' . $row['arquivo']);
        $ob_pdf->SetTextColor(0,0,0);
        $ob_pdf->SetY($ob_pdf->GetY()+10);
        
        foreach ($arr as $item)
        {
			$ob_pdf->SetWidths( array(45, 145) );
			$ob_pdf->SetAligns( array('L','L') );
		
			$args['cd_sumula_conselho_fiscal_item'] =  $item['cd_sumula_conselho_fiscal_item'];
			
			$this->sumula_conselho_fiscal_model->acompanhamento_item($result, $args);
			$arr_acompanhamento = $result->result_array();
			
			$this->sumula_conselho_fiscal_model->listar_anexo($result, $args);
			$arr_anexo = $result->result_array();
		
            $resposta = $item['descricao'];
                
            $ob_pdf->SetFont('Arial', 'B', 13);
            
            $ob_pdf->MultiCell(0, 7, "Item: ". $item['nr_sumula_conselho_fiscal_item'], '0', 'L');
            
            $ob_pdf->SetFont('Arial', '', 12);
            
            $ob_pdf->Row(array("Diretoria: ", $item['ds_diretoria']));	
            $ob_pdf->Row(array("Gerência: ", $item['gerencia']));	
            $ob_pdf->Row(array("Responsável: ", $item['responsavel']));
            $ob_pdf->Row(array("Substituto: ", $item['substituto']));
            $ob_pdf->Row(array("Descrição: ", $item['descricao_sumula']));
            $ob_pdf->Row(array("Resposta: ", $item['descricao']));
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
				$ob_pdf->SetFont('Arial', 'U', 12);
				$ob_pdf->SetTextColor(50,50,220);
				$ob_pdf->Write(5,base_url().'up/sumula_conselho_fiscal/' . $item_anexo['arquivo_nome'], base_url().'up/sumula_conselho_fiscal/' . $item_anexo['arquivo']);
				$ob_pdf->SetTextColor(0,0,0);
				$ob_pdf->SetY($ob_pdf->GetY()+4);
			}
			
			if(count($arr_acompanhamento) > 0)
			{
				$ob_pdf->SetY($ob_pdf->GetY()+5);
				
				$ob_pdf->SetWidths(array(35,100,55));
				$ob_pdf->SetAligns(array('C', 'C', 'C'));
				$ob_pdf->SetFont('Arial','',10);
				$ob_pdf->Row(array("Data ", "Acompanhamento", "Usuário"));
				$ob_pdf->SetAligns(array('C', 'L', 'L'));
								
				foreach ($arr_acompanhamento as $item2)
				{
					$ob_pdf->Row(array($item2['dt_inclusao'], $item2['descricao'], $item2['nome']));
				}
			}
			 
            $ob_pdf->SetY($ob_pdf->GetY()+5);
        }
		
		$this->sumula_conselho_fiscal_model->acompanhamento_sem_item($result, $args);
		$arr = $result->result_array();
		
		if(count($arr) > 0)
		{		
			$ob_pdf->SetFont('Arial', 'B', 13);
			
			$ob_pdf->MultiCell(0, 7, "Acompanhamentos da Súmula", '0', 'L');
			
			$ob_pdf->SetFont('Arial', '', 10);
			
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
	
	function anexo($cd_sumula_conselho_fiscal_item)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['arr_item'] = array();

		$args['cd_sumula_conselho_fiscal_item'] = intval($cd_sumula_conselho_fiscal_item);
		
		$this->sumula_conselho_fiscal_model->carrega_sumula_item_resposta($result, $args);
        $data['row'] = $result->row_array();
		
		$cd_usuario  = intval($this->session->userdata('codigo'));
		$diretoria   = $this->session->userdata('diretoria');
		$cd_gerencia = trim($this->session->userdata('divisao'));
		$gerente     = trim($this->session->userdata('tipo'));
        
        $args['cd_responsavel'] = $data['row']['cd_responsavel'];
        $args['cd_substituto']  = $data['row']['cd_substituto'];
        
        $this->sumula_conselho_fiscal_model->get_usuario_diretor($result, $args);
        $row = $result->row_array();
        
		# Habilitado para Gerente da Gerência destino entrar na resposta.
        if (
            ($cd_usuario == $data['row']['cd_responsavel']) 
            OR 
            ($cd_usuario == $data['row']['cd_substituto']) 
            OR ($diretoria == $data['row']['cd_diretoria']) 
            OR (
                ($cd_gerencia == $data['row']['cd_gerencia']) AND ($gerente == 'G')
               )
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_resposanvel'])
            OR
            ($this->session->userdata('codigo') == $row['cd_usuario_diretor_substituto'])
           )
        {
            $this->load->view('gestao/sumula_conselho_fiscal/anexo', $data);
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
		
		$args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);
		
		$this->sumula_conselho_fiscal_model->listar_anexo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('gestao/sumula_conselho_fiscal/anexo_result', $data);
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
				
				$args['cd_sumula_conselho_fiscal_item'] = $this->input->post("cd_sumula_conselho_fiscal_item", TRUE);
				$args["cd_usuario"]                     = $this->session->userdata('codigo');
				
				$this->sumula_conselho_fiscal_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("gestao/sumula_conselho_fiscal/anexo/".intval($args["cd_sumula_conselho_fiscal_item"]), "refresh");
	}
	
	function excluir_anexo($cd_sumula_conselho_fiscal_item, $cd_sumula_conselho_fiscal_item_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_sumula_conselho_fiscal_item']       = $cd_sumula_conselho_fiscal_item;
		$args['cd_sumula_conselho_fiscal_item_anexo'] = $cd_sumula_conselho_fiscal_item_anexo;
		$args["cd_usuario"]                           = $this->session->userdata('codigo');

		$this->sumula_conselho_fiscal_model->excluir_anexo($result, $args);
		
		redirect("gestao/sumula_conselho_fiscal/anexo/".intval($args["cd_sumula_conselho_fiscal_item"]), "refresh");
	}

    public function consulta()
    {
        $args = array();
        $data = array();
        $result = null;

        $this->load->view('gestao/sumula_conselho_fiscal/consulta', $data);
    }

    public function consulta_listar()
    {
        $args['nr_sumula_conselho_fiscal'] = $this->input->post("nr_sumula_conselho_fiscal", TRUE);   
        $args['descricao']                 = $this->input->post('descricao', TRUE);   
        $args['dt_ini']                    = $this->input->post("dt_ini", TRUE);   
        $args['dt_fim']                    = $this->input->post("dt_fim", TRUE);   
        $args['dt_div_ini']                = $this->input->post("dt_div_ini", TRUE);   
        $args['dt_div_fim']                = $this->input->post("dt_div_fim", TRUE);   
        $args['fl_respondido']             = '';   
        $args['cd_resposta']               = '';   
        $args['dt_resposta_ini']           = '';   
        $args['dt_resposta_fim']           = '';   

        manter_filtros($args);

        $this->sumula_conselho_fiscal_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/sumula_conselho_fiscal/consulta_result', $data);
    }

    function enviar_fundacao($cd_sumula_conselho_fiscal)
    {
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;
            $args['cd_usuario']                = $this->session->userdata('codigo');
                    
            $this->sumula_conselho_fiscal_model->carrega($result, $args);
            $row = $result->row_array();
            
            $args['assunto'] = 'Súmula Conselho Fiscal nº '.$row['nr_sumula_conselho_fiscal'];
            $args['texto']   = 'Está disponível a SÚMULA '.$row['nr_sumula_conselho_fiscal'].' referente a Reunião do Conselho Fiscal ocorrida em '.$row['dt_sumula_conselho_fiscal'].'.

            Clique no link abaixo para acessar:
            '.site_url('gestao/sumula_conselho_fiscal/sumula_pdf')."/".$row['cd_sumula_conselho_fiscal'].'

            ';

            $this->sumula_conselho_fiscal_model->enviar_fundacao($result, $args);

            redirect("gestao/sumula_conselho_fiscal/cadastro/".$cd_sumula_conselho_fiscal, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function sumula_pdf($cd_sumula_conselho_fiscal)
    {
        $args['cd_sumula_conselho_fiscal'] = $cd_sumula_conselho_fiscal;    
        
        $this->sumula_conselho_fiscal_model->carrega($result, $args);
        $ar_sumula = $result->row_array();      
        
        $ob_arq = './up/sumula_conselho_fiscal/'.$ar_sumula['arquivo'];
        $ds_arq = $ar_sumula['arquivo_nome'];

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="'.$ds_arq.'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($ob_arq));
        header('Accept-Ranges: bytes'); 

        readfile($ob_arq);  
    }
	
    public function getSumulaAssinatura()
    {
		if (gerencia_in(array('GC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_conselho_fiscal", TRUE); 

			$this->sumula_conselho_fiscal_model->getSumulaAssinatura($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula_conselho_fiscal/cadastro_assinatura_result', $data);
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
			
			$args['cd_sumula_conselho_fiscal'] = $this->input->post("cd_sumula_conselho_fiscal", TRUE); 

			$this->sumula_conselho_fiscal_model->getAtaAssinatura($result, $args);

			$data['collection'] = $result->result_array();

			$this->load->view('gestao/sumula_conselho_fiscal/cadastro_assinatura_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }		
}

?>