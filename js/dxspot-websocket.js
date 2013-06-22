var socket = io.connect('http://websocket.dxspot.tv');

socket.on('mapData', function (data) {
	console.log(data);
	loadUsers(data['users']);
    parseRepeaters(data['repeaters']);
    parseSpots(data['spots']);
	//socket.emit('my other event', { my: 'data' });
});
