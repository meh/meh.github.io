---
title: Go error handling is even worse than sh
categories: [programming, go, zsh, lol]
layout: post
---

While writing zwm I started using [xgb][1] code to understand how the protocol
works, because the specs are practically useless.

The first thing I did with it was writing the code to read the authority files
to get the name and the data for the authentication process.

This is the code it uses to get that data:

{% highlight go %}
// readAuthority reads the X authority file for the DISPLAY.
// If hostname == "" or hostname == "localhost",
// then use the system's hostname (as returned by os.Hostname) instead.
func readAuthority(hostname, display string) (
  name string, data []byte, err error) {

  // b is a scratch buffer to use and should be at least 256 bytes long
  // (i.e. it should be able to hold a hostname).
  b := make([]byte, 256)

  // As per /usr/include/X11/Xauth.h.
  const familyLocal = 256

  if len(hostname) == 0 || hostname == "localhost" {
    hostname, err = os.Hostname()
    if err != nil {
      return "", nil, err
    }
  }

  fname := os.Getenv("XAUTHORITY")
  if len(fname) == 0 {
    home := os.Getenv("HOME")
    if len(home) == 0 {
      err = errors.New("Xauthority not found: $XAUTHORITY, $HOME not set")
      return "", nil, err
    }
    fname = home + "/.Xauthority"
  }

  r, err := os.Open(fname)
  if err != nil {
    return "", nil, err
  }
  defer r.Close()

  for {
    var family uint16
    if err := binary.Read(r, binary.BigEndian, &family); err != nil {
      return "", nil, err
    }

    addr, err := getString(r, b)
    if err != nil {
      return "", nil, err
    }

    disp, err := getString(r, b)
    if err != nil {
      return "", nil, err
    }

    name0, err := getString(r, b)
    if err != nil {
      return "", nil, err
    }

    data0, err := getBytes(r, b)
    if err != nil {
      return "", nil, err
    }

    if family == familyLocal && addr == hostname && disp == display {
      return name0, data0, nil
    }
  }
  panic("unreachable")
}

func getBytes(r io.Reader, b []byte) ([]byte, error) {
  var n uint16
  if err := binary.Read(r, binary.BigEndian, &n); err != nil {
    return nil, err
  } else if n > uint16(len(b)) {
    return nil, errors.New("bytes too long for buffer")
  }

  if _, err := io.ReadFull(r, b[0:n]); err != nil {
    return nil, err
  }
  return b[0:n], nil
}

func getString(r io.Reader, b []byte) (string, error) {
  b, err := getBytes(r, b)
  if err != nil {
    return "", err
  }
  return string(b), nil
}
{% endhighlight %}

This is my translation to Zsh:

{% highlight bash %}
function X:auth:read:length:in {
	integer fd="$2"
	local encoded

	sysread -i $fd -s 2 encoded || return $?

	local first="$encoded[1]"
	local second="$encoded[2]"

	assign $1 "$(( #first << 8 | #second ))"
}

function X:auth:read:string:in {
	integer fd="$2"
	integer length

	X:auth:read:length:in length "$fd"
	sysread -i $fd -s $length $1 || return $?
}

function X:auth:read:in {
	local display="$2"
	local hostname="${3:-$HOST}"
	local authority="${XAUTHORITY:-$HOME/.Xauthority}"
	integer fd

	exec {fd}<"$authority"

	if [[ -z $fd ]]; then
		return 1
	fi

	integer family
	local addr
	local disp
	local name
	local data

	while true; do
		X:auth:read:length:in family $fd || break
		X:auth:read:string:in addr $fd || break
		X:auth:read:string:in disp $fd || break
		X:auth:read:string:in name $fd || break
		X:auth:read:string:in data $fd || break

    if [[ "$family" == 256 && "$addr" == "$hostname" && "$disp" == "$display" ]]
    then
      set -A $1 name "$name" data "$data"

			break
		fi
	done

	exec {fd}>&-

	(( ${#${(P)1}} ))
}
{% endhighlight %}

Error handling is easier and better looking in shell scripts than with Go, most of the code
in the loop in the Go code is error checking and returning the same thing.

Now that could have been easily rewritten like I did my thing, but the way he
did it is idiomatic Go, with exceptions that would have been way easier on the
eyes and easier to understand too.

But who am I to talk badly about Go? I'm the one writing a window manager in
Zsh.

[1]: https://github.com/BurntSushi/xgb
