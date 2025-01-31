#!/usr/bin/env bash

##
 # @flag -v Use to troubleshoot
 ##

s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

source "$__DIR__/../config.sh"

_rsync_verbosity=v
 for arg in "$@"; do
     if [[ "$arg" == "-v" ]]; then
         _rsync_verbosity=vvv
         break # Stop after finding the first -v
     fi
 done

# ATTN!!! For this to work you will need to establish key-based authentication with the LIVE server.
rsync -r$_rsync_verbosity ./web/ $SSH_USER@$SSH_HOST://$SSH_SERVER_PATH_TO_WEBROOT
echo ""
echo "🌎 $REPOSITORY_URL has been updated."
echo ""
