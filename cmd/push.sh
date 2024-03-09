#! /bin/bash
#push to github

message='maj'
if [[ $1 != '' ]] 
then
    message=$1
fi
git add .
git commit -m "$message"
git push