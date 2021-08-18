function get_12_hr(timestamp){
	timestamp = new Date(timestamp);
	let hours =
		// (timestamp.getHours() < 10 ? '0' : '') +
		timestamp.getHours();
	let minutes =
		// (timestamp.getMinutes() < 10 ? '0' : '') +
		timestamp.getMinutes();
	let newformat = hours >= 12 ? 'PM' : 'AM';
	hours = hours % 12;
	hours = hours ? hours : 12;
	// hours = hours < 10 ? '0' + hours : hours;
	minutes = minutes < 10 ? '0' + minutes : minutes;
	return hours + ':' + minutes + ' ' + newformat;
}