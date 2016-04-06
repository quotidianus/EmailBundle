#!/usr/bin/env bash

/usr/bin/php app/console doctrine:fixtures:load --env=test --no-interaction && phpunit -c app