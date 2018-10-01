#! /bin/bash

echo "/*"
echo Generated content - do not change! Changes will be overwritten!
echo
echo To make changes to these css variables find the one you want to change and go to
echo "it's declaration in one of the component files, for example in _admin.scss you will"
echo "find several calls to mixin @include set-bbv-var(...). these provide the input to"
echo this script.
echo
echo The purpose of these variables is to provide the ability to override them
echo in bene child themes. This is important because the b-level or bene-buildup-variables
echo are used in multiple places. These provide the ability to fine-tune a child theme.
echo
echo This file should be named sass/config/_05.cssvars.scss
echo "*/"

echo :root {

# This script reads through all the files in the components directory
# picks out the lines that contain a call to the mixin set-bbv-var
#
# @include set-bbv-var(font-size, font-size-x-small, --bene_intro_text-block-system-main-block);
#
# and generates output that looks like this
#
# --bene_intro_text-block-system-main-block: var(--font-size-x-small);
#
# call like this:
# > cd web/profiles/bene/themes/bene_parent/sass
# > ./gen_c_level_vars.sh
#

for x in $(ls ./components/*.scss);

  # input will look like each filename followed by lines from that file that include 'set-bbv-var':
  #
  # ./components/_template.scss
  #    @include set-bbv-var(line-height, input-height - 2px, --drop_down-line-height);
  #        @include set-bbv-var(color, font-color-accent, --general-font-accent-color);
  #          @include set-bbv-var(border-color, disabled-border-color, --drop_down-disabled-after-border-color);

  # For lines that look like a path and filename, output two slashes and a space followed
  # by everything after the last slash underscore from the input. note: \'$'\n'' means newline - we are using use
  # an ANSI C-quoted string ($'...') to splice in the newline $'\n'
  # then removing the filename extension (everything after the last dot) and adding a newline
  do echo $x | \
  sed 's/.*\/_/\'$'\n''\/\/ /' | \
  sed 's/\.[^.]*$//';

  # for lines that have set-bbv-var in them like this
  #
  #       @include set-bbv-var(font-size, font-size-x-small, --bene_intro_text-block-system-main-block);
  #
  # edit them so they output
  #
  # --bene_intro_text-block-system-main-block: var(--font-size-x-small);
  #
  # and for lines that have set-bbv-with-arg-before-var like this
  #
  #      @include set-bbv-with-arg-before-var(border, rem(1) solid, background-color-secondary, --footer-social-border-color);
  #
  # edit them so they output
  #
  # --footer-social-border-color: rem(1) solid var(--background-color-secondary);
  #
  # because this sets the new c-level variable to the value of the b-level variable in the correct location.
  # Use that output to create the c-level css variables.
  #
  cat $x | \
  grep 'set-bbv-var\|set-bbv-with-arg-before-var' | \

  # This next line looks for only those lines with @include set-bbv-var and outputs the correct setting of a css var.
  # note: the square brackets below contain both a space and a tab but for some reason \s was not working.
  #
  sed 's/ *@include set-bbv-var(\(.*\),[    ]*\(.*\),[  ]*\(.*\));/\3: var(--\2);/g' | \

  # Now look at the few lines that contain calculations. For example we might get a line that looks like this
  #
  # --drop_down-small-line-height: var(--input-height-small - 2px);
  #
  # If we get one of these we want to change it to
  #
  # --drop_down-small-line-height: calc( var(--input-height-small) - 2px);
  #
  sed 's/\([^     ]*\):[    ]*var(\(.*\)--\([^    ]*\) [    ]*\(.*\));/\1: calc(\2 var(--\3) \4);/' | \

  # This next line looks for only those lines with @include set-bbv-with-arg-before-var and outputs the correct setting
  # of a css var.
  # note: the square brackets below contain both a space and a tab but for some reason \s was not working.
  #
  # and for lines that have set-bbv-with-arg-before-var like this
  #
  #      @include set-bbv-with-arg-before-var(border, rem(1) solid, background-color-secondary, --footer-social-border-color);
  #
  # edit them so they output
  #
  # --footer-social-border-color: rem(1) solid var(--background-color-secondary);
  #
  sed 's/ *@include set-bbv-with-arg-before-var(\(.*\),[    ]*\(.*\),[    ]*\(.*\),[  ]*\(.*\));/\4: \2 var(--\3);/';

done

echo }