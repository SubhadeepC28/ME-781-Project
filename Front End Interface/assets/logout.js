function logout(){
	$.ajax({
		url: "../assets/utils/logout.php",
		success: function (result) {
			console.log(result);
			if(result === 'F'){
				M.toast({html: 'Some error occurred... Please try again!'});
			}
			else{
				window.location.href = '../';
			}
		}
	});
}