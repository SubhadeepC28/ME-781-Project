$(document).ready(function () {
	$(".hamburger").click(function () {
		$(".side-menu").toggleClass('active');
		$(".overlay").toggleClass('active');
	});

	$(".overlay").click(function () {
		$(".side-menu").toggleClass('active');
		$(".overlay").toggleClass('active');
	});
	$(".side-menu-div>ul>li").click(function () {
		if($(this).find('a').find('i').html()==='wallpaper') {
			alert('This feature is yet to be implemented...');
		}
		else if($(this).find('a').find('i').html()==='brightness_4') {
		}
		else{
			$(".side-menu").toggleClass('active');
			$(".overlay").toggleClass('active');
		}
	});
	$("#addGroupModal").modal();
	$("#addGroupSubmit").click(function(e) {
		e.preventDefault();
		let name_el = $("#group_name");
		let name = name_el.val().trim();
		if(name.length<4) {
			name_el.val(name);
			name_el.addClass('invalid');
			return;
		}
		$.ajax({
			url: "../assets/utils/addGroup.php",
			type:"POST",
			data:{
				"name":name
			},
			success: function (result) {
				console.log(result);
				if(result === 'S'){
					M.toast({html: 'Group added successfully!'});
					setTimeout(function(){window.location.reload();},1000);
				}
				else if(result === 'group_exist'){
					M.toast({html: 'This group already exists!'});
				}
				else{
					M.toast({html: 'Some error occurred... Please try again!'});
				}
			}
		});
	});
});