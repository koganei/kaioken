define(['libs/backbone'], function() {

	var functions = {
		compose: function(fn1, fn2) {
			var fn3 = function() {
				fn1();
				fn2();
			};

			return fn3;
		},

		status: function(message) {
			console.log(message);
			$('#statusbar .status').html(message);
			$('.statusbar').show();
			setTimeout(function(){$('.statusbar').hide('slow');},3000);
		}
	};

	return functions;
});