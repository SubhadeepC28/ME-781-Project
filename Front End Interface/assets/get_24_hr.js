function get_24_hr(timestamp){
	timestamp = new Date(timestamp);
	let hours = (timestamp.getHours() < 10 ? '0' : '') +
		timestamp.getHours();
	let minutes = (timestamp.getMinutes() < 10 ? '0' : '') +
		timestamp.getMinutes();
	return hours + ':' + minutes;
}