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
  # then removing the filename extension (everything after the last dot)
  do echo $x | sed 's/.*\/_/\/\/ /' | sed 's/\.[^.]*$//';

  # for lines that have set-bbv-var in them like this
  #
  #       @include set-bbv-var(font-size, font-size-x-small, --bene_intro_text-block-system-main-block);
  #
  # edit them so that they output
  #
  # --bene_intro_text-block-system-main-block: var(--font-size-x-small);
  #
  # because this sets the new c-level variable to the value of the b-level variable. Then use that output
  # to create the c-level css variables.
  cat $x | grep 'set-bbv-var' | sed 's/ *@include set-bbv-var(\(.*\),[ \t]*\(.*\),[ \t]*\(.*\));/\3: var(--\2);/g';

done

