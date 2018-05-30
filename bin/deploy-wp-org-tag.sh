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
echo "To exist is to survive unfair choices. - The OA";
rm -rf ../caldera-forms

echo "Checking out"
#Checkout, without copying everything
svn co https://plugins.svn.wordpress.org/caldera-forms --depth immediates

echo "Copying Tag"
cp -R ././../build/$TAG ./caldera-forms/tags/$TAG

echo "adding Tag"
svn add --force ./caldera-forms/tags/$TAG

echo "Committing Tag"
cd ./caldera-forms/
svn commit -m "Tag $TAG"
echo "completed"

echo https://plugins.svn.wordpress.org/caldera-forms/tags/$TAG