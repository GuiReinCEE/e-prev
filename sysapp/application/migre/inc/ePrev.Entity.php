<?php
include_once("ePrev.Entity.Extends.php");
class entity_projetos_atendimento_recadastro
{
    public $cd_atendimento_recadastro;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $nome;
    public $dt_criacao;
    public $cd_usuario_criacao;
    public $usuarioCriacao;
    public $dt_cancelamento;
    public $motivo_cancelamento;
    public $observacao;
    public $dt_periodo;
    public $servico_social;
    public $dt_atualizacao;
    public $cd_usuario_atualizacao;
    public $nome_usuario_atualizacao;
}
class entity_projetos_atendimento_protocolo
{
    private $cd_atendimento_protocolo;
    private $tipo;
    private $identificacao;
    private $dt_criacao;
    private $cd_usuario_recebimento;
    private $usuarioRecebimento;
    private $dt_recebimento;
    private $cd_usuario_criacao;
    private $usuarioCriacao;
    private $destino;
    private $cd_empresa;
    private $cd_registro_empregado;
    private $seq_dependencia;
    private $dt_cancelamento;
    private $motivo_cancelamento;
    private $nome;
    private $cd_atendimento_protocolo_tipo;
    private $cd_atendimento_protocolo_discriminacao;
    
    private $cd_atendimento;
    private $cd_encaminhamento;

    function entity_projetos_atendimento_protocolo()
    {
        // do nothing
    }

    function __destruct()
    {
        // do nothing
    }

    public function setcd_atendimento_protocolo( $value )
    {
        $this->cd_atendimento_protocolo = $value; 
    }

    public function getcd_atendimento_protocolo()
    {
        if ($this->cd_atendimento_protocolo=="") {
            $this->cd_atendimento_protocolo = "0";
        }
        return $this->cd_atendimento_protocolo; 
    }
    
    public function settipo( $value )
    {
        $this->tipo = $value; 
    }
    
    public function gettipo()
    {
        return $this->tipo; 
    }
    
    public function setidentificacao( $value )
    {
        $this->identificacao = $value; 
    }
    
    public function getidentificacao()
    {
        return $this->identificacao; 
    }
    
    public function setDe( $value )
    {
        $this->de = $value;
    }
    
    public function getDe()
    {
        return $this->de;
    }
    
    public function setdt_criacao( $value )
    {
        $this->dt_criacao = $value; 
    }
    
    public function getdt_criacao()
    {
        return $this->dt_criacao;
    }
    
    public function setcd_usuario_recebimento( $value )
    {
        $this->cd_usuario_recebimento = $value; 
    }
    
    public function getcd_usuario_recebimento()
    {
        return $this->cd_usuario_recebimento;
    }

    public function setdt_recebimento( $value )
    {
        $this->dt_recebimento = $value; 
    }
    
    public function getdt_recebimento()
    {
        return $this->dt_recebimento;
    }

    public function setcd_usuario_criacao( $value )
    {
        $this->cd_usuario_criacao = $value; 
    }
    
    public function getcd_usuario_criacao()
    {
        return $this->cd_usuario_criacao;
    }
    
    public function setdestino( $value )
    {
        $this->destino = $value; 
    }
    
    public function getdestino()
    {
        return $this->destino;
    }
    
    public function setcd_empresa( $value )
    {
        $this->cd_empresa = $value; 
    }
    
    public function getcd_empresa()
    {
        return $this->cd_empresa;
    }
    
    public function setcd_registro_empregado( $value )
    {
        $this->cd_registro_empregado = $value; 
    }
    
    public function getcd_registro_empregado()
    {
        return $this->cd_registro_empregado;
    }
    
    public function setseq_dependencia( $value )
    {
        $this->seq_dependencia = $value; 
    }
    
    public function getseq_dependencia()
    {
        return $this->seq_dependencia;
    }
    
    public function setUsuarioRecebimento( entity_projetos_usuarios_controledi $value )
    {
        $this->usuarioRecebimento = $value; 
    }
    
    public function getUsuarioRecebimento()
    {
        return $this->usuarioRecebimento;
    }
    
    public function setUsuarioCriacao( entity_projetos_usuarios_controledi $value )
    {
        $this->usuarioCriacao = $value; 
    }
    
    public function getUsuarioCriacao()
    {
        return $this->usuarioCriacao;
    }

    public function setdt_cancelamento( $value )
    {
        $this->dt_cancelamento = $value; 
    }
    
    public function getdt_cancelamento()
    {
        return $this->dt_cancelamento;
    }

    public function setmotivo_cancelamento( $value )
    {
        $this->motivo_cancelamento = $value; 
    }
    
    public function getmotivo_cancelamento()
    {
        return $this->motivo_cancelamento;
    }

    public function set_nome( $value )
    {
        $this->nome = $value; 
    }
    
    public function get_nome()
    {
        return $this->nome;
    }

    public function setcd_atendimento_protocolo_tipo( $value )
    {
        $this->cd_atendimento_protocolo_tipo = $value; 
    }

    public function getcd_atendimento_protocolo_tipo()
    {
        return $this->cd_atendimento_protocolo_tipo;
    }

    public function setcd_atendimento_protocolo_discriminacao( $value )
    {
        $this->cd_atendimento_protocolo_discriminacao = $value; 
    }

    public function getcd_atendimento_protocolo_discriminacao()
    {
        return $this->cd_atendimento_protocolo_discriminacao;
    }
    
    public function setcd_atendimento( $value )
    {
        $this->cd_atendimento = $value; 
    }

    public function getcd_atendimento()
    {
        return $this->cd_atendimento;
    }
    
    public function setcd_encaminhamento( $value )
    {
        $this->cd_encaminhamento = $value; 
    }

    public function getcd_encaminhamento()
    {
        return $this->cd_encaminhamento;
    }
}

class entity_projetos_avaliacao_capa
{
    private $cd_avaliacao_capa;
    private $cd_usuario_avaliado;
    private $cd_gerente_avaliado;
    private $dt_periodo;
    private $status;
    private $dt_criacao;
    private $cd_usuario_avaliador;
    private $grau_escolaridade;
    private $dt_publicacao;
    private $tipo_promocao;
    private $media_geral;
    private $cd_matriz_salarial;
    private $avaliador_responsavel_comite;
    private $cd_cargo;

    function entity_projetos_avaliacao_capa()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_avaliacao_capa()
    {
        return $this->cd_avaliacao_capa;
    }
    public function set_cd_avaliacao_capa($value)
    {
        $this->cd_avaliacao_capa = $value;
    }

    public function get_cd_usuario_avaliado()
    {
        return $this->cd_usuario_avaliado;
    }
    public function set_cd_usuario_avaliado($value)
    {
        $this->cd_usuario_avaliado = $value;
    }
	
    public function get_cd_gerente_avaliado()
    {
        return $this->cd_gerente_avaliado;
    }	
    public function set_cd_gerente_avaliado($value)
    {
        $this->cd_gerente_avaliado = $value;
    }	

    public function get_dt_periodo()
    {
        return $this->dt_periodo;
    }
    public function set_dt_periodo($value)
    {
        $this->dt_periodo = $value;
    }

    public function get_status()
    {
        return $this->status;
    }
    public function set_status($value)
    {
        $this->status = $value;
    }

    public function get_dt_criacao()
    {
        return $this->dt_criacao;
    }
    public function set_dt_criacao($value)
    {
        $this->dt_criacao = $value;
    }

    public function get_cd_usuario_avaliador()
    {
        return $this->cd_usuario_avaliador;
    }
    public function set_cd_usuario_avaliador($value)
    {
        $this->cd_usuario_avaliador = $value;
    }

    public function get_grau_escolaridade()
    {
        return $this->grau_escolaridade;
    }
    public function set_grau_escolaridade($value)
    {
        $this->grau_escolaridade = $value;
    }

    public function get_dt_publicacao()
    {
        return $this->dt_publicacao;
    }
    public function set_dt_publicacao($value)
    {
        $this->dt_publicacao = $value;
    }

    public function get_tipo_promocao()
    {
        return $this->tipo_promocao;
    }
    public function set_tipo_promocao($value)
    {
        $this->tipo_promocao = $value;
    }

    public function get_media_geral()
    {
        return $this->media_geral;
    }
    public function set_media_geral($value)
    {
        $this->media_geral = $value;
    }

    public function get_cd_matriz_salarial()
    {
        return $this->cd_matriz_salarial;
    }
    public function set_cd_matriz_salarial($value)
    {
        $this->cd_matriz_salarial = $value;
    }

    public function get_avaliador_responsavel_comite()
    {
        return $this->avaliador_responsavel_comite;
    }
    public function set_avaliador_responsavel_comite($value)
    {
        $this->avaliador_responsavel_comite = $value;
    }

    public function get_cd_cargo()
    {
        return $this->cd_cargo;
    }
	public function set_cd_cargo($value)
    {
        $this->cd_cargo = $value;
    }
}

class entity_projetos_avaliacao
{
    private $cd_avaliacao;
    private $cd_usuario_avaliador;
    private $tipo;
    private $dt_criacao;
    private $cd_avaliacao_capa;
    private $dt_conclusao;

    function entity_projetos_avaliacao()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_avaliacao()
    {
        return $this->cd_avaliacao;
    }
    public function set_cd_avaliacao($value)
    {
        $this->cd_avaliacao = $value;
    }

    public function get_cd_usuario_avaliador()
    {
        return $this->cd_usuario_avaliador;
    }
    public function set_cd_usuario_avaliador($value)
    {
        $this->cd_usuario_avaliador = $value;
    }

    public function get_tipo()
    {
        return $this->tipo;
    }
    public function set_tipo($value)
    {
        $this->tipo = $value;
    }

    public function get_dt_criacao()
    {
        return $this->dt_criacao;
    }
    public function set_dt_criacao($value)
    {
        $this->dt_criacao = $value;
    }

    public function get_cd_avaliacao_capa()
    {
        return $this->cd_avaliacao_capa;
    }
    public function set_cd_avaliacao_capa($value)
    {
        $this->cd_avaliacao_capa = $value;
    }
    
    public function get_dt_conclusao()
    {
        return $this->dt_conclusao;
    }
    public function set_dt_conclusao($value)
    {
        $this->dt_conclusao = $value;
    }

}

class entity_projetos_avaliacao_aspecto
{
    public $cd_avaliacao_aspecto;
    public $cd_avaliacao;
    public $aspecto;
    public $resultado_esperado;
    public $acao;
}

class entity_projetos_avaliacao_comite
{
    private $cd_avaliacao_comite;
    private $dt_periodo;
    private $cd_usuario_avaliado;
    private $cd_usuario_avaliador;
    private $cd_gerente_avaliado;
    private $fl_responsavel;
    private $dt_exclusao;
    private $cd_avaliacao_capa;


    function entity_projetos_avaliacao_comite()
    {
    }

    function __destruct()
    {
    }


    public function get_cd_avaliacao_comite()
    {
        return $this->cd_avaliacao_comite;
    }
    public function set_cd_avaliacao_comite($value)
    {
        $this->cd_avaliacao_comite = $value;
    }

    public function get_dt_periodo()
    {
        return $this->dt_periodo;
    }
    public function set_dt_periodo($value)
    {
        $this->dt_periodo = $value;
    }

    public function get_cd_usuario_avaliado()
    {
        return $this->cd_usuario_avaliado;
    }
    public function set_cd_usuario_avaliado($value)
    {
        $this->cd_usuario_avaliado = $value;
    }
	
    public function get_cd_gerente_avaliado()
    {
        return $this->cd_gerente_avaliado;
    }	
    public function set_cd_gerente_avaliado($value)
    {
        $this->cd_gerente_avaliado = $value;
    }	

    public function get_cd_usuario_avaliador()
    {
        return $this->cd_usuario_avaliador;
    }
    public function set_cd_usuario_avaliador($value)
    {
        $this->cd_usuario_avaliador = $value;
    }

    public function get_fl_responsavel()
    {
        return $this->fl_responsavel;
    }
    public function set_fl_responsavel($value)
    {
        $this->fl_responsavel = $value;
    }

    public function get_dt_exclusao()
    {
        return $this->dt_exclusao;
    }
    public function set_dt_exclusao($value)
    {
        $this->dt_exclusao = $value;
    }

    public function get_cd_avaliacao_capa()
    {
        return $this->cd_avaliacao_capa;
    }
    public function set_cd_avaliacao_capa($value)
    {
        $this->cd_avaliacao_capa = $value;
    }
}

class entity_projetos_avaliacoes_comp_inst
{

    private $cd_avaliacao;
    private $cd_comp_inst;
    private $grau;
    

    function entity_projetos_avaliacoes_comp_inst()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_avaliacao()
    {
        return $this->cd_avaliacao;
    }
    public function set_cd_avaliacao($value)
    {
        $this->cd_avaliacao = $value;
    }

    public function get_cd_comp_inst()
    {
        return $this->cd_comp_inst;
    }
    public function set_cd_comp_inst($value)
    {
        $this->cd_comp_inst = $value;
    }

    public function get_grau()
    {
        return $this->grau;
    }
    public function set_grau($value)
    {
        $this->grau = $value;
    }

}

class entity_projetos_avaliacoes_comp_espec
{

    private $cd_avaliacao;
    private $cd_comp_espec;
    private $grau;
    

    function entity_projetos_avaliacoes_comp_espec()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_avaliacao()
    {
        return $this->cd_avaliacao;
    }
    public function set_cd_avaliacao($value)
    {
        $this->cd_avaliacao = $value;
    }

    public function get_cd_comp_espec()
    {
        return $this->cd_comp_espec;
    }
    public function set_cd_comp_espec($value)
    {
        $this->cd_comp_espec = $value;
    }

    public function get_grau()
    {
        return $this->grau;
    }
    public function set_grau($value)
    {
        $this->grau = $value;
    }

}

class entity_projetos_avaliacoes_responsabilidades
{

    private $cd_avaliacao;
    private $cd_responsabilidade;
    private $grau;
    

    function entity_projetos_avaliacoes_responsabilidades()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_avaliacao()
    {
        return $this->cd_avaliacao;
    }
    public function set_cd_avaliacao($value)
    {
        $this->cd_avaliacao = $value;
    }

    public function get_cd_responsabilidade()
    {
        return $this->cd_responsabilidade;
    }
    public function set_cd_responsabilidade($value)
    {
        $this->cd_responsabilidade = $value;
    }

    public function get_grau()
    {
        return $this->grau;
    }
    public function set_grau($value)
    {
        $this->grau = $value;
    }

}

class entity_projetos_cargos
{

    private $cd_cargo;
    private $nome_cargo;
    private $desc_cargo;
    private $cd_familia;
    private $familia;

    function entity_projetos_cargos()
    {
    }

    function __destruct()
    {
    }

    public function get_cd_cargo()
    {
        return $this->cd_cargo;
    }
    public function set_cd_cargo($value)
    {
        $this->cd_cargo = $value;
    }

    public function get_nome_cargo()
    {
        return $this->nome_cargo;
    }
    public function set_nome_cargo($value)
    {
        $this->nome_cargo = $value;
    }

    public function get_desc_cargo()
    {
        return $this->desc_cargo;
    }
    public function set_desc_cargo($value)
    {
        $this->desc_cargo = $value;
    }

    public function get_cd_familia()
    {
        return $this->cd_familia;
    }
    public function set_cd_familia($value)
    {
        $this->cd_familia = $value;
    }

    public function get_familia()
    {
        if (is_null($this->familia)) {
			$this->familia = new entity_projetos_familias_cargos();
		}
        return $this->familia;
    }
    public function set_familia( entity_projetos_familias_cargos $value )
    {
        $this->familia = $value;
    }

}

class entity_projetos_documento_protocolo_item
{
    private $cd_documento_protocolo_item;
    private $cd_documento_protocolo;
    private $cd_tipo_doc;
    private $cd_empresa;
    private $cd_registro_empregado;
    private $seq_dependencia;
    private $dt_cadastro;
    private $cd_usuario_cadastro;
    private $dt_exclusao;
    private $cd_usuario_exclusao;
    private $fl_recebido;
    private $observacao;
    private $ds_processo;
    private $nr_folha;
    private $arquivo;
    private $arquivo_nome;

    function entity_projetos_documento_protocolo_item(){
        // do nothing
    }

    function __destruct(){
        // do nothing
    }

    public function set_cd_documento_protocolo_item( $value )
    {
        $this->cd_documento_protocolo_item = $value; 
    }

    public function get_cd_documento_protocolo_item()
    {
        return $this->cd_documento_protocolo_item;
    }

    public function set_cd_documento_protocolo( $value )
    {
        $this->cd_documento_protocolo = $value;
    }

    public function get_cd_documento_protocolo()
    {
        return $this->cd_documento_protocolo;
    }
    
    public function set_cd_tipo_doc( $value )
    {
        $this->cd_tipo_doc = $value;
    }

    public function get_cd_tipo_doc()
    {
        return $this->cd_tipo_doc;
    }


    public function set_cd_empresa( $value )
    {
        $this->cd_empresa = $value;
    }

    public function get_cd_empresa()
    {
        return $this->cd_empresa;
    }

    public function set_cd_registro_empregado( $value )
    {
        $this->cd_registro_empregado = $value;
    }

    public function get_cd_registro_empregado()
    {
        return $this->cd_registro_empregado;
    }

    public function set_seq_dependencia( $value )
    {
        $this->seq_dependencia = $value;
    }

    public function get_seq_dependencia()
    {
        return $this->seq_dependencia;
    }

    public function set_dt_cadastro( $value )
    {
        $this->dt_cadastro = $value;
    }

    public function get_dt_cadastro()
    {
        return $this->dt_cadastro;
    }

    public function set_cd_usuario_cadastro( $value )
    {
        $this->cd_usuario_cadastro = $value;
    }

    public function get_cd_usuario_cadastro()
    {
        return $this->cd_usuario_cadastro;
    }

    public function set_dt_exclusao( $value )
    {
        $this->dt_exclusao = $value;
    }

    public function get_dt_exclusao()
    {
        return $this->dt_exclusao;
    }

    public function set_cd_usuario_exclusao( $value )
    {
        $this->cd_usuario_exclusao = $value;
    }

    public function get_cd_usuario_exclusao()
    {
        return $this->cd_usuario_exclusao;
    }

    public function set_fl_recebido( $value )
    {
        $this->fl_recebido = $value;
    }

    public function get_fl_recebido()
    {
        return $this->fl_recebido;
    }

    public function set_observacao( $value )
    {
        $this->observacao = $value;
    }

    public function get_observacao()
    {
        return $this->observacao;
    }

    public function set_ds_processo( $value )
    {
        $this->ds_processo = $value;
    }

    public function get_ds_processo()
    {
        return $this->ds_processo;
    }

    public function set_nr_folha( $value )
    {
        $this->nr_folha = $value;
    }

    public function get_nr_folha()
    {
        return $this->nr_folha;
    }

    public function set_arquivo( $value )
    {
        $this->arquivo = $value;
    }

    public function get_arquivo()
    {
        return $this->arquivo;
    }

    public function set_arquivo_nome( $value )
    {
        $this->arquivo_nome = $value;
    }

    public function get_arquivo_nome()
    {
        return $this->arquivo_nome;
    }}

class entity_projetos_documento_protocolo
{
    private $ano; // integer, -- Ano de protocolo
    private $contador; //integer, -- Contador combinado com ano...
    private $cd_documento_protocolo; // serial NOT NULL, -- código auto-incremento da tabela
    private $dt_cadastro; // timestamp without time zone DEFAULT now(), -- data de criação do registro
    private $cd_usuario_cadastro; // integer, -- FK Usuário que realizou o cadastro
    private $dt_envio; // timestamp without time zone, -- Data que o protocolo foi enviado para GAD
    private $cd_usuario_envio; // integer, -- FK Código do usuário que enviou o protocolo para GAP
    private $dt_ok; // timestamp without time zone, -- Data de recebimento do protocolo pelo usuário da GAD
    private $cd_usuario_ok; // integer, -- FK Código do usuário da GAD que recebeu o protocolo
    private $dt_exclusao; // timestamp without time zone, -- Data e hora que o protocolo foi cancelado/excluído
    private $cd_usuario_exclusao; // integer, -- FK Código do usuário que excluiu/cancelou o protocolo
    private $motivo_exclusao; // character varying(255), -- Motivo da exclusão/cancelamento
    private $ordem_itens; // character varying(255), -- Motivo da exclusão/cancelamento
    private $dt_indexacao;
    
    private $usuario_cadastro;
    private $usuario_envio;
    private $usuario_ok;

    function entity_projetos_documento_protocolo()
    {
        // do nothing
    }

    function __destruct()
    {
        // do nothing
    }

    public function set_ano( $value )
    {
        $this->ano = $value; 
    }

    public function get_ano()
    {
        return $this->ano;
    }

    public function set_contador( $value )
    {
        $this->contador = $value;
    }

    public function get_contador()
    {
        return $this->contador;
    }

    public function set_cd_documento_protocolo( $value )
    {
        $this->cd_documento_protocolo = $value;
    }

    public function get_cd_documento_protocolo()
    {
        return $this->cd_documento_protocolo;
    }

    public function set_dt_cadastro( $value )
    {
        $this->dt_cadastro = $value;
    }

    public function get_dt_cadastro()
    {
        return $this->dt_cadastro;
    }

    public function get_usuario_cadastro()
    {
        return $this->usuario_cadastro;
    }
    
    public function set_usuario_cadastro( entity_projetos_usuarios_controledi $value )
    {
        $this->usuario_cadastro = $value;
    }

    public function get_usuario_envio()
    {
        return $this->usuario_envio;
    }
    
    public function set_usuario_envio( entity_projetos_usuarios_controledi $value )
    {
        $this->usuario_envio = $value;
    }

    public function get_usuario_ok()
    {
        return $this->usuario_ok;
    }
    
    public function set_usuario_ok( entity_projetos_usuarios_controledi $value )
    {
        $this->usuario_ok = $value;
    }

    public function set_cd_usuario_cadastro( $value )
    {
        $this->cd_usuario_cadastro = $value;
    }

    public function get_cd_usuario_cadastro()
    {
        return $this->cd_usuario_cadastro;
    }

    public function set_dt_envio( $value )
    {
        $this->dt_envio = $value;
    }

    public function get_dt_envio()
    {
        return $this->dt_envio;
    }

    public function set_cd_usuario_envio( $value )
    {
        $this->cd_usuario_envio = $value;
    }

    public function get_cd_usuario_envio()
    {
        return $this->cd_usuario_envio;
    }

    public function set_dt_ok( $value )
    {
        $this->dt_ok = $value;
    }

    public function get_dt_ok()
    {
        return $this->dt_ok;
    }

    public function set_dt_indexacao( $value )
    {
        $this->dt_indexacao = $value;
    }

    public function get_dt_indexacao()
    {
        return $this->dt_indexacao;
    }

    public function set_cd_usuario_ok( $value )
    {
        $this->cd_usuario_ok = $value;
    }

    public function get_cd_usuario_ok()
    {
        return $this->cd_usuario_ok;
    }

    public function set_dt_exclusao( $value )
    {
        $this->dt_exclusao = $value;
    }

    public function get_dt_exclusao()
    {
        return $this->dt_exclusao;
    }

    public function set_cd_usuario_exclusao( $value )
    {
        $this->cd_usuario_exclusao = $value;
    }

    public function get_cd_usuario_exclusao()
    {
        return $this->cd_usuario_exclusao;
    }

    public function set_motivo_exclusao( $value )
    {
        $this->motivo_exclusao = $value;
    }

    public function get_motivo_exclusao()
    {
        return $this->motivo_exclusao;
    }

    public function set_ordem_itens( $value )
    {
        $this->ordem_itens = $value;
    }

    public function get_ordem_itens()
    {
        return $this->ordem_itens;
    }
}

class entity_projetos_envia_emails
{
    public $cd_email;
    public $dt_envio;
    public $de;
    public $para;
    public $cc;
    public $cco;
    public $assunto;
    public $texto;
    public $dt_email_enviado;
    public $dt_schedule_email;
    public $arquivo_anexo;
    public $div_solicitante;
    public $cd_divulgacao;
    public $cd_plano;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $tipo_mensagem;
    public $cd_evento;

    function entity_projetos_envia_emails()
    {
    }

    function __destruct()
    {
    }

    public function get_cd_email()
    {
        return $this->cd_email;
    }
    public function set_cd_email($value)
    {
        $this->cd_email = $value;
    }

    public function get_dt_envio()
    {
        return $this->dt_envio;
    }
    public function set_dt_envio($value)
    {
        $this->dt_envio = $value;
    }

    public function get_de()
    {
        return $this->de;
    }
    public function set_de($value)
    {
        $this->de = $value;
    }

    public function get_para()
    {
        return $this->para;
    }
    public function set_para($value)
    {
        $this->para = $value;
    }

    public function get_cc()
    {
        return $this->cc;
    }
    public function set_cc($value)
    {
        $this->cc = $value;
    }

    public function get_cco()
    {
        return $this->cco;
    }
    public function set_cco($value)
    {
        $this->cco = $value;
    }

    public function get_assunto()
    {
        return $this->assunto;
    }
    public function set_assunto($value)
    {
        $this->assunto = $value;
    }

    public function get_texto()
    {
        return $this->texto;
    }
    public function set_texto($value)
    {
        $this->texto = $value;
    }

    public function get_dt_email_enviado()
    {
        return $this->dt_email_enviado;
    }
    public function set_dt_email_enviado($value)
    {
        $this->dt_email_enviado = $value;
    }

    public function get_dt_schedule_email()
    {
        return $this->dt_schedule_email;
    }
    public function set_dt_schedule_email($value)
    {
        $this->dt_schedule_email = $value;
    }

    public function get_arquivo_anexo()
    {
        return $this->arquivo_anexo;
    }
    public function set_arquivo_anexo($value)
    {
        $this->arquivo_anexo = $value;
    }

    public function get_div_solicitante()
    {
        return $this->div_solicitante;
    }
    public function set_div_solicitante($value)
    {
        $this->div_solicitante = $value;
    }

    public function get_cd_divulgacao()
    {
        return $this->cd_divulgacao;
    }
    public function set_cd_divulgacao($value)
    {
        $this->cd_divulgacao = $value;
    }

    public function get_cd_plano()
    {
        return $this->cd_plano;
    }
    public function set_cd_plano($value)
    {
        $this->cd_plano = $value;
    }

    public function get_cd_empresa()
    {
        return $this->cd_empresa;
    }
    public function set_cd_empresa($value)
    {
        $this->cd_empresa = $value;
    }

    public function get_cd_registro_empregado()
    {
        return $this->cd_registro_empregado;
    }
    public function set_cd_registro_empregado($value)
    {
        $this->cd_registro_empregado = $value;
    }

    public function get_seq_dependencia()
    {
        return $this->seq_dependencia;
    }
    public function set_seq_dependencia($value)
    {
        $this->seq_dependencia = $value;
    }

    public function get_tipo_mensagem()
    {
        return $this->tipo_mensagem;
    }
    public function set_tipo_mensagem($value)
    {
        $this->tipo_mensagem = $value;
    }

    public function get_cd_evento()
    {
        return $this->cd_evento;
    }
    public function set_cd_evento($value)
    {
        $this->cd_evento = $value;
    }

}

class entity_projetos_familias_cargos
{

    private $cd_familia;
    private $nome_familia;
    private $dt_inclusao;
    private $dt_alteracao;
    private $usu_alteracao;
    private $classe;
    

    function entity_projetos_familias_cargos()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_familia()
    {
        return $this->cd_familia;
    }
    public function set_cd_familia($value)
    {
        $this->cd_familia = $value;
    }

    public function get_nome_familia()
    {
        return $this->nome_familia;
    }
    public function set_nome_familia($value)
    {
        $this->nome_familia = $value;
    }

    public function get_dt_inclusao()
    {
        return $this->dt_inclusao;
    }
    public function set_dt_inclusao($value)
    {
        $this->dt_inclusao = $value;
    }

    public function get_dt_alteracao()
    {
        return $this->dt_alteracao;
    }
    public function set_dt_alteracao($value)
    {
        $this->dt_alteracao = $value;
    }

    public function get_usu_alteracao()
    {
        return $this->usu_alteracao;
    }
    public function set_usu_alteracao($value)
    {
        $this->usu_alteracao = $value;
    }
    
    public function get_classe()
    {
        return $this->classe;
    }
    public function set_classe($value)
    {
        $this->classe = $value;
    }

}

class entity_projetos_usuarios_controledi
{
    private $codigo;
    private $usuario;
    private $senha;
    private $nome;
    private $tipo;
    private $divisao;
    private $observacao;
    private $formato_mensagem;
    private $e_mail_alternativo;
    private $indic_01;
    private $indic_02;
    private $indic_03;
    private $indic_04;
    private $indic_05;
    private $indic_06;
    private $indic_07;
    private $indic_08;
    private $indic_09;
    private $indic_10;
    private $indic_11;
    private $indic_12;
    private $guerra;
    private $voto;
    private $cd_registro_empregado;
    private $dt_troca_senha;
    private $aeletro;
    private $cd_patrocinadora;
    private $skin;
    private $diretoria;
    private $locacao;
    private $cidade;
    private $opt_tarefas;
    private $opt_workspace;
    private $cd_cargo;
    private $cargo;
    private $usu_email;
    private $opt_dicas;
    private $opt_interatividade;
    private $tela_inicial;
    private $estacao_trabalho;
    private $favorito2;
    private $favorito3;
    private $favorito4;
    private $favorito5;
    private $dt_hora_confirmacao;
    private $dt_hora_scanner_computador;
    private $indic_msg;
    private $texto_msg;
    private $dash1;
    private $dash2;
    private $dash3;
    private $dash4;
    private $dash5;
    private $dt_ult_login;
    private $resolucao_video;
    private $dash6;
    private $dash7;
    private $np_computador;
    private $ultima_resposta_vida;
    private $chamada_web;
    
    function entity_projetos_usuarios_controledi()
    {
    }

    function __destruct()
    {
    }

    
    public function get_codigo()
    {
        return $this->codigo;
    }
    public function set_codigo($value)
    {
        $this->codigo = $value;
    }

    public function get_usuario()
    {
        return $this->usuario;
    }
    public function set_usuario($value)
    {
        $this->usuario = $value;
    }

    public function get_senha()
    {
        return $this->senha;
    }
    public function set_senha($value)
    {
        $this->senha = $value;
    }

    public function get_nome()
    {
        return $this->nome;
    }
    public function set_nome($value)
    {
        $this->nome = $value;
    }

    public function get_tipo()
    {
        return $this->tipo;
    }
    public function set_tipo($value)
    {
        $this->tipo = $value;
    }

    public function get_divisao()
    {
        return $this->divisao;
    }
    public function set_divisao($value)
    {
        $this->divisao = $value;
    }

    public function get_observacao()
    {
        return $this->observacao;
    }
    public function set_observacao($value)
    {
        $this->observacao = $value;
    }

    public function get_formato_mensagem()
    {
        return $this->formato_mensagem;
    }
    public function set_formato_mensagem($value)
    {
        $this->formato_mensagem = $value;
    }

    public function get_e_mail_alternativo()
    {
        return $this->e_mail_alternativo;
    }
    public function set_e_mail_alternativo($value)
    {
        $this->e_mail_alternativo = $value;
    }

    public function get_indic_01()
    {
        return $this->indic_01;
    }
    public function set_indic_01($value)
    {
        $this->indic_01 = $value;
    }

    public function get_indic_02()
    {
        return $this->indic_02;
    }
    public function set_indic_02($value)
    {
        $this->indic_02 = $value;
    }

    public function get_indic_03()
    {
        return $this->indic_03;
    }
    public function set_indic_03($value)
    {
        $this->indic_03 = $value;
    }

    public function get_indic_04()
    {
        return $this->indic_04;
    }
    public function set_indic_04($value)
    {
        $this->indic_04 = $value;
    }

    public function get_indic_05()
    {
        return $this->indic_05;
    }
    public function set_indic_05($value)
    {
        $this->indic_05 = $value;
    }

    public function get_indic_06()
    {
        return $this->indic_06;
    }
    public function set_indic_06($value)
    {
        $this->indic_06 = $value;
    }

    public function get_indic_07()
    {
        return $this->indic_07;
    }
    public function set_indic_07($value)
    {
        $this->indic_07 = $value;
    }

    public function get_indic_08()
    {
        return $this->indic_08;
    }
    public function set_indic_08($value)
    {
        $this->indic_08 = $value;
    }

    public function get_indic_09()
    {
        return $this->indic_09;
    }
    public function set_indic_09($value)
    {
        $this->indic_09 = $value;
    }

    public function get_indic_10()
    {
        return $this->indic_10;
    }
    public function set_indic_10($value)
    {
        $this->indic_10 = $value;
    }

    public function get_indic_11()
    {
        return $this->indic_11;
    }
    public function set_indic_11($value)
    {
        $this->indic_11 = $value;
    }

    public function get_indic_12()
    {
        return $this->indic_12;
    }
    public function set_indic_12($value)
    {
        $this->indic_12 = $value;
    }

    public function get_guerra()
    {
        return $this->guerra;
    }
    public function set_guerra($value)
    {
        $this->guerra = $value;
    }

    public function get_voto()
    {
        return $this->voto;
    }
    public function set_voto($value)
    {
        $this->voto = $value;
    }

    public function get_cd_registro_empregado()
    {
        return $this->cd_registro_empregado;
    }
    public function set_cd_registro_empregado($value)
    {
        $this->cd_registro_empregado = $value;
    }

    public function get_dt_troca_senha()
    {
        return $this->dt_troca_senha;
    }
    public function set_dt_troca_senha($value)
    {
        $this->dt_troca_senha = $value;
    }

    public function get_aeletro()
    {
        return $this->aeletro;
    }
    public function set_aeletro($value)
    {
        $this->aeletro = $value;
    }

    public function get_cd_patrocinadora()
    {
        return $this->cd_patrocinadora;
    }
    public function set_cd_patrocinadora($value)
    {
        $this->cd_patrocinadora = $value;
    }

    public function get_skin()
    {
        return $this->skin;
    }
    public function set_skin($value)
    {
        $this->skin = $value;
    }

    public function get_diretoria()
    {
        return $this->diretoria;
    }
    public function set_diretoria($value)
    {
        $this->diretoria = $value;
    }

    public function get_locacao()
    {
        return $this->locacao;
    }
    public function set_locacao($value)
    {
        $this->locacao = $value;
    }

    public function get_cidade()
    {
        return $this->cidade;
    }
    public function set_cidade($value)
    {
        $this->cidade = $value;
    }

    public function get_opt_tarefas()
    {
        return $this->opt_tarefas;
    }
    public function set_opt_tarefas($value)
    {
        $this->opt_tarefas = $value;
    }

    public function get_opt_workspace()
    {
        return $this->opt_workspace;
    }
    public function set_opt_workspace($value)
    {
        $this->opt_workspace = $value;
    }

    public function get_cd_cargo()
    {
        return $this->cd_cargo;
    }
    public function set_cd_cargo($value)
    {
        $this->cd_cargo = $value;
    }
    public function get_cargo()
    {
        if(is_null($this->cargo))
        {
            $this->cargo = new entity_projetos_cargos();
        }
        return $this->cargo;
    }
    public function set_cargo(entity_projetos_cargos $value)
    {
        $this->cargo = $value;
    }

    public function get_usu_email()
    {
        return $this->usu_email;
    }
    public function set_usu_email($value)
    {
        $this->usu_email = $value;
    }

    public function get_opt_dicas()
    {
        return $this->opt_dicas;
    }
    public function set_opt_dicas($value)
    {
        $this->opt_dicas = $value;
    }

    public function get_opt_interatividade()
    {
        return $this->opt_interatividade;
    }
    public function set_opt_interatividade($value)
    {
        $this->opt_interatividade = $value;
    }

    public function get_tela_inicial()
    {
        return $this->tela_inicial;
    }
    public function set_tela_inicial($value)
    {
        $this->tela_inicial = $value;
    }

    public function get_estacao_trabalho()
    {
        return $this->estacao_trabalho;
    }
    public function set_estacao_trabalho($value)
    {
        $this->estacao_trabalho = $value;
    }

    public function get_favorito2()
    {
        return $this->favorito2;
    }
    public function set_favorito2($value)
    {
        $this->favorito2 = $value;
    }

    public function get_favorito3()
    {
        return $this->favorito3;
    }
    public function set_favorito3($value)
    {
        $this->favorito3 = $value;
    }

    public function get_favorito4()
    {
        return $this->favorito4;
    }
    public function set_favorito4($value)
    {
        $this->favorito4 = $value;
    }

    public function get_favorito5()
    {
        return $this->favorito5;
    }
    public function set_favorito5($value)
    {
        $this->favorito5 = $value;
    }

    public function get_dt_hora_confirmacao()
    {
        return $this->dt_hora_confirmacao;
    }
    public function set_dt_hora_confirmacao($value)
    {
        $this->dt_hora_confirmacao = $value;
    }

    public function get_dt_hora_scanner_computador()
    {
        return $this->dt_hora_scanner_computador;
    }
    public function set_dt_hora_scanner_computador($value)
    {
        $this->dt_hora_scanner_computador = $value;
    }

    public function get_indic_msg()
    {
        return $this->indic_msg;
    }
    public function set_indic_msg($value)
    {
        $this->indic_msg = $value;
    }

    public function get_texto_msg()
    {
        return $this->texto_msg;
    }
    public function set_texto_msg($value)
    {
        $this->texto_msg = $value;
    }

    public function get_dash1()
    {
        return $this->dash1;
    }
    public function set_dash1($value)
    {
        $this->dash1 = $value;
    }

    public function get_dash2()
    {
        return $this->dash2;
    }
    public function set_dash2($value)
    {
        $this->dash2 = $value;
    }

    public function get_dash3()
    {
        return $this->dash3;
    }
    public function set_dash3($value)
    {
        $this->dash3 = $value;
    }

    public function get_dash4()
    {
        return $this->dash4;
    }
    public function set_dash4($value)
    {
        $this->dash4 = $value;
    }

    public function get_dash5()
    {
        return $this->dash5;
    }
    public function set_dash5($value)
    {
        $this->dash5 = $value;
    }

    public function get_dt_ult_login()
    {
        return $this->dt_ult_login;
    }
    public function set_dt_ult_login($value)
    {
        $this->dt_ult_login = $value;
    }

    public function get_resolucao_video()
    {
        return $this->resolucao_video;
    }
    public function set_resolucao_video($value)
    {
        $this->resolucao_video = $value;
    }

    public function get_dash6()
    {
        return $this->dash6;
    }
    public function set_dash6($value)
    {
        $this->dash6 = $value;
    }

    public function get_dash7()
    {
        return $this->dash7;
    }
    public function set_dash7($value)
    {
        $this->dash7 = $value;
    }

    public function get_np_computador()
    {
        return $this->np_computador;
    }
    public function set_np_computador($value)
    {
        $this->np_computador = $value;
    }

    public function get_ultima_resposta_vida()
    {
        return $this->ultima_resposta_vida;
    }
    public function set_ultima_resposta_vida($value)
    {
        $this->ultima_resposta_vida = $value;
    }

    public function get_chamada_web()
    {
        return $this->chamada_web;
    }
    public function set_chamada_web($value)
    {
        $this->chamada_web = $value;
    }
}

class entity_participantes
{

    private $col_rowid;
    private $cd_empresa;
    private $cd_registro_empregado;
    private $seq_dependencia;
    private $cd_estado_civil;
    private $cd_grau_de_instrucao;
    private $nome;
    private $dt_nascimento;
    private $sexo;
    private $cd_instituicao;
    private $cd_agencia;
    private $cd_instituicao_pode_ter_conta_;
    private $cd_agencia_pode_ter_conta_debi;
    private $cep;
    private $complemento_cep;
    private $tipo_folha;
    private $dt_obito;
    private $logradouro;
    private $bairro;
    private $cidade;
    private $unidade_federativa;
    private $ddd;
    private $telefone;
    private $ramal;
    private $conta_folha;
    private $conta_debitos;
    private $cpf_mf;
    private $email;
    private $dt_dig_obito;
    private $bloqueio_ender;
    private $dt_inicio_beneficio;
    private $cd_registro_patroc;
    private $dt_recadastramento;
    private $dt_envio_recadastramento;
    private $tipo_recadastramento;
    private $cd_plano;
    private $quant_dep_economico;
    private $data_alteracao_dep_economico;
    private $celular;
    private $fax;
    private $motivo_devolucao_correio;
    private $dt_alteracao_endereco;
    private $sigla_pais;
    private $dt_inclusao;
    private $usu_inclusao;
    private $dt_alteracao;
    private $usu_alteracao;
    private $opcao_ir;
    private $dt_opcao_ir;
    private $dt_adesao_instituidor;
    private $cd_grau_depen_instituidor;
    private $ddd_outro;
    private $telefone_outro;
    private $email_profissional;
    private $dt_envio_certificado;
    

    function entity_public_participantes()
    {
    }

    function __destruct()
    {
    }

    
    public function get_col_rowid()
    {
        return $this->col_rowid;
    }
    public function set_col_rowid($value)
    {
        $this->col_rowid = $value;
    }

    public function get_cd_empresa()
    {
        return $this->cd_empresa;
    }
    public function set_cd_empresa($value)
    {
        $this->cd_empresa = $value;
    }

    public function get_cd_registro_empregado()
    {
        return $this->cd_registro_empregado;
    }
    public function set_cd_registro_empregado($value)
    {
        $this->cd_registro_empregado = $value;
    }

    public function get_seq_dependencia()
    {
        return $this->seq_dependencia;
    }
    public function set_seq_dependencia($value)
    {
        $this->seq_dependencia = $value;
    }

    public function get_cd_estado_civil()
    {
        return $this->cd_estado_civil;
    }
    public function set_cd_estado_civil($value)
    {
        $this->cd_estado_civil = $value;
    }

    public function get_cd_grau_de_instrucao()
    {
        return $this->cd_grau_de_instrucao;
    }
    public function set_cd_grau_de_instrucao($value)
    {
        $this->cd_grau_de_instrucao = $value;
    }

    public function get_nome()
    {
        return $this->nome;
    }
    public function set_nome($value)
    {
        $this->nome = $value;
    }

    public function get_dt_nascimento()
    {
        return $this->dt_nascimento;
    }
    public function set_dt_nascimento($value)
    {
        $this->dt_nascimento = $value;
    }

    public function get_sexo()
    {
        return $this->sexo;
    }
    public function set_sexo($value)
    {
        $this->sexo = $value;
    }

    public function get_cd_instituicao()
    {
        return $this->cd_instituicao;
    }
    public function set_cd_instituicao($value)
    {
        $this->cd_instituicao = $value;
    }

    public function get_cd_agencia()
    {
        return $this->cd_agencia;
    }
    public function set_cd_agencia($value)
    {
        $this->cd_agencia = $value;
    }

    public function get_cd_instituicao_pode_ter_conta_()
    {
        return $this->cd_instituicao_pode_ter_conta_;
    }
    public function set_cd_instituicao_pode_ter_conta_($value)
    {
        $this->cd_instituicao_pode_ter_conta_ = $value;
    }

    public function get_cd_agencia_pode_ter_conta_debi()
    {
        return $this->cd_agencia_pode_ter_conta_debi;
    }
    public function set_cd_agencia_pode_ter_conta_debi($value)
    {
        $this->cd_agencia_pode_ter_conta_debi = $value;
    }

    public function get_cep()
    {
        return $this->cep;
    }
    public function set_cep($value)
    {
        $this->cep = $value;
    }

    public function get_complemento_cep()
    {
        return $this->complemento_cep;
    }
    public function set_complemento_cep($value)
    {
        $this->complemento_cep = $value;
    }

    public function get_tipo_folha()
    {
        return $this->tipo_folha;
    }
    public function set_tipo_folha($value)
    {
        $this->tipo_folha = $value;
    }

    public function get_dt_obito()
    {
        return $this->dt_obito;
    }
    public function set_dt_obito($value)
    {
        $this->dt_obito = $value;
    }

    public function get_logradouro()
    {
        return $this->logradouro;
    }
    public function set_logradouro($value)
    {
        $this->logradouro = $value;
    }

    public function get_bairro()
    {
        return $this->bairro;
    }
    public function set_bairro($value)
    {
        $this->bairro = $value;
    }

    public function get_cidade()
    {
        return $this->cidade;
    }
    public function set_cidade($value)
    {
        $this->cidade = $value;
    }

    public function get_unidade_federativa()
    {
        return $this->unidade_federativa;
    }
    public function set_unidade_federativa($value)
    {
        $this->unidade_federativa = $value;
    }

    public function get_ddd()
    {
        return $this->ddd;
    }
    public function set_ddd($value)
    {
        $this->ddd = $value;
    }

    public function get_telefone()
    {
        return $this->telefone;
    }
    public function set_telefone($value)
    {
        $this->telefone = $value;
    }

    public function get_ramal()
    {
        return $this->ramal;
    }
    public function set_ramal($value)
    {
        $this->ramal = $value;
    }

    public function get_conta_folha()
    {
        return $this->conta_folha;
    }
    public function set_conta_folha($value)
    {
        $this->conta_folha = $value;
    }

    public function get_conta_debitos()
    {
        return $this->conta_debitos;
    }
    public function set_conta_debitos($value)
    {
        $this->conta_debitos = $value;
    }

    public function get_cpf_mf()
    {
        return $this->cpf_mf;
    }
    public function set_cpf_mf($value)
    {
        $this->cpf_mf = $value;
    }

    public function get_email()
    {
        return $this->email;
    }
    public function set_email($value)
    {
        $this->email = $value;
    }

    public function get_dt_dig_obito()
    {
        return $this->dt_dig_obito;
    }
    public function set_dt_dig_obito($value)
    {
        $this->dt_dig_obito = $value;
    }

    public function get_bloqueio_ender()
    {
        return $this->bloqueio_ender;
    }
    public function set_bloqueio_ender($value)
    {
        $this->bloqueio_ender = $value;
    }

    public function get_dt_inicio_beneficio()
    {
        return $this->dt_inicio_beneficio;
    }
    public function set_dt_inicio_beneficio($value)
    {
        $this->dt_inicio_beneficio = $value;
    }

    public function get_cd_registro_patroc()
    {
        return $this->cd_registro_patroc;
    }
    public function set_cd_registro_patroc($value)
    {
        $this->cd_registro_patroc = $value;
    }

    public function get_dt_recadastramento()
    {
        return $this->dt_recadastramento;
    }
    public function set_dt_recadastramento($value)
    {
        $this->dt_recadastramento = $value;
    }

    public function get_dt_envio_recadastramento()
    {
        return $this->dt_envio_recadastramento;
    }
    public function set_dt_envio_recadastramento($value)
    {
        $this->dt_envio_recadastramento = $value;
    }

    public function get_tipo_recadastramento()
    {
        return $this->tipo_recadastramento;
    }
    public function set_tipo_recadastramento($value)
    {
        $this->tipo_recadastramento = $value;
    }

    public function get_cd_plano()
    {
        return $this->cd_plano;
    }
    public function set_cd_plano($value)
    {
        $this->cd_plano = $value;
    }

    public function get_quant_dep_economico()
    {
        return $this->quant_dep_economico;
    }
    public function set_quant_dep_economico($value)
    {
        $this->quant_dep_economico = $value;
    }

    public function get_data_alteracao_dep_economico()
    {
        return $this->data_alteracao_dep_economico;
    }
    public function set_data_alteracao_dep_economico($value)
    {
        $this->data_alteracao_dep_economico = $value;
    }

    public function get_celular()
    {
        return $this->celular;
    }
    public function set_celular($value)
    {
        $this->celular = $value;
    }

    public function get_fax()
    {
        return $this->fax;
    }
    public function set_fax($value)
    {
        $this->fax = $value;
    }

    public function get_motivo_devolucao_correio()
    {
        return $this->motivo_devolucao_correio;
    }
    public function set_motivo_devolucao_correio($value)
    {
        $this->motivo_devolucao_correio = $value;
    }

    public function get_dt_alteracao_endereco()
    {
        return $this->dt_alteracao_endereco;
    }
    public function set_dt_alteracao_endereco($value)
    {
        $this->dt_alteracao_endereco = $value;
    }

    public function get_sigla_pais()
    {
        return $this->sigla_pais;
    }
    public function set_sigla_pais($value)
    {
        $this->sigla_pais = $value;
    }

    public function get_dt_inclusao()
    {
        return $this->dt_inclusao;
    }
    public function set_dt_inclusao($value)
    {
        $this->dt_inclusao = $value;
    }

    public function get_usu_inclusao()
    {
        return $this->usu_inclusao;
    }
    public function set_usu_inclusao($value)
    {
        $this->usu_inclusao = $value;
    }

    public function get_dt_alteracao()
    {
        return $this->dt_alteracao;
    }
    public function set_dt_alteracao($value)
    {
        $this->dt_alteracao = $value;
    }

    public function get_usu_alteracao()
    {
        return $this->usu_alteracao;
    }
    public function set_usu_alteracao($value)
    {
        $this->usu_alteracao = $value;
    }

    public function get_opcao_ir()
    {
        return $this->opcao_ir;
    }
    public function set_opcao_ir($value)
    {
        $this->opcao_ir = $value;
    }

    public function get_dt_opcao_ir()
    {
        return $this->dt_opcao_ir;
    }
    public function set_dt_opcao_ir($value)
    {
        $this->dt_opcao_ir = $value;
    }

    public function get_dt_adesao_instituidor()
    {
        return $this->dt_adesao_instituidor;
    }
    public function set_dt_adesao_instituidor($value)
    {
        $this->dt_adesao_instituidor = $value;
    }

    public function get_cd_grau_depen_instituidor()
    {
        return $this->cd_grau_depen_instituidor;
    }
    public function set_cd_grau_depen_instituidor($value)
    {
        $this->cd_grau_depen_instituidor = $value;
    }

    public function get_ddd_outro()
    {
        return $this->ddd_outro;
    }
    public function set_ddd_outro($value)
    {
        $this->ddd_outro = $value;
    }

    public function get_telefone_outro()
    {
        return $this->telefone_outro;
    }
    public function set_telefone_outro($value)
    {
        $this->telefone_outro = $value;
    }

    public function get_email_profissional()
    {
        return $this->email_profissional;
    }
    public function set_email_profissional($value)
    {
        $this->email_profissional = $value;
    }

    public function get_dt_envio_certificado()
    {
        return $this->dt_envio_certificado;
    }
    public function set_dt_envio_certificado($value)
    {
        $this->dt_envio_certificado = $value;
    }

}

class entity_public_listas
{

    private $codigo;
    private $descricao;
    private $categoria;
    private $divisao;
    private $valor;
    private $tipo;
    private $imagem_associada;
    private $valor1;
    private $valor2;
    private $desviar_para;
    private $visao;
    private $dt_exclusao;

    function entity_public_listas()
    {
    }

    function __destruct()
    {
    }
    
    public function get_codigo()
    {
        return $this->codigo;
    }
    public function set_codigo($value)
    {
        $this->codigo = $value;
    }

    public function get_descricao()
    {
        return $this->descricao;
    }
    public function set_descricao($value)
    {
        $this->descricao = $value;
    }

    public function get_categoria()
    {
        return $this->categoria;
    }
    public function set_categoria($value)
    {
        $this->categoria = $value;
    }

    public function get_divisao()
    {
        return $this->divisao;
    }
    public function set_divisao($value)
    {
        $this->divisao = $value;
    }

    public function get_valor()
    {
        return $this->valor;
    }
    public function set_valor($value)
    {
        $this->valor = $value;
    }

    public function get_tipo()
    {
        return $this->tipo;
    }
    public function set_tipo($value)
    {
        $this->tipo = $value;
    }

    public function get_imagem_associada()
    {
        return $this->imagem_associada;
    }
    public function set_imagem_associada($value)
    {
        $this->imagem_associada = $value;
    }

    public function get_valor1()
    {
        return $this->valor1;
    }
    public function set_valor1($value)
    {
        $this->valor1 = $value;
    }

    public function get_valor2()
    {
        return $this->valor2;
    }
    public function set_valor2($value)
    {
        $this->valor2 = $value;
    }

    public function get_desviar_para()
    {
        return $this->desviar_para;
    }
    public function set_desviar_para($value)
    {
        $this->desviar_para = $value;
    }

    public function get_visao()
    {
        return $this->visao;
    }
    public function set_visao($value)
    {
        $this->visao = $value;
    }

    public function get_dt_exclusao()
    {
        return $this->dt_exclusao;
    }
    public function set_dt_exclusao($value)
    {
        $this->dt_exclusao = $value;
    }

}

class entity_expansao_empresas_instituicoes_comunidades
{

    private $cd_empresas_instituicoes_comunidades;
    private $cd_emp_inst;
    private $cd_comunidade;
    private $dt_exclusao;
    

    function entity_expansao_empresas_instituicoes_comunidades()
    {
    }

    function __destruct()
    {
    }

    
    public function get_cd_empresas_instituicoes_comunidades()
    {
        return $this->cd_empresas_instituicoes_comunidades;
    }
    public function set_cd_empresas_instituicoes_comunidades($value)
    {
        $this->cd_empresas_instituicoes_comunidades = $value;
    }

    public function get_cd_emp_inst()
    {
        return $this->cd_emp_inst;
    }
    public function set_cd_emp_inst($value)
    {
        $this->cd_emp_inst = $value;
    }

    public function get_cd_comunidade()
    {
        return $this->cd_comunidade;
    }
    public function set_cd_comunidade($value)
    {
        $this->cd_comunidade = $value;
    }

    public function get_dt_exclusao()
    {
        return $this->dt_exclusao;
    }
    public function set_dt_exclusao($value)
    {
        $this->dt_exclusao = $value;
    }

}


// NOVA GERAÇÃO
class entity_projetos_usuario_matriz
{
    public $cd_usuario_matriz;
    public $cd_matriz_salarial;
    public $cd_usuario;
    public $dt_admissao;
    public $dt_promocao;
    public $cd_escolaridade;
    public $tipo_promocao;
}

class entity_projetos_matriz_salarial
{
    public $cd_matriz_salarial;
    public $cd_familias_cargos;
    public $faixa;
    public $dt_exclusao;
    public $valor_inicial;
    public $valor_final;
}

class entity_public_controle_geracao_cobranca
{
    public $col_rowid;
    public $cd_plano;
    public $cd_empresa;
    public $mes_competencia;
    public $ano_competencia;
    public $dt_confirmacao;
    public $usuario_confirmacao;
    public $tot_internet_confirm;
    public $tot_bdl_confirm;
    public $tot_arrec_confirm;
    public $dt_geracao;
    public $usuario_geracao;
    public $tot_internet_gerado;
    public $tot_bdl_gerado;
    public $tot_arrec_gerado;
    public $vlr_internet_gerado;
    public $vlr_bdl_gerado;
    public $vlr_arrec_gerado;
    public $dt_envio_internet;
    public $usuario_envio_internet;
    public $tot_internet_enviado;
    public $vlr_internet_enviado;
    public $dt_envio_bdl;
    public $usuario_envio_bdl;
    public $tot_bdl_enviado;
    public $vlr_bdl_enviado;
    public $dt_envio_arrec;
    public $usuario_envio_arrec;
    public $tot_arrec_enviado;
    public $vlr_arrec_enviado;
    public $tot_cheque_confirm;
    public $tot_deposito_confirm;
    public $tot_debito_cc_confirm;
    public $vlr_cheque_confirm;
    public $vlr_deposito_confirm;
    public $vlr_debito_cc_confirm;
    public $tot_cheque_gerado;
    public $tot_deposito_gerado;
    public $tot_debito_cc_gerado;
    public $vlr_cheque_gerado;
    public $vlr_deposito_gerado;
    public $vlr_debito_cc_gerado;
    public $vlr_debito_cc_gerado_cobranca;
    public $tot_folha_confirm;
    public $vlr_folha_confirm;
    public $tot_folha_gerado;
    public $vlr_folha_gerado;
    public $dt_confirma_fol;
    public $usuario_confirma_fol;
    public $dt_geracao_fol;
    public $usuario_geracao_fol;
    public $tot_debito_cc_enviado;
    public $vlr_debito_cc_enviado;
    public $dt_envio_debito_cc;
    public $usuario_envio_debito_cc;
}

class entity_projetos_avaliacao_controle
{
    public $cd_avaliacao_controle;
    public $dt_periodo;
    public $dt_abertura;
    public $dt_fechamento;
    public $cd_usuario_abertura;
    public $cd_usuario_fechamento;
}

class entity_public_bloqueto
{
    public $dt_emissao;
    public $motivo;
    public $cd_ocorrencia;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $ano_competencia;
    public $mes_competencia;
    public $seq_lancamento;
    public $dt_lancamento;
    public $codigo_lancamento;
    public $descricao;
    public $dt_vencimento;
    public $valor_lancamento;
    public $vlr_multa;
    public $vlr_encargo;
    public $cpf_mf;
    public $nome;
    public $logradouro;
    public $cep;
    public $complemento_cep;
    public $cidade;
    public $unidade_federativa;
    public $seq_responsavel;
    public $tipo_folha;
    public $status;
    public $data_retorno;
    public $valor_consid_integr;
    public $id_integracao;
    public $doc_cobranca;
    public $programa_origem;
    public $cd_plano;
    public $dt_limite_sem_encargos;
    public $vlr_encargos_integr;
    public $vlr_basica_integr;
    public $vlr_risco_integr;
    public $vlr_adm_integr;
    public $dt_inclusao_cobranca;
    public $qtd_enviada;
}

class entity_projetos_auto_atendimento_pagamento_impressao
{
    public $cd_auto_atendimento_pagamento_impressao;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $tp_documento;
    public $vl_valor;
    public $mes_competencia;
    public $ano_competencia;
    public $dt_vencimento;
    public $dt_impressao;
    public $ip;
    
}
?>