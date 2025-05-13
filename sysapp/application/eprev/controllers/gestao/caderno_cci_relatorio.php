<?php
class Caderno_cci_relatorio extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("gestao/caderno_cci_model");
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GIN', 'DE')))
    	{
    		return TRUE;
    	}
    	#Carlos Alberto Britto Salamoni
    	else if($this->session->userdata('codigo') == 1)
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    private function get_mes()
    {
    	$ar_mes[] = array("value" => "01", "text" => "Janeiro");
		$ar_mes[] = array("value" => "02", "text" => "Fevereiro");
		$ar_mes[] = array("value" => "03", "text" => utf8_encode("Março"));
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

    private function get_ticket_mes_ano($ano)
    {
    	$ar_mes[] = "01/".$ano;
    	$ar_mes[] = "02/".$ano;
    	$ar_mes[] = "03/".$ano;
    	$ar_mes[] = "04/".$ano;
    	$ar_mes[] = "05/".$ano;
    	$ar_mes[] = "06/".$ano;
    	$ar_mes[] = "07/".$ano;
    	$ar_mes[] = "08/".$ano;
    	$ar_mes[] = "09/".$ano;
    	$ar_mes[] = "10/".$ano;
    	$ar_mes[] = "11/".$ano;
    	$ar_mes[] = "12/".$ano;

		return $ar_mes;	
    }

    private function calculo_projetado_mensal($nr, $mes)
    {
    	$nr_mes = pow((1+($nr/100)), (1/12));

    	$arr = array(
			"valor_mes" => 0.0,
			"valores"   => array()
		);

    	$i = 1;

    	while($i <= 12)
    	{
    		$nr_mes_atual   = pow($nr_mes, $i);
			$nr_mes_percent = (($nr_mes_atual-1)*100);

			$arr["valores"][$i-1] = $nr_mes_percent;

			if($i == intval($mes))
			{
				$arr["valor_mes"] = $nr_mes_percent;
			}

			$i++;
    	}

    	return $arr;
    }

    private function calculo_acumulado($valores, $key_arr, $mes)
    {
    	$arr = array(
			"valor_mes" => 0.0,
			"valores"   => array()
		);

    	$i = 1;

    	$realizado_acumulado = 0;

    	while($i <= 12)
    	{
    		if(isset($valores[$i-1]))
    		{
    			$item = $valores[$i-1];

    			$realizado = ($item[$key_arr]/100)+1;

    			if($realizado_acumulado == 0)
				{
					$realizado_acumulado = $realizado;
				}
				else
				{
					$realizado_acumulado = $realizado_acumulado * $realizado;
				}

				$arr["valores"][$i-1] = ($realizado_acumulado-1)*100;

				if($i == intval($mes))
				{
					$arr["valor_mes"] = ($realizado_acumulado-1)*100;
				}
    		}
    		else
    		{
    			$arr["valores"][$i-1] = "";
    		}

    		$i ++;
    	}

    	return $arr;
    }

    private function calcular_formula($calculo, $mes)
    {
    	$arr = json_decode($calculo, true);

    	$formula = $arr["calculo"];

    	$arr_sub = $arr["sub"];

    	$arr = array();

    	$args["mes"] = $mes;

    	foreach($arr_sub as $key => $item)
    	{
    		if(trim($item["referencia"]) == "rentabilidade")
    		{
    			$args["cd_caderno_cci_estrutura"] = $item["codigo"];

    			$this->caderno_cci_model->estrutura_valor_listar($result, $args);
				$arr_rentabilidade = $result->result_array();

				$arr_temp = array();

				foreach($arr_rentabilidade as $key => $item2)
				{
					$arr_temp[$key] = $item2["nr_rentabilidade"];
				}
    		}
    		else if(trim($item["referencia"]) == "indice")
    		{
    			$args["cd_caderno_cci_indice"] = $item["codigo"];

    			$this->caderno_cci_model->indice_valor_listar($result, $args);
				$arr_indice = $result->result_array();

				$arr_temp = array();

				foreach($arr_indice as $key => $item2)
				{
					$arr_temp[$key] = $item2["nr_indice"];
				}
    		}

    		$arr[] = array("nr" => $arr_temp, "campo" => $item["campo"]);
    	}

    	$count = count($arr);

    	$arr_temp = array();

    	$i = 0;
    	$j = 0;

    	while($i < $mes)
    	{
    		//$arr_result[$i] = array();

    		while($j < $count)
    		{
    			$arr_temp[$i][$arr[$j]["campo"]] = $arr[$j]["nr"][$i]; 

    			$j++;
    		}

    		$i++;

    		$j = 0;
    	}

    	$arr_result = array();

    	foreach($arr_temp as $key => $item)
    	{
    		$new_formula = $formula;

    		foreach($item as $key2 => $item2)
    		{
    			$new_formula = str_replace($key2, $item2, $new_formula);
    		}
    		
    		eval("\$arr_result[]['nr'] = $new_formula;");
    	}

    	return $arr_result;
    }

    private function indice_anterior($item)
    {
    	$args["cd_caderno_cci_indice"] = $item;

		$this->caderno_cci_model->indice($result, $args);
		$row = $result->row_array();
    }

    private function organiza_tabela($tabela_no_ordem)
    {
		$tabela = array();

		foreach($tabela_no_ordem as $key => $item2)
		{
			if(isset($tabela_no_ordem[$key]["ordem"]) AND $tabela_no_ordem[$key]["ordem"] != "")
			{
				$tabela[$tabela_no_ordem[$key]["ordem"]] = $tabela_no_ordem[$key];

				unset($tabela_no_ordem[$key]);
			}
		}
		
		ksort($tabela);
		
		$count_no_ordem = count($tabela)-1;

		foreach($tabela_no_ordem as $key => $item2)
		{
			$tabela[$count_no_ordem] = $tabela_no_ordem[$key];

			$count_no_ordem++;
		}

		return $tabela;
    }

    private function organiza_array($ordem, $array_no_ordem)
    {
    	$array = array();

    	foreach ($ordem as $key => $item) 
    	{
    		$array[$item] = $array_no_ordem[$key];
    	}

    	ksort($array);

    	return $array;
    }

    private function mes_referencia($fl_mes, $mes, $ano)
	{
		$ds_mes = "";

		switch ($fl_mes) {
		    case "C":
		        $ds_mes = mes_extenso($mes)."/".$ano;
		        break;
		    case "P":
		        if($mes == "12")
				{
					$ds_mes = mes_extenso(1)."/".($ano+1);
				}
				else
				{
					$ds_mes = mes_extenso(($mes+1))."/".$ano;
				}
		        break;
		    case "D":
		        if($mes == "11")
				{
					$ds_mes = mes_extenso(1)."/".($ano+1);
				}
				elseif($mes == "12")
				{
					$ds_mes = mes_extenso(2)."/".($ano+1);
				}
				else
				{

					$ds_mes = mes_extenso(($mes+2))."/".$ano;
				}
		        break;
		}

		return utf8_encode($ds_mes);
	}

	private function grupo_barra(&$serie, $agrupamento, $tipo, $cd, $valor)
	{
		foreach ($agrupamento as $key => $value) 
		{
			foreach ($value as $key2 => $value2) 
			{
				if((key($value2) == $tipo) AND ($cd == $value2[key($value2)]))
				{
					$serie[$key2][$key] = $valor;
					break;
				}
			}
		}
	}

	function index($cd_caderno_cci = "", $mes = "")
    {
    	if(gerencia_in(array('GIN', 'DE')))
    	{
			$result = null;
			$args   = Array();
			$data   = Array();

			if(trim($mes) == "")
			{
				$mes = date("m");
			}

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$data["row"]["mes"] = $mes;

			$data["collection"] = array();

			foreach($this->get_mes() as $key => $item)
			{
				$args["mes"] = $item["value"];

				$this->caderno_cci_model->fechamento($result, $args);
				$row = $result->row_array();

				$data["collection"][$key]["value"] = $item["value"];
				$data["collection"][$key]["text"]  = $item["text"];

				$data["collection"][$key]["dt_inclusao"] = (isset($row["dt_inclusao"]) ? $row["dt_inclusao"] : "");
				$data["collection"][$key]["nome"]        = (isset($row["nome"]) ? $row["nome"] : "");
			}

			$this->load->view("gestao/caderno_cci_relatorio/index", $data);

		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    private function gerar($cd_caderno_cci, $mes = "")
    {
    	$result = null;
		$args   = array();
		$data   = array();

		$this->load->plugin("pchart_pi");

		if(trim($mes) == "")
		{
			$mes = date("m");
		}

		$args["cd_caderno_cci"] = intval($cd_caderno_cci);

		$this->caderno_cci_model->carrega($result, $args);
		$data["row"] = $result->row_array();

		$data["fl_ano_anterior"] = false;

		$args["nr_ano"] = ($data["row"]["nr_ano"]-1);

		$nr_ano = $data["row"]["nr_ano"];

		$this->caderno_cci_model->ano_anterior($result, $args);
		$row_ano_anterior = $result->row_array();

		if(count($row_ano_anterior) > 0)
		{
			$data["fl_ano_anterior"] = true;
		}

		$data["row"]["mes"] = $mes;
		$args["mes"]        = $mes;

		$data["mes"] = $this->get_mes();

		$this->caderno_cci_model->grafico_listar($result, $args);
		$collection = $result->result_array();

		$data["collection"] = array();

		$j = 0;

		foreach($collection as $item)
		{
			//SLIDE HTML 
			if($item["tp_grafico"] == "E") 
			{
				$data["collection"][$j]["ds_html"]    = $item["ds_html"];
				$data["collection"][$j]["tp_grafico"] = $item["tp_grafico"];
				$data["collection"][$j]["titulo"]     = utf8_encode($item["ds_caderno_cci_grafico"]);
			}
			else
			{
				$arr = json_decode($item["parametro"], true);

				$valores            = array();
				$legenda            = array();
				$tabela             = array();
				$tabela_anterior    = array();
				$cor                = array();
				$rowspan            = false;
				$ds_mes             = "";
				$rent_hist          = "";
				$ordem_rent         = array();
				$ordem_grafico      = array();
				$agrupamento_config = array();

				$i = 0;
				//SLIDE TABELA, CONFIGURAÇÃO INICIAL
				if($item["tp_grafico"] == "T") 
				{
					$campo   = json_decode($item["campo"], true);
					$negrito = json_decode($item["negrito"], true);
					$linha   = json_decode($item["linha"], true);
					$ordem   = json_decode($item["ordem"], true);
					$tab     = json_decode($item["tab"], true);

					foreach ($campo as $key2 => $item2)
					{
						if(trim($item2) == "nr_rentabilidade")
						{
							$rowspan = true;
						}
					}
				}
				//SLIDE DE RENTABILIDADE HISTÓRICA (ÚLTIMOS 10 ANOS + ANO ATUAL)
				elseif($item["tp_grafico"] == "R") 
				{
					$this->caderno_cci_model->rentabilidae_historica($result, $args);
					$rentabilidade_real = $result->result_array();

					$ano_rent_real = "";
					$diferenca_ano = "";

					$nr_nominal_acum = 0;
					$nr_inpc_acum    = 0;
					$nr_real_acum    = 0;
					//HISTÓRICO DE RENTABILIDADE ANUAL E CÁLCULO DO ACUMULADO DA RENTABILIDADE
					foreach ($rentabilidade_real as $key => $item_rentabilidade) 
					{
						$diferenca_ano = $nr_ano - $item_rentabilidade["nr_ano"];

						$ano_rent_real = $item_rentabilidade["nr_ano"];

						if($diferenca_ano <= 10)
						{
							$rent_hist[$i][0] = $item_rentabilidade["nr_ano"];
							$rent_hist[$i][1] = number_format($item_rentabilidade["nr_nominal"], 2, ",", ".");
							$rent_hist[$i][2] = number_format($item_rentabilidade["nr_inpc"], 2, ",", ".");
							$rent_hist[$i][3] = number_format($item_rentabilidade["nr_real"], 2, ",", "."); 

							if($i > 0)
							{
								$nr_nominal_acum *= (($item_rentabilidade["nr_nominal"]/100)+1);
								$nr_inpc_acum    *= (($item_rentabilidade["nr_inpc"]/100)+1);
								$nr_real_acum    *= (($item_rentabilidade["nr_real"]/100)+1);
							}
							else
							{
								$nr_nominal_acum = (($item_rentabilidade["nr_nominal"]/100)+1);
								$nr_inpc_acum    = (($item_rentabilidade["nr_inpc"]/100)+1);
								$nr_real_acum    = (($item_rentabilidade["nr_real"]/100)+1);
							}

							$i++;
						}
					}
					
					//RENTABILIDADE NO CADERNO (DE 2014 ATÉ ANO ATUAL)
					while($ano_rent_real < $args["nr_ano"])
					{
						$ano_rent_real++;

						$rent_hist[$i][0] = $ano_rent_real;
						$rent_hist[$i][1] = 0;
						$rent_hist[$i][2] = 0;
						$rent_hist[$i][3] = 0; 

						$i++;
					}

					$i--;

					$ini_final = $i;
						
					//INPC
					if(count($arr["indice"]) > 0)
					{
						foreach($arr["indice"] as $cd_caderno_cci_indice) 
						{	
							$args["cd_caderno_cci_indice"] = $cd_caderno_cci_indice;

							while($args["cd_caderno_cci_indice"] > 0)
							{
								$this->caderno_cci_model->indice($result, $args);
								$indice = $result->row_array();

								if(trim($indice["fl_inpc"]) == "S" AND $indice["cd_caderno_cci_indice_referencia"] > 0)
								{
									$args["cd_caderno_cci_indice"] = $indice["cd_caderno_cci_indice_referencia"];

									$args["mes"] = 12;

									$this->caderno_cci_model->indice_valor_listar($result, $args);
									$indice_valor_acumulado = $this->calculo_acumulado($result->result_array(), "nr_indice", 12);

									if(isset($indice_valor_acumulado["valores"][11]))
									{
										$valor_indice = $indice_valor_acumulado["valores"][11];
									}

									$rent_hist[$i][2] = number_format($valor_indice, 2, ",", ".");

									$nr_inpc_acum *= (($valor_indice/100)+1);

									$i--;
								}
								else
								{
									$args["cd_caderno_cci_indice"] = 0;
								}
							}	
						}
					}

					$i = $ini_final;

					//RENTABILIDADE REAL E NOMINAL
					if(count($arr["rentabilidade"]) > 0)
					{
						foreach($arr["rentabilidade"] as $cd_caderno_cci_estrutura) 
						{
							$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;

							while($args["cd_caderno_cci_estrutura"] > 0)
							{
								$this->caderno_cci_model->estrutura($result, $args);
								$rentabilidade = $result->row_array();
								//RENTABILIDADE REAL
								if(trim($rentabilidade["fl_real"]) == "S" AND $rentabilidade["cd_caderno_cci_estrutura_referencia"] > 0)
								{
									$args["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura_referencia"];

									$args["mes"] = 12;

									$this->caderno_cci_model->estrutura($result, $args);
									$rentabilidade = $result->row_array();
							
									$rentabilidade_valor_acumulado = $this->calculo_acumulado($this->calcular_formula($rentabilidade["calculo"], 12), "nr", 12);

									if(isset($rentabilidade_valor_acumulado["valores"][11]))
									{
										$valor_rentabilidade = $rentabilidade_valor_acumulado["valores"][11];
									}

									$rent_hist[$i][3] = number_format($valor_rentabilidade, 2, ",", ".");

									$i--;

									$nr_real_acum *= (($valor_rentabilidade/100)+1);
								}
								else
								{
									$args["cd_caderno_cci_estrutura"] = 0;
								}
							}
						}

						$i = $ini_final;

						foreach($arr["rentabilidade"] as $cd_caderno_cci_estrutura) 
						{
							$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;

							while($args["cd_caderno_cci_estrutura"] > 0)
							{
								$this->caderno_cci_model->estrutura($result, $args);
								$rentabilidade = $result->row_array();
								//RENTABILIDADE NOMINAL
								if(trim($rentabilidade["fl_nominal"]) == "S" AND $rentabilidade["cd_caderno_cci_estrutura_referencia"] > 0)
								{
									$args["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura_referencia"];

									$args["mes"] = 12;

									$this->caderno_cci_model->estrutura_valor_listar($result, $args);
									$rentabilidade_valor_acumulado = $this->calculo_acumulado($result->result_array(), "nr_rentabilidade", 12);

									if(isset($rentabilidade_valor_acumulado["valores"][11]))
									{
										$valor_rentabilidade = $rentabilidade_valor_acumulado["valores"][11];
									}

									$rent_hist[$i][1] = number_format($valor_rentabilidade, 2, ",", ".");

									$i--;

									$nr_nominal_acum *= (($valor_rentabilidade/100)+1);
								}
								else
								{
									$args["cd_caderno_cci_estrutura"] = 0;
								}
							}
						}
					}


					$i = 0;
					$args["mes"] = $mes;
				}
				//SLIDE DE BARRA COM AGRUPAMENTO
				elseif($item["tp_grafico"] == "A")
				{
					$args["cd_caderno_cci_grafico"] = $item["cd_caderno_cci_grafico"];

					$this->caderno_cci_model->grafico_agrupamento_listar($result, $args);
					$agrupamento = $result->result_array();	

					$agrupamento_config["ticks"]       = array();
					$agrupamento_config["agrupamento"] = array();
		
					foreach ($agrupamento as $key => $value) 
					{
						$agrupamento_config["ticks"][$value["nr_ordem"]] = utf8_encode($value["ds_caderno_cci_grafico_agrupamento"]);

						$json = json_decode($value["agrupamento"], true);

						$arr_agrupamento = array();

						foreach ($json as $key2 => $value2) 
						{
							$args["cd_caderno_cci_grafico_rotulo"] = $key2;

							$this->caderno_cci_model->grafico_rotulo($result, $args);
							$row = $result->row_array();

							$arr_agrupamento[$row["nr_ordem"]] = $value2;
						}						

						$agrupamento_config["agrupamento"][$value["nr_ordem"]] = $arr_agrupamento;
					}

					$this->caderno_cci_model->grafico_rotulo_listar($result, $args);
					$rotulo = $result->result_array();	

					$agrupamento_config["legenda"] = array();
					$agrupamento_config["serie"]   = array();

					foreach ($rotulo as $key => $value) 
					{
						$cor[$value["nr_ordem"]] = "#".$value["cor"];

						$agrupamento_config["legenda"][$value["nr_ordem"]] = utf8_encode($value["ds_caderno_cci_grafico_rotulo"]);
						$agrupamento_config["serie"][$value["nr_ordem"]]   = array();

						foreach ($agrupamento as $key2 => $value2) 
						{
							$agrupamento_config["serie"][$value["nr_ordem"]][$value2["nr_ordem"]] = "";
						}
					}
				}
				// OUTROS SLIDES
				else
				{
					$ordem   = json_decode($item["ordem"], true);
					$arr_cor = json_decode($item["cor"], true);
				}
				//CÁLCULOS DOS VALORES PROJETADOS 
				if(count($arr["projetado"]) > 0)
				{
					foreach($arr["projetado"] as $cd_caderno_cci_projetado) 
					{
						$args["cd_caderno_cci_projetado"] = $cd_caderno_cci_projetado;

						$this->caderno_cci_model->projetado($result, $args);
						$projetado = $result->row_array();

						if(count($projetado) == 0)
						{
							break;
						}

						//CÁLCULO DO PROJETADO MENSAL
						$projetado_mensal = $this->calculo_projetado_mensal($projetado["nr_projetado"], $mes);

						$legenda[$i] = $projetado["ds_caderno_cci_projetado"];
						//VALOR DA MÊS DE DEZEMBRO DO ANO ANTERIOR (EXCLUÍDO)
						//SAINDO DE ZERO
						if(($item["tp_grafico"] == "L") AND ($data["fl_ano_anterior"]))
						{
							/*
							$args["cd_caderno_cci_projetado"] = $arr2["cd_caderno_cci_projetado_referencia"];

							$this->caderno_cci_model->projetado($result, $args);
							$projetado_referencia = $result->row_array();

							if(count($projetado_referencia) > 0)
							{
								$args["cd_caderno_cci_projetado"] = $projetado_referencia["cd_caderno_cci_projetado"];

								$this->caderno_cci_model->projetado($result, $args);
								$val_projetado_referencia = $result->row_array();

								$valores[$i][] = $val_projetado_referencia["nr_projetado"];
							}
							else
							{
								$valores[$i][] = 0;
							}
							*/
							$valores[$i][] = 0;
						}
						//VALORES
						foreach($projetado_mensal["valores"] as $val) 
						{
							$valores[$i][] = $val;
						}
						//SLIDE TABELA 
						if($item["tp_grafico"] == "T")
						{
							$tabela[$i]["ds"]      = utf8_encode($projetado["ds_caderno_cci_projetado"]);
							$tabela[$i]["tipo"]    = "P";

							$tabela[$i]["negrito"] = (isset($negrito["projetado"][$cd_caderno_cci_projetado]) ? $negrito["projetado"][$cd_caderno_cci_projetado] : "");
							$tabela[$i]["ordem"]   = (isset($ordem["projetado"][$cd_caderno_cci_projetado]) ? $ordem["projetado"][$cd_caderno_cci_projetado] : "");
							$tabela[$i]["linha"]   = (isset($linha["projetado"][$cd_caderno_cci_projetado]) ? $linha["projetado"][$cd_caderno_cci_projetado] : "");
							$tabela[$i]["tab"]     = (isset($tab["projetado"][$cd_caderno_cci_projetado]) ? $tab["projetado"][$cd_caderno_cci_projetado] : "");
							//RENTABILIDADE MENSAL E ANUAL
							foreach($campo as $item_campo)
							{
								if($item_campo == "nr_rentabilidade")
								{
									$tabela[$i]["rentabilidade_mes"] = number_format($projetado_mensal["valores"][0], 2, ",", ".");
									$tabela[$i]["rentabilidade_ano"] = number_format($projetado_mensal["valor_mes"], 2, ",", ".");
								}
								else
								{
									$tabela[$i][$item_campo] = "";
								}
							}
						}
						//SLIDE BARRA AGRUPADA
						else if($item["tp_grafico"] == "A")
						{
							$this->grupo_barra($agrupamento_config["serie"], $agrupamento_config["agrupamento"], 'projetado', $cd_caderno_cci_projetado, $valores[$i][$mes-1]);
						}
						else
						{
							//COR DO ITEM NO GRÁFICO
							if(isset($arr_cor["projetado"][$cd_caderno_cci_projetado]))
							{
								$cor[$i] = "#".$arr_cor["projetado"][$cd_caderno_cci_projetado];
							}
							//ORDEM DO ITEM NO GRÁFICO
							if(isset($ordem["projetado"][$cd_caderno_cci_projetado]))
							{
								$ordem_grafico[$i] = $ordem["projetado"][$cd_caderno_cci_projetado];
							}
						}

						$i++;
					}
				}
				//CÁLCULOS DOS VALORES ÍNDICES 
				if(count($arr["indice"]) > 0)
				{
					foreach($arr["indice"] as $cd_caderno_cci_indice) 
					{
						$args["cd_caderno_cci_indice"] = $cd_caderno_cci_indice;

						$this->caderno_cci_model->indice($result, $args);
						$indice = $result->row_array();

						if(count($indice) == 0)
						{
							break;
						}

						$this->caderno_cci_model->indice_valor_listar($result, $args);
						$indice_valor = $result->result_array();

						$indice_valor_acumulado = $this->calculo_acumulado($indice_valor, "nr_indice", $mes);

						$legenda[$i] = $indice["ds_caderno_cci_indice"];
						//VALOR DA MÊS DE DEZEMBRO DO ANO ANTERIOR (EXCLUÍDO)
						//SAINDO DE ZERO
						if(($item["tp_grafico"] == "L") AND ($data["fl_ano_anterior"]))
						{
							/*
							$args["cd_caderno_cci_indice"] = $row_indice["cd_caderno_cci_indice_referencia"];
							
							$this->caderno_cci_model->indice($result, $args);
							$indice_referencia = $result->row_array();
			
							if(count($indice_referencia) > 0)
							{
								$args_indice["cd_caderno_cci_indice"] = $indice_referencia["cd_caderno_cci_indice"];
								$args_indice["mes"]                   = 12;
								
								$this->caderno_cci_model->indice_valor($result, $args_indice);
								$val_indice_referencia = $result->row_array();
				
								$valores[$i][] = $val_indice_referencia["nr_indice"];
							}
							else
							{
								$valores[$i][] = 0;
							}
							*/

							$valores[$i][] = 0;
						}
						//VALORES
						foreach($indice_valor_acumulado["valores"] as $val) 
						{
							$valores[$i][] = $val;
						}
						//SLIDE TABELA 
						if($item["tp_grafico"] == "T")
						{
							$tabela[$i]["ds"]      = utf8_encode($legenda[$i]);
							$tabela[$i]["tipo"]    = "I";

							$tabela[$i]["negrito"] = (isset($negrito["indice"][$cd_caderno_cci_indice]) ? $negrito["indice"][$cd_caderno_cci_indice] : "");
							$tabela[$i]["ordem"]   = (isset($ordem["indice"][$cd_caderno_cci_indice]) ? $ordem["indice"][$cd_caderno_cci_indice] : "");
							$tabela[$i]["linha"]   = (isset($linha["indice"][$cd_caderno_cci_indice]) ? $linha["indice"][$cd_caderno_cci_indice] : "");
							$tabela[$i]["tab"]     = (isset($tab["indice"][$cd_caderno_cci_indice]) ? $tab["indice"][$cd_caderno_cci_indice] : "");
							//RENTABILIDADE MENSAL E ANUAL
							foreach($campo as $item_campo)
							{
								if($item_campo == "nr_rentabilidade")
								{
									$tabela[$i]["rentabilidade_mes"] = number_format($indice_valor[count($indice_valor)-1]["nr_indice"], 2, ",", ".");
									$tabela[$i]["rentabilidade_ano"] = number_format($indice_valor_acumulado["valores"][(intval($mes)-1)], 2, ",", ".");
								}
								else
								{
									$tabela[$i][$item_campo] = "";
								}
							}
						}
						//SLIDE BARRA AGRUPADA
						else if($item["tp_grafico"] == "A")
						{
							$this->grupo_barra($agrupamento_config["serie"], $agrupamento_config["agrupamento"], 'indice', $cd_caderno_cci_indice, $valores[$i][$mes-1]);
						}
						else
						{
							//COR DO ITEM NO GRÁFICO
							if(isset($arr_cor["indice"][$cd_caderno_cci_indice]))
							{
								$cor[$i] = "#".$arr_cor["indice"][$cd_caderno_cci_indice];
							}
							//ORDEM DO ITEM NO GRÁFICO
							if(isset($ordem["indice"][$cd_caderno_cci_indice]))
							{
								$ordem_grafico[$i] = $ordem["indice"][$cd_caderno_cci_indice];
							}
						}
						//SE FOR RENTABILIDADE HISTÓRICA E INPC
						if($item["tp_grafico"] == "R" AND $indice["fl_inpc"] == "S")
						{
							$ordem_rent[$i] = "fl_inpc";
						}

						$i++;
					}
				}
				//CÁLCULOS DOS VALORES BENCHMARK 
				if(count($arr["benchmark"]) > 0)
				{
					foreach($arr["benchmark"] as $cd_caderno_cci_benchmark) 
					{
						$args["cd_caderno_cci_benchmark"] = $cd_caderno_cci_benchmark;

						$this->caderno_cci_model->benchmark($result, $args);
						$benchmark = $result->row_array();

						if(count($benchmark) == 0)
						{
							break;
						}

						$this->caderno_cci_model->benchmark_valor_listar($result, $args);
						$benchmark_valor = $result->result_array();
												
						$benchmark_valor_acumulado = $this->calculo_acumulado($benchmark_valor, "nr_benchmark", $mes);

						$legenda[$i] = $benchmark["ds_caderno_cci_benchmark"];
						//VALOR DA MÊS DE DEZEMBRO DO ANO ANTERIOR (EXCLUÍDO)
						//SAINDO DE ZERO
						if(($item["tp_grafico"] == "L") AND ($data["fl_ano_anterior"]))
						{
							/*
							$args["cd_caderno_cci_benchmark"] = $row_benchmark["cd_caderno_cci_benchmark_referencia"];
							
							$this->caderno_cci_model->benchmark($result, $args);
							$benchmark_referencia = $result->row_array();
			
							if(count($benchmark_referencia) > 0)
							{
								$args_benchmark["cd_caderno_cci_benchmark"] = $benchmark_referencia["cd_caderno_cci_benchmark"];
								$args_benchmark["mes"]                      = 12;
								
								$this->caderno_cci_model->benchmark_valor($result, $args_benchmark);
								$val_benchmark_referencia = $result->row_array();
				
								$valores[$i][] = $val_benchmark_referencia["nr_benchmark"];
							}
							else
							{
								$valores[$i][] = 0;
							}
							*/

							$valores[$i][] = 0;
						}
						//VALORES
						foreach($benchmark_valor_acumulado["valores"] as $val) 
						{
							$valores[$i][] = $val;
						}
						//SLIDE TABELA 
						if($item["tp_grafico"] == "T")
						{
							$tabela[$i]["ds"]      = utf8_encode($legenda[$i]);
							$tabela[$i]["tipo"]    = "B";

							$tabela[$i]["negrito"] = (isset($negrito["benchmark"][$cd_caderno_cci_benchmark]) ? $negrito["benchmark"][$cd_caderno_cci_benchmark] : "");
							$tabela[$i]["ordem"]   = (isset($ordem["benchmark"][$cd_caderno_cci_benchmark]) ? $ordem["benchmark"][$cd_caderno_cci_benchmark] : "");
							$tabela[$i]["linha"]   = (isset($linha["benchmark"][$cd_caderno_cci_benchmark]) ? $linha["benchmark"][$cd_caderno_cci_benchmark] : "");
							$tabela[$i]["tab"]     = (isset($tab["benchmark"][$cd_caderno_cci_benchmark]) ? $tab["benchmark"][$cd_caderno_cci_benchmark] : "");
							//RENTABILIDADE MENSAL E ANUAL
							foreach($campo as $item_campo)
							{
								if($item_campo == "nr_rentabilidade")
								{
									$tabela[$i]["rentabilidade_mes"] = (isset($benchmark_valor[count($benchmark_valor)-1]) ? number_format($benchmark_valor[count($benchmark_valor)-1]["nr_benchmark"], 2, ",", ".") : 0);

									$tabela[$i]["rentabilidade_ano"] = 0;

									if($benchmark_valor_acumulado["valores"][(intval($mes)-1)] != "")
									{
										$tabela[$i]["rentabilidade_ano"] = number_format($benchmark_valor_acumulado["valores"][(intval($mes)-1)], 2, ",", ".");
									}
								}
								else
								{
									$tabela[$i][$item_campo] = "";
								}
							}
							//SLIDE TABLE DO ANO ANTERIOR
							if($item["fl_ano"] == "S")
							{
								$tabela_anterior[$i] = array(
									"ds"      => $tabela[$i]["ds"],
									"negrito" => $tabela[$i]["negrito"],
									"tipo"    => $tabela[$i]["tipo"],
									"ordem"   => $tabela[$i]["ordem"],
									"linha"   => $tabela[$i]["linha"],
									"tab"     => $tabela[$i]["tab"]
								);

								if($benchmark["cd_caderno_cci_benchmark_referencia"] > 0)
								{
									$args_benchmark["cd_caderno_cci_benchmark"] = $benchmark["cd_caderno_cci_benchmark_referencia"];
									$args_benchmark["mes"]                      = 12;

									$this->caderno_cci_model->benchmark_valor_listar($result, $args_benchmark);
									$benchmark_valor_referencia = $result->result_array();

									$this->caderno_cci_model->benchmark($result, $args_benchmark);
									$benchmark_referencia = $result->row_array();

									$benchmark_valor_acumulado_referencia = $this->calculo_acumulado($benchmark_valor_referencia, "nr_benchmark", 12);

									foreach($campo as $item_campo)
									{
										if($item_campo == "nr_rentabilidade")
										{
											$tabela_anterior[$i]["rentabilidade_mes"] = number_format($benchmark_valor_referencia[11]["nr_benchmark"], 2, ",", ".");

											$tabela_anterior[$i]["rentabilidade_ano"] = number_format($benchmark_valor_acumulado_referencia["valores"][11], 2, ",", ".");
										}
										else
										{
											$tabela_anterior[$i][$item_campo] = "";
										}
									}

									$tabela_anterior[$i]["ds"] = utf8_encode($benchmark_referencia["ds_caderno_cci_benchmark"]);
								}
								else
								{
									foreach($campo as $item_campo)
									{
										if($item_campo == "nr_rentabilidade")
										{
											$tabela_anterior[$i]["rentabilidade_mes"] = "";

											$tabela_anterior[$i]["rentabilidade_ano"] = "";
										}
										else
										{
											$tabela_anterior[$i][$item_campo] = "";
										}
									}
								}
							}
						}
						//SLIDE BARRA AGRUPADA
						else if($item["tp_grafico"] == "A")
						{
							$this->grupo_barra($agrupamento_config["serie"], $agrupamento_config["agrupamento"], 'benchmark', $cd_caderno_cci_benchmark, $valores[$i][$mes-1]);
						}
						else
						{
							//COR DO ITEM NO GRÁFICO
							if(isset($arr_cor["benchmark"][$cd_caderno_cci_benchmark]))
							{
								$cor[$i] = "#".$arr_cor["benchmark"][$cd_caderno_cci_benchmark];
							}
							//ORDEM DO ITEM NO GRÁFICO
							if(isset($ordem["benchmark"][$cd_caderno_cci_benchmark]))
							{
								$ordem_grafico[$i] = $ordem["benchmark"][$cd_caderno_cci_benchmark];
							}
						}

						$i++;
					}
				}
				//CÁLCULOS DOS VALORES RENTABILIADE 
				if(count($arr["rentabilidade"]) > 0)
				{
					$nr_valor_total              = 0;
					$nr_valor_integralizar_total = 0;
					$nr_realizado_total          = 0;

					$arr_total           = array();
					$arr_realizado_total = array();

					foreach($arr["rentabilidade"] as $cd_caderno_cci_estrutura) 
					{
						$args["cd_caderno_cci_estrutura"] = $cd_caderno_cci_estrutura;

						$this->caderno_cci_model->estrutura($result, $args);
						$rentabilidade = $result->row_array();

						if(count($rentabilidade) == 0)
						{
							break;
						}

						$legenda[$i] = $rentabilidade["ds_caderno_cci_estrutura"];
						//CALCULO ACUMULADO
						if(trim($rentabilidade["calculo"]) == "")
						{
							$this->caderno_cci_model->estrutura_valor_listar($result, $args);
							$rentabilidade_valor = $result->result_array();

							$rentabilidade_valor_acumulado = $this->calculo_acumulado($rentabilidade_valor, "nr_rentabilidade", $mes);
						}
						//CÁLCULO POR FÓRMULA
						else
						{
							$rentabilidade_valor = $this->calcular_formula($rentabilidade["calculo"], $mes);

							$rentabilidade_valor_acumulado = $this->calculo_acumulado($rentabilidade_valor, "nr", $mes);
						}
						//SLIDE PIZZA
						if($item["tp_grafico"] == "P")
						{
							//VALOR DA CI NOMINAL
							$this->caderno_cci_model->estrutura_total($result, $args);
							$rentabilidade_total = $result->row_array();

							$args["cd_caderno_cci_estrutura"] = $rentabilidade_total["cd_caderno_cci_estrutura"];

							$this->caderno_cci_model->estrutura_valor($result, $args);
							$rentabilidade_total_valor = $result->row_array();
							
							if((count($rentabilidade_total_valor)) > 0 AND ($rentabilidade_total_valor["nr_valor_atual"] > 0))
							{
								$valores[$i] = ($rentabilidade_valor[count($rentabilidade_valor)-1]["nr_valor_atual"]*100)/$rentabilidade_total_valor["nr_valor_atual"];
							}
							else
							{
								$valores[$i] = 0;
							}
							//COR DO ITEM NO GRÁFICO
							if(isset($arr_cor["rentabilidade"][$cd_caderno_cci_estrutura]))
							{
								$cor[$i] = "#".$arr_cor["rentabilidade"][$cd_caderno_cci_estrutura];
							}
							//ORDEM DO ITEM NO GRÁFICO
							if(isset($ordem["rentabilidade"][$cd_caderno_cci_estrutura]))
							{
								$ordem_grafico[$i] = $ordem["rentabilidade"][$cd_caderno_cci_estrutura];
							}
						}
						//SLIDE TABELA
						elseif($item["tp_grafico"] == "T")
						{
							$participacao    = json_decode($item["participacao"], true);
							$participacao_m2 = json_decode($item["participacao_m2"], true);

							//DEFINIÇÃO DO MÊS DE REFÊRENCIA
							$ds_mes = $this->mes_referencia($item["fl_mes"], $mes, $data["row"]["nr_ano"]);

							$tabela[$i]["ds"]      = utf8_encode($legenda[$i]);
							$tabela[$i]["tipo"]    = "R";

							$tabela[$i]["negrito"] = (isset($negrito["rentabilidade"][$cd_caderno_cci_estrutura]) ? $negrito["rentabilidade"][$cd_caderno_cci_estrutura] : "");
							$tabela[$i]["ordem"]   = (isset($ordem["rentabilidade"][$cd_caderno_cci_estrutura]) ? $ordem["rentabilidade"][$cd_caderno_cci_estrutura] : "");
							$tabela[$i]["linha"]   = (isset($linha["rentabilidade"][$cd_caderno_cci_estrutura]) ? $linha["rentabilidade"][$cd_caderno_cci_estrutura] : "");
							$tabela[$i]["tab"]     = (isset($tab["rentabilidade"][$cd_caderno_cci_estrutura]) ? $tab["rentabilidade"][$cd_caderno_cci_estrutura] : "");

							$arr_total["ds"]      = "Valor a integralizar + Valor integralizado";
							$arr_total["negrito"] = "S";
							$arr_total["tipo"]    = "T" ; 

							$arr_realizado_total["ds"]      = "Valor total";
							$arr_realizado_total["negrito"] = "S";
							$arr_realizado_total["tipo"]    = "T" ; 

							if(count($campo) > 0)
							{
								foreach($campo as $item_campo)
								{
									//RENTABILIADADE
									if($item_campo == "nr_rentabilidade")
									{										
										$tabela[$i]["rentabilidade_mes"] = number_format($rentabilidade_valor[count($rentabilidade_valor)-1]["nr_rentabilidade"], 2, ",", ".");
										
										if($rentabilidade_valor_acumulado["valores"][(intval($mes)-1)])
										{
											//OS : 50146
											if(intval($cd_caderno_cci_estrutura) == 500)
											{
												$tabela[$i]["rentabilidade_ano"] = '0,22';
											}
											else
											{
												$tabela[$i]["rentabilidade_ano"] = number_format($rentabilidade_valor_acumulado["valores"][(intval($mes)-1)], 2, ",", ".");
											}
										}
										else
										{
											$tabela[$i]["rentabilidade_ano"] = 0;
										}
									}
									//PARTICIPAÇÃO (%)
									else if($item_campo == "nr_participacao")
									{
										if(isset($participacao[$args["cd_caderno_cci_estrutura"]]))
										{
											$args["cd_caderno_cci_estrutura"] = $participacao[$args["cd_caderno_cci_estrutura"]];

											$this->caderno_cci_model->estrutura_valor($result, $args);
											$rentabilidade_participacao = $result->row_array();
											
											if((count($rentabilidade_participacao)) > 0 AND ($rentabilidade_participacao["nr_valor_atual"] > 0))
											{
												$tabela[$i][$item_campo] = number_format(($rentabilidade_valor[count($rentabilidade_valor)-1]["nr_valor_atual"]*100)/$rentabilidade_participacao["nr_valor_atual"], 2, ",", ".");
											}
											else
											{
												$tabela[$i][$item_campo] = 0;
											}
										}
										else
										{
											$tabela[$i][$item_campo] = "";
										}

										$tabela[$i]["cd_caderno_cci_estrutura"] = $args["cd_caderno_cci_estrutura"];
									}
									//VENCIMENTO
									else if($item_campo == "nr_ano_vencimento")
									{
										$tabela[$i][$item_campo] = $rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo];
									}
									//VALOR A INTEGRALIZAR
									else if($item_campo == "nr_valor_integralizar")
									{
										$nr_valor_integralizar_total += $rentabilidade_valor[count($rentabilidade_valor)-1]["nr_valor_integralizar"];

										$tabela[$i][$item_campo] = number_format(($rentabilidade_valor[count($rentabilidade_valor)-1]["nr_valor_integralizar_total"]/1000), 2, ",", ".");
									}
									//VALOR
									else if($item_campo == "nr_valor_atual")
									{
										if(isset($rentabilidade_valor[count($rentabilidade_valor)-1]) AND $rentabilidade_valor[count($rentabilidade_valor)-1]["cd_caderno_cci_estrutura_pai"] == "" AND $rentabilidade_valor[count($rentabilidade_valor)-1]["fl_agrupar"] == "S")
										{
											$nr_valor_total += $rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo];
										}

										if(isset($rentabilidade_valor[count($rentabilidade_valor)-1]))
										{
											$tabela[$i][$item_campo] = number_format(($rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo]/1000), 2, ",", ".");
										}
									}
									//REALIZADO
									else if($item_campo == "nr_realizado")
									{
										//REALIZADO TOTAL
										if($rentabilidade["fl_grupo"] == "S")
										{
											$nr_realizado_total += $rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo];
										}

										$tabela[$i][$item_campo] = number_format(($rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo]/1000), 2, ",", ".");
									}
									//LIMITES
									else if(($item_campo == "nr_politica_min") OR ($item_campo == "nr_politica_max") OR ($item_campo == "nr_legal_max") OR ($item_campo == "nr_alocacao_estrategica"))
									{
										$tabela[$i][$item_campo] = number_format($rentabilidade[$item_campo], 2, ",", ".");
									}
									//PARTICIPAÇÃO M²
									else if($item_campo == "nr_participacao_metro")
									{
										if(isset($participacao_m2[$cd_caderno_cci_estrutura]))
										{
											$args["cd_caderno_cci_estrutura"] = $participacao_m2[$cd_caderno_cci_estrutura];

											$this->caderno_cci_model->estrutura_valor($result, $args);
											$rentabilidade_participacao_m2 = $result->row_array();

											if((count($rentabilidade_participacao_m2)) > 0 AND ($rentabilidade_participacao_m2["nr_metro"] > 0))
											{
												$tabela[$i][$item_campo] = number_format(($rentabilidade_valor[count($rentabilidade_valor)-1]["nr_metro"]*100)/$rentabilidade_participacao_m2["nr_metro"], 2, ",", ".");
											}
											else
											{
												$tabela[$i][$item_campo] = 0;
											}
										}
										else
										{
											$tabela[$i][$item_campo] = "";
										}

										$tabela[$i]["cd_caderno_cci_estrutura"] = $args["cd_caderno_cci_estrutura"];
									}
									//OUTROS CAMPOS
									else
									{
										$tabela[$i][$item_campo] = number_format($rentabilidade_valor[count($rentabilidade_valor)-1][$item_campo], 2, ",", ".");
									}
								}
							}
							//VALOR INTEGRALIZAR TOTAL E VALOR TOTAL
							if(($nr_valor_integralizar_total > 0) AND ($nr_valor_total > 0))
							{
								$nr_valor_atual = $nr_valor_integralizar_total + $nr_valor_total;

	 							$this->caderno_cci_model->estrutura_total($result, $args);
								$rentabilidade_total = $result->row_array();

								$args["cd_caderno_cci_estrutura"] = $rentabilidade_total["cd_caderno_cci_estrutura"];

								$this->caderno_cci_model->estrutura_valor($result, $args);
								$rentabilidade_total_valor = $result->row_array();

								$arr_total["nr_participacao"] = number_format(($nr_valor_atual*100)/$rentabilidade_total_valor["nr_valor_atual"], 2, ",", ".");
								$arr_total["nr_valor_atual"]  = number_format(($nr_valor_atual/1000), 2, ",", ".");  
							}

							if($nr_realizado_total != 0)
							{
								$arr_realizado_total["nr_realizado"] = number_format(($nr_realizado_total/1000), 2, ",", ".");  
							}
							//ANO ANTERIOR
							if($item["fl_ano"] == "S")
							{
								$args_rentabilidade["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura_referencia"];
								$args_rentabilidade["mes"]                      = 12;

								$this->caderno_cci_model->estrutura($result, $args_rentabilidade);
								$rentabilidade_referencia = $result->row_array();

								if(count($rentabilidade_referencia) > 0)
								{
									if(trim($rentabilidade_referencia["calculo"]) == "")
									{
										$this->caderno_cci_model->estrutura_valor_listar($result, $args_rentabilidade);
										$arr2 = $result->result_array();

										$arr3 = $this->calculo_acumulado($arr2, "nr_rentabilidade", 12);
									}
									else
									{
										$arr2 = $this->calcular_formula($rentabilidade_referencia["calculo"], 12);

										$arr3 = $this->calculo_acumulado($arr2, "nr", 12);
									}
								}
								else
								{
									$arr2 = array();
									$arr3 = array();
								}

								$tabela_anterior[$i] = array(
									"ds"      => $tabela[$i]["ds"],
									"negrito" => $tabela[$i]["negrito"],
									"tipo"    => $tabela[$i]["tipo"],
									"ordem"   => $tabela[$i]["ordem"],
									"linha"   => $tabela[$i]["linha"],
									"tab"     => $tabela[$i]["tab"]
								);

								if(isset($rentabilidade_referencia["ds_caderno_cci_estrutura"]))
								{
									$tabela_anterior[$i]["ds"] = utf8_encode($rentabilidade_referencia["ds_caderno_cci_estrutura"]);
								}

								if(count($campo) > 0)
								{
									foreach($campo as $item_campo)
									{
										if($item_campo == "nr_rentabilidade")
										{
											if(count($arr2) > 0)
											{
												$tabela_anterior[$i]["rentabilidade_mes"] = number_format($arr2[count($arr2)-1]["nr_rentabilidade"], 2, ",", ".");	
											}
											else
											{
												$tabela_anterior[$i]["rentabilidade_mes"] = 0;
											}

											if(isset($arr3["valores"][11]))
											{
												$tabela_anterior[$i]["rentabilidade_ano"] = ($arr3["valores"][11] ? number_format($arr3["valores"][11], 2, ",", ".") : 0);
											}
											else
											{
												$tabela_anterior[$i]["rentabilidade_ano"] = 0;
											}
										}
										else if($item_campo == "nr_participacao")
										{
											if(isset($participacao[$cd_caderno_cci_estrutura]))
											{
												$args["cd_caderno_cci_estrutura"] = $participacao[$cd_caderno_cci_estrutura];

												$this->caderno_cci_model->estrutura($result, $args);
												$row_participacao = $result->row_array();
												
												$args_participacao["cd_caderno_cci_estrutura"] = $row_participacao["cd_caderno_cci_estrutura_referencia"];
												$args_participacao["mes"] = 12;

												$this->caderno_cci_model->estrutura_valor($result, $args_participacao);
												$row_rent_participacao = $result->row_array();
												
												if((count($arr2) > 0) AND (count($arr2) > 0) AND ($row_rent_participacao["nr_valor_atual"] > 0))
												{
													$tabela_anterior[$i][$item_campo] = number_format(($arr2[count($arr2)-1]["nr_valor_atual"]*100)/$row_rent_participacao["nr_valor_atual"], 2, ",", ".");
												}
												else
												{
													$tabela_anterior[$i][$item_campo] = 0;
												}
											}
											else
											{
												$tabela_anterior[$i][$item_campo] = "";
											}

											//$tabela_anterior[$i]["cd_caderno_cci_estrutura"] = $args_participacao["cd_caderno_cci_estrutura"];
										}
										else if($item_campo == "nr_valor_atual")
										{
											if(isset($arr2[count($arr2)-1]))
											{
												$tabela_anterior[$i][$item_campo] = number_format(($arr2[count($arr2)-1][$item_campo]/1000), 2, ",", ".");
											}
										}
										else if($item_campo == "nr_realizado")
										{
											if(count($arr2) > 0)
											{
												$tabela_anterior[$i][$item_campo] = number_format(($arr2[count($arr2)-1][$item_campo]/1000), 2, ",", ".");
											}
											else
											{
												$tabela_anterior[$i][$item_campo] = 0;
											}
										}
										else
										{
											if(count($arr2) > 0)
											{
												$tabela_anterior[$i][$item_campo] = number_format($arr2[count($arr2)-1][$item_campo], 2, ",", ".");
											}
											else
											{
												$tabela_anterior[$i][$item_campo] = 0;
											}
										}
									}
								}
							}
						}
						else
						{
							if($item["tp_grafico"] != "A")
							{
								if(isset($arr_cor["rentabilidade"][$cd_caderno_cci_estrutura]))
								{
									$cor[$i] = "#".$arr_cor["rentabilidade"][$cd_caderno_cci_estrutura];
								}

								if(isset($ordem["rentabilidade"][$cd_caderno_cci_estrutura]))
								{
									$ordem_grafico[$i] = $ordem["rentabilidade"][$cd_caderno_cci_estrutura];
								}
							}
							if(($item["tp_grafico"] == "L") AND ($data["fl_ano_anterior"]))
							{
								/*
								$args["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura_referencia"];
								
								$this->caderno_cci_model->estrutura($result, $args);
								$estrutura_referencia = $result->row_array();
								
								if(count($estrutura_referencia) > 0)
								{
									$args_estrutura["cd_caderno_cci_estrutura"] = $estrutura_referencia["cd_caderno_cci_estrutura"];
									$args_estrutura["mes"]                      = 12;
									
									$this->caderno_cci_model->estrutura_valor($result, $args_estrutura);
									$val_estrutura_referencia = $result->row_array();
									
									if((isset($estrutura_referencia["calculo"])) AND ($estrutura_referencia["calculo"] != ""))
									{
										$arr_calculo_referencia = $this->calcular_formula($estrutura_referencia["calculo"], 12);

										$arr_calculo_referencia_2 = $this->calculo_acumulado($arr_calculo_referencia, "nr", 12);

										$valores[$i][] = $arr_calculo_referencia_2["valor_mes"];
									}
									if(isset($val_estrutura_referencia["nr_rentabilidade"]))
									{
										$valores[$i][] = $val_estrutura_referencia["nr_rentabilidade"];
									}
								}
								else
								{
									$valores[$i][] = 0;
								}
								*/
								$valores[$i][] = 0;
							}

							foreach($rentabilidade_valor_acumulado["valores"] as $val) 
							{
								$valores[$i][] = $val;
							}

							//SLIDE BARRA AGRUPADA
							if($item["tp_grafico"] == "A")
							{
								$this->grupo_barra($agrupamento_config["serie"], $agrupamento_config["agrupamento"], 'rentabilidade', $cd_caderno_cci_estrutura, $valores[$i][$mes-1]);
							}
						}

						if($item["tp_grafico"] == "R")
						{
							if($rentabilidade["fl_real"] == "S")
							{
								$ordem_rent[$i] = "fl_real";
							}
							elseif($rentabilidade["fl_nominal"] == "S")
							{
								$ordem_rent[$i] = "fl_nominal";
							}
						}
						
						$i++;

						if(isset($arr_total["nr_valor_atual"]))
						{
							$arr_total["ordem"] = ($tabela[$i-1]["ordem"]+1);

							$tabela[$i] = $arr_total;
						}	

						if(isset($arr_realizado_total["nr_realizado"]))
						{
							$arr_realizado_total["ordem"] = ($tabela[$i-1]["ordem"]+1);

							$tabela[$i] = $arr_realizado_total;
						}				
					}
				}

				if($item["tp_grafico"] == "T")
				{
					$tabela          = $this->organiza_tabela($tabela);
					$tabela_anterior = $this->organiza_tabela($tabela_anterior);
				}
				elseif (in_array($item["tp_grafico"], array("P", "L", "B")) AND count($ordem_grafico) > 0) 
				{
					$legenda = $this->organiza_array($ordem_grafico, $legenda);
					$valores = $this->organiza_array($ordem_grafico, $valores);
					$cor     = $this->organiza_array($ordem_grafico, $cor);
				}
				elseif($item["tp_grafico"] == "R")
				{
					$id = count($rent_hist);

					$rent_hist[$id][0] = $nr_ano;
					$rent_hist[$id][1] = 0;
					$rent_hist[$id][2] = 0;
					$rent_hist[$id][3] = 0; 

					foreach ($ordem_rent as $key_ordem => $item_ordem) {
						switch ($item_ordem) {
						    case "fl_nominal":
						        $rent_hist[$id][1] = number_format($valores[$key_ordem][$mes-1], 2, ",", ".");  

						        $nr_nominal_acum *= (($valores[$key_ordem][$mes-1]/100)+1);
						        break;
						    case "fl_inpc":
						        $rent_hist[$id][2] = number_format($valores[$key_ordem][$mes-1], 2, ",", ".");  

						        $nr_inpc_acum *= (($valores[$key_ordem][$mes-1]/100)+1);
						        break;
						    case "fl_real":
						        $rent_hist[$id][3] = number_format($valores[$key_ordem][$mes-1], 2, ",", ".");  

						        $nr_real_acum *= (($valores[$key_ordem][$mes-1]/100)+1);
						        break;
						}
					}

					$id = count($rent_hist);

					$rent_hist[$id][0] = "Acumulado";
					$rent_hist[$id][1] = number_format(($nr_nominal_acum - 1) * 100, 2, ",", ".");
					$rent_hist[$id][2] = number_format(($nr_inpc_acum - 1) * 100, 2, ",", ".");
					$rent_hist[$id][3] = number_format(($nr_real_acum - 1) * 100, 2, ",", ".");
				}

				$data["collection"][$j]["nota_rodape"]     = utf8_encode($item["nota_rodape"]);
				$data["collection"][$j]["tp_grafico"]      = $item["tp_grafico"];
				$data["collection"][$j]["titulo"]          = utf8_encode($item["ds_caderno_cci_grafico"]);
				$data["collection"][$j]["legenda"]         = array_map("arrayToUTF8", $legenda); 
				$data["collection"][$j]["valores"]         = $valores;
				$data["collection"][$j]["tab_campo"]       = (isset($campo) ? $campo : "");
				$data["collection"][$j]["tabelas"]         = $tabela;
				$data["collection"][$j]["tabela_anterior"] = $tabela_anterior;
				$data["collection"][$j]["cor"]             = $cor;
				$data["collection"][$j]["ds_mes"]          = $ds_mes;
				$data["collection"][$j]["rowspan"]         = $rowspan;
				$data["collection"][$j]["rent_hist"]       = $rent_hist;
				$data["collection"][$j]["ordem_rent"]      = $ordem_rent;
				$data["collection"][$j]["agrupamento"]     = $agrupamento_config;
			}

			$j ++;
		}

		return $data;
    }

    private function carrega($cd_caderno_cci, $mes = "")
    {
    	$result = null;
		$args   = Array();
		$data   = Array();

		$args["mes"] = $mes;

		if(trim($args["mes"]) == "")
		{
			$args["mes"] = date("m");
		}

		$args["cd_caderno_cci"] = intval($cd_caderno_cci);

		$this->caderno_cci_model->fechamento($result, $args);
		$row = $result->row_array();

		return json_decode($row["parametro"], true);
    }

    function apresentacao($cd_caderno_cci, $gerar = "N", $mes = "", $debug = "N")
    {
    	if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			ini_set('max_execution_time', 0);

			if(trim($gerar) == "N")
			{
				$data = $this->gerar($cd_caderno_cci, $mes);
			}
			else
			{
				$data = $this->carrega($cd_caderno_cci, $mes);
			}

			$data["qt_total"] = 0;

			if(trim($debug) == "S")
			{
				echo "<pre>";
				print_r($data);
				exit;
			}

			$this->load->view("gestao/caderno_cci_relatorio/apresentacao", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function fechar($cd_caderno_cci, $mes = "", $debug = 'N')
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			ini_set('max_execution_time', 0);

			$data = $this->gerar($cd_caderno_cci, $mes);

			if(trim($debug) == "S")
			{
				echo "<pre>";
				print_r($data);
				exit;
			}

			$parametro = json_encode($data);

			switch (json_last_error()) 
			{
		        /* 
		        case JSON_ERROR_NONE:
		            echo ' - No errors';
		        break;
		        */
		        case JSON_ERROR_DEPTH:
		            echo ' - Maximum stack depth exceeded';
		        break;
		        case JSON_ERROR_STATE_MISMATCH:
		            echo ' - Underflow or the modes mismatch';
		        break;
		        case JSON_ERROR_CTRL_CHAR:
		            echo ' - Unexpected control character found';
		        break;
		        case JSON_ERROR_SYNTAX:
		            echo ' - Syntax error, malformed JSON';
		        break;
		        case JSON_ERROR_UTF8:
		            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
		        break;
		        default:
		            echo ' - Unknown error';
		        break;
		    }

		    if(trim($parametro) == '')
		    {
		    	exit;
		    }

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);
			$args["nr_mes"]         = $data["row"]["mes"];
			$args["parametro"]      = $parametro;
			$args["cd_usuario"]     = $this->session->userdata("codigo");

			$this->caderno_cci_model->fechamento_salvar($result, $args);

			redirect("gestao/caderno_cci_relatorio/index/".$args["cd_caderno_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function salvar_imagem()
	{
    	if(gerencia_in(array("GIN", "DE")))
		{
			$result = null;
			$args   = array();
			$data   = array();
		
			$id_imagem = $this->input->post('id_imagem');
			$nr_ano    = $this->input->post('nr_ano');
			$nr_mes    = $this->input->post('nr_mes');
			$ob_imagem = $this->input->post('ob_imagem');
			
			$ob_imagem = str_replace('data:image/png;base64,', '', $ob_imagem);
			$ob_imagem = str_replace(' ', '+', $ob_imagem);

			$ob_data = base64_decode($ob_imagem);
			//$arq = md5(uniqid(rand(), true));
			$arq = strtolower($this->session->userdata("usuario"))."_".$nr_ano.'_'.$nr_mes."_".$id_imagem;
			$file = '../cieprev/up/caderno_cci_relatorio/'.$arq.'.png';
			
			file_put_contents($file, $ob_data);			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function gera_pdf($nr_ano = 0, $nr_mes = 0, $qt = 0)
	{
		set_time_limit(0);
    	if(gerencia_in(array("GIN", "DE")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$this->load->plugin('fpdf');
				
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');	
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Comitê Consultivo de Investimentos";
			$ob_pdf->header_subtitulo = true;
			$ob_pdf->header_subtitulo_texto = 'Referente: '.$nr_mes.'/'.$nr_ano;				

			$ob_pdf->SetLineWidth(0);
			$ob_pdf->SetDrawColor(0, 0, 0);

			$i=0;
			while($i < $qt)
			{
				$margem_x = 10;
				
				$arq = './up/caderno_cci_relatorio/'.strtolower($this->session->userdata("usuario"))."_".$nr_ano.'_'.$nr_mes."_".$i.".png";
				list($w, $h) = getimagesize($arq);  
				
				if($w > $h)
				{
					$lim_width  = 1050;
					$lim_height = 640;	
					$pr_height = ceil(($lim_width * 100) / $w);
					$height = ($pr_height * $h) / 100;					
					$width  = $lim_width;	

					if($height > $lim_height)
					{
						$pr_width = ceil(($lim_height * 100) / $h);
						$width = ($pr_width * $w) / 100;					
						$height  = $lim_height;								
					}
					
					$ob_pdf->AddPage("L");
				}
				else
				{
					$lim_width  = 720;
					$lim_height = 900;
					$pr_width = ceil(($lim_height * 100) / $h);
					$width = ($pr_width * $w) / 100;					
					$height  = $lim_height;						
					
					if($width > $lim_width)
					{
						$pr_height = ceil(($lim_width * 100) / $w);
						$height = ($pr_height * $h) / 100;					
						$width  = $lim_width;							
					}		
					
					$ob_pdf->AddPage("P");
				}

				if($width < $lim_width)
				{
					$margem_x+=  $ob_pdf->ConvertSize(floor(($lim_width - $width) / 2));
				}				
					
				
				#$ob_pdf->MultiCell(190, 2, $w."|".$h."|".$width."|".$height."|".$lim_width."|".$lim_height."|".$margem_x, '0', 'L');
				$ob_pdf->Image($arq, $margem_x, $ob_pdf->GetY(), $ob_pdf->ConvertSize($width), $ob_pdf->ConvertSize($height),'','',true);
				
				//unlink($arq);
				$i++;
			}

	        $ob_pdf->Output();
	        exit;	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}
}
?>