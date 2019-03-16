$(document).ready(function(){
	var error_popup = function(msg) {
		$('#error_overlay').removeClass('hidden');
		$('#error_wrapper').removeClass('hidden');
		$('#error_wrapper>p').html(msg);
		$('#error_wrapper>p').removeClass('hidden');
	}
	$('#error_overlay').on('click',function(){
		$('#error_overlay').addClass('hidden');
		$('#error_wrapper').addClass('hidden');
		$('#error_wrapper>p').addClass('hidden');
	});
	$('div#error_wrapper>h1.heading>a.close').on('click',function(e){
		e.preventDefault();
		$('#error_overlay').addClass('hidden');
		$('#error_wrapper').addClass('hidden');
		$('#error_wrapper>p').addClass('hidden');
	});
	$('#desc_overlay').on('click',function(){
		$('#desc_overlay').addClass('hidden');
		$('#desc_wrapper').addClass('hidden');
		$('div#desc_wrapper>div.content').addClass('hidden');
	});
	$('div#desc_wrapper>div.content>h1.heading>a.close').on('click',function(e){
		e.preventDefault();
		$('#desc_overlay').addClass('hidden');
		$('#desc_wrapper').addClass('hidden');
		$('div#desc_wrapper>div.content').addClass('hidden');
	});
	$('div#price_wrapper>.content>p').on('click',function(){
		id = $(this).attr('id');
		$('#desc_overlay').removeClass('hidden');
		$('#desc_wrapper').removeClass('hidden');
		$('div#desc_wrapper>div.content#'+id).removeClass('hidden');
	});
	var get_time = function () {
		$.ajax({
			url: 'get_remaining_time.php',
			success: function(ret_data) {
				if (ret_data.success == 1) {
					$('#timer').attr('data-timer',ret_data.time);
					$('#timer').TimeCircles({
					    "animation": "smooth",
					    "bg_width": 0.4,
					    "fg_width": 0.04,
    					"circle_bg_color": "#40484F",
						"time": {
					        "Days": {
					            "show": false
					        },
					        "Hours": {
					            "show": false
					        },
					        "Minutes": {
					            "text": "Minutes",
					            "show": true,
					            "color": "#2E92DA"
					        },
					        "Seconds": {
					            "text": "Seconds",
					            "show": true,
					            "color": "#2E92DA"
					        }
					    },
						count_past_zero: false
					})
					.addListener(function(unit, amount, total){
						if(total == 0) {
							setTimeout(function(){location.reload();},1000);
						}
					});
				} else{
					error_popup(ret_data.msg);
				}
			},
			complete: function() {
				setTimeout(get_time,1000*10);
			},
			dataType: "json"
		});
	}
	get_time();
	var get_news = function () {
		$.ajax({
			url: 'get_news.php',
			success: function(ret_data) {
				if (ret_data.success == 1) {
					cur_event_id = $('#news_ticker').data('eventid');
					event_id = ret_data.event_id;
					if (event_id > cur_event_id && $('#news_ticker li').length < 3) {
						html = "<li>"+ret_data.event+"</li>";
						$('#news_ticker').append(html);
						$('#news_ticker').data('eventid',event_id);

					} else if (event_id > cur_event_id) {
						//if(ret_data.event.length != 0) {
							html = "<li>"+ret_data.event+"</li>";
							$('#news_ticker').append(html);
							$('#news_ticker').data('eventid',event_id);
							$('#news_ticker>li:first').animate({marginTop: '-71px'}, 800, function() {
								$(this).detach();
							});
						//}
					}
				}
			},
			complete: function() {
				setTimeout(get_news,5000);
			},
			dataType: "json"
		});
	}
	get_news();
	var get_leader = function () {
		$.ajax({
			url: 'leaderboard.php',
			success: function(ret_data) {
				if (ret_data.success == 1) {
					html1 = '<h1 class="heading">Leader board </h1>';
					$('div#leader_wrapper').html(html1);
					$.each(ret_data.leaders,function(key,val){
						html = "<p class="+val+"><span>"+key+"</span>"+val+"</p>";
						$('div#leader_wrapper').append(html);
					});
				} else {
					error_popup(ret_data.msg);
				}
			},
			complete: function() {
				setTimeout(get_leader,5000);
			},
			dataType: "json"
		});
	}
	get_leader();
	var get_prices = function () {
		$.ajax({
			url: 'get_prices.php',
			success: function(ret_data) {

				if(ret_data.success == 1){
					data = ret_data.data;
					html = "";
					$.each(data, function(id, info){
						html = html + "<p id=\""+id+"\"><span class=\"indicator\">&nbsp;</span><span class=\"name\">" +info[0]+ "</span><span class=\"price\">" +info[1]+ " <span class=\"remaining_stocks\"> (" +info[2]+ " left) </span>" + "</span></p>";
					})
					prices_parent_wrapper = $("#price_wrapper");
					$(prices_parent_wrapper).children(".content").html(html);
				}
				else{

				}



				// if (ret_data.success == 1) {
				// 	prices = ret_data.prices;
				// 	$.each(prices, function(id,price) {
				// 		ele = $('#price_wrapper .content p#'+id);
				// 		cur_price = ele.children('span.price').html();
				// 		if(cur_price == price) {}
				// 		else if(cur_price > price) {
				// 			ele.children('span.price').fadeOut("slow").html(price).fadeIn("slow");
				// 			if(ele.hasClass("up")) {
				// 				ele.removeClass("up").addClass("down");
				// 			}
				// 			if(!ele.hasClass("down")) {
				// 				ele.addClass("down");
				// 			}
				// 		} else {
				// 			ele.children('span.price').fadeOut("slow").html(price).fadeIn("slow");
				// 			if(ele.hasClass("down")) {
				// 				ele.removeClass("down").addClass("up");
				// 			}
				// 			if(!ele.hasClass("up")) {
				// 				ele.addClass("up");
				// 			}
				// 		}
				// 	});
				// } else {
				// 	error_popup(ret_data.msg);
				// }



			},
			complete: function() {
				setTimeout(get_prices,2000);
			},
			dataType: "json"
		});
	}
	get_prices();
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
	var send_trade_form = function(action) {
		var data = $('#trade_form').serializeArray();
		action_obj = Object();
		action_obj.name = "trade_action";
		action_obj.value = action;
		data.push(action_obj);
		var form_data = serializeObject(data);	
		$('#trade_form input').attr('disabled', 'disabled');	
		$.ajax({
			type: "GET",
			url: "process.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					$("#trade_form").find("input[type=number]").val("");
				} else {
					$("#trade_form").find("input[type=number]").val("");
					error_popup("'Part of' or the 'Whole' Transaction failed. Check log for details." + ret_data.msg);
				}
				$('#trade_form input').removeAttr('disabled');
				$('#holdings_wrapper .content').fadeOut("slow").load('get_holdings.php').fadeIn("slow");
				$('#log_wrapper .content').fadeOut("slow").load('get_log.php').fadeIn("slow");
			},
			dataType: "json"
		});
	}
	$('#trade_form').submit(function(e){
		e.preventDefault();
	});
	$('#trade_form input[name=trade_action]').on("click",function(e){
		e.preventDefault();
		$val = $(this).val();
		send_trade_form($val);
	});
	/*$('#price_wrapper .content').everyTime(3000,function() { 
		$('#price_wrapper .content').fadeOut("slow").load('get_prices.php').fadeIn("slow");
		$.ajaxSetup({cache: false});
	},0);
	
	$('#holdings_wrapper .content').everyTime(3000,function() { 
		$('#holdings_wrapper .content').fadeOut("slow").load('get_holdings.php').fadeIn("slow");
		$.ajaxSetup({cache: false});
	},0);*/
});