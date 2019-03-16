$(document).ready(function(){
	var serializeObject = function(ar)
	{
	    var o = {};
	    $.each(ar, function() {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	};
	var send_register_form = function(action) {
		var data = $('#register_form').serializeArray();
		action_obj = Object();
		action_obj.name = "reg_team";
		action_obj.value = "Register";
		data.push(action_obj);
		var form_data = serializeObject(data);	
		$('#register_form input').attr('disabled', 'disabled');	
		$.ajax({
			type: "POST",
			url: "register_team.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					$("#register_form").find("input[type=text]").val("");
					location.reload();
				} else {
					alert(ret_data.msg);
				}
				$('#register_form input').removeAttr('disabled');
			},
			dataType: "json"
		});
	};
	$('#register_form').submit(function(e){
		console.log('register button clicked');
		e.preventDefault();
		send_register_form();
	});
})