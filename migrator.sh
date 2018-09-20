#!/bin/bash

START_DIR=`pwd`
RUNNING_DIR=$START_DIR
while [[ $RUNNING_DIR != "/" ]]; do
    if [ -d "$RUNNING_DIR/bitrix/vendor/arrilot" ]; then
        break
    fi
    RUNNING_DIR="$( dirname "$RUNNING_DIR" )"
done
if [[ $RUNNING_DIR == "/" || ! -f "$RUNNING_DIR/bitrix/vendor/arrilot/bitrix-migrations/migrator" ]]; then
    echo "Probably console run not from Bitrix project root or Arrilot/bitrix-migrations is not installed!";
    exit
fi
SCRIPT="$RUNNING_DIR/bitrix/vendor/arrilot/bitrix-migrations/migrator $*"
export CLI_DOCUMENT_ROOT="$RUNNING_DIR"
export MIGRATIONS_DIR="$RUNNING_DIR/local/migrations"
/usr/bin/env php $SCRIPT
