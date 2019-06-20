var pointSelected = {
		set: false,
		x: -1,
		y: -1,
		setTot: false,
		moto: -1,
		bici: -1,
		
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
		},
		
		setPosti: function(moto, bici) {
			this.setTot = true;
			this.moto = moto;
			this.bici = bici;
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
		pointSelected.setTot = false;
		$("#circle_" + pointSelected.toString()).toggleClass("selected");
		
		getInfoPoint(x, y);
		$("#point_infos").show(200);
	}
}

function getInfoPoint(x, y) {

	if(isNaN(x) || isNaN(y) || x < 0 || y < 0) {
		//TODO display error.. anche se qua non deve succedere
		console.log("error!");
		return;
	}
	
	if(!pointSelected.set || x !== pointSelected.x || y !== pointSelected.y) {
		//TODO display error.. anche se qua non deve succedere
		console.log("error, point is not this!");
		return;
	}
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "infopoint", x: x, y: y },
		  success: function(data){
				$("#prenota_moto").val(0);
				$("#prenota_bici").val(0);
			
				var values = data.split("-");
				var moto = values[0];
				var bici = values[1];
				
				$("#disp_posto").html("X: " + x + " Y: " + y + "<br>");
				$("#disp_posto").append("Motorini: " + moto + "<br>Biciclette: " + bici);
				$("#prenota_moto").attr("max", moto);
				$("#prenota_bici").attr("max", bici);
				
				pointSelected.setPosti(moto, bici);
			},
		  error: (data) => showErrorMsg(data.responseText)
		});
}

function login(){	
	var email = $("#email").val();
	var psw = $("#psw").val();
	
	if(!checkEmail(email)) {
		showPopup("#email", "Controlla l'email.");
		return;
	}
	
	if(!checkPsw(psw)) {
		showPopup("#psw", "La password deve contenere due caratteri speciali.");
		return;
	}
	
	$("#js_popup").remove();
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "login", email: email, psw: psw },
		  success: function(data){
			  window.location.replace("main.php");
			},
		  error: (data) => showErrorMsg(data.responseText)
		});
}

function logout() {
	$.post( "server.php", { type: "logout"});
	
	//TODO vedi se mettere qui un redirect e nel caso un post in cui poi compare un informativa "logged out"
}

function registration() {
	var email = $("#email").val();
	var psw = $("#psw").val();
	var psw_confirm = $("#psw_confirm").val();
	
	if(!checkEmail(email)) {
		showPopup("#email", "Controlla l'email.");
		return;
	}
	
	if(!checkPsw(psw)) {
		showPopup("#psw", "Non hai inserito almeno due caratteri speciali.");
		return;
	}
	
	if(psw !== psw_confirm) {
		showPopup("#psw_confirm", "Le due password devono essere uguali.");
		return;
	}
	
	$("#js_popup").remove();
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "registration", email: email, psw: psw, psw_confirm: psw_confirm },
		  success: function(data){
			  window.location.replace("main.php");
			},
		  error: (data) => showErrorMsg(data.responseText)
		});
}

function reserve() {
	var moto = $("#prenota_moto").val();
	var bici = $("#prenota_bici").val();
	
	if(!pointSelected.set) {
		showPopup("#right_block", "Non hai selezionato nessun punto.", true);
		return;
	}
	
	var x = pointSelected.x;
	var y = pointSelected.y;
	
	if(!pointSelected.setTot) {
		showPopup("#right_block", "Fatal error: non so quanti posti ci sono qui.", true);
		return;
	}
	
	//check posti
	var totmoto = pointSelected.moto;
	var totbici = pointSelected.bici;
	
	if(totmoto == 0 && totbici == 0) {
		showPopup("#right_block", "Nessun mezzo disponibile qui.", true);
		return;
	}
	
	if(isNaN(moto) || isNaN(bici) || moto < 0 || bici < 0) {
		showPopup("#right_block", "Inserisci numeri validi.", true);
		return;
	}
	
	if(moto == 0 && bici == 0) {
		showPopup("#right_block", "Devi prenotare almeno un mezzo.", true);
		return;
	}
		
    if(totbici < parseInt(bici) + ((moto > totmoto) ? moto-totmoto : 0)) {
		showPopup("#right_block", "Mezzi non sufficienti.", true);
		return;
    }
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "reservation", x: x, y: y, moto: moto, bici: bici },
		  success: (data) => resultReservation("success_message", data),
		  error: (data) => resultReservation("error_message", data.responseText)
		});
}

function showErrorMsg(msg){
	$("#error_message > p").text(msg);
	$("#error_message").show(200);
}

function resultReservation(div_class, message) {	
	var url = 'main.php';
	var form = $('<form action="' + url + '" method="post" style="display:none">' +
	  '<input type="text" name="reservation_result" value="' + div_class + '" />' +
	  '<input type="text" name="reservation_message" value="' + message + '" />' +
	  '</form>');
	
	$('body').append(form);
	form.submit();
}

function checkEmail(email) {
	//very simple checker (server side is much stronger)
	var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function checkPsw(psw) {
	var re = /^([a-zA-Z\d]*[^a-zA-Z\d]){2,}[a-zA-Z\d]*$/;
    return re.test(psw);
}

function showPopup(id, msg, append = false) {
	$("#js_popup").remove();
	
	div = "<div id='js_popup'>" + 
			"<span class='closebtn' onclick='$(this).parent().hide(200)'>&times;</span>" +
			msg + "</div>";
	
	if(append) {
		$(id).append(div);
	}
	else {
		$(id).after(div);
	}
	
	$("#js_popup").hide();
	$("#js_popup").show(200);
}