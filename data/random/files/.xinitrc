#! /bin/sh
exec xscreensaver &
#exec scim -d &
exec xset m 200/30 1 &
exec xset r rate 250 30 &
exec xflux -l 44.4175 &
#exec xkbevd -bg &

exec xcompmgr -r10 -D5 -C -o0.8 &
exec fluxbox
