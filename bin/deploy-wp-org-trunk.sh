#!/usr/bin/env bash
#Copy version from package.json
TAG=$(cat ././../package.json \
  | grep version \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

USERNAME=${2-Shelob9}
PASSWORD=$1
echo "Tag to be committed:" $TAG

TAG_URL=https://plugins.svn.wordpress.org/caldera-forms/${TAG}
TRUNK_URL=https://plugins.svn.wordpress.org/caldera-forms/trunk/
echo Tag $TAG_URL will be copied to $TRUNK_URL

read -p "Are you sure? " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
    then
        svn $TAG_URL SRC
    fi

