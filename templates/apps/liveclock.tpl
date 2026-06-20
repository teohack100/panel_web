<script>
    flag_time = true;
	timer = '';
	{literal}
	setInterval(function(){phpJavascriptClock();},1000);
	{/literal}		
	function phpJavascriptClock()
	{
			if ( flag_time ) {
			timer = {$current_timestamp}*1000;
			}
		var d = new Date(timer);
		var months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
				
		var month_array = new Array('January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
				
		var day_array = new Array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
				
		currentYear = d.getFullYear();
		month = d.getMonth();
		var currentMonth = months[month];
		var currentMonth1 = month_array[month];
		var currentDate = d.getDate();
		currentDate = currentDate < 10 ? '0'+currentDate : currentDate;
				
		var day = d.getDay();
		current_day = day_array[day];
		var hours = d.getHours();
		var minutes = d.getMinutes();
		var seconds = d.getSeconds();
				
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour ’0′ should be ’12′
		minutes = minutes < 10 ? '0'+minutes : minutes;
		seconds = seconds < 10 ? '0'+seconds : seconds;
		var strTime = hours + ':' + minutes+ ':' + seconds + ' ' + ampm;
		timer = timer + 1000;
				
		document.getElementById("liveTime").innerHTML= currentMonth1+' ' + currentDate+' , ' + currentYear + ' ' + strTime ;	
				
		flag_time = false;
	}
</script>