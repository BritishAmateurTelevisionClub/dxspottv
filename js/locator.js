function getDeg(arg, base, offset, cmp)
// convert letters into angles by subtracting the base char code from the input char code
{
    return(base + offset * (arg.toUpperCase().charCodeAt(0) - cmp.charCodeAt(0)));
}

function LoctoLatLon(maidenhead) {
      var x = 0;
		var sw_lon;
		var ne_lon;
		var ce_lon;
		var sw_lat;
		var ne_lat;
		var ce_lat;
        while (x < maidenhead.length)
        {
            switch(x)
            {
                case 0:
                    sw_lon = getDeg(maidenhead.charAt(x), -180, 20, "A");
                    ne_lon = sw_lon + 20;
                    ce_lon = sw_lon + 10;
                    break;
                case 1:
                    sw_lat = getDeg(maidenhead.charAt(x), -90, 10, "A");
                    ne_lat = sw_lat + 10;
                    ce_lat = sw_lat + 5;
                    break;
                case 2:
                    sw_lon += getDeg(maidenhead.charAt(x), 0, 2, "0");
                    ne_lon = sw_lon + 2;
                    ce_lon = sw_lon + 1;
                    break;
                case 3:
                    sw_lat += getDeg(maidenhead.charAt(x), 0, 1, "0");
                    ne_lat = sw_lat + 1;
                    ce_lat = sw_lat + 0.5;
                    break;
                case 4:
                    sw_lon += getDeg(maidenhead.charAt(x), 0, 2/24, "A");
                    ne_lon = sw_lon + 2/24;
                    ce_lon = sw_lon + 1/24;
                    break;
                case 5:
                    sw_lat += getDeg(maidenhead.charAt(x), 0, 1/24, "A");
                    ne_lat = sw_lat + 1/24;
                    ce_lat = sw_lat + 0.5/24;
                    break;
                default:
                    break;
            }
            x++;
        }
        return [ce_lat, ce_lon];
}
