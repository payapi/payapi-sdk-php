#!/usr/bin/env bash

sudo composer/vendor/bin/phpunit --bootstrap composer/vendor/autoload.php --coverage-clover coverage.txt test tester.php