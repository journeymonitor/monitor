php-dependencies:
	composer install --no-interaction

dependencies: php-dependencies

migrations:
	PHP_ENV=dev php ./vendor/davedevelopment/phpmig/bin/phpmig migrate

test-migrations:
	PHP_ENV=test php ./vendor/davedevelopment/phpmig/bin/phpmig migrate

test:
	php ./vendor/phpunit/phpunit/phpunit ./tests/

travisci-packages:
	sudo apt-get update -qq
	sudo apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies test-migrations

travisci-script: test

travisci-after-success:
	/bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/monitor/builds/${TRAVIS_BUILD_ID}
