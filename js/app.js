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
	}
	else {
		pointSelected.setValues(x,y);
		$("#circle_" + pointSelected.toString()).toggleClass("selected");
		
		//TODO display infos..
	}
}

