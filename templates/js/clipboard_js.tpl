<!-- clipboard -->
<script type="text/javascript" src="{$base_url}bootstrap/assets/clipboard/clipboard.js"></script>

<script>
    $(document).ready(function(){
		var clipboard = new Clipboard('.btn', {
			text: function(trigger)
			{
				var source = trigger.getAttribute('id');
				return source; 
			}
		});
		clipboard.on('success', function(e) {
			console.log(e);
			alert("copying: " + e.text);
		});

		clipboard.on('error', function(e) {
			console.log(e);
			alert("copying: " + e.text);
		});	
    });
</script>