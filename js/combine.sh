#!/bin/bash
cat atvspot.js atvspot-ajax.js atvspot-ui.js atvspot-util.js locator.js map.js > atvspot-combined.js
python closure.py http://www.dxspot.tv/js/atvspot-combined.js > atvspot-combined.min.js
cp atvspot-combined.min.js atvspot-combined.min.js.1
gzip -9 atvspot-combined.min.js.1
mv atvspot-combined.min.js.1.gz atvspot-combined.min.js.gz
