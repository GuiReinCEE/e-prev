<?php
class eleicoes_apuracao extends Controller
{
	var $fl_libera = false;

	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('eleicoes/cadastro_eleicoes_model');
		
		$this->fl_libera = false;
		
		if ($this->session->userdata('codigo') == 103) #ELTON
		{
			$this->fl_libera = true;
		}
		elseif ($this->session->userdata('codigo') == 170) #CRISTIANO JACOBSEN
		{
			$this->fl_libera = true;
		}
		elseif ($this->session->userdata('codigo') == 251) #LUCIANO RODRIGUEZ
		{
			$this->fl_libera = true;
		}
    }
	
	function index()
    {
        if ($this->fl_libera)
        {
            $args = Array();
            $data = Array();
            $result = null;
			
            $this->load->view('gestao/eleicoes_apuracao/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
		if ($this->fl_libera)
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);

			manter_filtros($args);

            $this->cadastro_eleicoes_model->listar($result, $args);

            $data['ar_eleicao'] = $result->result_array();

            $this->load->view('gestao/eleicoes_apuracao/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function apuracao($id_eleicao)
    {
        if ($this->fl_libera)
        {
            if(intval($id_eleicao) > 0)
			{
				$args = Array();
				$data = Array();
				$result = null;
							
				$args["id_eleicao"] = intval($id_eleicao);			
				
				#### CONSELHO DELIBERATIVO ####
				$this->cadastro_eleicoes_model->listarCandidatoDeliberativo($result, $args);
				$data['ar_deliberativo'] = $result->result_array();					
				
				#### CONSELHO FISCAL ####
				$this->cadastro_eleicoes_model->listarCandidatoFiscal($result, $args);
				$data['ar_fiscal'] = $result->result_array();					
							
				#### DIRETOR ####
				$this->cadastro_eleicoes_model->listarCandidatoDiretor($result, $args);
				$data['ar_diretor'] = $result->result_array();		

				#### CAP  ####
				$this->cadastro_eleicoes_model->listarCandidatoCAPCGTEE($result, $args);
				$data['ar_cap_aessul'] = $result->result_array();	
				
				#### ELEICAO ####			
				$this->cadastro_eleicoes_model->listar($result, $args);	
                $data['row'] = $result->row_array();

				$this->load->view('gestao/eleicoes_apuracao/apuracao', $data);
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}			
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
	function apuracao_abrir()
	{
		if ($this->fl_libera)
		{
            if(intval($this->input->post("id_eleicao",TRUE)) > 0)
			{
				$args   = Array();
				$data   = Array();
				$result = null;
							
				$args["id_eleicao"]      = intval($this->input->post("id_eleicao",TRUE));	
				$args["qt_kit_recebido"] = intval($this->input->post("qt_kit_recebido",TRUE));
				$args["cd_usuario"]      = usuario_id();				
							
				#echo "<PRE>".print_r($args,true)."</PRE>";exit;
							
				$this->cadastro_eleicoes_model->apuracao_abrir($result, $args);				
							
				redirect("gestao/eleicoes_apuracao/apuracao/".$args["id_eleicao"], "refresh");	
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}				
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function apuracao_encerrar($id_eleicao)
	{
		if ($this->fl_libera)
		{
            if(intval($id_eleicao) > 0)
			{
				$args   = Array();
				$data   = Array();
				$result = null;
							
				$args["id_eleicao"]      = intval($id_eleicao);	
				$args["cd_usuario"]      = usuario_id();				
							
				#echo "<PRE>".print_r($args,true)."</PRE>";exit;
							
				$this->cadastro_eleicoes_model->apuracao_encerrar($result, $args);				
							
				redirect("gestao/eleicoes_apuracao/apuracao/".$args["id_eleicao"], "refresh");	
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}				
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function apuracao_salvar()
	{
		if ($this->fl_libera)
		{
            if(intval($this->input->post("id_eleicao",TRUE)) > 0)
			{
				$args   = Array();
				$data   = Array();
				$result = null;

				$args["id_eleicao"]        = intval($this->input->post("id_eleicao",TRUE));	
				$args["qt_total_invalido"] = intval($this->input->post("qt_total_invalido",TRUE));	
				$args["tp_voto"]           = $this->input->post("tp_voto",TRUE);
				$args["ar_candidato"]      = $this->input->post("ar_candidato",TRUE);
				$args["cd_usuario"]        = usuario_id();		
				
				$ar_retorno = $this->cadastro_eleicoes_model->apuracao_salvar($result, $args);

				echo "
						<script>
							alert('DADOS LANÇADOS".($ar_retorno["cd_lote_voto_invalido"] > 0 ? "\\n\\n- Número Lote (Kits Inválidos) => ".$ar_retorno["cd_lote_voto_invalido"]."              " : "").($ar_retorno["cd_lote_voto"] > 0 ? "\\n\\n- Número Lote (Votos Válidos) => ".$ar_retorno["cd_lote_voto"]."              " : "")."\\n\\n');
							document.location.href = '".site_url()."/gestao/eleicoes_apuracao/apuracao/".$args["id_eleicao"]."';
						</script>
					 ";
				exit;								
		
				#redirect("gestao/eleicoes_apuracao/apuracao/".$args["id_eleicao"], "refresh");	
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}				
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function lote($id_eleicao)
	{
		if ($this->fl_libera)
		{
			if(intval($id_eleicao) > 0)
			{
				$args   = Array();
				$data   = Array();
				$result = null;
				
				$data['id_eleicao'] = $id_eleicao;
				$args['id_eleicao'] = $id_eleicao;
				
				#### ELEICAO ####			
				$this->cadastro_eleicoes_model->listar($result, $args);	
                $data['row'] = $result->row_array();
				
				$this->cadastro_eleicoes_model->lotes($result, $args);
				$data['collection'] = $result->result_array();		
				
				$this->load->view('gestao/eleicoes_apuracao/lote', $data);
				
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	
	function cancelar_lote($id_eleicao, $cd_lote)
	{
		if ($this->fl_libera)
		{
			if(intval($id_eleicao) > 0)
			{
				$args   = Array();
				$data   = Array();
				$result = null;
				
				$args['id_eleicao'] = $id_eleicao;
				$args['cd_lote'] = $cd_lote;
				$args["cd_usuario"] = usuario_id();		
				
				$this->cadastro_eleicoes_model->cancelar_lote($result, $args);
				
				redirect("gestao/eleicoes_apuracao/lote/".$id_eleicao, "refresh");	
			}
			else
			{
				exibir_mensagem("ERRO ELEIÇÃO NÃO ENCOTRADA");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>