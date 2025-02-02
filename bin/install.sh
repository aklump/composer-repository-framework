#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)
cd "$__DIR__/.."
# This may be set by composer create-project
! [[ "$ROOT" ]] && ROOT="$PWD"

# Set all file permissions (must come AFTER composer install)
echo "✅ Setting file permissions"
chmod u+x ./bin/perms
./bin/perms

# Copy over all configuration and instruct token replacement.
echo "✅ Creating configuration"
FILE="$ROOT/.env"
! [ -f "$FILE" ] && cp -v ./install/.env "$FILE"

FILE="$ROOT/satis.json"
! [ -f "$FILE" ] &&  cp -v ./install/satis.json "$FILE"

source ./bin/check_config.sh

