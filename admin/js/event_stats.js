$(document).ready(function(){
	var get_time = function () {
		$.ajax({
			url: '../get_remaining_time.php',
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
							setTimeout(function(){location.reload();},3000);
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
	var get_leader = function () {
		$.ajax({
			url: '../leaderboard.php',
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
	var get_news = function () {
		$.ajax({
			url: 'get_next_news.php',
			success: function(ret_data) {
				if (ret_data.success == 1) {
					cur_event_id = $('#news-list').data('eventid');
					event_id = ret_data.event_id;
					if (event_id > cur_event_id && $('#news-list li').length < 3) {
						html = "<li>"+ret_data.event+"</li>";
						$('#news-list').append(html);
						$('#news-list').data('eventid',event_id);

					} else if (event_id > cur_event_id) {
						html = "<li id=\""+ret_data.event_id+"\"><p class=\"id\">"+ret_data.event_id+"</p><p class=\"event\">"+ret_data.event+"</p><button class=\"edit\">Edit</button><button class=\"update hidden\">update</button><button class=\"delete\">Delete</button></li>";
						$('#news-list').append(html);
						$('#news-list').data('eventid',event_id);
						$('#news-list>li:nth-child(2)').animate({marginTop: '-71px'}, 800, function() {
							$(this).detach();
						});
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
});