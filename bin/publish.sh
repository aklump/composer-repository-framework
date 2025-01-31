#!/usr/bin/env bash

s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

source "$__DIR__/../config.sh"

##
 # For this to work you will need to establish key-based authentication with the LIVE server.
 ##

rsync -rv ./web/ $SSH_HOST:///home/$SSH_USER/$SSH_HOST/app/web/
echo ""
echo "🌎 https://$SSH_HOST has been updated."
echo ""
