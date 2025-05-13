<?php
class Caderno_cci extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model("gestao/caderno_cci_model");
    }

    private function get_meses()
    {
    	$meses = array(
			'01','02','03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
		);

		return $meses;
    }

    private function montar_estrutura($estrutura_tabela, &$i, &$collection)
    {
    	$meses = $this->get_meses();

    	foreach ($estrutura_tabela as $key => $item) 
		{
			$realizado_acumulado = 0;
			
			$args["cd_caderno_cci_estrutura"] = $item["cd_caderno_cci_estrutura"];

			$this->caderno_cci_model->estrutura($result, $args);
			$estrutura = $result->row_array();

			$collection[$i] = array(
				'cd_caderno_cci_estrutura' => $item["cd_caderno_cci_estrutura"],
				'ds_caderno_cci_estrutura' => $estrutura['ds_caderno_cci_estrutura']
			);

			if(isset($item['cd_caderno_cci_benchmark']))
			{
				$args["cd_caderno_cci_benchmark"] = $item["cd_caderno_cci_benchmark"];

				$realizado_benchmark_acumulado = 0;
			}
			else
			{
				$realizado_benchmark_acumulado = '';
			}

			foreach ($meses as $key_mes => $item_mes) 
			{
				$args["mes"] = $item_mes;

				$this->caderno_cci_model->estrutura_valor($result, $args);
				$row = $result->row_array();

				$collection[$i]['rentabilidade'][] = (isset($row['nr_rentabilidade']) ? floatval($row['nr_rentabilidade']) : '0.00');

				if(isset($row['nr_rentabilidade']))
				{
					$realizado = ($row['nr_rentabilidade']/100)+1;

					if($realizado_acumulado == 0)
					{
						$realizado_acumulado = $realizado;
					}
					else
					{
						$realizado_acumulado = $realizado_acumulado * $realizado;
					}
				}

				if(isset($item['cd_caderno_cci_benchmark']))
				{
					$this->caderno_cci_model->benchmark_valor($result, $args);
					$benchmark_valor = $result->row_array();

					if(isset($benchmark_valor['nr_benchmark']))
					{
						$realizado = ($benchmark_valor['nr_benchmark']/100)+1;

						if($realizado_benchmark_acumulado == 0)
						{
							$realizado_benchmark_acumulado = $realizado;
						}
						else
						{
							$realizado_benchmark_acumulado = $realizado_benchmark_acumulado * $realizado;
						}
					}
				}
			}

			if(floatval($realizado_acumulado) > 0.00)
			{
				$realizado_acumulado = ($realizado_acumulado-1)*100;
			}

			if(floatval($realizado_benchmark_acumulado) > 0.00)
			{
				$realizado_benchmark_acumulado = ($realizado_benchmark_acumulado-1)*100;
			}

			$collection[$i]['rentabilidade'][] = $realizado_acumulado;
			$collection[$i]['rentabilidade'][] = $realizado_benchmark_acumulado;

			if(isset($item['sub_estrutura']) AND count($item['sub_estrutura']) > 0)
			{
				$i2 = 0;
				$collection[$i]['sub'] = array();

				$this->montar_estrutura($item['sub_estrutura'], $i2, $collection[$i]['sub']);
			}

			$i++;
		}
    }

    private function montar_indice($indice_tabela, &$i, &$collection)
    {
    	$meses = $this->get_meses();

    	foreach ($indice_tabela as $key => $item) 
		{
			$realizado_acumulado = 0;
			$args["cd_caderno_cci_indice"] = $item["cd_caderno_cci_indice"];

			$this->caderno_cci_model->indice($result, $args);
			$indice = $result->row_array();

			$collection[$i] = array(
				'cd_caderno_cci_estrutura' => $item["cd_caderno_cci_indice"],
				'ds_caderno_cci_estrutura' => $indice['ds_caderno_cci_indice']
			);

			foreach ($meses as $key_mes => $item_mes) 
			{
				$args["mes"] = $item_mes;

				$this->caderno_cci_model->indice_valor($result, $args);
				$row = $result->row_array();

				$collection[$i]['rentabilidade'][] = (isset($row['nr_indice']) ? floatval($row['nr_indice']) : '0.00');

				if(isset($row['nr_indice']))
				{
					$realizado = ($row['nr_indice']/100)+1;

					if($realizado_acumulado == 0)
					{
						$realizado_acumulado = $realizado;
					}
					else
					{
						$realizado_acumulado = $realizado_acumulado * $realizado;
					}
				}
			}

			if(floatval($realizado_acumulado) > 0.00)
			{
				$realizado_acumulado = ($realizado_acumulado-1)*100;
			}

			$collection[$i]['rentabilidade'][] = $realizado_acumulado;
			$collection[$i]['rentabilidade'][] = '';
			$i++;
		}
    }

    private function montar_benchmark($benchmark_tabela,  &$i, &$collection)
    {
    	$meses = $this->get_meses();

    	foreach ($benchmark_tabela as $key => $item) 
		{
			$realizado_acumulado = 0;
			$args["cd_caderno_cci_benchmark"] = $item["cd_caderno_cci_benchmark"];

			$this->caderno_cci_model->benchmark($result, $args);
			$indice = $result->row_array();

			$collection[$i] = array(
				'cd_caderno_cci_estrutura' => $item["cd_caderno_cci_benchmark"],
				'ds_caderno_cci_estrutura' => $indice['ds_caderno_cci_benchmark']
			);

			foreach ($meses as $key_mes => $item_mes) 
			{
				$args["mes"] = $item_mes;

				$this->caderno_cci_model->benchmark_valor($result, $args);
				$row = $result->row_array();

				$collection[$i]['rentabilidade'][] = (isset($row['nr_benchmark']) ? floatval($row['nr_benchmark']) : '0.00');

				if(isset($row['nr_benchmark']))
				{
					$realizado = ($row['nr_benchmark']/100)+1;

					if($realizado_acumulado == 0)
					{
						$realizado_acumulado = $realizado;
					}
					else
					{
						$realizado_acumulado = $realizado_acumulado * $realizado;
					}
				}
			}

			if(floatval($realizado_acumulado) > 0.00)
			{
				$realizado_acumulado = ($realizado_acumulado-1)*100;
			}

			$collection[$i]['rentabilidade'][] = $realizado_acumulado;
			$collection[$i]['rentabilidade'][] = '';

			$i++;
		}
    }

    private function get_dados_rentabilidade_planos_segmentos($nr_ano, $fl_tipo)
    {
    	if(intval($nr_ano) == 2023)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 3693,
						'cd_caderno_cci_benchmark' => 378
					),
					array(
						'cd_caderno_cci_estrutura' => 3824,
						'cd_caderno_cci_benchmark' => 373	
					),
					array(
						'cd_caderno_cci_estrutura' => 3939,
						'cd_caderno_cci_benchmark' => 379
					),
					array(
						'cd_caderno_cci_estrutura' => 4003,
						'cd_caderno_cci_benchmark' => 389
					),
					array(
						'cd_caderno_cci_estrutura' => 4074,
						'cd_caderno_cci_benchmark' => 390
					),
					array(
						'cd_caderno_cci_estrutura' => 4124
					),
					array(
						'cd_caderno_cci_estrutura' => 4126
					),
				);
    		}

    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 85
					),
					array(
						'cd_caderno_cci_indice' => 86
					),
					array(
						'cd_caderno_cci_indice' => 87
					),
					array(
						'cd_caderno_cci_indice' => 88
					),
					array(
						'cd_caderno_cci_indice' => 89
					),
					array(
						'cd_caderno_cci_indice' => 90
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 378
					),
					array(
						'cd_caderno_cci_benchmark' => 379
					),
					array(
						'cd_caderno_cci_benchmark' => 391
					)
				);
    		}
    	}

    	if(intval($nr_ano) == 2024)
    	{
			##gestao.caderno_cci_estrutura##
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4339, //SELECT * FROM gestao.caderno_cci_estrutura WHERE cd_caderno_cci_estrutura_referencia = 4339

						'cd_caderno_cci_benchmark' => 399 //SELECT * FROM gestao.caderno_cci_benchmark WHERE cd_caderno_cci_benchmark_referencia = 399
					),
					array(
						'cd_caderno_cci_estrutura' => 4362,
						'cd_caderno_cci_benchmark' => 394	
					),
					array(
						'cd_caderno_cci_estrutura' => 4486,
						'cd_caderno_cci_benchmark' => 400
					),
					array(
						'cd_caderno_cci_estrutura' => 4540,
						'cd_caderno_cci_benchmark' => 411
					),
					array(
						'cd_caderno_cci_estrutura' => 4620,
						'cd_caderno_cci_benchmark' => 412
					),
					array(
						'cd_caderno_cci_estrutura' => 4669
					),
					array(
						'cd_caderno_cci_estrutura' => 4682
					),
				);
    		}

			##gestao.caderno_cci_indice##
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 91
					),
					array(
						'cd_caderno_cci_indice' => 92
					),
					array(
						'cd_caderno_cci_indice' => 93
					),
					array(
						'cd_caderno_cci_indice' => 94
					),
					array(
						'cd_caderno_cci_indice' => 95
					),
					array(
						'cd_caderno_cci_indice' => 96
					),
				);
    		}
            ##gestao.caderno_cci_benchmark##
    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 399
					),
					array(
						'cd_caderno_cci_benchmark' => 400
					),
					array(
						'cd_caderno_cci_benchmark' => 413
					)
				);
    		}
    	}
		
		if(intval($nr_ano) == 2025)
    	{
			##gestao.caderno_cci_estrutura##
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4820, //SELECT * FROM gestao.caderno_cci_estrutura WHERE cd_caderno_cci_estrutura_referencia = 4339

						'cd_caderno_cci_benchmark' => 419 //SELECT * FROM gestao.caderno_cci_benchmark WHERE cd_caderno_cci_benchmark_referencia = 399
					),
					array(
						'cd_caderno_cci_estrutura' => 4971,
						'cd_caderno_cci_benchmark' => 414	
					),
					array(
						'cd_caderno_cci_estrutura' => 4987,
						'cd_caderno_cci_benchmark' => 420
					),
					array(
						'cd_caderno_cci_estrutura' => 5109,
						'cd_caderno_cci_benchmark' => 431
					),
					array(
						'cd_caderno_cci_estrutura' => 5126,
						'cd_caderno_cci_benchmark' => 432
					),
					array(
						'cd_caderno_cci_estrutura' => 5189
					),
					array(
						'cd_caderno_cci_estrutura' => 5204
					),
				);
    		}

			##gestao.caderno_cci_indice##
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 97 //SELECT * FROM gestao.caderno_cci_indice WHERE cd_caderno_cci_indice_referencia = 413
					),
					array(
						'cd_caderno_cci_indice' => 98
					),
					array(
						'cd_caderno_cci_indice' => 99
					),
					array(
						'cd_caderno_cci_indice' => 100
					),
					array(
						'cd_caderno_cci_indice' => 101
					),
					array(
						'cd_caderno_cci_indice' => 102
					),
				);
    		}
            ##gestao.caderno_cci_benchmark##
    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 419
					),
					array(
						'cd_caderno_cci_benchmark' => 420
					),
					array(
						'cd_caderno_cci_benchmark' => 433
					)
				);
    		}
    	}
    }

    public function rentabilidade_planos_segmentos($cd_caderno_cci)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if (in_array(intval($data["row"]['nr_ano']), array(2023, 2024, 2025))) 
			{
				$collection = array();

				$i = 0;

				$meses = $this->get_meses();

				$estrutura_tabela = $this->get_dados_rentabilidade_planos_segmentos(intval($data["row"]['nr_ano']), 'E');
				
				$this->montar_estrutura($estrutura_tabela, $i, $collection);

				$indice_tabela = $this->get_dados_rentabilidade_planos_segmentos(intval($data["row"]['nr_ano']), 'I');

				$this->montar_indice($indice_tabela, $i, $collection);

				$benchmark_tabela = $this->get_dados_rentabilidade_planos_segmentos(intval($data["row"]['nr_ano']), 'B');

				$this->montar_benchmark($benchmark_tabela, $i, $collection);

				$data['collection'] = $collection;
				$data['meses']      = $meses;

				$this->load->view("gestao/caderno_cci/rentabilidade_planos_segmentos", $data);
			}
			else
			{
				exibir_mensagem("RELATÓRIO NÃO CONFIRGURADO PARA ESSE ANO");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    private function get_dados_rentabilidade_planos($nr_ano, $fl_tipo)
    {
    	if(intval($nr_ano) == 2023)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4142,
						'cd_caderno_cci_benchmark' => 374
					),
					array(
						'cd_caderno_cci_estrutura' => 4143,
						'cd_caderno_cci_benchmark' => 376	
					),
					array(
						'cd_caderno_cci_estrutura' => 4144,
						'cd_caderno_cci_benchmark' => 375
					),
					array(
						'cd_caderno_cci_estrutura' => 4145,
						'cd_caderno_cci_benchmark' => 377
					),
					array(
						'cd_caderno_cci_estrutura' => 4146,
						'cd_caderno_cci_benchmark' => 381
					),
					array(
						'cd_caderno_cci_estrutura' => 4147,
						'cd_caderno_cci_benchmark' => 382
					),
					array(
						'cd_caderno_cci_estrutura' => 4148,
						'cd_caderno_cci_benchmark' => 383
					),
					array(
						'cd_caderno_cci_estrutura' => 4149,
						'cd_caderno_cci_benchmark' => 384
					),
					array(
						'cd_caderno_cci_estrutura' => 4150,
						'cd_caderno_cci_benchmark' => 387
					),
					array(
						'cd_caderno_cci_estrutura' => 4151,
						'cd_caderno_cci_benchmark' => 386
					),
					array(
						'cd_caderno_cci_estrutura' => 4152,
						'cd_caderno_cci_benchmark' => 388
					),
					array(
						'cd_caderno_cci_estrutura' => 4153,
						'cd_caderno_cci_benchmark' => 385
					),
					array(
						'cd_caderno_cci_estrutura' => 4188
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 85
					),
					array(
						'cd_caderno_cci_indice' => 86
					),
					array(
						'cd_caderno_cci_indice' => 87
					),
					array(
						'cd_caderno_cci_indice' => 88
					),
					array(
						'cd_caderno_cci_indice' => 89
					),
					array(
						'cd_caderno_cci_indice' => 90
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 378
					),
					array(
						'cd_caderno_cci_benchmark' => 379
					),
					array(
						'cd_caderno_cci_benchmark' => 391
					)
				);
    		}
    	}

    	if(intval($nr_ano) == 2024)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4697,
						'cd_caderno_cci_benchmark' => 395
					),
					array(
						'cd_caderno_cci_estrutura' => 4698,
						'cd_caderno_cci_benchmark' => 397	
					),
					array(
						'cd_caderno_cci_estrutura' => 4699,
						'cd_caderno_cci_benchmark' => 396
					),
					array(
						'cd_caderno_cci_estrutura' => 4700,
						'cd_caderno_cci_benchmark' => 398
					),
					array(
						'cd_caderno_cci_estrutura' => 4701,
						'cd_caderno_cci_benchmark' => 402
					),
					array(
						'cd_caderno_cci_estrutura' => 4702,
						'cd_caderno_cci_benchmark' => 403
					),
					array(
						'cd_caderno_cci_estrutura' => 4703,
						'cd_caderno_cci_benchmark' => 404
					),
					array(
						'cd_caderno_cci_estrutura' => 4704,
						'cd_caderno_cci_benchmark' => 405
					),
					array(
						'cd_caderno_cci_estrutura' => 4705,
						'cd_caderno_cci_benchmark' => 408
					),
					array(
						'cd_caderno_cci_estrutura' => 4706,
						'cd_caderno_cci_benchmark' => 407
					),
					array(
						'cd_caderno_cci_estrutura' => 4707,
						'cd_caderno_cci_benchmark' => 409
					),
					array(
						'cd_caderno_cci_estrutura' => 4708,
						'cd_caderno_cci_benchmark' => 406
					),
					array(
						'cd_caderno_cci_estrutura' => 4709,
						'cd_caderno_cci_benchmark' => 410
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 91
					),
					array(
						'cd_caderno_cci_indice' => 92
					),
					array(
						'cd_caderno_cci_indice' => 93
					),
					array(
						'cd_caderno_cci_indice' => 94
					),
					array(
						'cd_caderno_cci_indice' => 95
					),
					array(
						'cd_caderno_cci_indice' => 96
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 399
					),
					array(
						'cd_caderno_cci_benchmark' => 400
					),
					array(
						'cd_caderno_cci_benchmark' => 413
					)
				);
    		}
    	}
		
		if(intval($nr_ano) == 2025)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 5226,
						'cd_caderno_cci_benchmark' => 415
					),
					array(
						'cd_caderno_cci_estrutura' => 5227,
						'cd_caderno_cci_benchmark' => 417	
					),
					array(
						'cd_caderno_cci_estrutura' => 5228,
						'cd_caderno_cci_benchmark' => 416
					),
					array(
						'cd_caderno_cci_estrutura' => 5229,
						'cd_caderno_cci_benchmark' => 418
					),
					array(
						'cd_caderno_cci_estrutura' => 5230,
						'cd_caderno_cci_benchmark' => 422
					),
					array(
						'cd_caderno_cci_estrutura' => 5231,
						'cd_caderno_cci_benchmark' => 423
					),
					array(
						'cd_caderno_cci_estrutura' => 5232,
						'cd_caderno_cci_benchmark' => 424
					),
					array(
						'cd_caderno_cci_estrutura' => 5233,
						'cd_caderno_cci_benchmark' => 425
					),
					array(
						'cd_caderno_cci_estrutura' => 5234,
						'cd_caderno_cci_benchmark' => 428
					),
					array(
						'cd_caderno_cci_estrutura' => 5235,
						'cd_caderno_cci_benchmark' => 427
					),
					array(
						'cd_caderno_cci_estrutura' => 5236,
						'cd_caderno_cci_benchmark' => 429
					),
					array(
						'cd_caderno_cci_estrutura' => 5237,
						'cd_caderno_cci_benchmark' => 426
					),
					array(
						'cd_caderno_cci_estrutura' => 5238,
						'cd_caderno_cci_benchmark' => 430
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 97
					),
					array(
						'cd_caderno_cci_indice' => 98
					),
					array(
						'cd_caderno_cci_indice' => 99
					),
					array(
						'cd_caderno_cci_indice' => 100
					),
					array(
						'cd_caderno_cci_indice' => 101
					),
					array(
						'cd_caderno_cci_indice' => 102
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 419
					),
					array(
						'cd_caderno_cci_benchmark' => 420
					),
					array(
						'cd_caderno_cci_benchmark' => 433
					)
				);
    		}
    	}
    }

    public function rentabilidade_planos($cd_caderno_cci)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if (in_array(intval($data["row"]['nr_ano']), array(2023, 2024, 2025))) 
			{
				$collection = array();

				$i = 0;

				$meses = $this->get_meses();

				$estrutura_tabela = $this->get_dados_rentabilidade_planos(intval($data["row"]['nr_ano']), 'E');
				
				$this->montar_estrutura($estrutura_tabela, $i, $collection);

				$indice_tabela = $this->get_dados_rentabilidade_planos(intval($data["row"]['nr_ano']), 'I'); 

				$this->montar_indice($indice_tabela, $i, $collection);

				$benchmark_tabela = $this->get_dados_rentabilidade_planos(intval($data["row"]['nr_ano']), 'B');

				$this->montar_benchmark($benchmark_tabela, $i, $collection);

				$data['collection'] = $collection;
				$data['meses']      = $meses;

				$this->load->view("gestao/caderno_cci/rentabilidade_planos", $data);
			}
			else
			{
				exibir_mensagem("RELATÓRIO NÃO CONFIRGURADO PARA ESSE ANO");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    private function get_dados_rentabilidade_planos_aberto($nr_ano, $fl_tipo)
    {
    	if(intval($nr_ano) == 2023)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4142,
						'cd_caderno_cci_benchmark' => 374,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3821),
							array('cd_caderno_cci_estrutura' => 3925),
							array('cd_caderno_cci_estrutura' => 4036),
							array('cd_caderno_cci_estrutura' => 4068),
							array('cd_caderno_cci_estrutura' => 4096),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4143,
						'cd_caderno_cci_benchmark' => 376,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3822),
							array('cd_caderno_cci_estrutura' => 3980),
							array('cd_caderno_cci_estrutura' => 4004),
							array('cd_caderno_cci_estrutura' => 4069),
							array('cd_caderno_cci_estrutura' => 4091),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4144,
						'cd_caderno_cci_benchmark' => 375,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3840),
							array('cd_caderno_cci_estrutura' => 3958),
							array('cd_caderno_cci_estrutura' => 4030),
							array('cd_caderno_cci_estrutura' => 4070),
							array('cd_caderno_cci_estrutura' => 4076),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4145,
						'cd_caderno_cci_benchmark' => 377,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3911),
							array('cd_caderno_cci_estrutura' => 3959),
							array('cd_caderno_cci_estrutura' => 4018),
							array('cd_caderno_cci_estrutura' => 4054),
							array('cd_caderno_cci_estrutura' => 4080),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4146,
						'cd_caderno_cci_benchmark' => 381,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3912),
							array('cd_caderno_cci_estrutura' => 3961),
							array('cd_caderno_cci_estrutura' => 4037),
							array('cd_caderno_cci_estrutura' => 4055),
							array('cd_caderno_cci_estrutura' => 4088),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4147,
						'cd_caderno_cci_benchmark' => 382,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3913),
							array('cd_caderno_cci_estrutura' => 3978),
							array('cd_caderno_cci_estrutura' => 4029),
							array('cd_caderno_cci_estrutura' => 4056),
							array('cd_caderno_cci_estrutura' => 4094),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4148,
						'cd_caderno_cci_benchmark' => 383,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3914),
							array('cd_caderno_cci_estrutura' => 3949),
							array('cd_caderno_cci_estrutura' => 4022),
							array('cd_caderno_cci_estrutura' => 4058),
							array('cd_caderno_cci_estrutura' => 4085),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4149,
						'cd_caderno_cci_benchmark' => 384,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3915),
							array('cd_caderno_cci_estrutura' => 3963),
							array('cd_caderno_cci_estrutura' => 4023),
							array('cd_caderno_cci_estrutura' => 4049),
							array('cd_caderno_cci_estrutura' => 4083),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4150,
						'cd_caderno_cci_benchmark' => 387,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3916),
							array('cd_caderno_cci_estrutura' => 3965),
							array('cd_caderno_cci_estrutura' => 4024),
							array('cd_caderno_cci_estrutura' => 4048),
							array('cd_caderno_cci_estrutura' => 4078),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4151,
						'cd_caderno_cci_benchmark' => 386,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3917),
							array('cd_caderno_cci_estrutura' => 3966),
							array('cd_caderno_cci_estrutura' => 4025),
							array('cd_caderno_cci_estrutura' => 4047),
							array('cd_caderno_cci_estrutura' => 4082),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4152,
						'cd_caderno_cci_benchmark' => 388,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3902),
							array('cd_caderno_cci_estrutura' => 3967),
							array('cd_caderno_cci_estrutura' => 4005),
							array('cd_caderno_cci_estrutura' => 4059),
							array('cd_caderno_cci_estrutura' => 4090),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4153,
						'cd_caderno_cci_benchmark' => 385,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 3823)
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4188,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4191),
							array('cd_caderno_cci_estrutura' => 4197),
						)
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 85
					),
					array(
						'cd_caderno_cci_indice' => 86
					),
					array(
						'cd_caderno_cci_indice' => 87
					),
					array(
						'cd_caderno_cci_indice' => 88
					),
					array(
						'cd_caderno_cci_indice' => 89
					),
					array(
						'cd_caderno_cci_indice' => 90
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 378
					),
					array(
						'cd_caderno_cci_benchmark' => 379
					),
					array(
						'cd_caderno_cci_benchmark' => 391
					)
				);
    		}
    	}

    	if(intval($nr_ano) == 2024)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 4697,
						'cd_caderno_cci_benchmark' => 395,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4361),
							array('cd_caderno_cci_estrutura' => 4507),
							array('cd_caderno_cci_estrutura' => 4582),
							array('cd_caderno_cci_estrutura' => 4622),
							array('cd_caderno_cci_estrutura' => 4647),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4698,
						'cd_caderno_cci_benchmark' => 397,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4460),
							array('cd_caderno_cci_estrutura' => 4527),
							array('cd_caderno_cci_estrutura' => 4541),
							array('cd_caderno_cci_estrutura' => 4603),
							array('cd_caderno_cci_estrutura' => 4646),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4699,
						'cd_caderno_cci_benchmark' => 396,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4420),
							array('cd_caderno_cci_estrutura' => 4522),
							array('cd_caderno_cci_estrutura' => 4581),
							array('cd_caderno_cci_estrutura' => 4604),
							array('cd_caderno_cci_estrutura' => 4644),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4700,
						'cd_caderno_cci_benchmark' => 398,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4452),
							array('cd_caderno_cci_estrutura' => 4536),
							array('cd_caderno_cci_estrutura' => 4587),
							array('cd_caderno_cci_estrutura' => 4607),
							array('cd_caderno_cci_estrutura' => 4629),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4701,
						'cd_caderno_cci_benchmark' => 402,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4462),
							array('cd_caderno_cci_estrutura' => 4528),
							array('cd_caderno_cci_estrutura' => 4589),
							array('cd_caderno_cci_estrutura' => 4600),
							array('cd_caderno_cci_estrutura' => 4628),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4702,
						'cd_caderno_cci_benchmark' => 403,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4463),
							array('cd_caderno_cci_estrutura' => 4530),
							array('cd_caderno_cci_estrutura' => 4590),
							array('cd_caderno_cci_estrutura' => 4601),
							array('cd_caderno_cci_estrutura' => 4631),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4703,
						'cd_caderno_cci_benchmark' => 404,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4443),
							array('cd_caderno_cci_estrutura' => 4535),
							array('cd_caderno_cci_estrutura' => 4565),
							array('cd_caderno_cci_estrutura' => 4598),
							array('cd_caderno_cci_estrutura' => 4635),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4704,
						'cd_caderno_cci_benchmark' => 405,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4444),
							array('cd_caderno_cci_estrutura' => 4508),
							array('cd_caderno_cci_estrutura' => 4573),
							array('cd_caderno_cci_estrutura' => 4596),
							array('cd_caderno_cci_estrutura' => 4637),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4705,
						'cd_caderno_cci_benchmark' => 408,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4445),
							array('cd_caderno_cci_estrutura' => 4533),
							array('cd_caderno_cci_estrutura' => 4568),
							array('cd_caderno_cci_estrutura' => 4595),
							array('cd_caderno_cci_estrutura' => 4640),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4706,
						'cd_caderno_cci_benchmark' => 407,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4446),
							array('cd_caderno_cci_estrutura' => 4519),
							array('cd_caderno_cci_estrutura' => 4572),
							array('cd_caderno_cci_estrutura' => 4594),
							array('cd_caderno_cci_estrutura' => 4638),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4707,
						'cd_caderno_cci_benchmark' => 409,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4447),
							array('cd_caderno_cci_estrutura' => 4517),
							array('cd_caderno_cci_estrutura' => 4576),
							array('cd_caderno_cci_estrutura' => 4602),
							array('cd_caderno_cci_estrutura' => 4632),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4708,
						'cd_caderno_cci_benchmark' => 406,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4454)
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 4709,
						'cd_caderno_cci_benchmark' => 410,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4464),
							array('cd_caderno_cci_estrutura' => 4491),
							array('cd_caderno_cci_estrutura' => 4583),
							array('cd_caderno_cci_estrutura' => 4625),
						)
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 91
					),
					array(
						'cd_caderno_cci_indice' => 92
					),
					array(
						'cd_caderno_cci_indice' => 93
					),
					array(
						'cd_caderno_cci_indice' => 94
					),
					array(
						'cd_caderno_cci_indice' => 95
					),
					array(
						'cd_caderno_cci_indice' => 96
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 399
					),
					array(
						'cd_caderno_cci_benchmark' => 400
					),
					array(
						'cd_caderno_cci_benchmark' => 413
					)
				);
    		}
    	}
		
		if(intval($nr_ano) == 2025)
    	{
    		if(trim($fl_tipo) == 'E')
    		{
    			return array(
					array(
						'cd_caderno_cci_estrutura' => 5226,
						'cd_caderno_cci_benchmark' => 415,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4891),
							array('cd_caderno_cci_estrutura' => 4989),
							array('cd_caderno_cci_estrutura' => 5080),
							array('cd_caderno_cci_estrutura' => 5139),
							array('cd_caderno_cci_estrutura' => 5164),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5227,
						'cd_caderno_cci_benchmark' => 417,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4906),
							array('cd_caderno_cci_estrutura' => 5055),
							array('cd_caderno_cci_estrutura' => 5081),
							array('cd_caderno_cci_estrutura' => 5123),
							array('cd_caderno_cci_estrutura' => 5160),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5228,
						'cd_caderno_cci_benchmark' => 416,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4904),
							array('cd_caderno_cci_estrutura' => 5058),
							array('cd_caderno_cci_estrutura' => 5104),
							array('cd_caderno_cci_estrutura' => 5118),
							array('cd_caderno_cci_estrutura' => 5162),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5229,
						'cd_caderno_cci_benchmark' => 418,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4879),
							array('cd_caderno_cci_estrutura' => 5049),
							array('cd_caderno_cci_estrutura' => 5085),
							array('cd_caderno_cci_estrutura' => 5127),
							array('cd_caderno_cci_estrutura' => 5165),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5230,
						'cd_caderno_cci_benchmark' => 422,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4878),
							array('cd_caderno_cci_estrutura' => 5051),
							array('cd_caderno_cci_estrutura' => 5086),
							array('cd_caderno_cci_estrutura' => 5128),
							array('cd_caderno_cci_estrutura' => 5161),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5231,
						'cd_caderno_cci_benchmark' => 423,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4881),
							array('cd_caderno_cci_estrutura' => 5043),
							array('cd_caderno_cci_estrutura' => 5087),
							array('cd_caderno_cci_estrutura' => 5129),
							array('cd_caderno_cci_estrutura' => 5167),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5232,
						'cd_caderno_cci_benchmark' => 424,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4953),
							array('cd_caderno_cci_estrutura' => 5045),
							array('cd_caderno_cci_estrutura' => 5106),
							array('cd_caderno_cci_estrutura' => 5122),
							array('cd_caderno_cci_estrutura' => 5150),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5233,
						'cd_caderno_cci_benchmark' => 425,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4977),
							array('cd_caderno_cci_estrutura' => 4990),
							array('cd_caderno_cci_estrutura' => 5098),
							array('cd_caderno_cci_estrutura' => 5135),
							array('cd_caderno_cci_estrutura' => 5152),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5234,
						'cd_caderno_cci_benchmark' => 428,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4978),
							array('cd_caderno_cci_estrutura' => 5063),
							array('cd_caderno_cci_estrutura' => 5105),
							array('cd_caderno_cci_estrutura' => 5134),
							array('cd_caderno_cci_estrutura' => 5155),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5235,
						'cd_caderno_cci_benchmark' => 427,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4979),
							array('cd_caderno_cci_estrutura' => 5026),
							array('cd_caderno_cci_estrutura' => 5101),
							array('cd_caderno_cci_estrutura' => 5133),
							array('cd_caderno_cci_estrutura' => 5153),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5236,
						'cd_caderno_cci_benchmark' => 429,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4980),
							array('cd_caderno_cci_estrutura' => 5036),
							array('cd_caderno_cci_estrutura' => 5071),
							array('cd_caderno_cci_estrutura' => 5138),
							array('cd_caderno_cci_estrutura' => 5147),
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5237,
						'cd_caderno_cci_benchmark' => 426,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4981)
						)
					),
					array(
						'cd_caderno_cci_estrutura' => 5238,
						'cd_caderno_cci_benchmark' => 430,
						'sub_estrutura' => array(
							array('cd_caderno_cci_estrutura' => 4888),
							array('cd_caderno_cci_estrutura' => 5011),
							array('cd_caderno_cci_estrutura' => 5095),
							array('cd_caderno_cci_estrutura' => 5124),
						)
					),
				);
    		}
    		
    		if(trim($fl_tipo) == 'I')
    		{
    			return array(
					array(
						'cd_caderno_cci_indice' => 97
					),
					array(
						'cd_caderno_cci_indice' => 98
					),
					array(
						'cd_caderno_cci_indice' => 99
					),
					array(
						'cd_caderno_cci_indice' => 100
					),
					array(
						'cd_caderno_cci_indice' => 101
					),
					array(
						'cd_caderno_cci_indice' => 102
					),
				);
    		}

    		if(trim($fl_tipo) == 'B')
    		{
    			return array(
					array(
						'cd_caderno_cci_benchmark' => 419
					),
					array(
						'cd_caderno_cci_benchmark' => 420
					),
					array(
						'cd_caderno_cci_benchmark' => 433
					)
				);
    		}
    	}
    }

    public function rentabilidade_planos_aberto($cd_caderno_cci)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			if (in_array(intval($data["row"]['nr_ano']), array(2023, 2024, 2025))) 
			{
				$collection = array();

				$i = 0;

				$meses = $this->get_meses();

				$estrutura_tabela = $this->get_dados_rentabilidade_planos_aberto(intval($data["row"]['nr_ano']), 'E');

				$this->montar_estrutura($estrutura_tabela, $i, $collection);
				
				$indice_tabela = $this->get_dados_rentabilidade_planos_aberto(intval($data["row"]['nr_ano']), 'I');

				$this->montar_indice($indice_tabela, $i, $collection);

				$benchmark_tabela = $this->get_dados_rentabilidade_planos_aberto(intval($data["row"]['nr_ano']), 'B');

				$this->montar_benchmark($benchmark_tabela, $i, $collection);

				$data['collection'] = $collection;
				$data['meses']      = $meses;

				$this->load->view("gestao/caderno_cci/rentabilidade_planos_aberto", $data);
			}
			else
			{
				exibir_mensagem("RELATÓRIO NÃO CONFIRGURADO PARA ESSE ANO");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    public function importar($cd_caderno_cci, $mes)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$data["row"] = $result->row_array();

			$arquivo = $this->caderno_cci_model->get_arquivo_importar($cd_caderno_cci, $mes, $data['row']['nr_ano']);

			$data["row"]["mes"] = $mes;
			$data["row"]["ds_mes"] = mes_extenso($mes);
			$data["row"]["cd_caderno_cci_estrutura_arquivo"] = (isset($arquivo['cd_caderno_cci_estrutura_arquivo']) ? $arquivo['cd_caderno_cci_estrutura_arquivo'] : 0);
			$data["row"]["arquivo"] = (isset($arquivo['arquivo']) ? $arquivo['arquivo'] : '');
			$data["row"]["arquivo_nome"] = (isset($arquivo['arquivo_nome']) ? $arquivo['arquivo_nome'] : '');
			$data["row"]["dt_inclusao_arquivo"] = (isset($arquivo['dt_inclusao']) ? $arquivo['dt_inclusao'] : '');

			$data['importacao'] = array();

			if(trim($data["row"]["arquivo"]) != '')
			{
				$data['importacao'] = $this->get_validacao_importe(intval($cd_caderno_cci), $this->get_estrutura_importe_csv($data["row"]["arquivo"]));
			}

			$this->load->view("gestao/caderno_cci/importar", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    public function salvar_anexo_importar()
    {
    	if(gerencia_in(array("GIN")))
		{
			$cd_caderno_cci                   = $this->input->post("cd_caderno_cci", TRUE);
			$cd_caderno_cci_estrutura_arquivo = $this->input->post("cd_caderno_cci_estrutura_arquivo", TRUE);

			$args = array(
				'cd_caderno_cci' => $cd_caderno_cci,
				'nr_ano'         => $this->input->post("nr_ano", TRUE),
				'nr_mes'         => $this->input->post("mes", TRUE),
				'arquivo'        => $this->input->post("arquivo", TRUE),
				'arquivo_nome'   => $this->input->post("arquivo_nome", TRUE),
				'cd_usuario'     => $this->session->userdata('codigo')
			);

			if(intval($cd_caderno_cci_estrutura_arquivo) == 0)
			{
				$this->caderno_cci_model->salvar_anexo_importar($args);
			}
			else
			{
				$this->caderno_cci_model->atualizar_anexo_importar($cd_caderno_cci_estrutura_arquivo, $args);
			}

			redirect("gestao/caderno_cci/importar/".$cd_caderno_cci.'/'.$args['nr_mes'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    public function importa_valores($cd_caderno_cci, $mes)
    {
    	if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"] = intval($cd_caderno_cci);

			$this->caderno_cci_model->carrega($result, $args);
			$row = $result->row_array();

			$arquivo = $this->caderno_cci_model->get_arquivo_importar($cd_caderno_cci, $mes, $row['nr_ano']);

			$arquivo_enviar = (isset($arquivo['arquivo']) ? $arquivo['arquivo'] : '');

			$importacao = $this->get_validacao_importe(intval($cd_caderno_cci), $this->get_estrutura_importe_csv($arquivo_enviar));

			$dt_referencia = '01/'.$mes.'/'.$row['nr_ano'];

			foreach ($importacao['collection'] as $key => $item) 
			{	
				$estrutura_valor = $this->caderno_cci_model->get_estrutura_valor($item['cd_caderno_cci_estrutura'], $dt_referencia);

				$args["cd_caderno_cci_estrutura_valor"] = (isset($estrutura_valor['cd_caderno_cci_estrutura_valor']) ? intval($estrutura_valor['cd_caderno_cci_estrutura_valor']) : 0);
				$args["cd_caderno_cci_estrutura"]       = $item['cd_caderno_cci_estrutura'];

				$args["dt_referencia"]         = $dt_referencia;
				$args["nr_valor_atual"]        = $item['nr_valor_atual'];
				$args["nr_fluxo"]              = 0;
				$args["nr_rentabilidade"]      = $item['nr_rentabilidade'];
				$args["nr_realizado"]          = $item['nr_realizado'];
				$args["nr_metro"]              = 0;
				$args["nr_quantidade"]         = 0;
				$args["nr_valor_integralizar"] = 0;
				$args["nr_taxa_adm"]           = 0;
				$args["nr_ano_vencimento"]     = 0;
				$args["nr_participacao_fundo"] = 0;
				$args["cd_usuario"]            = $this->session->userdata('codigo');

				$this->caderno_cci_model->estrutura_valor_salvar($result, $args);
			}

			redirect("gestao/caderno_cci/estrutura_valor/".$cd_caderno_cci."/".$mes, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    private function get_estrutura_importe_csv($arquivo)
    {
    	$file = fopen('./up/caderno_cci_importe/'.$arquivo, 'r');

		$estrutura = array();
		
		$i = 0;
		while (!feof($file)) 
		{
			$linha = "";
			$linha = fgets($file, 4096);

			$array = explode(';', $linha);

			if($i > 0 && isset($array[1]))
			{
				foreach ($array as $key => $item) 
				{
					$estrutura[($i-1)][$key] = (trim($item) != '-' ? trim($item) : '');
				}
			}

			$i++;
		}

		fclose ($file);

		return $estrutura;
    }

    private function get_validacao_importe($cd_caderno_cci, $estrutura_csv)
    {
    	$result = null;
		$args   = Array();
		$data   = Array();

		$args["cd_caderno_cci"] = intval($cd_caderno_cci);

    	$this->caderno_cci_model->carrega($result, $args);
		$row = $result->row_array();

		$this->caderno_cci_model->estrutura_pai_principal($result, $args);
		$estrutura_pai = $result->result_array();
		
		$i = 0;

		$collection = array();

		foreach($estrutura_pai as $item)
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

			$estrutura[$key]["cd"] = $item['cd_caderno_cci_estrutura'];
			$estrutura[$key]["ds"] = $ordem.' - '.$item['ds_caderno_cci_estrutura'];

			//$estrutura[$key] = $item;

		}

		$csv = $estrutura_csv;

		$retorno = array();
		$retorno['collection'] = array();

		$qt_erro = 0;

		foreach ($estrutura as $key => $item) 
		{
			$ds = (isset($csv[$key]) ? $csv[$key][0] : '');
			$c1 = (isset($csv[$key]) ? trim($csv[$key][1]) : 0);
			$c2 = (isset($csv[$key]) ? trim($csv[$key][2]) : 0);
			$c3 = (isset($csv[$key]) ? trim($csv[$key][3]) : 0);
			
			//echo $ds.'  '.trim($c1).br();

			if(trim($c1) != '')
			{
				$c1 = str_replace('.', '', $c1);
    			$c1 = str_replace(',', '.', $c1);
			}

			//echo $ds.'  '.trim($c2).br();

			if(trim($c2) != '')
			{
				$c2 = str_replace('.', '', $c2);
    			$c2 = str_replace(',', '.', $c2);
    			$c2 = str_replace('%', '', $c2);
			}

			//echo $ds.'  '.trim($c3).br();

			if(trim($c3) != '')
			{
				$c3 = str_replace('.', '', $c3);
    			$c3 = str_replace(',', '.', $c3);
			}

			$retorno['collection'][] = array(
				'cd_caderno_cci_estrutura' => $item['cd'],
				'ds_csv'           		   => $ds,
				'ds_cci'                   => $item['ds'],
				'fl_ok'                    => (trim($ds) == trim($item['ds']) ? 'S' : 'N'),
				'nr_valor_atual'           => number_format((trim($c1) != '' ? $c1 : 0), 2, ",", "."),
				'nr_rentabilidade'         => number_format((trim($c2) != '' ? $c2 : 0), 4, ",", "."),
				'nr_realizado'             => number_format((trim($c3) != '' ? $c3 : 0), 2, ",", ".")
			);

			if(trim($ds) != trim($item['ds']))
			{
				$qt_erro++;
			}

			//echo br();
			//echo '<b>'. $item['ds']. '</b>'.br().$ds.br().(trim($ds) == trim($item['ds']) ? '<span style="color:green"><b>OK</b></span>' : '<span style="color:red"><b>ERRO</b></span>') .br();

			#echo  $item['ds'].';'.br();
		}

		$retorno['qt_erro'] = $qt_erro;
		$retorno['qt_csv']  = count($csv);
		$retorno['qt_cci']  = count($estrutura);


		return $retorno;
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
	
    private function web_service_sgs($cod, $mes, $ano)
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args = array(
			"mes" => $mes,
			"ano" => $ano
		);

		$this->caderno_cci_model->ultimo_dia_mes($result, $args);
		$row = $result->row_array();
	
		try {
		    $client = new SoapClient('https://www3.bcb.gov.br/sgspub/JSP/sgsgeral/FachadaWSSGS.wsdl');
			#echo "<PRE>"; var_dump($client->__getFunctions()); 		
			
			$xml =  $client->getValoresSeriesXML(array($cod), "01/".$mes."/".$ano, $row["dia"]."/".$mes."/".$ano);
			
			$respost = simplexml_load_string($xml);

			$respost = json_decode(json_encode($respost), TRUE);
		
			$indice = array(
				"referencia" => (isset($respost["SERIE"]["ITEM"]["DATA"]) ? $respost["SERIE"]["ITEM"]["DATA"] : ""),
				"valor"      => (isset($respost["SERIE"]["ITEM"]["VALOR"]) ? number_format($respost["SERIE"]["ITEM"]["VALOR"], 4, ",", ".") : ""),
				"bloqueado"  => (isset($respost["SERIE"]["ITEM"]["BLOQUEADO"]) ? $respost["SERIE"]["ITEM"]["BLOQUEADO"] : "")
			);

			return $indice;	
		} catch (Exception $e) {
		    return array();
		}
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
		if($this->get_permissao())
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
						"cd_referencia_integracao"            => $item["cd_referencia_integracao"],
						"cd_usuario"                          => $args["cd_usuario"]
					);

					$this->caderno_cci_model->projetado_salvar($result, $projetado);
				}

				//ESTRUTURA

				$this->caderno_cci_model->atualiza_estrutura_exclusao($result, $args);

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
						"nr_alocacao_estrategica"             => $item["nr_alocacao_estrategica"],
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
						"fl_real"                             => $item["fl_real"],
						"fl_nominal"                          => $item["fl_nominal"],
						"seq_estrutura"                       => $item["seq_estrutura"],
						"cd_referencia_integracao"            => $item["cd_referencia_integracao"],
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
						"fl_inpc"                          => $item["fl_inpc"],
						"cd_referencia_integracao"         => $item["cd_referencia_integracao"],
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
						"cd_referencia_integracao"            => $item["cd_referencia_integracao"],
						"cd_usuario"                          => $args["cd_usuario"]
					);

					$this->caderno_cci_model->benchmark_salvar($result, $benchmark);
				}

				//INTEGRAÇÃO ORACLE
				/*
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
						"cd_referencia_integracao"            => $item["cd_referencia_integracao"],
						"cd_usuario"                          => $args["cd_usuario"]
					);

					$this->caderno_cci_model->benchmark_salvar($result, $benchmark);
				}
				*/

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

					//GRAFICO ROTULO

					$args_grafico['cd_caderno_cci_grafico'] = $item["cd_caderno_cci_grafico"];

					$this->caderno_cci_model->grafico_rotulo_listar($result, $args_grafico);
					$collection_grafico_rotulo = $result->result_array();

					foreach($collection_grafico_rotulo as $item2)
					{
						$grafico_referencia["cd_caderno_cci_grafico_referencia"] = $item2["cd_caderno_cci_grafico"];

						$this->caderno_cci_model->grafico_referencia($result, $grafico_referencia);
						$row_grafico_referencia = $result->row_array();

						$grafico_rotulo = array(
							"cd_caderno_cci_grafico_rotulo_referencia" => $item2["cd_caderno_cci_grafico_rotulo"],
							"cd_caderno_cci_grafico_rotulo"            => 0,
							"cd_caderno_cci_grafico"                   => $row_grafico_referencia["cd_caderno_cci_grafico"],
							"cor"                                      => $item2["cor"],
							"nr_ordem"                                 => $item2["nr_ordem"],
							"ds_caderno_cci_grafico_rotulo"            => $item2["ds_caderno_cci_grafico_rotulo"],
							"cd_usuario"                               => $args["cd_usuario"]
						);

						$this->caderno_cci_model->grafico_configura_rotulo_salvar($result, $grafico_rotulo);
					}

					//GRAFICO AGRUPAMENTO

					$this->caderno_cci_model->grafico_agrupamento_listar($result, $args_grafico);
					$collection_grafico_agrupamento = $result->result_array();

					foreach($collection_grafico_agrupamento as $item2)
					{
						$grafico_referencia["cd_caderno_cci_grafico_referencia"] = $item2["cd_caderno_cci_grafico"];

						$this->caderno_cci_model->grafico_referencia($result, $grafico_referencia);
						$row_grafico_referencia = $result->row_array();

						$agrupamento = json_decode($item2["agrupamento"], true);

						$agrupamento_new = array();

						foreach($agrupamento as $key_rotulo => $item3)
						{
							$args_grafico_rotulo_referencia["cd_caderno_cci_grafico_rotulo_referencia"] = $key_rotulo;

							$this->caderno_cci_model->grafico_rotulo_referencia($result, $args_grafico_rotulo_referencia);
							$grafico_rotulo_referencia = $result->row_array();

							$chave = key($item3);
							$valor = "";

							switch (key($item3)) 
							{
							    case 'rentabilidade':
							        
							        $args["cd_caderno_cci_estrutura_referencia"] = current($item3);

									$this->caderno_cci_model->estrutura_referencia($result, $args);
									$row_estrutura_referencia = $result->row_array();

									$valor = $row_estrutura_referencia['cd_caderno_cci_estrutura'];

							        break;
							    case 'projetado':
									
							    	$args["cd_caderno_cci_projetado_referencia"] = current($item3);

									$this->caderno_cci_model->projetado_referencia($result, $args);
									$row_projetado_referencia = $result->row_array();

									$valor = $row_projetado_referencia['cd_caderno_cci_projetado'];

							        break;
							    case 'indice':

							    	$args["cd_caderno_cci_indice_referencia"] = current($item3);

									$this->caderno_cci_model->indice_referencia($result, $args);
									$row_indice_referencia = $result->row_array();

							        $valor = $row_indice_referencia['cd_caderno_cci_indice'];

							        break;
							    case 'benchmark':
							   
							    	$args["cd_caderno_cci_benchmark_referencia"] = current($item3);

									$this->caderno_cci_model->benchmark_referencia($result, $args);
									$row_benchmark_referencia = $result->row_array();

									$valor = $row_benchmark_referencia['cd_caderno_cci_benchmark'];

							        break;
							}

							$agrupamento_new[$grafico_rotulo_referencia["cd_caderno_cci_grafico_rotulo"]] = array($chave => $valor);
						}

						$grafico_agrupamento = array(
							"cd_caderno_cci_grafico_agrupamento_referencia" => $item2["cd_caderno_cci_grafico_agrupamento"],
							"cd_caderno_cci_grafico_agrupamento"            => 0,
							"cd_caderno_cci_grafico"                        => $row_grafico_referencia["cd_caderno_cci_grafico"],
							"agrupamento"                                   => json_encode($agrupamento_new),
							"nr_ordem"                                      => $item2["nr_ordem"],
							"ds_caderno_cci_grafico_agrupamento"            => $item2["ds_caderno_cci_grafico_agrupamento"],
							"cd_usuario"                                    => $args["cd_usuario"]
						);

						$this->caderno_cci_model->grafico_configura_agrupamento_salvar($result, $grafico_agrupamento);
					}
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

							if(count($row) > 0)
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

							if(count($row) > 0)
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

							if(count($row) > 0)
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

							if(count($row) > 0)
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

			$data['estrutura_oracle'] = $this->caderno_cci_model->estrutura_oracle($cd_caderno_cci, $cd_caderno_cci_estrutura);

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
					"nr_politica_max"              => 0,
					"nr_politica_min"              => 0,
					"nr_legal_max"                 => 0,
					"nr_legal_min"                 => 0,
					"nr_alocacao_estrategica"      => 0,
					"nr_rentabilidade"             => 0,
					"cd_caderno_cci_estrutura_pai" => "",
					"fl_grupo"                     => "",
					"fl_agrupar"                   => "",
					"nr_ordem"                     => "",
					"fl_campo_metro"               => "",
					"fl_campo_quantidade"          => "",
					"fl_fundo"                     => "",
					'seq_estrutura'                => ''
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
			$args["nr_alocacao_estrategica"]      = app_decimal_para_db($this->input->post("nr_alocacao_estrategica", TRUE));
			$args["nr_rentabilidade"]             = app_decimal_para_db($this->input->post("nr_rentabilidade", TRUE));
			$args["cd_caderno_cci_estrutura_pai"] = $this->input->post("cd_caderno_cci_estrutura_pai", TRUE);
			$args["fl_grupo"]                     = $this->input->post("fl_grupo", TRUE);
			$args["fl_agrupar"]                   = $this->input->post("fl_agrupar", TRUE);
			$args["nr_ordem"]                     = $this->input->post("nr_ordem", TRUE);
			$args["fl_campo_metro"]               = $this->input->post("fl_campo_metro", TRUE);
			$args["fl_campo_quantidade"]          = $this->input->post("fl_campo_quantidade", TRUE);
			$args["fl_fundo"]                     = $this->input->post("fl_fundo", TRUE);
			$args["seq_estrutura"]                = $this->input->post("seq_estrutura", TRUE);
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

					$data['cd_caderno_cci_estrutura_pai'] = $cd_caderno_cci_estrutura_pai;
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

			$this->recursividade_estrutura($cd_caderno_cci, $cd_caderno_cci_estrutura_pai, $collection, "");

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
				$data["collection"][$key]["total_filho"]                    = $item["total_filho"];
				$data["collection"][$key]["nivel"]                          = (intval($item["nivel"]) > 0 ? $item["nivel"] : 0);

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

			$cd_caderno_cci = $args["cd_caderno_cci"];

			$caderno_cci_estrutura = $this->input->post("caderno_cci_estrutura", TRUE);
			
			$caderno_cci_estrutura_nivel = $this->input->post("caderno_cci_estrutura_nivel", TRUE);

			arsort($caderno_cci_estrutura_nivel);

			$new = array();

			foreach($caderno_cci_estrutura_nivel as $key => $item)
			{
				$new[$key] = $caderno_cci_estrutura[$key];
			}

			$caderno_cci_estrutura = $new;

			$args["cd_usuario"] = $this->session->userdata("codigo");

			$arr_args = array();

			foreach($caderno_cci_estrutura as $key => $item)
			{
				$args["cd_caderno_cci_estrutura_valor"] = $item;
				$args["cd_caderno_cci_estrutura"]       = $key;
				$fl_pai                                 = $this->input->post("fl_pai_".$key, TRUE);

				$args["nr_valor_atual"]        = $this->input->post("nr_valor_atual_".$key, TRUE);
				$args["nr_fluxo"]              = $this->input->post("nr_fluxo_".$key, TRUE);
				$args["nr_rentabilidade"]      = $this->input->post("nr_rentabilidade_".$key, TRUE);
				$args["nr_realizado"]          = $this->input->post("nr_realizado_".$key, TRUE);
				$args["nr_metro"]              = $this->input->post("nr_metro_".$key, TRUE);
				$args["nr_quantidade"]         = $this->input->post("nr_quantidade_".$key, TRUE);
				$args["nr_valor_integralizar"] = $this->input->post("nr_valor_integralizar_".$key, TRUE);
				$args["nr_taxa_adm"]           = $this->input->post("nr_taxa_adm_".$key, TRUE);
				$args["nr_ano_vencimento"]     = $this->input->post("nr_ano_vencimento_".$key, TRUE);
				$args["nr_participacao_fundo"] = $this->input->post("nr_participacao_fundo_".$key, TRUE);

				$arr_args[$key] = $this->caderno_cci_model->estrutura_valor_salvar($result, $args);
				/*
				if($fl_pai == "N")
				{
					$args["nr_valor_atual"]        = $this->input->post("nr_valor_atual_".$key, TRUE);
					$args["nr_fluxo"]              = $this->input->post("nr_fluxo_".$key, TRUE);
					$args["nr_rentabilidade"]      = $this->input->post("nr_rentabilidade_".$key, TRUE);
					$args["nr_realizado"]          = $this->input->post("nr_realizado_".$key, TRUE);
					$args["nr_metro"]              = $this->input->post("nr_metro_".$key, TRUE);
					$args["nr_quantidade"]         = $this->input->post("nr_quantidade_".$key, TRUE);
					$args["nr_valor_integralizar"] = $this->input->post("nr_valor_integralizar_".$key, TRUE);
					$args["nr_taxa_adm"]           = $this->input->post("nr_taxa_adm_".$key, TRUE);
					$args["nr_ano_vencimento"]     = $this->input->post("nr_ano_vencimento_".$key, TRUE);
					$args["nr_participacao_fundo"] = $this->input->post("nr_participacao_fundo_".$key, TRUE);

					$arr_args[$key] = $this->caderno_cci_model->estrutura_valor_salvar($result, $args);
				}
				*/
			}	
			/*
			foreach($caderno_cci_estrutura as $key => $item)
			{
				$args["cd_caderno_cci_estrutura_valor"] = $item;
				$args["cd_caderno_cci_estrutura"]       = $key;
				$args["nr_quantidade"]                  = $this->input->post("nr_quantidade_".$key, TRUE);
				$fl_pai                                 = $this->input->post("fl_pai_".$key, TRUE);

				if($fl_pai == "S")
				{
					$arr_args[$key] = $this->caderno_cci_model->estrutura_valor_calcula_salvar($result, $args);
				}
			}

			$new = array();

			foreach($caderno_cci_estrutura_nivel as $key => $item)
			{
				$new[$key] = $arr_args[$key];
			}

			$arr_args = $new;

			foreach($arr_args as $key => $item)
			{
				$args["cd_caderno_cci_estrutura"] = $key;

				$this->caderno_cci_model->estrutura($result, $args);
				$row = $result->row_array();

				if(intval($row["cd_caderno_cci_estrutura_pai"]) > 0)
				{
					$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

					$this->caderno_cci_model->estrutura($result, $args);
					$row_pai = $result->row_array();

					$args = array(
						"cd_caderno_cci_estrutura_valor"     => $item,
						"cd_caderno_cci_estrutura_valor_pai" => $arr_args[$row_pai["cd_caderno_cci_estrutura"]],
						"cd_usuario"                         => $this->session->userdata("codigo")
					);

					$this->caderno_cci_model->estrutura_valor_participacao_salvar($result, $args);
				}
			}
		
			foreach($arr_args as $key => $item)
			{
				$args["cd_caderno_cci_estrutura"] = $key;

				$this->caderno_cci_model->estrutura($result, $args);
				$row = $result->row_array();

				if(intval($row["total_filho"]) > 0)
				{
					$this->caderno_cci_model->estrutura_filho($result, $args);
					$collection = $result->result_array();

					$arr = array();

					foreach($collection as $key2 => $item2)
					{
						$arr[] = $arr_args[$item2["cd_caderno_cci_estrutura"]];
					}

					$args = array(
						"cd_caderno_cci_estrutura_valor" => $item,
						"caderno_cci_estrutura_filho"    => $arr,
						"cd_usuario"                     => $this->session->userdata("codigo")
					);

					$this->caderno_cci_model->estrutura_valor_rentabilidade_pai_salvar($result, $args);
	
				}
			}
			*/

			redirect("gestao/caderno_cci/estrutura_valor/".$cd_caderno_cci."/".$this->input->post("mes", TRUE), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function get_valores_oracle()
	{
		$result = null;
		$args   = Array();
		$data   = Array();

		$args["cd_caderno_cci_estrutura"] = $this->input->post("cd_caderno_cci_estrutura_pai", TRUE);
		$args["cd_caderno_cci"]           = $this->input->post("cd_caderno_cci", TRUE);
		$nr_ano                           = $this->input->post("nr_ano", TRUE);
		$nr_mes                           = $this->input->post("nr_mes", TRUE);

		$collection = array();

		$this->caderno_cci_model->estrutura($result, $args);
		$row = $result->row_array();

		$collection[0] = $row;

		$this->recursividade_estrutura($args["cd_caderno_cci"], $args["cd_caderno_cci_estrutura"], $collection, "");

		$result = array();

		$i = 0;

		foreach ($collection as $key => $item) 
		{
			$valores = $this->caderno_cci_model->get_valor_oracle($item["seq_estrutura"], $nr_mes, $nr_ano);

			foreach ($valores as $key2 => $item2) 
			{
				$result[$i]['campo'] = $item2['ds_campo'].'_'.$item['cd_caderno_cci_estrutura'];
				
				switch ($item2['ds_campo']) 
				{
				    case 'nr_rentabilidade':
				        $result[$i]['valor'] = number_format($item2['vl_cad_mes'], 4, ",", ".");
				        break;
				    case 'nr_valor_atual':
				        $result[$i]['valor'] = number_format($item2['vl_cad_mes'], 2, ",", ".");
				        break;
				    case 'nr_metro':
				        $result[$i]['valor'] = intval($item2['vl_cad_mes']);
				        break;
				}

				$i++;
			}

			$i++;
		}

		echo json_encode($result);
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

				$this->recursividade_estrutura($cd_caderno_cci, $item["cd_caderno_cci_estrutura"], $arr, "");

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

	function grafico_configurar($cd_caderno_cci, $cd_caderno_cci_grafico, $cd_caderno_cci_grafico_rotulo = 0)
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

				$data["arr_campo"][] = array("value" => "nr_participacao",         "text" => "Part. (%)");
				$data["arr_campo"][] = array("value" => "nr_valor_atual",          "text" => "Valor");
				$data["arr_campo"][] = array("value" => "nr_valor_integralizar",   "text" => "Valor a Integralizar");
				$data["arr_campo"][] = array("value" => "nr_realizado",            "text" => "Realizado");
				#$data["arr_campo"][] = array("value" => "nr_fluxo",               "text" => "Fluxo de Caixa");
				$data["arr_campo"][] = array("value" => "nr_participacao_fundo",   "text" => "Part. do Fundo (%)");
				$data["arr_campo"][] = array("value" => "nr_taxa_adm",             "text" => "Taxa de Adm.");
				$data["arr_campo"][] = array("value" => "nr_ano_vencimento",       "text" => "Vencimento");
				$data["arr_campo"][] = array("value" => "nr_metro",                "text" => "M²");
				$data["arr_campo"][] = array("value" => "nr_participacao_metro",   "text" => "Part. M² (%)");
				$data["arr_campo"][] = array("value" => "nr_quantidade",           "text" => "Quantidade");
				$data["arr_campo"][] = array("value" => "nr_rentabilidade",        "text" => "Rentabilidade");
				$data["arr_campo"][] = array("value" => "nr_politica_min",         "text" => "Limite Política Mín");
				$data["arr_campo"][] = array("value" => "nr_alocacao_estrategica", "text" => "Alocação Estratégica");
				$data["arr_campo"][] = array("value" => "nr_politica_max",         "text" => "Limite Política Máx");
				$data["arr_campo"][] = array("value" => "nr_legal_max",            "text" => "Limites Legal");
				

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
			elseif(trim($data["grafico"]["tp_grafico"]) == "A")
			{
				if(intval($cd_caderno_cci_grafico_rotulo) == 0)
				{
					$data["grafico_rotulo"] = array(
						"cd_caderno_cci_grafico_rotulo" => $cd_caderno_cci_grafico_rotulo,
						"ds_caderno_cci_grafico_rotulo" => "",
						"nr_ordem"                        => "",
						"cor"                             => ""
					);
				}
				else
				{
					$args["cd_caderno_cci_grafico_rotulo"] = intval($cd_caderno_cci_grafico_rotulo);

					$this->caderno_cci_model->grafico_rotulo($result, $args);
					$data["grafico_rotulo"] = $result->row_array();
				}

				$this->caderno_cci_model->grafico_rotulo_listar($result, $args);
				$data["collection"] = $result->result_array();	

				$this->load->view("gestao/caderno_cci/grafico_configurar_rotulo", $data);
			}
			else
			{
				$parametro = json_decode($data["grafico"]["parametro"], true);
				
				$data["cor"]   = json_decode($data["grafico"]["cor"], true);
				$data["ordem"] = json_decode($data["grafico"]["ordem"], true);
				
				$data["projetado"]     = array();
				$data["indice"]        = array();
				$data["rentabilidade"] = array();
				$data["benchmark"]     = array();

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

				$i = 0;

				foreach($parametro["projetado"] as $item)
				{
					$args["cd_caderno_cci_projetado"] = $item;

					$this->caderno_cci_model->projetado($result, $args);
					$data["projetado"][$i] = $result->row_array();

					$i++;
				}

				$i = 0;

				foreach($parametro["indice"] as $item)
				{
					$args["cd_caderno_cci_indice"] = $item;

					$this->caderno_cci_model->indice($result, $args);
					$data["indice"][$i] = $result->row_array();

					$i++;
				}

				$i = 0;

				foreach($parametro["benchmark"] as $item)
				{
					$args["cd_caderno_cci_benchmark"] = $item;

					$this->caderno_cci_model->benchmark($result, $args);
					$data["benchmark"][$i] = $result->row_array();

					$i++;
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

			$projetado_cor     = (is_array($this->input->post("projetado_cor", TRUE)) ? $this->input->post("projetado_cor", TRUE) : array());
			$rentabilidade_cor = (is_array($this->input->post("rentabilidade_cor", TRUE)) ? $this->input->post("rentabilidade_cor", TRUE) : array());
			$indice_cor        = (is_array($this->input->post("indice_cor", TRUE)) ? $this->input->post("indice_cor", TRUE) : array());
			$benchmark_cor     = (is_array($this->input->post("benchmark_cor", TRUE)) ? $this->input->post("benchmark_cor", TRUE) : array());

			$cor = array(
				"projetado"     => $projetado_cor,
				"rentabilidade" => $rentabilidade_cor,
				"indice"        => $indice_cor,
				"benchmark"     => $benchmark_cor
			);

			/*
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
			*/

			$args["cd_usuario"] = $this->session->userdata("codigo");
			$args["ordem"]      = json_encode($ordem);
			$args["cor"]        = json_encode($cor);

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

	function grafico_configura_rotulo_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]                = $this->input->post("cd_caderno_cci", TRUE);
			$args["cd_caderno_cci_grafico"]        = $this->input->post("cd_caderno_cci_grafico", TRUE);
			$args["cd_caderno_cci_grafico_rotulo"] = $this->input->post("cd_caderno_cci_grafico_rotulo", TRUE);
			$args["ds_caderno_cci_grafico_rotulo"] = $this->input->post("ds_caderno_cci_grafico_rotulo", TRUE);
			$args["cor"]                           = $this->input->post("cor", TRUE);
			$args["nr_ordem"]                      = $this->input->post("nr_ordem", TRUE);
			$args["cd_usuario"]                    = $this->session->userdata("codigo");
 
			$this->caderno_cci_model->grafico_configura_rotulo_salvar($result, $args);

			redirect("gestao/caderno_cci/grafico_configurar/".$args["cd_caderno_cci"]."/".$args["cd_caderno_cci_grafico"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configura_rotulo_excluir($cd_caderno_cci, $cd_caderno_cci_grafico, $cd_caderno_cci_grafico_rotulo)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]                = $cd_caderno_cci;
			$args["cd_caderno_cci_grafico"]        = $cd_caderno_cci_grafico;
			$args["cd_caderno_cci_grafico_rotulo"] = $cd_caderno_cci_grafico_rotulo;
			$args["cd_usuario"]                    = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->grafico_configura_rotulo_excluir($result, $args);
			
			redirect("gestao/caderno_cci/grafico_configurar/".$args["cd_caderno_cci"]."/".$args["cd_caderno_cci_grafico"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configura_agrupamento($cd_caderno_cci, $cd_caderno_cci_grafico, $cd_caderno_cci_grafico_agrupamento = 0)
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

			$this->caderno_cci_model->grafico_rotulo_listar($result, $args);
			$data["rotulo"] = $result->result_array();	

			$parametro = json_decode($data["grafico"]["parametro"], true);

			$arr_rotulo = array();

			$i = 0;

			$j = 1;

			foreach($parametro["projetado"] as $item)
			{
				$args["cd_caderno_cci_projetado"] = $item;

				$this->caderno_cci_model->projetado($result, $args);
				$projetado = $result->row_array();

				$arr_rotulo[$i]["value"] = "projetado_".$projetado["cd_caderno_cci_projetado"];
				$arr_rotulo[$i]["text"]  = $projetado["ds_caderno_cci_projetado"];

				$i++;
			}
			
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

				$arr_rotulo[$i]["value"] = "rentabilidade_".$rentabilidade["cd_caderno_cci_estrutura"];
				$arr_rotulo[$i]["text"]  = $ordem." - ".$rentabilidade["ds_caderno_cci_estrutura"];

				if($rentabilidade["fl_grupo"] == "S")
				{
					$data["arr_rentabilidade_drop"][$j] = array("value" => $data["rentabilidade"][$i]["cd_caderno_cci_estrutura"], "text" => $data["rentabilidade"][$i]["ds_caderno_cci_estrutura"]);
				
					$j++;
				}

				$i++;
			}

			foreach($parametro["indice"] as $item)
			{
				$args["cd_caderno_cci_indice"] = $item;

				$this->caderno_cci_model->indice($result, $args);
				$indice = $result->row_array();

				$arr_rotulo[$i]["value"] = "indice_".$indice["cd_caderno_cci_indice"];
				$arr_rotulo[$i]["text"]  = $indice["ds_caderno_cci_indice"];

				$i++;
			}

			foreach($parametro["benchmark"] as $item)
			{
				$args["cd_caderno_cci_benchmark"] = $item;

				$this->caderno_cci_model->benchmark($result, $args);
				$benchmark = $result->row_array();

				$arr_rotulo[$i]["value"] = "benchmark_".$benchmark["cd_caderno_cci_benchmark"];
				$arr_rotulo[$i]["text"]  = $benchmark["ds_caderno_cci_benchmark"];

				$i++;
			}

			$data["arr_rotulo"] = $arr_rotulo;

			if(intval($cd_caderno_cci_grafico_agrupamento) == 0)
			{
				$data["grafico_agrupamento"] = array(
					"cd_caderno_cci_grafico_agrupamento" => $cd_caderno_cci_grafico_agrupamento,
					"ds_caderno_cci_grafico_agrupamento" => "",
					"nr_ordem"                           => ""
				);
			}
			else
			{
				$args["cd_caderno_cci_grafico_agrupamento"] = $cd_caderno_cci_grafico_agrupamento;

				$this->caderno_cci_model->grafico_agrupamento($result, $args);
				$data["grafico_agrupamento"] = $result->row_array();

				$arr = json_decode($data["grafico_agrupamento"]["agrupamento"], true);

				foreach ($arr as $key => $value) 
				{
					$data["grafico_agrupamento"][$key] = key($value)."_".$value[key($value)];
				}
			}

			$this->caderno_cci_model->grafico_agrupamento_listar($result, $args);
			$data["collection"] = $result->result_array();	

			foreach ($data["collection"] as $key => $value) 
			{
				$arr = json_decode($value["agrupamento"], true);

				foreach ($arr as $key2 => $value2) 
				{
					$data["collection"][$key][$key2] = ""; 

					switch (key($value2))
					{
						case 'rentabilidade':
							$args["cd_caderno_cci_estrutura"] = $value2["rentabilidade"];

							$this->caderno_cci_model->estrutura($result, $args);
							$rentabilidade = $result->row_array();

							$ordem = "";

							$args["cd_caderno_cci_estrutura"] = $rentabilidade["cd_caderno_cci_estrutura"];

							while(intval($args["cd_caderno_cci_estrutura"]) > 0)
							{
								$this->caderno_cci_model->estrutura_ordem($result, $args);
								$row = $result->row_array();

								$args["cd_caderno_cci_estrutura"] = $row["cd_caderno_cci_estrutura_pai"];

								$ordem = $row["nr_ordem"].($ordem != "" ? ".".$ordem : ""); 
							}

							$data["collection"][$key][$key2] = $ordem." - ".$rentabilidade["ds_caderno_cci_estrutura"];

							break;
						
						case 'indice':
							$args["cd_caderno_cci_indice"] = $value2["indice"];

							$this->caderno_cci_model->indice($result, $args);
							$indice = $result->row_array();

							$data["collection"][$key][$key2] = $indice["ds_caderno_cci_indice"];

							break;

						case 'benchmark':
							$args["cd_caderno_cci_benchmark"] = $value2["benchmark"];

							$this->caderno_cci_model->benchmark($result, $args);
							$benchmark = $result->row_array();

							$data["collection"][$key][$key2] = $benchmark["ds_caderno_cci_benchmark"];

							break;

						case 'projetado':
							$args["cd_caderno_cci_projetado"] = $value2["projetado"];

							$this->caderno_cci_model->projetado($result, $args);
							$projetado = $result->row_array();

							$data["collection"][$key][$key2] = $projetado["ds_caderno_cci_projetado"];
							break;
					}
				}
			}

			$this->load->view("gestao/caderno_cci/grafico_configurar_agrupamento", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configura_agrupamento_salvar()
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_caderno_cci"]                     = $this->input->post("cd_caderno_cci", TRUE);
			$args["cd_caderno_cci_grafico"]             = $this->input->post("cd_caderno_cci_grafico", TRUE);
			$args["ds_caderno_cci_grafico_agrupamento"] = $this->input->post("ds_caderno_cci_grafico_agrupamento", TRUE);
			$args["cd_caderno_cci_grafico_agrupamento"] = $this->input->post("cd_caderno_cci_grafico_agrupamento", TRUE);
			$args["nr_ordem"]                           = $this->input->post("nr_ordem", TRUE);
			$args["cd_usuario"]                         = $this->session->userdata("codigo");
			$args["agrupamento"]                        = array(); 

			$agrupamento = $this->input->post("arr_agrupamento", TRUE);

			$arr = array();

			foreach ($agrupamento as $key => $value) 
			{
				$arr_value = explode("_", $value);

				$arr[$key] = array($arr_value[0] => $arr_value[1]);
			}

			$args["agrupamento"] = json_encode($arr);

			$this->caderno_cci_model->grafico_configura_agrupamento_salvar($result, $args);

			redirect("gestao/caderno_cci/grafico_configura_agrupamento/".$args["cd_caderno_cci"]."/".$args["cd_caderno_cci_grafico"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function grafico_configura_agrupamento_excluir($cd_caderno_cci, $cd_caderno_cci_grafico, $cd_caderno_cci_grafico_agrupamento)
	{
		if(gerencia_in(array("GIN")))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_caderno_cci"]                     = $cd_caderno_cci;
			$args["cd_caderno_cci_grafico"]             = $cd_caderno_cci_grafico;
			$args["cd_caderno_cci_grafico_agrupamento"] = $cd_caderno_cci_grafico_agrupamento;
			$args["cd_usuario"]                         = $this->session->userdata("codigo");
			
			$this->caderno_cci_model->grafico_configura_agrupamento_excluir($result, $args);
			
			redirect("gestao/caderno_cci/grafico_configura_agrupamento/".$args["cd_caderno_cci"]."/".$args["cd_caderno_cci_grafico"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}