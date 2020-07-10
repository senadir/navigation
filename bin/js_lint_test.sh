
#!/usr/bin/env bash

set -o errexit

if [[ ${RUN_JS} == 1 ]]; then
	npm run -s install-if-deps-outdated
	npm run lint:js client/**/*.js
	npm run build
	npm run test:unit
fi