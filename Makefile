php-dependencies:
	composer install --no-interaction

dependencies: php-dependencies

migrations:
	PHP_ENV=dev /usr/bin/php ./vendor/davedevelopment/phpmig/bin/phpmig migrate

test-migrations:
	PHP_ENV=test /usr/bin/php ./vendor/davedevelopment/phpmig/bin/phpmig migrate

test:
	/usr/bin/php ./vendor/phpunit/phpunit/phpunit ./tests/

travisci-packages:
	/usr/bin/sudo /usr/bin/apt-get update -qq
	/usr/bin/sudo /usr/bin/apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies test-migrations

travisci-script: test

travisci-after-success:
	/bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/monitor/builds/${TRAVIS_BUILD_ID}
