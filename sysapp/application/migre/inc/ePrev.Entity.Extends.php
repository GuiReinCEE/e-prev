<?php
class entity_projetos_avaliacao_capa_extended extends entity_projetos_avaliacao_capa
{
    public $avaliacoes; // Array de objeto		entity_projetos_avaliacao_extended
    public $comite;     // Array de objeto		entity_projetos_avaliacao_comite
    public $avaliador;  // objeto				entity_projetos_usuarios_controledi
    public $avaliado;   // objeto				entity_projetos_usuarios_controledi
	public $cargo;		// objeto				entity_projetos_cargos

    function entity_projetos_avaliacao_capa_extended()
    {
        parent::entity_projetos_avaliacao_capa();
    }
    
    public function load()
    {
        $ado = new ADO_projetos_avaliacao_capa();
        return true;
    }
}

class entity_projetos_avaliacao_extended extends entity_projetos_avaliacao
{
    public $competencias_especificas;      // Array de objeto     entity_projetos_comp_espec
    public $competencias_institucionais;   // Array de objeto     entity_projetos_comp_inst
    public $responsabilidades;             // Array de objeto     entity_projetos_responsabilidades
    public $avaliador;                     // Objeto              entity_projetos_usuarios_controledi
    public $aspectos;                      // Array de Objeto     entity_projetos_avaliacao_aspecto

    function entity_projetos_avaliacao_extended()
    {
        parent::entity_projetos_avaliacao();
        $this->avaliador = new entity_projetos_usuarios_controledi();
    }
}

class entity_projetos_avaliacao_comite_extended extends entity_projetos_avaliacao_comite
{
    public $avaliador;  // objeto     entity_projetos_usuarios_controledi

    function entity_projetos_avaliacao_extended()
    {
        parent::entity_projetos_comite();
    }
}

class entity_expansao_empresas_instituicoes_comunidades_extended extends entity_expansao_empresas_instituicoes_comunidades
{
    public $comunidade;  // objeto     entity_public_listas

    function ADO_expansao_empresas_instituicoes_extended()
    {
        parent::__construct();
    }


}

class entity_projetos_usuarios_controledi_extended extends entity_projetos_usuarios_controledi
{
    public $version = '1.0';
    public $usuario_matriz;         // objeto     entity_projetos_usuario_matriz
}

class entity_projetos_usuario_matriz_extended extends entity_projetos_usuario_matriz
{
    public $version = '1.0';
    public $matriz_salarial;         // objeto     entity_projetos_matriz_salarial
}

class entity_projetos_matriz_salarial_extended extends entity_projetos_matriz_salarial
{
    public $version = '1.0';
    public $familias_cargos;         // objeto     entity_projetos_familias_cargos
}

class entity_projetos_envia_emails_extended extends entity_projetos_envia_emails
{
	public $dt_retorno = '';
}

?>