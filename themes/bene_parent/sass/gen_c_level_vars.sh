#! /bin/bash

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
  #
  # for lines that look like a path and filename, output two slashes and a space followed
  # by everything after the last slash underscore from the input
  do echo $x | sed 's/.*\/_/\/\/ /';

  # for lines that look like they have set-bbv-var in them
  cat $x | grep 'set-bbv-var';
done

