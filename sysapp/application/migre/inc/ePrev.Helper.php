<?php
class helper_projetos_atividades_agrupador_mes_ano
{
    public $ano;
    public $mes;
    public $abertas;
    public $solicitadas;
    public $atendidas_no_prazo;
    public $atendidas_fora_prazo;

    function helper_projetos_atividades_agrupador_mes_ano()
    {
    }

    function __destruct()
    {
    }
}

class helper_correspondencia_gap__fetch_by_filter extends entity_projetos_atendimento_protocolo
{
    public $dt_criacao__inicial;
    public $dt_criacao__final;

    public $hr_criacao__inicial;
    public $hr_criacao__final;

    function helper_correspondencia_gap__fetch_by_filter()
    {
        parent::entity_projetos_atendimento_protocolo();
    }
}

class helper_recadastro_gap__fetch_by_filter extends entity_projetos_atendimento_recadastro
{
    public $dt_criacao__inicial;
    public $dt_criacao__final;
}

class helper_usuarios_agrupados_por_divisao
{
    public $divisao;
    public $usuarios;           // coleзгo de entity_projetos_usuarios_controledi
}

class helper__avaliacao_capa__fetch_by_filter__filter
{
	public $dt_periodo=0;
	public $gerencia='';
	public $avaliado=0;
	public $dt_publicacao='';
	public $status='';
	public $tipo_promocao='';
}

class helper__avaliacao_capa__fetch_by_filter__entity
{
	public $cd_avaliacao_capa;
	public $grau_escolaridade;
	public $nome_avaliado;
	public $nome_avaliador;
	public $periodo;
	public $resultado_final;
	public $expectativas;
	public $tipo_promocao;
	public $status;
}

class hashtable
{
	public $key;
	public $value;

	function __construct($_key="", $_value="")
	{
		$this->key = $_key;
		$this->value = $_value;
	}
}

class hashtable_collection
{
	public $items;

	function __construct()
	{
		$this->items = array();
	}
	public function add( $key, $value )
	{
		$item = new hashtable( $key, $value );
		$this->items[sizeof($this->items)] = $item;
	}
}
?>