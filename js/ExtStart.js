Ext.onReady(function(){
	Ext.get('okButton').on('click', function(){
		var msg = Ext.get('msg');
		msg.load({
			url: 'http://10.63.255.94/cieprev/ajax-example.php', // <-- change if necessary
			params: 'name=' + Ext.get('name').dom.value,
			text: 'Updating...'
		});
		msg.show();
	});
});