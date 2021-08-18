function get_days(timestamp){
	timestamp = new Date(timestamp);
	let days = timestamp.getDay();
	if(days <= 0) return 'Today';
	if(days <= 1) return 'Yesterday';
	return days+' days ago';
	// return hours + ':' + minutes + ' ' + newformat;
}