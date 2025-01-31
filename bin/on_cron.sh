#!/usr/bin/env bash

##
 # Scan all registered packages, rebuild static index and publish to LIVE.
 #
 # This should be symlinked to your home directory; do not use this path directly.
 #
 # cd ~/bin && ln -s /PATH/TO/THIS/FILE/on_package_change.sh update_packages_itls.sh
 ##
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

source "$__DIR__/../inc/bootstrap.sh"
[ ! -f "$QUEUE_FILE" ] && exit 0

cd "$__DIR__"/..
./bin/rebuild.sh
./bin/publish.sh

rm "$QUEUE_FILE"
