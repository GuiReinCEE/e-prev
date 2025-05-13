<?php
class Caderno_cci_csv extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("gestao/caderno_cci_model");
    }

    private function recursividade_estrutura($cd_caderno_cci, $cd_caderno_cci_estrutura_pai, &$array = array(), $fl_grupo = "", $nivel = 0)
	{
		$result = null;
		$args   = Array();

		$nivel ++;

		$args["cd_caderno_cci"]               = $cd_caderno_cci;
		$args["cd_caderno_cci_estrutura_pai"] = $cd_caderno_cci_estrutura_pai;
		$args["fl_grupo"]                     = $fl_grupo;
		
		$this->caderno_cci_model->estrutura_listar($result, $args);
		$collection = $result->result_array();

		$i = count($array);

		foreach($collection as $key => $item)
		{
			$item["nivel"] = $nivel;

			$array[$i] = $item;

			$i++;

			$i = $this->recursividade_estrutura($cd_caderno_cci, $item["cd_caderno_cci_estrutura"], $array, "", $nivel);	
		}
	
		return $i;
	}

    public function index($cd_caderno_cci)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["cd_caderno_cci"] = $cd_caderno_cci;

			$this->caderno_cci_model->carrega($result, $args);
			$caderno = $result->row_array();

			$args["calculo"] = "N";

			$this->caderno_cci_model->estrutura_pai_principal($result, $args);
			$rentabilidade = $result->result_array();

			$i = 0;

			$collection = array();

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

			$estrutura = array();

			foreach($collection as $key => $item)
			{
				$estrutura[$key] = $item;

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

				$estrutura[$key]["nr_ordem"] = $ordem;
			}	

			$vl_carteira = 0;
			foreach ($estrutura as $key => $item) 
			{
				$args["cd_caderno_cci_estrutura"] = $item["cd_caderno_cci_estrutura"];

    			$this->caderno_cci_model->estrutura_valor($result, $args);
				$valores = $result->result_array();

				//$estrutura[$key]['valores'] = $valores;

				$estrutura[$key]['nr_valor_atual'] = 0;
				$estrutura[$key]['rentabilidade_acumulada'] = 0;

				$realizado_acumulado = 0;

				foreach ($valores as $key2 => $item2)
				{
					$realizado = ($item2['nr_rentabilidade']/100)+1;

					if($realizado_acumulado == 0)
					{
						$realizado_acumulado = $realizado;
					}
					else
					{
						$realizado_acumulado = $realizado_acumulado * $realizado;
					}

					$estrutura[$key]['rentabilidade'][$key2] = $item2['nr_rentabilidade'];
					$estrutura[$key]['nr_valor_atual']       = $item2['nr_valor_atual'];
				}

				$estrutura[$key]['rentabilidade_acumulada'] = ($realizado_acumulado-1)*100;

				if(trim($item['fl_total']) == 'S')
				{
					$vl_carteira = $item2['nr_valor_atual'];
				}

				//echo $item['nr_ordem'].' '.$item["ds_caderno_cci_estrutura"].br(); 
			}

			$csv = array();
			
			foreach ($estrutura as $key => $item) 
			{
				$estrutura[$key]['alocacao'] = 0;
				if(isset($item['nr_valor_atual']))
				{
					$estrutura[$key]['alocacao'] = ($item['nr_valor_atual']*100)/$vl_carteira;
				}

				$csv[$key] = array(
					$item['nr_ordem'].' '.$item["ds_caderno_cci_estrutura"],
					$estrutura[$key]['alocacao'],
					$item['nr_valor_atual'],
					$item['rentabilidade_acumulada']

				);
			}

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=caderno_'.$caderno['nr_ano'].'.csv');

			$saida = fopen('php://output', 'w');

			foreach ($csv as $linha) {
			    fwrite($saida, $linha[0].';'.number_format($linha[1], 2, ",", ".").'%'.';'.number_format($linha[2], 2, ",", ".").';'.number_format($linha[3], 2, ",", ".").'%'."\r\n");
			}

			fclose($saida);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
}