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
	$("#js_popup").remove();

	if(isNaN(x) || isNaN(y) || x < 0 || y < 0) {
		showPopup("#right_block", "Punto non valido.", true);
		return;
	}
	
	if(!pointSelected.set || x !== pointSelected.x || y !== pointSelected.y) {
		showPopup("#right_block", "Il punto non è quello selezionato.", true);
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
			},
		  error: function(data) { showMsg("error_message", data.responseText) }
		});
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
		showPopup("#psw", "La password deve contenere due caratteri speciali.");
		return;
	}
	
	$("#js_popup").remove();
	
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
	
	$("#js_popup").remove();
	
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
		  success: function(data) { reloadWithMessage("main.php", "success_message", data) },
		  error: function(data) { reloadWithMessage("main.php", "error_message", data.responseText) }
		});
}

function showMsg(type, msg){
	$("#info_message").addClass(type);
	$("#info_message > p").text(msg);
	$("#info_message").show(200);
}

function hideMsg(){
	$("#info_message").hide(200);
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
	//very simple checker (server side is much stronger)
	var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function checkPsw(psw) {
	var re = /^([a-zA-Z\d]*[^a-zA-Z\d]){2,}[a-zA-Z\d]*$/;
    return re.test(psw);
}

function showPopup(id, msg, append) {
	console.log(append);
	
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
	$("body").append("<h2>Cookies disabilitati</h2>");	
});