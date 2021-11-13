# Usage : 
# bash release.sh <tag>
# this will 
# - tag this (develop) branch with the given tag
# - merge the current (develop) branch with master and push it to origin
# prerequisite
# - you are in the right (development) directory
# - the code is ready to be committed

if [ $# -eq 0 ]
  then
    echo "Usage : bash release.sh <tag>"
    exit
fi

tag=$1
branch=`git status | awk '/On branch/ {print $3}'`

if [ $branch != "develop"]
    then   
        echo "Current branch is $branch while it should be develop"
        echo "Aborting"
        exit
fi

git status

echo ""
echo "Current tags"
git tag
echo "Releasing $tag from current branch : $branch"
echo "I am going to commit all, tag it with $1, merge into master and push to origin"
echo "Are you sure you want to continue ?"
read var_resp

if [ $var_resp != "y" ]
    then
        echo "Aborting"
        exit
fi
echo "Continuing"

# pushing current branch to origin
git commit -a -m "$tag"
git push --set-upstream origin $branch
git pull
git push

exit

# if sub-branch of the develop branch has been created : 
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
# sudo mkdir -p /var/www/event
# sudo cp -v params.php /var/www/event/
# scp /opt/db/mydatabase.db sd8:/tmp/
# ssh sd8 "sudo mkdir -p /opt/db"
# ssh sd8 "sudo cp -v /tmp/mydatabase.db /opt/db/"


ssh sd8 "sudo mkdir -p /var/www/event/"
ssh sd8 "sudo git clone --depth 1 https://github.com/toto240325/event.git"



scp -v params.php sd8:/var/www/event/
scp -rv {api,config,models,version.tmp} sd8:/var/www/event/
#sudo cp -v params.php /var/www/event/
#sudo vi /var/www/event/params.php

rm version.tmp

# checkout master again (current version is not master)
git checkout master

# sudo su
# . /opt/venv/watchdog_prod/bin/activate
# cd /opt/watchdog
# pip install -r requirements.txt
# python watchdog.py
