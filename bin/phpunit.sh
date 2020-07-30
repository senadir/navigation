#!/usr/bin/env bash
WORKING_DIR="$PWD"
cd "$WP_CORE_DIR/wp-content/plugins/navigation/"
if [[ {$COMPOSER_DEV} == 1 ]]; then
	echo "composer dev"
	./vendor/bin/phpunit --version
	if [[ {$RUN_RANDOM} == 1 ]]; then
		./vendor/bin/phpunit -c phpunit.xml.dist --order-by=random
	else
		./vendor/bin/phpunit -c phpunit.xml.dist
	fi
else
	echo "not composer dev"
	phpunit --version
	phpunit -c phpunit.xml.dist
fi
TEST_RESULT=$?
cd "$WORKING_DIR"
exit $TEST_RESULT
