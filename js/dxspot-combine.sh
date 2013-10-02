#!/bin/bash
echo "Removing old files.."
rm -fv dxspot-combined*
echo "Combining js.."
cat dxspot-map.js dxspot-parse.js dxspot-ui.js atvspot-util.js locator.js > dxspot-combined.js
echo "gzipping.."
cp dxspot-combined.js dxspot-combined.js.1
gzip -9 dxspot-combined.js.1
mv dxspot-combined.js.1.gz dxspot-combined.js.gz
echo "done."
