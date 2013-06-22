function webSocket() {
	var dataSocket = io.connect('http://websocket.dxspot.tv/mapData');

	dataSocket.on('mapData', function (data) {
		dataObject = JSON.parse(data);
		//console.log(dataObject);
		loadUsers(dataObject['users']);
		parseRepeaters(dataObject['repeaters']);
		parseSpots(dataObject['spots']);
		createGlobalSpotLog(dataObject['spots']);
		//socket.emit('my other event', { my: 'data' });
		checkSpots();
		checkUsers();
		checkRepeaters();
	});
}
