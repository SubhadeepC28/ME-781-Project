$(document).ready(function (){
	let dark_mode = localStorage.getItem("darkmode");
	if(dark_mode === null || dark_mode === false || dark_mode === 'false'){
		$('body').removeClass('dark');
	}
	else{
		$('body').addClass('dark');
	}
})
function toggleDarkMode(){
	let dark_mode = localStorage.getItem("darkmode");
	if(dark_mode === null || dark_mode === false || dark_mode === 'false'){
		localStorage.setItem("darkmode", true);
		$('body').addClass('dark');
	}
	else{
		localStorage.setItem("darkmode", false);
		$('body').removeClass('dark');
	}
}