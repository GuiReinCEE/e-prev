<?php

class Produto_financeiro extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/produto_financeiro_model');
	}
	
	function index()
    {
	
        if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
		
			$this->produto_financeiro_model->origem($result, $args);
            $data['arr_origem'] = $result->result_array();
			
			$this->produto_financeiro_model->responsavel($result, $args);
            $data['arr_responsavel'] = $result->result_array();
			
			$this->produto_financeiro_model->revisor($result, $args);
            $data['arr_revisor'] = $result->result_array();
		
			$this->produto_financeiro_model->entidade_fornecedor($result, $args);
            $data['arr_entidade_fornecedor'] = $result->result_array();
		
			$this->load->view('atividade/produto_financeiro/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
		
    }	
	
	function listar()
    {
        if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
            $result = null;
            $data = Array();
            $args = Array();
			$data['collection'] = array();
			
			$args["ds_produto"] = $this->input->post("ds_produto", TRUE);
			$args["cd_produto_financeiro_origem"] = $this->input->post("cd_produto_financeiro_origem", TRUE);
			$args["cd_reuniao_sg_instituicao"] = $this->input->post("cd_reuniao_sg_instituicao", TRUE);
			$args["cd_usuario_responsavel"] = $this->input->post("cd_usuario_responsavel", TRUE);
			$args["cd_usuario_revisor"] = $this->input->post("cd_usuario_revisor", TRUE);
			$args["dt_recebido_ini"] = $this->input->post("dt_recebido_ini", TRUE);
			$args["dt_recebido_fim"] = $this->input->post("dt_recebido_fim", TRUE);
			$args["dt_cadastro_ini"] = $this->input->post("dt_cadastro_ini", TRUE);
			$args["dt_cadastro_fim"] = $this->input->post("dt_cadastro_fim", TRUE);
			$args["dt_conclusao_ini"] = $this->input->post("dt_conclusao_ini", TRUE);
			$args["dt_conclusao_fim"] = $this->input->post("dt_conclusao_fim", TRUE);
			
			manter_filtros($args);

            $this->produto_financeiro_model->listar($result, $args);
            $collection = $result->result_array();
			$i = 0;
            
			foreach($collection as $item)
			{
				$args['cd_produto_financeiro'] = $item['cd_produto_financeiro'];
				
				$data['collection'][$i]['cd_produto_financeiro'] = $item['cd_produto_financeiro'];
				$data['collection'][$i]['ds_produto'] = $item['ds_produto'];
				$data['collection'][$i]['dt_recebido'] = $item['dt_recebido'];
				$data['collection'][$i]['dt_atualizacao'] = $item['dt_atualizacao'];
				$data['collection'][$i]['dt_conclusao'] = $item['dt_conclusao'];
				$data['collection'][$i]['nr_concluido'] = $item['nr_concluido'];
				$data['collection'][$i]['responsavel'] = $item['responsavel'];
				$data['collection'][$i]['revisor'] = $item['revisor'];
				$data['collection'][$i]['ds_reuniao_sg_instituicao'] = $item['ds_reuniao_sg_instituicao'];
				
				$this->produto_financeiro_model->listar_etapas($result, $args);
				$data['collection'][$i]['etapas'] = $result->result_array();
				
				$i++;
			}
			
            $this->load->view('atividade/produto_financeiro/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_produto_financeiro = '')
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['cd_produto_financeiro'] = intval($cd_produto_financeiro);
					
			$this->produto_financeiro_model->usuarios_gin($result, $args);
            $data['arr_usuarios'] = $result->result_array();
			
			if (intval($args['cd_produto_financeiro']) == 0)
            {
                $data['row'] = Array(
                  'cd_produto_financeiro' => 0,
                  'dt_recebido' => '',
                  'ds_produto' => '',
				  'cd_produto_financeiro_origem' => '',
				  'cd_reuniao_sg_instituicao' => '',
				  'contato' => '',
				  'cd_usuario_responsavel' => '',
				  'cd_usuario_revisor' => '',
				  'cd_usuario_inclusao' => '',
				  'dt_conclusao' => ''

                );
            }
            else
            {
                $this->produto_financeiro_model->cadastro($result, $args);
                $data['row'] = $result->row_array();
            }
			
			$this->load->view('atividade/produto_financeiro/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar()
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args["cd_produto_financeiro"] = $this->input->post("cd_produto_financeiro", TRUE);
			$args["dt_recebido"] = $this->input->post("dt_recebido", TRUE);
			$args["dt_conclusao"] = $this->input->post("dt_conclusao", TRUE);
			$args["ds_produto"] = $this->input->post("ds_produto", TRUE);
			$args["cd_produto_financeiro_origem"] = $this->input->post("cd_produto_financeiro_origem", TRUE);
			$args["cd_reuniao_sg_instituicao"] = $this->input->post("cd_reuniao_sg_instituicao", TRUE);
			$args["cd_usuario_responsavel"] = $this->input->post("cd_usuario_responsavel", TRUE);
			$args["cd_usuario_revisor"] = $this->input->post("cd_usuario_revisor", TRUE);
			$args["contato"] = $this->input->post("contato", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$args["cd_produto_financeiro"] = $this->produto_financeiro_model->salvar($result, $args);
            redirect("atividade/produto_financeiro/etapas/".intval($args["cd_produto_financeiro"]), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function etapas($cd_produto_financeiro)
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['cd_produto_financeiro'] = intval($cd_produto_financeiro);
			
			$this->produto_financeiro_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			
			$this->load->view('atividade/produto_financeiro/etapas', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_etapas()
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args["cd_produto_financeiro"] = $this->input->post("cd_produto_financeiro", TRUE);
			$args["cd_produto_financeiro_etapa"] = $this->input->post("cd_produto_financeiro_etapa", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->produto_financeiro_model->salvar_etapas($result, $args);
			
			redirect("atividade/produto_financeiro/etapas/".intval($args["cd_produto_financeiro"]), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function listar_etapas()
    {
        if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
            $result = null;
            $data = Array();
            $args = Array();
			
			$args["cd_produto_financeiro"] = $this->input->post("cd_produto_financeiro", TRUE);

            $this->produto_financeiro_model->listar_etapas($result, $args);
            $data['collection'] = $result->result_array();
						
			$this->produto_financeiro_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
            
            $this->load->view('atividade/produto_financeiro/etapas_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar_etapas_status()
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
            $result = null;
            $data = Array();
            $args = Array();
			
			$args["produto_financeiro_etapa_status"] = $this->input->post("produto_financeiro_etapa_status", TRUE);
			$args["cd_produto_financeiro"] = $this->input->post("cd_produto_financeiro", TRUE);

			foreach($args["produto_financeiro_etapa_status"] as $item)
			{
				$args['cd_produto_financeiro_etapa_status'] = $item['cd_produto_financeiro_etapa_status'];
				$args['nr_peso'] = $item['nr_peso'];
				$args['nr_concluido'] = $item['nr_concluido'];
				$args['observacao'] = $item['observacao'];
				$args['nr_ordem'] = $item['nr_ordem'];
				
				$this->produto_financeiro_model->atualiza_etapas($result, $args);
			}

			redirect("atividade/produto_financeiro/etapas/".intval($args["cd_produto_financeiro"]), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_etapa($cd_produto_financeiro=0, $cd_produto_financeiro_etapa_status=0)
	{
		if((intval($cd_produto_financeiro_etapa_status) > 0) and (gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE'))
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args["cd_produto_financeiro_etapa_status"] = intval($cd_produto_financeiro_etapa_status);
			$args["cd_produto_financeiro"]              = intval($cd_produto_financeiro);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->produto_financeiro_model->excluir_etapa($result, $args);
			
			redirect("atividade/produto_financeiro/etapas/".intval($cd_produto_financeiro), "refresh");
			
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
		
	}
	
	function salvar_todas_etapas($cd_produto_financeiro)
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args["cd_produto_financeiro"] = $cd_produto_financeiro;
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->produto_financeiro_model->listar_todas_etapas($result, $args);
			$collection = $result->result_array();
			
			foreach($collection as $item)
			{
				$args['cd_produto_financeiro_etapa'] = $item['cd_produto_financeiro_etapa'];
				
				$this->produto_financeiro_model->salvar_etapas($result, $args);
			}
			
			redirect("atividade/produto_financeiro/etapas/".intval($args["cd_produto_financeiro"]), "refresh");
			
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function anexo($cd_produto_financeiro)
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['cd_produto_financeiro'] = intval($cd_produto_financeiro);
			
			$this->produto_financeiro_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('atividade/produto_financeiro/anexo', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function listar_anexos()
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['cd_produto_financeiro'] = $this->input->post("cd_produto_financeiro", TRUE);
			
			$this->produto_financeiro_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
			
			$this->produto_financeiro_model->listar_anexos($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/produto_financeiro/anexo_result', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar_anexo()
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['arquivo_nome'] = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo'] = $this->input->post("arquivo", TRUE);
			$args['cd_produto_financeiro'] = $this->input->post("cd_produto_financeiro", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->produto_financeiro_model->salvar_anexo($result, $args);
			
			redirect("atividade/produto_financeiro/anexo/".intval($args["cd_produto_financeiro"]), "refresh");
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_anexo($cd_produto_financeiro, $cd_produto_financeiro_anexo)
	{
		if(gerencia_in(array('GIN')) OR  $this->session->userdata('diretoria') == 'FIN' OR  $this->session->userdata('diretoria') == 'PRE')
        {
			$result = null;
            $data = Array();
            $args = Array();
			
			$args['cd_produto_financeiro'] = $cd_produto_financeiro;
            $args['cd_produto_financeiro_anexo'] = $cd_produto_financeiro_anexo;
			$args["cd_usuario"] = $this->session->userdata('codigo');

			$this->produto_financeiro_model->excluir_anexo($result, $args);
			
			redirect("atividade/produto_financeiro/anexo/".intval($args["cd_produto_financeiro"]), "refresh");
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}
?>