<script>
function displayVals() {
	$.ajax({
		type: "GET",
		url: "{$base_url}serverside/users/update-v4.php",
		success: function(data){
			$('textarea').val(data);
		}
	});
}
displayVals();

function codeUpdate()
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to update?',function(){
		$.ajax({
			url: "{$base_url}/update-v4.php",
			type: "POST",
			data: $('#GUIFrm').serialize(),
			cache: false,
			success: function(response)
			{
				alertify.success('Successfully Updated!...');
				$('#success').html(response);
				displayVals();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alertify.error('Failed to Update!...');
				$('#success').html(response);
				displayVals();
			}
		});
	},function(){
		alertify.error('Declined');
		displayVals();
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});	
}
</script>