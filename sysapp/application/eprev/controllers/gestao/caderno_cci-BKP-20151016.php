<?php
class Caderno_cci extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("gestao/caderno_cci_model");
    }

    private function web_service_sgs($cod, $mes, $ano)
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$this->load->library("Nusoap_lib");

		$args = array(
			"mes" => $mes,
			"ano" => $ano
		);

		$this->caderno_cci_model->ultimo_dia_mes($result, $args);
		$row = $result->row_array();

		$this->nusoap_client = new nusoap_client("https://www3.bcb.gov.br/sgspub/JSP/sgsgeral/FachadaWSSGS.wsdl", true);

		$config = array(
			array($cod),
			"01/".$mes."/".$ano,
			$row["dia"]."/".$mes."/".$ano,
		);

		$xml = $this->nusoap_client->call("getValoresSeriesXML", $config);

		$respost = simplexml_load_string($xml);

		$respost = json_decode(json_encode($respost), TRUE);

		$indice = array(
			"referencia" => (isset($respost["SERIE"]["ITEM"]["DATA"]) ? $respost["SERIE"]["ITEM"]["DATA"] : ""),
			"valor"      => (isset($respost["SERIE"]["ITEM"]["VALOR"]) ? $respost["SERIE"]["ITEM"]["VALOR"] : ""),
			"bloqueado"  => (isset($respost["SERIE"]["ITEM"]["BLOQUEADO"]) ? $respost["SERIE"]["ITEM"]["BLOQUEADO"] : "")
		);

		return $indice;	
	}

    private function get_mes()
    {
    	$ar_mes[] = array("value" => "01", "text" => "Janeiro");
		$ar_mes[] = array("value" => "02", "text" => "Fevereiro");
		$ar_mes[] = array("value" => "03", "text" => "Março");
		$ar_mes[] = array("value" => "04", "text" => "Abril");
		$ar_mes[] = array("value" => "05", "text" => "Maio");
		$ar_mes[] = array("value" => "06", "text" => "Junho");
		$ar_mes[] = array("value" => "07", "text" => "Julho");
		$ar_mes[] = array("value" => "08", "text" => "Agosto");
		$ar_mes[] = array("value" => "09", "text" => "Setembro");
		$ar_mes[] = array("value" => "10", "text" => "Outubro");
		$ar_mes[] = array("value" => "11", "text" => "Novembro");
		$ar_mes[] = array("value" => "12", "text" => "Dezembro");

		return $ar_mes;	
    }

  	/* CADASTRO ANO ----------------------------------------------------------------- */  

    function index()
    {
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
								
			$this->load->view("gestao/caderno_cci/index", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {		
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["nr_ano"] = $this->input->post("nr_ano", TRUE);

			$data["fl_excluir"] = ($this->session->userdata("tipo") == "G" ? true : false);
			
			manter_filtros($args);
			
			$this->caderno_cci_model->listar($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("gestao/caderno_cci/index_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_caderno_cci = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"] = $cd_caderno_cci;

			if(intval($cd_caderno_cci) == 0)
			{
				$args["nr_ano"] = (date("Y") - 1);

				$this->caderno_cci_model->ano_anterior($result, $args);
				$row = $result->row_array();

				$data["row"] = array(
					"cd_caderno_cci"            => intval($cd_caderno_cci),
					"nr_ano"                    => date("Y"),
					"cd_caderno_cci_referencia" => (isset($row["cd_caderno_cci"]) ? intval($row["cd_caderno_cci"]) : 0)
				);
			}
			else
			{
				$this->caderno_cci_model->carrega($result, $args);
				$data["row"] = $result->row_array();
			}
			
			$this->load->view("gestao/caderno_cci/cadastro", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]            = $this->input->post("cd_caderno_cci", TRUE);
			$args["nr_ano"]                    = $this->input->post("nr_ano", TRUE);
			$args["cd_caderno_cci_referencia"] = $this->input->post("cd_caderno_cci_referencia", TRUE);
			$args["cd_usuario"]                = $this->session->userdata("codigo");

			$cd_caderno_cci = $this->caderno_cci_model->salvar($result, $args);

			if(intval($args["cd_caderno_cci_referencia"]) > 0)
			{
				$args["cd_caderno_cci"] = $args["cd_caderno_cci_referencia"];

				//PROJETADO

				$this->caderno_cci_model->projetado_listar($result, $args);
				$collection = $result->result_array();

				foreach($collection as $item)
				{
					$projetado = array(
						"cd_caderno_cci_projetado_referencia" => $item["cd_caderno_cci_projetado"],
						"cd_caderno_cci_projetado"            => 0,
						"ds_caderno_cci_projetado"            => $item["ds_caderno_cci_projetado"],
						"cd_caderno_cci"                      => intval($cd_caderno_cci),
						"nr_ordem"                            => $item["nr_ordem"],
						"nr_projetado"                        => 0,
						"cd_usuario"                          => $args["cd_usuario"]
					);

					$this->caderno_cci_model->projetado_salvar($result, $projetado);
				}

				//ESTRUTURA

				$this->caderno_cci_model->estrutura_listar($result, $args);
				$collection = $result->result_array();

				foreach($collection as $item)
				{
					$estrutura = array(
						"cd_caderno_cci_estrutura_referencia" => $item["cd_caderno_cci_estrutura"],
						"cd_caderno_cci_estrutura"            => 0,
						"ds_caderno_cci_estrutura"            => $item["ds_caderno_cci_estrutura"],
						"cd_caderno_cci"                      => intval($cd_caderno_cci),
						"nr_politica_max"                     => $item["nr_politica_max"],
						"nr_politica_min"                     => $item["nr_politica_min"],
						"nr_legal_max"                        => $item["nr_legal_max"],
						"nr_legal_min"                        => $item["nr_legal_min"],
						"nr_rentabilidade"                    => $item["nr_rentabilidade"],
						"cd_caderno_cci_estrutura_pai"        => $item["cd_caderno_cci_estrutura_pai"],
						"fl_grupo"                            => $item["fl_grupo"],
						"fl_agrupar"                          => $item["fl_agrupar"],
						"nr_ordem"                            => $item["nr_ordem"],
						"fl_campo_metro"                      => $item["fl_campo_metro"],
						"fl_campo_quantidade"                 => $item["fl_campo_quantidade"],
						"fl_fundo"                            => $item["fl_fundo"],
						"fl_total"                            => $item["fl_total"],
						"calculo"                             => $item["calculo"],
						"cd_usuario"                          => $args["cd_usuario"]
					);					

					$args["cd_caderno_cci_estrutura"] = $this->caderno_cci_model->estrutura_salvar($result, $estrutura);
				}

				//INDICE

				$this->caderno_cci_model->indice_listar($result, $args);
				$collection = $result->result_array();

				foreach($collection as $item)
				{
					$indice = array(
						"cd_caderno_cci_indice_referencia" => $item["cd_caderno_cci_indice"],
						"cd_caderno_cci_indice"            => 0,
						"cd_caderno_cci"                   => intval($cd_caderno_cci),
						"nr_ordem"                         => $item["nr_ordem"],
						"ds_caderno_cci_indice"            => $item["ds_caderno_cci_indice"],
						"cd_sgs"                           => $item["cd_sgs"],
						"cd_usuario"                       => $args["cd_usuario"]
					);

					$this->caderno_cci_model->indice_salvar($result, $indice);
				}

				//BENCHMARK

				$this->caderno_cci_model->benchmark_listar($result, $args);
				$collection = $result->result_array();

				foreach($collection as $item)
				{
					$benchmark = array(
						"cd_caderno_cci_benchmark_referencia" => $item["cd_caderno_cci_benchmark"],
						"cd_caderno_cci_benchmark"            => 0,
						"cd_caderno_cci"                      => intval($cd_caderno_cci),
						"nr_ordem"                            => $item["nr_ordem"],
						"ds_caderno_cci_benchmark"            => $item["ds_caderno_cci_benchmark"],
						"cd_usuario"                          => $args["cd_usuario"]
					);

					$this->caderno_cci_model->benchmark_salvar($result, $benchmark);
				}

				//GRAFICO

				$this->caderno_cci_model->grafico_listar($result, $args);
				$collection = $result->result_array();

				foreach($collection as $item)
				{
					$grafico = array(
						"cd_caderno_cci_grafico_referencia" => $item["cd_caderno_cci_grafico"],
						"cd_caderno_cci_grafico"            => 0,
						"cd_caderno_cci"                    => intval($cd_caderno_cci),
						"parametro"                         => $item["parametro"],
						"tp_grafico"                        => $item["tp_grafico"],
						"nr_ordem"                          => $item["nr_ordem"],
						"ds_caderno_cci_grafico"            => $item["ds_caderno_cci_grafico"],
						"fl_ano"                            => $item["fl_ano"],
						"campo"                             => $item["campo"],
						"participacao"                      => $item["participacao"],
						"participacao_m2"                   => $item["participacao_m2"],
						"nota_rodape"                       => $item["nota_rodape"],
						"cor"                               => $item["cor"],
						"ordem"                             => $item["ordem"],
						"negrito"                           => $item["negrito"],
						"ds_html"                           => $item["ds_html"],
						"fl_mes"                            => $item["fl_mes"],
						"linha"                             => $item["linha"],
						"tab"                               => $item["tab"],
						"cd_usuario"                        => $args["cd_usuario"]
					);

					$this->caderno_cci_model->grafico_salvar($result, $grafico);
				}
			}

			$args["cd_caderno_cci"] = $cd_caderno_cci;

			$this->caderno_cci_model->estrutura_listar($result, $args);
			$collection = $result->result_array();

			foreach($collection as $item)
			{
				if(intval($item["cd_caderno_cci_estrutura_pai"]) > 0)
				{
					$args["cd_caderno_cci_estrutura_referencia"] = $item["cd_caderno_cci_estrutura_pai"];

					$this->caderno_cci_model->estrutura_referencia($result, $args);
					$row = $result->row_array();

					$args["cd_caderno_cci_estrutura_pai"] = $row["cd_caderno_cci_estrutura"];
					$args["cd_caderno_cci_estrutura"]     = $item["cd_caderno_cci_estrutura"];

					$this->caderno_cci_model->estrutura_update_pai($result, $args);
				}
			}

			$this->caderno_cci_model->estrutura_calculo($result, $args);
			$collection = $result->result_array();

			$arr = array();

			foreach($collection as $key => $item)
			{
				if(isset($item["calculo"]))
				{
					$calculo = json_decode($item["calculo"], true);

					foreach($calculo["sub"] as $key2 => $item2)
	    			{
	    				if(trim($item2["referencia"]) == "rentabilidade")
	    				{
	    					$args["cd_caderno_cci_estrutura_referencia"] = $item2["codigo"];

	    					$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							$calculo["sub"][$key2]["codigo"] = $row["cd_caderno_cci_estrutura"];

	    				}
	    				else if(trim($item2["referencia"]) == "indice")
	    				{
	    					$args["cd_caderno_cci_indice_referencia"] = $item2["codigo"];

	    					$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							$calculo["sub"][$key2]["codigo"] = $row["cd_caderno_cci_indice"];
	    				}
	    			}

	    			$args["cd_caderno_cci_estrutura"] = $item["cd_caderno_cci_estrutura"];
	    			$args["calculo"]                  = json_encode($calculo);

	    			$this->caderno_cci_model->estrutura_update_calculo($result, $args);
    			}
			}

			$this->caderno_cci_model->grafico_listar($result, $args);
			$collection = $result->result_array();

			foreach($collection as $key => $item)
			{
				$parametro = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				$participacao    = array();
				$participacao_m2 = array();

				$cor = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				$ordem = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				$negrito = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				$linha = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				$tab = array(
					"projetado"     => array(),
					"indice"        => array(),
					"rentabilidade" => array(),
					"benchmark"     => array()
				);

				if((isset($item["parametro"])) AND ($item["parametro"]))
				{
					$arr_parametro = json_decode($item["parametro"], true);

					if((isset($arr_parametro["projetado"])) AND  (count($arr_parametro["projetado"]) > 0))
					{
						foreach($arr_parametro["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $item2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();

							if(count($cor) > 0)
							{
								$parametro["projetado"][] = $row["cd_caderno_cci_projetado"];
							}
						}
					}

					if((isset($arr_parametro["indice"])) AND  (count($arr_parametro["indice"]) > 0))
					{
						foreach($arr_parametro["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $item2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($cor) > 0)
							{
								$parametro["indice"][] = $row["cd_caderno_cci_indice"];
							}
						}
					}

					if((isset($arr_parametro["benchmark"])) AND  (count($arr_parametro["benchmark"]) > 0))
					{
						foreach($arr_parametro["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $item2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($cor) > 0)
							{
								$parametro["benchmark"][] = $row["cd_caderno_cci_benchmark"];
							}
						}
					}

					if((isset($arr_parametro["rentabilidade"])) AND  (count($arr_parametro["rentabilidade"]) > 0))
					{
						foreach($arr_parametro["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $item2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($cor) > 0)
							{
								$parametro["rentabilidade"][] = $row["cd_caderno_cci_estrutura"];
							}
						}
					}
				}

				if((isset($item["participacao"])) AND ($item["participacao"]))
				{
					$arr_participacao = json_decode($item["participacao"], true);

					if(count($arr_participacao) > 0)
					{
						foreach($arr_participacao as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row_key2 = $result->row_array();

							$args["cd_caderno_cci_estrutura_referencia"] = $item2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row_item2 = $result->row_array();
							
							if((count($row_key2) > 0) AND (count($row_item2)))
							{
								$participacao[$row_key2["cd_caderno_cci_estrutura"]] = $row_item2["cd_caderno_cci_estrutura"];
							}
						}
					}
				}

				if((isset($item["participacao_m2"])) AND ($item["participacao_m2"]))
				{
					$arr_participacao_m2 = json_decode($item["participacao_m2"], true);

					if(count($arr_participacao_m2) > 0)
					{
						foreach($arr_participacao_m2 as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row_key2 = $result->row_array();

							$args["cd_caderno_cci_estrutura_referencia"] = $item2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row_item2 = $result->row_array();
							
							if((count($row_key2) > 0) AND (count($row_item2)))
							{
								$arr_participacao_m2[$row_key2["cd_caderno_cci_estrutura"]] = $row_item2["cd_caderno_cci_estrutura"];
							}
						}
					}
				}

				if((isset($item["cor"])) AND ($item["cor"]))
				{
					$arr_cor = json_decode($item["cor"], true);
					
					if((isset($arr_cor["projetado"])) AND  (count($arr_cor["projetado"]) > 0))
					{
						foreach($arr_cor["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $key2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();
							
							if(count($row) > 0)
							{
								$cor["projetado"][$row["cd_caderno_cci_projetado"]] = $item2;
							}
						}
					}

					if((isset($arr_cor["indice"])) AND  (count($arr_cor["indice"]) > 0))
					{
						foreach($arr_cor["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $key2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$cor["indice"][$row["cd_caderno_cci_indice"]] = $item2;
							}							
						}
					}

					if((isset($arr_cor["benchmark"])) AND  (count($arr_cor["benchmark"]) > 0))
					{
						foreach($arr_cor["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $key2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$cor["benchmark"][$row["cd_caderno_cci_benchmark"]] = $item2;
							}	
						}
					}

					if((isset($arr_cor["rentabilidade"])) AND  (count($arr_cor["rentabilidade"]) > 0))
					{
						foreach($arr_cor["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$cor["rentabilidade"][$row["cd_caderno_cci_estrutura"]] = $item2;
							}
						}
					}
				}

				if((isset($item["ordem"])) AND ($item["ordem"]))
				{
					$arr_ordem = json_decode($item["ordem"], true);

					if((isset($arr_ordem["projetado"])) AND  (count($arr_ordem["projetado"]) > 0))
					{
						foreach($arr_ordem["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $key2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$ordem["projetado"][$row["cd_caderno_cci_projetado"]] = $item2;
							}
						}
					}

					if((isset($arr_ordem["indice"])) AND  (count($arr_ordem["indice"]) > 0))
					{
						foreach($arr_ordem["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $key2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$ordem["indice"][$row["cd_caderno_cci_indice"]] = $item2;
							}
						}
					}

					if((isset($arr_ordem["benchmark"])) AND  (count($arr_ordem["benchmark"]) > 0))
					{
						foreach($arr_ordem["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $key2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$ordem["benchmark"][$row["cd_caderno_cci_benchmark"]] = $item2;
							}
						}
					}

					if((isset($arr_ordem["rentabilidade"])) AND  (count($arr_ordem["rentabilidade"]) > 0))
					{
						foreach($arr_ordem["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$ordem["rentabilidade"][$row["cd_caderno_cci_estrutura"]] = $item2;
							}
						}
					}
				}

				if((isset($item["negrito"])) AND ($item["negrito"]))
				{
					$arr_negrito = json_decode($item["negrito"], true);

					if((isset($arr_negrito["projetado"])) AND  (count($arr_negrito["projetado"]) > 0))
					{
						foreach($arr_negrito["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $key2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$negrito["projetado"][$row["cd_caderno_cci_projetado"]] = $item2;
							}
						}
					}

					if((isset($arr_negrito["indice"])) AND  (count($arr_negrito["indice"]) > 0))
					{
						foreach($arr_negrito["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $key2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$negrito["indice"][$row["cd_caderno_cci_indice"]] = $item2;
							}
						}
					}

					if((isset($arr_negrito["benchmark"])) AND  (count($arr_negrito["benchmark"]) > 0))
					{
						foreach($arr_negrito["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $key2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$negrito["benchmark"][$row["cd_caderno_cci_benchmark"]] = $item2;
							}
						}
					}

					if((isset($arr_negrito["rentabilidade"])) AND  (count($arr_negrito["rentabilidade"]) > 0))
					{
						foreach($arr_negrito["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$negrito["rentabilidade"][$row["cd_caderno_cci_estrutura"]] = $item2;
							}
						}
					}
				}

				if((isset($item["linha"])) AND ($item["linha"]))
				{
					$arr_linha = json_decode($item["linha"], true);

					if((isset($arr_linha["projetado"])) AND  (count($arr_linha["projetado"]) > 0))
					{
						foreach($arr_linha["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $key2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$linha["projetado"][$row["cd_caderno_cci_projetado"]] = $item2;
							}
						}
					}

					if((isset($arr_linha["indice"])) AND  (count($arr_linha["indice"]) > 0))
					{
						foreach($arr_linha["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $key2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$linha["indice"][$row["cd_caderno_cci_indice"]] = $item2;
							}
						}
					}

					if((isset($arr_linha["benchmark"])) AND  (count($arr_linha["benchmark"]) > 0))
					{
						foreach($arr_linha["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $key2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$linha["benchmark"][$row["cd_caderno_cci_benchmark"]] = $item2;
							}
						}
					}

					if((isset($arr_linha["rentabilidade"])) AND  (count($arr_linha["rentabilidade"]) > 0))
					{
						foreach($arr_linha["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$linha["rentabilidade"][$row["cd_caderno_cci_estrutura"]] = $item2;
							}
						}
					}
				}

				if((isset($item["tab"])) AND ($item["tab"]))
				{
					$arr_tab = json_decode($item["tab"], true);

					if((isset($arr_tab["projetado"])) AND  (count($arr_tab["projetado"]) > 0))
					{
						foreach($arr_tab["projetado"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_projetado_referencia"] = $key2;

							$this->caderno_cci_model->projetado_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$tab["projetado"][$row["cd_caderno_cci_projetado"]] = $item2;
							}
						}
					}

					if((isset($arr_tab["indice"])) AND  (count($arr_tab["indice"]) > 0))
					{
						foreach($arr_tab["indice"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_indice_referencia"] = $key2;

							$this->caderno_cci_model->indice_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$tab["indice"][$row["cd_caderno_cci_indice"]] = $item2;
							}
						}
					}

					if((isset($arr_tab["benchmark"])) AND  (count($arr_tab["benchmark"]) > 0))
					{
						foreach($arr_tab["benchmark"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_benchmark_referencia"] = $key2;

							$this->caderno_cci_model->benchmark_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$tab["benchmark"][$row["cd_caderno_cci_benchmark"]] = $item2;
							}
						}
					}

					if((isset($arr_tab["rentabilidade"])) AND  (count($arr_tab["rentabilidade"]) > 0))
					{
						foreach($arr_tab["rentabilidade"] as $key2 => $item2) 
						{
							$args["cd_caderno_cci_estrutura_referencia"] = $key2;

							$this->caderno_cci_model->estrutura_referencia($result, $args);
							$row = $result->row_array();

							if(count($row) > 0)
							{
								$tab["rentabilidade"][$row["cd_caderno_cci_estrutura"]] = $item2;
							}
						}
					}
				}

				$args = array(
					"cd_caderno_cci_grafico" => $item["cd_caderno_cci_grafico"],
					"parametro"              => json_encode($parametro),
					"participacao"           => json_encode($participacao),
					"participacao_m2"        => json_encode($participacao_m2),
					"cor"                    => json_encode($cor),
					"ordem"                  => json_encode($ordem),
					"negrito"                => json_encode($negrito),
					"linha"                  => json_encode($linha),
					"tab"                    => json_encode($tab)
				);

				$this->caderno_cci_model->grafico_update($result, $args);
			}

			redirect("gestao/caderno_cci/projetado/".$cd_caderno_cci, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function excluir($cd_caderno_cci)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"] = $cd_caderno_cci;
			$args["cd_usuario"]     = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->excluir($result, $args);
			
			redirect("gestao/caderno_cci", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO ANO END -------------------------------------------------------------- */  

	/* CADASTRO PROJETADO ------------------------------------------------------------ */

	function projetado($cd_caderno_cci, $cd_caderno_cci_projetado = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"]           = $cd_caderno_cci;
			$args["cd_caderno_cci_projetado"] = $cd_caderno_cci_projetado;
			
			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if(intval($args["cd_caderno_cci_projetado"]) == 0)
			{
				$data["projetado"] = array(
					"cd_caderno_cci_projetado" => intval($cd_caderno_cci_projetado),
					"cd_caderno_cci"           => intval($cd_caderno_cci),
					"nr_ordem"                 => "",
					"ds_caderno_cci_projetado" => "",
					"nr_projetado"             => ""
				);
			}
			else
			{
				$this->caderno_cci_model->projetado($result, $args);
				$data["projetado"] = $result->row_array();
			}
			
			$this->load->view("gestao/caderno_cci/projetado", $data);

		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function projetado_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci_projetado"] = $this->input->post("cd_caderno_cci_projetado", TRUE);
			$args["ds_caderno_cci_projetado"] = $this->input->post("ds_caderno_cci_projetado", TRUE);
			$args["cd_caderno_cci"]           = $this->input->post("cd_caderno_cci", TRUE);
			$args["nr_ordem"]                 = $this->input->post("nr_ordem", TRUE);
			$args["nr_projetado"]             = $this->input->post("nr_projetado", TRUE);
			$args["cd_usuario"]               = $this->session->userdata("codigo");

			$this->caderno_cci_model->projetado_salvar($result, $args);
			
			redirect("gestao/caderno_cci/projetado/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function projetado_listar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);
			
			$this->caderno_cci_model->projetado_listar($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("gestao/caderno_cci/projetado_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function projetado_excluir($cd_caderno_cci, $cd_caderno_cci_projetado)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]           = $cd_caderno_cci;
			$args["cd_caderno_cci_projetado"] = $cd_caderno_cci_projetado;
			$args["cd_usuario"]               = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->projetado_excluir($result, $args);
			
			redirect("gestao/caderno_cci/projetado/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO PROJETADO END -------------------------------------------------------- */ 

	/* CADASTRO ESTRUTURA ------------------------------------------------------------ */  

	function estrutura($cd_caderno_cci, $cd_caderno_cci_estrutura = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"]           = $cd_caderno_cci;
			$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;
			
			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$this->caderno_cci_model->estrutura_pai($result, $args);
			$rentabilidade = $result->result_array();

			$data["estrutura_pai"] = array();

			$i = 0;

			foreach($rentabilidade as $item)
			{
				$arr = array();

				$data["estrutura_pai"][] = array(
					"i"        => $i,
					"nr_ordem" => $item["nr_ordem"],
					"value"    => $item["cd_caderno_cci_estrutura"], 
					"text"     => $item["ds_caderno_cci_estrutura"] 
				);

				$i++;

				$this->recursividade_estrutura($cd_caderno_cci, $item["cd_caderno_cci_estrutura"], $arr, "S");

				foreach($arr as $item2)
				{
					$data["estrutura_pai"][] = array(
						"i"        => $i,
						"nr_ordem" => $item2["nr_ordem"],
						"value"    => $item2["cd_caderno_cci_estrutura"], 
						"text"     => $item2["ds_caderno_cci_estrutura"]
					);

					$i++;
				}	
			}

			foreach($data["estrutura_pai"] as $key => $item)
			{
				$ordem = "";

				$args["cd_caderno_cci_estrutura"] = $item["value"];

				while(intval($args["cd_caderno_cci_estrutura"]) > 0)
				{
					$this->caderno_cci_model->estrutura_ordem($result, $args);
					$row = $result->row_array();

					$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

					$ordem = $row["nr_ordem"].($ordem != "" ? ".".$ordem : ""); 

				}

				$data["estrutura_pai"][$key]["text"] = $ordem." - ".$item["text"];
			}

			if(intval($cd_caderno_cci_estrutura) == 0)
			{
				$data["estrutura"] = array(
					"cd_caderno_cci_estrutura"     => intval($cd_caderno_cci_estrutura),
					"cd_caderno_cci"               => intval($cd_caderno_cci),
					"ds_caderno_cci_estrutura"     => "",
					"nr_politica_max"              => "",
					"nr_politica_min"              => "",
					"nr_legal_max"                 => "",
					"nr_legal_min"                 => "",
					"nr_rentabilidade"             => "",
					"cd_caderno_cci_estrutura_pai" => "",
					"fl_grupo"                     => "",
					"fl_agrupar"                   => "",
					"nr_ordem"                     => "",
					"fl_campo_metro"               => "",
					"fl_campo_quantidade"          => "",
					"fl_fundo"                     => ""
				);
			}
			else
			{
				$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;

				$this->caderno_cci_model->estrutura($result, $args);
				$data["estrutura"] = $result->row_array();
			}
			
			$this->load->view("gestao/caderno_cci/estrutura", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function estrutura_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci_estrutura"]     = $this->input->post("cd_caderno_cci_estrutura", TRUE);
			$args["ds_caderno_cci_estrutura"]     = $this->input->post("ds_caderno_cci_estrutura", TRUE);
			$args["cd_caderno_cci"]               = $this->input->post("cd_caderno_cci", TRUE);
			$args["nr_politica_max"]              = app_decimal_para_db($this->input->post("nr_politica_max", TRUE));
			$args["nr_politica_min"]              = app_decimal_para_db($this->input->post("nr_politica_min", TRUE));
			$args["nr_legal_max"]                 = app_decimal_para_db($this->input->post("nr_legal_max", TRUE));
			$args["nr_legal_min"]                 = app_decimal_para_db($this->input->post("nr_legal_min", TRUE));
			$args["nr_rentabilidade"]             = app_decimal_para_db($this->input->post("nr_rentabilidade", TRUE));
			$args["cd_caderno_cci_estrutura_pai"] = $this->input->post("cd_caderno_cci_estrutura_pai", TRUE);
			$args["fl_grupo"]                     = $this->input->post("fl_grupo", TRUE);
			$args["fl_agrupar"]                   = $this->input->post("fl_agrupar", TRUE);
			$args["nr_ordem"]                     = $this->input->post("nr_ordem", TRUE);
			$args["fl_campo_metro"]               = $this->input->post("fl_campo_metro", TRUE);
			$args["fl_campo_quantidade"]          = $this->input->post("fl_campo_quantidade", TRUE);
			$args["fl_fundo"]                     = $this->input->post("fl_fundo", TRUE);
			$args["fl_total"]                     = "N";
			$args["cd_usuario"]                   = $this->session->userdata("codigo");

			$this->caderno_cci_model->estrutura_salvar($result, $args);
			
			redirect("gestao/caderno_cci/estrutura/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function estrutura_listar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);

			$args["calculo"] = "N";

			$this->caderno_cci_model->estrutura_pai_principal($result, $args);
			$rentabilidade = $result->result_array();

			$i = 0;

			foreach($rentabilidade as $item)
			{
				$arr = array();

				$collection[] = $item;

				$i++;

				$this->recursividade_estrutura($args["cd_caderno_cci"], $item["cd_caderno_cci_estrutura"], $arr, "");

				foreach($arr as $item2)
				{
					$collection[] = $item2;

					$i++;
				}	
			}

			foreach($collection as $key => $item)
			{
				$data["collection"][$key] = $item;

				$cd_caderno_cci_estrutura_pai = $item["cd_caderno_cci_estrutura_pai"];

				$ordem = "";

				$args["cd_caderno_cci_estrutura"] = $item["cd_caderno_cci_estrutura_pai"];

				while(intval($args["cd_caderno_cci_estrutura"]) > 0)
				{
					$this->caderno_cci_model->estrutura_ordem($result, $args);
					$row = $result->row_array();

					$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

					$ordem = $row["nr_ordem"].".".$ordem; 
				}

				$ordem .= $item["nr_ordem"];

				$data["collection"][$key]["nr_ordem"] = $ordem;

			}
			
			$this->load->view("gestao/caderno_cci/estrutura_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function estrutura_excluir($cd_caderno_cci, $cd_caderno_cci_estrutura)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]           = $cd_caderno_cci;
			$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;
			$args["cd_usuario"]               = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->estrutura_excluir($result, $args);
			
			redirect("gestao/caderno_cci/estrutura/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO ESTRUTURA END -------------------------------------------------------- */

	/* CADASTRO ESTRUTURA VALOR ------------------------------------------------------ */ 

	private function recursividade_estrutura($cd_caderno_cci, $cd_caderno_cci_estrutura_pai, &$array = array(), $fl_grupo = "")
	{
		$result = null;
		$args   = Array();

		$args["cd_caderno_cci"]               = $cd_caderno_cci;
		$args["cd_caderno_cci_estrutura_pai"] = $cd_caderno_cci_estrutura_pai;
		$args["fl_grupo"]                     = $fl_grupo;

		$this->caderno_cci_model->estrutura_listar($result, $args);
		$collection = $result->result_array();

		$i = count($array);

		foreach($collection as $key => $item)
		{
			$array[$i] = $item;

			$i++;

			$i = $this->recursividade_estrutura($cd_caderno_cci, $item["cd_caderno_cci_estrutura"], $array);	
		}
	
		return $i;
	}

	function estrutura_valor($cd_caderno_cci, $mes = "", $cd_caderno_cci_estrutura_pai = "")
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$data["row"]["mes"] = $mes;

			$data["mes"] = $this->get_mes();

			$this->caderno_cci_model->estrutura_pai_principal($result, $args);
			$collection = $result->result_array();

			$data["estrutura_aba"] = array();

			$fl_active = false;

			foreach($collection as $key => $item)
			{
				if(((!$fl_active) AND (intval($cd_caderno_cci_estrutura_pai) == 0)) OR (intval($cd_caderno_cci_estrutura_pai) == $item["cd_caderno_cci_estrutura"]))
				{
					$fl_active = true;

					$cd_caderno_cci_estrutura_pai = $item["cd_caderno_cci_estrutura"];
				}
				else
				{
					$fl_active = false;
				}

				$data["estrutura_aba"][] = array("aba_estrutura_".$item["cd_caderno_cci_estrutura"], $item["ds_caderno_cci_estrutura"], $fl_active, "ir_estrutura_pai(".intval($item["cd_caderno_cci_estrutura"]).");");
			}

			$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura_pai;

			$collection = array();

			$this->caderno_cci_model->estrutura($result, $args);
			$row = $result->row_array();

			$collection[0] = $row;

			$this->recursividade_estrutura($cd_caderno_cci, $cd_caderno_cci_estrutura_pai, $collection);

			$data["collection"] = array();

			$args["mes"] = $mes;

			foreach($collection as $key => $item)
			{
				$data["collection"][$key] = $item;

				$ordem = "";

				$args["cd_caderno_cci_estrutura"] = $item["cd_caderno_cci_estrutura"];

				$this->caderno_cci_model->estrutura_valor($result, $args);
				$row = $result->row_array();

				$data["collection"][$key]["nr_valor_atual"]                 = (count($row) > 0 ? $row["nr_valor_atual"] : 0);
				$data["collection"][$key]["nr_fluxo"]                       = (count($row) > 0 ? $row["nr_fluxo"] : 0);
				$data["collection"][$key]["nr_rentabilidade"]               = (count($row) > 0 ? $row["nr_rentabilidade"] : 0);
				$data["collection"][$key]["nr_realizado"]                   = (count($row) > 0 ? $row["nr_realizado"] : 0);
				$data["collection"][$key]["cd_caderno_cci_estrutura_valor"] = (count($row) > 0 ? $row["cd_caderno_cci_estrutura_valor"] : 0);
				$data["collection"][$key]["fl_campo_metro"]                 = $item["fl_campo_metro"];
				$data["collection"][$key]["fl_campo_quantidade"]            = $item["fl_campo_quantidade"];
				$data["collection"][$key]["fl_fundo"]                       = $item["fl_fundo"];
				$data["collection"][$key]["nr_metro"]                       = (count($row) > 0 ? $row["nr_metro"] : 0);
				$data["collection"][$key]["nr_quantidade"]                  = (count($row) > 0 ? $row["nr_quantidade"] : 0);
				$data["collection"][$key]["nr_valor_integralizar"]          = (count($row) > 0 ? $row["nr_valor_integralizar"] : 0);
				$data["collection"][$key]["nr_taxa_adm"]                    = (count($row) > 0 ? $row["nr_taxa_adm"] : 0);
				$data["collection"][$key]["nr_ano_vencimento"]              = (count($row) > 0 ? $row["nr_ano_vencimento"] : 0);
				$data["collection"][$key]["nr_participacao_fundo"]          = (count($row) > 0 ? $row["nr_participacao_fundo"] : 0);

				while(intval($args["cd_caderno_cci_estrutura"]) > 0)
				{
					$this->caderno_cci_model->estrutura_ordem($result, $args);
					$row = $result->row_array();

					$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

					$ordem = $row["nr_ordem"].(trim($ordem) != "" ? ".".$ordem : ""); 
				}

				$data["collection"][$key]["ds_caderno_cci_estrutura"] = $ordem." - ".$item["ds_caderno_cci_estrutura"];
			}

			array_sort_by_column($data["collection"], "ds_caderno_cci_estrutura");
	
			$this->load->view("gestao/caderno_cci/estrutura_valor", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function estrutura_valor_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["dt_referencia"]  = "01/".$this->input->post("mes", TRUE)."/".$this->input->post("nr_ano", TRUE);
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);

			$caderno_cci_estrutura = $this->input->post("caderno_cci_estrutura", TRUE);

			$args["cd_usuario"] = $this->session->userdata("codigo");

			foreach($caderno_cci_estrutura as $key => $item)
			{
				$args["cd_caderno_cci_estrutura_valor"] = $item;
				$args["cd_caderno_cci_estrutura"]       = $key;
				$args["nr_valor_atual"]                 = $this->input->post("nr_valor_atual_".$key, TRUE);
				$args["nr_fluxo"]                       = $this->input->post("nr_fluxo_".$key, TRUE);
				$args["nr_rentabilidade"]               = $this->input->post("nr_rentabilidade_".$key, TRUE);
				$args["nr_realizado"]                   = $this->input->post("nr_realizado_".$key, TRUE);
				$args["nr_metro"]                       = $this->input->post("nr_metro_".$key, TRUE);
				$args["nr_quantidade"]                  = $this->input->post("nr_quantidade_".$key, TRUE);
				$args["nr_valor_integralizar"]          = $this->input->post("nr_valor_integralizar_".$key, TRUE);
				$args["nr_taxa_adm"]                    = $this->input->post("nr_taxa_adm_".$key, TRUE);
				$args["nr_ano_vencimento"]              = $this->input->post("nr_ano_vencimento_".$key, TRUE);
				$args["nr_participacao_fundo"]          = $this->input->post("nr_participacao_fundo_".$key, TRUE);

				$this->caderno_cci_model->estrutura_valor_salvar($result, $args);
			}	
			
			redirect("gestao/caderno_cci/estrutura/".$args["cd_caderno_cci"], "refresh");
			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO ESTRUTURA VALOR END -------------------------------------------------- */ 

	/* CADASTRO INDICE --------------------------------------------------------------- */

	function indice($cd_caderno_cci, $cd_caderno_cci_indice = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"]        = $cd_caderno_cci;
			$args["cd_caderno_cci_indice"] = $cd_caderno_cci_indice;
			
			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if(intval($args["cd_caderno_cci_indice"]) == 0)
			{
				$data["indice"] = array(
					"cd_caderno_cci_indice" => intval($cd_caderno_cci_indice),
					"cd_caderno_cci"        => intval($cd_caderno_cci),
					"nr_ordem"              => "",
					"ds_caderno_cci_indice" => ""
				);
			}
			else
			{
				$this->caderno_cci_model->indice($result, $args);
				$data["indice"] = $result->row_array();
			}
			
			$this->load->view("gestao/caderno_cci/indice", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function indice_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci_indice"] = $this->input->post("cd_caderno_cci_indice", TRUE);
			$args["ds_caderno_cci_indice"] = $this->input->post("ds_caderno_cci_indice", TRUE);
			$args["cd_caderno_cci"]        = $this->input->post("cd_caderno_cci", TRUE);
			$args["nr_ordem"]              = $this->input->post("nr_ordem", TRUE);
			$args["cd_usuario"]            = $this->session->userdata("codigo");

			$this->caderno_cci_model->indice_salvar($result, $args);
			
			redirect("gestao/caderno_cci/indice/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function indice_listar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);
			
			$this->caderno_cci_model->indice_listar($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("gestao/caderno_cci/indice_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function indice_excluir($cd_caderno_cci, $cd_caderno_cci_indice)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]        = $cd_caderno_cci;
			$args["cd_caderno_cci_indice"] = $cd_caderno_cci_indice;
			$args["cd_usuario"]            = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->indice_excluir($result, $args);
			
			redirect("gestao/caderno_cci/indice/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO INDICE END ----------------------------------------------------------- */

	/* CADASTRO INDICE VALOR --------------------------------------------------------- */ 
	
	function indice_valor($cd_caderno_cci, $mes = "")
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$data["row"]["mes"] = $mes;

			$ano = $data["row"]["nr_ano"];

			$data["mes"] = $this->get_mes();

			$this->caderno_cci_model->indice_listar($result, $args);
			$collection = $result->result_array();

			$data["collection"] = array();

			$args["mes"] = $mes;

			$data["fl_sgs"] = false;

			foreach($collection as $key => $item)
			{
				$data["collection"][$key] = $item;

				$args["cd_caderno_cci_indice"] = $item["cd_caderno_cci_indice"];

				$this->caderno_cci_model->indice_valor($result, $args);
				$row = $result->row_array();

				$data["collection"][$key]["nr_indice"]                   = 0;
				$data["collection"][$key]["cd_caderno_cci_indice_valor"] = 0;

				if(count($row) > 0)
				{
					$data["collection"][$key]["nr_indice"]                   = $row["nr_indice"];
					$data["collection"][$key]["cd_caderno_cci_indice_valor"] = $row["cd_caderno_cci_indice_valor"];
				}
			}

			$this->load->view("gestao/caderno_cci/indice_valor", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function indice_valor_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["dt_referencia"]  = "01/".$this->input->post("mes", TRUE)."/".$this->input->post("nr_ano", TRUE);
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);

			$caderno_cci_indice = $this->input->post("caderno_cci_indice", TRUE);

			$args["cd_usuario"] = $this->session->userdata("codigo");

			foreach($caderno_cci_indice as $key => $item)
			{
				$args["cd_caderno_cci_indice_valor"] = $item;
				$args["cd_caderno_cci_indice"]       = $key;
				$args["nr_indice"]                   = $this->input->post("caderno_cci_indice_".$key, TRUE);

				$this->caderno_cci_model->indice_valor_salvar($result, $args);
			}	
			
			redirect("gestao/caderno_cci/indice/".$args["cd_caderno_cci"], "refresh");
			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function get_valores()
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);
		$nr_ano                 = $this->input->post("nr_ano", TRUE);
		$nr_mes                 = $this->input->post("nr_mes", TRUE);

		$this->caderno_cci_model->indice_listar($result, $args);
		$collection = $result->result_array();

		$result = array();

		$i = 0;

		foreach ($collection as $key => $item) 
		{
			$result[$i] = $this->web_service_sgs($item["cd_sgs"], $nr_mes, $nr_ano);

			$result[$i]["cd_caderno_cci_indice"] = $item["cd_caderno_cci_indice"];

			$i++;
		}

		echo json_encode($result);
	}

	/* CADASTRO INDICE VALOR END ----------------------------------------------------- */ 

	/* CADASTRO BENCHMARK ------------------------------------------------------------ */

	function benchmark($cd_caderno_cci, $cd_caderno_cci_benchmark = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"]        = $cd_caderno_cci;
			$args["cd_caderno_cci_benchmark"] = $cd_caderno_cci_benchmark;
			
			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if(intval($args["cd_caderno_cci_benchmark"]) == 0)
			{
				$data["benchmark"] = array(
					"cd_caderno_cci_benchmark" => intval($cd_caderno_cci_benchmark),
					"cd_caderno_cci"           => intval($cd_caderno_cci),
					"nr_ordem"                 => "",
					"ds_caderno_cci_benchmark" => ""
				);
			}
			else
			{
				$this->caderno_cci_model->benchmark($result, $args);
				$data["benchmark"] = $result->row_array();
			}
			
			$this->load->view("gestao/caderno_cci/benchmark", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function benchmark_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci_benchmark"] = $this->input->post("cd_caderno_cci_benchmark", TRUE);
			$args["ds_caderno_cci_benchmark"] = $this->input->post("ds_caderno_cci_benchmark", TRUE);
			$args["cd_caderno_cci"]           = $this->input->post("cd_caderno_cci", TRUE);
			$args["nr_ordem"]                 = $this->input->post("nr_ordem", TRUE);
			$args["cd_usuario"]               = $this->session->userdata("codigo");

			$this->caderno_cci_model->benchmark_salvar($result, $args);
			
			redirect("gestao/caderno_cci/benchmark/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function benchmark_listar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);
			
			$this->caderno_cci_model->benchmark_listar($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("gestao/caderno_cci/benchmark_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function benchmark_excluir($cd_caderno_cci, $cd_caderno_cci_benchmark)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]           = $cd_caderno_cci;
			$args["cd_caderno_cci_benchmark"] = $cd_caderno_cci_benchmark;
			$args["cd_usuario"]               = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->benchmark_excluir($result, $args);
			
			redirect("gestao/caderno_cci/benchmark/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	/* CADASTRO BENCHMARK END -------------------------------------------------------- */ 

	/* CADASTRO BENCHMARK VALOR ------------------------------------------------------ */ 

	function benchmark_valor($cd_caderno_cci, $mes = "")
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$data["row"]["mes"] = $mes;

			$data["mes"] = $this->get_mes();

			$this->caderno_cci_model->benchmark_listar($result, $args);
			$collection = $result->result_array();

			$data["collection"] = array();

			$args["mes"] = $mes;

			foreach($collection as $key => $item)
			{
				$data["collection"][$key] = $item;

				$args["cd_caderno_cci_benchmark"] = $item["cd_caderno_cci_benchmark"];

				$this->caderno_cci_model->benchmark_valor($result, $args);
				$row = $result->row_array();

				$data["collection"][$key]["nr_benchmark"] = (count($row) > 0 ? $row["nr_benchmark"] : 0);
				$data["collection"][$key]["cd_caderno_cci_benchmark_valor"] = (count($row) > 0 ? $row["cd_caderno_cci_benchmark_valor"] : 0);
			}

			$this->load->view("gestao/caderno_cci/benchmark_valor", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function benchmark_valor_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["dt_referencia"]  = "01/".$this->input->post("mes", TRUE)."/".$this->input->post("nr_ano", TRUE);
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);

			$caderno_cci_benchmark = $this->input->post("caderno_cci_benchmark", TRUE);

			$args["cd_usuario"] = $this->session->userdata("codigo");

			foreach($caderno_cci_benchmark as $key => $item)
			{
				$args["cd_caderno_cci_benchmark_valor"] = $item;
				$args["cd_caderno_cci_benchmark"]       = $key;
				$args["nr_benchmark"]                   = $this->input->post("caderno_cci_benchmark_".$key, TRUE);

				$this->caderno_cci_model->benchmark_valor_salvar($result, $args);
			}	
			
			redirect("gestao/caderno_cci/benchmark/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico($cd_caderno_cci, $cd_caderno_cci_grafico = 0)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]         = $cd_caderno_cci;
			$args["cd_caderno_cci_grafico"] = $cd_caderno_cci_grafico;

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$this->caderno_cci_model->indice_listar($result, $args);
			$indice = $result->result_array();

			foreach($indice as $item)
			{
				$data["indice"][] = array("value" => $item["cd_caderno_cci_indice"], "text" => $item["ds_caderno_cci_indice"]);
			}

			$args["calculo"] = "N";

			$this->caderno_cci_model->estrutura_pai_principal($result, $args);
			$rentabilidade = $result->result_array();

			$i = 0;

			foreach($rentabilidade as $item)
			{
				$arr = array();

				$data["rentabilidade"][] = array(
					"i"        => $i,
					"nr_ordem" => $item["nr_ordem"],
					"value"    => $item["cd_caderno_cci_estrutura"], 
					"text"     => $item["ds_caderno_cci_estrutura"] 
				);

				$i++;

				$this->recursividade_estrutura($cd_caderno_cci, $item["cd_caderno_cci_estrutura"], $arr);

				foreach($arr as $item2)
				{
					$data["rentabilidade"][] = array(
						"i"        => $i,
						"nr_ordem" => $item2["nr_ordem"],
						"value"    => $item2["cd_caderno_cci_estrutura"], 
						"text"     => $item2["ds_caderno_cci_estrutura"]
					);

					$i++;
				}	
			}

			foreach($data["rentabilidade"] as $key => $item)
			{
				$ordem = "";

				$args["cd_caderno_cci_estrutura"] = $item["value"];

				while(intval($args["cd_caderno_cci_estrutura"]) > 0)
				{
					$this->caderno_cci_model->estrutura_ordem($result, $args);
					$row = $result->row_array();

					$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

					$ordem = $row["nr_ordem"].($ordem != "" ? ".".$ordem : ""); 

				}

				$data["rentabilidade"][$key]["text"] = $ordem." - ".$item["text"];
			}

			$this->caderno_cci_model->benchmark_listar($result, $args);
			$benchmark = $result->result_array();

			foreach($benchmark as $item)
			{
				$data["benchmark"][] = array("value" => $item["cd_caderno_cci_benchmark"], "text" => $item["ds_caderno_cci_benchmark"]);
			}

			$this->caderno_cci_model->projetado_listar($result, $args);
			$projetado = $result->result_array();

			foreach($projetado as $item)
			{
				$data["projetado"][] = array("value" => $item["cd_caderno_cci_projetado"], "text" => $item["ds_caderno_cci_projetado"]);
			}

			if(intval($args["cd_caderno_cci_grafico"]) == 0)
			{
				$data["grafico"] = array(
					"cd_caderno_cci_grafico" => intval($cd_caderno_cci_grafico),
					"cd_caderno_cci"         => intval($cd_caderno_cci),
					"ds_caderno_cci_grafico" => "",
					"nr_ordem"               => "",
					"tp_grafico"             => "",
					"projetado"              => array(),
					"indice"                 => array(),
					"rentabilidade"          => array(),
					"benchmark"              => array(),
					"nota_rodape"            => "",
					"fl_ano"                 => "",
					"fl_mes"                 => ""
				);
			}
			else
			{
				$this->caderno_cci_model->grafico($result, $args);
				$data["grafico"] = $result->row_array();

				$arr = json_decode($data["grafico"]["parametro"], true);

				$data["grafico"]["projetado"]     = $arr["projetado"];
				$data["grafico"]["indice"]        = $arr["indice"];
				$data["grafico"]["rentabilidade"] = $arr["rentabilidade"];
				$data["grafico"]["benchmark"]     = $arr["benchmark"];
			}

			$this->load->view("gestao/caderno_cci/grafico", $data);

		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_listar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $this->input->post("cd_caderno_cci", TRUE);
			
			$this->caderno_cci_model->grafico_listar($result, $args);
			$data["collection"] = $result->result_array();
			
			$this->load->view("gestao/caderno_cci/grafico_result", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci_grafico"] = $this->input->post("cd_caderno_cci_grafico", TRUE);
			$args["ds_caderno_cci_grafico"] = $this->input->post("ds_caderno_cci_grafico", TRUE);
			$args["tp_grafico"]             = $this->input->post("tp_grafico", TRUE);
			$args["nr_ordem"]               = $this->input->post("nr_ordem", TRUE);
			$args["cd_caderno_cci"]         = $this->input->post("cd_caderno_cci", TRUE);
			$args["nota_rodape"]            = $this->input->post("nota_rodape", TRUE);
			$args["fl_ano"]                 = $this->input->post("fl_ano", TRUE);
			$args["fl_mes"]                 = $this->input->post("fl_mes", TRUE);
			$args["cd_usuario"]             = $this->session->userdata("codigo");

			$arr_projetado     = (is_array($this->input->post("arr_projetado", TRUE)) ? $this->input->post("arr_projetado", TRUE) : array());
			$arr_rentabilidade = (is_array($this->input->post("arr_rentabilidade", TRUE)) ? $this->input->post("arr_rentabilidade", TRUE) : array());
			$arr_indice        = (is_array($this->input->post("arr_indice", TRUE)) ? $this->input->post("arr_indice", TRUE) : array());
			$arr_benchmark     = (is_array($this->input->post("arr_benchmark", TRUE)) ? $this->input->post("arr_benchmark", TRUE) : array());

			$arr = array(
				"projetado"     => $arr_projetado,
				"indice"        => $arr_indice,
				"rentabilidade" => $arr_rentabilidade,
				"benchmark"     => $arr_benchmark
			);

			$args["parametro"] = json_encode($arr);

			$this->caderno_cci_model->grafico_salvar($result, $args);
			
			redirect("gestao/caderno_cci/grafico/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_excluir($cd_caderno_cci, $cd_caderno_cci_grafico)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]         = $cd_caderno_cci;
			$args["cd_caderno_cci_grafico"] = $cd_caderno_cci_grafico;
			$args["cd_usuario"]             = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->grafico_excluir($result, $args);
			
			redirect("gestao/caderno_cci/grafico/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_salvar_ordem()
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args["cd_caderno_cci_grafico"] = $this->input->post("cd_caderno_cci_grafico", TRUE);
		$args["nr_ordem"]               = $this->input->post("nr_ordem", TRUE);
		$args["cd_usuario"]             = $this->session->userdata("codigo");

		$this->caderno_cci_model->grafico_salvar_ordem($result, $args);
	}

	function grafico_configurar($cd_caderno_cci, $cd_caderno_cci_grafico)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]         = $cd_caderno_cci;
			$args["cd_caderno_cci_grafico"] = $cd_caderno_cci_grafico;

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$this->caderno_cci_model->grafico($result, $args);
			$data["grafico"] = $result->row_array();
			
			if(trim($data["grafico"]["tp_grafico"]) == "T")
			{
				$campo           = json_decode($data["grafico"]["campo"], true);
				$participacao    = json_decode($data["grafico"]["participacao"], true);
				$participacao_m2 = json_decode($data["grafico"]["participacao_m2"], true);
				
				$data["negrito"] = json_decode($data["grafico"]["negrito"], true);
				$data["ordem"]   = json_decode($data["grafico"]["ordem"], true);
				$data["linha"]   = json_decode($data["grafico"]["linha"], true);
				$data["tab"]     = json_decode($data["grafico"]["tab"], true);

				$data["grafico"]["campo"]           = (is_array($campo) ? $campo : array());
				$data["grafico"]["participacao"]    = (is_array($participacao) ? $participacao : array());
				$data["grafico"]["participacao_m2"] = (is_array($participacao_m2) ? $participacao_m2 : array());

				$data["arr_campo"][] = array("value" => "nr_participacao",       "text" => "Part. (%)");
				$data["arr_campo"][] = array("value" => "nr_valor_atual",        "text" => "Valor");
				$data["arr_campo"][] = array("value" => "nr_valor_integralizar", "text" => "Valor a Integralizar");
				$data["arr_campo"][] = array("value" => "nr_realizado",          "text" => "Realizado");
				$data["arr_campo"][] = array("value" => "nr_fluxo",              "text" => "Fluxo de Caixa");
				$data["arr_campo"][] = array("value" => "nr_participacao_fundo", "text" => "Part. do Fundo (%)");
				$data["arr_campo"][] = array("value" => "nr_taxa_adm",           "text" => "Taxa de Adm.");
				$data["arr_campo"][] = array("value" => "nr_ano_vencimento",     "text" => "Vencimento");
				$data["arr_campo"][] = array("value" => "nr_metro",              "text" => "M²");
				$data["arr_campo"][] = array("value" => "nr_participacao_metro", "text" => "Part. M² (%)");
				$data["arr_campo"][] = array("value" => "nr_quantidade",         "text" => "Quantidade");
				$data["arr_campo"][] = array("value" => "nr_rentabilidade",      "text" => "Rentabilidade");
				$data["arr_campo"][] = array("value" => "nr_politica_max",       "text" => "Limites Política");
				$data["arr_campo"][] = array("value" => "nr_legal_max",          "text" => "Limites Legal");

				$parametro = json_decode($data["grafico"]["parametro"], true);

				$data["rentabilidade"] = array();

				$i = 0;

				$j = 1;

				foreach($parametro["rentabilidade"] as $item)
				{
					$args["cd_caderno_cci_estrutura"] = $item;

					$this->caderno_cci_model->estrutura($result, $args);
					$rentabilidade = $result->row_array();

					$data["rentabilidade"][$i] = $rentabilidade;

					$ordem = "";

					$args["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura"];

					while(intval($args["cd_caderno_cci_estrutura"]) > 0)
					{
						$this->caderno_cci_model->estrutura_ordem($result, $args);
						$row = $result->row_array();

						$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

						$ordem = $row["nr_ordem"].($ordem != "" ? ".".$ordem : ""); 
					}

					$data["rentabilidade"][$i]["ds_caderno_cci_estrutura"] = $ordem." - ".$rentabilidade["ds_caderno_cci_estrutura"];

					if($rentabilidade["fl_grupo"] == "S")
					{
						$data["arr_rentabilidade_drop"][$j] = array("value" => $data["rentabilidade"][$i]["cd_caderno_cci_estrutura"], "text" => $data["rentabilidade"][$i]["ds_caderno_cci_estrutura"]);
					
						$j++;
					}

					$i++;
				}

				$this->caderno_cci_model->estrutura_total($result, $args);
				$row = $result->row_array();

				$data["arr_rentabilidade_drop"][0] = array("value" => $row["cd_caderno_cci_estrutura"], "text" => $row["nr_ordem"]." - ".$row["ds_caderno_cci_estrutura"]);

				$i = 0;

				foreach($parametro["projetado"] as $item)
				{
					$args["cd_caderno_cci_projetado"] = $item;

					$this->caderno_cci_model->projetado($result, $args);
					$data["projetado"][$i] = $result->row_array();

					$i++;
				}

				foreach($parametro["indice"] as $item)
				{
					$args["cd_caderno_cci_indice"] = $item;

					$this->caderno_cci_model->indice($result, $args);
					$data["indice"][$i] = $result->row_array();

					$i++;
				}

				foreach($parametro["benchmark"] as $item)
				{
					$args["cd_caderno_cci_benchmark"] = $item;

					$this->caderno_cci_model->benchmark($result, $args);
					$data["benchmark"][$i] = $result->row_array();

					$i++;
				}
		
				$this->load->view("gestao/caderno_cci/grafico_configurar_tabela", $data);
			}
			elseif(trim($data["grafico"]["tp_grafico"]) == "E")
			{	
				$this->load->view("gestao/caderno_cci/grafico_configurar_texto", $data);
			}
			else
			{
				$parametro = json_decode($data["grafico"]["parametro"], true);
				
				$data["cor"] = json_decode($data["grafico"]["cor"], true);
				
				$data["projetado"]     = array();
				$data["indice"]        = array();
				$data["rentabilidade"] = array();
				$data["benchmark"]     = array();
				
				foreach($parametro["projetado"] as $item)
				{
					$args["cd_caderno_cci_projetado"] = $item;

					$this->caderno_cci_model->projetado($result, $args);
					$row = $result->row_array();

					$data["projetado"] [$item] = $row["ds_caderno_cci_projetado"];
				}

				foreach($parametro["indice"] as $item)
				{
					$args["cd_caderno_cci_indice"] = $item;

					$this->caderno_cci_model->indice($result, $args);
					$row = $result->row_array();

					$data["indice"][$item] = $row["ds_caderno_cci_indice"];
				}

				foreach($parametro["rentabilidade"] as $item)
				{
					$args["cd_caderno_cci_estrutura"] = $item;

					$this->caderno_cci_model->estrutura($result, $args);
					$row = $result->row_array();

					$data["rentabilidade"][$item] = $row["ds_caderno_cci_estrutura"];
				}

				foreach($parametro["benchmark"] as $item)
				{
					$args["cd_caderno_cci_benchmark"] = $item;

					$this->caderno_cci_model->benchmark($result, $args);
					$row = $result->row_array();

					$data["benchmark"][$item] = $row["ds_caderno_cci_benchmark"];
				}
				
				$this->load->view("gestao/caderno_cci/grafico_configurar", $data);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configurar_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]         = $this->input->post("cd_caderno_cci", TRUE);
			$args["cd_caderno_cci_grafico"] = $this->input->post("cd_caderno_cci_grafico", TRUE);

			$arr_cor = array(
				"projetado"     => array(),
				"indice"        => array(),
				"rentabilidade" => array(),
				"benchmark"     => array()
			);

			foreach($_POST as $key => $item)
			{
			 	$arr = explode("_", $key);

			 	switch (trim($arr[0])) 
			 	{
				    case "projetado":
				        $arr_cor["projetado"][$arr[1]] = $item;
				        break;
				    case "indice":
				        $arr_cor["indice"][$arr[1]] = $item;
				        break;
				    case "rentabilidade":
				        $arr_cor["rentabilidade"][$arr[1]] = $item;
				        break;
				    case "benchmark":
				        $arr_cor["benchmark"][$arr[1]] = $item;
				        break;
				}
			}

			$args["cd_usuario"] = $this->session->userdata("codigo");
			$args["cor"]        = json_encode($arr_cor);

			$this->caderno_cci_model->grafico_configurar_salvar($result, $args);

			redirect("gestao/caderno_cci/grafico/".$args["cd_caderno_cci"], "refresh");

		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configurar_tabela_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]         = $this->input->post("cd_caderno_cci", TRUE);
			$args["cd_caderno_cci_grafico"] = $this->input->post("cd_caderno_cci_grafico", TRUE);

			$arr_campo   = (is_array($this->input->post("arr_campo", TRUE)) ? $this->input->post("arr_campo", TRUE) : array());
			$arr_part    = (is_array($this->input->post("rentabilidade_part", TRUE)) ? $this->input->post("rentabilidade_part", TRUE) : array());
			$arr_partm2  = (is_array($this->input->post("rentabilidade_partm2", TRUE)) ? $this->input->post("rentabilidade_partm2", TRUE) : array());

			$projetado_ordem     = (is_array($this->input->post("projetado_ordem", TRUE)) ? $this->input->post("projetado_ordem", TRUE) : array());
			$rentabilidade_ordem = (is_array($this->input->post("rentabilidade_ordem", TRUE)) ? $this->input->post("rentabilidade_ordem", TRUE) : array());
			$indice_ordem        = (is_array($this->input->post("indice_ordem", TRUE)) ? $this->input->post("indice_ordem", TRUE) : array());
			$benchmark_ordem     = (is_array($this->input->post("benchmark_ordem", TRUE)) ? $this->input->post("benchmark_ordem", TRUE) : array());

			$ordem = array(
				"projetado"     => $projetado_ordem,
				"rentabilidade" => $rentabilidade_ordem,
				"indice"        => $indice_ordem,
				"benchmark"     => $benchmark_ordem
			);

			$projetado_negrito     = (is_array($this->input->post("projetado_negrito", TRUE)) ? $this->input->post("projetado_negrito", TRUE) : array());
			$rentabilidade_negrito = (is_array($this->input->post("rentabilidade_negrito", TRUE)) ? $this->input->post("rentabilidade_negrito", TRUE) : array());
			$indice_negrito        = (is_array($this->input->post("indice_negrito", TRUE)) ? $this->input->post("indice_negrito", TRUE) : array());
			$benchmark_negrito     = (is_array($this->input->post("benchmark_negrito", TRUE)) ? $this->input->post("benchmark_negrito", TRUE) : array());

			$negrito = array(
				"projetado"     => $projetado_negrito,
				"rentabilidade" => $rentabilidade_negrito,
				"indice"        => $indice_negrito,
				"benchmark"     => $benchmark_negrito
			);

			$projetado_linha     = (is_array($this->input->post("projetado_linha", TRUE)) ? $this->input->post("projetado_linha", TRUE) : array());
			$rentabilidade_linha = (is_array($this->input->post("rentabilidade_linha", TRUE)) ? $this->input->post("rentabilidade_linha", TRUE) : array());
			$indice_linha        = (is_array($this->input->post("indice_linha", TRUE)) ? $this->input->post("indice_linha", TRUE) : array());
			$benchmark_linha     = (is_array($this->input->post("benchmark_linha", TRUE)) ? $this->input->post("benchmark_linha", TRUE) : array());

			$linha = array(
				"projetado"     => $projetado_linha,
				"rentabilidade" => $rentabilidade_linha,
				"indice"        => $indice_linha,
				"benchmark"     => $benchmark_linha
			);

			$projetado_tab     = (is_array($this->input->post("projetado_tab", TRUE)) ? $this->input->post("projetado_tab", TRUE) : array());
			$rentabilidade_tab = (is_array($this->input->post("rentabilidade_tab", TRUE)) ? $this->input->post("rentabilidade_tab", TRUE) : array());
			$indice_tab        = (is_array($this->input->post("indice_tab", TRUE)) ? $this->input->post("indice_tab", TRUE) : array());
			$benchmark_tab     = (is_array($this->input->post("benchmark_linha", TRUE)) ? $this->input->post("benchmark_tab", TRUE) : array());

			$tab = array(
				"projetado"     => $projetado_tab,
				"rentabilidade" => $rentabilidade_tab,
				"indice"        => $indice_tab,
				"benchmark"     => $benchmark_tab
			);

			$args["campo"]           = json_encode($arr_campo);
			$args["participacao"]    = json_encode($arr_part);
			$args["participacao_m2"] = json_encode($arr_partm2);
			$args["ordem"]           = json_encode($ordem);
			$args["negrito"]         = json_encode($negrito);
			$args["linha"]           = json_encode($linha);
			$args["tab"]             = json_encode($tab);
			$args["cd_usuario"]      = $this->session->userdata("codigo");

			$this->caderno_cci_model->grafico_configurar_tabela_salvar($result, $args);

			redirect("gestao/caderno_cci/grafico/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configurar_texto_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]         = $this->input->post("cd_caderno_cci", TRUE);
			$args["cd_caderno_cci_grafico"] = $this->input->post("cd_caderno_cci_grafico", TRUE);
			$args["ds_html"]                = $this->input->post("ds_html", TRUE);
			$args["cd_usuario"]             = $this->session->userdata("codigo");

			$this->caderno_cci_model->grafico_configurar_texto_salvar($result, $args);

			redirect("gestao/caderno_cci/grafico/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}