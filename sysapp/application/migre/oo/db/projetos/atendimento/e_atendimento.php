<?php
class e_atendimento extends Entity
{
    public $cd_atendimento;
    public $cd_plano;
    public $cd_empresa;
    public $cd_registro_empregado;
    public $seq_dependencia;
    public $dt_hora_inicio_atendimento;
    public $dt_hora_fim_atendimento;
    public $id_atendente;
    public $origem_atendimento;
    public $indic_ativo;
    public $obs;
    public $tipo_atendimento_indicado;
    public $opt_atendimento;
    public $dt_encaminhamento;
    public $resp_encaminhamento;
    public $hora_senha;
    public $tipo_reclamacao;
    public $cd_programa;
    public $cd_tipo_obs;
    public $cd_tipo_solicitante;
}
?>