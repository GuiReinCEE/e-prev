<?php
class operacional_enquete extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/enquetes_model');
    }

    function index()
    {
		$result = null;
        $data   = Array();
		$args   = Array();
		
		$this->load->view('ecrm/operacional_enquete/index');
    }

    function listar()
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["titulo"]     = $this->input->post("titulo", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_fim", TRUE);
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);

		manter_filtros($args);

        $this->enquetes_model->listar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/operacional_enquete/index_result', $data);
    }
	
	function duplicar($cd_enquete = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete'] = intval($cd_enquete); 
		$args['cd_usuario'] = $this->session->userdata('codigo');

		$this->enquetes_model->duplicar($result, $args);
		$ar_reg = $result->row_array();
		
		redirect("ecrm/operacional_enquete/estrutura/".$ar_reg["cd_enquete"], "refresh");
	}		
	
    function cadastro($cd_enquete = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;

		$data["ar_controle_resposta"][] = Array('value' => 'I', 'text' => 'Computador-IP (Público externo e/ou interno)');
		$data["ar_controle_resposta"][] = Array('value' => 'U', 'text' => 'Usuário e-prev (Somente colaboradores)');
		$data["ar_controle_resposta"][] = Array('value' => 'F', 'text' => 'Formulário (Digitação de formulários)');
		$data["ar_controle_resposta"][] = Array('value' => 'P', 'text' => 'Participante');
		$data["ar_controle_resposta"][] = Array('value' => 'R', 'text' => 'RE');
		
		$this->enquetes_model->combo_area_responsavel($result, $args);
		$data['ar_area_responsavel'] = $result->result_array();		
		
		
		$args["cd_enquete"]      = intval($cd_enquete);
		$args["cd_gerencia"]     = $this->session->userdata("divisao");
		$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
		$args["cd_usuario"]      = $this->session->userdata("codigo");

		if(intval($args["cd_enquete"]) == 0)
		{
			$data["row"] = array(
				'cd_enquete'             => $args["cd_enquete"],
				'ds_url_pesquisa'        => "", 
				'ds_titulo'              => "", 
				'nr_publico_total'       => "",
				'dt_inicio'              => "",
				'hr_inicio'              => "",
				'dt_final'               => "",
				'hr_final'               => "",
				'tp_controle_resposta'   => "",
				'cd_divisao_responsavel' => "",
				'cd_gerencia'            => $this->session->userdata("divisao"),				
				'cd_responsavel'         => $this->session->userdata("codigo"),				
				'texto_abertura'         => "",
				'texto_encerramento'     => "",
				'fl_aba'                 => "N",
				'fl_editar'              => "S"
			);
		}
		else
		{
			$this->enquetes_model->cadastro($result, $args);
			$data['row'] = $result->row_array();
		}
		$this->load->view('ecrm/operacional_enquete/cadastro', $data);
    }	
	
	function salvar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']             = $this->input->post("cd_enquete", TRUE); 
		$args['ds_titulo']              = $this->input->post("ds_titulo", TRUE); 
		$args['nr_publico_total']       = $this->input->post("nr_publico_total", TRUE); 
		$args['dt_inicio']              = $this->input->post("dt_inicio", TRUE); 
		$args['hr_inicio']              = $this->input->post("hr_inicio", TRUE); 
		$args['dt_final']               = $this->input->post("dt_final", TRUE); 
		$args['hr_final']               = $this->input->post("hr_final", TRUE); 
		$args['tp_controle_resposta']   = $this->input->post("tp_controle_resposta", TRUE); 
		$args['cd_divisao_responsavel'] = $this->input->post("cd_divisao_responsavel", TRUE); 
		$args['cd_responsavel']         = $this->input->post("cd_responsavel", TRUE); 
		$args['texto_abertura']         = $this->input->post("texto_abertura", TRUE); 
		$args['texto_encerramento']     = $this->input->post("texto_encerramento", TRUE); 
		$args['cd_usuario']             = $this->session->userdata('codigo');
	
		$cd_enquete = $this->enquetes_model->salvar($result, $args);
		
		redirect("ecrm/operacional_enquete/cadastro/".$cd_enquete, "refresh");
	}	
	
	function limparResposta($cd_enquete = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']  = intval($cd_enquete); 
		$args['cd_usuario']  = $this->session->userdata('codigo');
	
		$this->enquetes_model->limparResposta($result, $args);
		
		redirect("ecrm/operacional_enquete/cadastro/".$args['cd_enquete'], "refresh");
	}	
	
	function estrutura($cd_enquete = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
    	if (intval($cd_enquete) > 0)
        {		
			$args["cd_enquete"]      = intval($cd_enquete);

			$this->enquetes_model->combo_agrupamento($result, $args);
			$data['ar_agrupamento'] = $result->result_array();
			
			$args["cd_gerencia"]     = $this->session->userdata("divisao");
			$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
			$args["cd_usuario"]      = $this->session->userdata("codigo");			
			$this->enquetes_model->cadastro($result, $args);
			$data['ar_cadastro'] = $result->row_array();			
			
			
			$this->load->view('ecrm/operacional_enquete/estrutura', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }	
	
	function estruturaSalvar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']        = $this->input->post("cd_enquete", TRUE); 
		$args['cd_agrupamento']    = $this->input->post("cd_agrupamento", TRUE); 
		$args['cd_pergunta_texto'] = $this->input->post("cd_pergunta_texto", TRUE); 
		$args['pergunta_texto']    = $this->input->post("pergunta_texto", TRUE); 
		$args['cd_usuario']        = $this->session->userdata('codigo');

		$this->enquetes_model->estruturaSalvar($result, $args);
		
		redirect("ecrm/operacional_enquete/estrutura/".$args['cd_enquete'], "refresh");
	}	
	
    function agrupamento($cd_enquete = 0, $cd_agrupamento = 0)
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"]     = intval($cd_enquete);
		$args["cd_agrupamento"] = intval($cd_agrupamento);
		
		if(intval($args["cd_agrupamento"]) == 0)
		{
			$data["row"] = array(
				'cd_enquete'            => $args["cd_enquete"],
				'cd_agrupamento'        => $args["cd_agrupamento"],
				'ds_agrupamento'        => "",
				'indic_escala'          => "N",
				'mostrar_valores'       => "S",
				'numero_colunas_maximo' => "",
				'ncolsamp_diss'         => "",
				'nr_ordem'              => "",
				'nota_rodape'           => "",
				'disposicao'            => "V"			
			);
		}
		else
		{
			$this->enquetes_model->agrupamento($result, $args);
			$data['row'] = $result->row_array();
		}		
		
		$args["cd_gerencia"]     = $this->session->userdata("divisao");
		$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
		$args["cd_usuario"]      = $this->session->userdata("codigo");			
		$this->enquetes_model->cadastro($result, $args);
		$data['ar_cadastro'] = $result->row_array();			
		
        $this->load->view('ecrm/operacional_enquete/agrupamento', $data);
    }	
	
    function agrupamentoListar()
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
        $this->enquetes_model->agrupamentoListar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/operacional_enquete/estrutura_agrupamento_result', $data);
    }	
	
	function agrupamentoSalvar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']      = $this->input->post("cd_enquete", TRUE); 
		$args['cd_agrupamento']  = $this->input->post("cd_agrupamento", TRUE); 
		$args['ds_agrupamento']  = $this->input->post("ds_agrupamento", TRUE); 
		$args['nr_ordem']        = $this->input->post("nr_ordem", TRUE); 
		$args['indic_escala']    = $this->input->post("indic_escala", TRUE); 
		$args['mostrar_valores'] = $this->input->post("mostrar_valores", TRUE); 
		$args['disposicao']      = $this->input->post("disposicao", TRUE); 
		$args['nota_rodape']     = $this->input->post("nota_rodape", TRUE); 
		$args['cd_usuario']      = $this->session->userdata('codigo');
	
		$cd_agrupamento = $this->enquetes_model->agrupamentoSalvar($result, $args);
		
		redirect("ecrm/operacional_enquete/agrupamento/".$args['cd_enquete']."/".$cd_agrupamento, "refresh");
	}

	function agrupamentoExcluir($cd_enquete = 0, $cd_agrupamento = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"]     = intval($cd_enquete);
		$args["cd_agrupamento"] = intval($cd_agrupamento); 
		$args['cd_usuario']     = $this->session->userdata('codigo');
	
		$this->enquetes_model->agrupamentoExcluir($result, $args);
		
		redirect("ecrm/operacional_enquete/estrutura/".$args['cd_enquete'], "refresh");
	}	
	
    function questao($cd_enquete = 0, $cd_pergunta = 0)
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"]  = intval($cd_enquete);
		$args["cd_pergunta"] = intval($cd_pergunta);
		
		if(intval($args["cd_pergunta"]) == 0)
		{
			$data["row"] = array(
				'cd_enquete'           => $args["cd_enquete"],
				'cd_pergunta'          => $args["cd_pergunta"],
				'cd_agrupamento'       => "",
				'nr_ordem'             => "",
				'ds_pergunta'          => "",
				'r_diss'               => "N",
				'rotulo_dissertativa'  => "",
				'r_justificativa'      => "N",
				'rotulo_justificativa' => "",
				'r1' => "S",
				'r2' => "N",
				'r3' => "N",
				'r4' => "N",
				'r5' => "N",
				'r6' => "N",
				'r7' => "N",
				'r8' => "N",
				'r9' => "N",
				'r10' => "N",
				'r11' => "N",
				'r12' => "N",
				'r13' => "N",
				'r14' => "N",
				'r15' => "N",
				'rotulo1' => "",
				'rotulo2' => "",
				'rotulo3' => "",
				'rotulo4' => "",
				'rotulo5' => "",
				'rotulo6' => "",
				'rotulo7' => "",
				'rotulo8' => "",
				'rotulo9' => "",
				'rotulo10' => "",
				'rotulo11' => "",
				'rotulo12' => "",	
				'rotulo13' => "",	
				'rotulo14' => "",	
				'rotulo15' => "",	
				'legenda1' => "",
				'legenda2' => "",
				'legenda3' => "",
				'legenda4' => "",
				'legenda5' => "",
				'legenda6' => "",
				'legenda7' => "",
				'legenda8' => "",
				'legenda9' => "",
				'legenda10' => "",
				'legenda11' => "",
				'legenda12' => "",	
				'legenda13' => "",	
				'legenda14' => "",	
				'legenda15' => "",	
				'r1_complemento' => "N",
				'r2_complemento' => "N",
				'r3_complemento' => "N",
				'r4_complemento' => "N",
				'r5_complemento' => "N",
				'r6_complemento' => "N",
				'r7_complemento' => "N",
				'r8_complemento' => "N",
				'r9_complemento' => "N",
				'r10_complemento' => "N",
				'r11_complemento' => "N",
				'r12_complemento' => "N",	
				'r13_complemento' => "N",	
				'r14_complemento' => "N",	
				'r15_complemento' => "N",	
				'r1_complemento_rotulo' => "",
				'r2_complemento_rotulo' => "",
				'r3_complemento_rotulo' => "",
				'r4_complemento_rotulo' => "",
				'r5_complemento_rotulo' => "",
				'r6_complemento_rotulo' => "",
				'r7_complemento_rotulo' => "",
				'r8_complemento_rotulo' => "",
				'r9_complemento_rotulo' => "",
				'r10_complemento_rotulo' => "",
				'r11_complemento_rotulo' => "",
				'r12_complemento_rotulo' => "",		
				'r13_complemento_rotulo' => "",		
				'r14_complemento_rotulo' => "",		
				'r15_complemento_rotulo' => ""		
			);
		}
		else
		{
			$this->enquetes_model->questao($result, $args);
			$data['row'] = $result->row_array();
		}		
		
		$this->enquetes_model->combo_agrupamento($result, $args);
		$data['ar_agrupamento'] = $result->result_array();		
		
		$args["cd_gerencia"]     = $this->session->userdata("divisao");
		$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
		$args["cd_usuario"]      = $this->session->userdata("codigo");			
		$this->enquetes_model->cadastro($result, $args);
		$data['ar_cadastro'] = $result->row_array();			
		
        $this->load->view('ecrm/operacional_enquete/questao', $data);
    }

	function questaoSalvar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']      = $this->input->post("cd_enquete", TRUE); 
		$args['cd_pergunta']     = $this->input->post("cd_pergunta", TRUE); 
		$args['cd_agrupamento']  = $this->input->post("cd_agrupamento", TRUE); 
		$args['ds_pergunta']     = $this->input->post("ds_pergunta", TRUE); 
		
		$nr_conta = 1;
		while($nr_conta <= 15)
		{
			$args['r'.$nr_conta]                       = $this->input->post('r'.$nr_conta, TRUE); 
			$args['rotulo'.$nr_conta]                  = $this->input->post('rotulo'.$nr_conta, TRUE); 
			$args['legenda'.$nr_conta]                 = $this->input->post('legenda'.$nr_conta, TRUE); 
			$args['r'.$nr_conta.'_complemento']        = $this->input->post('r'.$nr_conta.'_complemento', TRUE); 
			$args['r'.$nr_conta.'_complemento_rotulo'] = $this->input->post('r'.$nr_conta.'_complemento_rotulo', TRUE); 
			$nr_conta++;
		}
		
		$args['nr_ordem']             = $this->input->post("nr_ordem", TRUE); 
		$args['r_diss']               = $this->input->post("r_diss", TRUE); 
		$args['rotulo_dissertativa']  = $this->input->post("rotulo_dissertativa", TRUE); 
		$args['r_justificativa']      = $this->input->post("r_justificativa", TRUE); 
		$args['rotulo_justificativa'] = $this->input->post("rotulo_justificativa", TRUE); 
		$args['cd_usuario']           = $this->session->userdata('codigo');
	
		$cd_pergunta = $this->enquetes_model->questaoSalvar($result, $args);
		
		redirect("ecrm/operacional_enquete/questao/".$args['cd_enquete']."/".$cd_pergunta, "refresh");
	}	
	
	function questaoExcluir($cd_enquete = 0, $cd_pergunta = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"]  = intval($cd_enquete);
		$args["cd_pergunta"] = intval($cd_pergunta); 
		$args['cd_usuario']  = $this->session->userdata('codigo');
	
		$this->enquetes_model->questaoExcluir($result, $args);
		
		redirect("ecrm/operacional_enquete/estrutura/".$args['cd_enquete'], "refresh");
	}	
	
    function questaoListar()
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
        $this->enquetes_model->questaoListar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/operacional_enquete/estrutura_questao_result', $data);
    }

    function resposta($cd_enquete = 0, $cd_resposta = 0)
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"]  = intval($cd_enquete);
		$args["cd_resposta"] = intval($cd_resposta);
		
		if(intval($args["cd_resposta"]) == 0)
		{
			$data["row"] = array(
				'cd_enquete'  => $args["cd_enquete"],
				'cd_resposta' => $args["cd_resposta"],
				'ds_resposta' => "",
				'nr_ordem'    => "",
				'valor'       => ""
			);
		}
		else
		{
			$this->enquetes_model->resposta($result, $args);
			$data['row'] = $result->row_array();
		}		
		
		$args["cd_gerencia"]     = $this->session->userdata("divisao");
		$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
		$args["cd_usuario"]      = $this->session->userdata("codigo");			
		$this->enquetes_model->cadastro($result, $args);
		$data['ar_cadastro'] = $result->row_array();			
		
        $this->load->view('ecrm/operacional_enquete/resposta', $data);
    }	
	
	function respostaSalvar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_enquete']  = $this->input->post("cd_enquete", TRUE); 
		$args['cd_resposta'] = $this->input->post("cd_resposta", TRUE); 
		$args['ds_resposta'] = $this->input->post("ds_resposta", TRUE); 
		$args['nr_ordem']    = $this->input->post("nr_ordem", TRUE); 
		$args["vl_valor"]    = app_decimal_para_db($this->input->post("vl_valor",TRUE));
		$args['cd_usuario']  = $this->session->userdata('codigo');
	
		$cd_resposta = $this->enquetes_model->respostaSalvar($result, $args);
		
		redirect("ecrm/operacional_enquete/resposta/".$args['cd_enquete']."/".$cd_resposta, "refresh");
	}	
	
    function respostaListar()
    {
		$result = null;
        $data   = Array();
		$args   = Array();
        
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
        $this->enquetes_model->respostaListar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/operacional_enquete/estrutura_resposta_result', $data);
    }	
	
	function resultado($cd_enquete = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
    	if (intval($cd_enquete) > 0)
        {		
			$args["cd_enquete"]      = intval($cd_enquete);
			
			$args["cd_gerencia"]     = $this->session->userdata("divisao");
			$args["cd_gerencia_ant"] = $this->session->userdata("divisao_ant");
			$args["cd_usuario"]      = $this->session->userdata("codigo");			
			$this->enquetes_model->cadastro($result, $args);
			$data['ar_cadastro'] = $result->row_array();			
			
			$this->load->view('ecrm/operacional_enquete/resultado', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }	
	
	function resultadoResumo()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoResumo($result, $args);
		$data['ar_resumo'] = $result->row_array();
		
		$this->load->view('ecrm/operacional_enquete/resultado_resumo_result', $data);
	}

	function resultadoAgrupamento()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoAgrupamento($result, $args);
		$data['ar_reg'] = $result->result_array();
		$this->load->view('ecrm/operacional_enquete/resultado_agrupamento_result', $data);
	}	
	
	function resultadoQuestaoResumo()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoQuestaoResumo($result, $args);
		$data['ar_reg'] = $result->result_array();
		$this->load->view('ecrm/operacional_enquete/resultado_questao_resumo_result', $data);
	}	
	
	function resultadoVerComentario()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"]  = $this->input->post("cd_enquete", TRUE);
		$args["cd_pergunta"] = $this->input->post("cd_pergunta", TRUE);
		$args["dt_ini"]      = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoVerComentario($result, $args);
		$ar_reg = $result->result_array();
		
		echo br();
		foreach($ar_reg as $item)
		{
			echo $item['descricao'].br()."<hr>";
		}
	}

	function resultadoVerGrafico()
    {
		$this->load->library('charts');
		
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"]  = $this->input->post("cd_enquete", TRUE);
		$args["cd_pergunta"] = $this->input->post("cd_pergunta", TRUE);
		$args["dt_ini"]      = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoVerGrafico($result, $args);
		$ar_reg = $result->result_array();
		
		$titulo = $ar_reg[0]['ds_pergunta'];
		$ar_legenda = Array();
		$ar_dado = Array();				

		foreach($ar_reg as $ar_item )
		{
			$ar_legenda[] = $ar_item["ds_resposta"]." (".number_format($ar_item["qt_resposta"],0,",",".").")";
			$ar_dado[]   = $ar_item["qt_resposta"];
		}		
		$ar_image = $this->charts->pieChart(140, $ar_dado, $ar_legenda, "", $titulo );	
		
		echo '<img src="'.base_url().str_replace("cieprev/","",$ar_image["name"]).'" border="0">';
	}
	
	function resultadoQuestao()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoQuestao($result, $args);
		$ar_ret = $result->result_array();
		
		$nr_conta = 0;
		$nr_fim   = count($ar_ret);
		while($nr_conta < $nr_fim)
		{
			$args["cd_pergunta"] = $ar_ret[$nr_conta]["cd_pergunta"];
			$this->enquetes_model->resultadoQuestaoResposta($result, $args);
			$ar_r = $result->result_array();			
			
			foreach($ar_r as $item)
			{
				$ar_ret[$nr_conta]["ar_resp"][] = $item;
			}
			$nr_conta++;
		}
		
		$data["ar_reg"] = $ar_ret;
		
		$this->load->view('ecrm/operacional_enquete/resultado_questao_result', $data);
	}

	function resultadoVerComplemento()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_enquete"]  = $this->input->post("cd_enquete", TRUE);
		$args["cd_pergunta"] = $this->input->post("cd_pergunta", TRUE);
		$args["cd_resposta"] = $this->input->post("cd_resposta", TRUE);
		$args["dt_ini"]      = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_referencia_fim", TRUE);
		
		$this->enquetes_model->resultadoVerComplemento($result, $args);
		$ar_reg = $result->result_array();
		
		echo br();
		foreach($ar_reg as $item)
		{
			echo $item['complemento'].br()."<hr>";
		}
	}	
	
	function relatorioPDF()
	{
		set_time_limit(0);
		$result = null;
        $data   = Array();
		$args   = Array();
		
		$this->load->plugin('fpdf');
		$this->load->plugin('pchart');
		#$this->load->library('charts');
		
		#echo "<PRE>".print_r($_POST,true)."</PRE>";exit;
		
		$args["cd_enquete"] = $this->input->post("cd_enquete", TRUE);
		$args["dt_ini"]     = $this->input->post("dt_referencia_ini", TRUE);
		$args["dt_fim"]     = $this->input->post("dt_referencia_fim", TRUE);
    
		if(intval($args["cd_enquete"]) > 0)
		{
			$nr_largura = 190;
			$nr_espaco = 4;		
			$ob_pdf = new PDF('P','mm','A4'); 	
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10,14,5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->borda_exibe = true;
			
			################################################## CAPA ##################################################		
			$ob_pdf->AddPage();
			
			$qr_sql = "
						SELECT e.cd_enquete, 
							   e.titulo, 
							   e.texto_abertura, 
							   TO_CHAR(e.dt_inicio, 'DD/MM/YYYY HH24:MI') AS dt_inicio, 
							   TO_CHAR(e.dt_fim, 'DD/MM/YYYY HH24:MI') AS dt_final, 
							   e.cd_site, 
							   e.cd_responsavel, 
							   e.cd_evento_institucional, 
							   e.cd_publicacao, 
							   e.imagem, 
							   e.controle_respostas,
							   u.guerra, 
							   u.divisao, 
							   d.nome AS nome_divisao, 
							   e.nr_publico_total,
							   (SELECT COUNT(DISTINCT(er.ip)) 
								  FROM projetos.enquete_resultados er
								 WHERE er.cd_enquete = e.cd_enquete 
								 ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
							   ) AS qt_resposta
						  FROM projetos.enquetes e
						  JOIN projetos.usuarios_controledi u
							ON u.codigo = e.cd_responsavel
						  JOIN projetos.divisoes d
							ON d.codigo = e.cd_divisao_responsavel
						 WHERE e.cd_enquete = ".intval($args["cd_enquete"])." 
					  ";
			$result = $this->db->query($qr_sql);
			$ar_capa = $result->row_array();
			
			$ob_pdf->SetY(100);
			$ob_pdf->SetFont('Courier','B',22);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Resultado da Pesquisa",0,"C");
			$ob_pdf->SetY($ob_pdf->GetY() + 15);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco + 4, $ar_capa['titulo'],0,"C");
			$ob_pdf->SetY($ob_pdf->GetY());
			$ob_pdf->MultiCell($nr_largura, 4, "____________________________",0,"C");
			
			$ob_pdf->SetFont('Courier','',14);
			$ob_pdf->SetY($ob_pdf->GetY() + 10);
			$periodo_leitura = "";
			if ((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != ""))
			{
				$periodo_leitura = "\n\n\nPeríodo de Amostragem " . $_SESSION["filtro_data_inicio"] . " e " . $_SESSION["filtro_data_fim"] . "";
			}
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Período de realização ".$ar_capa['dt_inicio']." e ".$ar_capa['dt_final'] . $periodo_leitura, 0, "C");	
			
			
			if (intval($ar_capa['nr_publico_total']) > 0)
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 10);
				$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Público total: ".intval($ar_capa['nr_publico_total']), 0, "C");
			}

			$ob_pdf->SetY($ob_pdf->GetY() + 5);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Número de respondentes: ".$ar_capa['qt_resposta'], 0, "C");

			if (intval($ar_capa['nr_publico_total']) > 0)
			{
				$nr_perc = (intval($ar_capa['qt_resposta']) * 100) / intval($ar_capa['nr_publico_total']);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Percentual de respondentes: ".number_format($nr_perc,2,",",".")."%", 0, "C");			
			}
			
			$ob_pdf->SetFont('Courier','B',20);
			$ob_pdf->SetY($ob_pdf->GetY() + 20);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, $ar_capa['nome_divisao'], 0, "C");	
			
			
			################################################## RESULTADOS ##################################################
			$ob_pdf->AddPage();
					  
			$qr_sql = "
						SELECT DISTINCT p.cd_enquete, a.cd_agrupamento,a.ordem,a.nome AS nome_agrupamento, p.cd_pergunta, p.texto, count(r.valor) AS soma, avg(r.valor) AS media
						  FROM projetos.enquete_resultados r
						  JOIN projetos.enquete_perguntas p
							ON p.cd_enquete = r.cd_enquete
						  JOIN projetos.enquete_agrupamentos a
							ON a.cd_enquete = r.cd_enquete
						   AND a.cd_agrupamento = r.cd_agrupamento  
						 WHERE r.questao::text = ('R_'::text || p.cd_pergunta::text)
						   AND (r.valor <> 6::numeric AND a.indic_escala = 'S'::bpchar OR a.indic_escala = 'N'::bpchar)
						   AND r.cd_enquete = ".intval($args["cd_enquete"])." 
						   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(r.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
						 GROUP BY p.cd_enquete, a.cd_agrupamento, a.ordem, a.nome, p.cd_pergunta, p.texto
						 ORDER BY a.ordem, p.cd_pergunta, p.texto, soma, media;
					  ";
			$result = $this->db->query($qr_sql);
			$ar_resultado = $result->result_array();
			
			$nr_conta = 0;
			foreach ($ar_resultado as $ar_reg)
			{
				if($nr_conta > 0)
				{
					$ob_pdf->AddPage();
				}
				$nr_conta++;
				#$ob_pdf->SetXY(10,15);		
				
				
				##### AGRUPAMENTO #####
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->SetXY(15,$ob_pdf->GetY());
				$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, "Grupo: ", 0, "J");		
				$ob_pdf->SetFont('Courier','I',14);
				$ob_pdf->SetXY(15,$ob_pdf->GetY()+2);
				$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, $ar_reg['nome_agrupamento'], 0, "J");	
				
				##### PERGUNTA #####
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->SetXY(15,$ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, "Pergunta: ", 0, "J");			
				$ob_pdf->SetFont('Courier','B',12);
				$ob_pdf->SetXY(15,$ob_pdf->GetY()+2);
				$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco,$ar_reg['texto'], 0, "J");		
				

				##### DADOS PARA GRAFICO #####
				$qr_sql = " 
							SELECT valor,
								   COUNT(*) AS qt_total
							  FROM projetos.enquete_resultados
							 WHERE cd_enquete = ".intval($args["cd_enquete"])."  
							   AND questao = 'R_".$ar_reg['cd_pergunta']."'
							   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
							 GROUP BY valor 
							 ORDER BY qt_total DESC
						  ";
				$result = $this->db->query($qr_sql);
				$ob_resul_graf = $result->result_array();		
				
				$ar_grafico     = Array();
				$ar_resp_complemento = Array();
				$ar_complemento = Array();

				foreach($ob_resul_graf as $ar_reg_graf)
				{
					if($ar_reg_graf['valor'] == 0)
					{
						$ar_reg_graf_nome['nome'] = "(outros)";
					}
					else
					{
						$qr_sql = "
									SELECT CASE WHEN TRIM(COALESCE(ep.legenda".intval($ar_reg_graf['valor']).",'')) <> '' THEN ep.legenda".intval($ar_reg_graf['valor'])."
												WHEN TRIM(COALESCE(ep.rotulo".intval($ar_reg_graf['valor']).",'')) <> ''  THEN ep.rotulo".intval($ar_reg_graf['valor'])."
												WHEN ea.indic_escala = 'S' THEN COALESCE((SELECT er.nome::TEXT FROM projetos.enquete_respostas er WHERE er.cd_enquete = ep.cd_enquete AND er.cd_resposta = ".intval($ar_reg_graf['valor'])."),'".intval($ar_reg_graf['valor'])."')
											ELSE '".intval($ar_reg_graf['valor'])."'
										   END AS nome
									  FROM projetos.enquete_perguntas ep
									  JOIN projetos.enquete_agrupamentos ea
										ON ea.cd_agrupamento = ep.cd_agrupamento
									   AND ea.cd_enquete     = ep.cd_enquete
									 WHERE ep.cd_enquete  = ".intval($args["cd_enquete"])."  
									   AND ep.cd_pergunta = ".$ar_reg['cd_pergunta']."						
						          ";
						/*
						$qr_sql = "
									SELECT CASE WHEN legenda".number_format($ar_reg_graf['valor'])." IS NOT NULL AND TRIM(legenda".number_format($ar_reg_graf['valor']).") <> ''
													  THEN legenda".number_format($ar_reg_graf['valor'])."
												WHEN rotulo".number_format($ar_reg_graf['valor'])." IS NOT NULL AND TRIM(rotulo".number_format($ar_reg_graf['valor']).") <> ''
													  THEN rotulo".number_format($ar_reg_graf['valor'])."
												ELSE '".number_format($ar_reg_graf['valor'])."'
											END AS nome
									  FROM projetos.enquete_perguntas 
									 WHERE cd_enquete  = ".intval($args["cd_enquete"])."  
									   AND cd_pergunta = ".$ar_reg['cd_pergunta']."			
								  ";
						*/		  
						$result = $this->db->query($qr_sql);
						$ar_reg_graf_nome = $result->row_array();				
					}
					$ar_grafico[$ar_reg_graf_nome['nome']] = $ar_reg_graf['qt_total'];
					
					
					#### MONTA COMPLEMENTO ####
					$qr_sql = "
								SELECT complemento
								  FROM projetos.enquete_resultados	
								 WHERE cd_enquete = ".intval($args["cd_enquete"])." 
								   AND questao    = 'R_".$ar_reg['cd_pergunta']."'
								   AND valor      = ".$ar_reg_graf['valor']."
									".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
								   AND complemento IS NOT NULL	
								 ORDER BY dt_resposta  
							  ";
					$result = $this->db->query($qr_sql);
					$ob_resul_complemento = $result->result_array();

					if(count($ob_resul_complemento) > 0)
					{
						$ar_resp_complemento[] = array($ar_reg['cd_pergunta'].'_'.$ar_reg_graf['valor'], $ar_reg_graf_nome['nome']);
					}
					
					foreach ($ob_resul_complemento as $ar_reg_complemento) 
					{
						$ar_complemento[$ar_reg['cd_pergunta'].'_'.$ar_reg_graf['valor']][] = $ar_reg_complemento['complemento'];
					}	
					
				}
				
				if(count($ar_grafico) > 0)
				{
					#### GRAFICO ####
					$ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);	
					$ar_titulo = Array();
					$ar_dado = Array();
					$ar_titulo = array_keys($ar_grafico);
					$ar_dado = array_values($ar_grafico);	
					
					$tot_graf = array_sum($ar_dado);
					$nr_conta_graf = 0;
					$nr_fim_graf = count($ar_dado);
					while($nr_conta_graf < $nr_fim_graf)
					{
						$nr_percentual = ($ar_dado[$nr_conta_graf] * 100) / $tot_graf;
						$nr_percentual = number_format($nr_percentual, 2,",",".");
						$ar_titulo[$nr_conta_graf] = $ar_titulo[$nr_conta_graf]." (".$nr_percentual."%)";
					
						$nr_conta_graf++;
					}
					
					$im = piechart($ar_titulo, $ar_dado, $ar_titulo, 600, 300);
					list($w, $h) = getimagesize("./".$im); 
					$w = intval((intval($w) * 88) / 100);
					$h = intval((intval($h) * 88) / 100);
					$x = 15 + intval( 90 - (($ob_pdf->ConvertSize($w)) / 2)  );
					$ob_pdf->Image($im, $x, $ob_pdf->GetY() + 5, $ob_pdf->ConvertSize($w), $ob_pdf->ConvertSize($h));	
				}
				
				$ob_pdf->SetXY(15,$ob_pdf->GetY() + 75);
				
				#### EXIBE COMPLEMENTO DAS RESPOSTAS ####
				$nr_conta_complemento = 0;
				while($nr_conta_complemento < count($ar_resp_complemento))
				{
					#### CABEÇALHO ####
					$ob_pdf->SetXY(15,$ob_pdf->GetY() + 5);
					$ob_pdf->SetLineWidth(0);
					$ob_pdf->SetDrawColor(0,0,0);
					$ob_pdf->SetWidths(array(180));
					$ob_pdf->SetAligns(array('J'));
					$ob_pdf->SetFont('Courier','B',10);
					$ob_pdf->Row(array("Complemento da resposta ".$ar_resp_complemento[$nr_conta_complemento][1]));		
					$ob_pdf->SetFont('Courier','',10);
					
					$cd_comp = $ar_resp_complemento[$nr_conta_complemento][0];
					$ar_comp = $ar_complemento[$cd_comp];
					$nr_conta_comp_resp = 0;
					while($nr_conta_comp_resp < count($ar_comp))
					{	
						#### LINHAS ####
						$ob_pdf->SetX(15);
						$ob_pdf->Row(array($ar_comp[$nr_conta_comp_resp]));	
						$nr_conta_comp_resp++;
					}		
					$nr_conta_complemento++;
				}
				
				if(count($ar_resp_complemento) > 0)
				{
					$ob_pdf->SetY($ob_pdf->GetY() + 10);
				}
				else
				{
					$ob_pdf->SetY($ob_pdf->GetY() + 30);
				}
				
				#### COMENTÁRIOS DA PERGUNTA ####
				$qr_sql = " 
							SELECT descricao 
							  FROM projetos.enquete_resultados 
							 WHERE cd_enquete      = ".intval($args["cd_enquete"])." 
							   AND questao         = 'R_".$ar_reg['cd_pergunta']."' 
							   AND descricao       IS NOT NULL
							   AND TRIM(COALESCE(descricao,'')) <> ''
							   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
							 ORDER BY descricao		
						  ";
				$result = $this->db->query($qr_sql);
				$ob_resul_comenta = $result->result_array();	

				
				if(count($ob_resul_comenta) > 0)
				{
					#### CABEÇALHO ####
					$ob_pdf->SetXY(15,$ob_pdf->GetY() + 15);
					$ob_pdf->SetLineWidth(0);
					$ob_pdf->SetDrawColor(0,0,0);
					$ob_pdf->SetWidths(array(180));
					$ob_pdf->SetAligns(array('J'));
					$ob_pdf->SetFont('Courier','B',10);
					$ob_pdf->Row(array("Comentários da Questão (total de comentários: ".count($ob_resul_comenta).")"));		
					$ob_pdf->SetFont('Courier','',10);					
					foreach($ob_resul_comenta as $ar_reg_comenta)
					{
						#### LINHAS ####
						$ob_pdf->SetLineWidth(0);
						$ob_pdf->SetX(15);
						$ob_pdf->Row(array($ar_reg_comenta['descricao']));	
					}
				}
				else
				{
					$ob_pdf->SetY($ob_pdf->GetY());
				}
				
			}		
			
			################################################## QUESTÃO DISSERTATIVA ##################################################	
			$ob_pdf->AddPage();
			#$ob_pdf->SetXY(10,15);
			$ob_pdf->SetFont('Courier','B',16);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Comentários por questões",0,"C");

			$qr_sql = " 
						SELECT pergunta_texto 
						  FROM projetos.enquete_perguntas  
						 WHERE cd_enquete           = ".intval($args["cd_enquete"])." 
						   AND texto                IS NULL 
						   AND TRIM(COALESCE(pergunta_texto,'')) <> ''				
					  ";	
			$result = $this->db->query($qr_sql);
			$ar_pergunta = $result->row_array();
			
			if(count($ar_pergunta) > 0)
			{
				#### CABEÇALHO ####
				$ob_pdf->SetXY(15,$ob_pdf->GetY() + 7);
				$ob_pdf->SetLineWidth(0);
				$ob_pdf->SetDrawColor(0,0,0);
				$ob_pdf->SetWidths(array(180));
				$ob_pdf->SetAligns(array('J'));
				$ob_pdf->SetFont('Courier','B',10);
						  
				$qr_sql = " 
							SELECT descricao 
							  FROM projetos.enquete_resultados 
							 WHERE cd_enquete = ".intval($args["cd_enquete"])." 
							   AND questao    = 'Texto'
							   AND TRIM(COALESCE(descricao,'')) <> ''	
							   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
						  ";
				$result = $this->db->query($qr_sql);
				$ar_resp = $result->result_array();				  

				$ob_pdf->Row(array($ar_pergunta['pergunta_texto']." (total de respostas: ".count($ar_resp).")"));		
				$ob_pdf->SetFont('Courier','',10);				  
							
				foreach($ar_resp as $ar_reg)
				{
					#### LINHAS ####
					$ob_pdf->SetLineWidth(0);
					$ob_pdf->SetX(15);
					$ob_pdf->Row(array($ar_reg['descricao']));	
				}		
			}
			
			
			#### GERA PDF ####
			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ERRO - CÓDIGO DA PESQUISA NÃO INFORMADO");
		}
	}
}
