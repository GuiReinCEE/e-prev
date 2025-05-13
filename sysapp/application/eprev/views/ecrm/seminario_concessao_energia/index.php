<?php
$this->load->view('header', array('topo_titulo'=>'Seminario Concessão de Energia') );
?>
<script>
function filtrar()
{
	document.getElementById("current_page").value = 0;
	load();
}
function load()
{
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_concessao_energia/listar'
		,{
			current_page: $('#current_page').val()
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}
function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
				"DateTimeBR"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "RE"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
}


function setPresente(fl_presente,cd_inscricao)
{
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_concessao_energia/presente'
		,{
			cd_inscricao: cd_inscricao,
			fl_presente: fl_presente
		}
		,
	function(data)
		{
			if(data != "")
			{
				alert(data);
			}
			;
		}
	);
}


function excluirInscricao(cd_inscricao)
{
	if(confirm("Deseja excluir a inscrição?"))
	{
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_concessao_energia/excluir'
			,{
				cd_inscricao: cd_inscricao
			}
			,
		function(data)
			{
				if(data != "")
				{
					alert(data);
				}
				else
				{
					filtrar();
					alert("Inscrição excluída");
				}
			}
		);
	}
}
</script>
<?php
$abas[0] = array('aba_lista', 'Lista', true, 'location.reload();');
echo aba_start( $abas );


?>
<div id="result_div"></div>
<br />
<?php echo aba_end( ''); ?>
<script type="text/javascript">
	filtrar();
</script>
<?php
$this->load->view('footer');
?>