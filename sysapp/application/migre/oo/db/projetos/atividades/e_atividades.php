<?php
class e_atividades extends Entity
{
    public $numero;
    public $tipo;
    public $dt_cad;
    public $descricao;
    public $area;
    public $dt_inicio_prev;
    public $sistema;
    public $problema;
    public $solucao;
    public $dt_inicio_real;
    public $status_atual;
    public $complexidade;
    public $prioridade;
    public $negocio_fim;
    public $prejuizo;
    public $legislacao;
    public $situacao;
    public $dependencia;
    public $dias_realizados;
    public $cliente_externo;
    public $concorrencia;
    public $tarefa;
    public $tipo_solicitacao;
    public $numero_dias;
    public $dt_fim_prev;
    public $periodicidade;
    public $dt_fim_real;
    public $dt_deacordo;
    public $observacoes;
    public $divisao;
    public $origem;
    public $recurso;
    public $cod_atendente;
    public $cod_solicitante;
    public $dt_limite;
    public $dt_limite_testes;
    public $ok;
    public $complemento;
    public $num_dias_adicionados;
    public $titulo;
    public $cod_testador;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $cd_sequencia;
    public $dt_retorno;
    public $pertinencia;
    public $cd_cenario;
    public $opt_grafica;
    public $opt_eletronica;
    public $opt_evento;
    public $opt_anuncio;
    public $opt_folder;
    public $opt_mala;
    public $opt_cartaz;
    public $opt_cartilha;
    public $opt_site;
    public $opt_outro;
    public $cores;
    public $formato;
    public $gramatura;
    public $quantia;
    public $custo;
    public $cc;
    public $pacs;
    public $patracs;
    public $nacs;
    public $cacs;
    public $lacs;
    public $dacs;
    public $forma;
    public $solicitante;
    public $cd_plano;
    public $dt_env_teste;
    public $dt_fim_real_nova;
    public $numero_at_origem;
    public $dt_implementacao_norma_legal;
    public $dt_prevista_implementacao_norma_legal;
    public $cd_recorrente;
}
class e_atividades_ext extends e_atividades
{
	/**
	 * @access public
	 * @var e_usuario_controledi
	 */
	public $atendente;
	
	/**
	 * @access public
	 * @var e_listas
	 */
	public $status;
}
?>