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
	removePopup();

	if(isNaN(x) || isNaN(y) || x < 0 || y < 0) {
		showMsg("error_message", "Punto non valido.");
		return;
	}
	
	if(!pointSelected.set || x !== pointSelected.x || y !== pointSelected.y) {
		showMsg("error_message", "Il punto non è quello selezionato.");
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
				
				$("#disp_posto").html("X: " + x + " Y: " + y + "<br><br>");
				$("#disp_posto").append("Motorini: " + moto + "<br>Biciclette: " + bici);
				$("#prenota_moto").attr("max", moto);
				$("#prenota_bici").attr("max", bici);
				
				pointSelected.setPosti(moto, bici);
				
				$("#circle_"+ x + "_" + y).removeClass();
				$("#circle_"+ x + "_" + y).addClass(getClass(parseInt(moto) + parseInt(bici)));
			},
		  error: function(data) { showMsg("error_message", data.responseText) }
		});
}

function getClass(num){
	if(num >= 4) return "green";
	if(num > 0) return "yellow";
	return "red";
}

function login(e){	
	e.preventDefault();
	
	var email = $("#email").val();
	var psw = $("#psw").val();
	
	if(!checkEmail(email)) {
		showPopup("#email", "Controlla l'email.");
		return;
	}
	
	if(!checkPsw(psw)) {
		removePopup();
		showPopup("#psw", "La password non contiene almeno due caratteri speciali.");
		return;
	}
	
	removePopup();
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "login", email: email, psw: psw },
		  success: function(data) { reloadWithMessage("main.php", "success_message", "Login eseguito.") },
		  error: function(data) { showMsg("error_message", data.responseText) }
		});
}

function logout() {
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "logout"},
		  success: function(data) { reloadWithMessage("main.php", "success_message", "Logout eseguito.") },
		  error: function(data) { showMsg("error_message", "Non sei loggato.") }
		});
}

function registration(e) {
	e.preventDefault();

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
	
	removePopup();
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "registration", email: email, psw: psw, psw_confirm: psw_confirm },
		  success: function(data) { reloadWithMessage("main.php", "success_message", "Registrazione eseguita.") },
		  error: function(data) { showMsg("error_message", data.responseText) }
		});
}

function reserve(e) {
	e.preventDefault();

	var moto = $("#prenota_moto").val();
	var bici = $("#prenota_bici").val();
	
	if(!pointSelected.set) {
		showMsg("warning_message", "Non hai selezionato nessun punto.");
		return;
	}
	
	var x = pointSelected.x;
	var y = pointSelected.y;
	
	if(!pointSelected.setTot) {
		showMsg("warning_message", "Fatal error: non so quanti posti ci sono qui.");
		return;
	}
	
	//check posti
	var totmoto = pointSelected.moto;
	var totbici = pointSelected.bici;
	
	if(totmoto == 0 && totbici == 0) {
		showMsg("warning_message", "Nessun mezzo disponibile qui.");
		return;
	}
	
	if(isNaN(moto) || isNaN(bici) || moto < 0 || bici < 0) {
		showMsg("warning_message", "Inserisci numeri validi.");
		return;
	}
	
	if(moto == 0 && bici == 0) {
		showMsg("warning_message", "Devi prenotare almeno un mezzo.");
		return;
	}
		
    if(totbici < parseInt(bici) + ((moto > totmoto) ? moto-totmoto : 0)) {
    	showMsg("warning_message", "Mezzi non sufficienti.");
		return;
    }
	
	$.ajax({
		  url: 'server.php',
		  type: 'POST',
		  data: { type: "reservation", x: x, y: y, moto: moto, bici: bici },
		  success: function(data) { reloadWithMessage("main.php", "success_message", data) },
		  error: function(data) { reloadWithMessage("main.php", "error_message", data.responseText) }
		});
}

function showMsg(type, msg){
	$("#info_message").addClass(type);
	$("#info_message > p").text(msg);
	//$("#info_message").show(200);
	$("#info_message").css({visibility: "visible"});
}

function hideMsg(){
	//$("#info_message").hide(200);
	$("#info_message").css({visibility: "hidden"});
	$("#info_message").removeClass();
}

function reloadWithMessage(url, type, message) {
	var form = $('<form action="' + url + '" method="post" style="display:none">' +
			  '<input type="text" name="type_msg" value="' + type + '" />' +
			  '<input type="text" name="message" value="' + message + '" />' +
			  '</form>');
			
	$('body').append(form);
	form.submit();
}

function checkEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkPsw(psw) {
	var re = /^([a-zA-Z\d]*[^a-zA-Z\d]){2,}[a-zA-Z\d]*$/;
    return re.test(psw);
}

function showPopup(id, msg) {
	removePopup();
	
	div = "<div id='js_popup'>" + 
			"<span class='closebtn' onclick='$(this).parent().hide(200)'>&times;</span>" +
			msg + "</div>";
	
	$(id).after(div);
	
	$("#js_popup").hide();
	$("#js_popup").show(200);
}

function removePopup(){
	$("#js_popup").remove();
}

function checkCookies() {
	if (navigator.cookieEnabled) return true;

	// set and read cookie
	document.cookie = "cookietest=1";
	var ret = document.cookie.indexOf("cookietest=") != -1;

	// delete cookie
	document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";

	return ret;
}

$(document).ready(function() {
	if(checkCookies()) return; //tutto ok
	
	$(".js_view").hide();

	$("#ck_js_disabled > p").text("Cookie disabilitati.");
	$("#ck_js_disabled").show();
});