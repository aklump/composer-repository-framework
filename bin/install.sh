#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

cd "$__DIR__/.."

# Set all file permissions
echo "✅ Setting file permissions"
chmod u+x bin/perms
bin/perms

# Download dependencies.
echo "✅ Gathering dependencies"
composer install

# Copy over all configuration and instruct token replacement.
echo "✅ Creating configuration"
FILE=.env
! [ -f "$FILE" ] && cp -v install/.env "$FILE"

FILE=./data/satis.json
! [ -f "$FILE" ] &&  cp -v install/satis.json "$FILE"

source bin/check_config.sh

