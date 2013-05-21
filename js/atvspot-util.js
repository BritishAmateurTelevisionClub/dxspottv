if(!Array.prototype.last) {
	Array.prototype.last = function() {
		return this[this.length - 1];
	}
}

function setTimeSpan(timeSpan) {
	switch(timeSpan)
	{
	case "year":
		valTimeSpan = 31557600;
		break;
	case "6months":
		valTimeSpan = 15778800;
		break;
	case "1month":
		valTimeSpan = 2678400;
		break;
	case "1week":
		valTimeSpan = 604800;
		break;
	case "24hours":
		valTimeSpan = 86400;
		break;
	case "12hours":
		valTimeSpan = 43200;
		break;
	default: // All
		valTimeSpan = 315576000; // 10 years, should do for now!
		break;
	}
}

function setBandChoice(bandChoice) {
	switch(bandChoice)
	{
	case "70cm":
		valBandChoice = { 1: true, 2: false, 3: false, 4: false};
		break;
	case "23cm":
		valBandChoice = { 1: false, 2: true, 3: false, 4: false};
		break;
	case "13cm":
		valBandChoice = { 1: false, 2: false, 3: true, 4: true};
		break;
	default: // All
		valBandChoice = { 1: true, 2: true, 3: true, 4: true};
		break;
	}
}

function bandFromID(bandID) {
    switch(bandID)
	{
	case 1:
		return "70cm";
		break;
	case 2:
		return "23cm";
		break;
	case 3:
		return "13cm";
		break;
	case 4:
		return "3cm";
		break;
	default:
	    return "ERROR";
		break;
	}
}
