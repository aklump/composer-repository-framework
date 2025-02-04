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
FILE="${ROOT}/.env"
! [ -f "$FILE" ] && cp -v ${FRAMEWORK_DIR}/install/.env "$FILE"

FILE="${ROOT}/satis.json"
! [ -f "$FILE" ] &&  cp -v ${FRAMEWORK_DIR}/install/satis.json "$FILE"
echo

source ${FRAMEWORK_DIR}/bin/check_config.sh

