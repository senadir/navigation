
#!/usr/bin/env bash

set -o errexit

echo "starting"

if [[ ${RUN_JS} == 1 ]]; then
echo "install"
	npm run -s install-if-deps-outdated
echo "linting"
	npm run lint:js client/**/*.js client/*.js
echo "building"
	npm run build
	npm run test:unit
fi
