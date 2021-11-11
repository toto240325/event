#
cd /home/toto/event_dev
git status
git commit -a -m "new functionality"
git push --set-upstream origin sqlite2
git pull
git push

git checkout develop
git merge new_dev_branch
git pull
git push

git checkout master
git merge develop
git pull
git push

git tag "v0.1"
git push --tag
#check last version
#git log --decorate --all --oneline  | grep tag | head -n 1 | awk '{print $2}' FS='tag: ' | awk '{ print $1}' FS=',' | sudo tee /opt/watchdog/version.txt
git tag | tail -n 1 > version.tmp
git checkout `cat version.tmp`

# this should be done by ansible only once
# sudo mkdir -p /opt/venv/watchdog_prod
# sudo virtualenv /opt/venv/watchdog_prod
# sudo cp /home/toto/watchdog/params.py /opt/watchdog

sudo mkdir -p /opt/event/
sudo cp -rv {api,config,models,version.tmp} /opt/event/
sudo cp -v params.php /opt/event/
sudo vi /opt/event/params.php

rm version.tmp

# checkout master again (current version is not master)
git checkout master

sudo su
. /opt/venv/watchdog_prod/bin/activate
cd /opt/watchdog
pip install -r requirements.txt
python watchdog.py
