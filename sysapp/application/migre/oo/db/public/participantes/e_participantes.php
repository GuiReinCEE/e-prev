<?php
class e_participantes extends Entity
{
	public $col_rowid;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $cd_estado_civil;
    public $cd_grau_de_instrucao;
    public $nome;
    public $dt_nascimento;
    public $sexo;
    public $cd_instituicao;
    public $cd_agencia;
    public $cd_instituicao_pode_ter_conta_;
    public $cd_agencia_pode_ter_conta_debi;
    public $cep;
    public $complemento_cep;
    public $tipo_folha;
    public $dt_obito;
    public $logradouro;
    public $bairro;
    public $cidade;
    public $unidade_federativa;
    public $ddd;
    public $telefone;
    public $ramal;
    public $conta_folha;
    public $conta_debitos;
    public $cpf_mf;
    public $email;
    public $dt_dig_obito;
    public $bloqueio_ender;
    public $dt_inicio_beneficio;
    public $cd_registro_patroc;
    public $dt_recadastramento;
    public $dt_envio_recadastramento;
    public $tipo_recadastramento;
    public $cd_plano;
    public $quant_dep_economico;
    public $data_alteracao_dep_economico;
    public $celular;
    public $fax;
    public $motivo_devolucao_correio;
    public $dt_alteracao_endereco;
    public $sigla_pais;
    public $dt_inclusao;
    public $usu_inclusao;
    public $dt_alteracao;
    public $usu_alteracao;
    public $opcao_ir;
    public $dt_opcao_ir;
    public $dt_adesao_instituidor;
    public $cd_grau_depen_instituidor;
    public $ddd_outro;
    public $telefone_outro;
    public $email_profissional;
    public $dt_envio_certificado;
    public $dt_recebimento_compl_apos;
}

class e_participantes_ext extends e_participantes
{
	/**
	 * PARTICIPANTE MIGRADO?
	 * 
	 * Identifica se o participante foi migrado, uma subquery serс rodada para carregar essa informaчуo
	 * a migraчуo atende aos planos 1>>2, de uma data em diante todo novo ingresso щ feita no plano 2
	 * para CEEE e FCEEE, mas os antigos que estavam no plano 1 tiveram opчуo de migraчуo.
	 */
	public $migrado;
	public $ativo;
	
	public function is_migrado()
	{
		if( ! isset($this->migrado))
		{
			$this->migrado = t_participantes::verifica_se_migrado( $this );
			
		}
		return $this->migrado;
	}
	
	public function is_ativo()
	{
		if( ! isset($this->ativo))
		{
			$this->ativo = t_participantes::verifica_se_ativo( $this );
			
		}
		return $this->ativo;
	}
}
?>