var socket = io.connect('http://websocket.dxspot.tv');

socket.on('mapData', function (data) {
	dataObject = JSON.parse(data)
	//console.log(dataObject);
	loadUsers(dataObject['users']);
    parseRepeaters(dataObject['repeaters']);
    parseSpots(dataObject['spots']);
	//socket.emit('my other event', { my: 'data' });
	checkSpots();
	checkUsers();
	checkRepeaters();
});
