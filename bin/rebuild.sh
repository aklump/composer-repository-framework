#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

cd "$__DIR__/.."
! "./vendor/bin/satis" build ./data/satis.json ./web && echo && echo "❌ Packages failed to build." && echo && source bin/check_config.sh && exit 1
echo ""
echo "📦 Package repository rebuilt."
echo ""
