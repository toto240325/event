#
cd /home/toto/watchdog
# git tag "v0.1.4"
git push
git pull
#check last version
#git log --decorate --all --oneline  | grep tag | head -n 1 | awk '{print $2}' FS='tag: ' | awk '{ print $1}' FS=',' | sudo tee /opt/watchdog/version.txt
git tag | tail -n 1 > version.tmp
git checkout `cat version.tmp`

# this should be done by ansible only once
# sudo mkdir -p /opt/venv/watchdog_prod
# sudo virtualenv /opt/venv/watchdog_prod
# sudo cp /home/toto/watchdog/params.py /opt/watchdog

sudo cp requirements.txtpy /opt/watchdog
sudo cp watchdog.py /opt/watchdog
sudo cp -P ping.py /opt/watchdog
sudo cp watchdog.py /opt/watchdog/
sudo cp version.tmp /opt/watchdog/version.txt
rm version.tmp
git checkout master

sudo su
. /opt/venv/watchdog_prod/bin/activate
cd /opt/watchdog
pip install -r requirements.txt
python watchdog.py
