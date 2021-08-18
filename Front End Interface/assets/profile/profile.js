let name,email;
$(document).ready(function () {
	$.ajax({
		url: "../assets/utils/getDetails.php",
		type: "POST",
		success: function (result) {
			if(result === 'F'){
				window.location.href = '../';
			}
			console.log(result);
			result = JSON.parse(result);
			name = result.name;
			email = result.email;
			$("#name").val(result.name);
			$("#email").val(result.email);
			M.updateTextFields();
		}
	});

	$("#eye").click(function (e) {
		$("#password").attr('type', function(index, attr){
			return attr === 'password' ? 'text' : 'password';
		});
		$(this).toggleClass('fa-eye').toggleClass('fa-eye-slash');
	});

	$("#edit").click(function (e) {
		e.preventDefault();
		$(this).removeClass('scale-in').addClass('scale-out');
		$("#change-pass").removeClass('scale-in').addClass('scale-out');
		setTimeout(function(){
			$("#change-pass").html('BACK<i class="fa fas fa-arrow-circle-left left"></i>').attr("data-function","detail-back").removeClass('scale-out').addClass('scale-in');
		},200);
		$("#save").removeClass('scale-out').addClass('scale-in').attr('data-form','details');
		$("#pass-form").slideUp(100);
		$("#detail-form").slideDown();
		let name_el = $("#name");
		let email_el = $("#email");
		name_el.prop('disabled',false);
		email_el.prop('disabled',false);
	});

	$("#change-pass").click(function (e) {
		e.preventDefault();
		if($(this).attr('data-function') === 'back') {
			$(this).removeClass('scale-in').addClass('scale-out');
			setTimeout(function(){
				$("#change-pass").html('CHANGE PASSWORD<i class="fa fas fa-lock right"></i>').attr("data-function","change-pass").removeClass('scale-out').addClass('scale-in');
			},200);
			$("#save").removeClass('scale-in').addClass('scale-out');
			$("#edit").removeClass('scale-out').addClass('scale-in');
			$("#pass-form").slideUp(100);
			$("#detail-form").slideDown();
			$("#password").val('').removeClass('valid').removeClass('invalid');
			$("#password~label").removeClass('active');
		}
		else if($(this).attr('data-function') === 'detail-back') {
			$(this).removeClass('scale-in').addClass('scale-out');
			setTimeout(function(){
				$("#change-pass").html('CHANGE PASSWORD<i class="fa fas fa-lock right"></i>').attr("data-function","change-pass").removeClass('scale-out').addClass('scale-in');
			},200);
			$("#save").removeClass('scale-in').addClass('scale-out');
			$("#edit").removeClass('scale-out').addClass('scale-in');
			$("#email").prop('disabled',true).val(email);
			$("#name").prop('disabled',true).val(name);
			$(".validate").removeClass('valid').removeClass('invalid');
			$("#pass-form").slideUp(100);
			$("#detail-form").slideDown();
		}
		else{
			$(this).removeClass('scale-in').addClass('scale-out');
			setTimeout(function(){
				$("#change-pass").html('BACK<i class="fa fas fa-arrow-circle-left left"></i>').attr("data-function","back").removeClass('scale-out').addClass('scale-in');
			},200);
			$("#edit").removeClass('scale-in').addClass('scale-out');
			$("#save").removeClass('scale-out').addClass('scale-in').attr('data-form','pass');
			$("#detail-form").slideUp(100);
			$("#pass-form").slideDown();
		}
	});
	$("#save").click(function (e) {
		e.preventDefault();
		if($(this).attr('data-form') === 'details'){
			$("#error").slideUp();
			let name_el = $("#name");
			let name_temp = name_el.val().trim();
			let email_el = $("#email");
			let email_temp = email_el.val().trim();
			if(name_temp.length<4) {
				name_el.val(name_temp);
				name_el.addClass('invalid');
				return;
			}
			let emailExp = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;
			if(!emailExp.test(email_temp)) {
				email_el.val(email_temp);
				// email_el.parent().find('.helper-text').attr('data-error','');
				email_el.addClass('invalid');
				return;
			}
			$.ajax({
				url: "../assets/utils/profile/updateDetails.php",
				type:"POST",
				data:{
					"name":name_temp,
					"email":email_temp,
				},
				success: function (result) {
					if(result === 'S'){
						$("#error").slideUp();
						$("#change-pass").removeClass('scale-in').addClass('scale-out');
						setTimeout(function(){
							$("#change-pass").html('CHANGE PASSWORD<i class="fa fas fa-lock right"></i>').attr("data-function","change-pass").removeClass('scale-out').addClass('scale-in');
						},200);
						$("#save").removeClass('scale-in').addClass('scale-out');
						$("#edit").removeClass('scale-out').addClass('scale-in');
						$("#email").prop('disabled',true).val(email);
						$("#name").prop('disabled',true).val(name);
						$(".validate").removeClass('valid').removeClass('invalid');
						$("#pass-form").slideUp(100);
						$("#detail-form").slideDown();
						name=name_temp;
						email=email_temp;
						M.toast({html: 'Profile Updated Successfully!'})
					}
					else if(result === 'user_exist'){
						$("#error").html('This email address is already used...').slideDown();
					}
					else{
						$("#error").html('We are sorry, some error occurred... Please try again...').slideDown();
					}
				}
			});
		}
		else{
			$("#pass-error").slideUp();
			let pass_el = $("#password");
			let pass = pass_el.val();
			if(pass.length<8) {
				pass_el.val(pass);
				pass_el.addClass('invalid')
				return;
			}
			let hashpassword = CryptoJS.HmacSHA256(pass, "LiveChat").toString();
			$.ajax({
				url: "../assets/utils/profile/updatePassword.php",
				type:"POST",
				data:{
					"pass":hashpassword,
				},
				success: function (result) {
					console.log(result);
					if(result === 'S'){
						$("#pass-error").slideUp();
						$("#pass-form").slideUp(100);
						$("#detail-form").slideDown();
						$("#change-pass").removeClass('scale-in').addClass('scale-out');
						setTimeout(function(){
							$("#change-pass").html('CHANGE PASSWORD<i class="fa fas fa-lock right"></i>').attr("data-function","change-pass").removeClass('scale-out').addClass('scale-in');
						},200);
						$("#save").removeClass('scale-in').addClass('scale-out');
						$("#edit").removeClass('scale-out').addClass('scale-in');
						$("#password").val('').removeClass('valid').removeClass('invalid');
						$("#password~label").removeClass('active');
						M.toast({html: 'Password Updated Successfully!'})
					}
					else if(result === 'pass_exist'){
						$("#pass-error").html('Please enter a new password...').slideDown();
					}
					else{
						$("#pass-error").html('We are sorry, some error occurred... Please try again...').slideDown();
					}
				}
			});
		}
	});
});