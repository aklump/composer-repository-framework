rm -r include/ &> /dev/null
rm -r vendor/ &> /dev/null
rm -r web/include &> /dev/null
rm -r .cache &> /dev/null
rm composer.lock &> /dev/null
rm index.html &> /dev/null
rm packages.json &> /dev/null
rm web/index.html &> /dev/null
rm web/packages.json &> /dev/null
rm know.log &> /dev/null
rm data/satis.json &>/dev/null
if [ -f .env ]; then
  chmod 0644 .env && rm .env || exit 1
fi
exit 0
