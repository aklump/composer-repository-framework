#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)
cd "$__DIR__/.."
! [[ "$ROOT" ]] && ROOT="$PWD"
! [[ "$FRAMEWORK_DIR" ]] && FRAMEWORK_DIR="$PWD"

echo "✅ Webroot"
mkdir -p "${ROOT}/web"
rsync -arv ${FRAMEWORK_DIR}/web/ "${ROOT}/web"
rm ${ROOT}/web/api/packages.php
if [[ "$FRAMEWORK_DIR" != "$ROOT" ]]; then
  cp -v ${FRAMEWORK_DIR}/install/packages.php "${ROOT}/web/api/"
fi
echo

echo "✅ Data"
mkdir -p "${ROOT}/data"
rsync -arv "${FRAMEWORK_DIR}/data/" "${ROOT}/data" --exclude=satis.json
echo


## Set all file permissions (must come AFTER composer install)
#echo "✅ Setting file permissions"
#chmod u+x "bin/perms"
#"bin/perms"

# Copy over all configuration and instruct token replacement.
echo "✅ Creating configuration"
env_file="${ROOT}/.env"
! [ -f "$env_file" ] && cp -v ${FRAMEWORK_DIR}/install/.env "$env_file"

satis_file="${ROOT}/satis.json"
! [ -f "$satis_file" ] &&  cp -v ${FRAMEWORK_DIR}/install/satis.json "$satis_file"
echo

source ${FRAMEWORK_DIR}/bin/check_config.sh

echo
echo "👉 Next Step: Open and edit config files..."
echo
echo "🔲 $env_file"
echo "🔲 $satis_file"
echo
