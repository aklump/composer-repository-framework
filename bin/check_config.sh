#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)
cd "$__DIR__/.."
# This may be set by composer create-project
! [[ "$ROOT" ]] && ROOT="$PWD"

function check_token() {
  local token="$1"
  local file="$2"

  context=$(grep "$token" "$file")
  context=${context## }
  if [[ "$context" ]]; then
    echo "⚠️ Replace $token in ${file#$ROOT/} ►►► $context"
    return 0
  fi
  return 1
}

check_token "URL_SAFE_SECURE_SECRET" "$ROOT/.env"
if ! check_token "VENDOR" "$ROOT/satis.json"; then
  check_token "PACKAGE" "$ROOT/satis.json"
fi
check_token "REPOSITORY_URL" "$ROOT/satis.json"
