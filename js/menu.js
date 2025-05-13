		function setMenuShow(id_menu)
		{
			$("li_menu_s").style.display = "none";	
			$("li_menu_h").style.display = "block";
			
			var ar_menu = id_menu.split(",");
			for(i = 0; i < ar_menu.length; i++)
			{
				$("li_menu_"+ar_menu[i]).style.display = "block";
			}
		}

		function setMenuHide(id_menu)
		{
			$("li_menu_h").style.display = "none";
			$("li_menu_s").style.display = "block";
			
			var ar_menu = id_menu.split(",");
			for(i = 0; i < ar_menu.length; i++)
			{
				$("li_menu_"+ar_menu[i]).style.display = "none";
			}
		}

		function getMenu(cd_menu, id_menu, lnk, base)
		{
			if(base==undefined)base=document.getElementById('base_url').value;
			if(lnk==undefined) lnk='';
			
			// $("#menu").load("/Main_Page #jq-p-Getting-Started li");
			
			 $.ajax({
			   type: "POST",
			   url: base + "menu/load",
			   data: "ds_funcao="+getMenu+"&cd_menu="+cd_menu+"",
			   success: function(msg){

				     document.getElementById('menu').innerHTML = msg;

				     // Callback
					if(id_menu != "")
					{
						var ar_menu = id_menu.split(",");
						id_menu = "";
						for(i = 0; i < ar_menu.length; i++)
						{
							if(cd_menu != ar_menu[i])
							{
								if(id_menu == "")
								{
									id_menu = ar_menu[i];
								}
								else
								{
									id_menu+= "," + ar_menu[i];
								}
							}
						}
						setMenuHide(id_menu);
					}

					if(lnk!='')
					{
						location.href=lnk;
					}
				}
			 });

			/*
			new Ajax.Updater( 'menu', base + "menu/load",
			{
				method:'post',
				parameters: 
				{ 
					ds_funcao: 'getMenu'
					,cd_menu: cd_menu
				},
				onComplete:function()
				{
					// Callback
					if(id_menu != "")
					{
						var ar_menu = id_menu.split(",");
						id_menu = "";
						for(i = 0; i < ar_menu.length; i++)
						{
							if(cd_menu != ar_menu[i])
							{
								if(id_menu == "")
								{
									id_menu = ar_menu[i];
								}
								else
								{
									id_menu+= "," + ar_menu[i];
								}
							}
						}
						setMenuHide(id_menu);
					}
					
					if(lnk!='')
					{
						location.href=lnk;
					}
				}
			});*/
			
			
		}