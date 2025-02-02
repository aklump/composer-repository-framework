#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)
cd "$__DIR__/.."
! [[ "$ROOT" ]] && ROOT="$PWD"

source "$__DIR__/../inc/_fw.bootstrap.sh"

# Ensure we have a working satis.json
! [ -f "$SATIS_FILE_PATH" ] && ! cp "$SATIS_CANONICAL_PATH" "$SATIS_FILE_PATH" && echo && echo "❌ Missing $SATIS_FILE_PATH." && exit 1

# Copy over some values from the canonical to the working satis.json
name=$(jq -r .name "$SATIS_CANONICAL_PATH")
homepage=$(jq -r .homepage "$SATIS_CANONICAL_PATH")
jq --arg name "$name" '.name = $name' "$SATIS_FILE_PATH" > "${SATIS_FILE_PATH}.tmp" && mv "${SATIS_FILE_PATH}.tmp" "$SATIS_FILE_PATH"
jq --arg homepage "$homepage" '.homepage = $homepage' "$SATIS_FILE_PATH" > "${SATIS_FILE_PATH}.tmp" && mv "${SATIS_FILE_PATH}.tmp" "$SATIS_FILE_PATH"

! "./vendor/bin/satis" build "$SATIS_FILE_PATH" "$ROOT/web" && echo && echo "❌ Packages failed to build." && echo && source ./bin/check_config.sh && exit 1
echo ""
echo "📦 Package repository rebuilt."
echo ""
