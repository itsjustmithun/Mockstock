$(document).ready(function(){
	$('#add_company_form').submit(function(e) {
		e.preventDefault();
		var form_data = new Object;
		name = $("#add_company_form input[name=stock]").val();
		description = $("#add_company_form textarea[name=description]").val();
		price = $("#add_company_form input[name=price]").val();
		minval = $("#add_company_form input[name=minval]").val();
		maxval = $("#add_company_form input[name=maxval]").val();
		remaining = $("#add_company_form input[name=remaining]").val();
		form_data['name'] = name;
		form_data['description'] = description;
		form_data['price'] = price;
		form_data['minval'] = minval;
		form_data['maxval'] = maxval;
		form_data['remaining'] = remaining;
		$.ajax({
			type: "POST",
			url: "insert_co.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					html = "<li id="+ ret_data.id +">\n<p class='id'>"+ ret_data.id;
					html += "</p>\n<p class='name'>"+ name;
					html += "</p>\n<p class='description'>"+ description;
					html += "</p>\n<p class='price'>"+ price;
					html += "</p>\n<p class='minval'>"+ minval;
					html += "</p>\n<p class='maxval'>"+ maxval;
					html += "</p>\n<p class='remaining'>"+ remaining;
					html += "</p>\n<button class='edit'>Edit</button>\n<button class='update hidden'>update</button>\n<button class='delete'>delete</button>\n</li>";
					$('#companies').children('ul#co-list').append(html);
					$("#add_company_form input[name=stock]").val("");
					$("#add_company_form textarea[name=description]").val("");
					$("#add_company_form input[name=price]").val("");
					$("#add_company_form input[name=minval]").val("");
					$("#add_company_form input[name=maxval]").val("");
					$("#add_company_form input[name=remaining]").val("");
				} else {
					alert(ret_data.message);
				}
			},
			dataType: "json"
		})

	});

	$('ul#co-list').on('click','button.update',function(e) {
		e.preventDefault();
		list = $(this).parent('li');
		var form_data = new Object;
		name = list.children('p.name').children('input').val();
		description = list.children('p.description').children('textarea').val();
		price = list.children('p.price').children('input').val();
		minval = list.children('p.minval').children('input').val();
		maxval = list.children('p.maxval').children('input').val();
		remaining = list.children('p.remaining').children('input').val();
		form_data['id'] = list.children('p.id').text();
		form_data['name'] = name;
		form_data['description'] = description;
		form_data['price'] = price;
		form_data['minval'] = minval;
		form_data['maxval'] = maxval;
		form_data['remaining'] = remaining;
		$.ajax({
			type: "POST",
			url: "update_co.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					list.children('button.edit').removeClass('hidden');
					list.children('button.update').addClass('hidden');
					$.each(items,function(key,val) {
						if($(val).hasClass('id')){}
						else {
							if($(val).hasClass('description') || $(val).hasClass('event')) {
								text = $(val).children('textarea').val();
								$(val).html(text);
							} else {
								text = $(val).children('input').val();
								$(val).html(text);
							}
						}
					});
				} else {
					alert(ret_data.message);
				}
			},
			dataType: "json"
		});

	});

	$('ul#co-list').on('click','button.delete',function(e) {
		e.preventDefault();
		list = $(this).parent('li');
		var name = list.children('p.name').children('input').val();
		if(!name)
			name = list.children('p.name').html();
		var form_data = new Object;
		form_data['id'] = list.children('p.id').text();
		var yes = confirm("Are you sure you want to delete "+name+"?");
		if(yes) {
			$.ajax({
				type: "POST",
				url: "delete_co.php",
				data: form_data,
				success: function(ret_data){
					if(ret_data.success == 1) {
						list.remove();
					} else {
						alert(ret_data.message);
					}
				},
				dataType: "json"
			});
		}

	});


	$('ul#news-list').on('click','button.delete',function(e) {
		e.preventDefault();
		list = $(this).parent('li');
		if(!name)
			name = list.children('p.name').html();
		var form_data = new Object;
		form_data['id'] = list.children('p.id').text();
		var id = list.children('p.id').text();
		var yes = confirm("Are you sure you want to delete news item "+id+"?");
		if(yes) {
			$.ajax({
				type: "POST",
				url: "delete_news.php",
				data: form_data,
				success: function(ret_data){
					if(ret_data.success == 1) {
						list.remove();
					} else {
						alert(ret_data.message);
					}
				},
				dataType: "json"
			});
		}

	});


	$('ul#news-list').on('click','button.update',function(e) {
		e.preventDefault();
		list = $(this).parent('li');
		event_item = list.children('p.event').children('textarea').val();
		console.log(event_item);
		var form_data = new Object;
		form_data['id'] = list.children('p.id').text();
		form_data['event'] = event_item; 
		$.ajax({
			type: "POST",
			url: "update_news.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					list.children('button.edit').removeClass('hidden');
					list.children('button.update').addClass('hidden');
					$.each(items,function(key,val) {
						if($(val).hasClass('id')){}
						else {
							if($(val).hasClass('description') || $(val).hasClass('event')) {
								text = $(val).children('textarea').val();
								$(val).html(text);
							} else {
								text = $(val).children('input').val();
								$(val).html(text);
							}
						}
					});
				} else {
					alert(ret_data.message);
				}
			},
			dataType: "json"
		});

	});


	$('ul').on('click','button.edit',function() {
		// body...
		list = $(this).parent('li');
		list.children('button.edit').addClass('hidden');
		list.children('button.update').removeClass('hidden');
		items = list.children('p');
		$.each(items,function(key,val) {
			if($(val).hasClass('id')){}
			else {
				text = $(val).text();
				html = "<input type='text' name="+ $(val).attr('class') +" value='"+text+"'>";
				if($(val).hasClass('description') || $(val).hasClass('event'))
				html = "<textarea name="+ $(val).attr('class') +">"+text+"</textarea>";
				$(val).html(html);
			}
		});
	});
	$('#add_news_form').submit(function(e){
		e.preventDefault();
		event_item = $("#add_news_form textarea[name=event]").val();
		var form_data = new Object;
		form_data['event'] = event_item; 
		$.ajax({
			type: "POST",
			url: "insert_news.php",
			data: form_data,
			success: function(ret_data){
				if(ret_data.success == 1) {
					html = "<li id="+ ret_data.id +">\n<p class='id'>"+ ret_data.id;
					html += "</p>\n<p class='event'>"+ event_item;
					html += "</p>\n<button class='edit'>Edit</button>\n<button class='update hidden'>update</button>\n<button class='delete'>delete</button>\n</li>";
					$('#news').children('ul#news-list').append(html);
					top = $('ul#news-list>li#'+ret_data.id).position().top;
					$('ul#news-list').animate({ scrollTop: 2795+70 }, 2000);
					$("#add_news_form textarea[name=event]").val("");
				} else {
					alert(ret_data.message);
				}
			},
			dataType: "json"
		})

	});
	$('button#export-db').on('click',function() {
		window.location = 'export_db.php';
	});
	$('button#reset-all').on('click',function() {
		$.ajax({
			url: 'reset_tables.php',
			dataType: 'json',
			success: function(ret_data) {
				if(ret_data.success == 1)
					alert('Tables Reset Successfully');
				else
					alert(ret_data.msg);
			}
		})
	});
	$('button#reset').on('click',function() {
		$.ajax({
			url: 'reset_tables.php',
			data: {'tables':'restrict_few'},
			dataType: 'json',
			success: function(ret_data) {
				if(ret_data.success == 1)
					alert('Table Reset Successfully');
				else
					alert(ret_data.msg);
			}
		})
	});
	var get_teams = function () {
		$.ajax({
			url: 'get_teams_reg.php',
			success: function(ret_data) {
				if (ret_data.success == 1) {
					$('#teams-registered>span').html(ret_data.teams);
				} else {
					//alert(ret_data.msg);
				}
			},
			complete: function() {
				setTimeout(get_teams,3000);
			},
			dataType: "json"
		});
	}
	get_teams();
});