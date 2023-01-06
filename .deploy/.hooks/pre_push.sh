#!/bin/bash
# Custom pre-push script

EXIT_CODE=0

chmod +x ./.tools/*

# Sensiolabs Security Checker
#.cmd//security_checker.sh || EXIT_CODE=1

# PHPUnit
.cmd//phpunit || EXIT_CODE=1

# Newman API tests
#.cmd//newman.sh || EXIT_CODE=1

[ $EXIT_CODE -ne 0 ] &&
echo -e "\e[31m\n\t************************************************\n\t*  PRE-PUSH HOOK FAILED (see reasons above)  *\n\t************************************************" ||
echo -e "\e[32m\n\t*********************************************\n\t*  PRE-PUSH HOOK PASSED, Code pushed :) *\n\t*********************************************"
echo -e "\033[0m"
exit $EXIT_CODE
