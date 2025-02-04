#!/usr/bin/env bash

s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

[[ ! "$ROOT" ]] && echo && echo "❌ Missing \$ROOT.  Change to the app/ directory and try again." && echo && exit 1
cd "$ROOT/web" && php -S localhost:8000
