#!/usr/bin/env bash

function check_token() {
  local token="$1"
  local file="$2"

  context=$(grep "$token" "$file")
  context=${context## }
  if [[ "$context" ]]; then
    echo "⚠️ Replace $token in $file ►►► $context"
    return 0
  fi
  return 1
}

check_token "URL_SAFE_SECURE_SECRET" ".env"
if ! check_token "VENDOR" "./data/satis.json"; then
  check_token "PACKAGE" "./data/satis.json"
fi
check_token "REPOSITORY_URL" "./data/satis.json"

