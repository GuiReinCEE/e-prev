var cad_empresas = {

	Version : '1.0',
	Author : 'cjunior',

	aba_comunidades_Click : function()
	{
		ajaxExecute( "cad_empresas_partial_comunidades.php", "codigo=" + document.getElementById("codigo").value, "document.getElementById('div_content')", ".innerHTML=", "POST" )

		document.getElementById( 'aba_identificacao' ).className = '';
		document.getElementById( 'aba_pessoas' ).className = '';
		document.getElementById( 'aba_contatos' ).className = '';
		document.getElementById( 'aba_comunidades' ).className = 'abaSelecionada';
	},

	adicionar_comunidade_Click : function()
	{
		if(document.getElementById("cd_comunidade_text").value=="")
		{
			alert( "Atenção:\n\nVocê deve escolher uma comunidade antes de adicionar." );
		}
		else
		{
			if(confirm("Atenção:\n\nVocê deseja Incluir uma comunidade nesta empresa?"))
			{
				ajaxExecute( "cad_empresas_partial_comunidades.php", 
					"cd_comunidade_text=" + document.getElementById("cd_comunidade_text").value + "&comando=ajax_insert_comunidade&codigo=" + document.getElementById("codigo").value, 
					"document.getElementById('div_content')", 
					".innerHTML=", 
					"POST" );
			}
		}
	},
	
	excluir_comunidade_Click : function(cd_eic)
	{
		if(confirm("Atenção:\n\nVocê deseja remover a comunidade desta empresa?"))
		{
			ajaxExecute( "cad_empresas_partial_comunidades.php", 
					"cd_empresas_instituicoes_comunidades_text=" + cd_eic + "&comando=ajax_delete_comunidade&codigo=" + document.getElementById("codigo").value + "", 
					"document.getElementById('div_content')", 
					".innerHTML=", 
					"POST" );
		}
	}
}