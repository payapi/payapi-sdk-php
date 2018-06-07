#!/bin/bash
./vendor/bin/phpcs --report=checkstyle --standard=PSR2 --extensions=php --sniffs=Generic.WhiteSpace.DisallowTabInden,Generic.WhiteSpace.ScopeIndent ./src/payapi/core