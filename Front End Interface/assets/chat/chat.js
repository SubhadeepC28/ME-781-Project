let name,email;
function toggleTab(tab){
	let list1 = document.querySelectorAll(tab+" .filter > *");
	let h1 = list1[0].offsetHeight, arr1 = [], i1 = -1, l1 = list1.length;
	let anim1 = "transform" in document.body.style ? "transform" : "webkitTransform";

	while (++i1 < l1) {
		arr1.push(list1[i1].textContent.trim());
		list1[i1].style[anim1] = "translateY(" + i1*h1 +"px)";
	}
	$(tab+" .filter").removeClass('before');
	document.querySelector(tab+" input.filter").addEventListener("input", function() {
		let rgx = new RegExp(this.value, "i");
		let check=0;
		arr1.forEach(function(el, idx) {
			if(idx===arr1.length-1) return ;
			if (rgx.test(el)){
				check=1;
				list1[idx].classList.remove("hidden");
			}
			else list1[idx].classList.add("hidden");
			let i1 = -1;
			let p1 = 0;
			while (++i1 < l1) {
				if (list1[i1].className !== "hidden") {
					list1[i1].style[anim1] = "translateY(" + p1++ * h1 + "px)";
				}
			}
		});
		// console.log(check);
		if(check===0) $(tab+" .no-search").removeClass('hidden');
		else $(tab+" .no-search").addClass('hidden');
	});
}
function say(from,message,timestamp,me,type){
	message = message.trim();
	if(message === '') return;
	if(type==='user'){
		$(".chat-thread").append(
			'               <div class="'+ ((me)?'me':'not-me') +'">\n' +
			'                        <div class="date">' + get_days(timestamp) + '</div>' +
			'                        <div class="text">' + message + '</div>\n' +
			'                        <div class="time">' + get_12_hr(timestamp) + '</div>' +
			'                    </div>'
		);
	}
	else{
		$(".chat-thread").append(
			'               <div class="'+ ((me)?'me':'not-me') +'">\n' +
			'                        <div class="date">' + get_days(timestamp) + '</div>' +
			'                        <div class="name">' + from + '</div>\n' +
			'                        <div class="text">' + message + '</div>\n' +
			'                        <div class="time">' + get_12_hr(timestamp) + '</div>' +
			'                    </div>'
		);
	}
	$(".chat-div")[0].scrollTop = $(".chat-div")[0].scrollHeight;
}
async function refresh_chat(token,type){
	setTimeout(function(){
		var server = new EventSource("../assets/utils/chat/server.php");
		server.onerror = function(err) {
			// M.toast({html: 'Some error occurred... Refreshing...'});
			console.log('Some error occurred... Refreshing...'+Date());
			server.close();
			refresh_chat(token,type);

			// setTimeout(function () {
			// 	window.location.reload();
			// },1000);
		};
		let type_token = type + '_' + token;
		server.addEventListener(type_token, function(event) {
			let data = JSON.parse(event.data);
			for(let i = 0; i < data.length ; i++){
				if(data[i].email === email) continue;
				say(data[i].name, data[i].message, data[i].timestamp,false,type);
			}
		});
	},500);
	server.addEventListener('debug', function(event) {
		console.log('listening '+type);
		console.log(event.data);
	});
}
function openChat(e) {
	$(".chat-loader").fadeIn(100);
	let type = ($(".chat-tab.active").attr('data-target') === '#users')?'user':'group';
	let token = $(e).attr('data-token');
	let chat_name = $(e).find('.name').html();
	setTimeout(function() {
		$.ajax({
			url: "../assets/utils/chat/getChats.php",
			type: 'POST',
			data: {
				token: token,
				type: type
			},
			success: function (result) {
				$(".chat-splash").slideUp();
				$("#chat-header").html(chat_name);
				$(".chat-loader").fadeOut();
				$(".chat-window-message").attr('data-token',token).attr('data-type',type);
				// console.log(result);
				let chats;
				if (result === 'F') {
					chats = [];
					M.toast({html: 'Some error occurred while receiving chats!'});
					return;
				}
				chats = JSON.parse(result);
				$(".chat-thread").html('<section class="section">\n' +
					'                                <span class="divider"></span>\n' +
					'                                <span>No chats above this</span>\n' +
					'                            </section>');
				for(let i = 0 ; i < chats.length ; i++){
					let chat_name = chats[i].name,
						chat_email = chats[i].email,
						chat_message = chats[i].message,
						chat_timestamp = chats[i].timestamp,
						chat_from_token = chats[i].from_token,
						temp = '';
					if(chat_email===email) temp+='<div class="me">';
					else temp+= '<div class="not-me">';
					temp+= '<div class="date">' + get_days(chat_timestamp) + '</div>';
					if(type!=='user') temp+= '<div class="name">' + chat_name + '</div>';
					temp+= '<div class="text">' + chat_message + '</div>';
					temp+= '<div class="time">' + get_12_hr(chat_timestamp) + '</div>';
					temp+= '</div>';
					$(".chat-thread").append(temp);
				}
			}
		});
		$.ajax({
			url: "../assets/utils/chat/checkIfAdmin.php",
			type: 'POST',
			data: {
				token: token
			},
			success: function(result){
				if(result!=='F'){
					$("#delete_group").fadeIn();
				}
			}
		});
		setTimeout(function () {
			$(".chat-div")[0].scrollTop = $(".chat-div")[0].scrollHeight;

			var server = new EventSource("../assets/utils/chat/server.php");
			server.onerror = function(err) {
				// M.toast({html: 'Some error occurred... Refreshing...'});
				console.log('Some error occurred... Refreshing...');
				server.close();
				refresh_chat(token,type);

				// setTimeout(function () {
				// 	window.location.reload();
				// },1000);
			};
			let type_token = type + '_' + token;
			server.addEventListener(type_token, function(event) {
				let data = JSON.parse(event.data);
				for(let i = 0; i < data.length ; i++){
					if(data[i].email === email) continue;
					say(data[i].name, data[i].message, data[i].timestamp,false,type);
				}
			});
			server.addEventListener('debug', function(event) {
				console.log('listening '+type);
				console.log(event.data);
			});

		},200);
	},200);
}
$(document).ready(function () {

	$("input.filter").on("input", function() {
		let value = $(this).val();
		if(value.length>0) $(this).addClass('active');
		else $(this).removeClass('active');
	});
	function getDetails(){
		$.ajax({
			url: "../assets/utils/getDetails.php",
			success: function (result) {
				console.log(result);
				if(result === 'F'){
					logout();
					window.location.href = '../';
				}
				result = JSON.parse(result);
				name = result.name;
				email = result.email;
			}
		});
	}
	function getList(tab){
		let url = (tab === "#users")?("../assets/utils/chat/getUsers.php"):("../assets/utils/chat/getGroups.php");
		$.ajax({
			url: url,
			success: function (result) {
				// console.log(result);
				if(result === 'F'){
					logout();
					window.location.href = '../';
				}
				result = JSON.parse(result);
				if(result.length <= 0){
					logout();
					window.location.href = '../';
				}
				let temp = '';
				for(let i = 0 ; i < result.length ; i++){
					let name = result[i].name,
						token = result[i].token;
						temp += "<li class='waves-effect waves-light' onclick='openChat(this);' data-token='" + token + "'>" +
									"<span class='img'>" +
										"<i class=\"fa fa-user\"></i>" +
									"</span>" +
									"<span class='name'>" + name + "</span>" +
								"</li>";
				}
				temp += "<li class='no-search hidden' data-token=''>" +
					"<span class='name'>No results...</span>" +
					"</li>";

				$(tab + " ul.filter").html(temp);
			}
		});
	}
	function init(){
		$("#users ul.filter").html('<div class="chat-list-loader scale-transition"><div class=\'loader loader1\'><div><div><div><div><div><div></div></div></div></div></div></div></div></div>');
		setTimeout(function () {
			$("#users .chat-list-loader").addClass('scale-out');
			setTimeout(()=>getList("#users"),100);
			$(".filter").addClass('before');
			$(".filter>li").css('transform','translateY(0)');
			setTimeout(()=>toggleTab("#users"),200);
		},1000);
		getDetails();
	}
	init();

	$('.chat-window-message').keypress(function(e){
		if(e.keyCode === 13){
			$('#send').click();
			e.preventDefault();
		}
	});
	// $(".chat-div")[0].scrollTop = $(".chat-div")[0].scrollHeight;
	$('#send').click(function(e){
		e.preventDefault();
		let text_el = $('.chat-window-message');
		let text = text_el.val().trim();
		let type = text_el.attr('data-type');
		let target = text_el.attr('data-token');
		text = text.trim();
		if(text === '') return;
		let timestamp = new Date();
		$.ajax({
			url: '../assets/utils/chat/sendMessage.php',
			type: 'POST',
			data: {
				target:target,
				type:type,
				text:text,
				timestamp:timestamp.toISOString().split('T')[0] + ' ' + timestamp.toTimeString().split(' ')[0],
			},
			success: function (result) {
				if(result === 'F'){
					M.toast({html: 'Some error occurred while sending the message!'});
					setTimeout(function(){window.location.reload();},1000);
				}
				else{
					$('.chat-window-message').val('');
					say(name, text, timestamp.toString(),true,type);
				}
			}
		});

	});
	// setTimeout(()=>toggleTab('#users'),100);
	$(".chat-tab").click(function(){
		// if($(this).hasClass('active')) return;

		$(".chat-tab.active").removeClass('active');
		$(this).addClass('active');
		let target = $(this).attr('data-target');


		$(target + " ul.filter").html('<div class="chat-list-loader scale-transition"><div class=\'loader loader1\'><div><div><div><div><div><div></div></div></div></div></div></div></div></div>');
		$(".chat-tab-div").fadeOut(10);
		$(target).fadeIn();
		// setTimeout(function () {
		// 	$(target + " .chat-list-loader").addClass('scale-out');
		// 	setTimeout(()=>getList("#users"),100);
		// 	$(".filter").addClass('before');
		// 	$(".filter>li").css('transform','translateY(0)');
		// 	setTimeout(()=>toggleTab("#users"),200);
		// },1000);
		setTimeout(function(){
			$(target + " .chat-list-loader").addClass('scale-out');
			setTimeout(()=>getList(target),100);

			$(".filter").addClass('before');
			$(".filter>li").css('transform','translateY(0)');
			setTimeout(()=>toggleTab(target),200);
		},1000);
	})
	$("#delete_group").click(function (){
		let token = $(".chat-window-message").attr('data-token');
		$.ajax({
			url: '../assets/utils/chat/deleteGroup.php',
			type: 'POST',
			data: {
				token:token
			},
			success: function (result) {
				if(result === 'F'){
					M.toast({html: 'Some error occurred... Please try again...'});
				}
				else{
					M.toast({html: 'Group deleted successfully!'});
					setTimeout(function(){window.location.reload();},1000);
				}
			}
		});
	});
});