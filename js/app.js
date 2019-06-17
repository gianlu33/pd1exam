var infoPointActive = -1;

function displayInfoPoint(id){
	switch(infoPointActive){
	case -1:
		//nessuno era cliccato
		$("#circle_" + id).attr("stroke-width", 1);
		
		infoPointActive = id;
		break;
		
	case id:
		//deseleziono
		$("#circle_" + id).attr("stroke-width", 0);
		
		infoPointActive = -1;
		break;
		
	default:
		//era selezionato un altro
		$("circle").each(function() {
			$(this).attr("stroke-width", 0);
		});
		$("#circle_" + id).attr("stroke-width", 1);
		
		infoPointActive = id;
		break;
	}	
}