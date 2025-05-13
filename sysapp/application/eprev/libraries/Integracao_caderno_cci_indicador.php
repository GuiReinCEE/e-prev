<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Integracao_caderno_cci_indicador
{
	private $ci;

	private $ds_caderno_cci_integracao_indicador = '';
	private $mes;
	private $ano;

	function __construct()
	{
		$this->ci =& get_instance();
	}

	public function set_descricao($ds_caderno_cci_integracao_indicador)
	{
		$this->ds_caderno_cci_integracao_indicador = $ds_caderno_cci_integracao_indicador;
	}

	public function set_mes($mes)
	{
		$this->mes = $mes;
	}

	public function set_ano($ano)
	{
		$this->ano = $ano;
	}

	private function get_campo_atualizar()
	{
		if($this->ds_caderno_cci_integracao_indicador)
		{
			$this->ci->load->model('gestao/caderno_cci_integracao_indicador_model');

			$data = $this->ci->caderno_cci_integracao_indicador_model->get_campo_atualizar($this->ds_caderno_cci_integracao_indicador);

			return $data;
		}
		else
		{
			return array();
		}
	}

	public function get_valores()
	{
		$data = array();

		$this->ci->load->model('gestao/caderno_cci_integracao_indicador_model');

		foreach ($this->get_campo_atualizar() as $key => $item) 
		{
			$data[$key]['ds_caderno_cci_integracao_indicador_campo'] = $item['ds_caderno_cci_integracao_indicador_campo'];

			switch ($item['fl_referencia_tabela']) {
				case 'P':
					$row = $this->ci->caderno_cci_integracao_indicador_model->get_valor_projetado($this->ano, $item['cd_referencia_integracao']);

					$data[$key]['nr_valor'] = (isset($row['nr_projetado']) ? number_format($row['nr_projetado'], 4, ',', '.') : 0);

					break;
				
				case 'I':
					$row = $this->ci->caderno_cci_integracao_indicador_model->get_valor_indice($this->ano, $this->mes, $item['cd_referencia_integracao']);

					$data[$key]['nr_valor'] = (isset($row['nr_indice']) ? number_format($row['nr_indice'], 4, ',', '.') : 0);

					break;

				case 'B':
					$row = $this->ci->caderno_cci_integracao_indicador_model->get_valor_benchmark($this->ano, $this->mes, $item['cd_referencia_integracao']);

					$data[$key]['nr_valor'] = (isset($row['nr_benchmark']) ? number_format($row['nr_benchmark'], 4, ',', '.') : 0);

					break;

				case 'E':
					$row = $this->ci->caderno_cci_integracao_indicador_model->get_valor_estrutura($this->ano, $this->mes, $item['cd_referencia_integracao']);

					$data[$key]['nr_valor'] = (isset($row['nr_rentabilidade']) ? number_format($row['nr_rentabilidade'], 4, ',', '.') : 0);

					break;
			}
		}

		return $data;
	}
}