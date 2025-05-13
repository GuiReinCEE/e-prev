<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include('inc/ePrev.Enums.php');
include('oo/start.php');
using(array('projetos.tarefa_checklist'));

class eprev_frm_tarefa_checklist_salvar_resposta
{
	static function salvar_resposta()
	{
		if(isset($_POST["cd_tarefas"]))
		{
			$cd_tarefas = $_POST["cd_tarefas"];
			
			if($cd_tarefas!="")
			{
				tarefa_checklist::excluir_resposta($cd_tarefas);
				foreach( $_POST["cd_tarefa_checklist_pergunta"] as $value )
				{
					$cd_tarefa_checklist_pergunta = $value;
					$fl_resposta = ( isset($_POST["resposta_$value"]) ) ? $_POST["resposta_$value"] : "";
					$fl_especialista = ( isset($_POST["especialista_$value"]) ) ? $_POST["especialista_$value"] : "N";

					tarefa_checklist::inserir_resposta( $cd_tarefas, $cd_tarefa_checklist_pergunta, $fl_resposta, $fl_especialista );
				}
			}
		}
	}
}

if( $_POST['comando']=="salvar_resposta" )
{
	eprev_frm_tarefa_checklist_salvar_resposta::salvar_resposta();
	
}
?>

<script>
	alert("Operação concluída!");
</script>