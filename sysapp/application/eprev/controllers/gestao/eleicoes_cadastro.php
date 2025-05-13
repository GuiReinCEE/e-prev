<?php
class Eleicoes_cadastro extends Controller
{
	var $fl_libera = false;

	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('eleicoes/Cadastro_eleicoes_model');
		
		$this->fl_libera = ($this->session->userdata('codigo') == 170 ? true : false);
    }
	
	function index()
    {
        if (gerencia_in(array('GI')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$this->Cadastro_eleicoes_model->comboEleicoes($result, $args);
            $data['ar_eleicoes'] = $result->result_array();			

            $this->load->view('gestao/eleicoes_cadastro/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
        if (gerencia_in(array('GI')))
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);

			manter_filtros($args);

            $this->Cadastro_eleicoes_model->listar($result, $args);

            $data['ar_eleicao'] = $result->result_array();

            $this->load->view('gestao/eleicoes_cadastro/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function geracao($id_eleicao)
    {
        if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {
            if(intval($id_eleicao) > 0)
			{
				$args = Array();
				$data = Array();
				$result = null;
							
				$args["id_eleicao"] = intval($id_eleicao);			
							
				$this->Cadastro_eleicoes_model->listar($result, $args);	
                $data['row'] = $result->row_array();

				$this->load->view('gestao/eleicoes_cadastro/geracao', $data);
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
	
    function importaOracle()
    {
        if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {        
			$data   = Array();
			$args   = Array();
			$result = null;	
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);
			
			$this->Cadastro_eleicoes_model->importaOracle($result, $args);

			redirect("gestao/eleicoes_cadastro/geracao/".$args["id_eleicao"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }

	function geraNumeroControle()
    {
		if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {        
			$data   = Array();
			$args   = Array();
			$result = null;	
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);
			
			$this->Cadastro_eleicoes_model->geraNumeroControle($result, $args);

			redirect("gestao/eleicoes_cadastro/geracao/".$args["id_eleicao"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
	
	function geraCodigoBarra()
    {
        if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {        
			$data   = Array();
			$args   = Array();
			$result = null;	
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);
			
			$this->Cadastro_eleicoes_model->geraCodigoBarra($result, $args);

			redirect("gestao/eleicoes_cadastro/geracao/".$args["id_eleicao"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
	
	function atualizaOracle()
    {
		if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {        
			$data   = Array();
			$args   = Array();
			$result = null;	
			
			$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);
			
			$this->Cadastro_eleicoes_model->atualizaOracle($result, $args);

			redirect("gestao/eleicoes_cadastro/geracao/".$args["id_eleicao"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }	

	function kit($id_eleicao)
    {
        if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {
            if(intval($id_eleicao) > 0)
			{
				$args = Array();
				$data = Array();
				$result = null;
							
				$args["id_eleicao"] = intval($id_eleicao);			
							
				$this->Cadastro_eleicoes_model->listar($result, $args);	
                $data['row'] = $result->row_array();

				$this->load->view('gestao/eleicoes_cadastro/kit', $data);
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

	function imprimirKit()
	{
        if (($this->fl_libera) and (gerencia_in(array('GI'))))
        {
            if(intval($id_eleicao) > 0)
			{
				$args = Array();
				$data = Array();
				$result = null;
						
				$args["id_eleicao"] = $this->input->post("id_eleicao", TRUE);	
						
				$this->Cadastro_eleicoes_model->kit($result, $args);
				$ar_kit = $result->result_array();
				
				echo "<PRE>".print_r($ar_kit,true)."</PRE>";
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