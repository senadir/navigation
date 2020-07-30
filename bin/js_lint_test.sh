
#!/usr/bin/env bash

set -o errexit

if [[ ${RUN_JS} == 1 ]]; then
	npm install
	npm run lint:js client/**/*.js client/*.js
	npm run build
	npm run test:unit
fi
