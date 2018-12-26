#! /bin/bash

# our own little yeoman
#
# This script works on a theme that was started by copying bene_child.
# If the theme still has the name bene_child sprinkled throughout, this does the
# job of changing those instances of 'bene_child' to the new theme name. For
# example bene_cori had a bunch of places where it was still named bene_child.
# This script goes through the bene_cori directory and replaces those with
# bene_cori and does so in place. (Does _not_ create a new directory or a new copy.)
# It also renames the directory from bene_child to the new theme name if it is
# not already re-named.
#
# call like this:
# > cd theme/directory
# > update_theme_name.sh my_new_theme_name
#

  THEME_NAME=$1
  if [[ $# -eq 0 ]] ; then
    echo 'A new theme name is required. Call like this:'
    echo './update_theme_name.sh my_new_theme_name'
    echo 'replacing my_new_theme_name with the name you want to use.'
    exit 0
  fi

  echo Theme name is $THEME_NAME
  # assumes the currently running script is inside the bene_child directory that will be re-named
  THEME_DEST="$( cd "$(dirname "$0")" ; cd ..; pwd -P )"/$THEME_NAME
  CURRENT_DIR="$(pwd -P )"
  echo "Theme will be placed in $THEME_DEST"

  if [ $THEME_DEST != $CURRENT_DIR ]
  then
    mv $CURRENT_DIR $THEME_DEST
  fi

  PROJECT_NAME=$(echo $THEME_DEST | sed 's:.*/\(.*\)/web/.*:\1:g')

  # go through files and edit them replacing bene_child with the new theme name
  sed "s/bene_child_/${THEME_NAME}_/g" $THEME_DEST/bene_child.theme >$THEME_DEST/$THEME_NAME.theme
  rm $THEME_DEST/bene_child.theme

  sed "s/name: Bene Child/name: ${THEME_NAME}/g" $THEME_DEST/bene_child.info.yml | sed "s/bene_child/${THEME_NAME}/g" >$THEME_DEST/$THEME_NAME.info.yml
  rm $THEME_DEST/bene_child.info.yml

  sed "s/bene_child/${THEME_NAME}/g" $THEME_DEST/bene_child.libraries.yml >$THEME_DEST/$THEME_NAME.libraries.yml
  rm $THEME_DEST/bene_child.libraries.yml

  mv $THEME_DEST/composer.json $THEME_DEST/composer.child
  sed "s/bene_child/${THEME_NAME}/g" $THEME_DEST/composer.child >$THEME_DEST/composer.json
  rm $THEME_DEST/composer.child

  mv $THEME_DEST/package.json $THEME_DEST/package.child
  sed "s/bene_child/${THEME_NAME}/g" $THEME_DEST/package.child >$THEME_DEST/package.json
  rm $THEME_DEST/package.child

  # fix up the README file so it reflects the new theme name and location
  mv $THEME_DEST/README.md $THEME_DEST/README.md.child
  sed "s/Bene Child/${THEME_NAME}/g" $THEME_DEST/README.md.child | \
  sed "s/Bene_child/${THEME_NAME}/g" | \
  sed "s:/new-project-name/web/profiles/contrib/bene/themes/bene_child where new-project-name is your project.:${THEME_DEST}:g" | \
  sed "s:can be found in a project called \"new-project-name\" here\: /new-project-name/web/profiles/contrib/bene/themes/bene_child:can be found in a project called \"${PROJECT_NAME}\" here\: ${THEME_DEST}:g" | \
  sed "s/new-project-name/${THEME_NAME}/g" >$THEME_DEST/README.md
  rm $THEME_DEST/README.md.child

  #fix the Rakefile so it starts the correct url
  sed -i .child "s/bene-child/${PROJECT_NAME}/g" $THEME_DEST/Rakefile
  rm Rakefile.child

  # self modifying code
  mv $THEME_DEST/update_theme_name.sh $THEME_DEST/update_theme_name.child
  sed "s/bene-child/${THEME_NAME}/g" update_theme_name.child | \
  sed "s/bene_child/${THEME_NAME}/g" | \
  sed "s/Bene Child/${THEME_NAME}/g" | \
  sed "s/Bene_child/${THEME_NAME}/g" >$THEME_DEST/update_theme_name.sh
  chmod a+x $THEME_DEST/update_theme_name.sh
  rm $THEME_DEST/update_theme_name.child
