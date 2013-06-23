#!/bin/bash
echo "Removing old files.."
rm -fv dxspot-combined*
echo "Combining js.."
<script src="/js/dxspot-map.js"></script>
<script src="/js/dxspot-parse.js"></script>
<script src="/js/dxspot-websocket.js"></script>
<script src="/js/dxspot-ui.js"></script>
<script src="/js/atvspot-util.js"></script>
<script src="/js/locator.js"></script>
cat dxspot-map.js dxspot-parse.js dxspot-websocket.js dxspot-ui.js atvspot-util.js locator.js > dxspot-combined.js
echo "gzipping.."
cp dxspot-combined.js atvspot-combined.js.1
gzip -9 dxspot-combined.js.1
mv dxspot-combined.js.1.gz dxspot-combined.js.gz
echo "done."
