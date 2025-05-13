<?php
class e_dependentes extends Entity
{
	public $col_rowid;
	public $cd_empresa;
	public $cd_registro_empregado;
	public $seq_dependencia;
	public $cd_grau_parentesco;
	public $tipo_pensao;
	public $cd_motivo_desligamento;
	public $id_incapacidade;
	public $id_pensionista;
	public $seq_pensionista;
	public $dt_desligamento;
	public $id_economico;
	public $id_pensao_alimenticia;
	public $id_previdenciario;
	public $seq_grupo;
	public $dt_motivo_especial;
	public $cd_motivo_especial;
	public $ck_dep_risco;
}

class e_dependentes_ext extends e_dependentes
{
}
?>