<?php
class atividade_atendimento extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/atividade_atendimento_model');
    }
    
    public function index($cd_atividade = 0)
    {
		$args   = Array();
        $data   = Array();
        $result = null;
        $data['fl_salvar'] = false;
		
        $args['cd_atividade'] = intval($cd_atividade);

        if(intval($args['cd_atividade']) > 0)
		{   
			$this->atividade_atendimento_model->atividade($result, $args);
			$data['ar_atividade'] = $result->row_array();		

			#echo '<pre>';
			#print_r($data['ar_atividade']);
			#exit;	
			
			if(count($data['ar_atividade']) > 0)
			{
				if($data['ar_atividade']['tipo_ativ'] != "L")
				{
					if(in_array($data['ar_atividade']['cd_gerencia_destino'], array("GI","GAP","GB","GAD","GF","GC","GA","GRI","GJ", "GRC-RH","GC-RH", "GS-RH", "GFC-DIG", "GCM-CAD", "GS-RH")))
					{
						$data['fl_salvar'] = ($data['ar_atividade']['cod_atendente'] == $this->session->userdata('codigo') ? TRUE : 
							($data['ar_atividade']['cd_substituto'] == $this->session->userdata('codigo') ? TRUE : FALSE)
						);
						$data['fl_salvar'] = (trim($data['ar_atividade']['dt_fim_real']) != "" ? FALSE : $data['fl_salvar']);
						$data['fl_teste']  = (trim($data['ar_atividade']['status_atual']) == "ETES" ? TRUE : FALSE);
						
						$args['cd_gerencia_destino'] = $data['ar_atividade']['cd_gerencia_destino'];
						$args['status_atual']        = $data['ar_atividade']['status_atual'];
						$args['dt_fim_real']         = $data['ar_atividade']['dt_fim_real'];
						$args['cod_atendente']       = $data['ar_atividade']['cod_atendente'];
						$args['cd_substituto']       = $data['ar_atividade']['cd_substituto'];
						$args['cod_testador']        = $data['ar_atividade']['cod_testador'];

						$this->atividade_atendimento_model->cb_status_atual($result, $args);
						$data['ar_status_atual'] = $result->result_array();
						
						if(in_array($data['ar_atividade']['cd_gerencia_destino'], array("GI","GRI", "GRC-RH", "GC-RH", "GS-RH")))
						{
							$this->atividade_atendimento_model->cb_testador($result, $args);
							$data['ar_testador'] = $result->result_array();	
						}	

						if(in_array($data['ar_atividade']['cd_gerencia_destino'], array("GI")))
						{
							$this->atividade_atendimento_model->cb_classificacao($result, $args);
							$data['ar_classificacao'] = $result->result_array();								
							
							$this->atividade_atendimento_model->cb_sistema($result, $args);
							$data['ar_sistema'] = $result->result_array();	
							
							$this->atividade_atendimento_model->cb_complexidade($result, $args);
							$data['ar_complexidade'] = $result->result_array();
							
							$this->atividade_atendimento_model->cb_solucao($result, $args);
							$data['ar_solucao'] = $result->result_array();						
						}

						if(in_array($data['ar_atividade']['cd_gerencia_destino'], array("GRC-RH", "GC-RH", "GS-RH")))
						{
							$data['ar_atividade']['cd_gerencia_destino'] = 'grc-rh';
						}

						$this->load->view('atividade/atividade_atendimento/index_'.str_replace('-', '_',strtolower(trim($data['ar_atividade']['cd_gerencia_destino']))), $data);
					}
					else
					{
						exibir_mensagem("ERRO: ATIVIDADE NÃO ENCONTRADA");
						#header( 'location:'.base_url("sysapp/application/migre/cad_atividade_atend.php?n=".intval($args['cd_atividade'])."&aa=".$data['ar_atividade']['cd_gerencia_destino']));
						#exit;
					}					
				}
				else
				{
					#### ATIVIDADE LEGAL - REDIRECIONAR ####
					redirect("atividade/atividade_atendimento_cenario_legal/index/".intval($args['cd_atividade'])."/".$data['ar_atividade']['cd_gerencia_destino'], "refresh");
				}
			}
			else
			{
				exibir_mensagem("ATIVIDADE NÃO ENCONTRADA");
			}			
        }
        else
        {
            exibir_mensagem("ATIVIDADE NÃO ENCONTRADA");
        }
    }

	function sugerirDataTeste()
    {
		$args   = Array();
        $data   = Array();
        $result = null;

		$this->atividade_atendimento_model->sugerirDataTeste($result, $args);
		$ar_retorno = $result->row_array();

		echo json_encode(array("dt_sugerida" => utf8_encode($ar_retorno["dt_sugerida"])));		
    }	
	
	function cronogramaCombo()
    {
		$args   = Array();
        $data   = Array();
        $result = null;

		$args["cd_atividade"]  = $this->input->post("cd_atividade", TRUE);
		$args["cod_atendente"] = $this->input->post("cod_atendente", TRUE);

		$this->atividade_atendimento_model->cronogramaCombo($result, $args);
		$ar_retorno = $result->result_array();
		
		$ar_json = array();
		
		foreach($ar_retorno as $ar_reg)
		{
			$ar_json[] = array("value" => $ar_reg["value"], "text" => utf8_encode($ar_reg["text"]));
		}			
		
		echo json_encode($ar_json);		
    }	
	
	function cronogramaListar()
    {
		$args   = Array();
        $data   = Array();
        $result = null;

		$args["cd_atividade"]        = $this->input->post("cd_atividade", TRUE);
		$args["cd_gerencia_destino"] = $this->input->post("cd_gerencia_destino", TRUE);

		$this->atividade_atendimento_model->cronogramaListar($result, $args);
		$data['collection'] = $result->result_array();
		
		$data["fl_salvar"]  = $this->input->post("fl_salvar", TRUE);
		$data["fl_teste"]  = $this->input->post("fl_teste", TRUE);
		
		$this->load->view('atividade/atividade_atendimento/index_'.strtolower(trim($args['cd_gerencia_destino'])).'_cronograma', $data);
    }	

	function tarefaListar()
    {
		$args   = Array();
        $data   = Array();
        $result = null;

		$args["cd_atividade"]        = $this->input->post("cd_atividade", TRUE);
		$args["cd_gerencia_destino"] = $this->input->post("cd_gerencia_destino", TRUE);

		$this->atividade_atendimento_model->tarefaListar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/atividade_atendimento/index_'.strtolower(trim($args['cd_gerencia_destino'])).'_tarefa', $data);
    }

	function salvar()
    {
		$args   = Array();
        $data   = Array();
        $result = null;

        $args['cd_atividade'] = intval($this->input->post("numero", TRUE));

		$this->atividade_atendimento_model->atividade($result, $args);
		$atividade = $result->row_array();

		$args['numero']                     = $this->input->post("numero", TRUE);
		$args['dt_env_teste']               = $this->input->post("dt_env_teste", TRUE);
		$args['cd_gerencia_destino']        = $this->input->post("cd_gerencia_destino", TRUE);
		$args['cd_atividade_solucao']       = $this->input->post("cd_atividade_solucao", TRUE);
		$args['cd_solucao_categoria']       = $this->input->post("cd_solucao_categoria", TRUE);
		$args['ds_solucao_assunto']         = $this->input->post("ds_solucao_assunto", TRUE);
		$args['sistema']                    = $this->input->post("sistema", TRUE);
		$args['status_anterior']            = $this->input->post("status_anterior", TRUE);
		$args['status_atual']               = $this->input->post("status_atual", TRUE);
		$args['dt_inicio_prev']             = $this->input->post("dt_inicio_prev", TRUE);
		$args['dt_fim_prev']                = $this->input->post("dt_fim_prev", TRUE);
		$args['dt_inicio_real']             = $this->input->post("dt_inicio_real", TRUE);
		$args['dt_limite_teste']            = $this->input->post("dt_limite_teste", TRUE);
		$args['fl_teste_relevante']         = $this->input->post("fl_teste_relevante", TRUE);
		$args['cod_testador']               = $this->input->post("cod_testador", TRUE);
		$args['solucao']                    = $this->input->post("solucao", TRUE);
		$args['complexidade']               = $this->input->post("complexidade", TRUE);
		$args['fl_balanco_gi']              = $this->input->post("fl_balanco_gi", TRUE);
		$args['cd_atividade_classificacao'] = $this->input->post("cd_atividade_classificacao", TRUE);
		$args['cd_gerencia_solicitante']    = $atividade['cd_gerencia_solicitante'];
		$args['cd_empresa']                 = $atividade['cd_empresa'];
		$args['cd_registro_empregado']      = $atividade['cd_registro_empregado'];
		$args['cd_sequencia']               = $atividade['cd_sequencia'];
		$args['cd_usuario']                 = $this->session->userdata('codigo');
		
		$this->atividade_atendimento_model->salvar($result, $args);
		
		redirect("atividade/atividade_atendimento/index/".intval($args["numero"]), "refresh");
    }	
}
?>