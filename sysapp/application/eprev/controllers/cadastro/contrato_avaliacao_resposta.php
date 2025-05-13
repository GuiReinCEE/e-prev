<?php

class contrato_avaliacao_resposta extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/contrato_avaliacao_resposta_model');
    }
	
	public function index($cd_contrato_avaliacao)
    {
        $args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_contrato_avaliacao'] = $cd_contrato_avaliacao;
		$args['cd_usuario']            = $this->session->userdata('codigo');
		
		$this->contrato_avaliacao_resposta_model->verificacoes_contrato_avaliacao($result, $args);
		$arr = $result->row_array();
		
		if(count($arr) > 0 AND intval($arr['tl']) > 0)
		{
			if(intval($arr['fl_limite']) > 0)
			{
				if(intval($arr['fl_avaliou']) == 0)
				{
					$this->contrato_avaliacao_resposta_model->contrato_avaliacao($result, $args);
					$data['row'] = $result->row_array();
					
					$this->contrato_avaliacao_resposta_model->grupos_perguntas($result, $args);
					$grupos = $result->result_array();
					
					$data['grupos'] = array();
					
					$i = 0;
					
					foreach($grupos as $item)
					{
						$data['grupos'][$i]['numero'] = $i+1;
						$data['grupos'][$i]['cd_contrato_formulario_grupo'] = $item['cd_contrato_formulario_grupo'];
						$data['grupos'][$i]['ds_contrato_formulario_grupo'] = $item['ds_contrato_formulario_grupo'];
						
						$args['cd_contrato_formulario_grupo'] = $item['cd_contrato_formulario_grupo'];
						
						$this->contrato_avaliacao_resposta_model->perguntas($result, $args);
						$grupos = $result->result_array();
						
						$j = 0;
						
						foreach($grupos as $item2)
						{
							$data['grupos'][$i]['perguntas'][$j]['cd_contrato_formulario_pergunta'] = $item2['cd_contrato_formulario_pergunta'];
							$data['grupos'][$i]['perguntas'][$j]['ds_contrato_formulario_pergunta'] = $item2['ds_contrato_formulario_pergunta'];
							
							$args['cd_contrato_formulario_pergunta'] = $item2['cd_contrato_formulario_pergunta'];
							
							$this->contrato_avaliacao_resposta_model->respostas($result, $args);
							$respostas = $result->result_array();
							
							$f = 0;
							
							foreach($respostas as $item3)
							{
								$data['grupos'][$i]['perguntas'][$j]['respostas'][$f]['text']  = $item3['ds_resposta'];
								$data['grupos'][$i]['perguntas'][$j]['respostas'][$f]['value'] = $item['cd_contrato_avaliacao_item'].'_'.$item3['cd_contrato_formulario_resposta'];
								
								$f ++;
							}
							
							$j++;
						}
						
						$i++;
					}
				
					$this->load->view('cadastro/contrato_avaliacao_resposta/index', $data);
				}
				else
				{
					exibir_mensagem("VOCส Jม AVALIOU O CONTRATO");
				}
			}
			else
			{
				exibir_mensagem("DATA LIMITE PARA AVALIAวรO ENCERROU");
			}
		}
		else
		{
			exibir_mensagem("USUมRIO NรO ษ AVALIADOR");
		}
    }
	
	function salvar()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$resposta           = $this->input->post("resposta", TRUE);
		$args['cd_usuario'] = $this->session->userdata('codigo');
		
		foreach($resposta as $item)
		{
			$arr = explode("_", $item);
			
			$args['cd_contrato_formulario_pergunta'] = $arr[0];
			$args['cd_contrato_formulario_resposta'] = $arr[1];
			
			$this->contrato_avaliacao_resposta_model->salvar($result, $args);
		}
		
		redirect("home", "refresh");
	}
}
?>