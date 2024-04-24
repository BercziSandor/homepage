mount --bind /mnt/DATA/system/opt /opt
export PATH=$PATH:/opt/bin:/opt/sbin

cd /mnt/DATA/shared/git/homepage
tDir="./logs/$(date '+%Y/%m/%d')"
tFile="$tDir/homepage_pulls.log"

mkdir -p "$tDir"
date "+%Y.%m.%d %H:%M:%S" >>"$tFile"
git pull >>"$tFile"
