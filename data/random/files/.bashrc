export MBOX_DAEMON_DIR="${HOME}/mail"
export MBOX_DAEMON_BOXES="inbox; github"
export MBOX_DAEMON_EVERY=120

export PAGER=vless
export EDITOR=vim
export INTEL_BATCH=1
export OOO_FORCE_DESKTOP=gnome
export GDK_NATIVE_WINDOWS=1
export PATH="/usr/lib/colorgcc/bin:${PATH}:${HOME}/bin:${HOME}/projects/miniLOL/utils/modules:${HOME}/projects/miniLOL/utils"

if [[ $TERM != "screen" && $TERM != "linux" ]]; then
    export TERM="rxvt-256color"
fi
# Ruby
export RUBYOPT="-rrubygems -rap -rcolorb"

# lulzJS
export JSPATH="${HOME}/lib/lulzjs"
export JSINCLUDE="meh.js:System/Console:System/Network/Protocol/HTTP/Simple:System/Network/Protocol/HTTP:System/FileSystem/Directory:System/FileSystem/File:System/FileSystem/Link"

# PSPSDK
export PSPDEV=/usr/local/pspdev
export PSPSDK="$PSPDEV/psp/sdk"
export PATH="$PATH:$PSPDEV/bin:$PSPSDK/bin"

umask 077

alias t="task"
alias mplayer="mplayer -ass"
alias startx="startx &> /dev/null"
alias autoit="wine ~/.wine/drive_c/Program\ Files/AutoIt3/AutoIt3.exe"
alias screen="screen -U"
alias walk_into="ssh -p 9001 -l root"
alias nds="wine ~/bin/NDS/*EXE &> /dev/null"
alias sprunger="curl -F 'sprunge=<-' http://sprunge.us"
alias lrn="man"
alias lock="xscreensaver-command -lock &> /dev/null"
alias vi="vim"
alias vis="vim -S .vim.session"

function sprunge {
    cat $1 | sprunger
}

function imageshack {
    if [[ ! -e ~/.imgshack && -e ~/.imgshackrc ]]; then
        . ~/.imgshackrc
        curl -s -c ~/.imgshack -b ~/.imgshack -H Expect: -F "username=${USERNAME}" -F "password=${PASSWORD}" -F 'stay_logged_in=true' -F 'format=json' www.imageshack.us/auth.php
    fi

    if [[ -e ~/.imgshack ]]; then
        link=$(curl -s -D - -c ~/.imgshack -b ~/.imgshack -F "fileupload=@${1}" -F 'refer=http://my.imageshack.us/v_images.php' -F 'MAX_FILE_SIZE=13145728' -F 'uploadtype=on' -F 'optimage=resample' -F 'optsize=resample' -F 'rembar=0' www.imageshack.us/index.php | grep -i 'location: ' | cut -c 11- | sed -r 's/content_round\.php\?page=done&l=//')
    else
        link=$(curl -s -H Expect: -F "fileupload=@${1}" -F xml=yes http://www.imageshack.us/index.php | grep -E '<image_link>(.+?)</image_link>' | grep -o 'http://[^<]*')
    fi

    if [[ -z "${link}" ]]; then
        link="Failed to upload image"
    fi

    echo "${link}"
}

function imagebanana {
    local link=$(curl -s -L -H Expect: -F "img=@${1}" -F 'send=Hochladen!' imagebanana.com | grep '\[IMG\]' | sed -r 's/.*\[IMG\]|\[\/IMG\].*//g' | tail -n 1)
    if [[ -z "${link}" ]]; then
        link="Failed to upload image"
    fi

    echo "${link}"
}

function shacklast {
    imageshack `tail -n 1 ~/random/images/screenshots/screenshots.log`
}

function shackbanana {
    imagebanana `tail -n 1 ~/random/images/screenshots/screenshots.log`
}

function cpaste () { gpg -o - -a -c $1 | curl -s -F 'sprunge=<-' http://sprunge.us }
function dpaste () { curl -s $1 | gpg -o - -d }

if [[ $(ps aux | grep "ruby.*mbox-daemon" | grep -v grep) == "" ]]; then
    mbox-daemon &> /dev/null &
fi

if [[ $TERM == "xterm" ]]; then
    PS1="READY."$'\n'
    clear
    echo "    **** COMMODORE 64 BASIC V2 ****"
    echo ""
    echo " 64K RAM SYSTEM  38911 BASIC BYTES FREE"
    echo ""
fi

#if [ "$PS1" ] ; then
#    mkdir -m 0700 /sys/fs/cgroup/cpu/user/$$
#    echo $$ > /sys/fs/cgroup/cpu/user/$$/tasks
#fi
