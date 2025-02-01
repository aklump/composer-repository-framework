function remove() {
  local path="$1"

  [ -d "$path" ] && rm -r "$path"; return
  [ -f "$path" ] && rm "$path"; return
}

remove repository_manager/.cache
remove repository_manager/.env
remove repository_manager/include/
remove repository_manager/index.html
remove repository_manager/packages.json
remove repository_manager/vendor/
remove repository_manager/composer.lock

remove repository/vendor/
remove repository/web/include
remove repository/web/index.html
remove repository/web/packages.json
remove repository/.env
remove repository/composer.lock
remove repository/satis.json

exit 0
