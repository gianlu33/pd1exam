var pointSelected = {
		set: false,
		x: -1,
		y: -1,
		
		toString: function() {
			return this.x + "_" + this.y;
		},
		
		equals: function(x,y) {
			return this.set && this.x === x && this.y === y;
		},
		
		setValues: function(x,y) {
			this.set = true;
			this.x = x;
			this.y = y;
		}	
}

function displayInfoPoint(x, y) {
	if(pointSelected.set)
		$("#circle_" + pointSelected.toString()).toggleClass("selected");
	
	if(pointSelected.equals(x,y)) {
		pointSelected.set = false;
		$("#point_infos").hide(200);
	}
	else {
		pointSelected.setValues(x,y);
		$("#circle_" + pointSelected.toString()).toggleClass("selected");
		
		$("#point_infos").show(200);
		getInfoPoint(x, y);
	}
}

function getInfoPoint(x, y) {
	$.post( "server.php", { type: "infopoint", x: x, y: y },
	
		function(data){
			$("#prenota_moto").val(0);
			$("#prenota_bici").val(0);
		
			var values = data.split("-");
			var moto = values[0];
			var bici = values[1];
			
			$("#disp_posto").html("X: " + x + " Y: " + y + "<br>");
			$("#disp_posto").append("Motorini: " + moto + "<br>Biciclette: " + bici);
			$("#prenota_moto").attr("max", parseInt(bici) + parseInt(moto));
			$("#prenota_bici").attr("max", bici);
		});
}

function login(){	
	var email = $("#email").val();
	var psw = $("#psw").val();
	
	//TODO check parameters (and sanitize also here?)
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "login", email: email, psw: psw },
		  success: function(data){
			  window.location.replace("main.php");
			},
		  error: function(){
			  $(".error_message").show();
		  }
		});
}

function logout() {
	$.post( "server.php", { type: "logout"});
}

function registration() {
	var email = $("#email").val();
	var psw = $("#psw").val();
	var psw_confirm = $("#psw_confirm").val();
	
	//TODO check parameters
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "registration", email: email, psw: psw, psw_confirm: psw_confirm },
		  success: function(data){
			  window.location.replace("main.php");
			},
		  error: function(){
			  $(".error_message").show();
		  }
		});
}

function reserve() {
	var moto = $("#prenota_moto").val();
	var bici = $("#prenota_bici").val();
	
	//TODO vedi se i numeri sono tali e sono corretti
	
	//TODO verifica se il punto è effettivamente selezionato
	var x = pointSelected.x;
	var y = pointSelected.y;
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "reservation", x: x, y: y, moto: moto, bici: bici },
		  success: function(data){
				var values = data.split("-");
				var result = values[0];
				var message = values[1];
				var div_class = result == 1 ? "success_message" : "error_message";
				
				var url = 'main.php';
				var form = $('<form action="' + url + '" method="post" style="display:none">' +
				  '<input type="text" name="reservation_result" value="' + div_class + '" />' +
				  '<input type="text" name="reservation_message" value="' + message + '" />' +
				  '</form>');
				$('body').append(form);
				form.submit();
			},
		  error: function(){
			  alert("Sessione scaduta, o parametri invalidi");
		  }
		});
}
